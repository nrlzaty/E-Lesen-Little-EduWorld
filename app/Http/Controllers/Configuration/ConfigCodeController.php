<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Models\ConfigCode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id, Request $request)
    {
        $query = ConfigCode::where('config_codes.kategori', $id)
            ->orderBy('config_codes.updated_at', 'DESC');

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where('config_codes.keterangan', 'like', '%' . $request->search . '%'); // Adjust column name as needed
        }

        // Paginate results and append search query
        $codes = $query->paginate(10)->appends(['search' => $request->search]);

        return Inertia::render('Configration/Codes/Index', [
            'codes' => $codes,
            'kategori' => $id,
            'search' => $request->search,  // Pass search term to the view
        ]);
    }


    public function create($category)
    {
        return Inertia::render('Configration/Categorys/Create', [
            'category' => $category
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kod' => 'required',
            'kod_lain' => 'nullable',
            'keterangan' => 'required',
            'kategori' => 'required',
            'is_aktif' => 'boolean',
        ]);

        $formatCategory = str_replace(' ','_',strtolower($validated['kategori']));

        $codes = new ConfigCode();
        $codes->kod = $validated['kod'];
        $codes->kod_lain = $validated['kod_lain'] ?? null;
        $codes->keterangan = $validated['keterangan'];
        $codes->kategori = $formatCategory;
        $codes->is_aktif = $validated['is_aktif'];
        $codes->save();

        return redirect()->route('setting.code.index', $codes->kategori);
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
    public function edit(string $id)
    {
        $codes = ConfigCode::findOrFail($id);

        return Inertia::render('Configration/Codes/Update', ['codes' => $codes]);
    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kod' => 'required',
            'kod_lain' => 'nullable',
            'keterangan' => 'required',
            'kategori' => 'required',
            'is_aktif' => 'boolean',
        ]);

        $formatCategory = str_replace(' ','_',strtolower($validated['kategori']));

        $codes = ConfigCode::findOrFail($id);
        $codes->kod = $validated['kod'];
        $codes->kod_lain = $validated['kod_lain'] ?? null;
        $codes->keterangan = $validated['keterangan'];
        $codes->kategori = $formatCategory;
        $codes->is_aktif = $validated['is_aktif'];
        $codes->update();

        return redirect()->route('setting.code.index', $codes->kategori);    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
