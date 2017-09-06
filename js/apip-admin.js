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
})
