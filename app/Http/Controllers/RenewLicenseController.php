<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\RenewLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RenewLicenseController extends Controller
{
    public function index(Request $request)
    {
        // Update: check for 'Kerani'
        if (auth()->user()->role !== 'Kerani') {
            abort(403);
        }

        $userId = auth()->id();
        // Exclude applicants with any renewal in progress (not just Perbaharui Lesen)
        $renewalStatuses = [
            'Perbaharui Lesen',
            'Perbaharui Lesen Dalam Semakan',
            'Perbaharui Lesen Telah Disemak',
            'Perbaharui Lesen Diluluskan',
            'Perbaharui Lesen Tidak Diluluskan',
            'Perbaharui Lesen Borang Tidak Lengkap'
        ];

        $applicants = Applicant::where('status', 'Hampir Tamat Tempoh Lesen')
            ->where('user_id', $userId)
            ->whereNotIn('id', function($q) use ($renewalStatuses) {
                $q->select('previous_applicant_id')
                  ->from('renew_licenses')
                  ->whereIn('applicant_id', function($q2) use ($renewalStatuses) {
                      $q2->select('id')
                         ->from('applicants')
                         ->whereIn('status', $renewalStatuses);
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Attach renew_in_progress property
        foreach ($applicants as $applicant) {
            $renew = \App\Models\RenewLicense::where('previous_applicant_id', $applicant->id)
                ->whereHas('applicant', function($q) use ($renewalStatuses) {
                    $q->whereIn('status', $renewalStatuses);
                })
                ->first();
            $applicant->renew_in_progress = $renew ? $renew->applicant_id : null;
        }

        return inertia('RenewLicense/Index', [
            'applicants' => $applicants,
        ]);
    }

    public function renew($id)
    {
        // Update: check for 'Kerani'
        if (auth()->user()->role !== 'Kerani') {
            abort(403);
        }

        $old = Applicant::findOrFail($id);

        // Check if a renewal already exists for this old applicant and is not completed
        $existingRenew = \App\Models\RenewLicense::where('previous_applicant_id', $old->id)
            ->whereHas('applicant', function($q) {
                $q->where('status', 'Perbaharui Lesen');
            })
            ->first();

        if ($existingRenew) {
            // Redirect to edit the existing renewal, do not clone again
            return redirect()->route('applicant.edit', $existingRenew->applicant_id);
        }

        // Clone applicant (except id, created_at, updated_at, status, etc)
        $new = $old->replicate(['id', 'created_at', 'updated_at', 'status']);
        $new->status = 'Perbaharui Lesen'; // <-- Use the new kategori status exactly as in your config
        $new->save();

        // Link in renew_licenses
        RenewLicense::create([
            'applicant_id' => $new->id,
            'previous_applicant_id' => $old->id,
            'user_id' => Auth::id(),
        ]);

        // Clone related data (Pengusaha, Taska, Pengurus, Penyelia, Maklumat Pekerja)
        $relatedModels = [
            \App\Models\ButiranPengusaha::class,
            \App\Models\ButiranTaska::class,
            \App\Models\ButiranPengurus::class,
            \App\Models\ButiranPenyelia::class,
            \App\Models\MaklumatPekerja::class,
        ];
        foreach ($relatedModels as $model) {
            $oldRelated = $model::where('applicant_id', $old->id)->first();
            if ($oldRelated) {
                $newRelated = $oldRelated->replicate(['id', 'created_at', 'updated_at']);
                $newRelated->applicant_id = $new->id;
                $newRelated->save();

                // Clone pengalaman_pengurus pivot
                if ($model === \App\Models\ButiranPengurus::class) {
                    $oldPivot = DB::table('pengalaman_pengurus')->where('pengurus_id', $oldRelated->id)->get();
                    foreach ($oldPivot as $pivot) {
                        DB::table('pengalaman_pengurus')->insert([
                            'pengurus_id' => $newRelated->id,
                            'kod_id' => $pivot->kod_id,
                            'is_true' => $pivot->is_true,
                           
                        ]);
                    }
                }
                // Clone pengalaman_penyelias pivot
                if ($model === \App\Models\ButiranPenyelia::class) {
                    $oldPivot = DB::table('pengalaman_penyelias')->where('penyelia_id', $oldRelated->id)->get();
                    foreach ($oldPivot as $pivot) {
                        DB::table('pengalaman_penyelias')->insert([
                            'penyelia_id' => $newRelated->id,
                            'kod_id' => $pivot->kod_id,
                            'is_true' => $pivot->is_true,
                        ]);
                    }
                }
            }
        }

        // Clone Document records
        $oldDocuments = \App\Models\Document::where('applicant_id', $old->id)->get();
        foreach ($oldDocuments as $oldDoc) {
            $newDoc = $oldDoc->replicate(['id', 'created_at', 'updated_at']);
            $newDoc->applicant_id = $new->id;
            $newDoc->save();
        }

        // Redirect to edit new application (not old!)
        return redirect()->route('applicant.edit', $new->id);
    }
}
