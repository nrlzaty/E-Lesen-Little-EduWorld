<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ConfigCode;
use App\Models\ButiranPengusaha; 
use App\Models\ButiranTaska;
use App\Models\ButiranPengurus;
use App\Models\ButiranPenyelia;
use App\Models\MaklumatPekerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ActionListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Applicant::query();
        $User = Auth::user();

        // Apply role-based filters
        if ($User->role === "Admin") {
            // Admin: no status filter here, allow all
        } elseif ($User->role === "Pegawai Penyemakan") {
            $query->whereIn('status', [
                'Dalam Semakan',
                'Pemohonan Baru',
                'Borang Tidak Lengkap'
            ]);
        } elseif ($User->role === "Kerani") {
            $query->where('user_id', $User->id);
        } elseif ($User->role === "Pegawai Perlulusan") {
            $query->whereIn('status', ['Diluluskan','Tidak Diluluskan', 'Telah Disemak']);
        }

        // Apply search filter if present
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kad_pengenalan', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply status filter if present (for all roles)
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $Applicants = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('Status/Index', ['applicants' => $Applicants]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($applicant_id)
    {
        $applicant = Applicant::find($applicant_id);
        if (!$applicant) {
            abort(404, 'Permohonan tidak dijumpai.');
        }
        $status = ConfigCode::where('kategori', 'status')->get();
        $Pengusaha = ButiranPengusaha::where('applicant_id', $applicant_id)->first();
        $butiranTaska = ButiranTaska::where('applicant_id', $applicant_id)->first();

        // Eager load PengalamanPengurus and PengalamanPenyelia
        $butiranPengurus = ButiranPengurus::with('PengalamanPengurus')->where('applicant_id', $applicant_id)->first();
        $butiranPenyelia = ButiranPenyelia::with('PengalamanPenyelia')->where('applicant_id', $applicant_id)->first();

        $maklumatPekerja = MaklumatPekerja::where('applicant_id', $applicant_id)->first();

        // Fetch all documents for this applicant
        $documents = \App\Models\Document::where('applicant_id', $applicant_id)->get();

        // Prepare pengalaman_keterangan arrays
        $pengurus_pengalaman_keterangan = [];
        if ($butiranPengurus && $butiranPengurus->PengalamanPengurus) {
            $pengurus_pengalaman_keterangan = $butiranPengurus->PengalamanPengurus->pluck('keterangan')->toArray();
        }
        $penyelia_pengalaman_keterangan = [];
        if ($butiranPenyelia && $butiranPenyelia->PengalamanPenyelia) {
            $penyelia_pengalaman_keterangan = $butiranPenyelia->PengalamanPenyelia->pluck('keterangan')->toArray();
        }

        return Inertia::render('Status/Display', [
            'Applicants' => $applicant,
            'status' => $status,
            'Pengusaha' => $Pengusaha, 
            'butiranTaska' => $butiranTaska,
            'butiranPengurus' => $butiranPengurus,
            'butiranPenyelia' => $butiranPenyelia,
            'maklumatPekerja' => $maklumatPekerja,
            'userRole' => Auth::user()->role,
            'documents' => $documents,
            'pengurus_pengalaman_keterangan' => $pengurus_pengalaman_keterangan,
            'penyelia_pengalaman_keterangan' => $penyelia_pengalaman_keterangan,
        ]);
    }

    public function create($applicant_id)
    {
        $application = Applicant::find($applicant_id);
        $status = ConfigCode::where('kategori', 'status')->get();
        dd($status);
        return Inertia::render('Status/Display', [
            'status' => $status,
            'applicant' => $application
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|max:255',
            'komen' => 'nullable|string',
            'expired_date' => 'nullable', // Accepts datetime-local string
            'expired_time' => 'nullable',
             // 
        ]);

        $applicant = Applicant::findOrFail($id);

        // Require komen for Perbaharui Lesen Tidak Diluluskan
        if (
            $request->input('status') === 'Perbaharui Lesen Tidak Diluluskan' &&
            (auth()->user()->role === 'Pegawai Perlulusan' || auth()->user()->role === 'Admin') &&
            (!$request->input('komen') || trim($request->input('komen')) === '')
        ) {
            return response()->json([
                'message' => 'Komen diperlukan untuk status Perbaharui Lesen Tidak Diluluskan.',
            ], 422);
        }

        $applicant->status = $request->input('status');
        $applicant->komen = $request->input('komen');

        // Save expiry date/time if provided and status is approved
        if (
            in_array($request->input('status'), ['Diluluskan', 'Perbaharui Lesen Diluluskan']) &&
            $request->filled('expired_date')
        ) {
            // Store as local Malaysia time, no timezone conversion
            $applicant->expired_date = $request->input('expired_date');
            $applicant->expired_time = $request->input('expired_time'); // Allow null if time not provided
        }

        // Reset expiry when Kerani submits renewal (status: Perbaharui Lesen Dalam Semakan)
        if ($request->input('status') === 'Perbaharui Lesen Dalam Semakan') {
            $applicant->expired_date = null;
            $applicant->expired_time = null;
        }

        // Notification logic
        if ($request->input('status') === 'Borang Tidak Lengkap') {
            $applicant->notification_status = 'alert_kerani';
        } elseif ($request->input('status') === 'Telah Disemak') {
            $applicant->notification_status = 'alert_perlulus';
        } elseif (
            $request->input('status') === 'Perbaharui Lesen Diluluskan' ||
            $request->input('status') === 'Perbaharui Lesen Tidak Diluluskan'
        ) {
            // Notify kerani after final decision on renew
            $applicant->notification_status = 'alert_kerani';
        } else {
            $applicant->notification_status = null;
        }

        $applicant->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Provide data for the dashboard.
     */
    public function dashboardData()
    {
        $user = Auth::user();
        $role = $user->role; // no strtolower

        $dashboardData = [
            'newApplications' => 0,
            'penyemakanPending' => 0,
            'borangTidakLengkap' => 0,
            'perlulusanPending' => 0,
            'diluluskan' => 0,
            'tidakDiluluskan' => 0,
            'userRole' => $user->role,
            'renewReviewPending' => 0,
            'renewReviewNotifications' => [],
            'renewSemakanNotifications' => [],
            'keraniSedangDiproses' => 0,
            'keraniRenewSedangDiproses' => 0,
            'keraniRenewExpiringSoon' => 0,
            // Add new fields for Pegawai Perlulusan
            'renewDiluluskan' => 0,
            'renewTidakDiluluskan' => 0,
        ];

        if ($role === 'Admin') {
            // Admin sees everything (all cards and notifications)
            $dashboardData['newApplications'] = Applicant::where('status', 'Pemohonan Baru')->count();
            $dashboardData['penyemakanPending'] = Applicant::where('status', 'Dalam Semakan')->count();
            $dashboardData['borangTidakLengkap'] = Applicant::where('status', 'Borang Tidak Lengkap')->count();
            $dashboardData['perlulusanPending'] = Applicant::where('status', 'Telah Disemak')->count();
            $dashboardData['diluluskan'] = Applicant::where('status', 'Diluluskan')->count();
            $dashboardData['tidakDiluluskan'] = Applicant::where('status', 'Tidak Diluluskan')->count();

            // Pegawai Penyemakan notifications
            $dashboardData['penyemakNotifications'] = Applicant::where('notification_status', 'alert_penyemak')
                ->where('status', '!=', 'Perbaharui Lesen Dalam Semakan')
                ->get(['id', 'nama', 'status', 'komen']);
            $dashboardData['renewReviewPending'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen')->count();
            $dashboardData['renewReviewNotifications'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen')
                ->get(['id', 'nama', 'status', 'komen']);
            $dashboardData['renewSemakanNotifications'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Dalam Semakan')
                ->get(['id', 'nama', 'status', 'komen']);

            // Pegawai Perlulusan notifications
            $dashboardData['perlulusNotifications'] = Applicant::where('notification_status', 'alert_perlulus')
                ->get(['id', 'nama', 'status', 'komen']);
            $dashboardData['renewTelahDisemak'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Telah Disemak')->count();
            $dashboardData['renewTelahDisemakNotifications'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Telah Disemak')
                ->get(['id', 'nama', 'status', 'komen']);
            $dashboardData['renewDiluluskan'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Diluluskan')->count();
            $dashboardData['renewTidakDiluluskan'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Tidak Diluluskan')->count();
        } elseif ($role === 'Pegawai Penyemakan') {
            $dashboardData['newApplications'] = Applicant::where('status', 'Pemohonan Baru')->count();
            $dashboardData['penyemakanPending'] = Applicant::where('status', 'Dalam Semakan')->count();
            $dashboardData['borangTidakLengkap'] = Applicant::where('status', 'Borang Tidak Lengkap')->count(); // Add this line
            $dashboardData['perlulusanPending'] = Applicant::where('status', 'Telah Disemak')->count();
            $dashboardData['diluluskan'] = Applicant::where('status', 'Diluluskan')->count();
            $dashboardData['tidakDiluluskan'] = Applicant::where('status', 'Tidak Diluluskan')->count();
            // Add penyemak notifications for Pegawai Penyemakan
            $dashboardData['penyemakNotifications'] = Applicant::where('notification_status', 'alert_penyemak')
                ->where('status', '!=', 'Perbaharui Lesen Dalam Semakan') // Only for Borang Tidak Lengkap flow
                ->get(['id', 'nama', 'status', 'komen']);
            // Add: count and list for renew review
            $dashboardData['renewReviewPending'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen')->count();
            $dashboardData['renewReviewNotifications'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen')
                ->get(['id', 'nama', 'status', 'komen']);
            // Add: notification for renew applications completed by Kerani
            $dashboardData['renewSemakanNotifications'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Dalam Semakan')
                ->get(['id', 'nama', 'status', 'komen']);
        } elseif ($role === 'Pegawai Perlulusan') {
            $dashboardData['perlulusanPending'] = Applicant::where('status', 'Telah Disemak')->count();
            $dashboardData['diluluskan'] = Applicant::where('status', 'Diluluskan')->count();
            $dashboardData['tidakDiluluskan'] = Applicant::where('status', 'Tidak Diluluskan')->count();
            // Add perlulus notifications for Pegawai Perlulusan
            $dashboardData['perlulusNotifications'] = Applicant::where('notification_status', 'alert_perlulus')
                ->get(['id', 'nama', 'status', 'komen']);
            // Add count for Perbaharui Lesen Telah Disemak
            $dashboardData['renewTelahDisemak'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Telah Disemak')->count();
            $dashboardData['renewTelahDisemakNotifications'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Telah Disemak')
                ->get(['id', 'nama', 'status', 'komen']);
            // Add: Perbaharui Lesen Diluluskan and Tidak Diluluskan counts for Pegawai Perlulusan
            $dashboardData['renewDiluluskan'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Diluluskan')->count();
            $dashboardData['renewTidakDiluluskan'] = \App\Models\Applicant::where('status', 'Perbaharui Lesen Tidak Diluluskan')->count();
        } elseif ($role === 'Kerani') {
            // Only include Borang Tidak Lengkap for kerani notifications
            $dashboardData['keraniNotifications'] = Applicant::where('user_id', $user->id)
                ->where('status', 'Borang Tidak Lengkap')
                ->get(['id', 'nama', 'status', 'komen']);

            // Card 1: Permohonan Baharu Sedang Diproses (Dalam Semakan & Telah Disemak)
            $dashboardData['keraniSedangDiproses'] = \App\Models\Applicant::where('user_id', $user->id)
                ->whereIn('status', ['Dalam Semakan', 'Telah Disemak'])
                ->count();

            // Card 2: Perbaharui Lesen Sedang Diproses (Perbaharui Lesen Dalam Semakan & Perbaharui Lesen Telah Disemak)
            $dashboardData['keraniRenewSedangDiproses'] = \App\Models\Applicant::where('user_id', $user->id)
                ->whereIn('status', [
                    'Perbaharui Lesen Dalam Semakan',
                    'Perbaharui Lesen Telah Disemak'
                ])
                ->count();

            // Card 3: Perbaharui Lesen Hampir Tamat Tempoh (expiry_date within 30 days from now)
            $dashboardData['keraniRenewExpiringSoon'] = \App\Models\Applicant::where('user_id', $user->id)
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', now())
                ->whereDate('expiry_date', '<=', now()->addDays(30))
                ->count();

            // Fix: Only count "Diluluskan" for this Kerani's own applications
            $dashboardData['diluluskan'] = \App\Models\Applicant::where('user_id', $user->id)
                ->where('status', 'Diluluskan')
                ->count();

            // Also show "Tidak Diluluskan" for Kerani
            $dashboardData['tidakDiluluskan'] = \App\Models\Applicant::where('user_id', $user->id)
                ->where('status', 'Tidak Diluluskan')
                ->count();

            // New: Perbaharui Lesen Diluluskan for Kerani
            $dashboardData['keraniRenewDiluluskan'] = \App\Models\Applicant::where('user_id', $user->id)
                ->where('status', 'Perbaharui Lesen Diluluskan')
                ->count();

            // New: Kerani Diluluskan Notifications (for noti block)
            $dashboardData['keraniDiluluskanNotifications'] = \App\Models\Applicant::where('user_id', $user->id)
                ->where('status', 'Diluluskan')
                ->orderBy('updated_at', 'desc')
                ->limit(1)
                ->get(['id', 'nama', 'status', 'komen']);

            // New: Kerani Perbaharui Lesen Diluluskan Notifications (for noti block)
            $dashboardData['keraniRenewDiluluskanNotifications'] = \App\Models\Applicant::where('user_id', $user->id)
                ->where('status', 'Perbaharui Lesen Diluluskan')
                ->orderBy('updated_at', 'desc')
                ->limit(1)
                ->get(['id', 'nama', 'status', 'komen']);
        }

        return Response::json($dashboardData);
    }

    /**
     * Display applicants with "Dalam Semakan" status.
     */
    public function dalamSemakan()
    {
        $applicants = Applicant::where('status', 'Dalam Semakan')
            ->orderBy('created_at', 'desc') // Order by latest created
            ->paginate(10);

        return Inertia::render('Status/DalamSemakan', [
            'applicants' => $applicants,
        ]);
    }

    /**
     * Display applicants with "Telah Disemak" status.
     */
    public function telahDisemak()
    {
        $applicants = Applicant::where('status', 'Telah Disemak')
            ->orderBy('created_at', 'desc') // Order by latest created
            ->paginate(10);

        return Inertia::render('Status/TelahDisemak', [
            'applicants' => $applicants,
        ]);
    }

    /**
     * Display applicants with "Diluluskan" status.
     */
    public function diluluskan()
    {
        $applicants = Applicant::where('status', 'Diluluskan')
            ->orderBy('created_at', 'desc') // Order by latest created
            ->paginate(10);

        return Inertia::render('Status/Diluluskan', [
            'applicants' => $applicants,
        ]);
    }

    /**
     * Display applicants with "Tidak Diluluskan" status.
     */
    public function tidakDiluluskan()
    {
        $applicants = Applicant::where('status', 'Tidak Diluluskan')
            ->orderBy('created_at', 'desc') // Order by latest created
            ->paginate(10);

        return Inertia::render('Status/TidakDiluluskan', [
            'applicants' => $applicants,
        ]);
    }

    /**
     * Display applicants with "Pemohonan Baru" status.
     */
    public function pemohonanBaru(Request $request)
    {
        $user = Auth::user();
        $query = Applicant::query();

        // Only show "Pemohonan Baru" for all roles, but restrict Kerani to their own
        if ($user->role === "Kerani") {
            $query->where('user_id', $user->id)->where('status', 'Pemohonan Baru');
        } else {
            $query->where('status', 'Pemohonan Baru');
        }

        // Add search logic here
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kad_pengenalan', 'like', '%' . $searchTerm . '%');
            });
        }

        $applicants = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('Status/PemohonanBaru', [
            'applicants' => $applicants,
        ]);
    }

    /**
     * Display applicants with "Borang Tidak Lengkap" status.
     */
    public function borangTidakLengkap()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Admin', 'Pegawai Penyemakan'])) {
            abort(403, 'Unauthorized');
        }

        $applicants = Applicant::where('status', 'Borang Tidak Lengkap')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Status/BorangTidakLengkap', [
            'applicants' => $applicants,
        ]);
    }

    /**
     * Show renew application for Pegawai Penyemakan to review and update status.
     */
    public function renewReview($id)
    {
        $applicant = \App\Models\Applicant::findOrFail($id);
        $status = \App\Models\ConfigCode::where('kategori', 'status')->get();
        $Pengusaha = \App\Models\ButiranPengusaha::where('applicant_id', $id)->first();
        $butiranTaska = \App\Models\ButiranTaska::where('applicant_id', $id)->first();
        $butiranPengurus = \App\Models\ButiranPengurus::where('applicant_id', $id)->first();
        $butiranPenyelia = \App\Models\ButiranPenyelia::where('applicant_id', $id)->first();
        $maklumatPekerja = \App\Models\MaklumatPekerja::where('applicant_id', $id)->first();

        return \Inertia\Inertia::render('Status/RenewReview', [
            'Applicants' => $applicant,
            'status' => $status,
            'Pengusaha' => $Pengusaha,
            'butiranTaska' => $butiranTaska,
            'butiranPengurus' => $butiranPengurus,
            'butiranPenyelia' => $butiranPenyelia,
            'maklumatPekerja' => $maklumatPekerja,
            'userRole' => \Illuminate\Support\Facades\Auth::user()->role,
        ]);
    }

    /**
     * List only renewal applications for Pegawai Penyemakan to review.
     */
    public function perbaharuiLesenList(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Pegawai Penyemakan', 'Admin'])) {
            abort(403, 'Unauthorized');
        }

        // Only show renewal applicants with status 'Perbaharui Lesen Dalam Semakan'
        $query = \App\Models\Applicant::whereIn('id', function($query) {
                $query->select('applicant_id')
                      ->from('renew_licenses');
            })
            ->where('status', 'Perbaharui Lesen Dalam Semakan');

        // Add search logic here
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kad_pengenalan', 'like', '%' . $searchTerm . '%');
            });
        }

        $applicants = $query->orderBy('updated_at', 'desc')->paginate(10);

        return Inertia::render('Status/PerbaharuiLesen', [
            'applicants' => $applicants,
        ]);
    }

    /**
     * List renewal applications that have been reviewed by Pegawai Penyemakan and are pending Pegawai Perlulusan action.
     */
    public function perbaharuiLesenTelahDisemak(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Pegawai Perlulusan', 'Admin'])) {
            abort(403, 'Unauthorized');
        }

        // Only show renewal applicants with status 'Perbaharui Lesen Telah Disemak'
        $query = \App\Models\Applicant::whereIn('id', function($query) {
                $query->select('applicant_id')
                      ->from('renew_licenses');
            })
            ->where('status', 'Perbaharui Lesen Telah Disemak');

        // Add search logic here (search by nama, kad_pengenalan, email, etc)
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kad_pengenalan', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $applicants = $query->orderBy('updated_at', 'desc')->paginate(10);

        return Inertia::render('Status/PerbaharuiLesenTelahDisemak', [
            'applicants' => $applicants,
        ]);
    }

    // Add this method for Pegawai Penyemakan notification bell
    public function penyemakRenewNotifications()
    {
        $user = Auth::user();
        if ($user->role !== 'Pegawai Penyemakan') {
            abort(403, 'Unauthorized');
        }
        $notifications = \App\Models\Applicant::where('status', 'Perbaharui Lesen')
            ->get(['id', 'nama', 'status', 'komen']);
        return response()->json(['notifications' => $notifications]);
    }

    // Add this method for Pegawai Penyemakan notification block (Perbaharui Lesen Dalam Semakan)
    public function penyemakRenewDalamSemakNotifications()
    {
        $user = Auth::user();
        if ($user->role !== 'Pegawai Penyemakan') {
            abort(403, 'Unauthorized');
        }
        $notifications = \App\Models\Applicant::where('status', 'Perbaharui Lesen Dalam Semakan')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'nama', 'status', 'komen', 'updated_at']);
        return response()->json(['notifications' => $notifications]);
    }

    // Add this method for Pegawai Perlulusan notification block (Telah Disemak)
    public function perlulusTelahDisemakNotifications()
    {
        $user = Auth::user();
        if ($user->role !== 'Pegawai Perlulusan') {
            abort(403, 'Unauthorized');
        }
        $notifications = \App\Models\Applicant::where('status', 'Telah Disemak')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'nama', 'status', 'komen', 'updated_at']);
        return response()->json(['notifications' => $notifications]);
    }

    // Add this method for Pegawai Perlulusan notification block (Perbaharui Lesen Telah Disemak)
    public function perlulusRenewTelahDisemakNotifications()
    {
        $user = Auth::user();
        if ($user->role !== 'Pegawai Perlulusan') {
            abort(403, 'Unauthorized');
        }
        $notifications = \App\Models\Applicant::where('status', 'Perbaharui Lesen Telah Disemak')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'nama', 'status', 'komen', 'updated_at']);
        return response()->json(['notifications' => $notifications]);
    }
}