<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NewCronController extends Controller
{
    public function bonusPasangan()
    {

        $gnl = GeneralSetting::first();
        $gnl->last_cron = Carbon::now()->toDateTimeString();
        $gnl->save();
        $userx = UserExtra::where('paid_left', '>=', 1)
        ->where('paid_right', '>=', 1)->get();

        $cron = array();
        foreach ($userx as $uex) {
            $user = $uex->user_id;
            $weak = $uex->paid_left < $uex->paid_right ? $uex->paid_left : $uex->paid_right;
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
                $payment->balance += $bonus;
                $payment->total_binary_com += $bonus;

                $trx = new Transaction();
                $trx->user_id = $payment->id;
                $trx->amount = $bonus;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = $payment->balance;
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

                                    $uex->paid_left -= $weak;
                                    $uex->paid_right -= $weak;
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

                                    $uex->paid_left -= 25;
                                    $uex->paid_right -= 25;
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

                                $uex->paid_left -= 25;
                                $uex->paid_right -= 25;
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

                            $uex->paid_left -= 25;
                            $uex->paid_right -= 25;
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

                                    $uex->paid_left -= $weak;
                                    $uex->paid_right -= $weak;
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

                                    $uex->paid_left -= 25;
                                    $uex->paid_right -= 25;
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

                                $uex->paid_left -= $weak;
                                $uex->paid_right -= $weak;
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

                            $uex->paid_left -= 25;
                            $uex->paid_right -= 25;
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

                    $uex->level_binary = $pair;
                    $uex->save();

                    $gnl->last_paid = Carbon::now()->toDateTimeString();
                    $gnl->save();

                    $cron[] = $user . '/' . $pair;
                }
            }
        }
        return $cron;
        // dd($dd);

    }

    public function checkSponsor(){
        $sponsor = User::where('comp',0)->get();
        foreach ($sponsor as $key => $value) {
          
            $update = checkQuali($value->id);
        }
        return 'success';
    //    $id = [54,63,70,73,76,627,629,631,99,527];
    //    $uex = UserExtra::whereIn('user_id',$id)->get();
    //    foreach ($uex as $key => $value) {
    //         $value->is_gold = 0;
    //         $value->save();
    //    }      
    //    return true;
    //    $isGoldUser = User::join 
       $referralCounts = User::groupBy('ref_id')
            ->selectRaw('ref_id, count(*) as count')
            ->where('ref_id','!=',0)
            ->get();
        $updated = [];   
        foreach ($referralCounts as $ex) {
            $ex = UserExtra::where('is_gold',0)->where('user_id',$ex->ref_id)->first();
            if($ex){
                $ex->update(['is_gold'=>1]);
                $updated[] = 'update qualified user: '. $ex->id;
            }
            
        }
        if($updated !== null){
            addToLog(json_encode($updated));
        }
        return $updated;
        
    }
}
