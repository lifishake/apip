<?php

/**
 * Plugin Name: All plugins in pewae
 * Plugin URI:  http://pewae.com
 * GitHub Plugin URI: https://github.com/lifishake/apip
 * Description: Plugins used by pewae
 * Author:      lifishake
 * Author URI:  http://pewae.com
 * Version:     1.33.5
 * License:     GNU General Public License 3.0+ http://www.gnu.org/licenses/gpl.html
 */

/*宏定义*/
define('APIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('APIP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ) ;
define('APIP_GALLERY_URL',home_url('/',is_ssl()?'https':'http').'wp-content/gallery/');
define('APIP_GALLERY_DIR', ABSPATH.'wp-content/gallery/');
register_activation_hook( __FILE__, 'apip_plugin_activation' );
register_deactivation_hook( __FILE__,'apip_plugin_deactivation' );
register_uninstall_hook(__FILE__, 'apip_plugin_deactivation');

/* 打log用 */
function apip_log(  $any )
{
    echo '<pre>'.$any.'</pre>';
}

/*插件激活*/
function apip_plugin_activation()
{
    global $wpdb;
    //4.1
    $thumb_path = APIP_GALLERY_DIR . "gravatar_cache";
    if (file_exists ($thumb_path)) {
        if (! is_writeable ( $thumb_path )) {
            @chmod ( $thumb_path, '511' );
        }
    } else {
        @mkdir ( $thumb_path, '511', true );
    }

    if (!file_exists($thumb_path."/default.png")) {
        @copy (APIP_PLUGIN_DIR."img/default.png", $thumb_path."/default.png");
    }

    //8.5
    $thumb_path = APIP_GALLERY_DIR . "douban_cache";

    if (file_exists ($thumb_path)) {
        if (! is_writeable ( $thumb_path )) {
            @chmod ( $thumb_path, '511' );
        }
    } else {
        @mkdir ( $thumb_path, '511', true );
    }

    //8.6
    $thumb_path = APIP_GALLERY_DIR . "game_poster";
    if (file_exists ($thumb_path)) {
        if (! is_writeable ( $thumb_path )) {
            @chmod ( $thumb_path, '511' );
        }
    } else {
        @mkdir ( $thumb_path, '511', true );
    }

    //8.11
    $thumb_path = APIP_GALLERY_DIR . "myfv";
    if (file_exists ($thumb_path)) {
        if (! is_writeable ( $thumb_path )) {
            @chmod ( $thumb_path, '511' );
        }
    } else {
        @mkdir ( $thumb_path, '511', true );
    }

}

/*插件反激活*/
function apip_plugin_deactivation()
{

}

/*配置画面*/
if (is_admin())
{
    require_once( APIP_PLUGIN_DIR . '/apip-options.php');
}
//包含自定义的函数
require ( APIP_PLUGIN_DIR.'/apip-func.php') ;

function apip_option_check( $key, $val = 1 )
{
    global $apip_options;
    if ( empty($apip_options) ) {
        $apip_options = get_option('apip_settings');
    }
    //array_key_exists
    if ( isset( $apip_options[$key] ) && $apip_options[$key] == $val ) {
        return true;
    }
    return false;
}

/* Plugin页面追加配置选项 */
function apip_settings_link($action_links, $plugin_file){
    if($plugin_file == plugin_basename(__FILE__)){
        $apip_settings_link = '<a href="options-general.php?page=apip/apip-options.php">Settings</a>';
        array_push($action_links, $apip_settings_link);
    }
    return $action_links;
}
add_filter('plugin_action_links','apip_settings_link',10,2);

/*变量初期化*/
add_action('plugins_loaded', 'apip_init', 99);
function apip_init()
{
    /** 00 */
    global $wpdb;
    //0.1 插件自带脚本控制
    add_action( 'wp_enqueue_scripts', 'apip_scripts' );
    add_action( 'admin_enqueue_scripts', 'apip_admin_scripts' );
    //0.2 屏蔽不必要的js
    add_filter( 'wp_print_scripts', 'apip_remove_scripts', 99 );
    add_filter( 'admin_print_scripts', 'apip_remove_scripts', 99 );
    //0.3 屏蔽不必要的css
    add_filter( 'wp_print_styles', 'apip_remove_styles', 99 );
    add_filter( 'admin_print_styles', 'apip_remove_styles', 99 );
    //0.4 在feed中增加关联内容
    add_filter('the_excerpt_rss', 'apip_addi_feed');
    add_filter('the_content_feed', 'apip_addi_feed');
    //0.5 后台追加的快捷按钮
    add_action('admin_print_footer_scripts','apip_quicktags');
    //0.6 去掉后台的OpenSans  -->移至统一的admin_enqueue_scripts
    //0.7 自带的TagCloud格式调整  -->暂时不用
    //0.8 移除后台的“作者”列
    add_filter( 'manage_posts_columns', 'apip_posts_columns' );
    //0.9 升级后替换高危文件
    add_action( 'upgrader_process_complete', 'apip_remove_default_risk_files', 11, 2 );
    //0.10 作者页跳转到404 -->移至统一的template_redirect钩子
    //add_action('template_redirect', 'apip_redirect_author');
    add_action('template_redirect', 'apip_template_redirect');
    //0.11 屏蔽留言class中的作者名
    add_filter('comment_class', 'apip_remove_author_class', 10, 5);
    //0.12 禁用古腾堡
    add_filter('use_block_editor_for_post', '__return_false');
    remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
    //0.13 替换human_time_diff函数中的英文单词
    add_filter( 'human_time_diff', 'apip_replaced_human_time_diff', 10, 1 );
    //0.14 改善代码在feed里的表现
    add_filter('the_content_feed', 'apip_code_highlight') ;
    add_filter('the_content_feed', 'so_handle_038', 199, 1);
    //0.15 移除后台界面的WP版本升级提示 -->因为会引起downgrade失败,所以改为有配置项的2.11
    //0.16 修改AdminBar
    add_action( 'wp_before_admin_bar_render', 'apip_admin_bar', 199 );
    //0.17 针对苹果旧设备的访问，减少404
    add_filter('site_icon_meta_tags','apip_add_apple_touch_icon');
    //0.18 汉字标题自动转utf8字符
    add_filter( 'sanitize_title', 'apip_slug', 1 );
    //0.19 autop与shortcode冲突问题
    add_filter( 'the_content', 'apip_fix_shortcodes');
    //0.20 改用户profile不需要邮件确认
    remove_action('personal_options_update', 'send_confirmation_on_profile_email');
    //0.21 设置chrome内核浏览器的tab颜色
    add_action('wp_head', 'apip_set_theme_color', 20);
    /** 01 */
    //颜色目前没有函数

    /** 02 */
    //2.1停止自动版本更新　　=>这个必须在config里面设才行，已删除
    //2.2停止自动保存
    if( apip_option_check('auto_save_disabled') ) {
        add_action( 'wp_print_scripts', 'apip_auto_save_setting' );
    }
    //2.3是否显示adminbar
    add_filter( 'show_admin_bar', 'apip_admin_bar_setting' );

    //2.4后台英文前台中文
    if ( apip_option_check('forground_chinese') ) {
        add_filter( 'locale', 'apip_locale', 99 );
    }

    //2.5屏蔽已经注册的open sans
    if ( apip_option_check('block_open_sans') ) {
        add_action( 'wp_default_styles', 'apip_block_open_sans', 100);
    }

    //2.6默认留言widget里屏蔽作者
    if ( apip_option_check('show_author_comment') )
    {
        add_filter( 'widget_comments_args', 'before_get_comments' );
    }
    
    //2.7 搜索结果只有一条时直接跳入，移至apip_template_redirect
    //2.8禁止直接访问wp_comments.php
    if ( apip_option_check('protect_comment_php') )
    {
        add_action('check_comment_flood', 'check_referrer_comment');
    }

    //2.9搜索结果不包括page页面
    if ( apip_option_check('search_without_page') )
    {
        add_filter('pre_get_posts','remove_page_search');
    }

    //2.10外链转内链
    if  ( apip_option_check('redirect_external_link') ) {
        add_filter('the_content','convert_to_internal_links',99); // 文章正文外链转换
        add_filter('comment_text','convert_to_internal_links',99); // 评论内容的链接转换
        add_filter('comment_url','apip_comment_url', 10, 2); //链接转换
    }

    //2.11移除后台界面的WP版本升级提示
    if  ( apip_option_check('remove_core_updates') ) {
        add_filter('pre_site_transient_update_core','remove_core_updates');
    }

    if ( is_admin() ) {
        define('NGG_DISABLE_RESOURCE_MANAGER', FALSE);
    } else {
        define('NGG_DISABLE_RESOURCE_MANAGER', TRUE);
    }
    if ( is_page('gallery') ) {
        define('NGG_DISABLE_FILTER_THE_CONTENT', FALSE);
    }  else {
        define('NGG_DISABLE_FILTER_THE_CONTENT', TRUE);
    }

    /** 03 */
    if( apip_option_check('better_excerpt') ) {
        //更好的中文摘要
        add_filter('the_excerpt', 'apip_excerpt', 100);
    }
    if ( apip_option_check('header_description') ) {
        //网站描述和关键字
        add_action( 'wp_head', 'apip_desc_tag' );
    }

    /** 04 */
    //4.1 头像替换
    add_filter('get_avatar','apip_get_cavatar');
    //4.2 表情链接替换
    add_filter( 'emoji_url', 'apip_rep_emoji_url', 99, 1);

    /** 05 */
    //5.1 广告关键字替换，抢在akimest前面
    add_filter('preprocess_comment', 'hm_check_user',1);
    add_action('comment_post', 'apip_remember_advertise_comment_details',10,3);
    add_filter('comment_row_actions', 'apip_show_advertise_comment_details', 10, 2 );
    add_filter('comment_form_defaults', 'apip_replace_triditional_comment_placeholder_text');

    /** 06*/
    //social没有添加项,需要外部手动调用

    /** 07 自定义页面 */
    //7.1 TAGcloud 注册
    if ( apip_option_check('apip_tagcloud_enable') )
    {
        add_shortcode('mytagcloud', 'apip_tagcloud_page');
    }
    //7.2 友链注册
    if ( apip_option_check('apip_link_enable') )
    {
        add_shortcode('mylink', 'apip_link_page');
    }
    //7.3 归档页注册
    if ( apip_option_check('apip_archive_enable') )
    {
        add_shortcode('myarchive', 'apip_archive_page');
    }

    /** 08 */
    //头部动作，一般用于附加css的加载
    //add_action('wp_head','apip_header_actions') ;
    //8.1 prettyprint脚本激活
    //add_action('get_footer','apip_footer_actions') ;

    //8.2 lazyload
    if ( apip_option_check('apip_lazyload_enable') )  {
        add_filter('the_content', 'apip_lazyload_filter', 200);
        add_filter('post_thumbnail_html', 'apip_lazyload_filter', 200);
    }

    //8.3 结果集内跳转
    if ( apip_option_check('range_jump_enable') ) {
        add_action('template_redirect', 'apip_keep_query', 9 );
    }

    //8.4 留言邮件回复
    if ( apip_option_check('notify_comment_reply') )  {
        //邮件回复
        add_action('wp_insert_comment','apip_comment_inserted',99,2);
    }
    //8.5 豆瓣显示
    if ( !class_exists('Apip_SimpleImage') ) {
        //包跳转类含头文件
        require_once ( APIP_PLUGIN_DIR.'/class/apip-image.php') ;
    }

    add_shortcode('myimdb', 'apip_imbd_detail');

    //8.6 每夜一游
    add_shortcode('mygame', 'apip_game_detail');

    //8.7 发帖天气
    //当作每篇文章都会存草稿.草稿转成公开的时刻为发表时刻
    add_action( 'draft_to_publish','apip_save_heweather',99,1);
    add_action( 'draft_to_private','apip_save_heweather',99,1);
    add_action( 'auto-draft_to_publish','apip_save_heweather',99,1);
    add_action( 'auto-draft_to_private','apip_save_heweather',99,1);
    add_action( 'new_to_publish','apip_save_heweather',99,1);
    add_action( 'new_to_private','apip_save_heweather',99,1);
    //在后台update区域增加手动更新天气的checkbox
    if (is_admin())  {
        add_action( 'post_submitbox_misc_actions', 'apip_heweather_field' );
    }
    //8.8 留言验证问题
    if(is_admin() && apip_option_check('apip_commentquiz_enable') ) {
        add_action('admin_init','apip_commentquiz_init');
    }
    //8.10 特色图主颜色按钮
    if(is_admin()) {
        add_action('admin_menu','apip_optimize_boxes');
        //增加ajax回调函数
        add_action( 'wp_ajax_apip_accept_color', 'apip_accept_color' );
        add_action( 'wp_ajax_apip_new_thumbnail_color', 'apip_new_thumbnail_color' );
        add_action( 'wp_ajax_apip_weather_manual_update', 'apip_weather_manual_update' );
    }
    //8.11 我的收藏第一版
    add_shortcode('myfv', 'apip_myfv_detail');
    //add_filter('do_shortcode_tag', 'apip_append_linebreak_to_myfv', 10, 2);
    add_action( 'transition_post_status', 'apip_myfv_filter', 10, 3 );

    //8.12 我的引文
    add_shortcode('mysup', 'apip_sup_detail');
    add_filter( 'the_content', 'apip_make_sup_anchors', 101);
    add_filter( 'the_content_feed', 'apip_make_sup_anchors', 101);

    /** 09  */
    //9.1 后台taglist增加private和draft计数列
    if(is_admin()) {
        add_filter( "manage_edit-post_tag_columns", 'apip_edit_post_tag_column_header', 10);
        add_action( "manage_post_tag_custom_column", 'apip_edit_post_tag_content', 10, 3);
    }
    //9.2
    if(is_admin()) {
        add_filter( 'post_tag_row_actions', 'apip_convert_post_tag_slug_to_utf', 10, 2 );
    }

    // Add to the admin_init action hook
    //add_filter('current_screen', 'my_current_screen' );
    
    function my_current_screen($screen) {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return $screen;
        print_r($screen);
        return $screen;
    }

    //0X 暂时不用了
    //三插件冲突
    //add_action( 'wp_print_scripts', 'apip_filter_filter',2 );

    /** 99 */
    if ( apip_option_check('local_widget_enable') ) {
        require APIP_PLUGIN_DIR.'/apip-widgets.php';
    }

}

register_activation_hook( __FILE__, 'apip_disable_embeds_remove_rewrite_rules' );
register_deactivation_hook( __FILE__, 'apip_disable_embeds_flush_rewrite_rules' );

