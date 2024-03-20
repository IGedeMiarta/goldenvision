<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(){
        $data['page_title'] = 'Dashboard';
        return view('admin.dashboard.new-dashboard',$data);
    }
}
