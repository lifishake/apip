jQuery(document).ready(function($){
	function getParamsOfShareWindow(width,height){
		return['toolbar=0,status=0,resizable=1,width='+width+',height='+height+',left=',(screen.width-width)/2,',top=',(screen.height-height)/2].join('');
	}
	function bindShareList(){
		var link=encodeURIComponent(document.location);
		var title=encodeURIComponent(document.title.substring(0,76));
		var source=encodeURIComponent('网站名称');
		var windowName='share';
		var site='http://www.example.com/';
		jQuery('#twitter-share').click(function(){
			var url='http://twitter.com/share?url='+link+'&text='+title;
			var params=getParamsOfShareWindow(500,375);
			window.open(url,windowName,params);
		});
		jQuery('#kaixin001-share').click(function(){
			var url='http://www.kaixin001.com/repaste/share.php?rurl='+link+'&rcontent='+link+'&rtitle='+title;
			var params=getParamsOfShareWindow(540,342);
			window.open(url,windowName,params);
		});
		jQuery('#renren-share').click(function(){
			var url='http://share.renren.com/share/buttonshare?link='+link+'&title='+title;
			var params=getParamsOfShareWindow(626,436);
			window.open(url,windowName,params);
		});
		jQuery('#douban-share').click(function(){
			var url='http://www.douban.com/recommend/?url='+link+'&title='+title;
			var params=getParamsOfShareWindow(450,350);
			window.open(url,windowName,params);
		});
		jQuery('#fanfou-share').click(function(){
			var url='http://fanfou.com/sharer?u='+link+'?t='+title;
			var params=getParamsOfShareWindow(600,400);
			window.open(url,windowName,params);
		});
		jQuery('#sina-share').click(function(){
			var url='http://v.t.sina.com.cn/share/share.php?url='+link+'&title='+title;
			var params=getParamsOfShareWindow(607,523);
			window.open(url,windowName,params);
		});
		jQuery('#tencent-share').click(function(){
			var url='http://v.t.qq.com/share/share.php?title='+title+'&url='+link+'&site='+site;
			var params=getParamsOfShareWindow(640,480);
			window.open(url,windowName,params);
		});
		jQuery('#googleplus-share').click(function(){
			var url='https://plus.google.com/share?url='+link;
			var params=getParamsOfShareWindow(640,480);
			window.open(url,windowName,params);
		});
		jQuery('#facebook-share').click(function(){
			var url='http://www.facebook.com/sharer.php?u='+link+'&t='+title;
			var params=getParamsOfShareWindow(640,480);
			window.open(url,windowName,params);
		});
	}
	bindShareList();
});