add_action('init', 'apip_init_actions', 999);
function apip_init_actions()
{   
    //0.A    移除没用的过滤项
    remove_action('wp_head','feed_links_extra',3);
    remove_action('wp_head','rsd_link' );
    remove_action('wp_head','wlwmanifest_link' );
    remove_action('wp_head','adjacent_posts_rel_link_wp_head',10,0);
    remove_action('wp_head','wp_generator');
    remove_filter('the_content','capital_P_dangit',11);
    remove_filter('the_title','capital_P_dangit',11);
    remove_filter('wp_title','capital_P_dangit',11);
    remove_filter('comment_text','capital_P_dangit',31);
    add_filter( 'use_default_gallery_style', '__return_false' );    //不使用默认gallery
    add_filter('xmlrpc_enabled', '__return_false');     //不使用xmlrpc
    add_filter( 'feed_links_show_comments_feed', '__return_false' ); //不输出comments的rss,4.4以上

    ////0A.1屏蔽ngg带来的无用钩子
    if( class_exists('M_Third_Party_Compat') ) {
        apip_remove_anonymous_object_hook( 'the_content', 'M_Third_Party_Compat', 'check_weaverii' );
    }
    if( class_exists('C_NextGen_Shortcode_Manager') ) {
        apip_remove_anonymous_object_hook( 'the_content', 'C_NextGen_Shortcode_Manager', 'fix_nested_shortcodes' );
    }
    if( class_exists('M_Gallery_Display') ) {
        //20180320删除。好像插件更新，这个钩子的参数发生了变化，php有错误产生。
        //apip_remove_anonymous_object_hook( 'the_content', 'M_Gallery_Display', '_render_related_images' );
        //apip_remove_anonymous_object_hook( 'wp_enqueue_scripts', 'M_Gallery_Display', 'no_resources_mode' );
    }
    if( class_exists('M_NextGen_Basic_Singlepic') ) {
        apip_remove_anonymous_object_hook( 'the_content', 'M_NextGen_Basic_Singlepic', 'enqueue_singlepic_css' );
    }
    //静态函数
    remove_filter('the_content', 'NextGEN_shortcodes::convert_shortcode');
    remove_action('wp_head', 'nggGallery::nextgen_version');
    if( class_exists('C_NextGen_Shortcode_Manager') )  {
        apip_remove_anonymous_object_hook( 'the_content', 'C_NextGen_Shortcode_Manager', 'parse_content' );
        apip_remove_anonymous_object_hook( 'widget_text', 'C_NextGen_Shortcode_Manager', 'fix_nested_shortcodes' );
    }
    if( class_exists('M_Attach_To_Post') )  {
        apip_remove_anonymous_object_hook( 'the_content', 'M_Attach_To_Post', 'substitute_placeholder_imgs' );
        apip_remove_anonymous_object_hook( 'media_buttons', 'M_Attach_To_Post', 'add_media_button' );
    }
    if( class_exists('C_NextGEN_Bootstrap') )  {
        //20180320删除。好像插件更新，这个钩子的参数发生了变化，php有错误产生。
        //apip_remove_anonymous_object_hook( 'wp_enqueue_scripts', 'C_NextGEN_Bootstrap', 'fix_jquery' );
        //apip_remove_anonymous_object_hook( 'wp_print_scripts', 'C_NextGEN_Bootstrap', 'fix_jquery' );
    }
    if( class_exists('C_Lightbox_Library_Manager') )  {
        //20180320删除。好像插件更新，这个钩子的参数发生了变化，php有错误产生。
        //apip_remove_anonymous_object_hook( 'wp_enqueue_scripts', 'C_Lightbox_Library_Manager', 'maybe_enqueue' );
    }
    /*
    if( class_exists('C_Photocrati_Resource_Manager') )
    {
    //20180320删除。好像插件更新，这个钩子的参数发生了变化，php有错误产生。
        apip_remove_anonymous_object_hook( 'wp_footer', 'C_Photocrati_Resource_Manager', 'print_marker' );
    }*/
    //删除原来插入时的class
    remove_action('media_upload_nextgen','media_upload_nextgen');
    if (is_admin()){
        add_action('media_upload_nextgen','apip_media_upload_nextgen');
    }

    ////0A.2
    ////禁用4.4以后的embed功能
    ////来源:disable-embeds
    /*
    global $wp;
    if ( is_array($wp->public_query_vars) && !empty($wp->public_query_vars) ) {
        $wp->public_query_vars = array_diff( $wp->public_query_vars, array(
            'embed',
        ) );
    }
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    add_filter( 'embed_oembed_discover', '__return_false' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'tiny_mce_plugins', 'apip_disable_embeds_tiny_mce_plugin' );
    add_filter( 'rewrite_rules_array', 'apip_disable_embeds_rewrites' );
    remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
    if ( class_exists('WP_Embed')) {
        apip_remove_anonymous_object_hook( 'the_content', 'WP_Embed', 'run_shortcode' );
        apip_remove_anonymous_object_hook( 'the_content', 'WP_Embed', 'autoembed' );
    }
    */

    //8.3 结果集内跳转的先决条件
    if( !session_id() )
    {
        session_start();
    }
    include (plugin_dir_path( __FILE__ )."apip-local-debug.php");
}

function apip_header_actions()
{
}

/*
$options
00.                                     无选项，必须选中的内容
    0.1                                 Ctrl+Enter提交                      //20200418移除
    0.2                                 屏蔽不必要的js
    0.3                                 屏蔽不必要的style
    0.4                                 feed结尾的追加内容
    0.5                                 追加的快捷按钮
    0.6                                 屏蔽后台的OpenSans
    0.7                                 调整默认的TagCloud Widget
    0.8                                 移除后台的作者列
    0.9                                 版本升级后自动替换掉危险文件(wp-comments-post.php,xmlrpc.php)
    0.11                                移除无用的钩子
    0.12                                禁用古腾堡（5.0后）
    0.13                                替换human_time_diff函数中的英文单词
    0.14                                改善代码在feed里的表现
    0.15                                已删除
    0.16                                强化AdminBar
    0.17                                针对苹果旧设备的访问，减少404
    0.18                                汉字标题自动转utf8字符
    0.19                                autop与shortcode冲突问题
    0.20                                改用户profile不需要邮件确认
    0.21                                设置chrome的标签颜色
01.     颜色选项
02.     高级编辑选项
    2.1     save_revisions_disable      阻止自动版本                ×已删除
    2.2     auto_save_disabled          阻止自动保存
    2.3     show_admin_bar              显示登录用户的admin bar
    2.4     apip_locale                 后台英文前台中文
    2.5     block_open_sans             屏蔽后台的open sans字体
    2.6     show_author_comment         屏蔽作者留言
    2.7     redirect_if_single          搜索结果只有一条时直接跳入
    2.8     protect_comment_php         禁止直接访问wp_comments.php
    2.9     search_without_page         搜索结果中屏蔽page
    2.10    redirect_external_link      外链转内链
    2.11    remove_core_updates         移除后台界面的WP版本升级提示
03.     header_description              头部描述信息
    3.1     hd_home_text                首页描述文字
    3.2     hd_home_keyword             首页标签
    3.3     excerpt_length              摘要长度
    3.4     excerpt_ellipsis            摘要结尾字符
04.     GFW选项
    4.1     local_gravatar              头像本地缓存
    4.2     replace_emoji               替换emoji地址
05.    留言者控制
   5.1  blocked_commenters              替换广告留言用户名和网址
06.     social_share_enable             社会化分享使能
07.     自定义的shortcode
    7.1     apip_tagcloud_enable        更好看的标签云
    7.2     apip_link_page              自定义友情链接
    7.3     apip_achive_page            自定义归档页
08.     比较复杂的设定
    8.1     apip_codehighlight_enable   代码高亮
    8.2     apip_lazyload_enable        LazyLoad
    8.3                                 结果集内跳转
    8.4.    notify_comment_reply        有回复时邮件提示
    8.5                                 豆瓣电影
    8.6                                 gaintbomb游戏信息
    8.7     heweather_key               和风天气/发帖时天气信息
    8.8     apip_commentquiz_enable     回复前答题
    8.9     apip_title_hex_meta_box     手动将标题转换成unicode值的按钮
    8.10    apip_colorthief_meta_box    取特色图片主色调相关内容
    8.11                                自定义收藏的添加和显示
09.     后台维护相关
    9.1                                 后台taglist增加private和draft计数列
99.     local_widget_enable             自定义小工具
    99.1    local_definition_count      自定义widget条目数
*/

/******************************************************************************/
/*        00.没有选项必须好用                                                     */
/******************************************************************************/
//0.1+
 /**
 * 作用: 插件自带脚本
 * 来源: 自产
 * URL:
 */
function apip_scripts()
{
    global $apip_options;
    $color_border = isset( $apip_options['border_color'] ) ? $apip_options['border_color'] : "#8a8988";
    $color_link = isset( $apip_options['link_color'] ) ? $apip_options['link_color'] : "#1a5f99";
    $color_font = isset( $apip_options['font_color'] ) ? $apip_options['font_color'] : "#0a161f";
    $color_bg = isset( $apip_options['bg_color'] ) ? $apip_options['bg_color'] : "#ece5df";
    wp_enqueue_style( 'apip-style-all', APIP_PLUGIN_URL . 'css/apip-all.css', array(), '20200804' );
    wp_enqueue_script('apip-js-option', APIP_PLUGIN_URL . 'js/apip-option.js', array(), "20200418", true);
    $css = '';

    if ( /*is_single()*/1 ) {
        wp_enqueue_style( 'apip_weather_style', APIP_PLUGIN_URL . 'css/weather-icons.min.css' );
        wp_enqueue_style( 'apip_wind_style', APIP_PLUGIN_URL . 'css/weather-icons-wind.min.css' );
    }

    if (is_singular()) {
        wp_enqueue_script('apip-js-singular', APIP_PLUGIN_URL . 'js/apip-singular.js', array(), "20201208", true);
    }

    //0.1 Ctrl+Enter 提交
    //if (is_singular() && comments_open() ) {
        //wp_enqueue_script('apip-comment-form', APIP_PLUGIN_URL . 'js/apip-comment-form.js', array(), "20200417", true);
    //}
    //07
    if  ( is_singular() && apip_option_check('social_share_enable') )
    {
        wp_enqueue_script('apip-js-social', APIP_PLUGIN_URL . 'js/apip-social.js', array(), "20191101", true);
    }
    //7.1
    if ( is_page('my-tag-cloud') && apip_option_check('apip_tagcloud_enable') )
    {
        $link_colors = array();
        $bg_colors = array();
        $link_colors = apip_get_link_colors($color_link);
        $bg_colors = apip_get_bg_colors($color_bg);
        $css .= '   ul.tagcloud li {
                        background-color: '.$color_bg.'E6;
                    }
                    a.lk0 {
                        color: '.$link_colors[0].';
                    }
                    a.lk1 {
                        color: '.$link_colors[1].';
                    }
                    a.lk2 {
                        color: '.$link_colors[2].';
                    }
                    a.lk3 {
                        color: '.$link_colors[3].';
                    }
                    a.lk4 {
                        color: '.$link_colors[4].';
                    }
                    a.lk5 {
                        color: '.$link_colors[5].';
                    }
                    a.lk6 {
                        color: '.$link_colors[6].';
                    }
                    a.lk7 {
                        color: '.$link_colors[7].';
                    }';
                    
    }
    //7.2
    if ( is_page('my_links') && apip_option_check('apip_link_enable') )
    {
        $css .= '   .url::after {
                        color: '.$color_link.';
                    }';
    }
    //7.3
    if ( (is_page('archive')||is_page('archives')) && apip_option_check('apip_archive_enable') )
    {
        $css .= '   
                    .post-'.get_the_ID().' .entry-content ul li .achp-child {
                        line-height: 1.25rem;
                        font-size: 1rem;
                    }
                    .post-'.get_the_ID().' .entry-content ul,
                    .post-'.get_the_ID().' .entry-content ol {
                        border: none !important;
                        font-weight: normal !important;
                        text-shadow: none !important;
                    }
                    .post-'.get_the_ID().' .entry-content ul:not(.apip-no-disp),
                    .post-'.get_the_ID().' .entry-content ol:not(.apip-no-disp) {
                        display: inherit;
                    }';

        wp_enqueue_script('apip-js-achp', APIP_PLUGIN_URL . 'js/apip-achp.js', array(), "20191105", true);
    }
    //8.1
    if ( /*is_single() &&*/ (in_category('code_share') || has_tag('testcode')) && apip_option_check('apip_codehighlight_enable') == 1 )
    {
        add_filter('the_content', 'apip_code_highlight') ;
        add_filter('the_content', 'so_handle_038', 199, 1);
        wp_enqueue_script('apip-js-prettify', APIP_PLUGIN_URL . 'js/apip-prettify.js', array(), "20191101", true);
    }
    //8.2
    if ( apip_option_check('apip_lazyload_enable') ) {
        wp_enqueue_script('apip-js-lazyload', APIP_PLUGIN_URL . 'js/unveil-ui.min.js', array(), '20200413', true);
    }

     //8.8
     if ( is_single() && comments_open() && apip_option_check('apip_commentquiz_enable')) {
        wp_enqueue_script( 'apip-js-comment-quiz',APIP_PLUGIN_URL . 'js/apip-commentquiz.js', array(), false, true);
     }
    if ( $css !== '' ) {
        wp_add_inline_style('apip-style-all', $css);
    }
}
/* 统一处理后台相关的脚本 */
function apip_admin_scripts() {
    global $apip_options;
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'apip-style-option', APIP_PLUGIN_URL . 'css/apip-option.css' );
    wp_enqueue_style( 'apip-style-admin', APIP_PLUGIN_URL . 'css/apip-admin.css' );
    wp_enqueue_script('apip-color-thief', APIP_PLUGIN_URL . 'js/color-thief.js', array(), '20191101', true);
    wp_enqueue_script('apip-js-admin', APIP_PLUGIN_URL . 'js/apip-admin.js', array('wp-color-picker' ), '20200804', true);
    //wp_localize_script('apip-js-admin','yandexkey',$apip_options['yandex_translate_key']);
    //20200416 原0.6功能,移除OpenSans字体
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
}

//0.2
/**
 * 作用: 屏蔽已装载插件的不必要的js
 * 来源: 自产
 * URL:
 */
function apip_remove_scripts()
{
    global $wp_scripts;
    if ( !is_array($wp_scripts) || empty($wp_scripts) || empty($wp_scripts->registered) )
        return;
    foreach ($wp_scripts->registered as $libs){
        $libs->src = str_replace('//ajax.googleapis.com', '//gapis.geekzu.org/ajax', $libs->src);
        //fonts.gmirror.org
        }
    if ( !is_admin() )
    {
        wp_dequeue_script( 'photocrati_ajax' );
        wp_dequeue_script( 'lazy_resources' );
        wp_dequeue_script( 'frame_event_publisher' );
        wp_dequeue_script( 'ngg-store-js' );
        wp_dequeue_script( 'nextgen_lightbox_context' );
        wp_dequeue_script( 'ngg_common' );
        wp_dequeue_script( 'photocrati-nextgen_basic_thumbnails' );
    }
    if ( !is_page('gallery') )
    {
        wp_dequeue_script( 'jquery-nivo-slider' );
        wp_dequeue_script( 'jquery-shuffle' );
    }
}

//0.3
/**
 * 作用: 屏蔽不必要的style
 * 来源: 自产
 * URL:
 */
function apip_remove_styles()
{
    global $wp_styles;
    foreach ($wp_styles->registered as $libs){
    //替换google字体
        $libs->src = str_replace('//fonts.googleapis.com', '//gapis.geekzu.org/g-fonts', $libs->src);
        //fonts.gmirror.org
        }
    if ( !is_admin() )
    {
        wp_dequeue_style( 'fontawesome' );
        wp_dequeue_style( 'ngg_trigger_buttons' );
        wp_dequeue_style( 'nextgen_basic_singlepic_style' ) ;
        wp_dequeue_style( 'nextgen_pagination_style' );
        wp_dequeue_style( 'nextgen_pagination_style' );

    }
    if ( !is_page('gallery') )
    {
        wp_dequeue_style( 'jquery-plugins-slider-style' );
        wp_dequeue_style( 'ngg-nivoslider-theme' );
    }
}

//0.4
/**
 * 作用: 在feed中增加相关内容
 * 来源: 自产
 * URL:
 */
function apip_addi_feed($content)
{
    if( !is_feed() )
    {
        return $content ;
    }
    $addi = sprintf( '<div style="max-width: 520px; margin:0 auto; padding:5px 30px;margin: 15px; border-top: 1px solid #CCC;"><span style="margin-left: 2px; display:block;">《%1$s》采用<a rel="license" href="//creativecommons.org/licenses/by-nc-nd/3.0/cn/deed.zh">署名-非商业性使用-禁止演绎</a>许可协议进行许可。 『%2$s』期待与您交流。</span><div style="display:table;">%3$s</div></div>',
                        sprintf( '<a href="%1$s">%2$s</a>' , get_permalink(get_the_ID()), get_the_title() ),
                        sprintf( '<a href="%1$s">%2$s</a>' , get_bloginfo('url'), get_bloginfo('name') ),
                        sprintf('<div style="margin: 5px 25px; display:table-cell; max-width:500px; "><h3 style="font-size:16px; font-weight:800;" >相关推荐:</h3>%s</div>', apip_related_post() )
                        );

    $content.=$addi ;
    return $content ;
}

//0.5
/**
 * 作用: 追加代码和网易云的快捷按钮
 * 来源: 自产
 * URL:
 */
