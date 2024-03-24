<?php

namespace App\Http\Controllers;

use App\Models\brodev;
use App\Models\GoldExchange;
use App\Models\Plan;
use App\Models\sendgold;
use App\Models\Transaction;
use App\Models\UserGold;
use App\Models\UserPin;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserReportController extends Controller
{

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function bvBonusLog(Request $request)
    {

        $search = $request->search;
        if ($search) {
            $data['page_title'] = "Matching Bonus search : " . $search;
            $data['transactions'] = auth()->user()->transactions()->where('remark', 'matching_bonus')->where('trx', 'like', "%$search%")->latest()->paginate(getPaginate());
        } else {
            $data['page_title'] = 'Matching Bonus';
            $data['transactions'] = auth()->user()->transactions()->where('remark', 'matching_bonus')->latest()->paginate(getPaginate());
        }
        $data['search'] = $search;

        $data['empty_message'] = 'No data found.';
        return view($this->activeTemplate . 'user.transactions', $data);

    }

    public function investLog(Request $request)
    {

        $search = $request->search;
        if ($search) {
            $data['page_title'] = "Invest search : " . $search;
            // $data['transactions'] = auth()->user()->transactions()->where('remark', 'purchased_plan')->where('trx', 'like', "%$search%")->orwhere('remark', 'purchased_product')->where('trx', 'like', "%$search%")->latest()->paginate(getPaginate());\
            $data['transactions'] = Transaction::where('user_id',Auth::user()->id)->where('remark', 'purchased_plan')->where('trx', 'like', "%$search%")->orwhere('remark', 'purchased_product')->where('user_id',Auth::user()->id)->where('trx', 'like', "%$search%")->latest()->paginate(getPaginate());
        } else {
            $data['page_title'] = 'Invest Log';
            // $data['transactions'] = auth()->user()->transactions()->where('remark', 'purchased_plan')->orwhere('remark', 'purchased_product')->latest()->paginate(getPaginate());
            $data['transactions'] = Transaction::where('user_id',Auth::user()->id)->where('remark', 'purchased_plan')->orwhere('remark', 'purchased_product')->where('user_id',Auth::user()->id)->latest()->paginate(getPaginate());
        }
        $data['search'] = $search;

        $data['empty_message'] = 'No data found.';
        return view($this->activeTemplate . 'user.transactions', $data);

    }

    public function binaryCom(Request $request)
    {

        $search = $request->search;
        if ($search) {
            $data['page_title'] = "Binary Commissions search : " . $search;
            $data['transactions'] = auth()->user()->transactions()->where('remark', 'binary_commission')->where('trx', 'like', "%$search%")->latest()->paginate(getPaginate());
        } else {
            $data['page_title'] = 'Binary Commissions';
            $data['transactions'] = auth()->user()->transactions()->where('remark', 'binary_commission')->latest()->paginate(getPaginate());
        }
        $data['search'] = $search;
        // $data['transactions'] = auth()->user()->transactions()->where('remark', 'referral_commission')->where('trx_type', "-")->latest()->paginate(getPaginate());;
        $data['empty_message'] = 'No data found.';
        return view($this->activeTemplate . 'user.transactions', $data);

    }

    public function leadersComm(Request $request){
        $search = $request->search;
        if ($search) {
            $data['page_title'] = "Leadership Commission search : " . $search;
            $data['transactions'] = auth()->user()->transactions()->where('remark', 'leadership_com')->where('trx', 'like', "%$search%")->latest()->paginate(getPaginate());
        } else {
            $data['page_title'] = 'Leadership Commission';
            $data['transactions'] = auth()->user()->transactions()->where('remark', 'leadership_com')->latest()->paginate(getPaginate());
        }
        $data['search'] = $search;
        $data['empty_message'] = 'No data found.';
        return view($this->activeTemplate . 'user.transactions', $data);
    }

    public function refCom(Request $request)
    {
        $search = $request->search;
        if ($search) {
            $data['page_title'] = "Referral Commissions search : " . $search;
            $data['transactions'] = auth()->user()->transactions()->where('remark', 'referral_commission')->where('trx', 'like', "%$search%")->latest()->paginate(getPaginate());
        } else {
            $data['page_title'] = 'Referral Commissions';
            $data['transactions'] = auth()->user()->transactions()->where('remark', 'referral_commission')->latest()->paginate(getPaginate());
        }
        $data['search'] = $search;
        // $data['transactions'] = auth()->user()->transactions()->where('remark', 'referral_commission')->where('trx_type', "-")->latest()->paginate(getPaginate());
        $data['empty_message'] = 'No data found.';
        return view($this->activeTemplate . 'user.transactions', $data);

    }

    public function transactions(Request $request)
    {

        $search = $request->search;
        if ($search) {
            $data['page_title'] = "Transaction search : " . $search;
            $data['transactions'] = auth()->user()->transactions()->where('trx', 'like', "%$search%")->where('trx_type', "-")->latest()->paginate(getPaginate());
        } else {
            $data['page_title'] = 'Transaction Log';
            $data['transactions'] = auth()->user()->transactions()->where('trx_type', "-")->latest()->paginate(getPaginate());
        }
        $data['search'] = $search;
        $data['empty_message'] = 'No transactions.';
        return view($this->activeTemplate . 'user.transactions', $data);

    }

    public function depositHistory(Request $request)
    {
        $search = $request->search;
        
        if ($search) {
            $data['page_title'] = "Deposit search : " . $search;
            $data['logs'] = auth()->user()->deposits()->where('trx', 'like', "%$search%")->with(['gateway'])->latest()->paginate(getPaginate());
        } else {
            $data['page_title'] = 'Deposit Log';
            $data['logs'] = auth()->user()->deposits()->latest()->paginate(getPaginate());
        }
        $data['search'] = $search;
        $data['plan'] = Plan::first();
        $data['empty_message'] = 'No history found.';
        return view($this->activeTemplate . 'user.deposit_history', $data);
    }

    public function dailyGoldLog(Request $request)
    {
        $user = Auth::user();
        $data['page_title'] = 'Claim Gold Log';
        $d_count = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
            ->where('user_id', $user->id)
            ->where('type', 'daily')
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();
        $count = $d_count->count();
            

        if($count >=100 || $user->wd_gold){
            $count = 100;
        }
        $log = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
            ->where('user_id', $user->id)
            ->where('type', 'daily')
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        $logGold = [];
        $totalGold = $count * 0.005;
        foreach ($log as $key => $value) {
            $logGold[] = [
                'day' => $count,
                'gold'=>$totalGold,
                'created_at' => $value->created_at
            ];
            $count -= 1;
            $totalGold -= 0.005;
        }
        
        $latesDaily = UserGold::where(['user_id'=>auth()->user()->id,'type'=>'daily'])->orderByDesc('created_at')->first();
        if($latesDaily){
            $logweek = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
            ->where('user_id', $user->id)
            ->where('type', 'weekly')
            ->where('created_at','>',$latesDaily->created_at)
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(5)
            ->get();

            $count_d = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
                ->where('user_id', $user->id)
                ->where('type', 'weekly')
                ->where('created_at','>',$latesDaily->created_at)
                ->groupBy('date')
                ->orderByDesc('date')
                ->get();
        }else{
            $logweek = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
            ->where('user_id', $user->id)
            ->where('type', 'weekly')
            
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(5)
            ->get();

            $count_d = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
                ->where('user_id', $user->id)
                ->where('type', 'weekly')
              
                ->groupBy('date')
                ->orderByDesc('date')
                ->get();
        }

        $count_w = $count_d->count();
        // $count = $total->count();
        if($count_w >=100){
            $count_w = 100;
        }
        $logGoldWeek = [];
        $totalGoldWeek = $count_w * 0.005;
        // dd($totalGoldWeek);
        foreach ($logweek as $key => $value) {
            $logGoldWeek[] = [
                'day' => $count_w,
                'gold'=>$totalGoldWeek,
                'created_at' => $value->created_at
            ];
            $count_w -= 1;
            $totalGoldWeek -= 0.005;
        }
        // dd($logGold);
        $data['logs_day'] = $logGold;
        $data['logs_week'] = $logGoldWeek;
        $data['empty_message'] = 'No history found.';

// dd($data);
        return view($this->activeTemplate . 'user.gold_history', $data);
    }

    public function withdrawLog(Request $request)
    {
        $search = $request->search;

        if ($search) {
            $data['page_title'] = "Withdraw search : " . $search;
            $data['withdraws'] = auth()->user()->withdrawals()->where('trx', 'like', "%$search%")->with('method')->latest()->paginate(getPaginate());
        } else {
            $data['page_title'] = "Withdraw Log";
            $data['withdraws'] = auth()->user()->withdrawals()->with('method')->latest()->paginate(getPaginate());
        }
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view($this->activeTemplate . 'user.withdraw.log', $data);
    }

    public function exchangeLog(Request $request){
        $search = $request->search;

        if ($search) {
            $data['page_title'] = "Gold Exchange search : " . $search;
            $data['exchange'] = GoldExchange::where('user_id',Auth::user()->id)->where('ex_id', 'like', "%$search%")
            ->join('products','products.id','=','gold_exchanges.prod_id')
            ->select('gold_exchanges.*','products.name')
            ->orderBy('gold_exchanges.created_at','DESC')
            ->paginate(getPaginate());
        } else {
            $data['page_title'] = "Gold Exchange Log";
            $data['exchange'] = GoldExchange::where('user_id',Auth::user()->id)->join('products','products.id','=','gold_exchanges.prod_id')->select('gold_exchanges.*','products.name')
            ->orderBy('gold_exchanges.created_at','DESC')
            ->paginate(getPaginate());
        }
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view($this->activeTemplate . 'user.exchangeGold', $data);
    }
    public function deliveryLog(Request $request){
        $search = $request->search;

        // if ($search) {
        //     $data['page_title'] = "Gold Delivery search : " . $search;
        //     $data['delivery'] = sendgold::where('user_id',Auth::user()->id)->where('trx', 'like', "%$search%")
        //     ->join('products','products.id','=','gold_exchanges.prod_id')
        //     ->select('gold_exchanges.*','products.name')
        //     ->orderBy('gold_exchanges.created_at','DESC')
        //     ->paginate(getPaginate());
        // } else {
            $data['page_title'] = "Gold Delivery Log";
            $data['delivery'] = sendgold::where('sendgolds.user_id',Auth::user()->id)
            ->join('golds','golds.id','=','sendgolds.gold_id')
            ->join('products','products.id','=','golds.prod_id')
            ->leftjoin('corders','corders.gold_id','=','golds.id')
            ->select('sendgolds.*','golds.is_custom','products.name as pname', 'products.weight as pweight','corders.name as cname')
            ->orderBy('sendgolds.created_at','DESC')
            ->paginate(getPaginate());

            // dd($data['delivery']);
        // }
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view($this->activeTemplate . 'user.deliveryGold', $data);
    }

    public function BroDeliveryLog(Request $request){
        $search = $request->search;

        // if ($search) {
        //     $data['page_title'] = "Gold Delivery search : " . $search;
        //     $data['delivery'] = sendgold::where('user_id',Auth::user()->id)->where('trx', 'like', "%$search%")
        //     ->join('products','products.id','=','gold_exchanges.prod_id')
        //     ->select('gold_exchanges.*','products.name')
        //     ->orderBy('gold_exchanges.created_at','DESC')
        //     ->paginate(getPaginate());
        // } else {
            $data['page_title'] = "MP Package Delivery Log";
            $data['delivery'] = brodev::where('user_id',Auth::user()->id)
            ->orderBy('created_at','DESC')
            ->paginate(getPaginate());

            // dd($data['delivery']);
        // }
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view($this->activeTemplate . 'user.BROdev', $data);
    }

    public function PinDeliveriyLog(Request $request){
        $search = $request->search;
        $data['page_title'] = "PIN Delivery Log";
        $data['transactions'] = UserPin::where('user_id',Auth::user()->id)
                            ->leftjoin('users','users.id','=','user_pin.pin_by')
                            ->select('user_pin.*','users.username')
                            ->orderBy('id','DESC')
                            ->paginate(getPaginate());
                            //->get(); dd($data);
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view($this->activeTemplate . 'user.pinLog', $data);
    }
    public function promoSeptLog(){
       
    }

}
