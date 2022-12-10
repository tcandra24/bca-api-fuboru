<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ERP_Invoice;
use App\Models\ERP_Customer;

class InvoiceController extends Controller
{
    public function inquiry(Request $request)
    {
        try {
            $this->validate($request,  [
                'virtualAccountNo' => 'required|string'
            ]);

            $virtual_account = trim($request->virtualAccountNo);
            $customer = ERP_Customer::select('kode' ,'perusahaan AS name', 'kode_area_telp', 'telp', 'virtual_account')
            ->where('virtual_account', '=', $virtual_account);

            if(!$customer->exists()) {
                throw new \Exception('Virtual account does not found');
            }
            
            $customer = $customer->first();
            $invoices = ERP_Invoice::select(
                'masterjual.kode_nota', 
                'pelanggan.perusahaan',
                'masterjual.sisa_bayar',
                'masterjual.tgl'
            )
            ->selectRaw('CAST(masterjual.sisa_bayar AS decimal) AS total_bayar')
                ->join('pelanggan', 'pelanggan.kode', '=', 'masterjual.cust')
                ->where('pelanggan.kode', '=', $customer->kode)
                ->where('masterjual.sisa_bayar', '>', 0)
                ->orderBy('tgl')
                ->get();

            if($invoices->count() < 1){
                throw new \Exception('Invoice data not found');
            }

            $total_amount = $invoices->sum('total_bayar');
            
            JWTAuth::invalidate($request->token);
            
            return response()->json([
                'success'               => true,
                'responseMessage'       => 'Successfull',
                'virtualAccountNo'      => $customer->virtual_account,
                'virtualAccountName'    => $customer->name,
                'virtualAccountPhone'   => $customer->kode_area_telp . ' - ' . $customer->telp,
                'billDetails'           => $invoices,
                'totalAmount'           => 
                [
                    "value"     => $total_amount,
                    "currency"  => "IDR"
                ],
            ], 200, [], JSON_NUMERIC_CHECK);
            
        } catch (\Exception $e) {
            JWTAuth::invalidate($request->token);
            
            return response()->json([
                'success' => false,
                'responseMessage' => $e->getMessage(),
            ], 400);
        }
    }

    public function payment(Request $request){
        try {
            $this->validate($request,  [
                'virtualAccountNo'      => 'required|string',
                'virtualAccountName'    => 'required|string',
                'paidAmount'            => 'required|numeric'
            ]);

            $virtual_account = trim($request->virtualAccountNo);
            $customer_name = trim($request->virtualAccountName);
            $pay_amount = $request->paidAmount;

            $customer = ERP_Customer::select('kode' ,'perusahaan AS name', 'kode_area_telp', 'telp', 'virtual_account')
            ->where('virtual_account', '=', $virtual_account)
            ->where('perusahaan', '=', $customer_name);

            if(!$customer->exists()) {
                throw new \Exception('Virtual account does not found');
            }
            
            $customer = $customer->first();

            $invoices = ERP_Invoice::select('masterjual.kode_nota')
                ->selectRaw('CAST(masterjual.terbayar AS decimal) AS terbayar')
                ->selectRaw('CAST(masterjual.sisa_bayar AS decimal) AS sisa_bayar')
                ->selectRaw('CAST(masterjual.total_bayar AS decimal) AS total_bayar')
                ->join('pelanggan', 'pelanggan.kode', '=', 'masterjual.cust')
                ->where('pelanggan.kode', '=', $customer->kode)
                ->where('masterjual.sisa_bayar', '>', 0)
                ->orderBy('tgl')
                ->get();
            
            if($invoices->count() < 1){
                throw new \Exception('Invoice data not found');
            }
            
            foreach($invoices as $invoice) {
                if($pay_amount > 0){
                    if($pay_amount < $invoice->total_bayar){
                        $amount = $pay_amount;
                    } elseif($invoice->sisa_bayar < $pay_amount){
                        $amount = $invoice->sisa_bayar;
                    }else{
                        $amount = $invoice->total_bayar;
                    }
    
                    ERP_Invoice::where('kode_nota', '=', $invoice->kode_nota)
                        ->update([
                            'terbayar' => $invoice->terbayar + $amount,
                            'sisa_bayar' => $invoice->sisa_bayar - $amount
                        ]);

                    $pay_amount -= $amount;
                }
            }
            
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success'           => true,
                'responseMessage'   => 'Payment Success'
            ], 200);
        } catch (\Exception $e) {
            JWTAuth::invalidate($request->token);
            
            return response()->json([
                'success'           => false,
                'responseMessage'   => $e->getMessage()
            ], 400);
        }
    }
}