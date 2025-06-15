<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ButiranPengurus extends Model
{
    use HasFactory;

    protected $table = "butiran_pengurus";

    protected $fillable = [
        'applicant_id',
        'nama_pengurus',
        'kad_pengenalan_pengurus',
        'umur_pengurus',
        'kelulusan_akademik_pengurus',
        'jawatan_hakiki_pengurus',
        'jenis_pengalaman_pengurus',
    ];
    protected $casts = [
        'jenis_pengalaman_pengurus' => 'array',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'id');
    }

    public function PengalamanPengurus()
    {
        // Make sure the pivot table and keys match your DB
        return $this->belongsToMany(
            \App\Models\ConfigCode::class,
            'pengalaman_pengurus', // pivot table
            'pengurus_id',         // foreign key on pivot to this model
            'kod_id'               // foreign key on pivot to ConfigCode
        );
    }
}
