<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Models\ConfigCode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ConfigCode::query()
            ->groupBy('config_codes.kategori')
            ->select('config_codes.kategori', ConfigCode::raw('MAX(config_codes.created_at) as latest_created_at')) // Use MAX to get the latest created_at
            ->orderBy('latest_created_at', 'desc'); // Order by the aggregated column

        if ($request->has('search') && $request->search) {
            $query->where('config_codes.kategori', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(10);

        return Inertia::render('Configration/Categorys/Index', [
            'categories' => $categories,
            'search' => $request->search,
        ]);
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
