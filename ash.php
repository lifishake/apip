<?php
/*
移动不适用的代码到这里，作为备份
*/

//add_filter('do_shortcode_tag', 'apip_append_linebreak_to_myfv', 10, 2);

//add_action('admin_print_footer_scripts','apip_quicktags_a');
function apip_quicktags_a()
{
?>
    <script type="text/javascript" charset="utf-8">
        QTags.addButton( 'eg_163music', '网易云音乐', '<iframe frameborder="no" border="0" marginwidth="0" marginheight="0" width=330 height=86 src="//music.163.com/outchain/player?type=2&id=', '&auto=1&height=66"></iframe>' );
        QTags.addButton( 'eg_mydoubanmovie', '豆瓣电影', '[mydouban id="', '" type="movie" nipple="no" /]', 'p' );
        QTags.addButton( 'eg_mydoubanmusic', '豆瓣音乐', '[mydouban id="', '" type="music" /]', 'p' );
        QTags.addButton( 'eg_mydoubanbook', '豆瓣读书', '[mydouban id="', '" type="book" /]', 'p' );
    </script>
<?php
}

/**
 * 作用: 去掉后台的Open Sans
 * 来源: 自产
 * URL:
 */
/*
function apip_remove_open_sans() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
}
*/

//0.7 自带的TagCloud格式调整
 /**
 * 作用: 调整TagCloud Widget输出的顺序及显示数量
 * 来源: 原创
 * Author URI:
 */
function apip_resort_tagcloud( $arg )
{
    $arg['number'] = '39' ;
    $arg['order'] = 'RAND' ;
    return $arg ;
}

?>