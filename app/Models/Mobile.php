<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobile extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile',
        'country_code_id',
        'model_id',
        'model_type',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function countryCode()
    {
        return $this->belongsTo(CountryCode::class);
    }
}
