<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ConfigCode;
use App\Models\ButiranPenyelia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ButiranPenyeliaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ButiranPenyelia::with('applicant');

        // Apply search filter
        // if ($request->has('search') && $request->search) {
        //     $query->where('nama_taska', 'like', '%' . $request->search . '%')
        //           ->orWhere('alamat_taska', 'like', '%' . $request->search . '%');
        // }

        $butiranPenyelia = $query->paginate(2);

        return Inertia::render('ButiranPenyelia/Index', [
            'butiranPenyelia' => $butiranPenyelia,
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
            'nama_penyelia' => 'required|string|max:255',
            'kad_pengenalan_penyelia' => 'required|string|max:20',
            'umur_penyelia' => 'required|string|min:2',
            'kelulusan_akademik_penyelia' => 'required|string',
            'jenis_pengalaman_penyelia' => 'nullable|array',

            
        ]);

        $butiran=ButiranPenyelia::where('applicant_id',$validated['applicant_id'])->first();
        if($butiran)
        {
            $butiran->update($validated);
            $butiran->PengalamanPenyelia()->sync($validated['jenis_pengalaman_penyelia']);
    
        }
        else
        {
            $butiran=ButiranPenyelia::create($validated);
            $butiran->PengalamanPenyelia()->sync($validated['jenis_pengalaman_penyelia']);

        }
       
       
        
        // Redirect with success message
        return Redirect::route('dokumen.create',$validated['applicant_id'])->with('success', 'Penyelia created successfully!');
    }
    public function create($applicant_id)
    {
        
        $applicant = Applicant::find($applicant_id);
        $butiranPenyelia = ButiranPenyelia::with('PengalamanPenyelia')->where('applicant_id',$applicant_id)->first();
        $jenis_pengalaman_penyelia = ConfigCode::where('kategori', 'jenis_pengalaman_penyelia')->get();
    
        return Inertia::render('ButiranPenyelia/Create', [
            'applicant' => $applicant,
            'jenis_pengalaman_penyelia' => $jenis_pengalaman_penyelia,
            'butiranPenyelia' => $butiranPenyelia,
        ]);

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

        // Eager load PengalamanPenyelia
        $butiranPenyelia = ButiranPenyelia::with('PengalamanPenyelia')->where('applicant_id', $applicant_id)->first();

        // Prepare pengalaman_keterangan array
        $pengalaman_keterangan = [];
        if ($butiranPenyelia && $butiranPenyelia->PengalamanPenyelia) {
            $pengalaman_keterangan = $butiranPenyelia->PengalamanPenyelia->pluck('keterangan')->toArray();
        }

        return Inertia::render('ButiranPenyelia/Display', [
            'butiranPenyelia' => $butiranPenyelia,
            'applicant' => $application,
            'pengalaman_keterangan' => $pengalaman_keterangan, // Pass to frontend
        ]);
    }
    public function display(string $id)
    {
        $butiranPenyelia = ButiranPenyelia::where($id);
        return Inertia::render('ButiranPenyelia/Display', [
            'butiranPenyelia' => $butiranPenyelia,
        
        ]);
        
    }
    public function edit($applicant_id)
    {
        $applicant = Applicant::find($applicant_id);
        $butiranPenyelia = ButiranPenyelia::with('PengalamanPenyelia')->where('applicant_id',$applicant_id)->first();
        $jenis_pengalaman_penyelia = ConfigCode::where('kategori', 'jenis_pengalaman_penyelia')->get();
    
        return Inertia::render('ButiranPenyelia/Update', [
            'applicant' => $applicant,
            'jenis_pengalaman_penyelia' => $jenis_pengalaman_penyelia,
            'butiranPenyelia' => $butiranPenyelia,
        ]);
    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'applicant_id' => 'nullable',
            'nama_penyelia' => 'required|string|max:255',
            'kad_pengenalan_penyelia' => 'required|string|max:20',
            'umur_penyelia' => 'required|string|min:2',
            'kelulusan_akademik_penyelia' => 'required|string',
            'jenis_pengalaman_penyelia' => 'nullable|array',

            
        ]);
        $butiranPenyelia=ButiranPenyelia::find($id);
        $butiranPenyelia->applicant_id=$validated['applicant_id'];
        $butiranPenyelia->nama_penyelia=$validated['nama_penyelia'];
        $butiranPenyelia->kad_pengenalan_penyelia=$validated['kad_pengenalan_penyelia'];
        $butiranPenyelia->umur_penyelia=$validated['umur_penyelia'];
        $butiranPenyelia->kelulusan_akademik_penyelia=$validated['kelulusan_akademik_penyelia'];
        $butiranPenyelia->jenis_pengalaman_penyelia=$validated['jenis_pengalaman_penyelia'];

       

        $butiranPenyelia->update();

        return Redirect::route('butiranpenyelia.edit', $butiranPenyelia->applicant_id)->with('success', 'Butiran Penyelia updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


}
