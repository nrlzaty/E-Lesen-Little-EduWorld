<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ButiranPengusaha extends Model
{
    use HasFactory;
    protected $fillable=[
        'applicant_id',
        'nama_pengusaha',
        'kad_pengenalan_pengusaha',
        'warganegara_pengusaha',
        'alamat_rumah_pengusaha',
        'alamat_berlainan_pengusaha',
        'email_pengusaha',
        'telefon_pengusaha',
        'faks_pengusaha',
        'jenis_pengusaha',
        

    ];
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'id');
    }
}
