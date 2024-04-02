<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BvLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use App\Models\UserPin;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function adminPinDeliver(Request $request){
        $search = $request->search;
        $data['page_title'] = "PIN Delivery Log";
        $data['transactions'] = UserPin::where('pin_by', null)
            ->leftJoin('users AS pin_users', 'pin_users.id', '=', 'user_pin.pin_by')
            ->join('users AS user', 'user.id', '=', 'user_pin.user_id')
            ->select('user_pin.*', 'user.username AS user_username', 'pin_users.username AS pin_username')
            ->orderBy('user_pin.id', 'DESC')
            ->paginate(getPaginate());
        $data['pin'] = User::sum('pin');
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view('admin.pin.admin-pin', $data);
    }
    public function pinAll(Request $request){
        $search = $request->search;
        $data['pin'] = User::sum('pin');
        $data['page_title'] = "PIN Delivery Log";
        $data['transactions'] = UserPin::leftJoin('users AS pin_users', 'pin_users.id', '=', 'user_pin.pin_by')
            ->join('users AS user', 'user.id', '=', 'user_pin.user_id')
            ->select('user_pin.*', 'user.username AS user_username', 'pin_users.username AS pin_username')
            ->orderBy('user_pin.id', 'DESC')
            ->paginate(getPaginate());
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view('admin.pin.admin-pin', $data);
    }
    public function SystemPointDeliver(Request $request){
        $search = $request->search;
        $data['page_title'] = "Redemption Delivery Point Log";
        $data['transactions'] = UserPoint::where('user_points.type','+')-> where('user_points.point','>',0)
         ->join('users','users.id','=','user_points.user_id')
                            ->orderBy('user_points.id','DESC')
                            ->paginate(getPaginate());
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view('admin.point.log', $data);
    }
    public function pointAll(Request $request){
        $search = $request->search;
        $data['page_title'] = "Redemption Delivery Point Log";
        $data['transactions'] = UserPoint::where('user_points.point','>',0)
                            ->join('users','users.id','=','user_points.user_id')
                            ->orderBy('user_points.id','DESC')
                            // ->get();
                            ->paginate(getPaginate());
                            // dd($data);
        $data['search'] = $search;
        $data['empty_message'] = "No Data Found!";
        return view('admin.point.log', $data);
    }

    public function bvLog(Request $request)
    {

        if ($request->type) {
            if ($request->type == 'leftBV') {
                $data['page_title'] = "Left BV";
                $data['logs'] = BvLog::where('position', 1)->where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));
            } elseif ($request->type == 'rightBV') {
                $data['page_title'] = "Right BV";
                $data['logs'] = BvLog::where('position', 2)->where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));
            }elseif ($request->type == 'cutBV') {
                $data['page_title'] = "Cut BV";
                $data['logs'] = BvLog::where('trx_type', '-')->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));
            } else {
                $data['page_title'] = "All Paid BV";
                $data['logs'] = BvLog::where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));
            }
        }else{
            $data['page_title'] = "BV LOG";
            $data['logs'] = BvLog::orderBy('id', 'desc')->paginate(config('constants.table.default'));
        }

        $data['empty_message'] = 'No data found';
        return view('admin.reports.bvLog', $data);
    }

    public function singleBvLog(Request $request, $id)
    {

        $user = User::findOrFail($id);
        if ($request->type) {
            if ($request->type == 'leftBV') {
                $data['page_title'] = $user->username . " - Left BV";
                $data['logs'] = BvLog::where('user_id', $user->id)->where('position', 1)->where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));
            } elseif ($request->type == 'rightBV') {
                $data['page_title'] = $user->username . " - Right BV";
                $data['logs'] = BvLog::where('user_id', $user->id)->where('position', 2)->where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));
            } elseif ($request->type == 'cutBV') {
                $data['page_title'] = $user->username . " - All Cut BV";
                $data['logs'] = BvLog::where('user_id', $user->id)->where('trx_type', '-')->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));
            } else {
                $data['page_title'] = $user->username . " - All Paid BV";
                $data['logs'] = BvLog::where('user_id', $user->id)->where('trx_type', '+')->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));
            }
        }else{
            $data['page_title'] = $user->username . " - All  BV";
            $data['logs'] = BvLog::where('user_id', $user->id)->orderBy('id', 'desc')->with('user')->paginate(config('constants.table.default'));

        }

        $data['empty_message'] = 'No data found';
        return view('admin.reports.bvLog', $data);
    }



    public function refCom(Request $request)
    {
        if ($request->userID)
        {
            $user = User::findOrFail($request->userID);
            $page_title = $user->username . ' - Referral Commission Logs';
            $transactions = Transaction::where('user_id', $user->id)->where('remark', 'referral_commission')->with('user')->latest()->paginate(getPaginate());
        }else {
            $page_title = 'Referral Commission Logs';
            $transactions = Transaction::where('remark', 'referral_commission')->with('user')->latest()->paginate(getPaginate());
        }

        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }

    public function binary(Request $request)
    {
        if ($request->userID)
        {
            $user = User::findOrFail($request->userID);
            $page_title = $user->username . ' - Binary Commission Logs';
            $transactions = Transaction::where('user_id', $user->id)->where('remark', 'binary_commission')->with('user')->latest()->paginate(getPaginate());
        }else {
            $page_title = 'Binary Commission Logs';
            $transactions = Transaction::where('remark', 'binary_commission')->with('user')->latest()->paginate(getPaginate());
        }

        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }
    public function founder(Request $request)
    {
        if ($request->userID)
        {
            $user = User::findOrFail($request->userID);
            $page_title = $user->username . ' - Founder Commission Logs';
            $transactions = Transaction::where('user_id', $user->id)->where('remark', 'founder_com')->with('user')->latest()->paginate(getPaginate());
        }else {
            $page_title = 'Founder Commission Logs';
            $transactions = Transaction::where('remark', 'founder_com')->with('user')->latest()->paginate(getPaginate());
        }

        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }
    public function leadership(Request $request)
    {
        if ($request->userID)
        {
            $user = User::findOrFail($request->userID);
            $page_title = $user->username . ' - Leadership Commission Logs';
            $transactions = Transaction::where('user_id', $user->id)->where('remark', 'leadership_com')->with('user')->latest()->paginate(getPaginate());
        }else {
            $page_title = 'Leadership Commission Logs';
            $transactions = Transaction::where('remark', 'leadership_com')->with('user')->latest()->paginate(getPaginate());
        }

        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }
    public function invest(Request $request)
    {
        if ($request->userID)
        {
            $user = User::findOrFail($request->userID);
            $page_title = $user->username . ' - Invest Logs';
            $transactions = Transaction::where('user_id', $user->id)->where('remark', 'purchased_plan')->orwhere('user_id', $user->id)->where('remark', 'purchased_product')->with('user')->latest()->paginate(getPaginate());
        }else {
            $page_title = 'Invest Logs';
            $transactions = Transaction::where('remark', 'purchased_plan')->orwhere('remark', 'purchased_product')->with('user')->latest()->paginate(getPaginate());
        }

        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }
    public function allPayout(Request $request)
    {
        if ($request->userID)
        {
            $user = User::findOrFail($request->userID);
            $page_title = $user->username . ' - All Payout Logs';
            $transactions = Transaction::where('user_id', $user->id)->whereIn('remark', ['leadership_com','founder_com','binary_commission','referral_commission'])->with('user')->latest()->paginate(getPaginate());
        }else {
            $page_title = 'All Payout Logs';
            $transactions = Transaction::whereIn('remark', ['leadership_com','founder_com','binary_commission','referral_commission'])->with('user')->latest()->paginate(getPaginate());
        }
        $binnary = Transaction::where('remark','binary_commission')->sum('amount');
        $leader = Transaction::where('remark','leadership_com')->sum('amount');
        $founder = Transaction::where('remark','founder_com')->sum('amount');
        $ref = Transaction::where('remark','referral_commission')->sum('amount');
        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message','binnary','leader','founder','ref'));
    }
    public function transaction()
    {
        $page_title = 'Transaction Logs';
        $transactions = Transaction::with('user')->orderBy('id','desc')->paginate(getPaginate());
        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }

    public function transactionSearch(Request $request)
    {
        $request->validate(['search' => 'required']);
        $search = $request->search;
        $page_title = 'Transactions Search - ' . $search;
        $empty_message = 'No transactions.';

        $transactions = Transaction::with('user')->whereHas('user', function ($user) use ($search) {
            $user->where('username', 'like',"%$search%");
        })->orWhere('trx', $search)->orwhere('details', 'like',"%$search%")->orderBy('id','desc')->paginate(getPaginate());

        return view('admin.reports.transactions', compact('page_title', 'transactions','search', 'empty_message'));
    }

    public function loginHistory(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $page_title = 'User Login History Search - ' . $search;
            $empty_message = 'No search result found.';
            $login_logs = UserLogin::whereHas('user', function ($query) use ($search) {
                $query->where('username', $search);
            })->orderBy('id','desc')->paginate(getPaginate());
            return view('admin.reports.logins', compact('page_title', 'empty_message', 'search', 'login_logs'));
        }
        $page_title = 'User Login History';
        $empty_message = 'No users login found.';
        $login_logs = UserLogin::orderBy('id','desc')->paginate(getPaginate());
        return view('admin.reports.logins', compact('page_title', 'empty_message', 'login_logs'));
    }

    public function loginIpHistory($ip)
    {
        $page_title = 'Login By - ' . $ip;
        $login_logs = UserLogin::where('user_ip',$ip)->orderBy('id','desc')->paginate(getPaginate());
        $empty_message = 'No users login found.';
        return view('admin.reports.logins', compact('page_title', 'empty_message', 'login_logs'));

    }

    public function export(Request $request){
        // dd($request->all());
        $search = $request->search;
        $page = $request->page;
        if ($search) {
            return Excel::download(
                new ExportData(
                Transaction::query()
                ->join('users','users.id','=','transactions.user_id')
                ->whereHas('user', function ($user) use ($search) {
                    $user->where('username', 'like',"%$search%");
                })->orWhere('trx', $search)->orwhere('details', 'like',"%$search%")
                ->orderBy('transactions.id','DESC')
                ->select(db::raw("DATE_ADD(transactions.created_at, INTERVAL 0 HOUR)"), 'transactions.trx','users.username',db::raw("CONCAT(transactions.trx_type,' ',transactions.amount)"), 'transactions.charge','transactions.post_balance','transactions.details'
            )), 'report.xlsx')
            ;
        }

        if ($page) {
            # code...
            if ($page == 'Transaction Logs') {
                # code...
                return Excel::download(
                    new ExportData(
                    Transaction::query()
                    ->join('users','users.id','=','transactions.user_id')
                    ->orderBy('transactions.id','DESC')
                    ->select(db::raw("DATE_ADD(transactions.created_at, INTERVAL 0 HOUR)"), 'transactions.trx','users.username',db::raw("CONCAT(transactions.trx_type,' ',transactions.amount)"), 'transactions.charge','transactions.post_balance','transactions.details'
                )), 'report.xlsx')
                ;
            }

            if ($page == 'Invest Logs') {
                $remark = "purchased_plan";
            }
            if ($page == 'Binary Commission Logs') {
                $remark = 'binary_commission';
            }

            return Excel::download(
                new ExportData(
                Transaction::query()
                ->join('users','users.id','=','transactions.user_id')
                ->where('remark', $remark)
                ->orderBy('transactions.id','DESC')
                ->select(db::raw("DATE_ADD(transactions.created_at, INTERVAL 0 HOUR)"), 'transactions.trx','users.username',db::raw("CONCAT(transactions.trx_type,' ',transactions.amount)"), 'transactions.charge','transactions.post_balance','transactions.details'
            )), 'report.xlsx')
            ;

        }
    }

}
