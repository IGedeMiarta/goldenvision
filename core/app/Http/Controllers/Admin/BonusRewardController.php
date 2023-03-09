<?php

namespace App\Http\Controllers\Admin;

use App\Models\corder;
use App\Http\Controllers\Controller;
use App\Models\BonusReward;
use App\Models\GeneralSetting;
use App\Models\Gold;
use App\Models\Product;
use App\Models\ureward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;

class BonusRewardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Bonus Rewards';
        $empty_message = 'No Bonus Rewards Found';
        $table = BonusReward::all()->paginate(getPaginate());
        return view('admin.bonus_reward.index', compact('page_title','table', 'empty_message'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $prod = new BonusReward();
        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $filename = time() . '_image_' . strtolower(str_replace(" ", "",$prod->name)) . '.jpg';
            $location = 'assets/images/reward/' . $filename;
            $prod->images = $filename;

            $path = './assets/images/reward/';

            $link = $path . $prod->image;
            if (file_exists($link)) {
                @unlink($link);
            }
            // $size = imagePath()['product']['size'];
            $image = Image::make($image);
            // $size = explode('x', strtolower($size));
            // $image->crop($size[0], $size[1]);
            $image->save($location);
        }

        // dd($request->file('images'));
        $prod->kiri            = $request->kiri;
        $prod->kanan           = $request->kanan;
        $prod->reward           = $request->bonus;
        $prod->save();

        $notify[] = ['success', 'New Reward created successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\corder  $corder
     * @return \Illuminate\Http\Response
     */
    public function show(corder $corder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\corder  $corder
     * @return \Illuminate\Http\Response
     */
    public function edit(corder $corder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\corder  $corder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, corder $corder)
    {
        //
    }
    public function upreward(Request $request)
    {
        //
        // dd($request->all());
        $prod = BonusReward::find($request->id);
        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $filename = time() . '_image_' . strtolower(str_replace(" ", "",$prod->name)) . '.jpg';
            $location = 'assets/images/reward/' . $filename;
            $prod->images = $filename;

            $path = './assets/images/reward/';

            $link = $path . $prod->image;
            if (file_exists($link)) {
                @unlink($link);
            }
            // $size = imagePath()['product']['size'];
            $image = Image::make($image);
            // $size = explode('x', strtolower($size));
            // $image->crop($size[0], $size[1]);
            $image->save($location);
        }

        // dd($request->file('images'));
        $prod->kiri            = $request->kiri;
        $prod->kanan           = $request->kanan;
        $prod->reward           = $request->bonus;
        $prod->save();

        $notify[] = ['success', 'New Reward updated successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\corder  $corder
     * @return \Illuminate\Http\Response
     */
    public function destroy(corder $corder)
    {
        //
    }

    public function UserBonus(){
        $page_title = 'User Rewards';
        $empty_message = 'No User Rewards Found';
        $table = ureward::all()->paginate(getPaginate());
        return view('admin.bonus_reward.user', compact('page_title','table', 'empty_message'));
    }

    public function UserUpdate(Request $request){
        ureward::find($request->id)->update([
            'ket'       => $request->ket,
            'status'    => $request->status
        ]);
        $notify[] = ['success', 'Data Updated'];
        return back()->withNotify($notify);
    }
}
