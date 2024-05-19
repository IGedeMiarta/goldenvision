<?php

namespace App\Http\Controllers\Gateway\Paylabs;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\LogActivity;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPin;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaylabsPaymentController extends Controller
{
    public function payment(Request $request){
        $request->validate([
            'pin' => 'required|numeric|min:1'
        ]);
        // DB::beginTransaction();
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
            $trx->status        = 2;
            $trx->save();
            
            $pg = $this->requestPayment($trx);
            // DB::commit();
            if($pg){
                return redirect()->to($trx->payment_url);
            }
        } catch (\Throwable $th) {
                // DB::rollBack();
                $notify[] = ['error', 'Error: ' . $th->getMessage() ];
                return redirect()->back()->withNotify($notify);
        }
    }

    private function requestPayment(Deposit $trx){

        $user = Auth::user();

        $httpMethod = "POST";
        $endpointURL = "/payment/v2/h5/createLink";
        $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $timestamp = $date->format('Y-m-d\TH:i:s.uP');
        $mid = "010414";
        $trxid = $trx->id;
        $body = array(
            "merchantId"        => $mid,
            "merchantTradeNo"   => $trx->trx,
            "requestId"         => $trx->id,
            "amount"            => number_format(intval($trx->final_amo),2,'.',''),
            "productName"       => "Goldenvision PIN Deposit",
            "payer"             => $user->username, //User fullname,
            "phoneNumber"       => $user->mobile, //user mobile
            "notifyUrl"         => url('api/v1/notify'), //URL yang akan ditembak saat terjadi pembayaran. Untuk parameter-parameternya cek di bagian Inquiry Order
            "redirectUrl"       => route('user.report.deposit'), //Baik saat sukses ataupun gagal, akan diarahkan ke URL tersebut
        );

        $privateKeyPath = __DIR__ . "/private.pem";
        $privateKey     = file_get_contents($privateKeyPath);

        // minify json body
        $minifiedJson = minifyJsonBody(json_encode($body));
        
        //membuat string content
        $stringContent = createStringContent($httpMethod,$endpointURL,$minifiedJson,$timestamp);

        // membuat signature
        $signature = createSignature($stringContent,$privateKey);

        $data_string = json_encode($body);

        $url = 'https://sit-pay.paylabs.co.id' . $endpointURL;

        $header = array(
            'Content-Type: application/json;charset=utf-8',
            'X-TIMESTAMP:' . $timestamp,
            'X-SIGNATURE:' . $signature ,
            'X-PARTNER-ID:' . $mid,
            'X-REQUEST-ID:' . $trxid
        );
        // konfigurasi cURL
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);

        // execute and add get response
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        // close connection
        curl_close($ch);

        $response = json_decode($result,true);
        if ($response['errCode'] == 0) {
            //update status
            $trx->status = 2;
            $trx->payment_url = $response['url'];
            $trx->save();
            return true;
        }else{
            echo    'method: ' . $httpMethod .'<br>';
            echo    'TimeStamp: ' . $timestamp.'<br>';
            echo    'Parameter: ' . $minifiedJson .'<br>';
            echo    'stringContent: ' . $stringContent.'<br>';
            echo    'signature: ' . $signature .'<br>';
            dd($header,$response);

            // return false;
            addToLog('error payment gateway: ' . implode(", ", $response));
            $notify[] = ['error', 'Error Payment gateway: ' . implode(", ", $response) ];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

    }

    //{"merchantId":"010414","merchantTradeNo":"TRX24040613340400009","requestId":18,"amount":"700000","productName":"GoldenvisionPINDeposit","payer":"miarta","phoneNumber":"62081529963914","notifyUrl":"http://dev.goldenvision.co.id/api/v1/notify","redirectUrl":"http://dev.goldenvision.co.id/user/report/deposit/log"}
    //{"merchantId":"010414","merchantTradeNo":"TRX24040613340400009","requestId":"18","amount":700000,"productName":"GoldenvisionPINDeposit","payer":"miarta","phoneNumber":"62081529963914","notifyUrl":"http://dev.goldenvision.co.id/api/v1/notify","redirectUrl":"http://dev.goldenvision.co.id/user/report/deposit/log"}

    // POST:/payment/v2/h5/createLink:9cffc712a4c2f908b38d3e4e9a1dd7e0408755100d6447611859f146af6866c9:2024-04-06T13:48:04.942495+07:00
    // POST:/payment/v2/h5/createLink:9cffc712a4c2f908b38d3e4e9a1dd7e0408755100d6447611859f146af6866c9:2024-04-06T13:48:04.942495+07:00

    // nBD0k8k6d5cD6zvxynxih8GxvQ0auUDihct+Rsm4ojNO8h4QgroFxP3YPrwskzvz3sfB7Kpu3XY38MMTmuZOByWVNaw2Jjf+3gIZNpIryRMvOcplkjL17AvszYxJqkt08pKNKQAhn23/QZJ1Qpsaj+8MU9E2R0mk34+MFKv1lhbXsn/27VV7CYGSEGQHnv9KXo9M99ojK8GfU6tp99Ay2kqXsjCfyYaOiIFTpWTTM5H2nuiKQ6J/RQbs+YUnHStTil9dVyRkmNnr6hk8Vo3ZLRUvAdoCZSca0faIDJ6QWbaIdlOcOErR9jZPc4AdZhBlgwbVb9nv3LDdqxQ+z/FrjQ==
    // nBD0k8k6d5cD6zvxynxih8GxvQ0auUDihct+Rsm4ojNO8h4QgroFxP3YPrwskzvz3sfB7Kpu3XY38MMTmuZOByWVNaw2Jjf+3gIZNpIryRMvOcplkjL17AvszYxJqkt08pKNKQAhn23/QZJ1Qpsaj+8MU9E2R0mk34+MFKv1lhbXsn/27VV7CYGSEGQHnv9KXo9M99ojK8GfU6tp99Ay2kqXsjCfyYaOiIFTpWTTM5H2nuiKQ6J/RQbs+YUnHStTil9dVyRkmNnr6hk8Vo3ZLRUvAdoCZSca0faIDJ6QWbaIdlOcOErR9jZPc4AdZhBlgwbVb9nv3LDdqxQ+z/FrjQ==


    
    public function notify(Request $request){
        $status = $request->status;
        $data = Deposit::where('trx',$request->merchantTradeNo)->first();
        if ($data) {
            if ($status=='02') {
                $this->userDataUpdate($data);
            }
            
        }
        return response()->json(['status'=>$status]);
    }
    public static function userDataUpdate(Deposit $deposit){
        $plan = Plan::first();

        $deposit->status = 1;
        $deposit->save();

        $user = User::find($deposit->user_id);

        $addPin = getAmount($deposit->amount) / $plan->price;
        
        $pin = new UserPin();
        $pin->user_id   = $user->id;
        $pin->pin       = $addPin;
        $pin->pin_by    = null;
        $pin->type      = "+";
        $pin->start_pin = $user->pin;
        $pin->end_pin   = $user->pin + $addPin;
        $pin->ket       = 'System Send '.$addPin . ' PIN to ' . $user->username . ' From Deposit Order PIN';
        $pin->save();

        $user->pin += $addPin;
        $user->save();

        $adminNotif = new AdminNotification();
        $adminNotif->user_id    = $user->id;
        $adminNotif->title      = 'Deposit Successful Via Paylabs Payment';
        $adminNotif->click_url  = route('admin.deposit.successful');
        $adminNotif->save();
    }
}
