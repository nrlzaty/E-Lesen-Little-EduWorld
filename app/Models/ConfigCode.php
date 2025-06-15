<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigCode extends Model
{
    use HasFactory;
    protected $fillable=[
        'kod',
        'kod_lain',
        'kategori',
        'keterangan',
        'is_aktif',
    ];
}
