<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const
        PAGE_SIZE = 2,
        END        = true;



    //删除token值
    public function delToken(array  $params)
    {
        if(!isset($params['_token'])){
            return false;
        }

        unset($params['_token']);

        return $params;
    }

    //保存操作，可用于添加和修改
    public function storeData($object,$params)
    {
        if(empty($params)){
            return false;
        }

        foreach($params as $key => $value){
            $object->$key = $value;
        }

        return $object->save();
    }

    //删除操作
    public function delRecord($object,$id,$key="id")
    {
        return $object->where($key,$id)->delete();
    }

    //获取单条数据
    public function getDataInfo($object,$id,$key="id")
    {
        if(empty($id)){
            return false;
        }

        return $object->where($key,$id)->first();
    }

    //无分页的数据列表
    public function getLists($object,$where=[])
    {
            return $object->where($where)->get()->toArray();
    }

    //修改
    public function updateDataInfo($object,$params,$id,$key="id")
    {
        return $object->where($key,$id)->update($params);
    }

    //带有分页的条件查询数据
    public function getListsInfo($object,$where=[])
    {
        return $object->where($where)->paginate(self::PAGE_SIZE);
    }

    //添加信息返回id
    public function addDataBackId($object,$params)
    {
        return $object->insertGetId($params);
    }

    //多条添加
    public function addDataMany($object,$params)
    {
        return $object->insert($params);
    }
}
