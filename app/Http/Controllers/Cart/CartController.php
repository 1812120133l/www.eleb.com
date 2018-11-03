<?php

namespace App\Http\Controllers\Cart;

use App\Models\Cart;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    //
    public function addCart(Request $request){
        $goodsLists=$request->goodsList;
        $goodsCounts=$request->goodsCount;

        $user_id=Auth::user()->id;
        $user_goods=Cart::where('user_id',$user_id)->get();

        for($i=0;$i<count($goodsLists);$i++) {
            if (!Cart::where('goods_id', $goodsLists[$i])->where('user_id', $user_id)->first()) {
                Cart::create([
                    'user_id' => $user_id,
                    'goods_id' => $goodsLists[$i],
                    'amount' => $goodsCounts[$i],
                ]);
            } else {
                foreach ($user_goods as $goods) {
                    if ($goods->goods_id == $goodsLists[$i]) {
                        DB::update("update carts set amount=$goods->amount+$goodsCounts[$i] where goods_id=?", [$goods->goods_id]);
                    }
                }

            }
        }
        return  [
            "status"=> "true",
            "message"=> "添加成功"];

    }

    public function cart(){
        $id=Auth::user()->id;
        $rows=DB::select("select * from carts where user_id = ?",[$id]);

        $goods_list=[];
        $Price=[];
        foreach($rows as $row){

            $goods=Menu::where('id',$row->goods_id)->first();
            //return $goods;
            $goods_list[]=[
                "goods_id"=> $goods->id,
                "goods_name"=> $goods->goods_name,
                "goods_img"=> $goods->goods_img,
                "amount"=> $row->amount,
                "goods_price"=> $goods->goods_price
            ];
            $Price[]=$row->amount * $goods->goods_price;
        }
        //return $goods_list;

        $data=[
            "goods_list"=>$goods_list,
            "totalCost"=> array_sum($Price)
        ];

        return $data;

    }

}
