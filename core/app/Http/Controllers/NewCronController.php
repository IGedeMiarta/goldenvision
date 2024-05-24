<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\FiliError;
use App\Models\GeneralSetting;
use App\Models\PoolLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class NewCronController extends Controller
{
    public function bonusPasangan()
    {
        $this->cancelDeposit();
        $gnl = GeneralSetting::first();
        $gnl->last_cron = Carbon::now()->toDateTimeString();
        $gnl->save();
        $userx = UserExtra::where('paid_left', '>=', 1)
        ->where('paid_right', '>=', 1)->get();

        $cron = array();
        foreach ($userx as $uex) {
            $user = $uex->user_id;
            $weak = $uex->paid_left < $uex->paid_right ? $uex->paid_left : $uex->paid_right;
            $weaktext = $uex->paid_left < $uex->paid_right ? 'kiri' : 'kanan';
            $weaks = $uex->left < $uex->right ? $uex->left : $uex->right;

            $user_plan = User::where('users.id', $user)
                ->join('plans', 'plans.id', '=', 'users.plan_id')
                ->where('users.plan_id', '!=', 0)->first();

            $us = User::where('id', $uex->user_id)->first();

            if (Date('H') != "00") {
                continue;
            }

            if (!$user_plan) {
                continue;
            }
            if ($weaks >= 25) {
                $pairs = intval($weak);
                $pair = intval($weak);
            } else {
                $pairs = intval($weak);
                $pair = intval($weak);
            }

            if ($pair < 1) {
                continue;
            }

            if ($uex->level_binary != 0 && $pairs != $uex->level_binary) {
                if ($pair > $uex->level_binary) {
                    if ($pair - $uex->level_binary >= 25) {
                        $pair = 25;
                        $bonus = intval(($pair) * ($user_plan->tree_com * 2));
                    } else {
                        if ($pair >= 25) {
                            $pair = 25;
                            $bonus = intval(($pair - $uex->level_binary) * ($user_plan->tree_com * 2));
                        } else {
                            $bonus = intval(($pair - $uex->level_binary) * ($user_plan->tree_com * 2));
                        }
                    }
                } else {
                    if ($pair >= 25) {
                        $pair = 25;
                        $bonus = intval(($uex->level_binary - $pair) * ($user_plan->tree_com * 2));
                    } else {
                        $bonus = intval(($uex->level_binary - $pair) * ($user_plan->tree_com * 2));
                    }
                }
            } else {
                if ($pair >= 25) {
                    $pair = 25;
                    $bonus = intval($pair * ($user_plan->tree_com * 2));
                } else {
                    $bonus = intval($pair * ($user_plan->tree_com * 2));
                }
            }

            $pair2[] = $pair == $uex->level_binary;

            if ($pair >= 25) {
                $pair = 25;
            }

            if ($pair == $uex->level_binary) {
            } else {
                $payment = User::find($uex->user_id);
                $payment->b_balance += $bonus;
                $payment->total_binary_com += $bonus;

                $trx = new Transaction();
                $trx->user_id = $payment->id;
                $trx->amount = $bonus;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = $payment->b_balance;
                $trx->remark = 'binary_commission';
                $trx->trx = getTrx();

                if ($pair >= 25) {
                    if ($uex->last_flush_out) {
                        if (Carbon::parse($uex->last_flush_out)->format('Y-m-d') != Carbon::now()->toDateString()) {
                            if ($uex->level_binary == 0) {
                                if (Carbon::parse($uex->last_getcomm)->format('Y-m-d') != Carbon::now()->toDateString()) {
                                    $payment->save();
                                    $trx->details = '[1] Paid Flush Out ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair . ' Pairs.';
                                    $trx->save();

                                    if($weaktext == 'kiri'){
                                        $uex->paid_right = $uex->paid_right - $weak;
                                        $uex->paid_left = 0;
                                    }else{
                                        $uex->paid_left = $uex->paid_left - $weak;
                                        $uex->paid_right = 0;
                                    }
 
                                    $uex->level_binary = 0;
                                    $uex->limit += $pair;
                                    $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                    $uex->save();

                                    $gnl->last_paid = Carbon::now()->toDateTimeString();
                                    $gnl->save();

                                    $cron[] = $user . '/' . $pair . '/' . Carbon::parse($uex->last_flush_out)->format('Y-m-d') . '/FlushOut3';
                                } else {
                                    $payment->save();
                                    $trx->details = '[2] Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair - $uex->level_binary) . ' Pairs.';
                                    $trx->save();

                                    if($weaktext == 'kiri'){
                                        $uex->paid_right = $uex->paid_right - $weak;
                                        $uex->paid_left = 0;
                                    }else{
                                        $uex->paid_left = $uex->paid_left - $weak;
                                        $uex->paid_right = 0;
                                    }
                                    $uex->level_binary = 0;
                                    $uex->limit += ($pair - $uex->level_binary);
                                    $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                    $uex->save();

                                    $gnl->last_paid = Carbon::now()->toDateTimeString();
                                    $gnl->save();

                                    $cron[] = $user . '/' . $pair . '/' . Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                                }
                            } else {
                                $payment->save();
                                $trx->details = '[3] Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair - $uex->level_binary) . ' Pairs.';
                                $trx->save();

                                if($weaktext == 'kiri'){
                                    $uex->paid_right = $uex->paid_right - $weak;
                                    $uex->paid_left = 0;
                                }else{
                                    $uex->paid_left = $uex->paid_left - $weak;
                                    $uex->paid_right = 0;
                                }
                                $uex->level_binary = 0;
                                $uex->limit += ($pair - $uex->level_binary);
                                $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                $uex->save();

                                $gnl->last_paid = Carbon::now()->toDateTimeString();
                                $gnl->save();

                                $cron[] = $user . '/' . $pair . '/' . Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                            }
                        } else {
                            $payment->save();

                            if ($uex->level_binary == 0) {
                                $trx->details = '[4] Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair . ' Pairs.';
                            } else {
                                $trx->details = '[5] Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair - $uex->level_binary) . ' Pairs.';
                            }
                            $trx->save();

                            if($weaktext == 'kiri'){
                                $uex->paid_right = $uex->paid_right - $weak;
                                $uex->paid_left = 0;
                            }else{
                                $uex->paid_left = $uex->paid_left - $weak;
                                $uex->paid_right = 0;
                            }

                            $uex->level_binary = 0;
                            $uex->limit += ($pair - $uex->level_binary);
                            $uex->last_getcomm = Carbon::now()->toDateTimeString();
                            $uex->save();

                            $gnl->last_paid = Carbon::now()->toDateTimeString();
                            $gnl->save();

                            $cron[] = $user . '/' . $pair . '/' . Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                        }
                    } else {
                        if ($uex->level_binary == 0) {
                            if (!empty($uex->last_getcomm)) {
                                if (Carbon::parse($uex->last_getcomm)->format('Y-m-d') != Carbon::now()->toDateString()) {
                                    $payment->save();
                                    $trx->details = '[6] Paid Flush Out ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair . ' Pairs.';
                                    $trx->save();

                                    if($weaktext == 'kiri'){
                                        $uex->paid_right = $uex->paid_right - $weak;
                                        $uex->paid_left = 0;
                                    }else{
                                        $uex->paid_left = $uex->paid_left - $weak;
                                        $uex->paid_right = 0;
                                    }
                                    $uex->level_binary = 0;
                                    $uex->limit += $pair;
                                    $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                    $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                    $uex->save();

                                    $gnl->last_paid = Carbon::now()->toDateTimeString();
                                    $gnl->save();

                                    $cron[] = $user . '/' . $pair . '/' . Carbon::parse($uex->last_flush_out)->format('Y-m-d') . '/FlushOut2';
                                } else {
                                    $payment->save();
                                    $trx->details = '[6.5] Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair - $uex->level_binary) . ' Pairs.';
                                    $trx->save();

                                    if($weaktext == 'kiri'){
                                        $uex->paid_right = $uex->paid_right - $weak;
                                        $uex->paid_left = 0;
                                    }else{
                                        $uex->paid_left = $uex->paid_left - $weak;
                                        $uex->paid_right = 0;
                                    }
                                    $uex->level_binary = 0;
                                    $uex->limit += ($pair - $uex->level_binary);
                                    $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                    $uex->save();

                                    $gnl->last_paid = Carbon::now()->toDateTimeString();
                                    $gnl->save();

                                    $cron[] = $user . '/' . $pair . '/' . Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                                }
                            }else{
                                $payment->save();
                                $trx->details = '[7] Paid Flush Out ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair . ' Pairs.';
                                $trx->save();

                                if($weaktext == 'kiri'){
                                    $uex->paid_right = $uex->paid_right - $weak;
                                    $uex->paid_left = 0;
                                }else{
                                    $uex->paid_left = $uex->paid_left - $weak;
                                    $uex->paid_right = 0;
                                }
                                $uex->level_binary = 0;
                                $uex->limit += $pair;
                                $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                $uex->save();

                                $gnl->last_paid = Carbon::now()->toDateTimeString();
                                $gnl->save();

                                $cron[] = $user . '/' . $pair . '/' . Carbon::parse($uex->last_flush_out)->format('Y-m-d') . '/FlushOut1';
                            }
                        } else {
                            $payment->save();
                            $trx->details = '[8] Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair - $uex->level_binary) . ' Pairs.';
                            $trx->save();

                            if($weaktext == 'kiri'){
                                $uex->paid_right = $uex->paid_right - $weak;
                                $uex->paid_left = 0;
                            }else{
                                $uex->paid_left = $uex->paid_left - $weak;
                                $uex->paid_right = 0;
                            }
                            $uex->level_binary = 0;
                            $uex->limit += ($pair - $uex->level_binary);
                            $uex->last_getcomm = Carbon::now()->toDateTimeString();
                            $uex->save();

                            $gnl->last_paid = Carbon::now()->toDateTimeString();
                            $gnl->save();

                            $cron[] = $user . '/' . $pair . '/' . Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                        }
                    }
                } else {
                    $payment->save();

                    if ($uex->level_binary != 0 && $pairs != $uex->level_binary) {
                        $trx->details = '[9] Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair - $uex->level_binary) . ' Pairs.';
                        $uex->limit += ($pair - $uex->level_binary);
                        $uex->last_getcomm = Carbon::now()->toDateTimeString();
                    } else {
                        $uex->limit += $pair;
                        $uex->last_getcomm = Carbon::now()->toDateTimeString();
                        $trx->details = '[10] Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair . ' Pairs.';
                    }
                    $trx->save();

                    if($weaktext == 'kiri'){
                        $uex->paid_right = $uex->paid_right - $weak;
                        $uex->paid_left = 0;
                    }else{
                        $uex->paid_left = $uex->paid_left - $weak;
                        $uex->paid_right = 0;
                    }
                    $uex->level_binary = 0;
                    $uex->save();

                    $gnl->last_paid = Carbon::now()->toDateTimeString();
                    $gnl->save();

                    $cron[] = $user . '/' . $pair;
                }
            }
        }
        // return $cron;
        // abort(404);
        // dd($dd);
        return response()->json([
            'http_code' => 200,
            'response' => 'ok'
        ]);

    }

    public function resetStartDay(){
        
    }

    public function poolSharing(){
        $gnl = GeneralSetting::first();
        $currentDate = Carbon::now();
        $users = User::where('rank', '>',3)->get();
        $omset = omsetLastMonth();

        $now = Carbon::now(); // Mendapatkan tanggal dan waktu saat ini
        $year = $now->year; // Mendapatkan tahun saat ini
        $month = $now->month; // Mendapatkan bulan saat ini

        $poolLogs = PoolLog::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->first();

        // if ($poolLogs) {
        //     return true;
        // }

        $cron = array();
        foreach ($users as $user) {
            $userx = UserExtra::where('user_id', $user->id)->first();
            $weak = $userx->left < $userx->right ? $userx->left : $userx->right;
            if ($weak < 100) {
                continue;
            }

            // if (Date('H') != "23" && $currentDate->day != 14) {
            //     continue;
            // }

            if ($weak >= 100 && $weak < 500) {
                $bonus = ($omset * 0.005) / $weak;
            }else if ($weak >= 500 && $weak < 1000) {
                $bonus = ($omset * 0.01) / $weak;
            }else if ($weak >= 1000 && $weak < 3000) {
                $bonus = ($omset * 0.015) / $weak;
            }else if ($weak >= 3000) {
                $bonus = ($omset * 0.02) / $weak;
            }

            $payment = User::find($user->id);
            $payment->b_balance += intval($bonus);
            $payment->save();

            $trx = new Transaction();
            $trx->user_id = $payment->id;
            $trx->amount = intval($bonus);
            $trx->charge = 0;
            $trx->trx_type = '+';
            $trx->post_balance = $payment->b_balance;
            $trx->remark = 'pool_commission';
            $trx->trx = getTrx();
            $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' Global Sharing Pool';
            $trx->save();                

            $cron[] = $user->id.'/' . $user->ranks->name.'/' . $weak .'/' . intval($bonus);
        }

        PoolLog($omset);

        // return $cron;
        // abort(404);
        return response()->json([
            'http_code' => 200,
            'response' => 'ok'
        ]);
    }

    
    public function cancelDeposit() {
        
        $deposits = Deposit::where('status', 0)->get();
        foreach ($deposits as $deposit) {
            $created_at_plus_24_hours = strtotime($deposit->created_at . ' +24 hours');
            $now = time();

            if ($created_at_plus_24_hours > $now) {
                $deposit->status = 3;
                $deposit->admin_feedback = 'Cancelled by system, past the transaction time';
                $deposit->save();
            }
        }
    }


    public function migrateUser()
    {
       
        $url = env('FILI_URL') .'api/migrate/gv';
        $user = User::where(['fili_id'=>0,'plan_id'=>1,'status'=>1])->first(); 

        // Check if user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        function isValidationFailed($response) {
            $errors = $response['errors'] ?? [];
            return isset($errors['email']) || isset($errors['username']);
        }
        
        // Function to check if the email validation failed
        function isEmailInvalid($response) {
            return isset($response['errors']['email']);
        }

        // Function to check if the username validation failed
        function isUsernameInvalid($response) {
            return isset($response['errors']['username']);
        }

        $email = $user->email;
        $emailBase = preg_replace('/\+.*@/', '@', $email);  // Remove any existing '+' part
        $counter = 1;

        $username = $user->username;

        while (true) {
            $data = [
                'username'  => $username,
                'firstname' => $user->firstname,
                'lastname'  => $user->lastname,
                'password'  => $user->password,
                'email'     => $email,
                'balance'   => $user->balance,
                'mobile'    => $user->mobile,
                'address'   => $user->address,
                'ref_id'    => $this->findUser($user->ref_id), 
                'pos_id'    => $this->findUser($user->pos_id),
                'position'  => $user->position,
                'left'      => $user->userExtra->left,
                'right'     => $user->userExtra->right,
                'paid_left' => $user->userExtra->paid_left,
                'paid_right'=> $user->userExtra->paid_right,
            ];

            // Send data to external API
            $response = Http::post($url, $data);

            // Check if the request was successful
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['user_id'])) {
                    $user->fili_id = $responseData['user_id'];
                    $user->save();  // Save the updated user
                }
                return response()->json([
                    'status'  => 'OK',
                    'message' => 'user_created',
                    'user_id' => $user->id,
                    'data'    => $responseData['data']
                ], 200);
            } else {
                // API call failed, handle error
                $errorResponse = $response->json();

                if (isValidationFailed($errorResponse)) {
                    // Modify the email and/or username based on validation errors
                    if (isEmailInvalid($errorResponse)) {
                        $email = preg_replace('/@/', "+$counter@", $emailBase, 1);
                    }
                    if (isUsernameInvalid($errorResponse)) {
                        $username .= '_gv';
                    }
                    $counter++;
                } else {
                    $fili = new FiliError();
                    $fili->username = $username;
                    $fili->errors = json_encode($errorResponse['errors']);  // Ensure errors are properly encoded
                    $fili->save();

                    return response()->json([
                        'status'  => 'Err',
                        'message' => 'Errors',
                        'username' => $username,
                        'user_id' => $user->id,
                        'response' => json_encode($errorResponse['errors'])
                    ], 422);
                }
            }
        }

    }

    public function findUser($id){
        $user = User::find($id);
        return $user? $user->fili_id:$id;
    }
}