function apip_quicktags()
{
?>
    <script type="text/javascript" charset="utf-8">
        QTags.addButton( 'eg_pre', 'pre', '\n<pre>\n', '\n</pre>\n', 'p' );
        QTags.addButton( 'eg_myimdb', 'imdb', '[myimdb id="', '" cname="" score="99" nipple="no" /]', 'p' );
        QTags.addButton( 'eg_mygame', '每夜一游', '[mygame id="', '" cname="" ename="" jname="" alias="" year="" publisher=""  platform="" download="" genres="" poster="" /]', 'p' );
        QTags.addButton( 'eg_myfavbook', '收藏书', '[myfv id="x" type="book" title="', '" img="x" link="" score="99" abs="doulink:;douscore:;作者:;译者:;出版年份:;出版社:;" series="0"/]', 'p' );
        QTags.addButton( 'eg_myfavbooklist', '收藏书系', '[myfv id="x" type="book" title="', '" img="" link="" score="99" abs="作者:;译者:;出版年份:;出版社:;全套册数:" series="1"/]', 'p' );
        QTags.addButton( 'eg_myfavmusic', '收藏音乐', '[myfv id="x" type="music" title="', '" img="x" link="" score="99" abs="出版年份:;出版公司:;表演者:;doulink:;douscore:;" series="0"/]', 'p' );
        QTags.addButton( 'eg_myfavmovie', '收藏电影', '[myfv id="x" type="movie" title="', '" img="x" link="" score="99" abs="年份:;导演:;演员:;类型:;nipple:;doulink:;douscore:;" series="0"/]', 'p' );
        QTags.addButton( 'eg_myfavauto', '收藏自动', '[myfv id="x" type="auto" img="x" link="', '" score="99" /]', 'p' );
        QTags.addButton( 'eg_mysup', '引文', '[mysup sup_content="', '" /]', 'p' );
    </script>
<?php
}

//0.8 移除后台的作者列
function apip_posts_columns( $columns ) {
    unset( $columns['author'] );
    return $columns;
}

//0.9 升级后替换高危文件
function apip_remove_default_risk_files( $upgrader_object, $options )
{
    if( 'update' === $options['action'] && 'core' === $options['type'] )
    {
        global $wp_filesystem;
        $wp_dir = trailingslashit($wp_filesystem->abspath());
        @$wp_filesystem->copy( APIP_PLUGIN_DIR.'/ext/wp-go-die.php', $wp_dir.'wp-comments-post.php', true );
        @$wp_filesystem->copy( APIP_PLUGIN_DIR.'/ext/wp-go-die.php', $wp_dir.'xmlrpc.php', true );
    }
}

//0.10 author页跳转到404
function apip_redirect_author() {
    if (is_author()) {
        global $wp_query;
        $wp_query->set404();
        wp_redirect( network_site_url( '404.php' ) );
        exit;
    }
}

//0.11 屏蔽留言中的作者名class
function apip_remove_author_class( $classes, $class, $comment_ID, $comment, $post_id ) {
    $c_rm = array();
    if ( $comment->user_id > 0 && $user = get_userdata( $comment->user_id ) ) {
        $c_rm[] = 'comment-author-' . sanitize_html_class( $user->user_nicename, $comment->user_id );
    }
    $classes = array_diff( $classes, $c_rm );
    return $classes;
}

//0.13
//来源:https://www.syshut.com/human_time_diff-function-localization-with-en-wp.html
function apip_replaced_human_time_diff( $since ) {
    $search = array( 'years', 'year', 'months', 'month', 'weeks', 'week', 'days', 'day', 'hours', 'hour', 'mins', 'min', 'seconds', 'second', );
    $replace = array( '年', '年', '个月', '个月', '周', '周', '天', '天', '小时', '小时', '分钟', '分钟', '秒', '秒', );
    $since = str_replace( $search, $replace, $since );
    return $since;
}
//0.15-->2.11
//来源:https://thomas.vanhoutte.be/miniblog/wordpress-hide-update/
function remove_core_updates(){
    global $wp_version;
    return (object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}

//0.16 优化AdminBar
function apip_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo'); //移除Logo
    $wp_admin_bar->remove_menu('updates');
    if (!is_admin()){
        $wp_admin_bar->remove_menu('customize'); 
        $wp_admin_bar->add_menu( array(
            'id' => 'custom_plugin',
            'title' => 'Plugins',
            'href' => home_url('/',is_ssl()?'https':'http').'wp-admin/plugins.php',
            'parent' => 'site-name',
            )
        );
    }
    else {
        //后台增加直接跳到草稿
        $wp_admin_bar->add_menu( array(
            'id' => 'custom_drafts',
            'title' => 'Drafts',
            'href' => home_url('/',is_ssl()?'https':'http').'wp-admin/edit.php?post_status=draft&post_type=post',
            'parent' => 'site-name',
            )
        );
    }
}

//0.17 减少苹果旧设备访问的404错误
function apip_add_apple_touch_icon($meta_tags){
    $icon_180 = get_site_icon_url( 180 );
    if ( $icon_180 ) {
        $meta_tags[] = sprintf( '<link rel="apple-touch-icon" href="%s" />', esc_url( $icon_180 ) );
    }
    return $meta_tags;
}

//0.18 处理汉字slug
//来源： https://so-wp.com/plugins/
function apip_slug($strTitle) {
    $PSL = get_option( 'slug_length', 100 );

    $origStrTitle = $strTitle;
    $containsChinese = false;
    $strRet = "";
    
    if ( get_bloginfo( 'charset' ) !="UTF-8" ) {
        $strTitle = iconv( get_bloginfo( "charset" ), "UTF-8", $strTitle );
    }
    
    if ( $PSL>0 ) {
        $strTitle=substr( $strTitle, 0, $PSL );
    }
    for ( $i = 0; $i < strlen( $strTitle ); $i++ ) {
        $byte1st = ord( substr( $strTitle, $i, 1 ) );
        if ( $byte1st >= 224 && $byte1st <= 239 ) {
            $containsChinese = true;
            $aChinese = sprintf("%02x%02x%02x-", ord(substr( $strTitle, $i, 1 )), ord(substr( $strTitle, $i+1, 1 )), ord(substr( $strTitle, $i+2, 1 )));
            $i += 2;
            $strRet .= $aChinese;
        } else {
            $strRet .= preg_replace( '/[^A-Za-z0-9\-]/', '$0', chr( $byte1st ) );
        }
    }

    if (! $containsChinese ) { 
        $strRet = $origStrTitle;
    }
    $strRet = rtrim($strRet, "-");

    return $strRet;
}

//0.19 给短代码擦屁股
//来源：https://www.wpexplorer.com/clean-up-wordpress-shortcode-formatting/
function apip_fix_shortcodes($content){   
    $array = array (
        '<p>[' => '[', 
        ']</p>' => ']', 
        ']<br />' => ']'
    );
    $content = strtr($content, $array);
    return $content;
}

//0.21 设置chrome内核浏览器的tab颜色
//来源：https://developers.google.com/web/updates/2014/11/Support-for-theme-color-in-Chrome-39-for-Android
function apip_set_theme_color() {
    global $apip_options;
    $color_bg = isset( $apip_options['bg_color'] ) ? $apip_options['bg_color'] : "#ece5df";
    $color_bg = apply_filters('apip_tab_color',$color_bg);
    echo '<meta name="theme-color" content="'.trim($color_bg).'">';
    //echo '<meta name="theme-color" content="#db5945">';
}

/*                                          00终了                             */

/******************************************************************************/
/*        01.解决中文摘要问题                                                     */
/******************************************************************************/

/*                                          01终了                             */

/******************************************************************************/
/*        02.高级编辑选项（就是全部为T/F的选项）                                       */
/******************************************************************************/
//2.1
 /**
 * 作用: 阻止自动生成版本
 * 来源: Amandeep S. Patti
 * URL:  http://www.aspatti.com
 */
function apip_auto_rev_settings()
{
    define('WP_POST_REVISIONS', false);
}

//2.2
 /**
 * 作用: 阻止自动保存
 * 来源: Amandeep S. Patti
 * URL:  http://www.aspatti.com
 */
function apip_auto_save_setting()
{
    wp_deregister_script('autosave');
}

 /**
 * 作用: 是否显示admin bar
 * 来源: Amandeep S. Patti
 * URL:  http://www.aspatti.com
 */

//2.3
function apip_admin_bar_setting($showvar)
{
    global $show_admin_bar;
    if( apip_option_check('show_admin_bar') )
    {
        return $showvar ;
    }
    else
    {
        $show_admin_bar = false;
        return false;
    }
}

//2.4
/**
 * 作用: 后台显示英文,前台显示中文
 * 来源: 自产
 * URL:
 */
function apip_locale( $locale )
{
    if ( is_admin() )
    {
        return $locale ;
    }
    return 'zh_CN' ;
}

//2.5
 /**
 * 作用: 屏蔽后台中的Open Sans.
 * 来源: lifishake原创
 * URL:  http://pewae.com
 */

function apip_block_open_sans ($styles)
{
    $open_sans = $styles->registered{'open-sans'};
    $open_sans->src = null;
    return $styles;
}
//2.6
 /**
 * 作用: 在comment widget中屏蔽作者.
 * 原理: 访客的user_id = 0
 * 来源: lifishake原创
 * URL:  http://pewae.com
 */

 function before_get_comments($args)
{
    $args['user_id'] = 0 ;
    return $args ;
}

//2.7
/**
 * 作用: 搜索结果只有一条记录时直接显示内容
 * 来源: 自产
 * URL:
 */
function redirect_single_post() {
    if (is_search()||is_archive()||is_category()||is_tag()) {
        global $wp_query;
        if ($wp_query->post_count == 1 && $wp_query->max_num_pages == 1) {
            wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
            exit;
        }
    }
}

//2.8
/**
 * 作用: 禁止直接访问wp_comments.php
 * 来源: 小赖子
 * URL:  https://justyy.com/archives/2465
 */
function check_referrer_comment() {
    if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') {
    wp_die('spammer狗带。');
    }
}

//2.9
/**
 * 作用: 在搜索结果中屏蔽page页面
 * 来源: Editorial Staff
 * URL: http://www.wpbeginner.com/wp-tutorials/how-to-exclude-pages-from-wordpress-search-results/
 */
function remove_page_search($query) {
    if ($query->is_search) {
        $query->set('post_type', 'post');
    }
    return $query;
}

//2.10
/**
 * 作用: 外链加密并在新页面打开，内链保持不变
 * 来源: 灵尘子
 * URL: https://www.lingchenzi.com/2019/01/wordpress-waibulianjie-neilian-base64.html
 */
function convert_to_internal_links($content){
    preg_match_all('/\shref=(\'|\")(http[^\'\"#]*?)(\'|\")([\s]?)/',$content,$matches);
    if($matches){
        foreach($matches[2] as $val){
            if(strpos($val,home_url())===false){
                $rep = $matches[1][0].$val.$matches[3][0];
                $new = '"'.home_url().'/gaan/'.base64_encode($val).'" target="_blank"';
                $content = str_replace("$rep","$new",$content);
            }
        }
    }
    return $content;
}

function apip_comment_url($url, $ID) {
    if (""===$url) {
        return "";
    }
    if (is_admin()) {
        return $url;
    }
    $domain = str_replace(array("http://","https://","//"), "", home_url()); 
    if(strpos($url,$domain)===false) {
        $new = home_url().'/gaan/'.base64_encode($url);
        return $new;
    } else {
        return $url;
    }
}

function apip_e2i_redirect() {
    $baseurl = 'gaan';
    if (is_ssl()) {
        $prefix = 'https://';
    } else {
        $prefix = 'http://';
    }
    $request = $prefix.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $hop_base = trailingslashit(trailingslashit(home_url()).$baseurl); //删除末尾的 斜杠/ 符号
    if (substr($request,0,strlen($hop_base)) != $hop_base) return false; //内链
    $hop_key = str_ireplace($hop_base, '', $request);
    if(substr($hop_key, -1) == '/')$hop_key = substr($hop_key, 0, -1);
    if (!empty($hop_key)) {
        $url = base64_decode($hop_key);
        wp_redirect( $url, 302 );
        exit;
    }
}
/*                                          02终了                             */

/******************************************************************************/
/*        03.文字处理                                  */
/******************************************************************************/
//3.1
 /**
 * 作用: header中追加description和keyword.
 * 来源: lifishake原创
 * URL:  http://pewae.com
 */
function apip_desc_tag(){
    global $apip_options;
    if (is_home())
    {
        $description = trim($apip_options['hd_home_text']) ;
        if ( '' == $description )
        {
            $description = get_bloginfo( 'description' ) ;
        }
        $keywords = trim($apip_options['hd_home_keyword']) ;
        if ( '' == $keywords )
        {
            $tags = get_tags(array('orderby' => 'count', 'order' => 'DESC', 'number' => '10'));
            foreach ( $tags as $tag ) :
                $keywords .= $tag->name.',';
            endforeach;
        }

    }
    else if (is_single())
    {
        global $post ;
        $description = substr(strip_tags(strip_shortcodes($post->post_content)),0,240)."...";
        $keywords = "";
        $tags = wp_get_post_tags($post->ID);
        foreach ( $tags as $tag ) :
            $keywords .= $tag->name.',';
        endforeach;
    }
    elseif (is_category())
    {
        $description = category_description();
        $keywords = single_cat_title('', false);
    }
    elseif (is_tag())
    {
        $description = tag_description();
        $keywords = single_tag_title('', false);
    }
    else {
        return ;
    }
    ?>
<meta name="description" content="<?=$description?>" />
<meta name="keywords" content="<?=$keywords?>" />
<?php
}
//3.2
/**
* 作用: 子函数,处理UTF8字符串的最后一个符号.
* 来源: 中文工具箱
* Author URI: http://yan.me/dia
*/
function utf8_trim($str) {

   $len = strlen($str);
   $hex = '';
   for ($i=strlen($str)-1; $i>=0; $i-=1){
       $hex .= ' '.ord($str[$i]);
       $ch = ord($str[$i]);
       if (($ch & 128)==0) return(substr($str,0,$i));
       if (($ch & 192)==192) return(substr($str,0,$i));
   }
   return($str.$hex);
}
//3.3
/**
* 作用: 精确处理中文excerpt
* 来源: 综合WP CN Excerpt和中文工具箱
* URL:  http://yan.me/dia, http://weibo.com/joychaocc
*/
function apip_excerpt( $text )
{
   global $apip_options;
   //erase short codes
   $text = get_the_content();
   $text = strip_shortcodes($text);
   $text = str_replace(']]>', ']]&gt;', $text);
   $text = strip_tags($text );

   //return and spaces
   $search = array(

                  '/<br\s*\/?>/' => "\n",

                  '/\\n\\n/'     => "\n",

                  '/&nbsp;/i'    => '',

                 );

   $text = preg_replace(array_keys($search), $search, $text);

   if( $apip_options['excerpt_length'] > 0 )
   {
       $len = $apip_options['excerpt_length'] ;
   }
   else
   {
       $len = 180 ;
   }
   $text = mb_substr($text,0,$len,'utf-8');

   $text = utf8_trim( $text ).$apip_options['excerpt_ellipsis'] ;
   return $text;
}
/*                                          03终了                             */

/******************************************************************************/
/*        04.GFW有关的内容                                                       */
/******************************************************************************/
//4.1
/**
 * 作用: gravatar本地缓存/v2ex镜像
 * 来源: 邪罗刹
 * URL:  http://www.imevlos.com/
 */
function apip_get_cavatar($source) {
    if( !apip_option_check('local_gravatar') )
    {
        //$source = preg_replace('/\/\/\w+\.gravatar\.com\/avatar/', '//cdn.libravatar.org/avatar', $source);
        //$source = preg_replace('/\/\/\w+\.gravatar\.com\/avatar/', '//cdn.v2ex.com/gravatar', $source);
        $source = preg_replace('/\/\/\w+\.gravatar\.com\/avatar/', '//cn.gravatar.com/gravatar', $source);
        //gravatar.eqoe.cn

    //$source = str_replace( $src, $replace, $source);
        return $source ;
    }
    $pos_sch = strpos( $source, 'http' );
    $src = substr( $source, $pos_sch, strpos( $source, '\'', $pos_sch ) - $pos_sch );
    $tmp = array();
    preg_match('/avatar\/([a-z0-9]+)\?s=(\d+)/',$source, $tmp);  
    $abs = APIP_GALLERY_DIR . 'gravatar_cache/'.$tmp[1];
    $dest = APIP_GALLERY_URL.'gravatar_cache/'.$tmp[1];
    $default =  APIP_GALLERY_URL.'gravatar_cache/default.png';
    $cache_key = 'gravatar_local_'.$tmp[1];

    if (!is_file($abs)||1 != get_transient( $cache_key )){
        //$src = 'http://www.gravatar.com/avatar/'.$tmp[1].'?s=64&d='.$default.'&r=G';
        //$src = $g;
        $response = @wp_remote_get( 
            htmlspecialchars_decode($src), 
            array( 
                'timeout'  => 300, 
                'stream'   => true, 
                'filename' => $abs 
            ) 
        );
        if (is_wp_error($response)) {
            return '<img alt="" src="'.$default.'" class="avatar avatar-'.$tmp[2].'" width="'.$tmp[2].'" height="'.$tmp[2].'" />';
        }
        delete_transient( $cache_key );
        set_transient($cache_key, 1, 60*60*24*91);
    }
    return '<img alt="" src="'.$dest.'" class="avatar avatar-'.$tmp[2].'" width="'.$tmp[2].'" height="'.$tmp[2].'" />';
}
//4.2
/**
 * 作用: 替换emoji服务器地址
 * 来源: 自创
 */
