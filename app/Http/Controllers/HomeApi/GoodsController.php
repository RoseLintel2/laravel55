<?php

namespace App\Http\Controllers\HomeApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Goods;
use App\Model\GoodsGallery;
use App\Tools\ToolsOss;

class GoodsController extends Controller
{
    //商品信息接口
    

    public function getGods(Request $request)
    {

    	$params = $request->all();

    	//接口返回格式
    	$return = [
    		'code' => 2000,
    		'msg'  => '获取商品数据成功'
    	];

    	//判断限制条数是否存在
    	if(!isset($params['num']) || empty($params['num'])){
    		$return = [
	    		'code' => 4000,
	    		'msg'  => '限制条数不能为空'
    		];

    		return json_encode($return);
    	}

    	
    	if(isset($params['is_recommand']) && !empty($params['is_recommand'])){
    	

    		//判断如果输入的参数是推荐商品
    		
    		$sql = \DB::table('jy_goods')->select('jy_goods.id','jy_goods.goods_name','jy_goods.market_price','jy_goods_gallery.image_url')
    							->leftJoin('jy_goods_gallery','jy_goods.id','=','jy_goods_gallery.goods_id')
    							->where('is_shop',1)
    							->where('is_recommand',$params['is_recommand'])
    							->limit($params['num'])
    							->get()
    							->toArray();
    		

    	}elseif(isset($params['is_new']) && !empty($params['is_new'])){
    	

    		//判断如果输入的参数是最新商品

    		$sql = \DB::table('jy_goods')->select('jy_goods.id','jy_goods.goods_name','jy_goods.market_price','jy_goods_gallery.image_url')
    							->leftJoin('jy_goods_gallery','jy_goods.id','=','jy_goods_gallery.goods_id')
    							->where('is_shop',1)
    							->where('is_new',$params['is_new'])
    							->limit($params['num'])
    							->get()
    							->toArray();
    	}else{

    		//判断如果输入的参数是热销商品
    		
    		//判断限制热销是否存在
	    	if(!isset($params['is_hot']) || empty($params['is_hot'])){
	    		$return = [
		    		'code' => 4001,
		    		'msg'  => '参数不全'
	    		];

	    		return json_encode($return);
	    	}
    		
    		$sql = \DB::table('jy_goods')->select('jy_goods.id','jy_goods.goods_name','jy_goods.market_price','jy_goods_gallery.image_url')
    							->leftJoin('jy_goods_gallery','jy_goods.id','=','jy_goods_gallery.goods_id')
    							->where('is_shop',1)
    							->where('is_hot',$params['is_hot'])
    							->limit($params['num'])
    							->get()
    							->toArray();
    	}


	    $return['data'] = $sql;

    	return json_encode($return);

    }

    
}
