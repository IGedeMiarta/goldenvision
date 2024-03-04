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

class PlanController extends Controller
{
    public function __construct(public TreeService $treeService)
    {
        $data =  'lorem';
        $this->activeTemplate = activeTemplate();
    }

    function planIndex()
    {
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
        $request['position'] = $request->pos ?? 2;

        $this->validate($request, [
            'plan_id' => 'required|integer', 
            'qty' => 'required',
        ]);
        $checkloop  = $request->qty > 1  ? true:false;
        $checkBankAcc = rekening::where('user_id',auth()->user()->id)->first();
        $waitlistUserID = [];
        DB::beginTransaction();
        try {
           
            $sponsor = User::find(Auth::user()->ref_id);
            if(!$sponsor){
                $sponsor = User::where('comp',1)->first();
            }
            // checkJalur Kiri;
            if($request['position'] == 1){
                //check jika jalur kiri pertama kosong;
                $ref_user = User::where(['pos_id'=>$sponsor->id,'position'=>1])->first();
               
                if(!$ref_user){
                    $ref_user = $sponsor;
                    $request['position'] =  1;
                }else{
                    $request['position'] =  2;          
                }
            }else{
                $ref_user = $sponsor;
                $request['position'] =  2;          
            }
           
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
            

            $firstUpline = $this->placementFirstAccount($user,$request,$ref_user,$plan,$sponsor);
            updateLimit($firstUpline->id);
            
            if($firstUpline == false){
                $notify[] = ['error', 'Invalid On First Placement, Rollback'];
                return redirect()->back()->withNotify($notify);
            }
            $waitlistUserID[] =  $user->id;
            
            if (!$checkloop) {
                fnSingleQualified($sponsor->id,$firstUpline->id);
                fnDelWaitList(Auth::user()->id);
                
                deliverPoint(Auth::user()->id,$request->qty*2);
                checkRank($user->id);
                
                DB::commit();
                leaderCommission(Auth::user()->id,$request->qty);
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
                $newBankName = $checkBankAcc->nama_bank??null;
                $newBankAcc = $checkBankAcc->nama_akun??null;
                $newBankNo = $checkBankAcc->no_rek??null;
                $newBankCity = $checkBankAcc->kota_cabang??null;

                $nextUser = fnRegisterUser(
                    $sponsor,
                    $bro_upline,
                    $position,
                    $firstnameNewUser,
                    $lastnameNewUser,
                    $usernameNewUser,
                    $emailNewUser,
                    $phoneNewUser,
                    $pinNewUser,
                    $newBankName,
                    $newBankCity,
                    $newBankAcc,
                    $newBankNo
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
            
            deliverPoint(Auth::user()->id,$request->qty*2);
            
            checkRank($user->id);
            leaderCommission(Auth::user()->id,$request->qty);
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
    function placementFirstAccount($user,$request,$ref_user,$plan,$sponsor)
    {

        $gnl = GeneralSetting::first();
        try {
            
            $pos = getPosition($ref_user->id, $request->position);

            if($pos['position'] == 0){
                return false;
            }
            $user = User::find($user->id);
            $user->ref_id           = $sponsor->id; // ref id = sponsor
            $user->pos_id           = $pos['pos_id']; //pos id = upline
            $user->position         = $pos['position'];
            $user->position_by_ref  = $ref_user->position;
            $user->plan_id          = $plan->id;
            $user->total_invest     += ($plan->price * 1);
            $user->save();

            $spin = UserPin::create([
                'user_id' => $user->id,
                'pin'     => $request->qty,
                'pin_by'  => $user->id,
                'type'      => "-",
                'start_pin' => $user->pin,
                'end_pin'   => $user->pin - ($request->qty),
                'ket'       => 'Sponsor Subscibe and Create '.$request->qty.' New User'
            ]);

            $user->update([
                'pin' => $spin->end_pin
            ]);

        
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
            
            updatePaidCount2($user->id);

            updateCycleNasional($user->id);

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

}