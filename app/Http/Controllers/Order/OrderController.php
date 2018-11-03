<?php

namespace App\Http\Controllers\Order;

use App\Models\Addresse;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //
    public function addorder(Request $request){
      // return $total;

       // DB::begiTransaction();
        DB::beginTransaction();
        try {


            $address_id=$request->address_id;
            $user_id=Auth::user()->id;
            $goods_id=DB::select("select goods_id from carts where user_id = ? ORDER BY goods_id DESC",[$user_id]);
            $goods_id=$goods_id[0]->goods_id;
            $shop_id=DB::select("select shop_id from menus where id = ?",[$goods_id]);
            $shop_id=$shop_id[0]->shop_id;
            $shop_id2=DB::select("select shop_id from users where id=?",[$shop_id]);
            $shop_id2=$shop_id2[0]->shop_id;
            $sn=date('YmdHi').rand(1000,9999);
            $address=Addresse::where('id',$address_id)->first();
            $carts=Cart::where('user_id',$user_id)->get();
            $goods='';
            $goods_price='';
            $price=[];
            $goods_ids=[];
            $amount=[];
            foreach($carts as $cart){
                if($goods!=$cart->goods_id){
                    $menu=Menu::where('id',$cart->goods_id)->first();
                    $goods_price=$menu->goods_price;
                }
                $goods_ids[]=$cart->goods_id;
                $amount[]=$cart->amount;
                $price[]=$goods_price * $cart->amount;
                $goods=$cart->goods_id;
            }
            $total=array_sum($price);

            $order=Order::create([
                "user_id"=> $user_id,
                "shop_id"=> $shop_id2,
                "sn"=> $sn,
                "province"=> $address->province,
                "city"=> $address->city,
                "county"=> $address->county,
                "address"=> $address->address,
                "tel"=> $address->tel,
                "name"=> $address->name,
                "total"=> $total,
                "status"=> 0,
                "out_trade_no"=> rand(100000,999999),
            ]);

           // $order2=[];
            foreach($carts as $cart){
                $goods=Menu::where('shop_id',$shop_id)->where('id',$cart->goods_id)->first();
               // return $goods;
                $order2=OrderDetail::create([
                        'order_id'=>$order->id,
                        'goods_id'=>$cart->goods_id,
                        'amount'=>$cart->amount,
                        'goods_name'=>$goods->goods_name,
                        'goods_img'=>$goods->goods_img,
                        'goods_price'=>$goods->goods_price,
                ]);

            }

            if($order && $order2){
                DB::commit();
                DB::table("carts")->where('user_id',$user_id)->delete();

               return  ["status"=> "true", "message"=> "添加成功","order_id"=>$order->id];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return  ["status"=> "false", "message"=> "添加失败","order_id"=>1];
        }

    }

//    public function order(){
//        //$id=$_GET['id'];
//        $id=3;
//        $orders=Order::where('id',$id)->get();
//        return $orders;
//    }

    public function orderList(){
        $user_id=Auth::user()->id;
        //$user_id=3;
       // return $user_id;
        $orders=Order::where('user_id',$user_id)->get();
        $data=[];
        foreach($orders as $order){
            $shop=Shop::where('id',$order->shop_id)->first();
           // return $order->shop_id;
            if($order->status==0){
                $status='代付款';
            }else{
                $status='代发货';
            }
            $details=OrderDetail::where('order_id',$order->id)->get();
            $list=[];
            foreach($details as $detail){
                $list[]=[
                    "goods_id"=> $detail->goods_id,
                    "goods_name"=> $detail->goods_name,
                    "goods_img"=> $detail->goods_img,
                    "amount"=> $detail->amount,
                    "goods_price"=> $detail->goods_price
                ];
            }



            $data[]=[
                "id"=> $order->id,
                "order_code"=> time().rand('100',999),
                "order_birth_time"=> $order->created_at->toDateTimeString(),
                "order_status"=> $status,
                "shop_id"=>  $order->shop_id,
                "shop_name"=>  $shop->shop_name,
                "shop_img"=> $shop->shop_img,
                "goods_list"=>$list,
                "order_price"=>  $order->total,
                "order_address"=>  $order->address
            ];
        }

        return $data;
    }

    public function order(){
       // $id=$_GET['id'];
        $id=31;
        //$user_id=3;
        // return $user_id;
        $order=Order::where('id',$id)->first();
        $shop=Shop::where('id',$order->shop_id)->first();
            // return $order->shop_id;
            if($order->status==0){
                $status='代付款';
            }else{
                $status='代发货';
            }
            $details=OrderDetail::where('order_id',$order->id)->get();
            $list=[];
            foreach($details as $detail){
                $list[]=[
                    "goods_id"=> $detail->goods_id,
                    "goods_name"=> $detail->goods_name,
                    "goods_img"=> $detail->goods_img,
                    "amount"=> $detail->amount,
                    "goods_price"=> $detail->goods_price
                ];
            }
            $data=[
                "id"=> $order->id,
                "order_code"=> time().rand('100',999),
                "order_birth_time"=> $order->created_at->toDateTimeString(),
                "order_status"=> $status,
                "shop_id"=>  $order->shop_id,
                "shop_name"=>  $shop->shop_name,
                "shop_img"=> $shop->shop_img,
                "goods_list"=>$list,
                "order_price"=>  $order->total,
                "order_address"=>  $order->address
            ];


        return $data;
    }
}
