<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ButiranTaska extends Model
{
    use HasFactory;
    protected $fillable=[
        'applicant_id',
        'nama_taska',
        'alamat_taska',
        'telefon_taska_r',
        'telefon_taska_b',
        'telefon_taska_p',
        'faks_taska',
        'email_taska',
        'laman_web_taska',
        

    ];
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'id');
    }
}
