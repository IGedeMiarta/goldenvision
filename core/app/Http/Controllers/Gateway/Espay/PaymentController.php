<?php

namespace App\Http\Controllers\Gateway\Espay;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function userOrderPin(Request $request){
        DB::beginTransaction();
        try {
            $trx                = new Deposit();
            $trx->user_id       = auth()->user()->id;
            $trx->method_code   = 1;
            $trx->amount        = $request->pin * 500000;
            $trx->method_currency = 'IDR';
            $trx->charge        = 0;
            $trx->rate          = 1;
            $trx->final_amo     = $request->pin * 500000;
            $trx->detail        = null;
            $trx->trx           = generateTrxCode();
            $trx->status        = 0;
            $trx->save();
            DB::commit();
            $notify[] = ['success', 'Order '.$request->pin.' PIN equal to '.$request->pin * 500000 .' IDR created'];
            return redirect()->back()->withNotify($notify);
       } catch (\Throwable $th) {
            DB::rollBack();
            $notify[] = ['error', 'Error: ' . $th->getMessage() ];
            return redirect()->back()->withNotify($notify);
       }
    }
    public function userOrderUpdate(Request $request,$id){

        $deposit = Deposit::find($id);
        DB::beginTransaction();
        try {
            if ($request->hasFile('images')) {
                $image = $request->file('images');
                $filename = time() . '_image_' . strtolower(str_replace(" ", "",$deposit->trx)) . '.jpg';
                $path = './assets/images/deposit/';
                $imageSave = $path . $filename;
    
                $deposit->detail = '/assets/images/deposit/'.$filename;
    
                if (file_exists($imageSave)) {
                    @unlink($imageSave);
                }
    
                $image->move($path,$filename);
            }
            $deposit->status  = 2;
            $deposit->btc_amo = $request->name;
            $deposit->save();
            DB::commit();
            $notify[] = ['success', "Bukti transfer di upload"];
            return redirect()->back()->withNotify($notify);
        } catch (\Throwable $th) {
            DB::rollBack();
            $notify[] = ['error', "Error:" . $th->getMessage() ];
            return redirect()->back()->withNotify($notify);
        }


        dd($request->all());
    }
}
