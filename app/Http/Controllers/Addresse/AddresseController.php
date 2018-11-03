<?php

namespace App\Http\Controllers\Addresse;

use App\Models\Addresse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class AddresseController extends Controller
{
    //

    public function addAddress(Request $request){

        $user=Auth::user();
        //return  $request->dd;
        $addresse=Addresse::create([
            'user_id' => $user->id,
            'province' => $request->provence,
            'city' => $request->city,
            'county' => $request->area,
            'address' => $request->detail_address,
            'tel' => $request->tel,
            'name' => $request->name,
            'is_default' => 1,
        ]);

        if($addresse){
            $data=["status"=> "true", "message"=> "添加成功"];
        }else{
            $data=["status"=> "false", "message"=> "添加成功"];
        }

        return $data;
    }

    public function addressList(){
        $id=Auth::user()->id;
        //return $id;
        $address=Addresse::where('user_id',$id)->get();
        $data=[];
        foreach($address as $addres){
            $data[]=[
                "id"=> $addres->id,
                "provence"=> $addres->province,
                "city"=> $addres->city,
                "area"=> $addres->county,
                "detail_address"=> $addres->address,
                "name"=> $addres->name,
                "tel"=> $addres->tel
            ];
        }
        return $data;
    }

    public function address(){
        $id=$_GET['id'];

        $row=Addresse::where('id',$id)->first();
            $data=[
                "id"=> "$row->id",
                "provence"=> $row->province,
                "city"=> $row->city,
                "area"=> $row->county,
                "detail_address"=> $row->address,
                "name"=> $row->name,
                "tel"=> $row->tel
            ];
        return $data;
    }

    public function editAddress(Request $request){

        DB::update("update addresses set province='{$request->provence}',city='{$request->city}',county='{$request->area}',address='{$request->detail_address}',tel='{$request->tel}',name='{$request->name}' where id=?",[$request->id]);


        return ["status" => "true", "message"=> "修改成功"];
    }
}
