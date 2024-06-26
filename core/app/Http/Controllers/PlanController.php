<?php

namespace App\Http\Controllers;

use App\Models\BvLog;
use App\Models\GeneralSetting;
use App\Models\Gold;
use App\Models\Plan;
use App\Models\rekening;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserPin;
use App\Services\Tree\Enums\TreePosition;
use App\Services\Tree\TreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PlanController extends Controller
{
    public function __construct(public TreeService $treeService)
    {
        $this->activeTemplate = activeTemplate();
    }

    function planIndex()
    {
        $data['general'] = GeneralSetting::orderByDesc('id')->first(); 
        $sponsor = $_GET['sponsor'] ?? '';
        $pos = $_GET['pos'] ?? false;
        $findUser = User::with('userExtra')->where('username',$sponsor)->first();
        $data['sponsor'] = $findUser?->userExtra->is_gold?$findUser:false;
        $data['pos'] = $pos;

        $data['page_title'] = "Order Plan";
        $data['plans'] = Plan::whereStatus(1)->get();
        return view($this->activeTemplate . '.user.new-plan', $data);
    }
    public function repeatOrder(){
        $data['page_title'] = "Reapeat Order";
        $data['plans'] = Plan::whereStatus(1)->get();
        return view($this->activeTemplate . '.user.plan_ro', $data);
    }
    public function planRoStore(Request $request){

        $user = Auth::user();
        $activePin = Auth::user()->pin;
        if ($activePin < 1) {
            $notify[] = ['error', 'Insufficient Balance, Not Enough PIN to Buy'];
            return back()->withNotify($notify);
        }
        $plan = Plan::first();
        DB::beginTransaction();
        try {
            $spin = UserPin::create([
                'user_id' => $user->id,
                'pin'     => 1,
                'pin_by'  => $user->id,
                'type'      => "-",
                'start_pin' => $user->pin,
                'end_pin'   => $user->pin - 1,
                'ket'       => 'Reorder Point 1 ID'
            ]);
            $trx = $user->transactions()->create([
                'amount' => $plan->price *  1 ,
                'trx_type' => '-',
                'details' => 'Repeat Order ' . $plan->name . ' For '. 1 .' Membership POINT',
                'remark' => 'repeat_order',
                'trx' => getTrx(),
                'post_balance' => 0,
            ]);


            $user->pin -= 1;
            $user->total_invest     += $plan->price;
            $user->save();
            
            updatePaidCountRO($user->id);
            updateLimit($user->id);
            
            referralCommission2($user->id, $trx->details);
            leaderCommission2RO($user->id,1);
            leaderCommission2ROFounder($user->id,1);

            // $deliferPointTo = $user->group == 0 ? $user->id:$user->group;
            $firstUsername = findFirstUsername($user->username);
            if(!$firstUsername){
                $deliferPointTo  = $user->id;
            }else{
                $deliferPointTo = $firstUsername;
            }

            deliverPointRO($user,$deliferPointTo,$plan->point);

            DB::commit();
            $notify[] = ['success', 'Reorder Point Success!'];
            return back()->withNotify($notify);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
       
    }

    public function changeDafault(Request $request){
        $user = Auth::user();
        $user->default_pos = $request->pos;
        $user->save();
        

        $notify[] = ['success', 'Success Update Referrals Default Position'];
        return back()->withNotify($notify);
    }

    public function buyMpStore(Request $request){

        $this->validate($request, [
            'qtyy' => 'required|integer|min:1',
        ]);
        $activePin  = Auth::user()->pin;

        $user = User::find(Auth::id());
        $plan = Plan::where('id', $request->plan_id)->where('status', 1)->firstOrFail();
        $gnl = GeneralSetting::first();
        if ($activePin < $request->qtyy) {
            $notify[] = ['error', 'Insufficient Balance, Not Enough PIN to Buy'];
            return back()->withNotify($notify);
        }
        UserPin::create([
            'user_id' => $user->id,
            'pin'     => $request->qtyy,
            'pin_by'  => $user->id,
            'type'      => '-',
            'start_pin' => $user->pin,
            'end_pin'   => $user->pin - $request->qtyy,
            'ket'       => 'Purchased ' . $plan->name . ' For '.$request->qtyy.' MP',
        ]);
        
        $user->pin -= $request->qtyy;
        $user->total_invest += ($plan->price * $request->qtyy);
        $user->bro_qty += $request->qtyy;
        $user->save();

        $trx = $user->transactions()->create([
            'amount' => $plan->price * $request->qty,
            'trx_type' => '-',
            'details' => 'Purchased ' . $plan->name . ' For '.$request->qty.' MP',
            'remark' => 'purchased_plan',
            'trx' => getTrx(),
            'post_balance' => getAmount($user->balance),
        ]);
        $notif = 'Purchased new MP quantity for '.$request->qtyy.' MP Successfully';
       

        if(countAllBonus() >= 10000000){
            $ux = UserExtra::where('user_id',auth()->user()->id)->first();
            $ux->last_ro += countAllBonus();
            $ux->save();
            $notif = 'Purchased new MP Repeat Order quantity for '.$request->qtyy.' MP Successfully';

        }

        $notify[] = ['success', $notif];
        addToLog($notif);
        return redirect()->route('user.home')->withNotify($notify);

    }

    function planStore(Request $request)
    {
        $general = GeneralSetting::orderByDesc('id')->first(); 
        if($general->disable_placement){
            $notify[] = ['error', 'Invalid Placement: Membership registration is currently unavailable.'];
            return redirect()->back()->withNotify($notify);
        }
        $this->validate($request, [
            'plan_id' => 'required|integer', 
            'qty' => 'required',
        ]);
        $checkloop  = $request->qty > 1  ? true:false;
        $waitlistUserID = [];
        DB::beginTransaction();
        try {
           
            $sponsor = User::find(Auth::user()->ref_id);

            if(!$sponsor){
                $sponsor = User::where('comp',1)->first();
            }
            $request['position'] = $sponsor->default_pos;
           
            $user = Auth::user();
            if (!$sponsor) {
                $notify[] = ['error', 'Check Username or Sponsor Subcribe Status'];
                $notify[] = ['error', 'Invalid Sponsor'];
                return back()->withNotify($notify);
            }
            if($sponsor->id  ==  $user->id){
                $notify[] = ['error', 'Invalid Sponsor, you cant not reffer yourself'];
                return back()->withNotify($notify);
            }

        

            $plan = Plan::where('id', $request->plan_id)->where('status', 1)->firstOrFail();
    
            $activePin = Auth::user()->pin;
            if ($activePin < $request->qty) {
                $notify[] = ['error', 'Insufficient Balance, Not Enough PIN to Buy'];
                return back()->withNotify($notify);
            }
            

            $firstUpline = $this->placementFirstAccount($user,$request,$plan,$sponsor);
            
            if($firstUpline == false){
                $notify[] = ['error', 'Invalid On First Placement, Rollback'];
                return redirect()->back()->withNotify($notify);
            }
            $waitlistUserID[] =  $user->id;
            
            if (!$checkloop) {

                deliverPoint(Auth::user()->id, $request->qty * $plan->point );
                checkRank($user->id);
                DB::commit();
                $notify[] = ['success', 'Successfully Purchased Plan'];
                return redirect()->route('user.my.tree')->withNotify($notify);
            }


            $registeredUser = $request->qty;
            

            $firstUsername =  auth()->user()->username;
            for ($i=1; $i < $registeredUser; $i++) { 
                if($registeredUser == 3){
                    if($i== 1){
                        $position = 1;
                    }
                    if($i == 2){
                        $position = 2;
                    }
                }
                if($registeredUser == 5){
                    if($i== 1){
                        $position = 1;
                    }
                    if($i == 2){
                        $position = 1;
                    }
                    if($i== 3){
                        $position = 2;
                    }
                    if($i == 4){
                        $position = 2;
                    }
                }

                $mark = false;
                if($i <= 4){
                    $sponsor = $firstUpline;
                    $mark = true;
                    // 02: 2,3,4,5
                }
                if ($i >= 5 && $i <= 8) {
                    $sponsor = User::where('username',$firstUsername .'_'. $i)->first();
                 
                    $mark = true;
                    // 03: 6,7,8,9
                }
                $bro_upline = $firstUpline->username;
                $firstnameNewUser = $firstUpline->firstname;
                $lastnameNewUser = $firstUpline->lastname;
                $usernameNewUser = $firstUpline->username .'_'. $i+1;
                $emailNewUser = $firstUpline->email;
                $phoneNewUser = $firstUpline->mobile;
                $pinNewUser = 1;

                $nextUser = fnRegisterUser(
                    $sponsor,
                    $bro_upline,
                    $position,
                    $firstnameNewUser,
                    $lastnameNewUser,
                    $usernameNewUser,
                    $emailNewUser,
                    $phoneNewUser,
                    $pinNewUser
                );
                if(!$nextUser){
                    $notify[] = ['error', 'Invalid On Create Downline, Rollback'];
                    // $notify[] = ['error',$nextUser];
                    return redirect()->back()->withNotify($notify);
                }
              
                $bro_upline = $nextUser->username;
               
                $user = UserExtra::where('user_id',$sponsor->id)->first();
                $user->is_gold = 1;
                $user->save();

               
            }
          
            deliverPoint(Auth::user()->id,$request->qty * $plan->point);
            checkRank($user->id);
            DB::commit();
            $notify[] = ['success', 'Purchased ' . $plan->name . 'and Registered New  '.$registeredUser.' Account Successfully'];
            return redirect()->route('user.my.tree')->withNotify($notify);
        } catch (\Throwable $th) {
            DB::rollBack();
            $notify[] = ['error', 'Invalid on Placement, Rollback!'];
            $notify[] = ['error', $th->getMessage()];
            return redirect()->back()->withNotify($notify);
        }
    }
    function placementFirstAccount($user,$request,$plan,$sponsor)
    {

        $gnl = GeneralSetting::first();
        try {
            $user = User::find($user->id);
            
            $pos = getPosition($sponsor->id, $sponsor->default_pos);

            if($pos['position'] == 0){
                return false;
            }
            $user->pos_id           = $pos['pos_id']; 
            $user->position         = $pos['position'];
            $user->position_by_ref  = $sponsor->default_pos;
            $user->plan_id          = $plan->id;
            $user->total_invest     += $plan->price;
            

            $spin = UserPin::create([
                'user_id' => $user->id,
                'pin'     => $request->qty,
                'pin_by'  => $user->id,
                'type'      => "-",
                'start_pin' => $user->pin,
                'end_pin'   => $user->pin - ($request->qty),
                'ket'       => 'Sponsor Subscibe and Create '.$request->qty.' New User'
            ]);

            $user->pin -= $request->qty;
            $user->save();

        
            brodev(Auth::user()->id, $request->qty);

            $trx = $user->transactions()->create([
                'amount' => $plan->price * $request->qty,
                'trx_type' => '-',
                'details' => 'Purchased ' . $plan->name . ' For '.$request->qty.' MP',
                'remark' => 'purchased_plan',
                'trx' => getTrx(),
                'post_balance' => getAmount($user->balance),
            ]);
            addToLog('Purchased ' . $plan->name . ' For '.$request->qty.' MP');

            sendEmail2($user->id, 'plan_purchased', [
                'plan' => $plan->name. ' For '.$request->qty.' MP',
                'amount' => getAmount($plan->price * $request->qty),
                'currency' => $gnl->cur_text,
                'trx' => $trx->trx,
                'post_balance' => getAmount($user->balance),
            ]);

            
                
            $details = Auth::user()->username . ' Subscribed to ' . $plan->name . ' plan.';

            referralCommission2($user->id, $details);
            leaderCommission($user->id,$request->qty);
            leaderCommissionFounder($user->id,$request->qty);
            updatePaidCount2($user->id);
            updateLimit($user->id);
            return $user;
        } catch (\Throwable $th) {
            return false;
            dd('placement',$th->getMessage());
        }
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
    public function seeTree(Request $request){
        if ($request->username) {
            $user = User::where('username', $request->username)->first();
        } else {
            $user = User::where('username', $username)->first();
        }
        if ($user && treeAuth($user->id, auth()->id())) {
            $data['tree'] = showTreePage($user->id);

            $data['page_title'] = "Tree of " . $user->fullname;
            
            // if(!Session::get('log')){
            //     $log[] = $request->up;
            //     Session::put('log',$log);

            // }else{
            //     $ss = Session::get('log');
                
            //     $ss[] = $request->up;
            //     Session::put('log',$ss);
            // }
        

            return view($this->activeTemplate . 'user.myTree', $data);
        }
        
        $notify[] = ['error', 'Tree Not Found or You do not have Permission to view that!!'];
        return redirect()->route('user.my.tree')->withNotify($notify);
    }

}