<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Applicant;
use Carbon\Carbon;

class UpdateProductStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-product-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update applicant status based on expiry date/time to match browser logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Force timezone to match your server (Asia/Kuala_Lumpur for Malaysia)
        $now = Carbon::now('Asia/Kuala_Lumpur');

        $applicants = Applicant::all();

        foreach ($applicants as $applicant) {
            $originalStatus = $applicant->status;
            $newStatus = $originalStatus;

            // Debug: Show current applicant info
            $this->info("Applicant ID: {$applicant->id}, Status: {$originalStatus}, Expired Date: {$applicant->expired_date}, Expired Time: {$applicant->expired_time}");

            // Skip auto-expiry for renewal in progress or under review
            // But DO NOT skip Perbaharui Lesen Diluluskan (treat like Diluluskan)
            if (in_array($originalStatus, [
                'Perbaharui Lesen',
                'Perbaharui Lesen Dalam Semakan',
                'Perbaharui Lesen Telah Disemak',
                'Perbaharui Lesen Borang Tidak Lengkap',
                'Perbaharui Lesen Tidak Diluluskan'
                // Do NOT include 'Perbaharui Lesen Diluluskan' here!
            ])) {
                continue;
            }

            if (
                $applicant->expired_date &&
                $applicant->expired_date !== '0000-00-00' &&
                $applicant->expired_date !== '' &&
                $applicant->expired_date !== null &&
                $applicant->expired_time &&
                $applicant->expired_time !== '' &&
                $applicant->expired_time !== null
            ) {
                try {
                    $expiry = Carbon::parse($applicant->expired_date . ' ' . $applicant->expired_time, 'Asia/Kuala_Lumpur');
                } catch (\Exception $e) {
                    $this->error("Invalid date/time for applicant ID {$applicant->id}");
                    continue;
                }

                $this->info("Now: {$now->format('Y-m-d H:i:s')}, Expiry: {$expiry->format('Y-m-d H:i:s')}");

                if ($expiry->isPast()) {
                    $newStatus = 'Tamat Tempoh Lesen';
                } else {
                    $minutes = $now->diffInMinutes($expiry, false);
                    if ($minutes >= 0 && $minutes <= 30) {
                        $newStatus = 'Hampir Tamat Tempoh Lesen';
                    } else {
                        // Only set to Diluluskan if it was previously Diluluskan or Perbaharui Lesen Diluluskan
                        if (in_array($originalStatus, ['Diluluskan', 'Perbaharui Lesen Diluluskan'])) {
                            $newStatus = $originalStatus;
                        }
                    }
                }
            }

            if ($newStatus !== $originalStatus) {
                $this->info("Updating applicant ID {$applicant->id} from '{$originalStatus}' to '{$newStatus}'");
                $applicant->status = $newStatus;
                $applicant->save();
            }
        }

        $this->info('Status update completed.');
        return 0;
    }
}
