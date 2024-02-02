<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LandingController extends Controller
{
    public function createAcc($jml,$username){
        try {
            for ($i=1; $i <= $jml; $i++) { 
                $user = User::create([
                'firstname' => 'master',
                'lastname'  => $username  .'1'. $i,
                'email'    => 'acc@masterplan.co.id',
                'password'  => Hash::make('password'),
                'username'  => $username.$i,
                'mobile'    => '12345678910',
                'address'   => [
                    'address' => '',
                    'state' => '',
                    'zip' => '',
                    'country' => 'Indonesia',
                    'city' => ''
                ],
                'no_bro'    => 'BRO-001' .'1'.$i,
                'ref_id'    => '1'. $i - 1,
                'pos_id'    => '1'. $i - 1,
                'position'  => 2,
                'position_by_ref'=>2,
                'plan_id'   => 1,
                'comp'      => 1,
                'pin'       => 0,
                'status'    => 1,
                'ev'        => 1,
                'sv'        => 1,
                'ts'        => 0,
                'tv'        => 1,
                'new_ps'    => 1,

                ]);
                UserExtra::create([
                    'user_id' => $user->id,
                    'is_gold' =>1,
                ]);
            }
             return 'success';
        } catch (\Throwable $th) {
            //throw $th;
            return ['sts'=>'error','error'=>$th->getMessage()];
        }
       
    }
    public function index(){
        $data['user']= User::count();
        $active = UserExtra::with('user')->where('n_cyle',0)->orderBy('id','asc')->first();
        // dd($active);

        $atas =  UserExtra::where('n_cyle',1)->select('id')->get();
        $data['cycle'] = $atas->count() < 1?false:true;
        if($atas->count() < 1){
            return view('v3.home',$data);

        }
        foreach ($atas as $key => $value) {
            $atasArr[] = $value->id;
        }
        $rand = $this->pickRandomNumber($atasArr);

        $pAtas  = UserExtra::with('user')->where('user_id',$rand)->first();
        $p1     = UserExtra::with('user')->where('user_id',$pAtas->national_1)->first();
        $p2     = UserExtra::with('user')->where('user_id',$pAtas->national_2)->first();
        $p3     = UserExtra::with('user')->where('user_id',$pAtas->national_3)->first();
        $p4     = UserExtra::with('user')->where('user_id',$pAtas->national_4)->first();

        $data['atas'] = $pAtas;
        $data['c1']      = $p1;
        $data['c11']     = $p1->national_1 != null?User::find($p1->national_1):null;
        $data['c12']     = $p1->national_2 != null?User::find($p1->national_2):null;
        $data['c13']     = $p1->national_3 != null?User::find($p1->national_3):null;
        $data['c14']     = $p1->national_4 != null?User::find($p1->national_4):null;
        $data['c2']      = $p2;
        $data['c21']     = $p2->national_1 != null?User::find($p2->national_1):null;
        $data['c22']     = $p2->national_2 != null?User::find($p2->national_2):null;
        $data['c23']     = $p3->national_3 != null?User::find($p2->national_3):null;
        $data['c24']     = $p3->national_4 != null?User::find($p2->national_4):null;

        $data['c3']      = $p3;
        $data['c31']     = $p3->national_1 != null?User::find($p3->national_1):null;
        $data['c32']     = $p3->national_2 != null?User::find($p3->national_2):null;
        $data['c33']     = $p3->national_3 != null?User::find($p3->national_3):null;
        $data['c34']     = $p3->national_4 != null?User::find($p3->national_4):null;

        $data['c4']      = $p4;
        $data['c41']     = $p4->national_1 != null?User::find($p4->national_1):null;
        $data['c42']     = $p4->national_2 != null?User::find($p4->national_2):null;
        $data['c43']     = $p4->national_3 != null?User::find($p4->national_3):null;
        $data['c44']     = $p4->national_4 != null?User::find($p4->national_4):null;

        
        // $data['cyle1'] = ;

        // $data['cyle'] = $active;
        // $data['downline'] = [
        //     '1' => $active->national_1 != null?User::find($active->national_1):null,
        //     '2' => $active->national_2 != null?User::find($active->national_2):null,
        //     '3' => $active->national_3 != null?User::find($active->national_3):null,
        //     '4' => $active->national_4 != null?User::find($active->national_4):null,
        // ];
        return view('v3.home',$data);
    }
    function pickRandomNumber($array) {
        // Get a random key/index from the array
        $randomKey = array_rand($array);
        
        // Return the element corresponding to the random key/index
        return $array[$randomKey];
    }
}
