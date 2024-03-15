<?php

namespace App\Http\Controllers\Admin;

use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserLogin;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\BonusReward;
use App\Models\LogActivity;
use App\Models\MemberGrow;
use App\Models\ureward;
use App\Models\UserPin;

class AdminController extends Controller
{

    public function maps(Request $request){
        $user = User::whereNotNull('lat')->get();
        $ll = [];
        foreach ($user as $key => $value) {
            $ll[] = ['lat' => $value->lat, 'lng' => $value->lng];
        }
        return response()->json(['status'=>200,'data'=>$ll,'status'=>'success']);
    }

    public function dashboard(Request $request)
    {
        // dd(totalWdGold());
        if ($request->has('date')) {
           $date = $request->input('date');
        }else{
            $date = date('Y-m',strtotime(now()));
        }
        // dd(totalGlobalPayout());
        // dd(adminLeaderSellPin('2023-03-18 13:31:45'));
        $page_title = 'Dashboard';
        // User Info
        $widget['total_users'] = User::where('comp',0)->count();
        $widget['verified_users'] = User::where('comp',0)->where('status', 1)->count();
        $widget['email_verified_users'] = User::where('comp',0)->where('ev', 1)->count();
        $widget['sms_verified_users'] = User::where('comp',0)->where('sv', 1)->count();

        $widget['banned_users'] = User::where('comp',0)->where('status', 0)->count();
        $widget['emailUnverified'] = User::where('comp',0)->emailUnverified()->count();
        $widget['smsUnverified'] = User::where('comp',0)->smsUnverified()->count();

        $widget['gold_silver'] = countGold();
        $widget['users_balance'] = User::where('comp',0)->sum('balance');
        $widget['totalWdGold'] = totalWdGold();
        $widget['totalMpProd'] = totalMpProd();
        $widget['totalColagenProd'] = totalColagenProd();
        $widget['totalPurchasedPlan'] = sumPurchasedPlan(); //sum semua purched plan dari amount transaksi;
        $widget['totalReferalsCommision'] = sumRefComm();
        $widget['users_invest'] = User::where('comp',0)->sum('total_invest');
        $widget['omset_this_month'] = omsetThisMonth();
        $widget['omset_last_month'] = omsetLastMonth();
        $widget['last7days_invest'] = Transaction::whereDate('created_at', '>=', Carbon::now()->subDays(6))->where('remark', 'purchased_plan')->sum('amount');
        $widget['total_binary_com'] = User::where('comp',0)->sum('total_binary_com');
        $widget['total_ref_com'] = User::where('comp',0)->sum('total_ref_com');
        $widget['total_bro_joined'] = User::where('comp',0)->where('plan_id','!=',0)->count();
        $widget['admin_leader_pin'] = adminLeaderSellPin($date);
        $report['months'] = collect([]);
        $report['deposit_month_amount'] = collect([]);
        $report['withdraw_month_amount'] = collect([]);

        $depositsMonth = Deposit::whereYear('created_at', '>=', Carbon::now()->subYear())
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as depositAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M') as months")
            ->orderBy('created_at')
            ->groupBy(DB::Raw("MONTH(created_at)"))->get();

        $depositsMonth->map(function ($aaa) use ($report) {
            $report['months']->push($aaa->months);
            $report['deposit_month_amount']->push(getAmount($aaa->depositAmount));
        });

        $withdrawalMonth = Withdrawal::whereYear('created_at', '>=', Carbon::now()->subYear())->where('status', 1)
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as withdrawAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M') as months")
            ->orderBy('created_at')
            ->groupBy(DB::Raw("MONTH(created_at)"))->get();
        $withdrawalMonth->map(function ($bb) use ($report){
            $report['withdraw_month_amount']->push(getAmount($bb->withdrawAmount));
        });

        // $registered = userRegiteredChart();
        // dd($registered);
        $registered = registerThisMount();
        $weekleader = sumPinByWeek();
        // dd($weekleader);

        $withdrawal = Withdrawal::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->where('status', 1)
            ->select(array(DB::Raw('sum(amount)   as totalAmount'), DB::Raw('DATE(created_at) day')))
            ->groupBy('day')->get();
        $withdrawals['per_day'] = collect([]);
        $withdrawals['per_day_amount'] = collect([]);
        $withdrawal->map(function ($a) use ($withdrawals) {
            $withdrawals['per_day']->push(date('d M', strtotime($a->day)));
            $withdrawals['per_day_amount']->push($a->totalAmount + 0);
        });


        // user Browsing, Country, Operating Log
        $user_login_data = UserLogin::whereDate('created_at', '>=', \Carbon\Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $user_login_data->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $user_login_data->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $user_login_data->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);


        $payment['payment_method'] = Gateway::count();
        $payment['total_deposit_amount'] = Deposit::where('status',1)->sum('amount');
        $payment['total_deposit_charge'] = Deposit::where('status',1)->sum('charge');
        $payment['total_deposit_pending'] = Deposit::where('status',2)->count();
        $payment['total_deposit_reject'] = Deposit::where('status',3)->count();

        $paymentWithdraw['withdraw_method'] = WithdrawMethod::count();
        $paymentWithdraw['total_withdraw_amount'] = Withdrawal::where('status',1)->sum('amount');
        $paymentWithdraw['total_withdraw_charge'] = Withdrawal::where('status',1)->sum('charge');
        $paymentWithdraw['total_withdraw_pending'] = Withdrawal::where('status',2)->count();


        $bv['bvLeft'] = UserExtra::sum('bv_left');
        $bv['bvRight'] = UserExtra::sum('bv_right');
        $bv['totalBvCut'] = BvLog::where('trx_type', '-')->sum('amount');
        
        $bonusr = BonusReward::where('type','monthly')->count();
        $bonusa = BonusReward::where('type','alltime')->count();
        $ure = ureward::all()->count();
        $ure2 = ureward::where('status',2)->count();

        $latestUser = User::latest()->limit(6)->get();
        $latesLog = LogActivity::with(['user'])->where('subject','like','%Login.%')->orderByDesc('id')->groupBy('user_id')->limit(4)->get();
// dd($leader);
        if(auth()->guard('admin')->user()->role == 'su'){
            return view('admin.dashboard.dashboard', compact('page_title','date','weekleader',
                'widget', 'report', 'withdrawals', 'chart','payment',
                'paymentWithdraw','latestUser', 'bv', 'depositsMonth','latesLog', 'withdrawalMonth','registered'));
        }elseif(auth()->guard('admin')->user()->role == 'vu'){
            return view('admin.dashboard.dasboardView', compact('page_title','date','lPin','weekleader',
                'widget', 'report', 'withdrawals', 'chart','payment',
                'paymentWithdraw','latestUser', 'bv', 'depositsMonth','latesLog','leader', 'withdrawalMonth','registered'));
        }else{
            return view('admin.dashboard.dashboard_ar', compact('page_title','date',
                'widget', 'report', 'withdrawals', 'chart','payment','weekleader',
                'paymentWithdraw','latestUser', 'bv','mem','lPin', 'depositsMonth','latesLog', 'withdrawalMonth','registered','bonusr','bonusa','ure','ure2'));
        }

        
    }


