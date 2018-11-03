<?php

namespace App\Http\Controllers\User;

use App\Models\Member;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    //
    public function sms(){
        $tel=$_GET['tel'];
        $str=rand(1000,9999);
        Redis::setex('str_'.$tel,300,$str);// key:code_13312345678  1234

        $this->note($tel,$str);
        $data=[
            "status"=>"true",
            "message"=>"获取短信验证码成功"
        ];
        return  $data;
    }

    public function regist(Request $request){


        if(Redis::get('str_'.$request->tel)!=$request->sms){
            $data=["status"=> "false", "message"=> "短信验证失败"];
            return $data;
        }

        $menber=Member::create([
            'username' => $request->username,
            'tel' => $request->tel,
            'password' => bcrypt($request->password),
            'rememberToken' => str_random(50),
        ]);

        if($menber){
            $data=["status"=> "true", "message"=> "注册成功"];
        }else{
            $data=["status"=> "false", "message"=> "注册失败"];
        }
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
        $params["TemplateCode"] = "SMS_149097544";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => $str,
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

    public function login(Request $request){
        //return "q231";
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                "status" => "false",
                "message"=>"用户或密码不能为空",
            ];
        }


        if(Auth::attempt(['username'=>$request->name,'password'=>$request->password])){
            $user=Auth::user();
            $data = [
                "status" => "true",
                "message"=>"登录成功",
                "user_id"=>$user->id,
                "username"=>$user->username,
            ];
        }else{
            $data = [
                "status" => "false",
                "message"=>"登录失败",
            ];
        }
        return $data;
    }

    public function changePassword(Request $request){
        $password=Auth::user()->password;
        if(!Hash::check($request->oldPassword,$password)){
            $data = [
                "status" => "false",
                "message"=>"原密码错误",
            ];
        }

        $id=Auth::user()->id;
        $password=bcrypt($request->newPassword);
        $update=DB::update("update members set password='{$password}' where id=? ",[$id]);
        if($update){
            $data = [
                "status" => "true",
                "message"=>"修改成功",
            ];
        }else{
            $data = [
                "status" => "false",
                "message"=>"修改失败",
            ];
        }
        return $data;
    }

    public function forgetPassword(Request $request){
        if(Redis::get('str_'.$request->tel)!=$request->sms){
            $data=["status"=> "false", "message"=> "短信验证失败"];
            return $data;
        }

        $password=bcrypt($request->password);

        $update=DB::update("update members set password='{$password}' where tel=? ",[$request->tel]);
        if($update){
            $data = [
                "status" => "true",
                "message"=>"重置成功",
            ];
        }else{
            $data = [
                "status" => "false",
                "message"=>"重置失败",
            ];
        }
        return $data;
    }
}
