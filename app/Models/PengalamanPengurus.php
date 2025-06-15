<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengalamanPengurus extends Model
{
    use HasFactory;

    protected $fillable = [
        'kod_id', 
        'pengurus_id', 
        'is_true',
       

    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'pengurus_id');
    }

    public function configCode()
    {
        return $this->belongsTo(ConfigCode::class, 'kod_id');
    }
    public function butiranPengurus()
{
    return $this->belongsTo(ButiranPengurus::class, 'pengurus_id');
}

}
