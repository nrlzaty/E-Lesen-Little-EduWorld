<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\ButiranPengusaha;
use App\Models\ButiranPengurus;
use App\Models\ButiranPenyelia;
use App\Models\ButiranTaska;
use App\Models\MaklumatPekerja;

class PDFController extends Controller
{
    public function generatePDF(Request $request)
    {
        // Validate the incoming request to ensure required data is provided
        $request->validate([
            'id' => 'required|exists:applicants,id',
        ]);
    
        // Fetch the applicant's data based on the provided ID
        $applicant = Applicant::find($request->id);
        $butiranPengusaha = ButiranPengusaha::where('applicant_id', $applicant->id)->first();
        $butiranPengurus = ButiranPengurus::where('applicant_id', $applicant->id)->first();
        $butiranPenyelia = ButiranPenyelia::where('applicant_id', $applicant->id)->first();
        $butiranTaska = ButiranTaska::where('applicant_id', $applicant->id)->first();
        $maklumatPekerja = MaklumatPekerja::where('applicant_id', $applicant->id)->get();
    
        // Generate the PDF using a Blade view
        $pdf = Pdf::loadView('pdf.applicant', [
            'applicant' => $applicant,
            'butiranPengusaha' => $butiranPengusaha,
            'butiranPengurus' => $butiranPengurus,
            'butiranPenyelia' => $butiranPenyelia,
            'butiranTaska' => $butiranTaska,
            'maklumatPekerja' => $maklumatPekerja,
        ]);
    
        // Return the PDF as a response for download
        return $pdf->download('applicant.pdf');
    }
    public function downloadAdminPDF($id)
    {
        // Fetch the applicant's data based on the provided ID
        $applicant = Applicant::find($id);
        $butiranPengusaha = ButiranPengusaha::where('applicant_id', $applicant->id)->first();
        $butiranPengurus = ButiranPengurus::where('applicant_id', $applicant->id)->first();
        $butiranPenyelia = ButiranPenyelia::where('applicant_id', $applicant->id)->first();
        $butiranTaska = ButiranTaska::where('applicant_id', $applicant->id)->first();
        $maklumatPekerja = MaklumatPekerja::where('applicant_id', $applicant->id)->get();

        if (!$applicant) {
            return redirect()->back()->with('error', 'Applicant not found.');
        }

        // Generate the PDF using a Blade view
        $pdf = Pdf::loadView('pdf.admin_applicant', [
            'applicant' => $applicant,
            'butiranPengusaha' => $butiranPengusaha,
            'butiranPengurus' => $butiranPengurus,
            'butiranPenyelia' => $butiranPenyelia,
            'butiranTaska' => $butiranTaska,
            'maklumatPekerja' => $maklumatPekerja,
        ]);

        // Return the PDF as a response for download
        return $pdf->download('admin_applicant.pdf');
    }
    public function index()
    {
        //
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
