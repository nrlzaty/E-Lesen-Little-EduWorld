<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Applicant extends Model
{
    use HasFactory;
    protected $fillable=[
       'nama',
        'kad_pengenalan',
        'warganegara',
        'alamat_rumah',
        'alamat_berlainan',
        'email',
        'telefon_r',
        'telefon_b',
        'telefon_p',
        'faks',
        'user_id',
        'start_date',
        'expiry_date',
        'status',
        'notification_status', // <-- add this
        'application_file',
        'komen',
        'expired_date',
        'expired_time',
    ];
    
    public function butiranPengusaha()
    {
        return $this->hasMany(ButiranPengusaha::class, 'applicant_id', 'id');
    }
    public function butiranTaska()
    {
        return $this->hasMany(ButiranTaska::class, 'applicant_id', 'id');
    }
    public function ButiranPengurus()
    {
        return $this->hasMany(ButiranPengurus::class, 'applicant_id', 'id');
    }
    public function ButiranPenyelia()
    {
        return $this->hasMany(ButiranPenyelia::class, 'applicant_id', 'id');
    }
    public function MaklumatPekerja()
    {
        return $this->hasMany(MaklumatPekerja::class, 'applicant_id', 'id');
    }
    public function workers()
    {
        return $this->hasMany(MaklumatPekerja::class, 'applicant_id', 'id');
    }
    public function PengalamanPengurus()
    {
        return $this->belongsToMany(ConfigCode::class, 'pengalaman_pengurus', 'pengurus_id', 'kod_id');
    }

    public function PengalamanPenyelia()
    {
        return $this->belongsToMany(ConfigCode::class, 'pengalaman_penyelias','penyelia_id', 'kod_id');
    }

    public function isExpired()
    {
        return $this->expired_date && Carbon::now()->greaterThan($this->expired_date);
    }
    public function scopeExpiringSoon($query, $minutes = 5)
    {
        $now = Carbon::now();
        $soon = $now->copy()->addMinutes($minutes);
        return $query->where('expired_date', '>', $now)
                     ->where('expired_date', '<=', $soon)
                     ->where('status', 'Diluluskan');
    }

    public function scopeExpired($query)
    {
        return $query->where('expired_date', '<', Carbon::now())
                     ->where('status', 'Diluluskan');
    }

    // Add status constants for consistency
    const STATUS_DALAM_SEMAKAN = 'Dalam Semakan';
    const STATUS_TELAH_DISEMAK = 'Telah Disemak';
    const STATUS_PERBAHARUI_LESEN_DALAM_SEMAKAN = 'Perbaharui Lesen Dalam Semakan';
    const STATUS_PERBAHARUI_LESEN_TELAH_DISEMAK = 'Perbaharui Lesen Telah Disemak';
    // ...add others as needed...
}
