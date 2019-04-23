<?php

namespace App\Http\Controllers\HomeApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Tools\ToolsAdmin;

class CategoryController extends Controller
{
    //首页类型
    
	public function getlist(Request $request)
	{
		$return = [
			'code' => 2000,
			'msg'  => '成功'
 		];

 		
 		$data = Category::getCategory();

 		$sql = ToolsAdmin::buildTree($data,$fid=0,$parent_id="f_id");
 		

 		$return['data'] = $sql;

 		return json_encode($return);

	}

}
