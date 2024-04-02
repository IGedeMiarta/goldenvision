<?php

namespace App\Http\Controllers\Gateway\Espay;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function payment(Request $request){
        $request->validate([
            'pin' => 'required|numeric|min:1'
        ]);
        DB::beginTransaction();
        $plan = Plan::first();
        try {
            $trx                = new Deposit();
            $trx->user_id       = auth()->user()->id;
            $trx->method_code   = 1;
            $trx->amount        = $request->pin * $plan->price;
            $trx->method_currency = 'IDR';
            $trx->charge        = 0;
            $trx->rate          = 1;
            $trx->final_amo     = $request->pin * $plan->price;
            $trx->detail        = null;
            $trx->trx           = generateTrxCode();
            $trx->status        = 0;
            $trx->save();
            DB::commit();
            $notify[] = ['success', 'Order '.$request->pin.' PIN equal to '. nb($request->pin * $plan->price) .' IDR created'];
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
            return redirect()->route('user.report.deposit')->withNotify($notify);
        } catch (\Throwable $th) {
            DB::rollBack();
            $notify[] = ['error', "Error:" . $th->getMessage() ];
            return redirect()->back()->withNotify($notify);
        }
    }

}
