<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ConfigCode;
use App\Models\PengalamanPengurus;
use Illuminate\Http\Request;
use Inertia\Inertia;


class PengalamanPengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PengalamanPengurus::with('applicant');

        // Apply search filter
        // if ($request->has('search') && $request->search) {
        //     $query->where('nama_taska', 'like', '%' . $request->search . '%')
        //           ->orWhere('alamat_taska', 'like', '%' . $request->search . '%');
        // }

        $pengalamanPengurus = $query->paginate(2);

        return Inertia::render('PengalamanPengurus/Index', [
            'pengalamanPengurus' => $pengalamanPengurus,
            // 'search' => $request->search,
        ]);
    }
    public function create($applicant_id)
    {
        $applicant = Applicant::find($applicant_id);
        $pengalaman_pengurus = ConfigCode::where('kategori','jenis_pengalaman_pengurus')->get(); 
    
        return Inertia::render('PengalamanPengurus/Create', [
            'applicant' => $applicant,
            'jenis_pengalaman_pengurus' => $pengalaman_pengurus
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'pengurus_id' => 'required|exists:butiran_pengurus,id',
            'pengalaman' => 'required|array',
            'pengalaman.*.kod_id' => 'required|exists:config_codes,id',
            'pengalaman.*.is_true' => 'required|boolean',
        ]);
    
        foreach ($request->pengalaman as $data) {
            PengalamanPengurus::updateOrCreate(
                [
                    'pengurus_id' => $request->pengurus_id,
                    'kod_id' => $data['kod_id'],
                ],
                ['is_true' => $data['is_true']]
            );
        }
    
        return redirect()->route('pengalamanpengurus.index')->with('success', 'Data saved successfully.');
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
