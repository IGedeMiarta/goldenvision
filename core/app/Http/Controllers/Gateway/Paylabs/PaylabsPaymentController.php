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
        $date = new DateTime('now', new DateTimeZone('Asia/Jakarta')); // Adjust timezone as needed
        $timestamp = $date->format('Y-m-d\TH:i:s.uP'); // $timestamp = '2024-03-31T16:58:47.964+07:00';
        $mid = "010414";
        $trxid = $trx->id;
        $body = array(
            "merchantId"        => $mid,
            "merchantTradeNo"   => $trx->trx,
            "requestId"         => $trx->id,
            "amount"            => intval($trx->final_amo),
            "productName"       => "Goldenvision PIN Deposit",
            "payer"             => $user->username, //User fullname,
            "phoneNumber"       => $user->mobile, //user mobile
            "notifyUrl"         => url('api/v1/notify'), //URL yang akan ditembak saat terjadi pembayaran. Untuk parameter-parameternya cek di bagian Inquiry Order
            "redirectUrl"       => route('user.report.deposit'), //Baik saat sukses ataupun gagal, akan diarahkan ke URL tersebut
        );

        $privateKeyPath = __DIR__ . "/private.pem";
        $privateKey     = file_get_contents($privateKeyPath);
        // dd(json_encode($body));

        // minify json body
        $minifiedJson = minifyJsonBody(json_encode($body));
        
        //membuat string content
        $stringContent = createStringContent($httpMethod,$endpointURL,$minifiedJson,$timestamp);

        // membuat signature
        $signature = createSignature($stringContent,$privateKey);


       
        //hasil dari Calculation Tool  POST:/payment/v2/h5/createLink:46bf90614f316be3a60ac9013410a7006f477bb21105ff6c179593da8e954c31:2024-04-05T19:16:53.654493+07:00   
        //hasil dari aplikasi          POST:/payment/v2/h5/createLink:46bf90614f316be3a60ac9013410a7006f477bb21105ff6c179593da8e954c31:2024-04-05T19:16:53.654493+07:00

        $data_string = json_encode($body);

        $url = 'https://sit-pay.paylabs.co.id' . $endpointURL;

        // konfigurasi cURL
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array(
            'Content-Type: application/json;charset=utf-8',
            'X-TIMESTAMP:' . $timestamp,
            'X-SIGNATURE:' . $signature ,
            'X-PARTNER-ID:' . $mid,
            'X-REQUEST-ID:' . $trxid
        ));

        // execute and add get response
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        // close connection
        curl_close($ch);

        $response = json_decode($result,true);
        // echo  'method: ' . $httpMethod .'<br>';
        // echo  'TimeStamp: ' . $timestamp.'<br>';
        // echo  'Parameter: ' . $minifiedJson .'<br>';
        // echo  'stringContent: ' . $stringContent.'<br>';
        // echo 'signature: ' . $signature .'<br>';
        if ($response['errCode'] == 0) {
            //update status
            $trx->status = 2;
            $trx->payment_url = $response['url'];
            $trx->save();
            return true;
        }else{
            // return false;
            addToLog('error payment gateway: ' . implode(", ", $response));
            $notify[] = ['error', 'Error Payment gateway: ' . implode(", ", $response) ];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

    }

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
