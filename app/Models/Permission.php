<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Translatable\HasTranslations;

class Permission extends SpatiePermission
{
    use HasTranslations;

    public $translatable = ['display'];

    protected $fillable = [
        'name',
        'guard_name',
        'display',
        'permission_group',
    ];
}
