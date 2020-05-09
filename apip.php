<?php

/**
 * Plugin Name: All plugins in pewae
 * Plugin URI:  http://pewae.com
 * GitHub Plugin URI: https://github.com/lifishake/apip
 * Description: Plugins used by pewae
 * Author:      lifishake
 * Author URI:  http://pewae.com
 * Version:     1.30.8
 * License:     GNU General Public License 3.0+ http://www.gnu.org/licenses/gpl.html
 */

/*宏定义*/
define('APIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('APIP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ) ;
define('APIP_GALLERY_URL',home_url('/',is_ssl()?'https':'http').'wp-content/gallery/');
define('APIP_GALLERY_DIR', ABSPATH.'wp-content/gallery/');
global $apip_options;

register_activation_hook( __FILE__, 'apip_plugin_activation' );
register_deactivation_hook( __FILE__,'apip_plugin_deactivation' );
register_uninstall_hook(__FILE__, 'apip_plugin_deactivation');

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
function apip_settings_link($action_links,$plugin_file){
	if($plugin_file==plugin_basename(__FILE__)){
		$apip_settings_link = '<a href="options-general.php?page=apip/apip-options.php">Settings</a>';
		array_push($action_links,$apip_settings_link);
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
    //0.6 去掉后台的OpenSans
    //add_action( 'admin_enqueue_scripts', 'apip_remove_open_sans' );
    //0.7 自带的TagCloud格式调整
    add_filter( 'widget_tag_cloud_args', 'apip_resort_tagcloud' ) ;
    //0.8 移除后台的“作者”列
    add_filter( 'manage_posts_columns', 'apip_posts_columns' );
    //0.9 升级后替换高危文件
    add_action( 'upgrader_process_complete', 'apip_remove_default_risk_files', 11, 2 );
    //0.10 作者页跳转到404
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
    //add_filter('pre_site_transient_update_core','remove_core_updates');
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
    if( apip_option_check('save_revisions_disable') ) {
        //2.1停止自动版本更新
        //这个必须在config里面设才行
        //apip_auto_rev_settings();
    }
    if( apip_option_check('auto_save_disabled') ) {
    //2.2停止自动保存
        add_action( 'wp_print_scripts', 'apip_auto_save_setting' );
    }
    //2.3是否显示adminbar
    add_filter( 'show_admin_bar', 'apip_admin_bar_setting' );
    if ( apip_option_check('forground_chinese') ) {
    //2.4后台英文前台中文
        add_filter( 'locale', 'apip_locale', 99 );
    }
    if ( apip_option_check('block_open_sans') ) {
        //2.5屏蔽已经注册的open sans
        add_action( 'wp_default_styles', 'apip_block_open_sans', 100);
    }
    if ( apip_option_check('show_author_comment') )
    {
        //2.6默认留言widget里屏蔽作者
        add_filter( 'widget_comments_args', 'before_get_comments' );
    }
    /*
    if ( apip_option_check('redirect_if_single') )
    {
        //2.7搜索结果只有一条时直接跳入
        add_action('template_redirect', 'redirect_single_post');
    }
    */
    if ( apip_option_check('protect_comment_php') )
    {
        //2.8禁止直接访问wp_comments.php
        add_action('check_comment_flood', 'check_referrer_comment');
    }
    if ( apip_option_check('search_without_page') )
    {
        //2.9搜索结果不包括page页面
        add_filter('pre_get_posts','remove_page_search');
    }

    if  ( apip_option_check('redirect_external_link') ) {
        //2.10外链转内链
        add_filter('the_content','convert_to_internal_links',99); // 文章正文外链转换
        add_filter('comment_text','convert_to_internal_links',99); // 评论内容的链接转换
        add_filter('comment_url','apip_comment_url', 10, 2); //链接转换
    }

    if  ( apip_option_check('remove_core_updates') ) {
        //2.11移除后台界面的WP版本升级提示
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
    add_filter( 'comment_row_actions', 'apip_show_advertise_comment_details', 10, 2 );
    add_filter( 'comment_form_defaults', 'apip_replace_triditional_comment_placeholder_text');

    /** 06*/
    //social没有添加项,需要外部手动调用
    /** 07 */
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
        add_filter( 'the_content', 'apip_lazyload_filter',200 );
        add_filter( 'post_thumbnail_html', 'apip_lazyload_filter',200 );
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
    if ( apip_option_check('apip_douban_enable') )  {
        add_shortcode('mydouban', 'apip_dou_detail');
        add_shortcode('myimdb', 'apip_imbd_detail');
    }
    //8.6 每夜一游
    add_shortcode('mygame', 'apip_game_detail');
    if ( !class_exists('Apip_SimpleImage') ) {
        //包跳转类含头文件
        require_once ( APIP_PLUGIN_DIR.'/class/apip-image.php') ;
    }
    //8.7 发帖天气
    //当作每篇文章都会存草稿.草稿转成公开的时刻为发表时刻
    add_action( 'draft_to_publish','apip_save_heweather',99,1);
    add_action( 'draft_to_private','apip_save_heweather',99,1);
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
    //8.9 手动翻译按钮
    //8.10 特色图主颜色按钮
    if(is_admin()) {
        add_action('admin_menu','apip_optimize_boxes');
        //增加ajax回调函数
        add_action( 'wp_ajax_apip_accept_color', 'apip_accept_color' );
        add_action( 'wp_ajax_apip_new_thumbnail_color', 'apip_new_thumbnail_color' );
        add_action( 'wp_ajax_apip_weather_manual_update', 'apip_weather_manual_update' );
    }

    //0X 暂时不用了
    //三插件冲突
    //add_action( 'wp_print_scripts', 'apip_filter_filter',2 );
    //确认提交前的提示,未添加配置项
    //add_filter('comment_form_defaults' , 'apip_replace_tag_note', 30);

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
    0.12                                禁用古腾堡（5.0）后
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
    2.1     save_revisions_disable      阻止自动版本
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
    8.9     yandex_translate_key        手动翻译标题的按钮
    8.10    apip_colorthief_meta_box    取特色图片主色调相关内容
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
    wp_enqueue_style( 'apip-style-all', APIP_PLUGIN_URL . 'css/apip-all.css', array(), '20200422' );
    wp_enqueue_script('apip-js-option', APIP_PLUGIN_URL . 'js/apip-option.js', array(), "20200418", true);
    $css = '';

    if ( /*is_single()*/1 ) {
        wp_enqueue_style( 'apip_weather_style', APIP_PLUGIN_URL . 'css/weather-icons.min.css' );
        wp_enqueue_style( 'apip_wind_style', APIP_PLUGIN_URL . 'css/weather-icons-wind.min.css' );
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
        $css .= '   a.lk0 {
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

function apip_admin_scripts() {
    global $apip_options;
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'apip-style-option', APIP_PLUGIN_URL . 'css/apip-option.css' );
    wp_enqueue_style( 'apip-style-admin', APIP_PLUGIN_URL . 'css/apip-admin.css' );
    wp_enqueue_script('apip-color-thief', APIP_PLUGIN_URL . 'js/color-thief.js', array(), '20191101', true);
    wp_enqueue_script('apip-js-admin', APIP_PLUGIN_URL . 'js/apip-admin.js', array('wp-color-picker' ), '20191101', true);
    wp_localize_script('apip-js-admin','yandexkey',$apip_options['yandex_translate_key']);
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
        QTags.addButton( 'eg_163music', '网易云音乐', '<iframe frameborder="no" border="0" marginwidth="0" marginheight="0" width=330 height=86 src="//music.163.com/outchain/player?type=2&id=', '&auto=1&height=66"></iframe>' );
        QTags.addButton( 'eg_mydoubanmovie', '豆瓣电影', '[mydouban id="', '" type="movie" nipple="no" /]', 'p' );
        QTags.addButton( 'eg_myimdb', 'imdb', '[myimdb id="', '" cname="" nipple="no" /]', 'p' );
        QTags.addButton( 'eg_mydoubanmusic', '豆瓣音乐', '[mydouban id="', '" type="music" /]', 'p' );
        QTags.addButton( 'eg_mygame', '每夜一游', '[mygame id="', '" cname="" ename="" jname="" alias="" year="" publisher=""  platform="" download="" genres="" poster="" /]', 'p' );
        QTags.addButton( 'eg_mydoubanbook', '豆瓣读书', '[mydouban id="', '" type="book" /]', 'p' );
        QTags.addButton( 'eg_mybook', '自定义读书', '[mybook id="', '" name="" author="" year="未知" publisher="未知" media="实体" cover="" score="6" subtitle="" translater="" /]', 'p' );
    </script>
<?php
}

//0.6
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

//8.5 豆瓣电影
/**
* 作用: 显示来自豆瓣的音乐/电影/图书信息。本函数是主入口。
* 来源: 大发(bigFa)
* URL: https://github.com/bigfa/wp-douban
*/
function apip_dou_detail( $atts, $content = null ) {
    extract( shortcode_atts( array( 'id' => '', 'type' => '', 'score'=>'', 'nipple'=>'no', 'link'=>'', 'count'=>'0', 'total'=>'0', 'alt'=>'', 'series'=>'' ), $atts ) );
    $items =  explode(',', $id);
    $output = "";
    foreach ( $items as $item )  {
        if ($type == 'music') {
                $output .= apip_dou_music_detail($item, $atts);
        }
        else if ($type == 'book') {
            $output .= apip_dou_book_detail($item, $atts);
        }
        else if ($type == 'book_series') {
            $output .= apip_dou_book_list($item, $atts);
        }
        else{ //movie
                $output .= apip_dou_movie_detail($item, $atts);
        }
    }
    return $output;
}

function apip_dou_book_detail($id, $atts){
    extract( $atts );
    $data = apip_get_dou_content($id,$type = 'book');
    /*
    if(apip_is_debug_mode()) {
        $data = apip_debug_book_content();
    }
    */
    if (!$data) {
        return '';
    }
    $template = '<div class="apip-item"><div class="mod"><div class="%5$s"><div class="apiplist-post">%1$s</div><div class="title">%2$s</div><div class="rating">%3$s</div><div class="abstract">%4$s</div></div></div></div>';

    $subject_class="v-overflowHidden doulist-subject";//5
    $img_str=sprintf('<img src="%1$s" alt="%2$s"></img>',
                        apip_get_saved_images($id,str_replace('spic','mpic',$data['image']),'douban'),
                        base64_encode($data["alt"]));//1
    $title_str=sprintf('<a href="%1$s" class="cute" target="_blank" rel="external nofollow">%2$s</a>',
                        $data["alt"],
                        $data["title"]);//2
    $rating_str="";//3
    $star_dou=sprintf('<span class="dou-stars-%s"></span>',
                        round(floatval($data["rating"]["average"])));    
    $star_my="";
    
    $str_rnum="";
    $abstract_str="";//4
    
    if ( $score !== '' ) {
        $subject_class .= " my-score-".$score;
        $str_rnum = sprintf('<span class="rating_nums">(%1$s / %2$s)</span>',$score, $data["rating"]["average"]);
        $star_my = sprintf('<span class="my-stars-%s"></span>', $score);
    }
    else  {
        $str_rnum = sprintf('<span class="rating_nums">(%1$s)</span>', $data["rating"]["average"]);
    }
    $rating_str = sprintf('<span class="allstardark">%1$s%2$s</span>%3$s', $star_dou,$star_my,$str_rnum);

    $str_author=sprintf('<span class="author">作者：%s</span>', apip_convert_dou_array_to_string($data,'author',''));
    $str_trans_1 = apip_convert_dou_array_to_string($data,'translator','','xxx');
    if ('xxx' !== $str_trans_1) {
        $str_translator=sprintf('<span class="translator">译者：%s</span>', $str_trans_1);
    } else {
        $str_translator='';
    }
       
    $str_pubdate = sprintf('<span class="pubdate">出版年份：%s</span>', $data["pubdate"]);
    $str_publisher = sprintf('<span class="publisher">出版社：%s</span>', $data["publisher"]);
    $str_price = sprintf('<span class="price">定价：%s</span>', $data["price"]);
    $abstract_str = $str_author.$str_translator.$str_pubdate.$str_publisher.$str_price;


    $out = sprintf($template, $img_str, $title_str, $rating_str, $abstract_str, $subject_class);
    return $out;

}

/**
* 作用: 显示丛书中所有书籍的子函数，输入参是丛书id。
* 来源: 自创
*/
function apip_dou_book_list($id, $atts) {
    extract( $atts );
    $books = array();
    $serial_name = $alt;
    if ( 'x' == $id ) {
        $series_ids = explode(',', $series);
        $count = min($count, count($series_ids));
        $i = 0;
        for($i =0; $i<$count; ++$i) {
            if (apip_is_debug_mode()) {
                //$books[$i] = apip_debug_book_content();
                $books[$i] = apip_get_dou_content($series_ids[$i],$type = 'book');
            }else{
                $books[$i] = apip_get_dou_content($series_ids[$i],$type = 'book');
            }
            
            if (!$books[$i]) {
                $count--;
                $i--;
                continue;
            }
        }
        if (0==$count){
            return '';
        }
    }
    else{
        $cache = apip_get_dou_content($id, $type = 'book_series');
        if (!$cache) {
            return '';
        }
        $link = "//book.douban.com/series/".$id;
        $count = min($cache['count'],$cache['total']);
        $total = $cache['total'];
        $books = $cache['books'];
    }
    $start_time = $books[0]['pubdate'];
    $finish_time = $books[$count-1]['pubdate'];
    if (''==$start_time) {
        $start_time = '未知';
    }
    if (''==$finish_time) {
        $finish_time = '未知';
    }
    $pubdate = $start_time." 至 ".$finish_time;

    $template = '<div class="apip-item"><div class="mod"><div class="%4$s"><div class="title">%1$s</div><div class="abstract-left">%2$s</div>%3$s</div></div></div>';
    $class_str = "v-overflowHidden doulist-subject";//4
    $title_str = sprintf('<a href="%1$s" class="cute" target="_blank" rel="external nofollow">%2$s</a>',$link, $serial_name);//1

    $str_author = sprintf('<span class="author">作者：%s</span>', apip_convert_dou_array_to_string($books[0],'author',''));
    $str_pubdate = sprintf('<span class="pubdate">出版年份：%s</span>', $pubdate);
    $str_publisher = sprintf('<span class="publisher">出版社：%s</span>', $books[0]["publisher"]);
    $str_count = sprintf('<span class="totalcount">全套共（ %s ）册</span>', $total);
    $abstract_str = $str_author.$str_pubdate.$str_publisher.$str_count;//2
    $poster_str = "";//3

    for ($i = 0; $i < $count; ++$i ) {
        $poster_str.=sprintf('<div class="apiplist-post"><a href="%1$s" class="cute" target="_blank" rel="external nofollow"><img src="%2$s" alt="%3$s" /></a></div>',
                        $books[$i]["alt"],
                        apip_get_saved_images($books[$i]["id"],str_replace('spic','mpic',$books[$i]['image']),'douban'),
                        base64_encode($books[$i]["alt"]));
    }
    $output =sprintf($template, $title_str, $abstract_str, $poster_str, $class_str);
    return $output;
}

/**
* 作用: 显示音乐专辑详情的子函数，主要区别是格式和字段。
* 来源: 大发(bigFa)
*/
function apip_dou_music_detail($id, $atts){

    $data = apip_get_dou_content($id,$type = 'music');
    extract( $atts );
    $template = '<div class="apip-item"><div class="mod"><div class="%5$s"><div class="apiplist-post">%1$s</div><div class="title">%2$s</div><div class="rating">%3$s</div><div class="abstract">%4$s</div></div></div></div>';

    $subject_class="v-overflowHidden doulist-subject";//5
    $img_str=sprintf('<img src="%1$s" alt="%2$s"></img>',
                        apip_get_saved_images($id,str_replace('spic','mpic',$data['image']),'douban'),
                        base64_encode($data["alt"]));//1
    $title_str=sprintf('<a href="%1$s" class="cute" target="_blank" rel="external nofollow">%2$s</a>',
                        $data["alt"],
                        $data["title"]);//2
    $rating_str="";//3
    $star_dou=sprintf('<span class="dou-stars-%s"></span>',
                        round(floatval($data["rating"]["average"])));    
    $star_my="";
    
    $str_rnum="";
    $abstract_str="";//4
    
    if ( $score !== '' ) {
        $subject_class .= " my-score-".$score;
        $str_rnum = sprintf('<span class="rating_nums">(%1$s / %2$s)</span>',$score, $data["rating"]["average"]);
        $star_my = sprintf('<span class="my-stars-%s"></span>', $score);
    }
    else  {
        $str_rnum = sprintf('<span class="rating_nums">(%1$s)</span>', $data["rating"]["average"]);
    }
    $rating_str = sprintf('<span class="allstardark">%1$s%2$s</span>%3$s', $star_dou,$star_my,$str_rnum);

    $str_author=sprintf('<span class="author">表演者：%s</span>', apip_convert_dou_array_to_string($data,'author','name'));
    $str_pubdate = sprintf('<span class="pubdate">年份：%s</span>', $data["attrs"]["pubdate"][0]);
    $str_publisher = sprintf('<span class="publisher">出版社：%s</span>', $data["attrs"]["publisher"][0]);
    $abstract_str = $str_author.$str_pubdate.$str_publisher;

    $out = sprintf($template, $img_str, $title_str, $rating_str, $abstract_str, $subject_class);

    return $out;
}

/**
* 作用: 显示电影详情的子函数，主要区别是格式和字段。
* 来源: 大发(bigFa)
*/
function apip_dou_movie_detail($id, $atts) {
    $data = apip_get_dou_content($id,$type = 'movie');
    extract($atts);
    /*
    if ( apip_is_debug_mode() ){
        $data = apip_debug_movie_content();
    }
    */
    if ( empty($data) ) {
        return '';
    }

    //1:list_begin_class
    //2:img
    //3:title
    //4:rating
    //5:abstract
    $template = '<div class="apip-item"><div class="mod"><div class="%5$s"><div class="apiplist-post">%1$s</div><div class="title">%2$s</div><div class="rating">%3$s</div><div class="abstract">%4$s</div></div></div></div>';
    if ( array_key_exists('msg', $data) && "movie_not_found" === $data['msg']) {
        $output = '<div class="apip-item"><div class="mod "><div class="v-overflowHidden doulist-subject"><div class="apiplist-post"><img src="'.  APIP_PLUGIN_URL.'img/nocover.jpg" /></div>';
        $output .= '<div class="title">惨遭和谐的豆瓣资源：【'. $id .'】</div></div></div></div>';
        return $output;
    }
    $subject_class="v-overflowHidden doulist-subject";//5
    $img_str=sprintf('<img src="%1$s" alt="%2$s"></img>',
                        apip_get_saved_images($id,$data['images']['medium'],'douban'),
                        base64_encode($data["alt"]));//1
    $title_str=sprintf('<a href="%1$s" class="cute" target="_blank" rel="external nofollow">%2$s</a>',
                        $data["alt"],
                        $data["title"]);//2
    $rating_str="";//3
    $star_dou=sprintf('<span class="dou-stars-%s"></span>',
                        round(floatval($data["rating"]["average"])));    
    $star_my="";
    
    $str_rnum="";
    $abstract_str="";//4
    
    if ( $score !== '' ) {
        $subject_class .= " my-score-".$score;
        $str_rnum = sprintf('<span class="rating_nums">(%1$s / %2$s)</span>',$score, $data["rating"]["average"]);
        $star_my = sprintf('<span class="my-stars-%s"></span>', $score);
    }
    else  {
        $str_rnum = sprintf('<span class="rating_nums">(%1$s)</span>', $data["rating"]["average"]);
    }
    $rating_str = sprintf('<span class="allstardark">%1$s%2$s</span>%3$s', $star_dou,$star_my,$str_rnum);

    $str_director=sprintf('<span class="director">导演：%s</span>', apip_convert_dou_array_to_string($data,'directors','name'));
    $str_casts=sprintf('<span class="casts">演员：%s</span>', apip_convert_dou_array_to_string($data,'casts','name'));   
    $str_genres=sprintf('<span class="genres">类型：%s</span>', apip_convert_dou_array_to_string($data,'genres',''));
    $str_year = sprintf('<span class="year">年份：%s</span>', $data["year"]);
    $abstract_str = $str_director.$str_casts.$str_genres.$str_year;

    if ("yes"===$nipple) {
        $abstract_str .= '<span class="feature">奶</span>';
    }

    $out = sprintf($template, $img_str, $title_str, $rating_str, $abstract_str, $subject_class);
    return $out;
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
* 作用: 从doubanapi取得数据的子函数。
* 来源: 大发
*/
function apip_get_dou_content( $id, $type )  {

    $type = $type ? $type : 'movie';
    $cache_key = $type . '_' . $id;
    //申请缓存
    $cache =  get_transient($cache_key);
    if ($cache)  {
        if (count($cache) == 3){//error
            delete_transient($cache_key);
            unset($cache);
        } else {
        return $cache;
        }
    }
    global $apip_options;
    $apikey = $apip_options['douban_key'];
    if( empty($apikey) )
    {
        return false;
    }
    if ( $type == 'movie') {
        $link = "https://api.douban.com/v2/movie/subject/".$id."?apikey=".$apikey;
    } elseif ( $type == 'book' ) {
        $link = "https://api.douban.com/v2/book/".$id."?apikey=".$apikey;
        //$link = "http://isbn.szmesoft.com/isbn/query?isbn=" . $id;
        //$link = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $id;
        //20190507因为豆瓣图书API已经关闭，所以废掉了。
        //20191219早已复活，使用搜索得到的API KEY

    } elseif ($type == 'book_series') {
        $link = "https://api.douban.com/v2/book/series/".$id."/books?apikey=".$apikey."&count=36";
        //apip_debug_page($link,'douapi');
    }
    else {
        $link = "https://api.douban.com/v2/music/".$id."?apikey=".$apikey;
    }

    $args = array(
        'timeout' => 15000,
        'sslverify' => false,
        'headers' => array(
          'Content-Type' => 'application/json;charset=UTF-8',
          'Accept' => 'application/json',
        ),);
    $response = @wp_remote_get($link);
    if (is_wp_error( $response ))
    {
        return false;
    }
    delete_transient($cache_key);
    $cache = json_decode(wp_remote_retrieve_body($response),true);
    $cache = apip_slim_dou_cache($cache, $type);
    set_transient($cache_key, $cache, 60*60*24*30*6);

	return $cache;
}

/**
* 作用: 用于保存图像缓存的子函数。
* 来源: 大发
*/
function apip_get_saved_images($id, $src, $dst )  {

    if ( apip_is_debug_mode() )
    {
        //return APIP_GALLERY_URL.'douban_cache/26752106.jpg';
    }
    if ( 'douban'===$dst ) {
        $thumb_path = APIP_GALLERY_DIR . 'douban_cache/';
    } else {
        $thumb_path = APIP_GALLERY_DIR . 'game_poster/';
    }

    /*
    $e = $thumb_path. $id .'.jpg';
    $regen = 0;
    if (!is_file($e)) {
        $regen = 1;
    }
    else {
        $imglocal = new Apip_SimpleImage();
        $imglocal->load($e);
        if ($imglocal->getWidth() != 100) {
            unlink($e);
            $regen = 1;
        }
    }
    
    if ( $regen) {
        $imgstream = new Apip_SimpleImage();
        $imgstream->load($src);
        $imgstream->resize(100, 150);
        $imgstream->save($e);
    }
    */

    $imagetype = substr(strrchr($src,'.'),0);
    $e = $thumb_path. $id .'.jpg';
    $e_temp = $thumb_path. $id .$imagetype;

    if (is_file($e))
    {
        $imglocal = new Apip_SimpleImage();
        $imglocal->load($e);
        if ($imglocal->getWidth() != 100) {
            unlink($e);
        }
    }

    if ( !is_file($e) ) {
        $response = @wp_remote_get( 
            htmlspecialchars_decode($src), 
            array( 
                'timeout'  => 5000, 
                'stream'   => true, 
                'filename' => $e_temp 
            ) 
        );
        if ( is_wp_error( $response ) )
        {
            if (is_file($e_temp)) {
                unlink($e_temp);
            }
            $url = APIP_PLUGIN_URL."img/nocover.jpg";
            return $url;
        } 
        $image = new Apip_SimpleImage();
        $image->load($e_temp);
        $image->resize(100, 150);
        $image->save($e);
        if ($imagetype != ".jpg") {
            unlink($e_temp);
        }
    }

    if ( 'douban'===$dst ) {
        $url =APIP_GALLERY_URL.'douban_cache/'. $id .'.jpg';
    } else {
        $url =APIP_GALLERY_URL.'game_poster/'. $id .'.jpg';
    }

    return $url;
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


function apip_is_debug_mode()
{
    if (isset( $_SERVER['REDIRECT_TMP'] ) && strpos($_SERVER['REDIRECT_TMP'], "xampp" ) > 0)
    {
        return 1;
    }
    return 0;
}

/**
* 作用: theimdbapi.org取得电影资料，用于豆瓣无资料的电影。
* 来源: 受大发启示，自作
* API格式： https://www.omdbapi.com/?i=tt3896198&apikey=36edb41f
*/
function apip_imbd_detail($atts, $content = null){
    extract( shortcode_atts( array( 'id' => '0', 'cname'=>'','alias'=>'','score'=>'','nipple'=>'no' ), $atts ) );
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
    $meta_class='';
    if ("yes"===$nipple) {
        $meta_class="has-nipple";
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
    
    if ( $score !== '' ) {
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
    $str_countries=sprintf('<span class="country">地区：%s</span>', str_replace(',','/',$content["Country"]));
    $str_year = sprintf('<span class="year">年份：%s</span>', $content["Year"]);
    $abstract_str = $str_director.$str_casts.$str_genres.$str_countries.$str_year;

    if ("yes"===$nipple) {
        $abstract_str .= '<span class="feature">奶</span>';
    }

    $out = sprintf($template, $img_str, $title_str, $rating_str, $abstract_str, $subject_class);
    return $out;


}

//8.6游戏资料
/**
* 作用: 从giantbomb.com取得游戏资料，显得正规。
* 来源: 受大发启示，自作
* API格式：https://www.giantbomb.com/api/game/THE_GAME_ID/?api_key=YOUR_TOKEN&format=json&field_list=site_detail_url,genres,image,platforms,original_release_date,name,publishers
*/
function apip_game_detail($atts, $content = null) {
    extract( shortcode_atts( array( 'id' => '0', 'cname'=>'','alias'=>'', 'ename'=>'', 'jname'=>'', 'year'=>'', 'download'=>'','platform'=>'','publisher'=>'','genres'=>'','poster'=>'' ), $atts ) );
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

//8.9 文章发布前，通过调用yandex translate的API，手动生成英文slug。
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
    add_meta_box('apipslugdiv', 'Slug and translate', 'apip_title_translate_meta_box', 'post', 'normal', 'core');
    //8.10
    add_meta_box('apipcolorthiefdiv', 'Color thief', 'apip_colorthief_meta_box', 'post', 'normal', 'core');
}

/*
yandex translate的文档
https://tech.yandex.com/translate/doc/dg/reference/translate-docpage/
*/
function apip_title_translate_meta_box( $post ){
    $editable_slug = apply_filters( 'editable_slug', $post->post_name, $post );//照抄
    ?>
    <label class="screen-reader-text" for="post_name"><?php _e('Slug') ?></label><input name="post_name" type="text" size="30" id="post_name" value="<?php echo esc_attr( $editable_slug ); ?>" />&nbsp;<button class="button"  type="button" name="apiptranlatebtn" >翻译或更新</button>
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

/*                                          08终了                             */

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

/**
 * 作用: 替换留言框下方的提示，暂时废弃
 * 来源: customizr论坛
 * URL:
 */
function apip_replace_tag_note( $defaults )
{
    $notice = "<em> 不懂问，不爽骂，无语右上有红叉。确定要按下按钮吗？</em>";
    $defaults['comment_notes_after'] = $notice;
    return $defaults;
}

/**
 * 20200416 整合所有template_redirect的钩子到同一函数
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

//goto 链接钩子
//link：https://www.lingchenzi.com/2019/01/wordpress-waibulianjie-neilian-base64.html
