<?php

namespace App\Http\Controllers\HomeApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tools\ToolsSms;
use App\Model\Member;
use App\Model\MemberInfo;
use App\Events\Event;

class LoginController extends Controller
{
    

    // 发送验证码
    
    public function sendSms(Request $request)
    {

    	//接收phone参数
    	$phone = $request->input('phone');

    	//接口返回的格式
    	$return = [
    		'code' => 2000,
    		'msg'  => '发送短信成功'
    	];


    	//判断参数是否传递
    	if(!isset($phone) || empty($phone))
    	{
    		$return = [
	    		'code' => 4001,
	    		'msg'  => '手机号不能为空'
    		];

    		return json_encode($return);
    	}

    	//验证手机号格式
    	if(!preg_match("/^1[34578]\d{9}$/", $phone)){

    		$return =[
    			'code' =>4002,
    			'msg'  => "手机号格式不正确"
    		];

    		return json_encode($return);
    	}

    	//实例化redis
    	$redis = new \Redis();

    	//连接redis
    	$redis->connect('127.0.0.1', 6379);

    	//校验手机号发送的次数
    	//当前手机号已经发送过得短信验证码的次数key
    	$key1 = $phone."_NUMS";

    	$nums = $redis->get($key1);

    	if($nums >=3){

    		$return =[
    			'code' =>4003,
    			'msg'  => "今天短信发送次数已经到达上限,请明天再来",
    		];

    		return json_encode($return);
    	}

    	//生成手机号认证码
    	$code = rand(10000,999999);

    	//存储认证码的key
    	$key  = "REGISTER_".$phone."_CODE";

    	// 记录日志
    	\Log::info('手机号'.$phone."发的的短信认证码：".$code);

    	//设置redis值
    	$redis->setex($key,1800,$code);


    	//发送短信认证码
    	$res = ToolsSms::sendSms($phone,$code);

    	//短信发送失败
    	if(!$res['status']){

    			$return = [
    				'code' => 4004,
    				'msg'  => $res['msg']
    			];

    			return json_encode($return);
    	}

    	//给用户短信发送次数自增一次
    	$redis->incr($key1);

    	//设置过期时间
    	$redis->expire($key,24*3600);


    	return  json_encode($return);

    }

    //用户注册的功能
    public function register(Request $request)
    {

    	$params = $request->all();

    	$return = [
    			'code' => 2000,
    			'msg'  => '短信发送成功'
    	];

    	//判断参数是否传递
    	if(!isset($params['phone']) || empty($params['phone']))
    	{
    		$return = [
	    		'code' => 4001,
	    		'msg'  => '手机号不能为空'
    		];

    		return json_encode($return);
    	}

    	//判断参数是否传递
    	if(!isset($params['password']) || empty($params['password']))
    	{
    		$return = [
	    		'code' => 4001,
	    		'msg'  => '密码不能为空'
    		];

    		return json_encode($return);
    	}

    	//判断参数是否传递
    	if(!isset($params['code']) || empty($params['code']))
    	{
    		$return = [
	    		'code' => 4001,
	    		'msg'  => '认证码不能为空'
    		];

    		return json_encode($return);
    	}

    	//实例化redis
    	$redis = new \Redis();

    	//连接redis
    	
    	$redis->connect("127.0.0.1",6379);

    	//获取缓存存储的短信验证码的值
    	$code = $redis->get("REGISTER_".$params['phone']."_CODE");


    	if($code != $params['code']){

    		$return = [
    				'code' =>4000,
    				'msg'  => '认证码错误，请重新输入'
    		];

    		return json_encode($return);
    	}

    	//删除认证码
    	$redis->del("REGISTER_".$params['phone']."_CODE");

    	//用户注册的功能
    	
    	try{

    		//开启事物
    		\DB::beginTransaction();

    		//添加到user主表信息
    		$member = new member();

    		$data = [

    			'phone' => $params['phone'],
    			'password' => md5($params['password'])
    		];

    		$userId = $this->addDataBackId($member,$data);

    		//添加suer_info表信息
    		$memberIndo = new MemberInfo();

    		$data1 = [
    			'user_id' => $userId,
    			'invite_code' => rand(100000,999999)
    		];
    		$this->storeData($memberIndo,$data1);

            

    		\DB::commit();
    	}catch(\Exception $e){
    		\DB::rollback();

    		\Log::error('用户注册失败'.$e->getMessage());

    		$return = [
    				'code' => $e->getCode(),
    				'msg'  => $e->getMessage()
    		];
    	}
        //测试一下事件系统机制的调用
        event(new Event(['id' => $userId]));

    	return json_encode($return);
    }



    //登录接口
    public function login(Request $request)
    {

    	$params = $request->all();

    	//接口返回格式
    	$return = [
    		'code' => 2000,
    		'msg'  => '登录成功'
    	];

    	//判断用户名是否存在
    	if(!isset($params['phone']) || empty($params['phone'])){

    		$return = [
    			'code' => 4000,
    			'msg'  => '手机号不能为空'
    		];

    		return json_encode($return);
    	}

    	//判断密码是否存在
    	if(!isset($params['password']) || empty($params['password'])){

    		$return = [
    			'code' => 4001,
    			'msg'  => '密码不能为空'
    		];

    		return json_encode($return);
    	}


    	//判断用户是否存在
    	

    	//实例化用户表
    	$user = new Member();

    	//根据用户名查询用户是否存在
    	$res = $this->getDataInfoByWhere($user,['phone' => $params['phone']]);
        
    	if(empty($res)){

    		$return = [
    			'code' => 4003,
    			'msg'  => '手机号不存在'
    		];

    		return json_encode($return);
    	}else{

    	}


    	//判断密码是否正确
    	if(md5($params['password']) != $res->password){


	    		$return = [
	    			'code' => 4004,
	    			'msg'  => '密码错误'
	    		];

	    		return json_encode($return);
    	}

    	//实例化redis
    	$redis = new \Redis();

    	//连接redis
    	$redis->connect('127.0.0.1',6379);

    	//生成token
    	$token = \DB::select('select replace(uuid(),"-","") as token');

    	$token2 = $token[0]->token;

		//把生成的token存入redis
    	$redis->setex($token2, 7200, $params['phone']);

        //把token值返回给用户
        $return['data'] = $token;


        return json_encode($return);
    	
    }



}
