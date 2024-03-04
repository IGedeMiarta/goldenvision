<?php

namespace App\Http\Controllers\Admin;

use App\Models\brodev;
use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use Illuminate\Http\Request;

class BrodevController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     //
    // }

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\brodev  $brodev
     * @return \Illuminate\Http\Response
     */
    public function show(brodev $brodev)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\brodev  $brodev
     * @return \Illuminate\Http\Response
     */
    public function edit(brodev $brodev)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\brodev  $brodev
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, brodev $brodev)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\brodev  $brodev
     * @return \Illuminate\Http\Response
     */
    public function destroy(brodev $brodev)
    {
        //
    }

    public function index(){
        $page_title = 'MP Package Delivery';
        $empty_message = "MP Package Delivery Request Not Found!";
        $items = brodev::orderBy('created_at','DESC')
        ->paginate(getPaginate());
        $order = ProductOrder::with('user','detail','detail.product')->orderBy('created_at','DESC')
        ->paginate(getPaginate());
        return view('admin.delivery.BroDelivery',compact('page_title','items','order','empty_message'));
    }

    public function delivery(Request $request){
        // dd($request->all());
        $sg = brodev::where('id',$request->id)->first();
        $sg->no_resi = $request->no_resi;
        $sg->status = 1;
        $sg->save();

        $notify[] = ['success', 'MP Package Deliver successfully'];
        return back()->withNotify($notify);
    }
}
