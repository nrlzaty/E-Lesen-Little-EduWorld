<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ButiranPengusaha;
use App\Models\ConfigCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ButiranPengusahaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = ButiranPengusaha::with('applicant'); // Include the applicant relationship
    
    // Apply search filter
    if ($request->has('search') && $request->search) {
        $query->where('nama_pengusaha', 'like', '%' . $request->search . '%')
            ->orWhere('kad_pengenalan_pengusaha', 'like', '%' . $request->search . '%');
    }
    
    
    // Paginate the results
    $Pengusaha = $query->paginate(2);

    // Return to Inertia
    return Inertia::render('Pengusaha/Index', [
        'Pengusaha' => $Pengusaha,
        'search' => $request->search,
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_id' => 'nullable',
            'nama_pengusaha' => 'required|string|max:255',
            'kad_pengenalan_pengusaha' => 'required|string|max:20', 
            'warganegara_pengusaha' => 'nullable|string|min:3', 
            'alamat_rumah_pengusaha' => 'nullable|string',
            'alamat_berlainan_pengusaha' => 'nullable|string',
            'email_pengusaha' => 'nullable', 
            'telefon_pengusaha' => 'nullable|string|max:15',
            'faks_pengusaha' => 'nullable|string|max:15',
            'jenis_pengusaha' => 'nullable',
        ]);

        // Save data to the database
        ButiranPengusaha::create($validated);

        // Redirect with success message
        // return Redirect::route('applicant.edit',$validated['applicant_id'])->with('success', 'Pengusaha created successfully!');

        // Redirect to the next page with the applicant ID
        return Redirect::route('butirantaska.create', ['applicant_id' => $validated['applicant_id']])->with('success', 'Pengusaha created successfully!');
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
    
        $Pengusaha = ButiranPengusaha::where('applicant_id', $applicant_id)->first();
    
        return Inertia::render('Pengusaha/Display', [
            'Pengusaha' => $Pengusaha,
            'applicant' => $application,
        ]);
    }
    
    public function display(string $id)
    {
        $Pengusaha = ButiranPengusaha::where($id);
        return Inertia::render('Pengusaha/Display', [
            'Pengusaha' => $Pengusaha,
        
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

     public function edit($applicant_id)
    {
        $application = Applicant::find($applicant_id);
        $butiranPengusaha = ButiranPengusaha::where('applicant_id',$applicant_id)->first();
        $jenis_pengusaha=ConfigCode::where('kategori','jenis_pengusaha')->get();
        return Inertia::render('Pengusaha/Update', [
            'jenis_pengusaha' => $jenis_pengusaha,
            'applicant' => $application,
            'butiranPengusaha' => $butiranPengusaha,
        ]);
    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'applicant_id' => 'nullable',
            'nama_pengusaha' => 'required|string|max:255',
            'kad_pengenalan_pengusaha' => 'required|string|max:20', 
            'warganegara_pengusaha' => 'nullable|string|min:3', 
            'alamat_rumah_pengusaha' => 'nullable|string',
            'alamat_berlainan_pengusaha' => 'nullable|string',
            'email_pengusaha' => 'nullable', 
            'telefon_pengusaha' => 'nullable|string|max:15',
            'faks_pengusaha' => 'nullable|string|max:15',
            'jenis_pengusaha' => 'nullable',
        ]);

        $pengusaha=ButiranPengusaha::find($id);
        $pengusaha->applicant_id=$validated['applicant_id'];
        $pengusaha->nama_pengusaha=$validated['nama_pengusaha'];
        $pengusaha->kad_pengenalan_pengusaha=$validated['kad_pengenalan_pengusaha'];
        $pengusaha->warganegara_pengusaha=$validated['warganegara_pengusaha'];
        $pengusaha->alamat_rumah_pengusaha=$validated['alamat_rumah_pengusaha'];
        $pengusaha->alamat_berlainan_pengusaha=$validated['alamat_berlainan_pengusaha'];
        $pengusaha->email_pengusaha=$validated['email_pengusaha'];
        $pengusaha->telefon_pengusaha=$validated['telefon_pengusaha'];
        $pengusaha->faks_pengusaha=$validated['faks_pengusaha'];
        $pengusaha->jenis_pengusaha=$validated['jenis_pengusaha'];

        $pengusaha->update();

        return Redirect::route('pengusaha.edit', $pengusaha->applicant_id)->with('success', 'Pengusaha updated successfully!');
        // Redirect to the next page
        // return Redirect::route('butirantaska.create', ['applicant_id' => $pengusaha->applicant_id])->with('success', 'Pengusaha updated successfully!');


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
        $butiranPengusaha = ButiranPengusaha::where('applicant_id',$applicant_id)->first();
        $jenis_pengusaha=ConfigCode::where('kategori','jenis_pengusaha')->get();
        return Inertia::render('Pengusaha/Create', [
            'jenis_pengusaha' => $jenis_pengusaha,
            'applicant' => $application,
            'butiranPengusaha' => $butiranPengusaha,
            'applicant_id' => $applicant_id, // Pass the applicant_id to the view
        ]);

        

    }
    
}
