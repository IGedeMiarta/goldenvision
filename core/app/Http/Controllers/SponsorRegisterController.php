<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\bank;
use App\Models\GeneralSetting;
use App\Models\Gold;
use App\Models\Plan;
use App\Models\rekening;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserLogin;
use App\Models\UserPin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Tree\TreeService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trunc;

class SponsorRegisterController extends Controller
{
    public function __construct(public TreeService $treeService)
    {
        $this->activeTemplate = activeTemplate();
    }
    public function setSession(Request $request){
        
        $data = [
            'upline'    => $request->upline,
            'position'  => $request->postion,
            'url'       => $request->back
        ];
        $request->session()->put('SponsorSet', $data);
        return response()->json(['sts'=>200,'url'=>route('user.sponsor.regist'),'data'=>$data]);
    }

    public function index(){
        // dd(session()->get('SponsorSet')['url']);
        // dd(session()->get('SponsorSet')['upline']);
        $data['page_title'] = "Register By Sponsor";
        $data['user'] = Auth::user();
        $data['bank'] = bank::all();
        $data['upline'] = User::where('no_bro',session()->get('SponsorSet')['upline'])->orWhere('id',session()->get('SponsorSet')['upline'])->first();
        return view($this->activeTemplate . 'user.registerSponsor', $data);
    }
    