function apip_rep_emoji_url( $url )
{
    global $apip_options;
    if ( !apip_option_check('replace_emoji') )
        return $url;
    return '//coding.net/u/MinonHeart/p/twemoji/git/raw/gh-pages/72x72/' ;
}
/*                                          05终了                             */

/******************************************************************************/
/*        05.控制留言者                                                        */
/******************************************************************************/
//5.1
/**
 * 作用: 替换广告留言
 * 来源: 自产
 * URL:
 */
function hm_check_user ( $comment ) {
    global $apip_options;
    $str_author = $comment['comment_author'];
    $str_author_url = $comment['comment_author_url'];
    $str_author_email = '2b@pewae.com';
    $str_include = $apip_options['blocked_commenters'] ;
    $str_replacement = "关键字【彪】" ;
    $show_random = 'false';
    $forbiddens = explode(',',$str_include);
    $f = 0 ;
    foreach ( $forbiddens as $forbidden ) {
        if ( $forbidden && false != strstr($str_author,$forbidden) ) {
            $f = 1;
            break;
        }
    }
    if ($f != 0) {
        $push_comment = array();
        $comment['comment_o_author'] = $comment['comment_author'];
        $comment['comment_o_email'] = $comment['comment_author_email'];
        $comment['comment_o_url'] = $comment['comment_author_url'];
        $comment['comment_forbidden'] = $forbidden;
        $comment['comment_author'] = $str_replacement ;
        $comment['comment_author_email'] = $str_author_email ;
        if ( 'true' == $show_random ) {
            $rand_posts = get_posts('numberposts=1&orderby=rand');
            $comment['comment_author_url'] = get_permalink($rand_posts[0]->ID);
        }
        else{
            $comment['comment_author_url'] = "" ;
        }
     }
    return $comment;
}

function apip_remember_advertise_comment_details($comment_ID, $approved, $commentdata)
{
    if ( !isset($commentdata['comment_o_author'])||!isset($commentdata['comment_forbidden']) )
    {
        return;
    }
    $comment_meta = array();
    $comment_meta['o_email'] = isset($commentdata['comment_o_email'])?$commentdata['comment_o_email']:'';
    $comment_meta['o_url'] = isset($commentdata['comment_o_url'])?$commentdata['comment_o_url']:'';
    $comment_meta['forbidden'] = $commentdata['comment_forbidden'];
    $comment_meta['o_author'] = $commentdata['comment_o_author'];
    add_comment_meta( $comment_ID, 'apip_hm_original', $comment_meta, true );
}

function apip_show_advertise_comment_details( $actions, $comment ){
    $comment_meta = get_comment_meta($comment->comment_ID,'apip_hm_original',true);
    if ( $comment_meta ) {
        $format = '<span data-comment-id="%d" data-post-id="%d" class="original_key" >%s</span>';
        $actions['original_key'] = sprintf($format, $comment->comment_ID, $comment->comment_post_ID, $comment_meta['o_author']);
        //echo $comment_meta['o_author']/*.' / '.$comment_meta['o_url'] .' / '.$comment_meta['o_email'].' /<b> '.$comment_meta['forbidden'].'</b>'*/;
    }
    return $actions;
}

function apip_replace_triditional_comment_placeholder_text( $default ) {
    $text = '请不要留下无趣的东西浪费大家时间。';
    $default['field'] = sprintf('<p class="comment-form-comment"><label for="comment">Comment</label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" aria-required="true" required="required" placeholder="%s"></textarea></p>', $text);
    return $default;
}

/*                                          05终了                             */

/******************************************************************************/
/*        06.社会化分享                                                         */
/******************************************************************************/

/**
 * 作用: 取得社会化链接（外部接口）
 * 来源: 自产
 * URL:
 */
function apip_get_social()
{
    $ret = '' ;
    $count = 0 ;
    $intro = '<span>分享到:</span>' ;
    if ( apip_option_check('social_share_enable') )
    {
        if ( apip_option_check('social_share_twitter') )
        {
            $ret .= '<a class="sharebar-twitter" rel="nofollow" id="twitter-share" title="Twitter" ></a>' ;
            $count++;
        }
        if ( apip_option_check('social_share_sina') )
        {
            $ret .= '<a class="sharebar-weibo" rel="nofollow" id="sina-share" title="sina" ></a>' ;
            $count++;
        }
        if ( apip_option_check('social_share_tencent') )
        {
            $ret .= '<a class="sharebar-tencent-weibo" rel="nofollow" id="tencent-share" title="tencent" ></a>' ;
            $count++;
        }
        if ( apip_option_check('social_share_googleplus') )
        {
            $ret .= '<a class="sharebar-googleplus" rel="nofollow" id="googleplus-share" title="g+" ></a>' ;
            $count++;
        }
        if ( apip_option_check('social_share_facebook') )
        {
            $ret .= '<a class="sharebar-facebook" rel="nofollow" id="facebook-share" title="facebook" ></a>' ;
            $count++;
        }
        if ( $count > 0 )
        {
            $ret = '<div id="sharebar">'.$intro.$ret.'</div>' ;
        }
    }
    return $ret;
}
/*                                          07终了                             */

/******************************************************************************/
/*        07.自定义SHORTCODE                                                   */
/******************************************************************************/
//7.1自定义标签云
/**
 * 作用: 更好看的标签云
 * 来源: 自产
 * URL:
 */
function apip_tagcloud_page($params = array()) {

    extract(shortcode_atts(array(
        'orderby' => 'count',       // sort by name or count
        'order' => 'DESC',      // in ASCending or DESCending order
        'number' => '169',          // limit the number of tags
        'wrapper' => 'li',      // a tag wrapped around tag links, e.g. li
        'sizeclass' => 'tagged',    // the tag class name
    ), $params));
    // initialize
    $ret = '<ul class="tagcloud">';
    $min = 9999999; $max = 0;
    // fetch all WordPress tags
    $tags = get_tags(array('orderby' => $orderby, 'order' => $order, 'number' => $number));
    // get minimum and maximum number tag counts
    $index = 0;

    foreach ($tags as $tag) {
        if ( $index < 3 ) {
            $tag->parent = 6;
        }
        elseif( $index < 13 )  {
            $tag->parent = 5;
        }
        elseif( $index < 39 )  {
            $tag->parent = 4;
        }
        elseif( $index < 91 ) {
            $tag->parent = 3;
        }
        elseif( $index < 143 ) {
            $tag->parent = 2;
        }
        else {
            $tag->parent = 1;
        }
        $index++ ;
    }

    shuffle($tags) ;
    // generate tag list
    foreach ($tags as $tag) {
        $url = get_tag_link($tag->term_id);
        $title = $tag->count . ' article' . ($tag->count == 1 ? '' : 's');
        $class = $sizeclass . $tag->parent. ' lk'.$tag->term_id%8 ;
        $ret .= ($wrapper ? "<$wrapper>" : '') ;
        $ret .= "<a href='{$url}' rel='external nofollow' class='{$class}' title='{$title}'>";
        $ret .= "{$tag->name}</a>" ;
        $ret .= ($wrapper ? "</$wrapper>" : '');
    }
    $ret = str_replace(get_bloginfo('url'), '', $ret);
    $ret .= '</ul>' ;
    return $ret ;
}
//7.2自定义友情链接页
/**
 * 作用: 取出一定时间内被博主回复最多的留言者
 * 来源: 自产
 * URL:
 */
function apip_link_page(){
    $links = apip_get_links();
    $ret = '<ul class = "apip-links">' ;
    foreach ( $links as $link ){
        $parm = sprintf( '<li><div class="commenter-link vcard">%1$s</div><a href="%2$s" target="_blank" class="url">%3$s</a></li>',
                            get_avatar( $link->comment_author_email, 64),
                            apip_option_check('redirect_external_link') ? apip_comment_url($link->comment_author_url,0) : $link->comment_author_url,
                            $link->comment_author) ;
        $ret.= $parm;
    }
    $ret.='</ul>';
    echo $ret;
}
//7.3自定义归档页
/**
 * 作用: JQuery效果的归档页
 * 来源: http://skatox.com/blog/
 * URL: http://skatox.com/blog/jquery-archive-list-widget
 */
function apip_build_cat_html( $cat, $is_child = 0 ) {
    global $cat_relation;
    $child_html = '';
    $exlude = array();
    if ( array_key_exists($cat->term_id, $cat_relation) ) {
        $child_html .= '<ul class="achp-child apip-no-disp">';
        foreach ( $cat_relation[$cat->term_id] as $child ) {
            /*递归*/
            $child_html .= apip_build_cat_html( $child, 1 );
        }
        $child_html .= '</ul>';
        $exlude = $cat_relation[$cat->term_id];
        unset($cat_relation[$cat->term_id]);
    }

    $post_html = '';
    $getpostsargs = array();
    $getpostsargs['posts_per_page'] = -1;
    $getpostsargs['orderby'] = 'date';
    $getpostsargs['order'] = 'ASC';
    $getpostsargs['category__in'] = array($cat->term_id);
    $getpostsargs['post_status'] = 'publish';
    $getpostsargs['post_type'] = 'post';
    $posts = get_posts($getpostsargs);
    if ( !empty($posts) ) {
        $post_html .= '<ul class="achp-child apip-no-disp">';
        foreach( $posts as $post ) {
            $post_html.= "<li class=\"achp-parent apip-no-disp\">";
            $post_html.= sprintf( "<a href=\"%s\" title=\"%s\">%s</a>",
                get_permalink($post->ID),
                htmlspecialchars($post->post_title),
                $post->post_title
            );
            $post_html.='</li>';//achp-child
        }
        $post_html .= '</ul>';
    }
    $html .= sprintf("<li class = \"achp-parent %s \"><a class=\"achp-sig\" href=\"#\" title=\"%s\"><span class=\"achp-symbol suffix \">[+]</span></a><a href=\"%s\" title=\"%s\">%s<span class=\"achp_count\">(%s)</span></a>%s%s</li>",
                    $is_child ? 'apip-no-disp' : '',
                    $cat->cat_name,
                    get_category_link($cat->term_id),
                    $cat->cat_name,
                    $cat->cat_name,
                    $cat->count,
                    $child_html,
                    $post_html
                    );

    return $html;
}
function apip_archive_page() {
    global $wpdb;

    //
    /* 日期归档 */
    $sql_total_count = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as `count`, GROUP_CONCAT(ID) AS `posts` FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC LIMIT 99999";

    $ret = '<h2 class="apip-h2">日期归档</h2><ul class="achp-widget">';
    $all_result = $wpdb->get_results($sql_total_count);
    $last_year = "";
    $month_str="";
    $total_month = "";
    $year_count=0;
    $i=0;
    $result_count = count($all_result);
    foreach ($all_result as $result) {
        
        //-------------
        $monthLink = get_month_link($result->year, $result->month);
        $monthFormat = $result->month < 10 ? '0'.$result->month : $result->month;
        $month_str .= "<li class=\"achp-parent apip-no-disp\" >" .
                "<a class=\"achp-sig\" href=\"#\" title=\"{$monthFormat}\"><span class=\"achp-symbol suffix\">[+]</span></a>".
                "<a href=\"{$monthLink}\" title=\"{$monthFormat}\">{$monthFormat}({$result->count})</a>";
        $month_str .= "<ul class = \"achp-child apip-no-disp\">";
        $includes = explode(",",$result->posts);
        $getpostsargs = array();
        $getpostsargs['posts_per_page'] = -1;
        $getpostsargs['orderby'] = 'date';
        $getpostsargs['order'] = 'ASC';
        $getpostsargs['include'] = $includes;
        $posts = get_posts($getpostsargs);
        foreach( $posts as $post ) {
            $month_str.= "<li class=\"achp-child apip-no-disp\">";
            $month_str.= sprintf( "<a href=\"%s\" title=\"%s\">%s</a>",
                get_permalink($post->ID),
                htmlspecialchars($post->post_title),
                $post->post_title
            );
            $month_str.='</li>';//achp-child
        }
        $month_str .= "</ul>";
        $month_str .= "</li>";//achp_months
        //-------------
        if (((!empty($last_year) && $result->year !== $last_year ) || $i == $result_count - 1)&& $year_count > 0) {
            $yearLink = get_year_link($last_year);
            $ret .= "<li class=\"achp-parent\">".
                    "<a class=\"achp-sig\" title=\"{$last_year}\" href=\"#\">".
                    "<span class=\"achp-symbol suffix\">[+]</span>".
                    "</a><a href=\"{$last_year}\" title=\"{$last_year}\">".
                    "{$last_year} ({$year_count})".
                    "</a><ul class=\"achp-child apip-no-disp\">";
            $ret .= $total_month;
            $ret .= "</ul></li>";
            $total_month = $month_str;
            $month_str = "";
            $year_count = $result->count;
            if($i == $result_count - 1 && $result->year != $last_year ) {
                $yearLink = get_year_link($result->year);
                $ret .= "<li class=\"achp-parent\">".
                        "<a class=\"achp-sig\" title=\"{$result->year}\" href=\"#\">".
                        "<span class=\"achp-symbol suffix\">[+]</span>".
                        "</a><a href=\"{$result->year}\" title=\"{$result->year}\">".
                        "{$result->year} ({$result->count})".
                        "</a><ul class=\"achp-child apip-no-disp\">";
                $ret .= $total_month;
                $ret .= "</ul></li>";
            }
        } else {
            $total_month .= $month_str;
            $year_count += $result->count;
            $month_str = "";
        }
        $last_year = $result->year;
        $i++;
    }//for each year result
    $ret .= "</ul>";//achp-widget

    /* 类别归档 */
    $all_cats = get_categories(
            array(
                'type' => 'post',
                'orderby' => 'term_id',
                'order' => 'ASC',
                'hide_empty' => 1,
                'hierarchical' => 1,
                'pad_counts' => true,
            )
        );
    global $cat_relation;
    $cat_relation = array();
    foreach ( $all_cats as $cat ) {
        if ( $cat->parent !== 0 ) {
            $cat_relation[$cat->parent][] = $cat;
        }
    }
    $ret .= '<h2 class="apip-h2">分类归档</h2><ul class="achp-widget">';
    foreach ( $all_cats as $cat ) {
        if ( $cat->parent === 0 )
            $ret .= apip_build_cat_html($cat, 0 );
    }
    $ret .= '</ul>';//ul achp-widget
    echo $ret;
}
/*                                          07终了                             */

/******************************************************************************/
/*        08.比较复杂的设置                                                      */
/******************************************************************************/
//8.1 codehighlight相关（0.14）20191101修正，改为js内自行调用函数
/**
 * 作用: 在页脚激活JS
 * 来源: 自产
 * URL:
 */
function apip_footer_actions()
{
    /*
    global $apip_options ;
    //9.1
    if ( (in_category('code_share') || has_tag('testcode')) && apip_option_check('apip_codehighlight_enable') )
    {
?>
        <script type="text/javascript">
            window.onload = function(){prettyPrint();};
        </script>
<?php
    }*/
}

/**
 * 作用: 过滤引号
 * 来源: 自产
 * URL:
 */
function wch_stripslashes($code){
    $code=str_replace('\\"', '"',$code);
    $code=htmlspecialchars($code,ENT_QUOTES);
    return $code;
}
/**
 * 作用: 追加prettyprint风格
 * 来源: 自产
 * URL:
 */
function apip_code_highlight($content) {
    $result = preg_replace_callback('/<pre(.*?)>(.*?)<\/pre>/is', function ($matches) {
        return '<pre class=" prettyprint ">' . wch_stripslashes($matches[2]) . '</pre>';
   }, $content);
   return $result ;
}

function wch_stripaddr($code){
    $code = str_replace(array("&#038;","&amp;"), "&", $code); 
    return $code;
}

