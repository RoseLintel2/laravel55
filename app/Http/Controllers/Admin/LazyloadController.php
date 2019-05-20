<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LazyloadController extends Controller
{
    //测试图片懒加载
    

    public function lists()
    {


    	return view('/admin/lazyload/lazyload');
    }
}
