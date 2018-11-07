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
                $tel=Auth::user()->tel;
                $str=111;
                $this->note($tel,$str);
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
        $id=$_GET['id'];
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

    public function note($tel,$str){
        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIj3HeUkqPbIJr";
        $accessKeySecret = "F6rD2vmIoUSWjByRgIaibhgahKWPu3";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "苏木情深";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_150172411";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "product" => $str,
        );

        // fixme 可选: 设置发送短信流水号
        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new \App\Models\SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            )),
            $security
        );
    }
}