function so_handle_038($content) {
    $result = preg_replace_callback('/<pre(.*?)>(.*?)<\/pre>/is', function ($matches) {
        return '<pre class=" prettyprint ">' . wch_stripaddr($matches[2]) . '</pre>';
   }, $content);
   return $result ;
}

//8.2 Lazyload相关
/**
 * 作用: lazyload过滤,替换src为data-src
 * 来源: 自产
 * URL:
 */
function apip_lazyload_filter( $content )
{
    if (is_feed()) {
        return $content;
    }
    $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
    $dom = new DOMDocument();
    @$dom->loadHTML($content);

    foreach ($dom->getElementsByTagName('img') as $node) {
        $oldsrc = $node->getAttribute('src');
        $node->setAttribute("data-src", $oldsrc );
        $node->setAttribute("data-unveil", "true" );
        $newsrc = APIP_PLUGIN_URL.'img/blank.gif';//'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        $node->setAttribute("src", $newsrc);
    }
    $newHtml = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom->saveHTML()));
    return $newHtml;
}

//8.3 范围内跳转
/**
 * 作用: 范围内查找的动作追加.
 * 来源: 自产
 * URL:
 */
function apip_keep_query(){
    global $wp_query;

    if (isset($_SESSION['last_tax'])) {
        $old_tax = $_SESSION['last_tax'];
    }
    else {
        $old_tax = '';
    }
    $new_tax='';
    if (is_search()||is_archive()) {
        if ( is_search() ){
            $new_tax = "搜索结果:" . get_search_query( false ) ;
        }
        else if ( is_category() ){
            $new_tax = "分类:" . single_cat_title( '', false );
        }
        else if ( is_tag() ){
            $new_tax = "标签:" . single_tag_title( '', false );
        }
        else if ( is_year() ) {
            $new_tax = "年:" . get_the_date('Y') ;
        }
        else if ( is_month() ) {
            $new_tax = "月:" . get_the_date('F Y');
        }
        else if ( is_day() ) {
            $new_tax = "日:" . get_the_date(get_option('date_format'));
        }
        else {
            $_SESSION['last_tax'] = '';
            $_SESSION['tax_ids'] = array();
            return;
        }
        if ($new_tax != $old_tax) {
            $vars = $wp_query->query_vars;
            $vars['posts_per_page'] = 9999;
            $vars['order'] = "ASC";
            $myquery = new WP_Query( $vars );
            if ($myquery->post_count == 1 && $myquery->max_num_pages == 1){
                wp_reset_postdata();
                $_SESSION['last_tax'] = '';
                $_SESSION['tax_ids'] = array();
                return;
            }
            $_SESSION['last_tax'] = $new_tax;
            $_SESSION['tax_ids'] = wp_list_pluck( $myquery->posts, 'ID' );
            wp_reset_postdata();
        }
    }
    else if (!is_single()) {
        $_SESSION['last_tax'] = '';
        $_SESSION['tax_ids'] = array();
    }
    else {
        //single
        $ID = get_the_ID();
        if (empty($old_tax)||!isset($_SESSION['tax_ids'])||count($_SESSION['tax_ids']) == 0) {
            return;
        }      
        if (FALSE===array_search($ID, $_SESSION['tax_ids'])) {
            $_SESSION['last_tax'] = '';
            $_SESSION['tax_ids'] = array();
            return;
        }
    }
}
//8.4 邮件回复
/**
 * 作用: comment有reply时,通过邮件通知留言发布者.
 * 来源: Comment Email Reply
 * URL:  http://kilozwo.de/wordpress-comment-email-reply-plugin
 */
function apip_comment_inserted($comment_id, $comment_object) {
    if ($comment_object->comment_parent > 0) {
        global $apip_options;
        /*$color_border = isset( $apip_options['border_color'] ) ? $apip_options['border_color'] : "#8a8988";
        $color_link = isset( $apip_options['link_color'] ) ? $apip_options['link_color'] : "#1a5f99";
        $color_font = isset( $apip_options['font_color'] ) ? $apip_options['font_color'] : "#0a161f";
        $color_bg = isset( $apip_options['bg_color'] ) ? $apip_options['bg_color'] : "#ece5df";*/
        $color_border = "#EDEFED";
        $color_link = "#660000";
        $color_font = "#000200";
        $color_bg = "#F7FCF8";
        $comment_parent = get_comment($comment_object->comment_parent);
        $bg_head = '<div style="border:3px solid '.$color_border.'; border-radius: 5px; margin: 1em 2em; background:'.$color_bg.'; font-size:14px;"><div style=" margin:0 auto; padding: 15px; margin: 15px; color: '.$color_font.'; ">' ;
        $content_border_head = '<p style="padding: 5px 20px; margin: 5px 15px 20px; border-bottom: 2px dashed '.$color_border.'; border-radius: 5px;">' ;
        $a_style = 'color:'.$color_link.'; text-decoration: none;';
        $random_posts = apip_random_post( get_the_ID(), 1 ) ;
        foreach ( $random_posts as $random_post ) :
            $random_link = get_permalink( $random_post->ID ) ;
        endforeach;
        $mailcontent = "<p style=\"display:none\">{$comment_object->comment_content}</p>";
        $mailcontent .= '<p>亲爱的 <b style=" font-weight:800; padding:0 3px ;">'.$comment_parent->comment_author.'</b>， 您的留言：</p>' ;
        $mailcontent .= $content_border_head.$comment_parent->comment_content.'</p><p>有了新回复：</p>';
        $mailcontent .= $content_border_head.$comment_object->comment_content.'</p>';
        $mailcontent .= sprintf( '<p>欢迎<a style="%4$s" href="%1$s" >继续参与讨论</a>或者<a style="%4$s" href="%2$s">随便逛逛</a>。<a style="%4$s" href="%3$s">「破襪子」</a>期待您再次赏光。</p>', get_comment_link( $comment_object->comment_ID ), $random_link, get_bloginfo('url'), $a_style ) ;
        $mailcontent = $bg_head.$mailcontent.'</div></div>' ;

        $headers  = 'MIME-Version: 1.0' . "\r\n";

        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

        $headers .= 'From: 破襪子站长 <dazhi.pewae@gmail.com>'. "\r\n";

        //$headers .= 'Bcc: lifishake@gmail.com'. "\r\n";

        wp_mail($comment_parent->comment_author_email,'您在『'.get_option('blogname').'』 的留言有了新回复。',$mailcontent,$headers);
    }
}

function apip_is_debug_mode()
{
    if (isset( $_SERVER['REDIRECT_TMP'] ) && strpos($_SERVER['REDIRECT_TMP'], "xampp" ) > 0)
    {
        return 1;
    }
    return 0;
}

/**
* 作用: 取得的内容太多，影响数据库效率，只保留有效字段。
* 来源: 自作
*/
function apip_slim_dou_cache($cache, $type) {
    $ret = array();
    $keys_movie = array('msg','code','request','id','images','rating','alt','title','directors','casts','genres','year',);
    $keys_imdb = array('Poster','imdbRating','Title','Director','Actors','Genre','Country','Year');
    $keys_book = array('msg','code','request','image','id','rating','alt','title','author','translator','pubdate','publisher','price');
    $keys_book_series = array('msg','code','request','count','total','books');
    $keys_music = array('msg','code','request','image','id','rating','alt','title','author','attrs');
    $keys_use = array();
    switch ($type) {
        case 'movie':
            $keys_use = $keys_movie;
            break;
        case "imdb":
            $keys_use = $keys_imdb;
            break;
        case "book":
            $keys_use = $keys_book;
            break;
        case "book_series":
            $keys_use = $keys_book_series;
            break;
        case "music":
            $keys_use = $keys_music;
            break;
        default:
            $ret = $cache;
            break;
    }
    if (!empty($keys_use)) {
        for ($i=0; $i<count($keys_use); $i++ ) {
            if (array_key_exists($keys_use[$i], $cache)) {
                if ("books"===$keys_use[$i]) {
                    $book_content = array();
                    for ($j=0; $j<count($cache["books"]); $j++) {
                        $book_content[$j] = apip_slim_dou_cache($cache["books"][$j],"book");
                    }
                    $ret["books"] = $book_content;
                }
                else {
                    $ret[$keys_use[$i]] = $cache[$keys_use[$i]];
                }
                
            } else {
                $ret[$keys_use[$i]] = 0;
            }
        }
    }
    return $ret;
}

/**
* 作用: theimdbapi.org取得电影资料，用于豆瓣无资料的电影。
* 来源: 受大发启示，自作
* API格式： https://www.omdbapi.com/?i=tt3896198&apikey=36edb41f
*/
function apip_imbd_detail($atts, $content = null){
    $atts = shortcode_atts( array( 'id' => '0', 'cname'=>'','alias'=>'','score'=>'','nipple'=>'no' ), $atts );
    extract( $atts );
    $cache_key = 'imdb_'.$id;
    $content = get_transient($cache_key);
    global $apip_options;
    //for local debug
    if ( apip_is_debug_mode() ){
        //$content = apip_debug_imdb_content();
    }
    if ( !$content )
    {
        $apikey = $apip_options['omdb_key'];
        if( empty($apikey) )
        {
            return false;
        }
        $url = "https://www.omdbapi.com/?i=".$id."&apikey=".$apikey;


        delete_transient($cache_key);

        $response = @wp_remote_get($url);
        if (is_wp_error($response))
        {
            return false;
        }
        $content = json_decode(wp_remote_retrieve_body($response),true);
        $content = apip_slim_dou_cache($content, "imdb");
        set_transient($cache_key, $content, 60*60*24*30*6);
    }

    $img_src = APIP_GALLERY_DIR . 'douban_cache/'.$id.'.jpg';
    $img_url = $content['Poster'];
    if ( !is_file($img_src) /*&& !apip_is_debug_mode()*/ ) {
        $response = @wp_remote_get( 
            htmlspecialchars_decode($img_url), 
            array( 
                'timeout'  => 300, 
                'stream'   => true, 
                'filename' => $img_src 
            ) 
        );
        if ( is_wp_error( $response ) )
        {
            return false;
        }
        /*
        if (!@copy(htmlspecialchars_decode($img_url), $img_src))
        {
            $errors= error_get_last();
            return false;
        }
        */
        $image = new Apip_SimpleImage();
        $image->load($img_src);
        $image->resize(100, 150);
        $image->save($img_src);
    }
    $imdb_url = "https://www.imdb.com/title/".$id;
    $img_url = APIP_GALLERY_URL.'douban_cache/'. $id .'.jpg';

    $template = '<div class="apip-item"><div class="mod"><div class="%5$s"><div class="apiplist-post">%1$s</div><div class="title">%2$s</div><div class="rating">%3$s</div><div class="abstract">%4$s</div></div></div></div>';

    $subject_class="v-overflowHidden doulist-subject";//5
    $img_str=sprintf('<img src="%1$s" alt="%2$s"></img>',
                        $img_url,
                        base64_encode($content["Title"]));//1
    $title_str=sprintf('<a href="%1$s" class="cute" target="_blank" rel="external nofollow">%2$s</a>',
                        $imdb_url,
                        $cname !== ''?$cname:$content["Title"]);//2
    $rating_str="";//3
    $star_dou=sprintf('<span class="dou-stars-%s"></span>',
                        round(floatval($content["imdbRating"])));    
    $star_my="";
    
    $str_rnum="";
    $abstract_str="";//4
    
    if ( $score !== '' && $score !="99" ) {
        $subject_class .= " my-score-".$score;
        $str_rnum = sprintf('<span class="rating_nums">(%1$s / %2$s)</span>',$score, $content["imdbRating"]);
        $star_my = sprintf('<span class="my-stars-%s"></span>', $score);
    }
    else  {
        $str_rnum = sprintf('<span class="rating_nums">(%1$s)</span>', $content["imdbRating"]);
    }
    $rating_str = sprintf('<span class="allstardark">%1$s%2$s</span>%3$s', $star_dou,$star_my,$str_rnum);

    $str_director=sprintf('<span class="director">导演：%s</span>', $content["Director"]);
    $str_casts=sprintf('<span class="casts">演员：%s</span>', str_replace(',','/',$content["Actors"]));   
    $str_genres=sprintf('<span class="genres">类型：%s</span>', str_replace(',','/',$content["Genre"]));
    $str_year = sprintf('<span class="year">年份：%s</span>', $content["Year"]);
    $abstract_str = $str_director.$str_casts.$str_genres.$str_countries.$str_year;

    if ("yes"===$nipple) {
        $abstract_str .= '<span class="feature">奶</span>';
    }

    $out = sprintf($template, $img_str, $title_str, $rating_str, $abstract_str, $subject_class);
    return $out;

}

/**
* 作用: 把豆瓣返回array的内容变成字符串
* 参数: data        array   数据源
*       key         string  查找的array key
*       key_name    string  如果找到的内容有子项目,要返回的子项目的关键字
*       unknown_str string  遇到未知项转换的内容
*/
function apip_convert_dou_array_to_string($data, $key, $name_key="name", $unknown_str="未知") {
    $ret = '';
    if (array_key_exists($key, $data) && is_array($data[$key])) {
        $subs = $data[$key];
        if ( count($subs)>1 ) {
            if ( is_array($subs[0]) && array_key_exists($name_key, $subs[0])) {
                $items = wp_list_pluck($subs, $name_key);
                $ret .= implode('/ ', $items);
            } else {
                $ret .= implode('/ ', $subs);
            }
        } else if (!empty($subs)) {
            if (is_array($subs[0]) && array_key_exists($name_key, $subs[0])) {
                $ret .= $subs[0][$name_key];
            } else {
                $ret .= $subs[0];
            }
        } else {
            $ret .= $unknown_str;
        }
    } elseif (array_key_exists($key, $data)) {
        $ret .= $data[$key];
    } else {
        $ret .= $unknown_str;
    }
    return $ret;
}

