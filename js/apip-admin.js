jQuery(document).ready(function($) {
    //var thisLabel = document.getElementById('set-boring-rank-label');
    $('#link-color').wpColorPicker();
    $('#border-color').wpColorPicker();
    $('#font-color').wpColorPicker();
    $('#bg-color').wpColorPicker();
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
})
