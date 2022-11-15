<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ERP_Invoice;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->validate($request,  [
                'virtual_account' => 'required|string'
            ]);

            $virtual_account = trim($request->virtual_account);
            $invoice = ERP_Invoice::select(
                'masterjual.virtual_account', 
                'masterjual.kode_nota', 
                'masterjual.cust AS pelanggan', 
                'pelanggan.perusahaan', 
                'masterjual.total_bayar'
            )
                ->join('pelanggan', 'pelanggan.kode', '=', 'masterjual.cust')
                ->where('masterjual.virtual_account', '=', $virtual_account);

            if(!$invoice->exists()) {
                throw new \Exception('Virtual account does not found');
            }

            return response()->json([
                'success' => true,
                'message' =>  [
                    'invoice' => $invoice->first()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
        
    }
}