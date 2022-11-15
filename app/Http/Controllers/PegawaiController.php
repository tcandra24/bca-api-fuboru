<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ERP_Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = ERP_Pegawai::select('kode', 'nik', 'card_id')->get();
        return response()->json(['pegawai' => $pegawai]);
    }
}