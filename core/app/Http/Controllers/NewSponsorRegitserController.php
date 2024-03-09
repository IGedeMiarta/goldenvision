<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserPin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class NewSponsorRegitserController extends Controller
{
    public function register(Request $request){
        // dd($request->all());
        $general = GeneralSetting::first();
        $agree = 'nullable';
        if ($general->agree_policy) {
            $agree = 'required';
        }
        $validate = Validator::make($request->all(),[
            'sponsor'   => 'required',
            'upline'    => 'required',
            'position'  => 'required',
            'pin'       => 'required|min:1',
            'firstname'     => 'sometimes|required|string|max:60',
            'lastname'      => 'sometimes|required|string|max:60',
            'email'         => 'required|regex:/^[a-zA-Z0-9@.]+$/|string|email|max:160',
            'email_dinaran'         => 'required|regex:/^[a-zA-Z0-9@.]+$/|string|email|max:160',
            'mobile'        => 'required|string|max:30',
            'password'      => 'required|string|min:6|confirmed',
            'username'      => 'required|alpha_num|unique:users|min:6',
            'country_code'  => 'required',
            'agree' => $agree
        ]);
      
        if ($validate->fails()) {
           return redirect()->back()->withInput($request->all())->withErrors($validate);
        }
            

        if (auth()->user()->pin +1 < $request->pin) {
            $notify[] = ['error','Not enough pin to send'];
            return redirect()->back()->withNotify($notify);
        }
        DB::beginTransaction();
        try {
                                
            $newUser = $this->placementFirstAccount($request->all());  //register user
         
            if($newUser == false){
                $notify[] = ['success', 'Invalid On First Placement, Rollback'];
                return redirect()->route('user.home')->withNotify($notify)->withInput($request->all());
            }
            $sponsor = User::find(auth()->user()->id);
            
            UserPin::create([
                'user_id' => $sponsor->id,
                'pin'     => $request->pin,
                'pin_by'  => '',
                'type'      => "-",
                'start_pin' => auth()->user()->pin,
                'end_pin'   => auth()->user()->pin - $request->pin,
                'ket'       => 'Sponsor Create '. $request->pin.' User and Subsibed each'
            ]);
            $sponsor->pin -=  $request->pin;
            $sponsor->save();
            
            $buyPlan = $this->planStore([
                'plan_id'   => 1,
                'upline'    => $request->upline,
                'sponsor'   => $request->sponsor,
                'pin'       => $request->pin,
                'position'  => $request->position,
                'user_id'   => $newUser->id,
            ]);
            // dd($buyPlan);
            if(!$buyPlan){
                $notify[] = ['success', 'Invalid On Subscibe Plan, Rollback'];
                return redirect()->back()->withNotify($notify);
            }        
            $checkloop = $request->pin > 1  ? true:false;

            if(!$checkloop){
                
                // leaderCommission2(Auth::user()->id,$request->pin);
                checkRank($newUser->id);
                DB::commit();
                addToLog('Created '.$request->pin.' User & Purchased Plan');
                $notify[] = ['success', 'Created User & Purchased Plan Successfully'];
                return redirect()->route('user.my.tree') ->withNotify($notify);
            }else{
                $registeredUser = $request->pin;
                $firstUpline = $newUser;
                $position = 2;
                for ($i=1; $i < $registeredUser; $i++) { 
                    if($i <= 4){
                    $sponsor = $firstUpline;
                    }
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

                    $firstUpline = User::find($firstUpline->id);
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
                        $pinNewUser,
                    );
                    // dd($nextUser);
                    if($nextUser == false){
                        $notify[] = ['error', 'Invalid On Create Downline, Rollback'];
                        return redirect()->back()->withNotify($notify);
                    }
                    
                    $bro_upline = $nextUser->no_bro;

                    $user = UserExtra::where('user_id',$sponsor->id)->first();
                    $user->is_gold = 1;
                    $user->save();
                }
            }
            
            // leaderCommission2(Auth::user()->id,$request->pin);
            checkRank(Auth::user()->id);
            DB::commit();
            addToLog('Created '.$request->pin.' User & Purchased Plan');
            $notify[] = ['success', 'Success Created '.$request->pin.' User & Purchased Plan Each'];
            return redirect()->route('user.my.tree') ->withNotify($notify);
        } catch (\Throwable $th) {
            dd($th->getMessage());
            DB::rollBack();
            $notify[] = ['error', 'Error on Placement, Rollback!'];
            return redirect()->back()->withNotify($notify);
        }
    
    }

    function placementFirstAccount(array $data)
    {
       
        try {
            $user = User::create([
                'firstname' => $data['firstname']??null,
                'lastname'  => $data['lastname']?? null,
                'email'    =>  $data['email'] ??null,
                'email_dinaran'    =>  $data['email_dinaran'] ??null,
                'password'  => Hash::make($data['password']),
                'username'  => $data['username'],
                'mobile'    => $data['mobile']??'',
                'address'   => [
                    'address' => '',
                    'state' => '',
                    'zip' => '',
                    'country' => 'Indonesia',
                    'city' => ''
                ],
                'status'    => 1,
                'ev'        => 1,
                'sv'        => 1,
                'ts'        => 0,
                'tv'        => 1,
                'new_ps'    => 1,

            ]);
            UserExtra::create([
                'user_id' => $user->id
            ]);
           

            return $user;
       } catch (\Throwable $th) {
            dd($th->getMessage());
            return false;
       }

       
    }
    public function addPin($pin,$user_id){
       
        $user = User::find($user_id);
        $sponsor = Auth::user();
        $trx = getTrx();
        try {
            if ($sponsor->pin < $pin) {
                return ['error'=>true, 'msg'=> 'Not enough pin to send'];
            }
            $spin = UserPin::create([
                'user_id' => $sponsor->id,
                'pin'     => $pin,
                'pin_by'  => $user->id,
                'type'      => "-",
                'start_pin' => $sponsor->pin,
                'end_pin'   => $sponsor->pin - $pin,
                'ket'       => 'Sponsor Create and Send '.$pin.' Pin to: '. $user->username
            ]);
            $sponsor->pin -= $pin;
            $sponsor->save();

            $upin = UserPin::create([
                'user_id' => $user_id,
                'pin'     => $pin,
                'pin_by'  => $sponsor->id,
                'type'      => '+',
                'start_pin' => $user->pin,
                'end_pin'   => $user->pin + $pin,
                'ket'       => 'Added Pin By Sponsor: '. $sponsor->username
            ]);
            addToLog('Sponsor Create and Send '.$pin.' Pin to: '. $user->username);
           
            
            $user->pin += $pin;
            $user->save();
            
            // $transaction = new Transaction();
            // $transaction->user_id = $user_id;
            // $transaction->amount = $pin;
            // $transaction->post_balance = 0;
            // $transaction->charge = 0;
            // $transaction->trx_type = '+';
            // $transaction->details = 'Added Pin Via Admin';
            // $transaction->trx =  $trx;
            // $transaction->save();

            return ['sts'=>$user->pin,'error'=>false];
        } catch (\Throwable $th) {
            return ['error'=>true, 'msg'=> 'Error: '.$th->getMessage()];
        }
         
    }
    public function planStore($data)
    {
        try {
            $plan = Plan::where('id', $data['plan_id'])->where('status', 1)->firstOrFail();
            
            $user = User::find($data['user_id']);
            
            $upline = User::where('username',$data['upline'])->first();

            $sponsor = User::where('username', $data['sponsor'])->first();
            
            $pos = getPosition($sponsor->id, $sponsor->default_pos);
            
            if($pos['position'] == 0){
                return false;
            }
            $user->ref_id           = $sponsor->id; // ref id = sponsor
            $user->pos_id           = $upline->id; //pos id = upline
            $user->position         = $data['position'];
            $user->no_bro           = $user->username;
            $user->position_by_ref  = $sponsor->default_pos;
            $user->plan_id          = $plan->id;
            $user->total_invest     += ($plan->price * 1);
            $user->save();

            brodev($data['user_id'], $data['pin']);

            $trx = $user->transactions()->create([
                'amount' => $plan->price * $data['pin'],
                'trx_type' => '-',
                'details' => 'Purchased ' . $plan->name . ' For '.$data['pin'].' MP',
                'remark' => 'purchased_plan',
                'trx' => getTrx(),
                'post_balance' => getAmount($user->balance),
            ]);
            
            updatePaidCount2($user->id);
            $userSponsor = User::find($data['user_id']);
            $details = $userSponsor->username. ' Subscribed to ' . $plan->name . ' plan.';

            addToLog('Purchased ' . $plan->name . ' For '.$data['pin'].' MP as Sponsor');

            referralCommission2($user->id, $details);
            leaderCommission2($sponsor->id,$data['pin']);
            updateLimit($user->id);
            // updatePaidCount2($user->id);

            deliverPoint($user->id,$data['pin']*2);
            return $trx;  
        } catch (\Throwable $th) {
            dd($th->getMessage());
            return false;
            // dd($th->getMessage(),'error');
        }
    }

}
