<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaklumatPekerja extends Model
{
    use HasFactory;
    protected $fillable=[
        'applicant_id',
        'nama_pekerja',
        'kad_pengenalan_pekerja',
        'umur_pekerja',
        'jantina_pekerja',
        'kelayakan_pekerja',
        'jawatan_pekerja',
        'tarikh_mula_pekerja',
    ];
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'id');
    }
}
