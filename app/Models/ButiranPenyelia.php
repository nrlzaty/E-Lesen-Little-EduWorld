<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ButiranPenyelia extends Model
{
    use HasFactory;

    protected $table = "butiran_penyelias";

    protected $fillable = [
        'applicant_id',
        'nama_penyelia',
        'kad_pengenalan_penyelia',
        'umur_penyelia',
        'kelulusan_akademik_penyelia', // fixed typo here
        'jenis_pengalaman_penyelia',
    ];

    protected $casts = [
        'jenis_pengalaman_penyelia' => 'array',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'id');
    }

    public function PengalamanPenyelia()
    {
        // Use the correct pivot table name and keys
        return $this->belongsToMany(
            \App\Models\ConfigCode::class,
            'pengalaman_penyelias', // correct table name (singular)
            'penyelia_id',         // foreign key to this model in pivot
            'kod_id'               // foreign key to ConfigCode in pivot
        );
    }
}
