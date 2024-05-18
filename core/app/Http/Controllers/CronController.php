<?php

namespace App\Http\Controllers;

use App\Models\DailyGold;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\MemberGrow;
use App\Models\rekening;
use App\Models\SilverCheck;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\WeeklyGold;
use Carbon\Carbon;
use GrahamCampbell\ResultType\Success;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Expr\New_;
use Weidner\Goutte\GoutteFacade;

class CronController extends Controller
{
    public function cronNew(){
        $gnl = GeneralSetting::first();
        $gnl->last_cron = Carbon::now()->toDateTimeString();
		$gnl->save();
        $userx = UserExtra::where('paid_left','>=',1)->where('paid_left','>=',1)->get();
        $cron = [];
        foreach($userx as $ux){
    
            $user_plan = user::where('users.id',$ux->user_id)
                    ->join('plans','plans.id','=','users.plan_id')
                    ->where('users.plan_id','!=',0)->first(); 
            if(Date('H') == "00"){
                $ux->limit = 0;
                $ux->last_flush_out = null;
                $ux->save();
            }
            
            
            $growLeft = $ux->paid_left; //25
            $growRight = $ux->paid_right; //24
            
            $pairID = $growLeft < $growRight ? $growLeft : $growRight; //24

            
            $payout = $this->setPayout($pairID,20);

            if($payout['pay'] != 0 && $ux->last_flush_out == null){

                $pairID = $payout['pay'];

                $ux->paid_left -= $pairID;
                $ux->paid_right -= $pairID;
                $ux->level_binary = 0;
                $ux->limit += $pairID;
                $ux->last_getcomm = Carbon::now()->toDateTimeString();
                $ux->save();

                $gnl->last_paid = Carbon::now()->toDateTimeString();
                $gnl->save();


                $bonus = intval(($pairID) * ($user_plan->tree_com * 2));
                $payment = User::find($ux->user_id);
                $payment->b_balance += $bonus;
                $payment->total_binary_com += $bonus;
                $payment->save();

                $trx = new Transaction();
                $trx->user_id = $payment->id;
                $trx->amount = $bonus;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = $payment->b_balance;
                $trx->remark = 'binary_commission';
                $trx->trx = getTrx();
                $trx->details = 'Paid Flush Out ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pairID * 2 . ' MP.';
                $trx->save();

                $cron[] = $payment.'/'.$pairID.'/'.Carbon::parse($ux->last_flush_out)->format('Y-m-d').'/bonus='.$bonus;

                continue;
            }

            if ($payout['flashout'] != 0 || $ux->last_flush_out != null) {

                $payment = User::find($ux->user_id);
                $ux->last_flush_out = Carbon::now()->toDateTimeString();
                //if flashout left right counting as paid;
                $ux->paid_left -= $payout['flashout'];
                $ux->paid_right -= $payout['flashout'];
                $ux->save();
                $cron[] = $payment.'/'.Carbon::parse($ux->last_flush_out)->format('Y-m-d').'/flashout';

                continue;
            }
            

          
           
            
        }
        return $cron;
    }
    function setPayout($pay, $maxPay) {
        if ($pay > $maxPay) {
            $flashout = $pay - $maxPay;
            $pay = $maxPay;
        } else {
            $flashout = 0;
        }
        
        return array('pay' => $pay, 'flashout' => $flashout);
    }


