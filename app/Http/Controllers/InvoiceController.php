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
                'pelanggan.perusahaan'
            )
            ->selectRaw('CAST(masterjual.sisa_bayar AS decimal) AS total_bayar')
                ->join('pelanggan', 'pelanggan.kode', '=', 'masterjual.cust')
                ->where('masterjual.virtual_account', '=', $virtual_account)
                ->where('masterjual.sisa_bayar', '>', 0);

            if(!$invoice->exists()) {
                throw new \Exception('Virtual account does not found');
            }

            return response()->json([
                'success' => true,
                'message' =>  [
                    'invoice' => $invoice->first()
                ]
            ], 200, [], JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
        
    }

    public function payment(Request $request){
        try {
            $this->validate($request,  [
                'virtual_account' => 'required|string',
                'pay_amount'   => 'required|numeric'
            ]);

            $virtual_account = trim($request->virtual_account);
            $pay_amount = $request->pay_amount;

            $invoice = ERP_Invoice::select('kode_nota')
            ->selectRaw('CAST(terbayar AS decimal) AS terbayar')
            ->selectRaw('CAST(sisa_bayar AS decimal) AS sisa_bayar')
                ->where('virtual_account', '=', $virtual_account)
                ->where('masterjual.sisa_bayar', '>', 0);

            if(!$invoice->exists()) {
                throw new \Exception('Virtual account does not found');
            }

            $invoice = $invoice->first();
            ERP_Invoice::where('virtual_account', '=', $virtual_account)
            ->update([
                'terbayar' => $invoice->terbayar + $pay_amount,
                'sisa_bayar' => $invoice->sisa_bayar - $pay_amount
            ]);

            return response()->json([
                'success' => true,
                'message' =>  'Payment Success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}