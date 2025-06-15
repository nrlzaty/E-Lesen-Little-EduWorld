<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Document;
use App\Models\Applicant;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function index()
    {
        // return view('documents.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'salinan_ic' => 'nullable|file|mimes:pdf|max:10240',
            'maklumat_kanak' => 'nullable|file|mimes:pdf|max:10240',
            'nisbah_kanak' => 'nullable|file|mimes:pdf|max:10240',
            'senarai_pekerja' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $applicantId = $request->input('applicant_id');

        // Salinan IC Pemohon
        if ($request->hasFile('salinan_ic')) {
            $file = $request->file('salinan_ic');
            $filePath = $file->store('documents', 'public');
            Document::create([
                'applicant_id' => $applicantId,
                'nama_dokumen' => 'Salinan IC Pemohon',
                'file_dokument' => $filePath,
            ]);
        }

        // MAKLUMAT KANAK-KANAK
        if ($request->hasFile('maklumat_kanak')) {
            $file = $request->file('maklumat_kanak');
            $filePath = $file->store('documents', 'public');
            Document::create([
                'applicant_id' => $applicantId,
                'nama_dokumen' => 'MAKLUMAT KANAK-KANAK',
                'file_dokument' => $filePath,
            ]);
        }

        // NISBAH KANAK-KANAK DENGAN PENGASUH
        if ($request->hasFile('nisbah_kanak')) {
            $file = $request->file('nisbah_kanak');
            $filePath = $file->store('documents', 'public');
            Document::create([
                'applicant_id' => $applicantId,
                'nama_dokumen' => 'NISBAH KANAK-KANAK DENGAN PENGASUH',
                'file_dokument' => $filePath,
            ]);
        }

        // SENARAI MAKLUMAT PEKERJA
        if ($request->hasFile('senarai_pekerja')) {
            $file = $request->file('senarai_pekerja');
            $filePath = $file->store('documents', 'public');
            Document::create([
                'applicant_id' => $applicantId,
                'nama_dokumen' => 'SENARAI MAKLUMAT PEKERJA',
                'file_dokument' => $filePath,
            ]);
        }

        return Redirect::route('applicant.index')->with('success', 'Dokumen berjaya dimuat naik!');
    }

    public function create($applicant_id)
    {
        $application = Applicant::find($applicant_id);
        $dokumen = Document::where('applicant_id', $applicant_id)->get()->map(function ($doc) {
            return [
                'file_dokument' => $doc->file_dokument,
            ];
        });

        return Inertia::render('Document/Create', [
            'applicant' => $application,
            'dokumen' => $dokumen,
        ]);
    }
    
    public function display($applicant_id)
    {
        $documents = Document::where('applicant_id', $applicant_id)->get();
        return Inertia::render('Document/Display', [
            'documents' => $documents,
        ]);
    }

    public function show($applicant_id)
    {
        $application = Applicant::find($applicant_id);
    
        if (!$application) {
            abort(404, 'Applicant not found');
        }
    
        $documents = Document::where('applicant_id', $applicant_id)->get(); // Fetch all documents
    
        return Inertia::render('Document/Display', [
            'documents' => $documents,
            'applicant' => $application,
        ]);
    }

    public function edit($applicant_id)
    {
        $application = Applicant::find($applicant_id);
        $documents = Document::where('applicant_id', $applicant_id)->get(); // Fetch all documents

        return Inertia::render('Document/Update', [
            'applicant' => $application,
            'documents' => $documents,
            'auth' => ['user' => auth()->user()], // Ensure auth.user is available in props
        ]);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $applicant = $document->applicant; // adjust if relation name is different

        if (
            (auth()->user()->role === 'kerani' || auth()->user()->role === 'Kerani')
            && !in_array($applicant->status, ['Borang Tidak Lengkap', 'Perbaharui Lesen'])
        ) {
            abort(403, 'Kerani hanya boleh kemaskini dokumen jika status adalah Borang Tidak Lengkap atau Perbaharui Lesen.');
        }

        $request->validate([
            'file_dokument' => 'nullable|mimes:pdf|max:10240', // 10MB max size per file
        ]);
    
        if ($request->hasFile('file_dokument')) {
            // Delete the old file if it exists
            if ($document->file_dokument) {
                Storage::disk('public')->delete($document->file_dokument);
            }
    
            // Store the new file
            $filePath = $request->file('file_dokument')->store('documents', 'public');
            $document->file_dokument = $filePath;
        }
    
        $document->save();
    
        return Redirect::route('dokumen.edit', $document->applicant_id)->with('success', 'Document updated successfully!');
    }

    public function destroy($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['error' => 'Dokumen tidak dijumpai.'], 404);
        }

        // Delete the file from storage
        if ($document->file_dokument && Storage::disk('public')->exists($document->file_dokument)) {
            Storage::disk('public')->delete($document->file_dokument);
        }

        // Delete the document record from the database
        $document->delete();

        return response()->json(['success' => 'Dokumen berjaya dipadam.']);
    }
}