    public function cron()
    {
        
        $gnl = GeneralSetting::first();
        $gnl->last_cron = Carbon::now()->toDateTimeString();
		$gnl->save();
        $userx = UserExtra::where('paid_left','>=',1)
        ->where('paid_right','>=',1)->get();

        // dd($userx);
        $cron = array();
        foreach ($userx as $uex) {
                        $user = $uex->user_id;
                        $weak = $uex->paid_left < $uex->paid_right ? $uex->paid_left : $uex->paid_right;
                        
                        $weaks = $uex->left < $uex->right ? $uex->left : $uex->right;
                      
                        $user_plan = user::where('users.id',$user)
                        ->join('plans','plans.id','=','users.plan_id')
                        ->where('users.plan_id','!=',0)->first(); 
                        
                        $us = user::where('id',$uex->user_id)->first();

                        if(Date('H') == "00"){
                            $uex->limit = 0;
                            $uex->save();
                        }

                        if (!$user_plan) {
                            # code...
                            continue;
                        }
                        if ($weaks >= 20) {
                            # code...
                            // continue;
                            $pairs = intval($weak);
                            $pair = intval($weak);
                        }else{
                            $pairs = intval($weak);
                            $pair = intval($weak);

                        }

                        if ($pair < 1) {
                            # code...
                            continue; 
                        }


                        if ($us->is_leader == 1 && $us->is_manag == 0) {
                            # code...
                            if ($uex->limit > 100 && Carbon::parse($uex->last_getcomm)->format('Y-m-d') == Carbon::now()->toDateString()) {
                                # code...
                                continue; 
                            }
                        }elseif ($us->is_leader == 0 && $us->is_manag == 1) {
                            # code...
                            if ($uex->limit > 300 && Carbon::parse($uex->last_getcomm)->format('Y-m-d') == Carbon::now()->toDateString()) {
                                # code...
                                continue; 
                            }
                        }elseif ($us->is_leader == 0 && $us->is_manag == 0) {
                            if ($uex->limit > 30 && Carbon::parse($uex->last_getcomm)->format('Y-m-d') == Carbon::now()->toDateString()) {
                                # code...
                                continue; 
                            }
                        }else{
                            if ($uex->limit > 30 && Carbon::parse($uex->last_getcomm)->format('Y-m-d') == Carbon::now()->toDateString()) {
                                # code...
                                continue; 
                            }
                        }



                        
                        

                        if($uex->level_binary != 0 && $pairs != $uex->level_binary){
                            // $pair = intval($weak) - $uex->level_binary;
                            if ($pair > $uex->level_binary) {
                                if ($pair - $uex->level_binary >= 20) {
                                    # code...
                                    $pair = 20;
                                    $bonus = intval(($pair) * ($user_plan->tree_com * 2));
                                }else{

                                    if ($pair >= 20) {
                                        $pair = 20;
                                        $bonus = intval(($pair - $uex->level_binary) * ($user_plan->tree_com * 2));
                                    }else{
                                        $bonus = intval(($pair - $uex->level_binary) * ($user_plan->tree_com * 2));
                                    }
                                }

                            }else{
                                if ($pair >= 20) {
                                    $pair = 20;
                                    $bonus = intval(($uex->level_binary - $pair ) * ($user_plan->tree_com * 2));
                                }else{
                                    $bonus = intval(($uex->level_binary - $pair ) * ($user_plan->tree_com * 2));
                                }
                            }
                        }else{
                            if ($pair >= 20) {
                                # code...
                                $pair = 20;
                                $bonus = intval($pair * ($user_plan->tree_com * 2));
                            }else{
                                $bonus = intval($pair * ($user_plan->tree_com * 2));
                            }
                        }

                        $pair2[] = $pair == $uex->level_binary;

                        if ($pair >= 20) {
                            $pair = 20;
                        }

                        // if($uex->level_binary != 0 && $pairs != $uex->level_binary){
                        //     // $pair = intval($weak) - $uex->level_binary;
                        //     if ($pair > $uex->level_binary) {
                        //         $bonus = intval(($pair - $uex->level_binary) * ($user_plan->tree_com * 2));
                        //     }else{
                        //         $bonus = intval(($uex->level_binary - $pair ) * ($user_plan->tree_com * 2));
                        //     }
                        // }else{
                        //     $bonus = intval($pair * ($user_plan->tree_com * 2));
                        // }
                        // $bonus = intval($pair * ($user_plan->tree_com * 2));

                        // dd(is_numeric($uex->paid_left));


                        if ($pair == $uex->level_binary) {
                            // if ($uex->level_binary == 20) {
                            //     $payment = User::find($uex->user_id);
                            //     $payment->balance += $bonus;
                            //     $payment->save();
    
                            //     $trx = new Transaction();
                            //     $trx->user_id = $payment->id;
                            //     $trx->amount = $bonus;
                            //     $trx->charge = 0;
                            //     $trx->trx_type = '+';
                            //     $trx->post_balance = $payment->balance;
                            //     $trx->remark = 'binary_commission';
                            //     $trx->trx = getTrx();
                            //     $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair * 2 . ' MP.';
                            //     $trx->save();

                            //     $uex->paid_left = 0;
                            //     $uex->paid_right = 0;
                            //     $uex->save();
    
                            //     sendEmail2($user, 'matching_bonus', [
                            //             'amount' => $bonus,
                            //             'currency' => $gnl->cur_text,
                            //             'paid_bv' => $pair * 2,
                            //             'post_balance' => $payment->balance,
                            //             'trx' =>  $trx->trx,
                            //     ]);
                            // }else{

                            // }

                        }else{
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

                            if ($pair >= 20) {
                                
                                    if ($uex->last_flush_out) {
                                        if (Carbon::parse($uex->last_flush_out)->format('Y-m-d') != Carbon::now()->toDateString()) {
                                        # code...

                                            $paid_bv = $uex->paid_left + $uex->paid_right;
                                        // }else{
                                        // }
                                        
                                            // sendEmail2($user, 'matching_bonus', [
                                            //         'amount' => $bonus,
                                            //         'currency' => $gnl->cur_text,
                                            //         'paid_bv' => $paid_bv,
                                            //         'post_balance' => $payment->balance,
                                            //         'trx' =>  $trx->trx,
                                            // ]);
                                        
                                            if($uex->level_binary == 0){
                                                if (Carbon::parse($uex->updated_at)->format('Y-m-d') != Carbon::now()->toDateString()) {
                                                    $payment->save();
                                                    $trx->details = 'Paid Flush Out ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair * 2 . ' Pairs.';
                                                // }else{
                                                //     $trx->details = 'Paid Flush Out ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair-$uex->level_binary) * 6 . ' MP.';
                                                // }

                                            // }
                                                    $trx->save();
                                                    
                                                    $uex->paid_left -= $weak;
                                                    $uex->paid_right -= $weak;
                                                    // $uex->paid_left -= 20;
                                                    // $uex->paid_right -= 20;
                                                    $uex->level_binary = 0;
                                                    
                                                    // $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                                    $uex->limit += $pair;
                                                    $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                                    $uex->save();

                                                    $gnl->last_paid = Carbon::now()->toDateTimeString();
                                                    $gnl->save();

                                                    // Carbon::now()->toDateString()
                                                    $cron[] = $user.'/'.$pair.'/'.Carbon::parse($uex->last_flush_out)->format('Y-m-d').'/FlushOut2';
                                                }else{
                                                
                                                        $payment->save();
                                                        $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair-$uex->level_binary) * 2 . ' Pairs.';
    
                                                        $trx->save();
                                                    
                                                        $uex->paid_left -= 20;
                                                        $uex->paid_right -= 20;
                                                        $uex->level_binary = 0;
                                                        // $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                                        $uex->limit += ($pair-$uex->level_binary);
                                                        $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                                        $uex->save();
            
                                                        $gnl->last_paid = Carbon::now()->toDateTimeString();
                                                        $gnl->save();
            
                                                        // Carbon::now()->toDateString()
                                                        $cron[] = $user.'/'.$pair.'/'.Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                                                }
                                                
                                            }else{

                                                
                                                    $payment->save();
                                                    $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair-$uex->level_binary) * 2 . ' MP.';

                                                    $trx->save();
                                                
                                                    $uex->paid_left -= 20;
                                                    $uex->paid_right -= 20;
                                                    $uex->level_binary = 0;
                                                    // $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                                    $uex->limit += ($pair-$uex->level_binary);
                                                    $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                                    $uex->save();
        
                                                    $gnl->last_paid = Carbon::now()->toDateTimeString();
                                                    $gnl->save();
        
                                                    // Carbon::now()->toDateString()
                                                    $cron[] = $user.'/'.$pair.'/'.Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                                                
                                            }
                                        }else{

                                        
                                        # code...

                                                $paid_bv = $uex->paid_left + $uex->paid_right;
                                                // }else{
                                                // }
                                                
                                                    // sendEmail2($user, 'matching_bonus', [
                                                    //         'amount' => $bonus,
                                                    //         'currency' => $gnl->cur_text,
                                                    //         'paid_bv' => $paid_bv,
                                                    //         'post_balance' => $payment->balance,
                                                    //         'trx' =>  $trx->trx,
                                                    // ]);
                                                
                                                // if ($pair >= 20) {
                                                $payment->save();

                                                if($uex->level_binary == 0){
                                                    $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair * 2 . ' MP.';
                                                }else{
                                                    $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair-$uex->level_binary) * 2 . ' MP.';
                                                }
                                                $trx->save();
                                                
                                                $uex->paid_left -= 20;
                                                $uex->paid_right -= 20;
                                                $uex->level_binary = 0;
                                                $uex->limit += ($pair-$uex->level_binary);
                                                $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                                // $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                                $uex->save();

                                                $gnl->last_paid = Carbon::now()->toDateTimeString();
                                                $gnl->save();

                                                // Carbon::now()->toDateString()
                                                $cron[] = $user.'/'.$pair.'/'.Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                                            
                                        }
                                    }else{

                                        
                                        # code...

                                            $paid_bv = $uex->paid_left + $uex->paid_right;
                                            // }else{
                                            // }
                                            
                                                // sendEmail2($user, 'matching_bonus', [
                                                //         'amount' => $bonus,
                                                //         'currency' => $gnl->cur_text,
                                                //         'paid_bv' => $paid_bv,
                                                //         'post_balance' => $payment->balance,
                                                //         'trx' =>  $trx->trx,
                                                // ]);
                                            
                                            // if ($pair >= 20) {
                                            

                                                if($uex->level_binary == 0){
                                                    if (Carbon::parse($uex->updated_at)->format('Y-m-d') != Carbon::now()->toDateString()) {
                                                        $payment->save();
                                                        // $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair * 6 . ' MP.';
                                                        $trx->details = 'Paid Flush Out ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair * 2 . ' MP.';
                                                    // }else{
                                                    //     $trx->details = 'Paid Flush Out ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair-$uex->level_binary) * 6 . ' MP.';
                                                    // }

                                                // }
                                                        $trx->save();
                                                        
                                                        // $uex->paid_left -= 20;
                                                        // $uex->paid_right -= 20;
                                                        $uex->paid_left -= $weak;
                                                        $uex->paid_right -= $weak;
                                                        $uex->level_binary = 0;
                                                        $uex->limit += $pair;
                                                        $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                                        $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                                        $uex->save();

                                                        $gnl->last_paid = Carbon::now()->toDateTimeString();
                                                        $gnl->save();

                                                        // Carbon::now()->toDateString()
                                                        $cron[] = $user.'/'.$pair.'/'.Carbon::parse($uex->last_flush_out)->format('Y-m-d').'/FlushOut1';
                                                    }else{
                                                            $payment->save();
                                                            $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair-$uex->level_binary) * 2 . ' MP.';
    
                                                            $trx->save();
                                                        
                                                            $uex->paid_left -= 20;
                                                            $uex->paid_right -= 20;
                                                            $uex->level_binary = 0;
                                                            $uex->limit += ($pair-$uex->level_binary);
                                                            // $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                                            $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                                            $uex->save();
                
                                                            $gnl->last_paid = Carbon::now()->toDateTimeString();
                                                            $gnl->save();
                
                                                            // Carbon::now()->toDateString()
                                                            $cron[] = $user.'/'.$pair.'/'.Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                                                    }
                                                    
                                                }else{
                                                        $payment->save();
                                                        $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair-$uex->level_binary) * 2 . ' MP.';

                                                        $trx->save();
                                                    
                                                        $uex->paid_left -= 20;
                                                        $uex->paid_right -= 20;
                                                        $uex->level_binary = 0;
                                                        $uex->limit += ($pair-$uex->level_binary);
                                                        $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                                        // $uex->last_flush_out = Carbon::now()->toDateTimeString();
                                                        $uex->save();
            
                                                        $gnl->last_paid = Carbon::now()->toDateTimeString();
                                                        $gnl->save();
            
                                                        // Carbon::now()->toDateString()
                                                        $cron[] = $user.'/'.$pair.'/'.Carbon::parse($uex->last_flush_out)->format('Y-m-d');
                                                }
                                                
                                    }
                                



                            }else{
                                    # code...
                                
                                $paid_bv = $pair * 2;
                                // sendEmail2($user, 'matching_bonus', [
                                //     'amount' => $bonus,
                                //     'currency' => $gnl->cur_text,
                                //     'paid_bv' => $paid_bv,
                                //     'post_balance' => $payment->balance,
                                //     'trx' =>  $trx->trx,
                                // ]);
                                $payment->save();

                                    if($uex->level_binary != 0 && $pairs != $uex->level_binary){
                                        $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . ($pair-$uex->level_binary) * 2 . ' MP.';
                                        $uex->limit += ($pair-$uex->level_binary);
                                        $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                    }else{
                                        $uex->limit += $pair;
                                        $uex->last_getcomm = Carbon::now()->toDateTimeString();
                                        $trx->details = 'Paid ' . $bonus . ' ' . $gnl->cur_text . ' For ' . $pair * 2 . ' MP.';

                                    }
                                $trx->save();

                                $uex->level_binary = $pair;
                                $uex->save();

                                $gnl->last_paid = Carbon::now()->toDateTimeString();
                                $gnl->save();

                                $cron[] = $user.'/'.$pair;
                            }

                            
                        }
        }
        return $cron;
        // dd($dd);

    }

    public function resetCountingLF() {
        UserExtra::query()->update([
            'p_left' => 0,
            'p_right' => 0
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

    public function convertBBalanceToBalance() {
    $allBBalance = User::where('b_balance', '>', 0)->get();
    $conversionDetails = [];

    foreach ($allBBalance as $val) {
        DB::beginTransaction();
        try {
            $user = User::find($val->id);

            if ($user === null) {
                throw new \Exception('User not found');
            }

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $val->b_balance;
            $transaction->post_balance = $user->balance + $val->b_balance; // Correct post_balance calculation
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Convert B-Wallet To Cash Wallet';
            $transaction->remark = 'convert_balance';
            $transaction->trx = getTrx();
            $transaction->save();

            $user->balance += $val->b_balance;
            $user->b_balance -= $val->b_balance;
            $user->save();

            DB::commit();

            // Collect user details
            $conversionDetails[] = [
                'user_id' => $user->id,
                'username' => $user->username,
                'status' => 'success'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            // Ensure user exists before adding to failed conversionDetails
            $conversionDetails[] = [
                'user_id' => $val->id, // Use $val->id since $user might be null
                'username' => $user ? $user->username : 'unknown',
                'status' => 'fails'
            ];
        }
    }

    return $conversionDetails;
}


}

