<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengalamanPenyelia extends Model
{
    use HasFactory;

    protected $fillable = ['kod_id', 'penyelia_id', 'is_true'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'penyelia_id');
    }

    public function configCode()
    {
        return $this->belongsTo(ConfigCode::class, 'kod_id');
    }
    public function butiranPenyelia()
    {
        return $this->belongsTo(ButiranPenyelia::class, 'penyelia_id');
    }


}
