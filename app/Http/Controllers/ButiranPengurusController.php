<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ConfigCode;
use App\Models\ButiranPengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;


class ButiranPengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ButiranPengurus::with('applicant');

        // Apply search filter
        // if ($request->has('search') && $request->search) {
        //     $query->where('nama_taska', 'like', '%' . $request->search . '%')
        //           ->orWhere('alamat_taska', 'like', '%' . $request->search . '%');
        // }

        $butiranPengurus = $query->paginate(2);

        return Inertia::render('ButiranPengurus/Index', [
            'butiranPengurus' => $butiranPengurus,
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
            'nama_pengurus' => 'required|string|max:255',
            'kad_pengenalan_pengurus' => 'required|string|max:20',           
            'umur_pengurus' => 'nullable|string|max:2',
            'kelulusan_akademik_pengurus' => 'required|string', 
            'jawatan_hakiki_pengurus' => 'nullable|string',
            'jenis_pengalaman_pengurus' => 'nullable|array',
            
        ]);
        $butiran=ButiranPengurus::where('applicant_id',$validated['applicant_id'])->first();
        if($butiran)
        {
            $butiran->update($validated);
            $butiran->PengalamanPengurus()->sync($validated['jenis_pengalaman_pengurus']);
    
        }
        else
        {
            $butiran=ButiranPengurus::create($validated);
            $butiran->PengalamanPengurus()->sync($validated['jenis_pengalaman_pengurus']);

        }
       
       
        
        // Redirect with success message
        return Redirect::route('butiranpenyelia.create',$validated['applicant_id'])->with('success', 'Pengurus created successfully!');
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

        // Eager load PengalamanPengurus and their configCode
        $butiranPengurus = ButiranPengurus::with('PengalamanPengurus')->where('applicant_id', $applicant_id)->first();

        // Prepare pengalaman_keterangan array
        $pengalaman_keterangan = [];
        if ($butiranPengurus && $butiranPengurus->PengalamanPengurus) {
            $pengalaman_keterangan = $butiranPengurus->PengalamanPengurus->pluck('keterangan')->toArray();
        }

        return Inertia::render('ButiranPengurus/Display', [
            'butiranPengurus' => $butiranPengurus,
            'applicant' => $application,
            'pengalaman_keterangan' => $pengalaman_keterangan, // Pass to frontend
        ]);
    }
    
    public function display(string $id)
    {
        $butiranPengurus = ButiranPengurus::where($id);
        return Inertia::render('ButiranPengurus/Display', [
            'butiranPengurus' => $butiranPengurus,
        
        ]);
    }
    public function edit($applicant_id)
    {
        $applicant = Applicant::find($applicant_id);
        $butiranPengurus = ButiranPengurus::with('PengalamanPengurus')->where('applicant_id',$applicant_id)->first();
        $jenis_pengalaman_pengurus = ConfigCode::where('kategori', 'jenis_pengalaman_pengurus')->get();
    // dd($butiranPengurus);

        return Inertia::render('ButiranPengurus/Update', [
            'applicant' => $applicant,
            'jenis_pengalaman_pengurus' => $jenis_pengalaman_pengurus, // Pass experiences
            'butiranPengurus'=>$butiranPengurus,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'applicant_id' => 'nullable',
            'nama_pengurus' => 'required|string|max:255',
            'kad_pengenalan_pengurus' => 'required|string|max:20',           
            'umur_pengurus' => 'nullable|string|max:2',
            'kelulusan_akademik_pengurus' => 'required|string', 
            'jawatan_hakiki_pengurus' => 'nullable|string',
            'jenis_pengalaman_pengurus' => 'nullable|array',
            
        ]);
        $butiranPengurus=ButiranPengurus::find($id);
        $butiranPengurus->applicant_id=$validated['applicant_id'];
        $butiranPengurus->nama_pengurus=$validated['nama_pengurus'];
        $butiranPengurus->kad_pengenalan_pengurus=$validated['kad_pengenalan_pengurus'];
        $butiranPengurus->umur_pengurus=$validated['umur_pengurus'];
        $butiranPengurus->kelulusan_akademik_pengurus=$validated['kelulusan_akademik_pengurus'];
        $butiranPengurus->jawatan_hakiki_pengurus=$validated['jawatan_hakiki_pengurus'];
        $butiranPengurus->jenis_pengalaman_pengurus=$validated['jenis_pengalaman_pengurus'];

        $butiranPengurus->update();

        return Redirect::route('butiranpengurus.edit', $butiranPengurus->applicant_id)->with('success', 'Butiran Pengurus updated successfully!');



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
        $applicant = Applicant::find($applicant_id);
        $butiranPengurus = ButiranPengurus::with('PengalamanPengurus')->where('applicant_id',$applicant_id)->first();
        $jenis_pengalaman_pengurus = ConfigCode::where('kategori', 'jenis_pengalaman_pengurus')->get();
    // dd($butiranPengurus);

        return Inertia::render('ButiranPengurus/Create', [
            'applicant' => $applicant,
            'jenis_pengalaman_pengurus' => $jenis_pengalaman_pengurus, // Pass experiences
            'butiranPengurus'=>$butiranPengurus,
            // 'applicant_id' => $applicant_id, // Pass the applicant_id to the view
        ]);
    }
    
}
