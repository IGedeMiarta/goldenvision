<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function products(){
        $page_title = 'Products';
        $empty_message = 'No Products found';
        $products = Product::where('is_custom','!=',1)->paginate(getPaginate());
        return view('admin.product.index', compact('page_title','products', 'empty_message'));
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

    public function productsStore(Request $request){
        $prod = new Product();
        $prod->name             = $request->name;
        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $filename = time() . '_image_' . strtolower(str_replace(" ", "",$prod->name)) . '.jpg';
            $path = './assets/images/product/';
            $imageSave = $path . $filename;

            $prod->image = '/assets/images/product/'.$filename;

            if (file_exists($imageSave)) {
                @unlink($imageSave);
            }

            $image->move($path,$filename);
        }

        $prod->price            = $request->price;
        $prod->weight           = $request->weight;
        $prod->details           = $request->details;

        $prod->stok             = $request->stok;
        $prod->status           = $request->status?1:0;
        $prod->is_reseller      = $request->reseller?1:0;
        $prod->save();

        $notify[] = ['success', 'New Plan created successfully'];
        return back()->withNotify($notify);
    }
    public function productsUpdate(Request $request){
        $prod                  = Product::find($request->id);
        $prod->name             = $request->name;
        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $filename = time() . '_image_' . strtolower(str_replace(" ", "",$prod->name)) . '.jpg';
            $path = './assets/images/product/';

            $imageSave = '/assets/images/product/'.$filename;

            $prod->image = $imageSave;
            if (file_exists($imageSave)) {
                @unlink($imageSave);
            }

            $image->move($path,$filename);
        }
        
        $prod->price            = $request->price;
        $prod->weight           = $request->weight;
        $prod->details          = $request->details;
        $prod->stok             = $request->stok;
        $prod->status           = $request->status?1:0;
        $prod->is_reseller      = $request->reseller?1:0;
        $prod->save();

        $notify[] = ['success', 'Product edited successfully'];
        return back()->withNotify($notify);
    }

    public function order(){
        $data['page_title'] = 'Product Order';
        $data['tables'] = ProductOrder::orderBy('status')->where('total_order','>',0)->paginate(10);
        return view('admin.product.order',$data); 
    }
    public function orderUp(Request $request){
        
        $order = ProductOrder::find($request->id);
        $user = User::find($order->user_id);
        if($request->action=='Reject'){
            
            $order->status = 4;
            $order->admin_feedback = $request->admin_feedback;
            
            $totalOrder = $order->total_order;
            $order->save();

            $log = new UserPoint();
            $log->user_id = $user->id;
            $log->point = $totalOrder;
            $log->type = '+';
            $log->start_point = $user->point;
            $log->end_point = $user->point + $totalOrder;
            $log->desc = 'Order Product Rejected  Invoice : #'  . $order->inv .' Return '. $totalOrder.' POINT'; 
            $log->save();
            
            $user->point += $totalOrder;
            $user->save();
            $notify[] = ['success', 'Rejected Order successfully'];
            return back()->withNotify($notify);
        }else{
            dd($request->all());

        }
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
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
