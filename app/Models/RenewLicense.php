<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenewLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id', // new application
        'previous_applicant_id', // old application
        'user_id',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function previousApplicant()
    {
        return $this->belongsTo(Applicant::class, 'previous_applicant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
