<?php

namespace App\Http\Controllers\HomeApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Goods;
use App\Model\GoodsGallery;
use App\Tools\ToolsOss;
use App\Model\GoodsSku;

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


    /**
     * 商品详情接口
     * @param  int $id 商品的id
     * @return json
     */
    public function goodsInfo($id)
    {
        //接口返回格式
        $return = [
            'code' => 2000,
            'msg'  => '获取商品详情成功'
        ];

        //获取商品详情数据
        $goods = new Goods();

        $goodsInfo = $this->getDataInfo($goods, $id)->toArray();

        //获取对应商品相册信息
        $GoodsGallery = new GoodsGallery();

        $gallers = $this->getLists($GoodsGallery , ['goods_id' => $id]);
        $img = [];
        foreach ($gallers as $key => $value) {
            $img[] = 'http://www.laravel55.com'.$value['image_url'];

            foreach ($img as $k => $v) {

                $value['image_url'] = $v;
            }

            $gallers[$key]['image_url'] = $value['image_url'];
            
        }

        // dd($gallers);

        

        //获取商品的sku的属性值
        $goodsSku = new GoodsSku();

        $spu = $goodsSku->getSpuHandle($id);

        $sku = $goodsSku->getSkuList($id);

        $sku_data= [];

        //组装前台的sku数据
        foreach ($sku as $k => $value) {
            //如果不存在
            if(!isset($sku_data[$value['attr_id']])){
                
                $sku_data[$value['attr_id']] = [
                    'attr_name' => $value['attr_name'],
                    'attr_sku'  => [

                        [
                            'sku_id' => $value['id'],
                            'sku_value' => $value['sku_value'],
                            'attr_price' => $value['attr_price']

                        ]

                    ]
                ];
            }else{
            //如果存在了
                $sku_data[$value['attr_id']]['attr_sku'][] = [

                    'sku_id' => $value['id'],
                    'sku_value' => $value['sku_value'],
                    'attr_price' => $value['attr_price']
                ];

            }

        }

        $return['data'] = [
            'goods'  => $goodsInfo,
            'gallers' => $gallers,
            'spu'     => $spu,
            'sku'     => $sku_data
        ];

        return json_encode($return);
    }

    //获取商品sku属性的列表信息
    public function getGoodsAttr(Request $request)
    {
        //传过来的sku的ids
        $sku_ids = $request->input('sku_ids');

        $sku_ids = explode(',', $sku_ids);

        $sku = \DB::table('jy_goods_sku')->select('attr_id','sku_value')->whereIn('attr_id',$sku_ids)->get();
        // dd($sku);
        $skuData = [];
        foreach ($sku as $key => $value) {

            //获取属性名
            $attr = \DB::table('jy_goods_attr')->select('attr_name')->where('id',$value->attr_id)->first();
            // dd($attr,$value->sku_value,$attr->attr_name);

            $skuData[$key]['sku_value'] = $value->sku_value;
            $skuData[$key]['attr_name'] = $attr->attr_name;
        }
        // dd($skuData);
        return json_encode($skuData);
    }

    
}
