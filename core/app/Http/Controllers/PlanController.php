<?php

namespace App\Http\Controllers;

use App\Models\BvLog;
use App\Models\GeneralSetting;
use App\Models\Gold;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    function planIndex()
    {
        $data['page_title'] = "Plans";
        $data['plans'] = Plan::whereStatus(1)->get();
        return view($this->activeTemplate . '.user.plan', $data);

    }

    function planStore(Request $request)
    {

        $this->validate($request, ['plan_id' => 'required|integer']);
        $plan = Plan::where('id', $request->plan_id)->where('status', 1)->firstOrFail();
        $gnl = GeneralSetting::first();

        $user = User::find(Auth::id());

        

        if ($user->balance < ($plan->price * $request->qty)) {
            $notify[] = ['error', 'Insufficient Balance'];
            return back()->withNotify($notify);
        }

            $oldPlan = $user->plan_id;
            
            $user->plan_id = $plan->id;
            $user->balance -= ($plan->price * $request->qty);
            $user->total_invest += ($plan->price * $request->qty);
            $user->bro_qty = $request->qty;
            $user->save();


            $gold = Gold::where('user_id',Auth::user()->id)->first();
            $gold1 = Gold::where('user_id',Auth::user()->id)->where('prod_id',1)->first();
            $gold2 = Gold::where('user_id',Auth::user()->id)->where('prod_id',2)->first();
            $gold3 = Gold::where('user_id',Auth::user()->id)->where('prod_id',3)->first();
            $gold4 = Gold::where('user_id',Auth::user()->id)->where('prod_id',4)->first();

            if($gold){
                if($gold1){
                    $gold1->qty += 50 * $request->qty;
                    $gold1->save();
                }else{
                    $newg = new Gold();
                    $newg->user_id = Auth::user()->id;
                    $newg->prod_id = 1;
                    $newg->qty = 50 * $request->qty;
                    $newg->save();
                }

                if($gold2){
                    $gold2->qty += 20 * $request->qty;
                    $gold2->save();
                }else{
                    $newg = new Gold();
                    $newg->user_id = Auth::user()->id;
                    $newg->prod_id = 2;
                    $newg->qty = 20 * $request->qty;
                    $newg->save();
                }

                if($gold3){
                    $gold3->qty += 5 * $request->qty;
                    $gold3->save();
                }else{
                    $newg = new Gold();
                    $newg->user_id = Auth::user()->id;
                    $newg->prod_id = 3;
                    $newg->qty = 5 * $request->qty;
                    $newg->save();
                }

                if($gold4){
                    $gold4->qty += 2 * $request->qty;
                    $gold4->save();
                }else{
                    $newg = new Gold();
                    $newg->user_id = Auth::user()->id;
                    $newg->prod_id = 4;
                    $newg->qty = 2 * $request->qty;
                    $newg->save();
                }


            }else{
                $newg = new Gold();
                $newg->user_id = Auth::user()->id;
                $newg->prod_id = 1;
                $newg->qty = 50 * $request->qty;
                $newg->save();

                $newg = new Gold();
                $newg->user_id = Auth::user()->id;
                $newg->prod_id = 2;
                $newg->qty = 20 * $request->qty;
                $newg->save();

                $newg = new Gold();
                $newg->user_id = Auth::user()->id;
                $newg->prod_id = 3;
                $newg->qty = 5 * $request->qty;
                $newg->save();

                $newg = new Gold();
                $newg->user_id = Auth::user()->id;
                $newg->prod_id = 4;
                $newg->qty = 2 * $request->qty;
                $newg->save();
            }

            $trx = $user->transactions()->create([
                'amount' => $plan->price * $request->qty,
                'trx_type' => '-',
                'details' => 'Purchased ' . $plan->name . ' For '.$request->qty.' BRO and Get '.$request->qty * 77 .' Pieces Gold',
                'remark' => 'purchased_plan',
                'trx' => getTrx(),
                'post_balance' => getAmount($user->balance),
            ]);

            // dd($user);

            sendEmail2($user->id, 'plan_purchased', [
                'plan' => $plan->name. ' For '.$request->qty.' BRO and Get '.$request->qty * 77 .' Pieces Gold',
                'amount' => getAmount($plan->price * $request->qty),
                'currency' => $gnl->cur_text,
                'trx' => $trx->trx,
                'post_balance' => getAmount($user->balance),
            ]);
            if ($oldPlan == 0) {
                updatePaidCount($user->id);
            }
            // $details = Auth::user()->username . ' Subscribed to ' . $plan->name . ' plan.';

            // updateBV($user->id, $plan->bv, $details);

            // if ($plan->tree_com > 0) {
            //     treeComission($user->id, $plan->tree_com, $details);
            // }

            // referralCommission($user->id, $details);

            $notify[] = ['success', 'Purchased ' . $plan->name . ' Successfully'];
            return redirect()->route('user.home')->withNotify($notify);

    }


    public function binaryCom()
    {
        $data['page_title'] = "Binary Commission";
        $data['logs'] = Transaction::where('user_id', auth()->id())->where('remark', 'binary_commission')->orderBy('id', 'DESC')->paginate(config('constants.table.default'));
        $data['empty_message'] = 'No data found';
        return view($this->activeTemplate . '.user.transactions', $data);
    }

    public function binarySummery()
    {
        $data['page_title'] = "Binary Summery";
        $data['logs'] = UserExtra::where('user_id', auth()->id())->firstOrFail();
        return view($this->activeTemplate . '.user.binarySummery', $data);
    }

    public function bvlog(Request $request)
    {

        if ($request->type) {
            if ($request->type == 'leftBV') {
                $data['page_title'] = "Left BV";
                $data['logs'] = BvLog::where('user_id', auth()->id())->where('position', 1)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(config('constants.table.default'));
            } elseif ($request->type == 'rightBV') {
                $data['page_title'] = "Right BV";
                $data['logs'] = BvLog::where('user_id', auth()->id())->where('position', 2)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(config('constants.table.default'));
            } elseif ($request->type == 'cutBV') {
                $data['page_title'] = "Cut BV";
                $data['logs'] = BvLog::where('user_id', auth()->id())->where('trx_type', '-')->orderBy('id', 'desc')->paginate(config('constants.table.default'));
            } else {
                $data['page_title'] = "All Paid BV";
                $data['logs'] = BvLog::where('user_id', auth()->id())->where('trx_type', '+')->orderBy('id', 'desc')->paginate(config('constants.table.default'));
            }
        } else {
            $data['page_title'] = "BV LOG";
            $data['logs'] = BvLog::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(config('constants.table.default'));
        }

        $data['empty_message'] = 'No data found';
        return view($this->activeTemplate . '.user.bvLog', $data);
    }

    public function myRefLog()
    {
        $data['page_title'] = "My Referral";
        $data['empty_message'] = 'No data found';
        $data['logs'] = User::where('ref_id', auth()->id())->latest()->paginate(config('constants.table.default'));
        return view($this->activeTemplate . '.user.myRef', $data);
    }

    public function myTree()
    {
        $data['tree'] = showTreePage(Auth::id());
        $data['page_title'] = "My Tree";
        return view($this->activeTemplate . 'user.myTree', $data);
    }


    public function otherTree(Request $request, $username = null)
    {
        if ($request->username) {
            $user = User::where('username', $request->username)->first();
        } else {
            $user = User::where('username', $username)->first();
        }
        if ($user && treeAuth($user->id, auth()->id())) {
            $data['tree'] = showTreePage($user->id);
            $data['page_title'] = "Tree of " . $user->fullname;
            return view($this->activeTemplate . 'user.myTree', $data);
        }

        $notify[] = ['error', 'Tree Not Found or You do not have Permission to view that!!'];
        return redirect()->route('user.my.tree')->withNotify($notify);

    }

}
