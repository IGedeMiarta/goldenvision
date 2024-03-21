<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportData;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{

    public function pending()
    {
        $page_title = 'Pending Deposits';
        $empty_message = 'No pending deposits.';
        $type = 'pending';
        $deposits = Deposit::where('status', 2)->with(['user'])->latest()->paginate(getPaginate());
        return view('admin.deposit.log', compact('page_title', 'empty_message', 'deposits','type'));
    }


    public function approved()
    {
        $page_title = 'Approved Deposits';
        $empty_message = 'No approved deposits.';
        $deposits = Deposit::where('status', 1)->with(['user'])->latest()->paginate(getPaginate());
        $type = 'approved';
        return view('admin.deposit.log', compact('page_title', 'empty_message', 'deposits','type'));
    }

    public function successful()
    {
        $page_title = 'Successful Deposits';
        $empty_message = 'No successful deposits.';
        $deposits = Deposit::where('status', 1)->with(['user'])->latest()->paginate(getPaginate());
        $type = 'successful';
        return view('admin.deposit.log', compact('page_title', 'empty_message', 'deposits','type'));
    }

    public function rejected()
    {
        $page_title = 'Rejected Deposits';
        $empty_message = 'No rejected deposits.';
        $type = 'rejected';
        $deposits = Deposit::where('status', 3)->with(['user'])->latest()->paginate(getPaginate());
        return view('admin.deposit.log', compact('page_title', 'empty_message', 'deposits','type'));
    }

    public function deposit()
    {
        $page_title = 'Deposit History';
        $empty_message = 'No deposit history available.';
        $deposits = Deposit::with(['user'])->where('status','!=',0)->latest()->paginate(getPaginate());
        return view('admin.deposit.log', compact('page_title', 'empty_message', 'deposits'));
    }

    public function depViaMethod($method,$type = null){
        $method = Gateway::where('alias',$method)->firstOrFail();

        if ($type == 'approved') {
            $page_title = 'Approved Payment Via '.$method->name;
            $deposits = Deposit::where('method_code',$method->code)->where('status', 1)->latest()->with(['user'])->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $page_title = 'Rejected Payment Via '.$method->name;
            $deposits = Deposit::where('method_code',$method->code)->where('status', 3)->latest()->with(['user'])->paginate(getPaginate());

        }elseif($type == 'successful'){
            $page_title = 'Successful Payment Via '.$method->name;
            $deposits = Deposit::where('status', 1)->where('method_code',$method->code)->latest()->with(['user'])->paginate(getPaginate());
        }elseif($type == 'pending'){
            $page_title = 'Pending Payment Via '.$method->name;
            $deposits = Deposit::where('method_code',$method->code)->where('status', 2)->latest()->with(['user'])->paginate(getPaginate());
        }else{
            $page_title = 'Payment Via '.$method->name;
            $deposits = Deposit::where('status','!=',0)->where('method_code',$method->code)->latest()->with(['user'])->paginate(getPaginate());
        }
        $methodAlias = $method->alias;
        $empty_message = 'Deposit Log';
        return view('admin.deposit.log', compact('page_title', 'empty_message', 'deposits','methodAlias'));
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $page_title = '';
        $empty_message = 'No search result was found.';

        $deposits = Deposit::with(['user'])->where('status','!=',0)->where(function ($q) use ($search) {
            $q->where('trx', 'like', "%$search%")->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', 'like', "%$search%");
            });
        });
        switch ($scope) {
            case 'pending':
                $page_title .= 'Pending Deposits Search';
                $deposits = $deposits->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved Deposits Search';
                $deposits = $deposits->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Deposits Search';
                $deposits = $deposits->where('status', 3);
                break;
            case 'list':
                $page_title .= 'Deposits History Search';
                break;
        }

        $deposits = $deposits->paginate(getPaginate());
        $page_title .= ' - ' . $search;

        return view('admin.deposit.log', compact('page_title', 'search', 'scope', 'empty_message', 'deposits'));
    }

    public function dateSearch(Request $request,$scope = null){
        $search = $request->date;
        if (!$search) {
            return back();
        }
        $date = explode('-',$search);

        if(!(@strtotime($date[0]) && @strtotime($date[1]))){
            $notify[]=['error','Please provide valid date'];
            return back()->withNotify($notify);
        }
        
        $start  = @$date[0];
        $end    = @$date[1];
        
        // dd(Carbon::parse($start)->subDays(0));

        // if ($start) {
        //     $deposits = Deposit::where('status','!=',0)->where('created_at','>',Carbon::parse($start)->subDays(1))->where('created_at','<=',Carbon::parse($start)->addDays(0));
        // }
        // if($end){
        //     $deposits = Deposit::where('status','!=',0)->where('created_at','>',Carbon::parse($start)->subDays(1))->where('created_at','<',Carbon::parse($end)->addDays(1));
        // }
        
        $deposits = Deposit::where('status','!=',0)->whereBetween('created_at', [Carbon::parse($start), Carbon::parse($end)->addDays(1)]);
        
        if ($request->method) {
            $method = Gateway::where('alias',$request->method)->firstOrFail();
            $deposits = $deposits->where('method_code',$method->code);
        }
        switch ($scope) {
            case 'pending':
                $deposits = $deposits->where('status', 2);
                break;
            case 'approved':
                $deposits = $deposits->where('status', 1);
                break;
            case 'rejected':
                $deposits = $deposits->where('status', 3);
                break;
        }
        $deposits = $deposits->with(['user'])->latest()->paginate(getPaginate());
        $page_title = ' Deposits Log';
        $empty_message = 'Deposit Not Found';
        $dateSearch = $search;
        return view('admin.deposit.log', compact('page_title', 'empty_message', 'deposits','dateSearch','scope'));
    }

    public function details($id)
    {
        $general = GeneralSetting::first();
        $deposit = Deposit::where('id', $id)->with(['user'])->firstOrFail();
        $page_title = $deposit->user->username.' requested ' . getAmount($deposit->amount/500000) . ' '.'PIN';
        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('page_title', 'deposit','details'));
    }


    public function approve(Request $request)
    {

        $request->validate(['id' => 'required|integer']);
        DB::beginTransaction();
        try {
            $deposit = Deposit::where('id',$request->id)->where('status',2)->firstOrFail();
            $deposit->status = 1;
            $deposit->save();
    
            $user = User::find($deposit->user_id);

            $addPin = getAmount($deposit->amount) / 500000;
            
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
            DB::commit();
            $notify[] = ['success', 'Deposit has been approved.'];
            return redirect()->route('admin.deposit.pending')->withNotify($notify);
        } catch (\Throwable $th) {
            DB::rollBack();
            $notify[] = ['error', 'Error: ' . $th->getMessage() ];
            return redirect()->route('admin.deposit.pending')->withNotify($notify);
        }

        // $transaction = new Transaction();
        // $transaction->user_id = $deposit->user_id;
        // $transaction->amount = getAmount($deposit->amount);
        // $transaction->post_balance = getAmount($user->balance);
        // $transaction->charge = getAmount($deposit->charge);
        // $transaction->trx_type = '+';
        // $transaction->details = 'Deposit Via Bank Trasfer';
        // $transaction->trx =  $deposit->trx;
        // $transaction->save();

        // $gnl = GeneralSetting::first();
        // notify($user, 'DEPOSIT_APPROVE', [
        //     'method_name' => $deposit->gateway_currency()->name,
        //     'method_currency' => $deposit->method_currency,
        //     'method_amount' => getAmount($deposit->final_amo),
        //     'amount' => getAmount($deposit->amount),
        //     'charge' => getAmount($deposit->charge),
        //     'currency' => $gnl->cur_text,
        //     'rate' => getAmount($deposit->rate),
        //     'trx' => $deposit->trx,
        //     'post_balance' => $user->balance
        // ]);
       
    }

    public function reject(Request $request)
    {

        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|max:250'
        ]);
        $deposit = Deposit::where('id',$request->id)->where('status',2)->firstOrFail();

        $deposit->admin_feedback = $request->message;
        $deposit->status = 3;
        $deposit->save();

        // $gnl = GeneralSetting::first();
        // notify($deposit->user, 'DEPOSIT_REJECT', [
        //     'method_name' => $deposit->gateway_currency()->name,
        //     'method_currency' => $deposit->method_currency,
        //     'method_amount' => getAmount($deposit->final_amo),
        //     'amount' => getAmount($deposit->amount),
        //     'charge' => getAmount($deposit->charge),
        //     'currency' => $gnl->cur_text,
        //     'rate' => getAmount($deposit->rate),
        //     'trx' => $deposit->trx,
        //     'rejection_message' => $request->message
        // ]);

        $notify[] = ['success', 'Deposit has been rejected.'];
        return  redirect()->route('admin.deposit.pending')->withNotify($notify);

    }

    public function export(Request $request){
        // dd($request->all());
        $scope = $request->page;
        $pt = $request->paget;
        $search = $request->search;
        $dates = $request->date;

        $date = explode('-',$dates);
        $start  = @$date[0];
        $end    = @$date[1];

        if($request->search){
            switch ($scope) {
                case 'pending':
                    $code = 2;
                    break;
                case 'approved':
                    $code = 1;
                    break;
                case 'rejected':
                    $code = 3;
                    break;
                case 'successful':
                    $code = 1;
                    break;
                case 'list':
                    return Excel::download(
                        new ExportData(
                        Deposit::query()
                        ->where('deposits.status','!=',0)
                        ->where('deposits.trx', 'like', "%$search%")
                        ->orWhere('users.username', 'like', "%$search%")
                        ->where('deposits.status','!=',0)
                        ->join('users','users.id','=','deposits.user_id')
                        ->orderBy('deposits.id','DESC')
                        ->select(db::raw("DATE_ADD(deposits.created_at, INTERVAL 0 HOUR)"),'deposits.trx','users.username','users.email','deposits.amount',db::raw("if(deposits.status = 2, 'pending',if(deposits.status = 1,'approved','Rejected'))"))), 'deposit.xlsx');
                    break;
            }
            return Excel::download(
                new ExportData(
                Deposit::query()
                ->where('deposits.status','!=',0)
                ->where('deposits.status','=',$code)
                ->where('deposits.trx', 'like', "%$search%")
                ->orWhere('users.username', 'like', "%$search%")
                ->where('deposits.status','!=',0)
                ->where('deposits.status','=',$code)
                ->join('users','users.id','=','deposits.user_id')
                ->orderBy('deposits.id','DESC')
                ->select(db::raw("DATE_ADD(deposits.created_at, INTERVAL 0 HOUR)"),'deposits.trx','users.username','users.email','deposits.amount',db::raw("if(deposits.status = 2, 'pending',if(deposits.status = 1,'approved','Rejected'))"))), 'deposit.xlsx');
        }

        if ($request->date) {
            # code...
             switch ($scope) {
                case 'pending':
                    $code = 2;
                    break;
                case 'approved':
                    $code = 1;
                    break;
                case 'successful':
                    $code = 1;
                    break;
                case 'rejected':
                    $code = 3;
                    break;
                case 'list':
                    return Excel::download(
                        new ExportData(
                        Deposit::query()
                        ->where('deposits.status','!=',0)
                        ->whereBetween('deposits.created_at', [Carbon::parse($start), Carbon::parse($end)->addDays(1)])
                        ->join('users','users.id','=','deposits.user_id')
                        ->orderBy('deposits.id','DESC')
                        ->select(db::raw("DATE_ADD(deposits.created_at, INTERVAL 0 HOUR)"),'deposits.trx','users.username','users.email','deposits.amount',db::raw("if(deposits.status = 2, 'pending',if(deposits.status = 1,'approved','Rejected'))"))), 'deposit.xlsx');
                    break;
            }
            return Excel::download(
                new ExportData(
                Deposit::query()
                ->where('deposits.status','!=',0)
                ->whereBetween('deposits.created_at', [Carbon::parse($start), Carbon::parse($end)->addDays(1)])
                ->where('deposits.status','=',$code)
                ->join('users','users.id','=','deposits.user_id')
                ->orderBy('deposits.id','DESC')
                ->select(db::raw("DATE_ADD(deposits.created_at, INTERVAL 0 HOUR)"),'deposits.trx','users.username','users.email','deposits.amount',db::raw("if(deposits.status = 2, 'pending',if(deposits.status = 1,'approved','Rejected'))"))), 'deposit.xlsx');
        }

        switch ($pt) {
            case 'Pending Deposits':
                $code = 2;
                break;
            case 'Approved Deposits':
                $code = 1;
                break;
            case 'Successful Deposits':
                $code = 1;
                break;
            case 'Rejected Deposits':
                $code = 3;
                break;
            case 'Deposit History':
                return Excel::download(
                    new ExportData(
                    Deposit::query()
                    ->where('deposits.status','!=',0)
                    ->join('users','users.id','=','deposits.user_id')
                    ->orderBy('deposits.id','DESC')
                    ->select(db::raw("DATE_ADD(deposits.created_at, INTERVAL 0 HOUR)"),'deposits.trx','users.username','users.email','deposits.amount',db::raw("if(deposits.status = 2, 'pending',if(deposits.status = 1,'approved','Rejected'))"))), 'deposit.xlsx');
            break;
        }

        return Excel::download(
            new ExportData(
            Deposit::query()
            ->where('deposits.status','=',$code)
            ->join('users','users.id','=','deposits.user_id')
            ->orderBy('deposits.id','DESC')
            ->select(db::raw("DATE_ADD(deposits.created_at, INTERVAL 0 HOUR)"),'deposits.trx','users.username','users.email','deposits.amount',db::raw("if(deposits.status = 2, 'pending',if(deposits.status = 1,'approved','Rejected'))")
            )), 'deposit.xlsx')
            ;

    }
}
