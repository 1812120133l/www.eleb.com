<?php

namespace App\Http\Controllers\Shop;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Shop;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    //
    public function list()
    {
        $shops = Shop::all();
        $datas = [];
        foreach ($shops as $shop) {
            $data = [
                "id" => $shop->id,
                "shop_name" => $shop->shop_name,
                "shop_img" => $shop->shop_img,
                "shop_rating" => $shop->shop_rating,
                "brand" => $shop->brand,
                "on_time" => $shop->id,
                "fengniao" => $shop->fengniao,
                "bao" => $shop->bao,
                "piao" => $shop->piao,
                "zhun" => $shop->zhun,
                "start_send" => $shop->start_send,
                "send_cost" => $shop->send_cost,
                "distance" => $shop->discount,
                "estimate_time" => '30',
                "notice" => $shop->notice,
                "discount" => $shop->discount,
            ];
            $datas[] = $data;
        }
        return $datas;
    }

    public function menus(){

        $id = $_GET['id'];
        $shops = Shop::where('id', $id)->get();
        $datas = "";
        $commodities = [];

        foreach ($shops as $shop) {
            $shop_id = User::where('shop_id', $shop->id)->first();
            if ($shop_id->id) {
                $categories = MenuCategory::where('shop_id', $shop_id->id)->get();
                foreach ($categories as $category) {
                    if ($category->id) {
                        $goods = Menu::where('category_id', $category->id)->get();
                        //dd($goods);
                        $goods_list = [];
                        foreach ($goods as $good) {
                            if($category->id==$good->category_id){
                                $goodes = [
                                    "goods_id" => $good->id,
                                    "goods_name" => $good->goods_name,
                                    "rating" => $good->rating,
                                    "goods_price" => $good->goods_price,
                                    "description" => $good->description,
                                    "month_sales" => $good->month_sales,
                                    "rating_count" => $good->rating_count,
                                    "tips" => $good->tips,
                                    "satisfy_count" => $good->satisfy_count,
                                    "satisfy_rate" => $good->satisfy_rate,
                                    "goods_img" => $good->goods_img
                                ];
                            }

                            $goods_list[] = $goodes;
                        }
                    }
                    $commodity= [
                        "description" => $category->description,
                        "is_selected" => true,
                        "name" => $category->name,
                        "type_accumulation" => $category->type_accumulation,
                        "goods_list" => $goods_list
                    ];
                    $commodities[] = $commodity;
                }
            }
            $data = [
                "id" => $shop->id,
                "shop_name" => $shop->shop_name,
                "shop_img" => $shop->shop_img,
                "shop_rating" => $shop->shop_rating,
                "brand" => $shop->brand,
                "on_time" => $shop->id,
                "fengniao" => $shop->fengniao,
                "bao" => $shop->bao,
                "piao" => $shop->piao,
                "zhun" => $shop->zhun,
                "start_send" => $shop->start_send,
                "send_cost" => $shop->send_cost,
                "distance" => $shop->discount,
                "estimate_time" => '30',
                "notice" => $shop->notice,
                "discount" => $shop->discount,
                "evaluate" => [
                    "user_id" => '12344',
                    "username" => "w******k",
                    "user_img" => "/images/slider-pic4.jpeg",
                    "time" => "2017-2-22",
                    "evaluate_code" => '1',
                    "send_time" => '30',
                    "evaluate_details" => "不怎么好吃"
                ],
                "commodity" => $commodities,
            ];
            $datas = $data;
        }
        return $datas;
    }

}
