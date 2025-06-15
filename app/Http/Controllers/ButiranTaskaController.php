<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ButiranTaska;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;


class ButiranTaskaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ButiranTaska::with('applicant');

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where('nama_taska', 'like', '%' . $request->search . '%')
                  ->orWhere('alamat_taska', 'like', '%' . $request->search . '%');
        }

        $butiranTaska = $query->paginate(2);

        return Inertia::render('ButiranTaska/Index', [
            'butiranTaska' => $butiranTaska,
            'search' => $request->search,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'applicant_id' => 'nullable',
            'nama_taska' => 'required|string|max:255',
            'alamat_taska' => 'required|string|min:3',
            'telefon_taska_r' => 'nullable|string|max:15',
            'telefon_taska_b' => 'nullable|string|max:15',
            'telefon_taska_p' => 'nullable|string|max:15',
            'faks_taska' => 'nullable|string|max:15',
            'email_taska' => 'nullable|email|max:255',
            'laman_web_taska' => 'nullable|string|max:255',
        ]);

        // Save the data
        ButiranTaska::create($validated);

        return Redirect::route('butiranpengurus.create',$validated['applicant_id'])->with('success', 'Butiran Taska created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($applicant_id)
    {
        $application = Applicant::find($applicant_id);
    
        if (!$application) {
            abort(404, 'Applicant not found');
        }
    
        $butiranTaska = ButiranTaska::where('applicant_id', $applicant_id)->first();
    
        return Inertia::render('ButiranTaska/Display', [
            'butiranTaska' => $butiranTaska,
            'applicant' => $application,
        ]);
    }

    public function display(string $id)
    {
        $butiranTaska = ButiranTaska::where($id); // Retrieve the specified Taska by ID

        return Inertia::render('ButiranTaska/Display', [
            'butiranTaska' => $butiranTaska,
    ]);
        
    }

    public function edit($applicant_id)
    {
        $application = Applicant::find($applicant_id);
        $butiranTaska = ButiranTaska::where('applicant_id',$applicant_id)->first();

        return Inertia::render('ButiranTaska/Update', [
            'applicant' => $application,
            'butiranTaska' => $butiranTaska,
        ]);
    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'applicant_id' => 'nullable',
            'nama_taska' => 'required|string|max:255',
            'alamat_taska' => 'required|string|min:3',
            'telefon_taska_r' => 'nullable|string|max:15',
            'telefon_taska_b' => 'nullable|string|max:15',
            'telefon_taska_p' => 'nullable|string|max:15',
            'faks_taska' => 'nullable|string|max:15',
            'email_taska' => 'nullable|email|max:255',
            'laman_web_taska' => 'nullable|string|max:255',
        ]);
        $butiranTaska=ButiranTaska::find($id);
        $butiranTaska->applicant_id=$validated['applicant_id'];
        $butiranTaska->nama_taska=$validated['nama_taska'];
        $butiranTaska->alamat_taska=$validated['alamat_taska'];
        $butiranTaska->telefon_taska_r=$validated['telefon_taska_r'];
        $butiranTaska->telefon_taska_b=$validated['telefon_taska_b'];
        $butiranTaska->telefon_taska_p=$validated['telefon_taska_p'];
        $butiranTaska->faks_taska=$validated['faks_taska'];
        $butiranTaska->email_taska=$validated['email_taska'];
        $butiranTaska->laman_web_taska=$validated['laman_web_taska'];
       

        $butiranTaska->update();

        return Redirect::route('butirantaska.edit', $butiranTaska->applicant_id)->with('success', 'Butiran Taska updated successfully!');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function create($applicant_id)
    {
        
        $application = Applicant::find($applicant_id);
        $butiranTaska = ButiranTaska::where('applicant_id',$applicant_id)->first();

        return Inertia::render('ButiranTaska/Create', [
            'applicant' => $application,
            'butiranTaska' => $butiranTaska,
            'applicant_id' => $applicant_id, // Pass the applicant_id to the view
        ]);

    }
}
