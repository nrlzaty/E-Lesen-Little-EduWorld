<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ConfigCode;
use Illuminate\Http\Request;
use App\Models\PengalamanPenyelia;
use Inertia\Inertia;

class PengalamanPenyeliaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PengalamanPenyelia::with('applicant');

        // Apply search filter
        // if ($request->has('search') && $request->search) {
        //     $query->where('nama_taska', 'like', '%' . $request->search . '%')
        //           ->orWhere('alamat_taska', 'like', '%' . $request->search . '%');
        // }

        $pengalamanPenyelia = $query->paginate(2);

        return Inertia::render('PengalamanPenyelia/Index', [
            'pengalamanPenyelia' => $pengalamanPenyelia,
            // 'search' => $request->search,
        ]);
    }

    public function create($applicant_id)
    {
        $applicant = Applicant::find($applicant_id);
        $pengalaman_penyelia = ConfigCode::where('kategori','jenis_pengalaman_penyelia')->get(); 
    
        return Inertia::render('PengalamanPenyelia/Create', [
            'applicant' => $applicant,
            'jenis_pengalaman_penyelia' => $pengalaman_penyelia
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'penyelia_id' => 'required|exists:butiran_penyelia,id',
            'pengalaman' => 'required|array',
            'pengalaman.*.kod_id' => 'required|exists:config_codes,id',
            'pengalaman.*.is_true' => 'required|boolean',
        ]);
    
        foreach ($request->pengalaman as $data) {
            PengalamanPenyelia::updateOrCreate(
                [
                    'penyelia_id' => $request->penyelia_id,
                    'kod_id' => $data['kod_id'],
                ],
                ['is_true' => $data['is_true']]
            );
        }
    
        return redirect()->route('pengalamanpenyelia.index')->with('success', 'Data saved successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
