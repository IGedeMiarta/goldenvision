<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(){
        $data['page_title'] = 'Dashboard';

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

        // dd($data);
        return view('admin.dashboard.new-dashboard',$data);
    }
}
