<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Carbon\Carbon;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Applicant::query();
        $User = Auth::user();

        // Only show own applications for non-admin users
        if ($User->role !== "Admin") {
            $query->where('user_id', $User->id);

            // Hide old application if renewal in progress (for Kerani)
            if (strtolower($User->role) === 'kerani') {
                $renewedApplicantIds = \App\Models\RenewLicense::whereIn('applicant_id', function($q) {
                        $q->select('id')
                          ->from('applicants')
                          ->whereIn('status', [
                              'Perbaharui Lesen',
                              'Perbaharui Lesen Dalam Semakan',
                              'Perbaharui Lesen Telah Disemak',
                              'Perbaharui Lesen Diluluskan',
                              'Perbaharui Lesen Tidak Diluluskan',
                              'Perbaharui Lesen Borang Tidak Lengkap'
                          ]);
                    })
                    ->pluck('previous_applicant_id')
                    ->toArray();
                if (!empty($renewedApplicantIds)) {
                    $query->whereNotIn('id', $renewedApplicantIds);
                }
            }
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kad_pengenalan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // If 'all' param is set, return all results (for filtering/searching)
        if ($request->has('all') && $request->all) {
            $Applicants = $query->orderBy('created_at', 'desc')->get();
            // Return as JSON for axios fetch
            return response()->json(['Applicants' => ['data' => $Applicants]]);
        } else {
            $Applicants = $query->orderBy('created_at', 'desc')->paginate(10);
        }

        $all_borang_tidak_lengkap = [];
        if ($User->role === "kerani") {
            $all_borang_tidak_lengkap = Applicant::where('user_id', $User->id)
                ->where('status', 'Borang Tidak Lengkap')
                ->get(['id', 'nama']);
        }

        return Inertia::render('Applicant/Index', [
            'Applicants' => $Applicants,
            'search' => $request->search,
            'all_borang_tidak_lengkap' => $all_borang_tidak_lengkap,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kad_pengenalan' => 'required|string|max:20',
            'warganegara' => 'required|string|min:3',
            'alamat_rumah' => 'nullable|string',
            'alamat_berlainan' => 'nullable|string',
            'email' => 'nullable|email',
            'telefon_r' => 'nullable|string|max:15',
            'telefon_b' => 'nullable|string|max:15',
            'telefon_p' => 'nullable|string|max:15',
            'faks' => 'nullable|string|max:15',
            'user_id' => 'nullable|integer',

        ]);

        // Save data to the database
        $applicant = Applicant::create($validated);

        // Redirect with success message
        // return Redirect::route('applicant.index')->with('success', 'Applicant created successfully!');

        // Redirect to the next page with the applicant ID
        return Redirect::route('pengusaha.create', ['applicant_id' => $applicant->id])->with('success', 'Applicant created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $applicant = Applicant::findOrFail($id);
        return Inertia::render('Applicant/Display', [
            'Applicants' => $applicant,
        ]);
    }

    public function edit(string $id)
    {
        $applicant = Applicant::findOrFail($id);
        return Inertia::render('Applicant/Update', [
            'applicant' => $applicant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $applicant = Applicant::find($id);

        // Only allow Kerani to update if status is Borang Tidak Lengkap or Perbaharui Lesen
        if (
            (auth()->user()->role === 'kerani' || auth()->user()->role === 'Kerani')
            && !in_array($applicant->status, ['Borang Tidak Lengkap', 'Perbaharui Lesen'])
        ) {
            abort(403, 'Kerani hanya boleh kemaskini jika status adalah Borang Tidak Lengkap atau Perbaharui Lesen.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kad_pengenalan' => 'required|string|max:20',
            'warganegara' => 'required|string|min:3',
            'alamat_rumah' => 'nullable|string',
            'alamat_berlainan' => 'nullable|string',
            'email' => 'nullable|email',
            'telefon_r' => 'nullable|string|max:15',
            'telefon_b' => 'nullable|string|max:15',
            'telefon_p' => 'nullable|string|max:15',
            'faks' => 'nullable|string|max:15',
            'user_id' => 'nullable|integer',
        ]);
        $applicant->nama = $validated['nama'];
        $applicant->kad_pengenalan = $validated['kad_pengenalan'];
        $applicant->warganegara = $validated['warganegara'];
        $applicant->alamat_rumah = $validated['alamat_rumah'];
        $applicant->alamat_berlainan = $validated['alamat_berlainan'];
        $applicant->email = $validated['email'];
        $applicant->telefon_r = $validated['telefon_r'];
        $applicant->telefon_b = $validated['telefon_b'];
        $applicant->telefon_p = $validated['telefon_p'];
        $applicant->faks = $validated['faks'];

        $applicant->update();

        // Notification logic for Kerani and Pegawai Penyemakan
        if ($applicant->status === 'Borang Tidak Lengkap') {
            // If Kerani edits, remove kerani alert and set penyemak alert
            if (auth()->user()->role === 'kerani' || auth()->user()->role === 'Kerani') {
                $applicant->notification_status = 'alert_penyemak';
                $applicant->save();
            } else {
                // If not Kerani, keep alert_kerani for new incomplete forms
                $applicant->notification_status = 'alert_kerani';
                $applicant->save();
            }
        } else if (in_array($applicant->status, ['Perbaharui Lesen Diluluskan', 'Perbaharui Lesen Tidak Diluluskan'])) {
            // Notify kerani after final decision on renew
            $applicant->notification_status = 'alert_kerani';
            $applicant->save();
        } else {
            // Remove notification if status is changed to something else
            if ($applicant->notification_status) {
                $applicant->notification_status = null;
                $applicant->save();
            }
        }

        // Redirect to the users index page
        return Redirect::route('applicant.index')->with('success', 'Applicant updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $applicant = Applicant::find($id);
        $applicant->delete();
        return redirect()->back();
    }

    public function create()
    {
        $user = Auth::user();
        return Inertia::render('Applicant/Create', ['user' => $user]);
    }

    public function display(string $id)
    {
        // Method implementation
    }

    public function approveLicense(Request $request, $id)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'duration' => 'required|integer|min:1',
        ]);

        dd('test');

        $applicant = Applicant::findOrFail($id);

        if ($applicant->status !== 'Telah Disemak') {
            return response()->json(['error' => 'Only applications with "Telah Disemak" status can be approved.'], 400);
        }

        $startDate = Carbon::parse($validated['start_date']);
        $expiredDate = $startDate->addYears($validated['duration']);

        $applicant->start_date = $startDate;
        $applicant->expired_date = $expiredDate;
        $applicant->status = 'Diluluskan'; // Update status to "Approved"
        $applicant->save();

        return response()->json(['message' => 'License details updated successfully!']);
    }

    public function completeRenewal($id)
    {
        $applicant = Applicant::findOrFail($id);
        if (
            (auth()->user()->role === 'kerani' || auth()->user()->role === 'Kerani')
            && $applicant->status === 'Perbaharui Lesen'
        ) {
            $applicant->status = 'Perbaharui Lesen Dalam Semakan'; // or your review status
            $applicant->notification_status = 'alert_penyemak';
            $applicant->save();
            return response()->json(['success' => true]);
        }
        abort(403);
    }
}