//8.6游戏资料
/**
* 作用: 从giantbomb.com取得游戏资料，显得正规。
* 来源: 受大发启示，自作
* API格式：https://www.giantbomb.com/api/game/THE_GAME_ID/?api_key=YOUR_TOKEN&format=json&field_list=site_detail_url,genres,image,platforms,original_release_date,name,publishers
*/
function apip_game_detail($atts, $content = null) {
    $atts = shortcode_atts( array( 'id' => '0', 'cname'=>'','alias'=>'', 'ename'=>'', 'jname'=>'', 'year'=>'', 'download'=>'','platform'=>'','publisher'=>'','genres'=>'','poster'=>'' ), $atts );
    extract( $atts );
    global $apip_options;
    $token = $apip_options['gaintbomb_key'];
    if (!$token) {
        return;
    }
    $nodata = 0;
    if( $id == 'x' ) {
        $id = 'nodata_'.get_the_ID();
        $nodata = 1;
    }

    $cache_key = 'game_'.$id;
    $content = get_transient($cache_key);
    
    $arg = array();
    //20200325 增加对代理的使用
    $proxy = new WP_HTTP_Proxy();
        if ($proxy->is_enabled()) {
        $proxy_str = $proxy->host().":".$proxy->port();
        $stream_default_opts = array(
            'http'=>array(
                'proxy'=>$proxy_str,
                'request_fulluri' => true,
                'user_agent'=>'API Test UA',
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ),
            );
            $cxContext = stream_context_create($stream_default_opts);
        }
        else {
            $cxContext = stream_context_create(['http' => ['user_agent' => 'API Test UA']]);
        }
    
    //$context = stream_context_create(['http' => ['user_agent' => 'API Test UA']]);
    if ( !$content )
    {
        if ( $nodata  ) {
            $content['error'] = 'OK';
            $content['results']['image']['thumb_url'] = $poster;
            $content['results']["site_detail_url"] = get_the_permalink();
            $content['results']["name"] = $ename!=''?$ename:($cname!=''?$cname: get_the_title());
        } else {
            $url = "http://www.giantbomb.com/api/game/".$id."?api_key=".$token."&format=json&field_list=site_detail_url,genres,image,platforms,original_release_date,name,publishers";
           
            delete_transient($cache_key);
            //从链接取数据            
            $response = @file_get_contents($url, false, $cxContext);
            if ($response) {
                $content = json_decode($response,true);
                set_transient($cache_key, $content, 60*60*24*30*6);
            } else {
                return false;
            }

            /*此处为igdb备用代码，未完成。因为giantbomb禁止wordpress访问API，所以此处代码暂不使用wp_remote_get。20200325
            $url = "https://api-v3.igdb.com/games/";
            $args = array(
                'timeout' => 3000,
                'sslverify' => false,
                'headers' => array(
                    'Accept' => 'application/json',
                    'user-key' => "3f704634aa13b081b29e2e469502f444",
                ),
                'body' => array(
                    'fields' =>'*',
                    'id' => '1942',
                ),
);
            $response = wp_remote_get($url, $args);
            if (is_wp_error( $response )) {
                return false;
            }
            $cache = json_decode(wp_remote_retrieve_body($response),true);
            */
        
        }
    }//content
    if ( $content['error'] != 'OK' ) {
        return '';
    }
    $data = $content['results'];
    $img_src = APIP_GALLERY_DIR . 'game_poster/'.$id.'.jpg';
    $img_url = $data['image']['thumb_url'];
    //拷贝到本地，该网站需要验证用户信息，所以不能直接使用@copy
    if (  !is_file($img_src) ) {
        $imageString = @file_get_contents($img_url, false, $cxContext);
        $save = file_put_contents($img_src, $imageString);
        if ( $nodata ) {
            $image = new Apip_SimpleImage();
            $image->load($img_src);
            $image->resize(100, 150);
            $image->save($img_src);
        }
        if( $save ) {
            $img_url = APIP_GALLERY_URL.'game_poster/'. $id .'.jpg';
        }
    } else {
        $img_url = APIP_GALLERY_URL.'game_poster/'. $id .'.jpg';
    }

    $template = '<div class="apip-item"><div class="mod"><div class="%4$s"><div class="apiplist-post">%1$s</div><div class="title">%2$s</div><div class="abstract">%3$s</div></div></div></div>';

    $subject_class="v-overflowHidden doulist-subject";//4
    $img_str=sprintf('<img src="%1$s" alt="%2$s"></img>',
                        $img_url,
                        base64_encode($data["name"]));//1
    $title_str=sprintf('<a href="%1$s" class="cute" target="_blank" rel="external nofollow">%2$s</a>',
                        $data["site_detail_url"],
                        $cname != ''? $cname : $data["name"]);//2
    $str_rnum="";
    $abstract_str="";//3
    
    if ( $score !== '' ) {
        $subject_class .= " my-score-".$score;
    }

    $str_name = '';
    if ( $cname !== '') {
        $str_name .= sprintf('<span class="game-name">英文名：%s</span>', $data["name"]);
    }
    if ( $jname !== '' ) {
        $str_name .= sprintf('<span class="game-name">日文名：%s</span>', $jname);
    }
    if ( $alias !== '' ) {
        $str_name .= sprintf('<span class="game-name">别名：%s</span>', str_replace(',','/ ',$alias));
    }
    
    $str_pubdate = '';
    if ( $year !== '' ) {
        $str_the_pubdate = $year;
    } else {
        if (array_key_exists("original_release_date",$data)) {
            $str_the_pubdate = substr($data['original_release_date'],0,10);
        } else {
            $str_the_pubdate = "不明";
        }
    }
    $str_pubdate .= sprintf('<span class="pubdate">发售日期：%s</span>', $str_the_pubdate);

    $str_publisher = '';
    if ( $publisher !== '' ) {
        $str_the_publisher = $publisher;
    } else {
        $str_the_publisher = apip_convert_dou_array_to_string($data, 'publishers', 'name', '不明');
    }
    $str_publisher = sprintf('<span class="publisher">发行商：%s</span>', $str_the_publisher);

    $str_genres = '';
    if ( $genres !== '' ) {
        $str_the_genres = $genres;
    } else {
        $str_the_genres = apip_convert_dou_array_to_string($data, 'genres', 'name', '不明');
    }
    $str_genres = sprintf('<span class="genres">类型：%s</span>', $str_the_genres);

    $str_platform = '';
    if ( $platform !== '' ) {
        $str_the_platform = $platform;
    } else {
        $str_the_platform = apip_convert_dou_array_to_string($data, 'platforms', 'abbreviation', '不明');
        $str_the_platform = str_replace( array('NES','GEN','SNES'), array('FC','MD','SFC'), $str_the_platform);
    }
    $str_platform = sprintf('<span class="platform">类型：%s</span>', $str_the_platform);

    $str_download = '';
    if ( $download !== '' ){
        $str_download =sprintf('<span class="platform"><a href="%s" class="cute" target="_blank" rel="external nofollow">下载</a></span>',$download);
    }
    
    $abstract_str = $str_name.$str_pubdate.$str_publisher.$str_genres.$str_platform.$str_download;

    $out = sprintf($template, $img_str, $title_str, $abstract_str, $subject_class);
    return $out;

}

//8.7 发帖时天气信息
/**
* 作用: post第一次发布或者从draft转成publish的时候，从和风天气heweather.com取得该时点的天气信息，保存到post_meta中。
* 主题调用相关函数，显示当日天气信息。
* TBD：widget支持，日后再说。
* 来源: 自作
* API格式：https://free-api.heweather.com/s6/weather/now?location=地点信息&key=key
* 资料：https://www.heweather.com/documents/api/s6/weather-now --和风天气时事天气API文档
* 资料：https://codex.wordpress.org/Post_Status_Transitions -- WP钩子说明
*/

function apip_save_heweather ( $post )
{
    $meta_key = 'apip_heweather';
    global $apip_options;
    $token = $apip_options['heweather_key'];
    if (!$token) {
        return;
    }
    if ( false != get_post_meta($post->ID, $meta_key, false) )
    {
        return;
    }
    $weather = array();
    $addr = "https://free-api.heweather.com/s6/weather/now?key=".$token."&location=CN101070209";
    $args = array(
        'sslverify' => false,
        'headers' => array(
          'Content-Type' => 'application/json;charset=UTF-8',
          'Accept' => 'application/json',
        ),);
    $response = @wp_remote_get($addr,$args);

    if ( is_wp_error($response) )
    {
        return;
    }
    else {
        $cache = json_decode(wp_remote_retrieve_body($response),true);
    }

    $got = $cache["HeWeather6"][0];
    $weather["time"] = $got["update"]["loc"];
    $weather["result"] = $got["now"];
    add_post_meta($post->ID, $meta_key, $weather, false);
}


function apip_heweather_field()
{
    global $post;

    if (get_post_type($post) != 'post') return false;

    $value = get_post_meta($post->ID, 'apip_heweather', true);
    $check = 0;
    if ( empty($value) )
    {
        $check= 0;
        $str = 'none';
    }
    else if(!empty($value[0]['error']))
    {
        $check = 1;
        $str = 'error';
    }
    else {
        $str = apip_get_heweather();
    }
    ?>
        <div class="misc-pub-section">
            <label><input type="checkbox"<?php echo ($check==1 ? ' checked="checked"' : null) ?> value="1" name="apip_heweather" />和天气：<?php echo $str;  ?></label>
        </div>
    <?php
}

add_action( 'save_post', 'apip_heweather_retrieve');

function apip_heweather_retrieve($postid)
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

    if ( !current_user_can( 'edit_page', $postid ) ) return false;

    if (!isset($_POST)) return false;

    if(empty($postid) || !isset($_POST['post_type']) || $_POST['post_type'] != 'post' ) return false;

    if(isset($_POST['apip_heweather'])){
        delete_post_meta($postid, 'apip_heweather');
        apip_save_heweather(get_post($postid));
    }
}

function apip_weather_meta_box( $post ){
    if (get_post_type($post) != 'post') return false;

    $value = get_post_meta($post->ID, 'apip_heweather', true);
    if ( empty($value) )
    {
        $str = 'none';
    }
    else if(!empty($value[0]['error']))
    {
        $str = 'error';
    }
    else {
        $str = apip_get_heweather('plain');
    }
    ?>
        <div class="misc-pub-section">
            <label>保存的和天气：<input type="text" name="heweather" value=" <?php echo $str; ?> "></label><button class="button"  type="button" name="apipweatherbtn" id="<?php echo $post->ID; ?>" wpnonce="<?php echo wp_create_nonce('apip-heweather-'.$post->ID);  ?>" >更新天气</button>
        </div>
    <?php
    /*剩下的看js的了*/
}

/**
* 作用: 按下button后，触发apip-admin.js里的ajax函数，这里是ajax的回调。
* 来源: 自作
* API格式：https://free-api.heweather.com/s6/weather/now?location=地点信息&key=key
* 资料：https://www.heweather.com/documents/api/s6/weather-now --和风天气时事天气API文档
* 资料：https://codex.wordpress.org/Post_Status_Transitions -- WP钩子说明
*/
function apip_weather_manual_update(){
    if ( !wp_verify_nonce($_GET['nonce'],"apip-heweather-".$_GET['id']))
        die();
    /*注意，这个时候没有全局的$post！*/
    $post_id = $_GET['id'];
    $post = get_post($post_id);
    delete_post_meta($post_id, 'apip_heweather');
    apip_save_heweather($post);
    $str = apip_get_heweather('plain', $post_id);
    /*把取得的字符串再传给ajax的success，让它动态更新天气框*/
    $resp = array('title' => 'here is the title', 'content' => $str) ;
    wp_send_json($resp) ;
}

//8.8 留言前答题
/*
作用：
1. 在后台编辑画面增加一个meta box，用于追加问题。在保存post的时候把问题存成post_meta。
2. 主题在显示留言框前调用接口，显示问题。如果选择正确，则显示留言框，如果选择错误，无法显示留言框。
来源：https://github.com/nrkbeta/nrkbetaquiz
license：GPLv3
修改内容：css风格，js简化，汉化，插件风格统一。
*/
function apip_commentquiz_init() {
    add_meta_box('apipcommentquiz', '留言验证问题', 'apip_commentquiz_meta_box', 'post', 'side', 'high');
}

function apip_commentquiz_meta_box($post)
{
    //插入一个空问题
    $questions = array_pad(get_post_meta($post->ID, 'apipcommentquiz'), 1, array());
    $addmore = '增加一个问题+';
    $correct = '正确答案';
    $answer = '答案';

  foreach($questions as $index => $question){
    $title = '问题'. ' ' . ($index + 1);
    $text = esc_attr(empty($question['text'])? '' : $question['text']);
    $name = 'apipcommentquiz' . '[' . $index . ']';

    echo '<div style="margin-bottom:1em;padding-bottom:1em;border-bottom:1px solid #eee">';
    echo '<label><strong>' . $title . ':</strong><br /><input type="text" name="' . $name . '[text]" value="' . $text . '"></label>';
    for($i = 0; $i<3; $i++){
      $check = checked($i, isset($question['correct'])? intval($question['correct']) : 0, false);
      $value = isset($question['answer'][$i])? esc_attr($question['answer'][$i]) : '';

      echo '<br /><input type="text" name="' . $name . '[answer][' . $i . ']" placeholder="' . $answer . '" value="' . $value . '">';
      echo '<label><input type="radio" name="' . $name . '[correct]" value="' . $i . '"' . $check . '> ' . $correct . '</label>';
    }
    echo '</div>';
  }
  echo '<button class="button" type="button" data-apipcommentquiz>' . $addmore . '</button>';

  ?><script>
    document.addEventListener('click', function(event){
      if(event.target.hasAttribute('data-apipcommentquiz')){
        var button = event.target;
        var index = [].indexOf.call(button.parentNode.children, button);
        var clone = button.previousElementSibling.cloneNode(true);
        var title = clone.querySelector('strong');

        title.textContent = title.textContent.replace(/\d+/, index + 1);
        [].forEach.call(clone.querySelectorAll('input'), function(input){
          input.name = input.name.replace(/\d+/, index);  //Update index
          if(input.type === 'text')input.value = '';      //Reset value
        });
        button.parentNode.insertBefore(clone, button);    //Insert in DOM
      }
    });
  </script>
  <?php wp_nonce_field('apipcommentquiz', 'apipcommentquiz-nonce');
}
add_action('save_post', 'apip_commentquiz_save', 10, 3);
function apip_commentquiz_save($post_id, $post, $update){
  if(isset($_POST['apipcommentquiz'], $_POST['apipcommentquiz-nonce']) &&
        wp_verify_nonce($_POST['apipcommentquiz-nonce'], 'apipcommentquiz')){
    delete_post_meta($post_id, 'apipcommentquiz');                         //Clean up previous quiz meta
    foreach($_POST['apipcommentquiz'] as $k=>$v){
      if($v['text'] && array_filter($v['answer'], 'strlen')){   //Only save filled in questions

        // Sanitizing data input
        foreach ( $v as $key => $value ) {
          $key = wp_kses_post( $key );
          $value = wp_kses_post( $value );
          $v[$key] = $value;
        }
        add_post_meta($post_id, 'apipcommentquiz', $v);
      }
    }
  }
}

/*
8.9 将标题转成16进制的按钮
 */
/*
apip_optimize_boxes 函数在admin_menu的钩子里调用。
这是官方文档上提供的方法，另有人主张在add_meta_box的钩子里调，事实证明只要在admin_menu里调用就可以
*/
function apip_optimize_boxes() {
    //第二个参数必须传‘post’，否则不好用。虽然注册的时候都是null。这些东西的注册在edit-form-advanced.php中。
    remove_meta_box('authordiv', 'post', 'normal');//移除[author]，顺道。
    remove_meta_box('trackbacksdiv', 'post', 'normal');//移除[trackback]，顺道。
    remove_meta_box('postexcerpt', 'post', 'normal');//移除[excerpt]，顺道。
    remove_meta_box('postcustom', 'post', 'normal');//移除[custom fields]，顺道。
    remove_meta_box('slugdiv', 'post', 'normal');//移除原生的[slug]，再扩展一个新的，因为原生的没提供钩子。在edit框后面增加一个按钮。
    //8.7
    add_meta_box('apipweatherdiv', 'Weather', 'apip_weather_meta_box', 'post', 'normal', 'core');
    //8.9
    add_meta_box('apipslugdiv', 'Slug to unicode', 'apip_title_hex_meta_box', 'post', 'normal', 'core');
    //8.10
    add_meta_box('apipcolorthiefdiv', 'Color thief', 'apip_colorthief_meta_box', 'post', 'normal', 'core');
}
function apip_title_hex_meta_box( $post ){
    $editable_slug = apply_filters( 'editable_slug', $post->post_name, $post );//照抄
    ?>
    <label class="screen-reader-text" for="post_name"><?php _e('Slug') ?></label><input name="post_name" type="text" size="30" id="post_name" value="<?php echo esc_attr( $editable_slug ); ?>" />&nbsp;<button class="button"  type="button" name="apiphexbtn" >uincode</button>
    <?php
    /*剩下的看js的了*/
}

//8.10 根据特色图片获取颜色。
/*
apip_colorthief_meta_box 函数在admin_menu的钩子里调用。
*/
function apip_colorthief_meta_box( $post ){
    $color_main = "#FFFFFF";
    $pic_id="";
    if (has_post_thumbnail()) {
        $pic_id = get_post_thumbnail_id();
        $color_main = get_post_meta($pic_id, "apip_main_color", true);
    }
    ?>
    <input type= 'text' name='apip-color-thief-picker' id='thief-color-picker'  value="<?php echo esc_attr( $color_main ); ?>" />
    <button class="button"  type="button" name="apipcolorthirfbtn" picid="<?php echo $pic_id; ?>" wpnonce="<?php echo wp_create_nonce('apip-color-thief-'.$pic_id);  ?>" >更新颜色</button>   
    <?php
    /*剩下的看js的了*/
}

/**
 * 作用: 按下按钮后，更新保存图片主颜色的回调函数。
 *      js在apip-admin.js中。
 * 来源: 自产
 * URL:
 */
function apip_accept_color(){
    $pic_id = $_POST['picid'];
    if ( !wp_verify_nonce($_POST['nonce'],"apip-color-thief-".$pic_id))
        die();
    $maincolor = $_POST['maincolor'];
    delete_post_meta( $pic_id, "apip_main_color", false );
    add_post_meta($pic_id, "apip_main_color", $maincolor, false);
}

/**
 * 作用: 设定特色图片后，更新保存图片主颜色的回调函数。
 *      js在apip-admin.js中。
 * 来源: 自产
 * URL:
 */
function apip_new_thumbnail_color(){
    $pic_id = $_POST['picid'];
    if (!$pic_id){
        return;
    }
    $maincolor = get_post_meta($pic_id, "apip_main_color", true);
    if (!$maincolor) {
        $maincolor = $_POST['maincolor'];
        delete_post_meta( $pic_id, "apip_main_color", false );
        add_post_meta($pic_id, "apip_main_color", $maincolor, false);
    }   
}

