<?php

namespace App\Http\Controllers\HomeApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tools\ToolsOss;

class AdController extends Controller
{

	
	
	/**
	 * 前台广告位置的接口
	 * @param  int   num        限制显示的条数
	 * @return int   position   限制展示的位置  
	 */
    public function list(Request $request)
    {
    	
    	$num = $request->input('num');

    	$position = $request->input('position');

    	//成功返回的格式
    	$return = [
    		'code' => 2000,
    		'msg'  => '成功'
    	];

    	//判断参数是否传递
    	if(empty($num) || empty($position)){

    		$return = [
	    		'code' => 4000,
	    		'msg'  => '参数不能为空'
    		];

    		return json_encode($return);
    	}

    	// 如果参数输入正确,查询数据
    	$sql = \DB::table('jy_ad')->select('ad_name','image_url','ad_link')
    					->where('position_id',$position)
    					->where('status',1)
    					->limit($num)
    					->get()
    					->toArray();

        $oss = new ToolsOss();
        
        //处理图片对象
        foreach ($sql as $key => $value) {
            
            $value->image_url  = $oss->getUrl($value->image_url,true);
            $sql[$key] = $value;
        }

        
    	//如果position输入错误，未找到对应数据
    	if(empty($sql)){

    		$return = [
	    		'code' => 4001,
	    		'msg'  => '广告位置未找到'
    		];

    		return json_encode($return);
    	}

    	$return['data'] = $sql;

    	//返回接口数据
    	return json_encode($return);

    }


    
}
