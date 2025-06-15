<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Applicant;

class DashboardController extends Controller
{
    public function dashboardData()
    {
        $user = Auth::user();

        // Default values
        $data = [
            'penyemakanPending' => 0,
            'perlulusanPending' => 0,
            'newApplications' => 0,
            'diluluskan' => 0,
            'tidakDiluluskan' => 0,
            'expiringSoon' => 0,
            'borangTidakLengkap' => 0,
            'keraniNotifications' => [],
            'penyemakNotifications' => [],
            'perlulusNotifications' => [],
            // ...add other dashboard fields as needed...
        ];

        if ($user) {
            // Only fetch counts if user has applications
            // Example: count permohonan diluluskan for this user
            $data['diluluskan'] = Applicant::where('user_id', $user->id)
                ->where('status', 'Diluluskan')
                ->count();

            // Example: count perbaharui lesen diluluskan for this user
            $data['keraniRenewDiluluskan'] = Applicant::where('user_id', $user->id)
                ->where('status', 'Perbaharui Lesen Diluluskan')
                ->count();

            // Example: count perbaharui lesen hampir tamat tempoh for this user
            $data['keraniRenewExpiringSoon'] = Applicant::where('user_id', $user->id)
                ->where('status', 'Perbaharui Lesen Hampir Tamat Tempoh')
                ->count();

            // ...add other counts as needed...
        }

        return response()->json($data);
    }

    // Other methods...
}