    public function profile()
    {
        $page_title = 'Profile';
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('page_title', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        $user = Auth::guard('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = uploadImage($request->image, 'assets/admin/images/profile/', '400X400', $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('admin.profile')->withNotify($notify);
    }


    public function password()
    {
        $page_title = 'Password Setting';
        $admin = Auth::guard('admin')->user();
        return view('admin.password', compact('page_title', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = Auth::guard('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password Do not match !!'];
            return back()->withErrors(['Invalid old password.']);
        }
        $user->update([
            'password' => bcrypt($request->password)
        ]);
        $notify[] = ['success', 'Password Changed Successfully.'];
        return redirect()->route('admin.password')->withNotify($notify);
    }


    public function notifications(){
        $notifications = AdminNotification::orderBy('id','desc')->paginate(getPaginate());
        $page_title = 'Notifications';
        return view('admin.notifications',compact('page_title','notifications'));
    }


    public function notificationRead($id){
        $notification = AdminNotification::findOrFail($id);
        $notification->read_status = 1;
        $notification->save();
        return redirect($notification->click_url);
    }

    public function AdminReward(){
        $data['page_title'] = 'Admin Reward';
        $data['empty_message'] = 'No Products found';
        $data['admin'] = Admin::where('role','=','ar')->paginate(getPaginate());
        return view('admin.admin_reward.index',$data);
    }
    public function AdminRewardStore(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required|unique:admins,username',
            'password'  => 'required|confirmed'
        ]);
        DB::beginTransaction();
        try {
            Admin::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'username'  => $request->username,
                'password'  => Hash::make($request->password),
                'role'      => 'ar'
            ]);
            DB::commit();
            $notify[] = ['success', 'Admin Reward Created Successfully.'];
            return redirect()->back()->withNotify($notify);
        } catch (\Throwable $th) {
            DB::rollBack();
            $notify[] = ['success', 'Error: '.$th->getMessage()];
            return redirect()->back()->withNotify($notify);
        }
    }

}
