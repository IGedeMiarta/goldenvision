<?php

namespace App\Http\Controllers;

use App\Models\corder;
use App\Models\GeneralSetting;
use App\Models\Gold;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductOrderDetail;
use App\Models\User;
use App\Models\UserChart;
use App\Models\UserExtra;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
    public function productIndex(){
        $data['page_title'] = "Reedem Product";
        $data['product'] = Product::where('status',1)->get();
        $data['cart'] = UserChart::with('product')->where('user_id',auth()->user()->id)->get();
        return view('templates.basic.user.product.index',$data);
    }
    public function productCart(Request $request){
        $cart = UserChart::where(['product_id'=> $request->product_id,'user_id'=>Auth::user()->id])->first();
        if($cart){
            $cart->qty += $request->qty;
            $cart->save();
        }else{
            $cart = new UserChart();
            $cart->user_id = Auth::user()->id;
            $cart->product_id = $request->product_id;
            $cart->qty = $request->qty;
            $cart->save();
        }
        $notify[] = ['success', 'Product Add to Cart'];
        return back()->withNotify($notify);
    }
    public function productCartUpdate(Request $request, $id){
        $cart = UserChart::find($id);
        $cart->qty = $request->qty;
        $cart->save();
        // $notify[] = ['success', 'Product Qty Updated'];
        // return back()->withNotify($notify);
        return $cart;
    }

    public function productPurchase(Request $request){
        $cart = UserChart::where('user_id',auth()->user()->id)->get();
        $total = 0;
        foreach ($cart as $value) {
            $total += $value->product->price  * $value->qty;
        }
        $gnl = GeneralSetting::first();

        $user = User::find(Auth::id());
        if ($user->point < $total) {
            $notify[] = ['error', 'Insufficient Point Balance'];
            return back()->withNotify($notify);
        }
        DB::beginTransaction();
        try {
            $order = new ProductOrder();
            $order->user_id = $user->id;
            $order->inv = 'INV'.time();
            $order->total_order =  $total;
            $order->status = 1;
            $order->admin_feedback = null;
            $order->save();

            foreach ($cart as $i => $value) {
                $product = product::where('id', $value->product_id)->where('status', 1)->firstOrFail();
                if ($product->stok == 0 || ($product->stok - $value->qty) < 1) {
                    $notify[] = ['error', 'Out Of Stock'];
                    return back()->withNotify($notify);
                }

                if ($product->stok < $value->qty) {
                    $notify[] = ['error', 'the number of qty you input exceeds the available stock'];
                    return back()->withNotify($notify);
                }
                $detail = new  ProductOrderDetail();
                $detail->order_id = $order->id;
                $detail->product_id = $product->id;
                $detail->qty        = $value->qty;
                $detail->total      = $product->price * $value->qty;
                $detail->save();
                $product->stock -= $value->qty;
            }
            
            $log = new UserPoint();
            $log->user_id = $user->id;
            $log->point = $total;
            $log->type = '-';
            $log->start_point = $user->point;
            $log->end_point = $user->point - $total;
            $log->desc = 'User Transaction Product with ' . $total . ' POINT. Invoice : #'  . $order->inv;
            $log->save();
            
            $user->point -= $total;
            $user->save();


            UserChart::where('user_id',$user->id)->delete();

            DB::commit();

            $notify[] = ['success', 'Purchased Product Successfully'];
            return redirect()->route('user.product.inv')->withNotify($notify);
        } catch (\Throwable $th) {
           DB::rollBack();
            $notify[] = ['success', 'Error:' . $th->getMessage() ];
            return redirect()->route('user.home')->withNotify($notify);
        }

        
    }
    public function productInvoice(){
        $data['page_title'] = "Invoice";
        $data['inv'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->get();
        $data['wait'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->where('status',1)->count();
        $data['deliver'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->where('status',2)->count();
        $data['accept'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->where('status',3)->count();
        $data['reject'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->where('status',4)->count();
        return view('templates.basic.user.product.inv',$data);
    }
    public function productTracking(){
        $data['page_title'] = "Tracking Product";
        $data['inv'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->get();
        $data['wait'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->where('status',1)->count();
        $data['deliver'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->where('status',2)->count();
        $data['accept'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->where('status',3)->count();
        $data['reject'] = ProductOrder::with('detail','detail.product')->where('user_id',auth()->user()->id)->where('status',4)->count();
        return view('templates.basic.user.product.tracking',$data);
    }
}