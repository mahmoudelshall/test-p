<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    use HasFactory;

    protected $fillable = ['iso', 'name', 'phonecode'];

    public function mobiles()
    {
        return $this->hasMany(Mobile::class);
    }
}