//8.11 显示自定义收藏内容

/**
 * 作用: 保存myfv用到的图片到本地
 * 来源: 自产
 * 参数: $id    [in]    图片的唯一标志，超过一张时为条目的主标志
 *       $img   [in]    图片网址。超过一张时用‘,’分割。
 *       $width [in]    图片保存时转换的宽度
 *       $height [in]   图片保存时转换的高度
 * 返回值：成功保存返回true，有一张失败返回false。
 * 备注：调用了Apip_SimpleImage，保存的格式默认是jpg。php5输入webp格式图片会保存成全黑jpg，php7支持webp的格式转换。
 */
function apip_save_myfv_img($id, $img, $width = 100, $height = 150) {
    $local_dir = APIP_GALLERY_DIR.'myfv/';
    $singlemode = true;
    $infs = array();
    if (strpos($img, ",")) {
        $singlemode = false;
        $imgs = explode(",", $img);
        for($i=0; $i<count($imgs); ++$i) {
            $infs[] = array('id'=>$id."-".($i+1), 'img'=>$imgs[$i]);
        }
    } else {
        $infs[] = array('id'=>$id, 'img'=>$img);
    }
    $ret = true;
    foreach ($infs as $inf) {
        $local_file = $local_dir.$inf['id'].'.jpg';
        $img = $inf['img'];
        if ( !is_file($local_file) /*&& !apip_is_debug_mode()*/ ) {
            if (!is_array(@getimagesize($img))) {
                $ret = false;
                continue;
            }
            $response = @wp_remote_get( 
                htmlspecialchars_decode($img), 
                array( 
                    'timeout'  => 300, 
                    'stream'   => true, 
                    'filename' => $local_file 
                ) 
            );
            if ( is_wp_error( $response ) )
            {
                $ret = false;
                continue;
            }
            $image = new Apip_SimpleImage();
            $image->load($local_file);
            $image->resize($width, $height);
            $image->save($local_file);
        } else {
            continue;
        }
    }
    return $ret;
}

/**
 * 作用: 读取myfv用到的图片
 * 来源: 自产
 * 参数: $id        [in]    条目的唯一标志
 *       $series    [in]    是否为系列中的图片，0为否，非0为是
 * 返回值:  非系列时返回本地图片的url，系列时返回本地图片url数组
 */
function apip_load_myfv_img($id, $series=0) {
    $local_dir = APIP_GALLERY_DIR.'myfv/';
    if (0===$series) {
        $local_file = $local_dir.$id.'.jpg';
        if (!is_file($local_file)) {
            return "";
        }
        return APIP_GALLERY_URL."myfv/".$id.".jpg";
    } else {
        $imgs=array();
        for($i=1; $i<100; ++$i ) {
            $local_file = $local_dir.$id.'-'.$i.'.jpg';
            if (!is_file($local_file)) {
                break;
            }
            $imgs[] = APIP_GALLERY_URL."myfv/".$id.'-'.$i.'.jpg';
        }
        return $imgs;
    }
}

function trim_fetched_item($value) {
    $value = trim($value);
    $value = str_replace(array("[", "]", "&nbsp;", ""),array("【", "】", "", "N/A"), $value);
    return $value;
}

function apip_fetch_douban_people_str ($from_str, $to_str, $base_str) {
    $pos_start = strpos($base_str, $from_str);
    $pos_end = strpos($base_str, $to_str, $pos_start);
    if ( $pos_start>0 && $pos_end > $pos_start) {
        $people_str = substr($base_str, $pos_start, ($pos_end - $pos_start) + strlen($to_str) );
        preg_match_all( '/(?<=>).*?(?=<\/a>)/', $people_str, $matches);
        if (is_array($matches) && count($matches)>0 && is_array($matches[0]) ) {
            return apip_items_implode($matches[0]);
        }
    }
    return "";
}

/**
 * 作用: 获得豆瓣数据
 * 来源: 自产
 * 参数: $id        [in]    条目的唯一标志
 *       $series    [in]    是否为系列中的图片，0为否，非0为是
 * 返回值:  abs的字符串和分列形式
 */
function apip_fetch_from_douban_page($url, $abs, $type, $wanttitle='0') {
    $ret = array('str'=> $abs, 'arr' => array(), 'title' => "");
    $response = @wp_remote_get( 
        htmlspecialchars_decode($url), 
        array( 'timeout'  => 1000, ) 
    );
    if ( is_wp_error( $response ) || !is_array($response) ) {
        return $ret;
    }
    $body = wp_remote_retrieve_body($response);
    if ('1' == $wanttitle) {
        $start_pos = strpos($body, "<title>", 0);
        $end_pos = strpos($body, "</title>", $end_pos);
        $title_str = "";
        if ( $start_pos>0 && $end_pos > $start_pos) {
            $title_str = substr($body, $start_pos, ($end_pos - $start_pos)+strlen("</title>") );
        }
        $title = str_replace(array("(豆瓣)","<title>","</title>"), "", $title_str);
        $ret['title'] = trim($title);
    }
    preg_match_all('/(<div id="mainpic"[\s\S]+?<\/div>)|(<div id="info"[\s\S]+?<\/div>)|(<strong .+? property="v:average">.+?(<\/strong>|>))/',$body, $matches);
    if (is_array($matches) && is_array($matches[0]) && count($matches[0])>=3) {
        $mainpic_div_str = $matches[0][0];
        $info_div_str = $matches[0][1];
        $score_str = $matches[0][2];
        $fetch['link'] = $url;

        //图
        preg_match('/(?<=img src=").*?(?=")/',$mainpic_div_str,$match_imgs);
        if (is_array($match_imgs)) {
            $fetch['pic'] = trim($match_imgs[0]);
        }

        //分
        preg_match('/(?<= property="v:average"\>).*?(?=\<)/',$score_str, $match_score);
        if (is_array($match_score)) {
            $fetch['average_score'] = trim($match_score[0]);
        }

        if ("movie"=== $type) {
            //电影：导演，演员，类型，上映时间，imdb链接
            $info_grep_keys = array(
                array('pattern'=>'/(?<="v:directedBy"\>).*?(?=\<)/', 'item'=>'director'),
                array('pattern'=>'/(?<="v:starring"\>).*?(?=\<)/', 'item'=>'actor'),
                array('pattern'=>'/(?<="v:genre"\>).*?(?=\<)/', 'item'=>'genre'),
                array('pattern'=>'/(?<=\<span property="v:initialReleaseDate" content=").*?(?=\")/', 'item'=>'release_date'),
                array('pattern'=>'/https:\/\/www.imdb.com\/title\/tt[0-9]{1,10}/', 'item'=>'imdblink'),
            );
        } elseif ("book"===$type) {
            $info_grep_keys = array(
                array('pattern'=>'/(?<=\<span class="pl"\>出版社:\<\/span\>).*?(?=\<br\/\>)/', 'item'=>'publisher'),
                array('pattern'=>'/(?<=\<span class="pl"\>出版年:\<\/span\>).*?(?=\<br\/\>)/', 'item'=>'pubdate'),
            );
            /*<span>[\s\S]+?<span class="pl"> 作者</span>\:[\s\S]+?<\/span> */
            /*<span>[\s\S]+?<span class="pl"> 译者</span>\:[\s\S]+?<\/span> */
            $ak = 1;
            $pos_start = strpos($info_div_str, '<span class="pl"> 作者</span>:');
            if ($pos_start <=0) {
                $pos_start = strpos($info_div_str, '<span class="pl">作者:</span>');
                $ak = 2;
            }
            if (1==$ak) {
                $pos_end = strpos($info_div_str, '</span><br/>', $pos_start);
            } else {
                $pos_end = strpos($info_div_str, '<br>', $pos_start);
            }
            
            if ( $pos_start>0 && $pos_end > $pos_start) {
                if (1 == $ak) {
                    $author_str = substr($info_div_str, $pos_start, ($pos_end - $pos_start)+strlen('</span><br/>') );
                } else {
                    $author_str = substr($info_div_str, $pos_start, ($pos_end - $pos_start)+strlen('<br>') );
                }
                
                unset($matches);
                preg_match_all( '/(?<=>)[\s\S].*?(?=<\/a>)/', $author_str, $matches);
                if (is_array($matches) && count($matches)>0 && is_array($matches[0]) ) {
                    $fetch['author'] = apip_items_implode($matches[0]);
                }
            }
            $ak = 1;
            $pos_start = strpos($info_div_str, '<span class="pl"> 译者</span>:');
            if ($pos_start <=0) {
                $ak = 2;
                $pos_start = strpos($info_div_str, '<span class="pl">译者:</span>');
            }
            if (1==$ak) {
                $pos_end = strpos($info_div_str, '</span><br/>', $pos_start);
            } else {
                $pos_end = strpos($info_div_str, '<br>', $pos_start);
            }
            if ( $pos_start>0 && $pos_end > $pos_start) {
                if (1==$ak) {
                    $author_str = substr($info_div_str, $pos_start, ($pos_end - $pos_start)+strlen('</span><br/>') );
                } else{
                    $author_str = substr($info_div_str, $pos_start, ($pos_end - $pos_start)+strlen('<br>') );
                }
                unset($matches);
                preg_match_all( '/(?<=>)[\s\S].*?(?=<\/a>)/', $author_str, $matches);
                if (is_array($matches) && count($matches)>0 && is_array($matches[0]) ) {
                    $fetch['translator'] = apip_items_implode($matches[0]);
                }
            }
        } elseif ("music"===$type) {
            $info_grep_keys = array(
                array('pattern'=>'/(?<=\<span class="pl"\>出版者:<\/span>).[\s\S]*?(?=\<br[\s\S]\/>)/', 'item'=>'publisher'),
                array('pattern'=>'/(?<=\<span class="pl"\>发行时间:<\/span>).[\s\S]*?(?=\<br[\s\S]\/>)/', 'item'=>'release_date'),
                array('pattern'=>'/(?<=\<span class="pl"\>流派:<\/span>).[\s\S]*?(?=\<br[\s\S]\/>)/', 'item'=>'genre'),
            );
            $fetch['artist'] = apip_fetch_douban_people_str('表演者:','</span>',$info_div_str);
        }

        foreach ($info_grep_keys as $grep) {
            unset($matches);
            preg_match_all( $grep['pattern'], $info_div_str, $matches);
            if (is_array($matches) && is_array($matches[0]) && count($matches[0])>=1) {
                $fetch[$grep['item']] = apip_items_implode($matches[0]);
            }
        }
    }//preg_matches

    if (!is_array($fetch) || count($fetch)==0) {
        return $ret;
    }
    $abs_a = apip_content_explode($abs);
    if ("movie" === $type) {
        $ci = array(
            array( 'itemf' => 'average_score', 'itemt' => 'douscore' ),
            array( 'itemf' => 'pic', 'itemt' => 'img' ),
            array( 'itemf' => 'link', 'itemt' => 'doulink' ),
            array( 'itemf' => 'imdblink', 'itemt' => 'imdblink' ),
            array( 'itemf' => 'director', 'itemt' => '导演' ),
            array( 'itemf' => 'actor', 'itemt' => '演员' ),
            array( 'itemf' => 'release_date', 'itemt' => '上映时间' ),
            array( 'itemf' => 'genre', 'itemt' => '类型' ),
        );
    } elseif ("book"===$type) {
        $ci = array(
            array( 'itemf' => 'average_score', 'itemt' => 'douscore' ),
            array( 'itemf' => 'pic', 'itemt' => 'img' ),
            array( 'itemf' => 'link', 'itemt' => 'doulink' ),
            array( 'itemf' => 'author', 'itemt' => '作者' ),
            array( 'itemf' => 'translator', 'itemt' => '译者' ),
            array( 'itemf' => 'pubdate', 'itemt' => '出版时间' ),
            array( 'itemf' => 'publisher', 'itemt' => '出版社' ),
        );
    } elseif ("music"===$type) {
        $ci = array(
            array( 'itemf' => 'average_score', 'itemt' => 'douscore' ),
            array( 'itemf' => 'pic', 'itemt' => 'img' ),
            array( 'itemf' => 'link', 'itemt' => 'doulink' ),
            array( 'itemf' => 'artist', 'itemt' => '表演者' ),
            array( 'itemf' => 'publisher', 'itemt' => '出版者' ),
            array( 'itemf' => 'release_date', 'itemt' => '发行时间' ),
            array( 'itemf' => 'genre', 'itemt' => '流派' ),
        );
    }
    foreach ($ci as $i) {
        if (array_key_exists($i['itemf'], $fetch)) {
            $abs_a[$i['itemt']] = $fetch[$i['itemf']];
        }
    }
    $ret['arr'] = $abs_a;
    unset($abs_a['img']);
    $ret['str'] = apip_content_implode($abs_a);
    return $ret;
}

