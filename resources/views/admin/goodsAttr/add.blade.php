@extends('comment.admin_base')

@section('title','管理后台-商品类型属性添加')

<!--页面顶部信息-->
@section('pageHeader')
    <div class="pageheader">
        <h2><i class="fa fa-home"></i> 商品类型属性添加 <span>Subtitle goes here...</span></h2>
        <div class="breadcrumb-wrapper">
        </div>
    </div>
@endsection

@section('content')
    @if(session('msg'))
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('msg') }}
        </div>
    @endif
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <span id="error_msg"></span>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns">
                <a href="" class="panel-close">&times;</a>
                <a href="" class="minimize">&minus;</a>
            </div>

            <h4 class="panel-title">商品类型表单</h4>
        </div>
        <div class="panel-body panel-body-nopadding" id="attr">

            <form class="form-horizontal form-bordered" action="/admin/goods/attr/doAdd" method="post">
                {{csrf_field()}}

                <div class="form-group">
                    <label class="col-sm-3 control-label">属性名字</label>
                    <div class="col-sm-6">
                        <input type="text" placeholder="属性名字" class="form-control" name="attr_name" value="" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">所属商品类型</label>
                    <div class="col-sm-6">
                        <select class="form-control" name="cate_id">
                            @if(!empty($list))
                                @foreach($list as $v)
                                    <option value="{{$v['id']}}">{{$v['type_name']}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">输入方式</label>
                    <div class="col-sm-6">
                        <div class="radio"><label><input @click="changeType" data-id="1" type="radio" name="input_type" value="1" checked> 手动录入</label></div>
                        <div class="radio"><label><input @click="changeType" data-id="2" type="radio" name="input_type" value="2" >从列表中选择</label></div>
                    </div>
                </div>



                <div class="form-group">
                    <label class="col-sm-3 control-label">可选值列表</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" rows="5" name="attr_value" v-if="input_type==1" disabled></textarea>
                        <textarea class="form-control" rows="5" name="attr_value" v-else></textarea>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-3 control-label">状态</label>
                    <div class="col-sm-6">
                        <div class="radio"><label><input type="radio" name="status" value="1" checked> 可用</label></div>
                        <div class="radio"><label><input type="radio" name="status" value="2" >禁用</label></div>
                    </div>
                </div>

                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3">
                            <button class="btn btn-primary btn-danger" id="btn-save">保存类型</button>&nbsp;
                        </div>
                    </div>
                </div><!-- panel-footer -->
            </form>

        </div><!-- panel-body -->
        <script src="/js/vue.min.js"></script>
        <script type="text/javascript">

            $(".alert-danger").hide();

            $("#btn-save").click(function(){

                var type_name = $("input[name=type_name]").val();

                if(type_name == ''){
                    $("#error_msg").text('类型名称不能为空');
                    $(".alert-danger").toggle();
                    return false;
                }

            });

            var attr = new Vue({
                el:"#attr",
                delimiters:["{",'}'],
                data:{
                    input_type:1
                },
                methods:{
                    //切换输入方式
                    changeType:function(e){
                        this.input_type = e.target.dataset.id;
                    }
                }
            })

        </script>

@endsection