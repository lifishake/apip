function dec_to_hex_string(dec, length) {
    var hex = dec.toString(16).toUpperCase();
    if (hex.length < length) {
        hex = new Array( length - hex.length + 1 ).join( '0' ) + hex;
    }
    return hex;
}

String.prototype.hexEncode = function(){
    var hex, i, ud, slash;
    var s = unescape(encodeURIComponent(this))
    var result = "";
	var chineseStart = 0;
    for (i=0; i<s.length; i++) {
		ud = s.charCodeAt(i);
		if (0==chineseStart && ud>=224 && ud<=239)
		{
			chineseStart++;
			hex = ud.toString(16);
		}else if (1==chineseStart){
			chineseStart++;
			hex = ud.toString(16);
		}else if (2==chineseStart){
			hex = ud.toString(16);
			chineseStart = 0;
			if (i!=s.length-1) {
				slash = 1;
			}
		}else {
			hex = ud;
		}
        result += hex;
		if (slash) {
			result += '-';
			slash = 0;
		}
    }

    return result;
}

function any_callback(element, canvas, ed){
    var t = this, startPos, endPos, cursorPos, scrollTop, v = canvas.value, l, r, i, sel, endTag = v ? t.tagEnd : '';
    var my_stuff = prompt( 'Enter Title Tag:', '' );
    if ( canvas.selectionStart || canvas.selectionStart === 0 ) { // FF, WebKit, Opera
        startPos = canvas.selectionStart;
        endPos = canvas.selectionEnd;

        cursorPos = endPos;
        scrollTop = canvas.scrollTop;
        l = v.substring(0, startPos); // left of the selection
        r = v.substring(endPos, v.length); // right of the selection
        i = v.substring(startPos, endPos); // inside the selection
        if ( startPos !== endPos ) {
            canvas.value = l + '<'+my_stuff+'>' + i + '</'+my_stuff+'>' + r;
            cursorPos += my_stuff.length + my_stuff.length + 5;
        }
    }
    canvas.selectionStart = cursorPos;
    canvas.selectionEnd = cursorPos;
    canvas.scrollTop = scrollTop;
    canvas.focus();
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
    $('#link-color').wpColorPicker();
    $('#border-color').wpColorPicker();
    $('#font-color').wpColorPicker();
    $('#bg-color').wpColorPicker();
    $('#thief-color-picker').wpColorPicker();
    $('button[name="apiphexbtn"]').click(function(){
        var parent = $(this).parent();
        var chinese_title = document.getElementById("title").value;
        var converted = chinese_title.hexEncode();
        document.getElementById("post_name").value=converted;
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
            timeout: 30000,
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
    $('button[name="apip_maintain_do"]').click(function(){
        var parent = $(this).parent();
        var mybar = parent.prev();
        var data = {
            action: 'apip_db_maintain',
            nonce: this.getAttribute('wpnonce'),
            id:this.getAttribute('id'),
		};
        $.ajax({
            url: ajaxurl,
			type: 'POST',
            data: data,
            beforeSend: function () {
				mybar[0].textContent="删除中...";
			},
            success: function (response) {
                mybar[0].textContent="已删除";
            },
            error: function() {
                mybar[0].textContent="异常";
			},
        });
    })
    $("#image_upload_btn").click(function(){        
        var files = $("#upload_files")[0].files;
        var wpnonce = this.getAttribute('wpnonce');
        var i, str_disp="";
        var fd = new FormData();
        var dest_path = $('#apip_upload_destination_paths').find(":selected").val();
        fd.append('action', 'apip_upload_image');
               
        for(i=0; i< files.length; ++i) {
            if (i > 0) {
                str_disp += " , ";
            }
            fd.append('file_'+i, files[i]);
            str_disp += files[i].name;
        }
        fd.append('file_count',i);
        fd.append('nonce', wpnonce);
        fd.append('dest_path', dest_path);
        
        $.ajax({
            url: ajaxurl,
			type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            beforeSend: function () {
				$("#image_upload_status").text("...");
			},
            success: function (response) {
                if (response.success) {
                    $("#image_upload_status").text(str_disp);
                } else {
                    $("#image_upload_status").text(response.data);
                }
            },
            error: function() {
                /*
                var id = '#image_upload_status';
                jQuery(id).html('');
                jQuery(id).append(fd);
                resetvalues();
                */
			},
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