/**
* 作用: 在保存时给自定义收藏赋予ID并保存图片到本地。
* 来源: 自创
* 说明: 新建条目时,$id="x"表示要生成图片。其余必须要有的参数是type，img，title，link，score。abs中的内容会在随后显示成列表，每个项目用分号隔开，项目的值用逗号隔开。系列时要增加series=1
* id标准:  
*   书籍: fvbk
*   电影: fvmv
*   游戏: fvgm
*   音乐: fvmu
* type: book, movie, music, game
*/
/* [myfv id="x" type="movie" title="', '" img="" link="" score="0" abs="年份:;导演:;演员:;类型:;nipple:;doulink:;imdblink:;作者:;译者:;出版年份:;出版社:;表演者:;download:;"/] */
function apip_myfv_filter( $new_status, $old_status, $post ) {
    if ( 'post' !== $post->post_type && 'page' !== $post->post_type) {
        return;
    }
    if (FALSE === get_option( 'apip_max_fav_ids' )) {
        $myfv_maxids = array('m_fvbk'=>1000001, 'm_fvmv'=>3000001, 'm_fvgm'=>5000001, 'm_fvmu'=>6000001);
        add_option('apip_max_fav_ids',$myfv_maxids, null, 'no');
    } else {
        $myfv_maxids = get_option( 'apip_max_fav_ids' );
    }
    if (!key_exists('m_fvbk', $myfv_maxids)) {
        $myfv_maxids['m_fvbk'] = 1000001;
    }
    if (!key_exists('m_fvmv', $myfv_maxids)) {
        $myfv_maxids['m_fvmv'] = 3000001;
    }
    if (!key_exists('m_fvgm', $myfv_maxids)) {
        $myfv_maxids['m_fvgm'] = 5000001;
    }
    if (!key_exists('m_fvmu', $myfv_maxids)) {
        $myfv_maxids['m_fvmu'] = 6000001;
    }
    $my_content = $post->post_content;
    $fix_to = "";
    if ( "draft" == $new_status || "publish" == $new_status || "private" == $new_status) {
        preg_match_all('/\[myfv.+[^\]]/', $post->post_content, $matches);
        if ( !is_array($matches) || empty($matches) ) {
            return;
        }
        foreach ($matches[0] as $hit) {
            /*id type title img link score abs*/
            unset($keys);
            unset($id);
            unset($title);
            unset($type);
            unset($img);
            unset($link);
            unset($score);
            unset($abs);
            unset($series);
            unset($nipple);
            unset($hasauto);

            //id
            preg_match('/(?<=id=").*?(?=")/', $hit, $keys);
            if(!is_array($keys) || count($keys) == 0 || trim($keys[0])== "") {
                continue;
            }
            $id = trim($keys[0]);
            if ("x"!==$id) {
                continue;
            }

            //type
            unset($keys);
            preg_match('/(?<=type=").*?(?=")/', $hit, $keys);
            if(!is_array($keys) || count($keys) == 0 || trim($keys[0])== "") {
                continue;
            }
            $type = trim($keys[0]);
            if ("movie"!==$type && "book"!==$type && "music"!==$type && "game"!==$type && "auto"!==$type) {
                continue;
            }

            //link
            unset($keys);
            preg_match('/(?<=link=").*?(?=")/', $hit, $keys);
            if(!is_array($keys) || count($keys) == 0 || trim($keys[0])== "") {
                continue;
            }
            $link = trim($keys[0]);

            //abs
            if (!isset($abs)) {
                unset($keys);
                preg_match('/(?<=abs=").*?(?=")/', $hit, $keys);
                if(is_array($keys) && count($keys) > 0 ) {
                    $abs = trim($keys[0]);
                }else {
                    $abs="";
                }
            }

            //nipple
            unset($keys);
            preg_match('/(?<=nipple=").*?(?=")/', $hit, $keys);
            if(is_array($keys) && count($keys) > 0 ) {
                $nipple = trim($keys[0]);
            }
            else {
                $nipple = "";
            }
            if ("yes" !== $nipple) {
                $nipple = "";
            }

            if ("auto" == $type) {
                unset($keys);
                if ($link !== "") {
                    if (strpos($link, "movie.douban.com")) {
                        $type = "movie";
                    } elseif (strpos($link, "book.douban.com")) {
                        $type = "book";
                    } elseif (strpos($link, "music.douban.com")) {
                        $type = "music";
                    }
                    if (isset($type)) {
                        $ret = apip_fetch_from_douban_page($link, $abs, $type, '1');
                        $title = $ret['title'];
                        $abs_a = $ret['arr'];
                        if (array_key_exists('img', $ret['arr']) && $ret['arr']['img']!="") {
                            $img = $ret['arr']['img'];
                        }
                        $abs = $ret['str'];
                        if ("yes" == $nipple) {
                            $abs .= "nipple:yes;";
                        }
                    }
                }
                $hasauto=1;
            }

            //title
            if (!isset($title)) {
                unset($keys);
                preg_match('/(?<=title=").*?(?=")/', $hit, $keys);
                if(is_array($keys) && count($keys) > 0 ) {
                    $title = trim($keys[0]);
                }
            }

            //img
            if (!isset($img)) {
                unset($keys);
                preg_match('/(?<=img=").*?(?=")/', $hit, $keys);
                if(is_array($keys) && count($keys) > 0 ) {
                    $img = trim($keys[0]);
                }
            }

            //score
            if (!isset($score)) {
                unset($keys);
                preg_match('/(?<=score=").*?(?=")/', $hit, $keys);
                if(is_array($keys) && count($keys) > 0 ) {
                    $score = trim($keys[0]);
                }
            }

            //series
            if (!isset($series)) {
                unset($keys);
                preg_match('/(?<=series=").*?(?=")/', $hit, $keys);
                if(is_array($keys) && count($keys) > 0 ) {
                    $series = trim($keys[0]);
                }
                else {
                    $series = "0";
                }
            }

            $width = 100;
            $height = 150;
            if ("movie" === $type) {
                $myfv_maxids['m_fvmv']++;
                $id = 'fvmv'.$myfv_maxids['m_fvmv'];
            } else if ("book" === $type) {
                $myfv_maxids['m_fvbk']++;
                $id = 'fvbk'.$myfv_maxids['m_fvbk'];
            }  else if ("music" === $type) {
                $myfv_maxids['m_fvmu']++;
                $id = 'fvmu'.$myfv_maxids['m_fvmu'];
                $width = 150;
            }  else if ("game" === $type) {
                $myfv_maxids['m_fvgm']++;
                $id = 'fvgm'.$myfv_maxids['m_fvgm'];
            } else {
                continue;
            }
            /*
            暂时注掉
            if ($link !== "" && strpos($link, "douban.com") && !isset($hasauto)) {
                $ret = apip_fetch_from_douban_page($link, $abs, $type);
                $abs_a = $ret['arr'];
                if (array_key_exists('img', $abs_a) && $abs_a['img']!="") {
                    $img = $abs_a['img'];
                }
                $abs = $ret['str'];
                if (isset($nipple) && "yes" == $nipple) {
                    $abs .= 'nipple:yes;';
                }
            }
            */
            $need_img_download = true;
            if ($img !== "") {
                $img = str_replace(".webp", ".jpg", $img);
                $pos = strpos($link, 'douban.com/subject/');
                if ($pos) {
                    $douid = str_replace(array("https://movie.douban.com/subject/","https://book.douban.com/subject/","https://book.douban.com/subject/","https://music.douban.com/subject/"),"",$link);
                    $src = APIP_GALLERY_DIR."douban_cache/".$douid.".jpg";
                    $dst = APIP_GALLERY_DIR."myfv/".$id.".jpg";
                    if (file_exists($src)){
                        if (copy($src, $dst)){
                            $need_img_download = false;
                        }
                    }
                }
                if ($need_img_download){
                    if (!apip_save_myfv_img($id, $img, $width, $height)) {
                        continue;
                    }
                }
            } else {
                //Must contain picture
                continue;
            }
            //找到的内容后面有个回车，直接替换会吃掉这个回车。
            $hit = trim($hit);
            $fix_to = sprintf('[myfv id="%s" type="%s" title="%s" img="%s" link="%s" score="%s" abs="%s" series="%s" /]', $id, $type, $title, $img, $link, $score, $abs, $series);
            $my_content = str_replace($hit, $fix_to, $my_content);
        }
        if ($fix_to !== "") {
            update_option( 'apip_max_fav_ids', $myfv_maxids );
            //防止无限循环
            remove_action( 'transition_post_status', 'apip_myfv_filter', 10 );
            remove_filter( 'the_content', 'apip_fix_shortcodes' );
            $my_post = array("ID"=>$post->ID, "post_content"=> $my_content);
            wp_update_post($my_post);
            add_action( 'transition_post_status', 'apip_myfv_filter', 10, 3 );
            add_filter( 'the_content', 'apip_fix_shortcodes');
        }
    }
}

function apip_items_implode($items) {
    if (!is_array($items)) {
        return $items;
    }
    $count = count($items);
    if (0 == $count) {
        return "";
    }
    if ($count > 8) {
        $items = array_slice($items, 0, 8);
    }
    $items = array_map('trim_fetched_item', $items);
    if (1 == $count) {
        return $items[0];
    } else {
        return implode(",",$items);
    }
}

/**
* 作用: 生成自定义属性的字符串，多个值用逗号分割。
* 来源: 自创
*/
function apip_content_implode($abs_array){
    $ret = "";
    foreach($abs_array as $key=>$value) {
        if (""===$key){
            continue;
        }
        if (is_array($value)) {
            if (count($value) > 1) {
                if (count($value) > 8) {
                    $value = array_slice($value, 0, 8);
                }
                $v1 = implode(",",$value);
            } else{
                $v1 = $value[0];
            }
        } else {
            $v1 = $value;
        }
        $ret .= $key.":".$v1.";";
    }
    return $ret;
}

/**
* 作用: 解析自定义的属性。
* 返回值: array[key]=value，value为空时不计入
* 来源: 自创
*/
function apip_content_explode($abs){
    $contentarray = array();
    $ret = array();
    $contentarray = explode(";", $abs);
    if ( !is_array($contentarray) || empty($contentarray)) {
        return $ret;
    }
    foreach ($contentarray as $abstract) {
        $gotarray = explode(":", $abstract);
        if (!is_array($gotarray) || empty($gotarray)) {
            continue;
        }
        if (count($gotarray) != 2) {
            continue;
        }
        if (trim($gotarray[1]) === "") {
            continue;
        }
        $ret[trim($gotarray[0])] = trim($gotarray[1]);
    }
    return $ret;
}

/**
* 作用: 在保存时给自定义收藏赋予ID并保存图片到本地。
* 来源: 自创
* id标准:  
*   书籍: fvbk
*   电影: fvmv
*   游戏: fvgm
*   音乐: fvmu
* type: book, movie, music, game, series(TBD)
*/
function apip_myfv_detail($atts, $content = null){
    extract( $atts );
    if ("x"===$id || ""===$id) {
        return "";
    }
    if (isset($abs)) {
        $abstracts = apip_content_explode($abs);
    } else {
        $abstracts = array();
    }
    

    if (0 == $series) {
        $template = '<div class="apip-item"><div class="mod"><div class="%5$s"><div class="apiplist-post">%1$s</div><div class="title">%2$s</div><div class="rating">%3$s</div><div class="abstract">%4$s</div></div></div></div>';
    } else {
        $template = '<div class="apip-item"><div class="mod"><div class="v-overflowHidden doulist-subject"><div class="title">%1$s</div><div class="abstract-left">%2$s</div>%3$s</div></div></div>';
    }
    

    $subject_class="v-overflowHidden doulist-subject";//5
    if (0 == $series) {
        $img_str=sprintf('<img src="%1$s" alt="%2$s"></img>',
                            apip_load_myfv_img($id),
                            base64_encode($link));//1
    } else {
        $img_urls = apip_load_myfv_img($id, $series);
        $img_str = "";
        $i = 0;
        foreach ($img_urls as $img_url) {
            $img_str.=sprintf('<div class="apiplist-post"><img src="%1$s" alt="%2$s" ></img></div>',
                        $img_url,
                        $id."-".++$i);
        }
    }

    //标题
    $title_str=sprintf('<a href="%1$s" class="cute" target="_blank" rel="external nofollow">%2$s</a>',
                        $link,
                        $title);//2

    //评分
    $rating_str="";//3
    $rated="--";
    if (intval($score)>=0 && intval($score) <= 10) {
        $subject_class .= " my-score-".$score;
    } else {
        $score = "--";
    }
    $star_dou = '<span class="dou-stars-0"></span>';
    if (isset($abstracts['douscore'])) {
        $rated = $abstracts['douscore'];
        $star_dou = sprintf('<span class="dou-stars-%d"></span>', $rated);
    }
    unset($abstracts['douscore']);
    unset($abstracts['doulink']);

    $str_rnum = sprintf('<span class="rating_nums">(%1$s / %2$s)</span>', $score, $rated);
    if ($score !== "--") {
        $star_my = sprintf('<span class="my-stars-%s"></span>', $score);
    } else {
        $star_my = '<span class="my-stars-0"></span>';
    }
    $rating_str = sprintf('<span class="allstardark">%1$s%2$s</span>%3$s', $star_dou, $star_my, $str_rnum);

    //详细
    $abstract_str="";//4
    $i=0;
    $abs_img_str="";
    foreach ($abstracts as $key=>$val) {
        if ($key=="nipple" && $val == "yes") {
            $abs_img_str .= '<span class="feature">奶</span>';
            continue;
        }
        //把详细里的逗号换成斜线
        $abstract_str.= sprintf('<span class="abs-%d">%s：%s</span>', ++$i, $key, str_replace(",", " / ", $val));
    }
    $abstract_str.= $abs_img_str;
    if (0 == $series) {
        $out = sprintf($template, $img_str, $title_str, $rating_str, $abstract_str, $subject_class);
    } else {
        $out = sprintf($template, $title_str, $abstract_str, $img_str);
    }
    return $out;

}

//8.12
/**
* 作用: 分拣出引文标志，并且最终表示出来
* 来源: 自创
*/
function apip_sup_detail($atts, $content = null){
    global $g_mysups;
    extract( $atts );
    if (!isset($sup_content) || $sup_content === "") {
        return;
    }
    if (!isset($g_mysups) || count($g_mysups) == 0) {
        //第一条
        $g_mysups = array();
    }
    $str_num = strval(count($g_mysups)+1);
    $g_mysups[$str_num] = $sup_content;
    $output = sprintf('<sup class="content-sup"><a href="#inner_anchor_%s" name="inner_ref_%s">[%s]</a></sup>', $str_num, $str_num, $str_num);
    return $output;
}

function apip_make_sup_anchors($content) {
    global $g_mysups;
    if (!isset($g_mysups) || count($g_mysups) == 0) {
        return $content;
    }
    $output = '<hr class="apip_inner_anchors_begin" />';
    $output .= '<div class="apip_inner_anchors"><ul>';
    foreach ($g_mysups as $index => $anchor) {
        $list = sprintf('<li><a name = "inner_anchor_%s" class="apip_anchor_link" href="#inner_ref_%s">(%s)</a>：%s</li>', $index, $index, $index, $anchor);
        $output .= $list;
    }
    $output .= '</ul></div>';
    return $content.$output;
}

/*                                          08终了                             */

/******************************************************************************/
/*        09.纯后台功能                                                        */
/******************************************************************************/
//9.1 后台taglist增加private和draft计数列
 /**
 * 作用: 添加taglist中的列标题
 * 来源: https://gist.github.com/maxfenton/593473788c2259209694
 * URL: https://make.wordpress.org/docs/plugin-developer-handbook/10-plugin-components/custom-list-table-columns/
 * 备注: filter: manage_edit-{$taxonomy}_columns
 */
function apip_edit_post_tag_column_header( $columns ){
        $columns['none-public-count'] = 'Non-public Count';
        return $columns;
}
 /**
 * 作用: 填充非public状态tag的计数
 * 来源: https://gist.github.com/maxfenton/593473788c2259209694
 * URL: https://make.wordpress.org/docs/plugin-developer-handbook/10-plugin-components/custom-list-table-columns/
 * 备注: filter: manage_{$taxonomy}_custom_column
 */
function apip_edit_post_tag_content( $value, $column_name, $tax_id ){
    $args = array(
        'numberposts' => -1,
        'post_type'   => 'post',
        'post_status' => array('private', 'draft'),
        'tag_id'      => $tax_id,
    );
    $myquery = new WP_Query( $args );   //查询类型为private和draft，并且包含tag_id与$tax_id的所有post。
    $p_count = 0; //private count
    $d_count = 0; //draft count
    foreach ($myquery->posts as $p) {
        //对两种类型分别计数
        if ($p->post_status == 'private') {
            $p_count++;
        } else {
            $d_count++;
        }
    }
    if (0 === $p_count + $d_count) {
        return "—";
    }
    $term_slug = get_term( $tax_id )->slug; //URL需要tag的slug
    $ret = "";
    $p_str = "";
    $d_str = "";
    $url_base = home_url('/',is_ssl()?'https':'http').'wp-admin/edit.php?post_type=post';
    if ($p_count) {
        $p_str = sprintf('<strong>privates:</strong><a href="%s&tag=%s&post_status=private">%d</a>', $url_base, $term_slug, $p_count); //加编辑用的超链
    }
    if ($d_count) {
        $d_str = sprintf('<strong>drafts:</strong><a href="%s&tag=%s&post_status=draft">%d</a>',$url_base, $term_slug, $d_count);
    }
    return sprintf("  %s  %s", $p_str, $d_str);
}

//9.2
function apip_convert_post_tag_slug_to_utf($actions, $tag){
    $action = '<a href="/">转unicode</a>';
    $new_slug=apip_mb_str2_hex($tag->name);
    $actions['convert_unicode_slug'] = $action;
    return $actions;
}

/*                                          09终了                             */

 /**
 * 作用: 解决bjlazyload，ngg-gallery之间的冲突问题，暂时废弃
 * 来源: 自产
 * URL:
 */
function apip_filter_filter()
{   global $wp_filter ;
    if ( empty($wp_filter['the_content'][100]) )
        return;
    foreach ($wp_filter['the_content'][100] as $id => $filter) {
        if (!strpos($id, 'bjlazyload_filter'))
            continue;

        $object = $filter['the_content'][0];

        if (is_object($object) && get_class($object) != 'M_Third_Party_Compat')
            continue;

        remove_filter('the_content', array($object, 'bjlazyload_filter'), 100);
        break;
    }
}

/******************************************************************************/
/*        一些共用功能的整合                                                   */
/******************************************************************************/
/**
 *  WP钩子：template_redirect
 *  整合日：20200416
 *  涉及功能：0.10 作者页跳转到404
 *           2.7  搜索结果只有一条时直接跳入
 *           2.10 外链转内链
 */
function apip_template_redirect() {
    //0.10 作者页跳转到404
    apip_redirect_author();
    //2.7搜索结果只有一条时直接跳入
    if ( apip_option_check('redirect_if_single') ) {
        redirect_single_post();
    }
    //2.10外链转内链
    if ( apip_option_check('redirect_external_link') ) {
        apip_e2i_redirect();
    }
}

/* */
//准备抄袭的功能
/* */

/******************************************************************************/
/*        已经废除不再调用的代码，但可能有些参考价值                              */
/******************************************************************************/
/**
* 作用: 将UTF8字符串转成16进制带下划线的字符串
*/
function apip_mb_str2_hex($str) {
    $ret="";    
    for ($i = 0; $i < mb_strlen($str, "utf-8"); $i++)
    {
        $char = mb_substr($str, $i, 1, "utf-8");

        for ($j = 0; $j < strlen($char); $j++)
        {
            $ret.= "_".(dechex(ord($char[$j])));
        }
    }
    return $ret;
}
