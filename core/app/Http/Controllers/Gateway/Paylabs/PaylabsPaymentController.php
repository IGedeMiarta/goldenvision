<?php

namespace App\Http\Controllers\Gateway\Paylabs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaylabsPaymentController extends Controller
{
    public function payment(Request $request){
        $httpMethod = "POST";
        $endpointURL = "/payment/v2/h5/createLink";
        // $timestamp = 
        $mid = "";
        $trxid = "";
        $body = array(
            "merchantId"        =>  $mid,
            "merchantTradeNo"   => $trxid,
            "requestId"         => "",
            "amount"            => "",
            "productName"       => "Deposit",
            "payer"             => "", //User fullname,
            "phoneNumber"       => "", //user monile
            "notifyUrl"         => "", //URL yang akan ditembak saat terjadi pembayaran. Untuk parameter-parameternya cek di bagian Inquiry Order
            "redirectUrl"       => "", //Baik saat sukses ataupun gagal, akan diarahkan ke URL tersebut
        );
        $privateKeyPath = __DIR__ . "/private.pem";
        $privateKey     = file_get_contents($privateKeyPath);

        // minify json body
        $minifiedJson = json_encode($body, JSON_UNESCAPED_UNICODE);

        //membuat string content
        $stringContent = 

        dd($minifiedJson);


    }
}
