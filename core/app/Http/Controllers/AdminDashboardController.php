<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPin;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(){
        $data['page_title'] = 'Dashboard';

        $data['pin'] = UserPin::where('pin_by',null)->sum('pin');
        $data['member_pin'] = User::where('pin','>',0)->sum('pin');
        $data['used_pin'] = UserPin::where('user_id','=','pin_by')->orWhere('pin_by',0)->sum('pin');
        $data['free_user'] = User::where('plan_id',0)->where('comp','!=',1)->count();
        $data['total_active_user'] = User::where('status',1)->count();
        $data['free_user_today'] = User::whereDate('created_at', now()->format('Y-m-d'))->where('plan_id',0)->count();
        $data['active_user_today'] = User::whereDate('created_at', now()->format('Y-m-d'))->count();
        $data['user_week'] = User::whereBetween('created_at', [
            now()->startOfWeek()->format('Y-m-d H:i:s'),
            now()->endOfWeek()->format('Y-m-d H:i:s')
        ])->count();

        $data['user_month'] = User::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $data['total_payout'] = Transaction::where('trx_type','+')->sum('amount');
        $data['payout_this_month'] = Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)->where('trx_type','+')
            ->sum('amount');
        $data['payout_this_week'] = Transaction::whereYear('created_at', now()->year)
            ->whereBetween('created_at', [
                now()->startOfWeek()->format('Y-m-d H:i:s'),
                now()->endOfWeek()->format('Y-m-d H:i:s')
            ])->where('trx_type','+')->sum('amount');
        $data['payout_today'] = Transaction::where('trx_type','+')->whereDate('created_at', now()->format('Y-m-d'))->sum('amount');

        $data['total_omset'] = Transaction::where('remark', 'purchased_plan')
                ->orWhere('remark', 'repeat_order')->sum('amount'); 
        $widget['omset_this_month'] = Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('remark', 'purchased_plan')
            ->orWhere('remark', 'repeat_order')->sum('amount');
        $widget['omset_this_week'] = Transaction::whereYear('created_at', now()->year)
                ->whereBetween('created_at', [
                    now()->startOfWeek()->format('Y-m-d H:i:s'),
                    now()->endOfWeek()->format('Y-m-d H:i:s')
                ])->where('remark', 'purchased_plan')
                ->orWhere('remark', 'repeat_order')->sum('amount');
        $widget['omset_today'] = Transaction::whereDate('created_at', now()->format('Y-m-d'))->where('remark', 'purchased_plan')
                ->orWhere('remark', 'repeat_order')->sum('amount');
        return view('admin.dashboard.new-dashboard',$data);
    }
}
