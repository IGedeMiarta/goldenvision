<?php

namespace App\Models;

use App\Enums\UserGoldReward;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'ver_code_send_at' => 'datetime'
    ];

    protected $data = [
        'data'=>1
    ];


    public function log(){
        return $this->hasMany(LogActivity::class);
    }

    public function login_logs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',0);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',0);
    }

    //mlm
    public function userExtra()
    {
        return $this->hasOne(UserExtra::class);
    }

    public function userBank()
    {
        return $this->hasOne(rekening::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function golds()
    {
        return $this->hasMany(UserGold::class);
    }
    public function reward()
    {
        return $this->hasMany(ureward::class);
    }
    public function bonus(){
        return $this->hasMany(BonusUser::class);
    }

    public function dailyGolds()
    {
        return $this->golds()->where('type', UserGoldReward::Daily->value);
    }

    public function weeklyGolds()
    {
        return $this->golds()->where('type', UserGoldReward::Weekly->value);
    }

    public function getTotalDailyGoldsAttribute()
    {
        return $this->dailyGolds()->sum('golds') ?: 0;
    }

    public function getTotalWeeklyGoldsAttribute()
    {
        return $this->weeklyGolds()->sum('golds') ?: 0;
    }

    public function getTotalGoldsAttribute()
    {
        return $this->golds()->sum('golds') ?: 0;
    }


    public function getCanAddWeeklyGoldAttribute()
    {
        // return $this->total_weekly_golds ?: 0 < 0.50 && $this->total_daily_golds
    }

    public static function hasClaimedDailyGold($id)
    {
        return (new static)
            ->newModelQuery()
            ->where('id', $id)
            ->whereHas(
                'dailyGolds',
                fn ($builder) => $builder->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            )
            ->exists();
    }
    public static function hasClaimedWeeklyGold($id)
    {
        return (new static)
            ->newModelQuery()
            ->where('id', $id)
            ->whereHas(
                'weeklyGolds',
                fn ($builder) => $builder->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            )
            ->exists();
    }

    public static function canClaimDailyGold($id)
    {
        $user = (new static)
            ->newModelQuery()
            ->withCount('dailyGolds')
            ->find($id);

        return  ($user?->daily_golds_count < 100) &&
                (! static::hasClaimedDailyGold($id)) &&
                // ($user?->is_kyc == 2) &&
                ($user?->no_bro != null);
    }
    public static function canClaimWeeklyGold($id)
    {
        $user = (new static)
            ->newModelQuery()
            ->withCount('dailyGolds')
            ->find($id);

        return  ($user?->daily_golds_count > 100) &&
                (! static::hasClaimedWeeklyGold($id)) &&
                // ($user?->is_kyc == 2) &&
                ($user?->no_bro != null);
    }

    public static function canAddWeeklyGold($id)
    {
        return (UserGold::dailyGoldsOf($id)->count() === 100) && (
            UserGold::weeklyGoldsOf($id)->sum('golds') < 0.50
        );
    }


    public function rank(){
        return $this->belongsTo(Rank::class,'rank');
    }

    // SCOPES

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeSmsUnverified()
    {
        return $this->where('sv', 0);
    }
    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeSmsVerified()
    {
        return $this->where('sv', 1);
    }

    public function scopeWithGoldsTotal($builder)
    {
        $builder->addSelect([
            // 'total_daily_golds' => $this->aggregateGoldsReward(UserGoldReward::Daily),
            // 'total_weekly_golds' => $this->aggregateGoldsReward(UserGoldReward::Weekly)
            'total_daily_golds' => $this->goldUser('daily'),
            'total_weekly_golds' => $this->goldUser('weekly')
        ]);
    }

    protected function aggregateGoldsReward(UserGoldReward $reward)
    {
        return fn ($builder) => $builder
            ->from('user_golds')
            ->selectRaw('sum(golds)')
            ->whereColumn('user_golds.user_id', 'users.id')
            ->where('type', $reward->value);
    }


    public static function goldUser($type){
        // $sql = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at,sum(golds) as total_gold')
        //     // ->where('user_id', auth()->user()->id)
        //     ->whereColumn('user_golds.user_id', 'users.id')
        //     ->where('type', $type)
        //     ->groupBy('date')
        //     ->orderByDesc('date')
        //     ->first();
        $sql =  fn ($builder) => $builder
            ->from('user_golds')
            ->selectRaw('sum(golds)')
            ->whereColumn('user_golds.user_id', 'users.id')
            ->where('type', $type);
            
        return $sql;
    }


    public static function userTree($id){
        $group = [];
        $user = User::find($id);
        $group[1] = User::where('ref_id',$user->id)->get();
        $id2 = [];
        $id3 = [];
        $id4 = [];
        $id5 = [];
        foreach ($group[1] as $key => $value) {
            $id2[] = $value->id; 
        }
        $group[2] = User::WhereIn('ref_id',$id2)->get();
        foreach ($group[2] as $key => $value) {
             $id3[] = $value->id; 
        }
        $group[3] = User::WhereIn('ref_id',$id3)->get();
        foreach ($group[3] as $key => $value) {
             $id4[] = $value->id; 
        }
        $group[4] = User::WhereIn('ref_id',$id4)->get();
        foreach ($group[4] as $key => $value) {
             $id5[] = $value->id; 
        }
        $group[5] = User::WhereIn('ref_id',$id5)->get();
        return $group;
    } 

    public function position(){
        $data = $this->position_by_ref;
        if($data == null){
            return '<span class="badge rounded-pill badge-secondary">not-set</span>';
        }elseif($data==1){
            return 'Kiri';
        }else{
            return 'Kanan';
        }
    }
    public function positionUpline(){
        $data = $this->position;
        if($data == null){
            return '<span class="badge rounded-pill badge-secondary">not-set</span>';
        }elseif($data==1){
            return 'Kiri';
        }else{
            return 'Kanan';
        }
    }

}
