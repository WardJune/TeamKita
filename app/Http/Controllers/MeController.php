<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function showMe()
    {
        return new UserResource(auth()->user());
    }
}
