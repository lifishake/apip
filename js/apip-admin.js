function dec_to_hex_string(dec, length) {
    var hex = dec.toString(16).toUpperCase();
    if (hex.length < length) {
        hex = new Array( length - hex.length + 1 ).join( '0' ) + hex;
    }
    return hex;
}

function rgb_to_hex_string(rgb_array) {
    var hex_string = '';
    for( var i = 0; i < rgb_array.length; i++) {
        hex_string += dec_to_hex_string(rgb_array[i], 2);
    }
    return '#' + hex_string;
}

function rgb_to_rgb_string(rgb_array) {
    var rgb_string = 'RGB(';
    for( var i = 0; i < rgb_array.length; i++) {
        rgb_string += rgb_array[i];
        if ( i< 2) {
            rgb_string += ',';
        }
    }
    return rgb_string + ')';
}

jQuery(document).ready(function($) {
    //var thisLabel = document.getElementById('set-boring-rank-label');
    $('#link-color').wpColorPicker();
    $('#border-color').wpColorPicker();
    $('#font-color').wpColorPicker();
    $('#bg-color').wpColorPicker();
    $('#thief-color-picker').wpColorPicker();
    $( 'select[name="boring-rank"]' ).change( function() {
        var parent = $(this).parent();
        var thisLevel = $(this).val();
        var thisLabel = parent.find('.set-boring-rank-label');
        var data = {
			action: 'set_boring_comment_rank',
			nonce: parent.attr('wp_nonce'),
			id: parent.attr('data-comment-id'),
            level : thisLevel
		};
        $.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function () {
				thisLabel.html( $( '<span>' ).text( '修改中...' ) );
			},
            error: function(request) {
				thisLabel.html( $( '<span>' ).text( '出错了!' ) );
			},
			success: function (response) {
				if (response) {
                    thisLabel.html( $( '<span>' ).text( '新数值 ('+thisLevel+') ' ) );
				}
			}
		});
    })
    $('button[name="apiptranlatebtn"]').click(function(){
        var parent = $(this).parent();
        var chinese_title = document.getElementById("title").value;
        var url = "https://translate.yandex.net/api/v1.5/tr.json/translate";
        var part = "key="+yandexkey+"&text="+encodeURI(chinese_title)+"&lang=zh-en";
        var xhr = new XMLHttpRequest();
        xhr.open("POST",url,true);
        xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xhr.send(part);
        xhr.onreadystatechange = function() {
            if (this.readyState==4 && this.status==200) {
                var res = this.responseText;
                var json = JSON.parse(res);
                if(json.code == 200) {
                    document.getElementById("post_name").value=json.text[0];
                }
            }
        }
    })
    $('button[name="apipweatherbtn"]').click(function(){
        var parent = $(this).parent();
        var mybar = parent[0].childNodes[1].childNodes[1];
        var heweatherresult;
        var data = {
            action: 'apip_weather_manual_update',
            nonce: this.getAttribute('wpnonce'),
            id:this.getAttribute('id'),
		};
        $.ajax({
            url: ajaxurl,
			type: 'GET',
            data: data,
            cache: false,
            timeout: 10000,
            beforeSend: function () {
				mybar.value="获取中...";
			},
            success:function(response){
                mybar.value=response.content;
            },
            error: function() {
                mybar.value="异常";
			},
        });
    })
    $('button[name="apipcolorthirfbtn"]').click(function(){
        var parent = $(this).parent();
        var thisLabel = parent.find('.thumbnail-main-color-label');
        var img=jQuery('#set-post-thumbnail')[0].childNodes[0];
        var colorThief = new ColorThief();
        var picmaincolor=colorThief.getColor(img);
        var colorhex=rgb_to_hex_string(picmaincolor);
        var colorrgb=rgb_to_rgb_string(picmaincolor);
        var picker = parent.find('.wp-picker-container').find('.wp-color-result');
        var data = {
            action: 'apip_accept_color',
            maincolor: colorhex,
            nonce: this.getAttribute('wpnonce'),
            picid: this.getAttribute('picid'),
		};
        $.ajax({
			url: ajaxurl,
			type: 'POST',
            data: data,
			success: function (response) {
				if (response) {
                    //成功后更新colorpicker的颜色
                    picker[0].setAttribute("style","background-color:"+colorrgb);
				}
			}
		});
    })
})

jQuery(function ($) { 
    $(document).ajaxComplete(function (event, xhr, settings)  {
        if (typeof settings.data==='string' && /action=get-post-thumbnail-html/.test(settings.data) && xhr.responseJSON && typeof xhr.responseJSON.data==='string') {
            if ( /thumbnail_id=-1/.test(settings.data) ) {
                return;
            }
            var pos = settings.data.toLowerCase().indexOf("thumbnail_id");
            if (pos <= 0) {
                return;
            }
            var res = settings.data.split("&");
            var pic_id = res[1].substr(res[1].indexOf("=")+1);
            if (!pic_id) {
                return;
            }
            var img=jQuery('#set-post-thumbnail')[0].childNodes[0];
            var colorThief = new ColorThief();
            var picmaincolor=colorThief.getColor(img);
            var colorhex=rgb_to_hex_string(picmaincolor);
            var colorrgb=rgb_to_rgb_string(picmaincolor);
            var data = {
                action: 'apip_new_thumbnail_color',
                picid: pic_id,
                maincolor: colorhex,
            };
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                success: function (response) {
				if (response) {
                    //成功后更新colorpicker的颜色
                    var picker = jQuery('#apipcolorthiefdiv').find('.wp-picker-container').find('.wp-color-result');
                    picker[0].setAttribute("style","background-color:"+colorrgb);
				}
			}
            });
           }
    });
});