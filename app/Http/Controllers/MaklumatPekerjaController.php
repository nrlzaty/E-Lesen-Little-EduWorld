<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\MaklumatPekerja;
use App\Models\ConfigCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MaklumatPekerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MaklumatPekerja::with('applicant');

        // Apply search filter
        // if ($request->has('search') && $request->search) {
        //     $query->where('nama_taska', 'like', '%' . $request->search . '%')
        //           ->orWhere('alamat_taska', 'like', '%' . $request->search . '%');
        // }

        $maklumatPekerja = $query->paginate(2);

        return Inertia::render('MaklumatPekerja/Index', [
            'maklumatPekerja' => $maklumatPekerja,
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
            'workers' => 'required|array',
            'workers.*.nama_pekerja' => 'required|string|max:255',
            'workers.*.kad_pengenalan_pekerja' => 'required|string|max:20',
            'workers.*.umur_pekerja' => 'nullable|string|max:2',
            'workers.*.jantina_pekerja' => 'nullable|string',
            'workers.*.kelayakan_pekerja' => 'nullable|string',
            'workers.*.jawatan_pekerja' => 'nullable|string',
            'workers.*.tarikh_mula_pekerja' => 'nullable|date_format:Y-m-d',
        ]);

        foreach ($validated['workers'] as $worker) {
            MaklumatPekerja::create([
                'applicant_id' => $validated['applicant_id'],
                'nama_pekerja' => $worker['nama_pekerja'],
                'kad_pengenalan_pekerja' => $worker['kad_pengenalan_pekerja'],
                'umur_pekerja' => $worker['umur_pekerja'],
                'jantina_pekerja' => $worker['jantina_pekerja'],
                'kelayakan_pekerja' => $worker['kelayakan_pekerja'],
                'jawatan_pekerja' => $worker['jawatan_pekerja'],
                'tarikh_mula_pekerja' => $worker['tarikh_mula_pekerja'],
            ]);
        }

        // Redirect with success message
        return Redirect::route('dokumen.create', ['applicant_id' => $validated['applicant_id']])->with('success', 'Maklumat Pekerja created successfully!');
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
    
        $maklumatPekerja = MaklumatPekerja::where('applicant_id', $applicant_id)->first();
    
        return Inertia::render('MaklumatPekerja/Display', [
            'maklumatPekerja' => $maklumatPekerja,
            'applicant' => $application,
        ]);
    }
    public function display(string $id)
    {
        $maklumatPekerja = MaklumatPekerja::where($id);
        return Inertia::render('MaklumatPekerja/Display', [
            'maklumatPekerja' => $maklumatPekerja,
        
        ]);
    }
    public function edit($applicant_id)
    {
        $application = Applicant::find($applicant_id);
        $maklumatPekerja = MaklumatPekerja::where('applicant_id',$applicant_id)->first();
        $jantina_pekerja=ConfigCode::where('kategori','jantina')->get();
        return Inertia::render('MaklumatPekerja/Update', [
            'applicant' => $application,
            'jantina' => $jantina_pekerja,
            'maklumatPekerja' => $maklumatPekerja,
            'applicant_id' => $applicant_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'applicant_id' => 'nullable',
            'nama_pekerja' => 'required|string|max:255',
            'kad_pengenalan_pekerja' => 'required|string|max:20',           
            'umur_pekerja' => 'nullable|string|max:2',
            'jantina_pekerja' => 'nullable|string', 
            'kelayakan_pekerja' => 'nullable|string',
            'jawatan_pekerja' => 'nullable|string',
            'tarikh_mula_pekerja' => 'nullable|date_format:Y-m-d',
            
        ]);

        $maklumatPekerja=MaklumatPekerja::find($id);
        $maklumatPekerja->applicant_id=$validated['applicant_id'];
        $maklumatPekerja->nama_pekerja=$validated['nama_pekerja'];
        $maklumatPekerja->kad_pengenalan_pekerja=$validated['kad_pengenalan_pekerja'];
        $maklumatPekerja->umur_pekerja=$validated['umur_pekerja'];
        $maklumatPekerja->jantina_pekerja=$validated['jantina_pekerja'];
        $maklumatPekerja->kelayakan_pekerja=$validated['kelayakan_pekerja'];
        $maklumatPekerja->jawatan_pekerja=$validated['jawatan_pekerja'];
        $maklumatPekerja->tarikh_mula_pekerja=$validated['tarikh_mula_pekerja'];
       

        $maklumatPekerja->update();

        return Redirect::route('maklumatpekerja.edit', $maklumatPekerja->applicant_id)->with('success', 'Maklumat Pekerja updated successfully!');

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
        $maklumatPekerja = MaklumatPekerja::where('applicant_id',$applicant_id)->first();
        $jantina_pekerja=ConfigCode::where('kategori','jantina')->get();
        return Inertia::render('MaklumatPekerja/Create', [
            'applicant' => $application,
            'jantina' => $jantina_pekerja,
            'maklumatPekerja' => $maklumatPekerja,
            'applicant_id' => $applicant_id,
        ]);

        

    }
}