    public function registerUser(Request $request){
        $general = GeneralSetting::first();
        $agree = 'nullable';
        if ($general->agree_policy) {
            $agree = 'required';
        }
        $validate = Validator::make($request->all(),[
            'sponsor'   => 'required',
            'upline'    => 'required',
            'position'  => 'required',
            'pin'       => 'required|numeric|min:1',
            'firstname'     => 'sometimes|required|string|max:60',
            'lastname'      => 'sometimes|required|string|max:60',
            'email'         => 'required|regex:/^[a-zA-Z0-9@.]+$/|string|email|max:160',
            'mobile'        => 'required|string|max:30',
            'password'      => 'required|string|min:6|confirmed',
            'username'      => 'required|alpha_num|unique:users|min:6',
            'country_code'  => 'required',
            'agree' => $agree
        ]);
      
        if ($validate->fails()) {
            // dd('error validasi');
           return redirect()->back()->withInput($request->all())->withErrors($validate);
        }
            

        if (auth()->user()->pin +1 < $request->pin) {
            $notify[] = ['error','Not enough pin to send'];
            return redirect()->back()->withNotify($notify);
        }
        DB::beginTransaction();
        try {
          
          
            // create rekening
            $cekbank = rekening::where('nama_bank',$request->bank_name)
                            ->where('nama_akun','like','%'.$request->acc_name.'%')
                            ->where('no_rek','like','%'.$request->acc_number.'%')
                                ->first();
                                
            // if($cekbank){
            //     $rek = new rekening();  
            //     $rek->user_id = $user->id;
            //     $rek->nama_bank = $cekbank->bank_name??$request->bank_name;
            //     $rek->nama_akun = $cekbank->acc_name??$request->acc_name;
            //     $rek->no_rek = $cekbank->acc_number??$request->acc_number;
            //     $rek->kota_cabang = $cekbank->kota_cabang??$request->kota_cabang;
            //     $rek->save();
            // }else{
            //     $rek = new rekening();  
            //     $rek->user_id = $user->id;
            //     $rek->nama_bank = $request->bank_name;
            //     $rek->nama_akun = $request->acc_name;
            //     $rek->no_rek = $request->acc_number;
            //     $rek->kota_cabang = $request->kota_cabang;
            //     $rek->save();
            // }
            // $pin = $this->addPin($request->pin,$user->id); //send pin to user

            // if($pin['error']){
            //     $notify[] = ['error',$pin['msg']];
            //     return redirect()->route('user.my.tree')->withNotify($notify);
            // }
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
            if(!$buyPlan){
                $notify[] = ['success', 'Invalid On Subscibe Plan, Rollback'];
                return redirect()->back()->withNotify($notify);
            }
            updateCycleNasional($newUser->id);
            
            $checkloop = $request->pin > 1  ? true:false;

            if(!$checkloop){
                fnsingleQualified($sponsor->id,$newUser->id);
                DB::commit();
                addToLog('Created '.$request->pin.' User & Purchased Plan');
                $notify[] = ['success', 'Created User & Purchased Plan Successfully'];
                return redirect(session()->get('SponsorSet')['url'])->withNotify($notify);
            }else{
                $registeredUser = $request->pin;
                $firstUpline = $newUser;
                $position = 2;
                for ($i=1; $i < $registeredUser; $i++) { 
                    if($i <= 4){
                    $sponsor = $firstUpline;
                    $mark = true;
                    // 02: 2,3,4,5
                    }
                    if ($i >= 5 && $i <= 8) {
                        $sponsor = User::where('username',$firstUpline .'_'. 2)->first();
                    
                        $mark = true;
                        // 03: 6,7,8,9
                    }
                    if ($i >= 9  && $i <= 12) {
                        $mark = true;
                        $sponsor = User::where('username',$firstUpline .'_'. 3)->first();
                        // 04: 10,11,12,13,14
                    }
                    if ($i >= 13 && $i <= 16) {
                        $mark = true;
                        $sponsor = User::where('username',$firstUpline .'_'. 4)->first();
                        // 05: 15,16,17,18,19
                    }
                    if ($i >= 17 && $i<= 20) {
                        $mark = true;
                        $sponsor = User::where('username',$firstUpline .'_'. 5)->first();
                        // 06: 20,21,22,13,24
                    }
                    if ($i >= 21 && $i<= 24) {
                        $sponsor = User::where('username',$firstUpline .'_'. 6)->first();
                        $mark = true;
                    }
                    $firstUpline = User::find($firstUpline->id);
                    $bro_upline = $firstUpline->no_bro;
                    $firstnameNewUser = $firstUpline->firstname;
                    $lastnameNewUser = $firstUpline->lastname;
                    $usernameNewUser = $firstUpline->username .'_'. $i+1;
                    $emailNewUser = $firstUpline->email;
                    $phoneNewUser = $firstUpline->mobile;
                    $pinNewUser = 1;
                    $newBankName = $cekbank->nama_bank??null;
                    $newBankAcc = $cekbank->nama_akun??null;
                    $newBankNo = $cekbank->no_rek??null;
                    $newBankCity = $cekbank->kota_cabang??null;

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
                    if($nextUser == false){
                        $notify[] = ['success', 'Invalid On Create Downline, Rollback'];
                        return redirect()->back()->withNotify($notify);
                    }
                    
                    $bro_upline = $nextUser->no_bro;

                    $user = UserExtra::where('user_id',$sponsor->id)->first();
                    $user->is_gold = 1;
                    $user->save();
                }
            }
            DB::commit();
            addToLog('Created '.$request->pin.' User & Purchased Plan');
            $notify[] = ['success', 'Success Created '.$request->pin.' User & Purchased Plan Each'];
            return redirect(session()->get('SponsorSet')['url'])->withNotify($notify);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
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
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'New member registered By Sponsor: '. Auth::user()->username;
            $adminNotification->click_url = route('admin.users.detail', $user->id);
            $adminNotification->save();

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
        // dd($request->all());
        $plan = Plan::where('id', $data['plan_id'])->where('status', 1)->firstOrFail();
        $gnl = GeneralSetting::first();

        $user = User::find($data['user_id']);
        // $ref_user = null;
        $ref_user = User::where('no_bro', $data['upline'])->first();
        if ($ref_user == null && $data['upline']) {
            return ['error'=>true, 'msg'=>'Invalid Upline MP Number.'];
        }
        if ($ref_user) {
            # code...
            $cek_pos = User::where('pos_id', $ref_user->id)->where('position',$data['position'])->first();
    
            if(!treeFilter($ref_user->id,$ref_user->id)){
                return ['error'=>true, 'msg'=> 'Refferal and Upline BRO number not in the same tree.'];
            }
            
            if ($cek_pos) {
                return ['error'=>true, 'msg'=> 'Node you input is already filled.'];
            }
        }
        $sponsor = User::where('no_bro', $data['sponsor'])->first();
        if (!$sponsor) {
            return ['error'=>true, 'msg'=> 'Invalid Sponsor MP Number.'];
        }
        if($ref_user->no_bro == $user->no_bro){
            return ['error'=>true, 'msg'=> 'Invalid Input MP Number. You can`t input your own MP number'];
        }
        $oldPlan = $user->plan_id;

        $pos = getPosition($ref_user->id, $data['position']);
        $wait = fnWaitingList($user->id,$ref_user->id,$pos['position']);
        if($wait){
            sleep(5);
            $pos = getPosition($ref_user->id, $data['position']);
        }
        try {
            $user->no_bro           = generateUniqueNoBro();
            $user->ref_id           = $sponsor->id; // ref id = sponsor
            $user->pos_id           = $ref_user->id; //pos id = upline
            $user->position         = $data['position'];
            $user->position_by_ref  = $ref_user->position;
            $user->plan_id          = $plan->id;
            $user->total_invest     += ($plan->price * $data['pin']);
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
            fnDelWaitList($user->id,$ref_user->id,$pos['position']);
            
            updatePaidCount2($user->id);
            $userSponsor = User::find($data['user_id']);
            $details = $userSponsor->username. ' Subscribed to ' . $plan->name . ' plan.';

            addToLog('Purchased ' . $plan->name . ' For '.$data['pin'].' MP as Sponsor');

            referralCommission2($user->id, $details);
            return $trx;
        } catch (\Throwable $th) {
            return false;
        }
      
        
    }

    public function sendPin(Request $request,$id){
         $validate = Validator::make($request->all(),[
            'pin'       => 'required|numeric|min:1',
        ]);
        if ($validate->fails()) {
           return redirect()->back()->withErrors($validate);
        }
        $user = User::find($id);
        $sponsor = Auth::user();
        $trx = getTrx();
        DB::beginTransaction();
        try {
            if ($sponsor->pin < $request->pin) {
                return ['error'=>true, 'msg'=> 'Not enough pin to send'];
            }
            $spin = UserPin::create([
                'user_id' => $sponsor->id,
                'pin'     => $request->pin,
                'pin_by'  => $user->id,
                'type'      => "-",
                'start_pin' => $sponsor->pin,
                'end_pin'   => $sponsor->pin - $request->pin,
                'ket'       => 'Sponsor Send '.$request->pin.' Pin to: '. $user->username
            ]);
            $sponsor->pin -= $request->pin;
            $sponsor->save();
            addToLog('Send '.$request->pin.' Pin to: '. $user->username);

            $upin = UserPin::create([
                'user_id' => $user->id,
                'pin'     => $request->pin,
                'pin_by'  => $sponsor->id,
                'start_pin' => $user->pin,
                'end_pin'   => $user->pin + $request->pin,
                'ket'       => 'Added '.$user->pin.' Pin By Sponsor: '. $sponsor->username
            ]);
           
            
            $user->pin += $request->pin;
            $user->save();
            
            // $transaction = new Transaction();
            // $transaction->user_id = $user->id;
            // $transaction->amount = $user->pin;
            // $transaction->post_balance = 0;
            // $transaction->charge = 0;
            // $transaction->trx_type = '+';
            // $transaction->details = 'Added Pin Via Admin';
            // $transaction->trx =  $trx;
            // $transaction->save();

            DB::commit();
            $notify[] = ['success','Send Pin Success to '.$user->username];
            return back()->withNotify($notify);
        } catch (\Throwable $th) {
            DB::rollBack();
            $notify[] = ['error', 'Error: '. $th->getMessage() ];
            return back()->withNotify($notify);
        }
         
    }


    public function convertSaldo(Request $request){
        $validate = Validator::make($request->all(),[
            "saldo" => "required|numeric",
            "qty" => "required|numeric",
            "idr" => "required|numeric",
            "sisa" => "required|numeric",
        ]);
        if ($validate->fails()) {
           return redirect()->back()->withInput($request->all())->withErrors($validate);
        }
        $user = Auth::user();
        $trx = getTrx();
        DB::beginTransaction();
        try {

            if ($user->balance < $request->idr) {
                return ['error'=>true, 'msg'=> 'Not enough balace to convert'];
            }

            $upin = UserPin::create([
                'user_id' => $user->id,
                'pin'     => $request->qty,
                'pin_by'  => null,
                'type'      => '+',
                'start_pin' => $user->pin,
                'end_pin'   => $user->pin + $request->qty,
                'ket'       => 'Convert '.$request->idr.' Balance To '.$request->qty.' Pin'
            ]);
           
            
            $user->pin += $request->qty;
            $user->balance -= $request->idr;
            $user->save();


            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $request->idr;
            $transaction->post_balance = $request->sisa;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Convert '.$request->idr.' Balance To '.$request->qty.' Pin';
            $transaction->trx =  $trx;
            $transaction->save();


            DB::commit();
            addToLog('Convert '.$request->idr.' Balance To '.$request->qty.' Pin');
            
            $notify[] = ['success', 'Convert '.$request->idr.' Balance To '.$request->qty.' Pin'];
            return redirect()->back()->withNotify($notify);

        } catch (\Throwable $th) {
            DB::rollBack();
            $notify[] = ['success', 'Error: '.$th->getMessage()];
            return redirect()->back()->withNotify($notify);
        }
    }

    public function userSendPin(){
        $data['page_title'] = "Send PINs";
        return view($this->activeTemplate . 'user.sendPin', $data);
    }
    public function findUname($uname){
        $find = User::where('username','=',$uname)->first();
       
        if(!$find){
            return response()->json(['status'=>404,'msg'=>"Username `".$uname."` Not Found!"]);
        }
        if($find->id == auth()->user()->id){
            return response()->json(['status'=>404,'msg'=>"Can't Send Pin To Yourself!"]);
        }
        return response()->json(['status'=>200,'msg'=>'Username Correct: `'.$find->username.' - '.$find->no_bro.'`','data'=>$find]);
    }

}
