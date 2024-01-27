<?php

use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Plan;
use App\Models\rekening;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserLogin;
use App\Models\UserPin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


function fnRegisterUser($sponsor,$broUpline,$position,$firstname,$lastname,$username,$email,$phone,$pin,$bank_name,$kota_cabang,$acc_name,$acc_number){
    
    $ref_user = User::where('no_bro', $broUpline)->first();
    
    
    $pos = getPosition($ref_user->id, 2);

    $data = [
        'pos'       => $pos,
        'sponsor'   => $sponsor,
        'upline'    => $broUpline,
        'position'  => $position,
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'username'  => $username,
        'email'     => $email,
        'phone'     => $phone,
        'pin'       => $pin,
        'bank_name' => $bank_name,
        'kota_cabang' => $kota_cabang,
        'acc_name' => $acc_name,
        'acc_number' => $acc_number,
    ];
    
    $user = fnCreateNewUser($data);  //register user

    fnPlanStore($data,$user);

    updateCycleNasional($user->id);

    sendEmail2($user->id,'sponsor_register',[
        'email' => $user->email,
        'sponsor'  => $data['sponsor']->username,
        'user' => $user->username,
        'url' => url('/login?username='.$user->username.'&password='.$user->username),
    ]);
    addToLog('Created User '.$user->username.' & Purchased Plan');
    return User::find($user->id);    
}

function fnPlanStore(array $data,$user)
{
    
    $plan = Plan::where('id', 1)->where('status', 1)->firstOrFail();

    $user = User::find($user->id);
    $user->no_bro           = generateUniqueNoBro();
    $user->ref_id           = $data['sponsor']->id; 
    $user->pos_id           = $data['pos']['pos_id']; //pos id = upline
    $user->position         = $data['pos']['position'];
    $user->position_by_ref  = 2;
    $user->plan_id          = $plan->id;
    $user->pin              -= 1;
    $user->total_invest     += ($plan->price * 1);
    $user->bro_qty          = 0;
    $user->save();

    brodev($user->id, 1);

    $user->transactions()->create([
        'amount' => $plan->price * 1,
        'trx_type' => '-',
        'details' => 'Purchased ' . $plan->name . ' For '. 1 .' MP',
        'remark' => 'purchased_plan',
        'trx' => getTrx(),
        'post_balance' => getAmount($user->balance),
    ]);

    $gnl = GeneralSetting::first();
    sendEmail2($user->id, 'plan_purchased', [
        'plan' => $plan->name. ' For '. 1 .' MP',
        'amount' => getAmount($plan->price * 1),
        'currency' => $gnl->cur_text,
        'trx' => getTrx(),
        'post_balance' => getAmount($user->balance),
    ]);

    $userSponsor = User::find($user->id);
    $details = $userSponsor->username. ' Subscribed to ' . $plan->name . ' plan.';

    addToLog('Purchased ' . $plan->name . ' For '.  1 .' MP as Sponsor');

    referralCommission2($user->id, $details);   
}
function fnCreateNewUser(array $data)
{
    $gnl = GeneralSetting::first();
    $user = User::create([
        'firstname' => isset($data['firstname']) ? $data['firstname'] : null,
        'lastname'  => isset($data['lastname']) ? $data['lastname'] : null,
        'email'     => strtolower(trim($data['email'])),
        'password'  => $data['sponsor']->password,
        'username'  => strtolower(trim($data['username'])),
        'mobile'    => $data['phone'],
        'address'   => $data['sponsor']->address,
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
    $rek = new rekening();  
    $rek->user_id = $user->id;
    $rek->nama_bank = $data['bank_name'];
    $rek->nama_akun = $data['acc_name'];
    $rek->no_rek = $data['acc_number'];
    $rek->kota_cabang = $data['kota_cabang'];
    $rek->save();

    $adminNotification = new AdminNotification();
    $adminNotification->user_id = $user->id;
    $adminNotification->title = 'New member registered By Sponsor: '.$data['sponsor']->username;
    $adminNotification->click_url = route('admin.users.detail', $user->id);
    $adminNotification->save();

    return $user;
}

function fnAddPin($pin,$user_id,$sponsor){
    $user = User::find($user_id);
    $sponsor = User::find($sponsor->id);
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
}