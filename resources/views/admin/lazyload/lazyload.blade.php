@extends("comment.admin_base")

@section("title","测试图片懒加载")

    <!-- 页面顶部信息 -->
@section('pageHeader')
<div class="pageheader">
      <h2><i class="fa fa-home"></i> 测试图片懒加载 <span>Subtitle goes here...</span></h2>
      <div class="breadcrumb-wrapper">
        
      </div>
    </div>
@endsection

@section("content")
<body>

	<p>
		<img src="" data-src="http://www.laravel55.com/ueditor/php/upload/image/20190406/开心果.png">
	</p>

	<p>
		<img src="" data-src="http://www.laravel55.com/ueditor/php/upload/image/20190406/圣女果.png">
	</p>

	<p>
		<img src="" data-src="http://www.laravel55.com/ueditor/php/upload/image/20190406/棒棒糖.png">
	</p>

	<p>
		<img src="" data-src="http://www.laravel55.com/ueditor/php/upload/image/20190406/鸡腿.png">
	</p>

	<p>
		<img src="" data-src="http://www.laravel55.com/ueditor/php/upload/image/20190406/杯子蛋糕.png">
	</p>

	<p>
		<img src="" data-src="http://www.laravel55.com/ueditor/php/upload/image/20190406/蓝莓.png">
	</p>

	<p>
		<img src="" data-src="http://www.laravel55.com/ueditor/php/upload/image/20190406/樱桃.png">
	</p>

	<p>
		<img src="" data-src="http://www.laravel55.com/ueditor/php/upload/image/20190406/茶.png">
	</p>



	<script type="text/javascript">

		var images = document.getElementsByTagName('img');

		window.onload=function(){
			for(var i=0; i<5; i++){
				images[i].src=images[i].getAttribute('data-src');
			}
		}

		window.onscroll= function(){
			//滚动条的高度
			var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;

			//可视化区域高度
			var winTop = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

			for(var j=0; j<images.length; j++){

				if(images[j].offsetTop <= scrollTop + winTop){
					images[j].src=images[j].getAttribute('data-src');
				}
			}
		}

	</script>
</body>
@endsection

	

