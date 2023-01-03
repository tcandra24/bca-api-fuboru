<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiTesting extends Controller
{
    public function index()
    {
        return response()->json([
            'domain' => request()->root(),
        ], 400);
    }
}