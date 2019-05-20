<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Events\Event;

class TestController extends Controller
{
    //
    
	//测试事件机制页面
    public function add()
    {
    	return view('admin.test.test');
    }

    //测试事件机制
    public function store()
    {
    	
    	
    	//测试一下事件系统机制的调用
    	// event(new Event(['user_id' => 1]));

    }

    
}
