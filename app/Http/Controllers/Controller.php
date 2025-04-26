<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    public function __construct()
    {
         // set local for api routes
         if (Auth::guard('api')->check()) {
            $userLanguage = Auth::guard('api')->user()->language;
            app()->setLocale($userLanguage);
        }
    }
}
