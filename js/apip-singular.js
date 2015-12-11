//jQuery(document).ready(function($){
	
	//回到顶部的JS
	/*var back_top_btn = $('#back_top');
	if(back_top_btn.length) {
		$(window).scroll(function () {
			setTimeout(function() {
				var scrollTop = $(this).scrollTop();
				if (scrollTop > 400) {
					back_top_btn.fadeIn();
				} else {
					back_top_btn.fadeOut();
				}
			},64);
		});
		back_top_btn.on('click',function (e) {
			e.preventDefault();
			$('body,html').animate({scrollTop: 0}, 400);
		});
	}*/
	
//});

//Ctrl+Enter提交
document.getElementById("comment").onkeydown = function (moz_ev)
{
var ev = null;
if (window.event){
	ev = window.event;
		}else{
		ev = moz_ev;
	}
	if (ev != null && ev.ctrlKey && ev.keyCode == 13)
	{
	document.getElementById("submit").click();
	}
}