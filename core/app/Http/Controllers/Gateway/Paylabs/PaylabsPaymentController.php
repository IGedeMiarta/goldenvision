<?php

namespace App\Http\Controllers\Gateway\Paylabs;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaylabsPaymentController extends Controller
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
            
            $pg = $this->requestPayment($trx);
            if($pg){
                return redirect()->to($trx->payment_url);
            }
            DB::commit();

            // $notify[] = ['success', 'Order '.$request->pin.' PIN equal to '. nb($request->pin * $plan->price) .' IDR created'];
            // return redirect()->back()->withNotify($notify);
        } catch (\Throwable $th) {
                DB::rollBack();
                $notify[] = ['error', 'Error: ' . $th->getMessage() ];
                return redirect()->back()->withNotify($notify);
        }
    }

    private function requestPayment(Deposit $trx){

        $user = Auth::user();

        $httpMethod = "POST";
        $endpointURL = "/payment/v2/h5/createLink";
        $timestamp = $trx->creted_at;
        $mid = "010414";
        $trxid = $trx->trx;
        $body = array(
            "merchantId"        =>  $mid,
            "merchantTradeNo"   => $trxid,
            "requestId"         => $trx->id,
            "amount"            => $trx->final_amo,
            "productName"       => "Deposit",
            "payer"             => $user->username, //User fullname,
            "phoneNumber"       => $user->mobile, //user mobile
            "notifyUrl"         => "", //URL yang akan ditembak saat terjadi pembayaran. Untuk parameter-parameternya cek di bagian Inquiry Order
            "redirectUrl"       => "", //Baik saat sukses ataupun gagal, akan diarahkan ke URL tersebut
        );
        $privateKeyPath = __DIR__ . "/private.pem";
        $privateKey     = file_get_contents($privateKeyPath);

        // minify json body
        $minifiedJson = minifyJsonBody(json_encode($body));
        // dd($minifiedJson);

        //membuat string content
        $stringContent = createStringContent($httpMethod,$endpointURL,$minifiedJson,$timestamp);
        // membuat signature
        $signature = createSignature($stringContent,$privateKey);
        // dd($signature);

        $datastring = json_encode($body);

        $url = 'https://sit-pay.paylabs.co.id' . $endpointURL;

        // konfigurasi cURL
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS,$datastring);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array(
            ' Content-Type: application/json;charset=utf-8',
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $signature ,
            'X-PARTNER-ID:' . $mid,
'            X-REQUEST-ID:' . $trxid
        ));

        // execute and add get response
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        // close connection
        curl_close($ch);

        $response = json_decode($result,true);
        dd($response);
        if ($response['errCode'] == 0) {
            //update status
            $trx->status = 2;
            $trx->payment_url = $response['url'];
            $trx->save();
            return true;
        }else{
            // return false;
            $notify[] = ['error', 'Error Payment gateway: ' . $response ];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

    }

}
