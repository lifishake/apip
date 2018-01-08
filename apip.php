<?php

/**
 * Plugin Name: All plugins in pewae
 * Plugin URI:  http://pewae.com
 * GitHub Plugin URI: https://github.com/lifishake/apip
 * Description: Plugins used by pewae
 * Author:      lifishake
 * Author URI:  http://pewae.com
 * Version:     1.24.3
 * License:     GNU General Public License 3.0+ http://www.gnu.org/licenses/gpl.html
 */

/*宏定义*/
define('APIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('APIP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ) ;
define('APIP_GALLERY_URL',home_url('/').'wp-content/gallery/');
define('APIP_GALLERY_DIR', ABSPATH.'wp-content/gallery/');
global $apip_options;

register_activation_hook( __FILE__, 'apip_plugin_activation' );
register_deactivation_hook( __FILE__,'apip_plugin_deactivation' );
register_uninstall_hook(__FILE__, 'apip_plugin_deactivation');

/*插件激活*/
function apip_plugin_activation()
{
    global $wpdb;
    /*因为是视图，所以每次都创建也无所谓*/
    $sql = "CREATE OR REPLACE VIEW `{$wpdb->prefix}v_posts_count_yearly`
     AS SELECT DISTINCT YEAR(post_date) AS `year`, COUNT(ID) AS `count`
    FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'
    GROUP BY year ORDER BY year DESC ; ";
    $wpdb->query($sql);

    $sql = "CREATE OR REPLACE VIEW `{$wpdb->prefix}v_posts_count_monthly`
     AS SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, COUNT(ID) AS `count`, GROUP_CONCAT(ID) AS `posts`
     FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'
     GROUP BY year, month ORDER BY year, month DESC ; ";
    $wpdb->query($sql);

    $sql = "CREATE OR REPLACE VIEW `{$wpdb->prefix}v_taxonomy_summary`
     AS SELECT rel.`term_taxonomy_id`, COUNT(rel.`term_taxonomy_id`) AS `term_count`, tax.`taxonomy`, {$wpdb->prefix}terms.`name` AS `term_name`,
    CASE WHEN tax.`taxonomy` = 'post_tag' THEN 10
    WHEN tax.`parent` = 0 THEN 0
    WHEN tax.`parent` IN (SELECT `term_taxonomy_id` FROM `{$wpdb->prefix}term_taxonomy` WHERE `parent` = 0 ) THEN 1
    WHEN tax.`parent` IN (SELECT `term_taxonomy_id` FROM `{$wpdb->prefix}term_taxonomy` WHERE `parent` IN (SELECT `term_taxonomy_id` FROM `{$wpdb->prefix}term_taxonomy` WHERE `parent` = 0 )) THEN 2
    ELSE 3 END AS `term_level`,
    CASE WHEN tax.`taxonomy` = 'post_tag' THEN FLOOR(4096/COUNT(rel.`term_taxonomy_id`))
    WHEN tax.`parent` = 0 THEN 10
    WHEN tax.`parent` = 2599 THEN 1024
    WHEN tax.`parent` IN (SELECT `term_taxonomy_id` FROM `{$wpdb->prefix}term_taxonomy` WHERE `parent` = 0 ) THEN CEIL(1024/COUNT(rel.`term_taxonomy_id`))
    WHEN tax.`parent` IN (SELECT `term_taxonomy_id` FROM `{$wpdb->prefix}term_taxonomy` WHERE `parent` IN (SELECT `term_taxonomy_id` FROM `{$wpdb->prefix}term_taxonomy` WHERE `parent` = 0 )) THEN 1000
    ELSE 1580 END AS `term_weight`
    FROM `{$wpdb->prefix}term_relationships` rel, `{$wpdb->prefix}term_taxonomy` tax, {$wpdb->prefix}terms WHERE rel.`term_taxonomy_id` = tax.`term_taxonomy_id` AND tax.`taxonomy` in ('category','post_tag') AND {$wpdb->prefix}terms.`term_id` = rel.`term_taxonomy_id` GROUP BY rel.`term_taxonomy_id` ORDER BY `term_count` DESC ";
    $wpdb->query($sql);

    $sql = "CREATE OR REPLACE VIEW `{$wpdb->prefix}v_boring_summary`
         AS SELECT `comment_author_email`, SUM(`meta_value`) AS `boring_value`
    FROM `{$wpdb->prefix}comments`
    LEFT JOIN `wp_commentmeta` ON `{$wpdb->prefix}comments`.`comment_ID` = `{$wpdb->prefix}commentmeta`.`comment_id`
    WHERE `user_id` = 0 AND `meta_key` = '_boring_rank'
    AND DATE_SUB( CURDATE( ) , INTERVAL 6 MONTH ) <= `comment_date_gmt`
    GROUP BY comment_author_email
    ORDER BY comment_author_email ASC ";
    $wpdb->query($sql);

    //8.5
    $thumb_path = APIP_GALLERY_DIR . "/douban_cache";

    if (file_exists ($thumb_path)) {
        if (! is_writeable ( $thumb_path )) {
            @chmod ( $thumb_path, '511' );
        }
    } else {
        @mkdir ( $thumb_path, '511', true );
    }

    //8.6
    $thumb_path = APIP_GALLERY_DIR . "/game_poster";
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
    global $wpdb;
    /*因为是视图，所以每次都删除也无所谓*/
    $sql = "DROP VIEW IF EXISTS `{$wpdb->prefix}v_posts_count_yearly` ; ";
    $wpdb->query($sql);
    $sql = "DROP VIEW IF EXISTS `{$wpdb->prefix}v_posts_count_monthly` ; ";
    $wpdb->query($sql);
    $sql = "DROP VIEW IF EXISTS `{$wpdb->prefix}v_taxonomy_summary` ; ";
    $wpdb->query($sql);
    $sql = "DROP VIEW IF EXISTS `{$wpdb->prefix}v_boring_summary` ; ";
    $wpdb->query($sql);
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

/*变量初期化*/
add_action('plugins_loaded', 'apip_init', 99);
function apip_init()
{
    /** 00 */
    global $wpdb;
    //wpdb->apipvpcy = $wpdb->prefix.'v_posts_count_yearly';
    //wpdb->apipvpcm = $wpdb->prefix.'v_posts_count_monthly';
    //wpdb->apipvts = $wpdb->prefix.'v_taxonomy_summary';

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
    add_action( 'admin_enqueue_scripts', 'apip_remove_open_sans' );
    //0.7 自带的TagCloud格式调整
    add_filter( 'widget_tag_cloud_args', 'apip_resort_tagcloud' ) ;
    //0.8 移除后台的“作者”列
    add_filter( 'manage_posts_columns', 'apip_posts_columns' );
    //0.9 升级后替换高危文件
    add_action( 'upgrader_process_complete', 'apip_remove_default_risk_files', 11, 2 );
    //0.10 作者页跳转到404
    add_action('template_redirect', 'apip_redirect_author');
    //0.11 屏蔽留言class中的作者名
    add_filter('comment_class', 'apip_remove_author_class', 10, 5);
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
    if ( apip_option_check('redirect_if_single') )
    {
        //2.7搜索结果只有一条时直接跳入
        add_action('template_redirect', 'redirect_single_post');
    }
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
    //5.2 用户留言等级评分
    if ( apip_option_check('commentator_rating_enable') ) {
        //后台动作增加
        add_filter( 'comment_row_actions', 'apip_show_commentator_rate', 11, 2 );
        //增加ajax回调函数
        add_action( 'wp_ajax_set_boring_comment_rank', 'apip_set_boring_comment_rank' );
        //在comment模板的合适地方增加filter'apip_placeholder_text'后才有效。
        add_filter( 'apip_placeholder_text', 'apip_replace_placeholder_text');
        //在comment模板的合适地方增加filter'apip_submit_status'后才有效。
        add_filter( 'apip_submit_status', 'apip_check_submit_status');
        //如果使用传统comment_form,则下面一行生效。
        add_filter( 'comment_form_defaults', 'apip_replace_triditional_comment_placeholder_text');
        //针对废话的css惩罚
        add_filter('comment_class', 'apip_add_boring_comment_style');
    }
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
    add_action( 'draft_to_publish','apip_save_heweather',10,1);
    add_action( 'draft_to_parvite','apip_save_heweather',10,1);

    /** 08 */
    //头部动作，一般用于附加css的加载
    //add_action('get_header','apip_header_actions') ;
    //8.1 prettyprint脚本激活
    add_action('get_footer','apip_footer_actions') ;

    //8.2 lazyload
    if ( apip_option_check('apip_lazyload_enable') )  {
        add_filter( 'the_content', 'apip_lazyload_filter',200 );
        add_filter( 'post_thumbnail_html', 'apip_lazyload_filter',200 );
    }

    //8.3 结果集内跳转
    if ( apip_option_check('range_jump_enable') ) {
        if ( !class_exists('Apip_Query') ) {
            //包跳转类含头文件
            require_once ( APIP_PLUGIN_DIR.'/class/apip-query.php') ;
        }
        $key = 'apip_aq_'.COOKIEHASH;//根据cookie生成标识
        $apip_aq = get_transient($key);
        if ( false === $apip_aq ){
            $apip_aq = new Apip_Query();
        }
        if ( !$apip_aq->isloaded() ){
            $apip_aq->init();
        }
        set_transient( $key, $apip_aq, 600);//保留10分钟
        add_action('template_redirect', 'apip_keep_quary', 9 );//优先级比直接跳转到文章的略高。
    }
    //8.4 留言邮件回复
    if ( apip_option_check('notify_comment_reply') )  {
    //邮件回复
        add_action('wp_insert_comment','apip_comment_inserted',99,2);
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
        apip_remove_anonymous_object_hook( 'the_content', 'M_Gallery_Display', '_render_related_images' );
        apip_remove_anonymous_object_hook( 'wp_enqueue_scripts', 'M_Gallery_Display', 'no_resources_mode' );
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
        apip_remove_anonymous_object_hook( 'wp_enqueue_scripts', 'C_NextGEN_Bootstrap', 'fix_jquery' );
        apip_remove_anonymous_object_hook( 'wp_print_scripts', 'C_NextGEN_Bootstrap', 'fix_jquery' );
    }
    if( class_exists('C_Lightbox_Library_Manager') )  {
        apip_remove_anonymous_object_hook( 'wp_enqueue_scripts', 'C_Lightbox_Library_Manager', 'maybe_enqueue' );
    }
    /*
    if( class_exists('C_Photocrati_Resource_Manager') )
    {
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
}

function apip_header_actions()
{
    global $apip_options ;
    //7.1
    /*if ( is_page('my-tag-cloud') && $apip_options['apip_tagcloud_enable']== 1 )
    {
    wp_enqueue_style( 'apip_tagcloud_style', APIP_PLUGIN_URL . 'css/apip-tagcloud.css' );
    }*/
    //8.1
    /*if ( in_category('code_share') && $apip_options['apip_codehighlight_enable'] == 1 )
    {
        add_filter('the_content', 'apip_code_highlight') ;
        wp_enqueue_style( 'prettify_style', APIP_PLUGIN_URL . 'css/apip-prettify.css' );
    }*/
}

/*
$options
00.                                     无选项，必须选中的内容
    0.1                                 Ctrl+Enter提交
    0.2                                 屏蔽不必要的js
    0.3                                 屏蔽不必要的style
    0.4                                 feed结尾的追加内容
    0.5                                 追加的快捷按钮
    0.6                                 屏蔽后台的OpenSans
    0.7                                 调整默认的TagCloud Widget
    0.8                                 移除后台的作者列
    0.9                                 版本升级后自动替换掉危险文件(wp-comments-post.php,xmlrpc.php)
    0.A                                 移除无用的钩子
01.     颜色选项
02.     高级编辑选项
    2.1     save_revisions_disable         阻止自动版本
    2.2     auto_save_disabled              阻止自动保存
    2.3     show_admin_bar                   显示登录用户的admin bar
    2.4     apip_locale                            后台英文前台中文
    2.5     block_open_sans                  屏蔽后台的open sans字体
    2.6     show_author_comment         屏蔽作者留言
    2.7     redirect_if_single                   搜索结果只有一条时直接跳入
    2.8     protect_comment_php          禁止直接访问wp_comments.php
    2.9     search_without_page           搜索结果中屏蔽page
03.     header_description                   头部描述信息
    3.1     hd_home_text                       首页描述文字
    3.2     hd_home_keyword               首页标签
    3.3     excerpt_length                      摘要长度
    3.4     excerpt_ellipsis                     摘要结尾字符
04.     GFW选项
    4.1     local_gravatar                        头像本地缓存
    4.2     replace_emoji                        替换emoji地址
05.    留言者控制
   5.1  blocked_commenters                 替换广告留言用户名和网址
   5.2     apip_show_commentator_rate  为留言评分
06.     social_share_enable                     社会化分享使能
07.     自定义的shortcode
    7.1     apip_tagcloud_enable            更好看的标签云
    7.2     apip_link_page                       自定义友情链接
    7.3     apip_achive_page                  自定义归档页
08.     比较复杂的设定
    8.1     apip_codehighlight_enable     代码高亮
    8.2     apip_lazyload_enable             LazyLoad
    8.3                                                    结果集内跳转
    8.4.    notify_comment_reply            有回复时邮件提示
    8.5                                                    豆瓣电影
    8.6                                                     gaintbomb游戏信息
    8.7     heweather_key                       和风天气/发帖时天气信息
99.     local_widget_enable                  自定义小工具
    99.1    local_definition_count           自定义widget条目数
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
    wp_enqueue_style( 'apip-style-all', APIP_PLUGIN_URL . 'css/apip-all.css' );
    $css = '';
    //所有要加载fontAowsem的情况
    if ( ( is_singular() && apip_option_check('social_share_enable') ) ||
         ( is_page('my_links') && apip_option_check('apip_link_enable') ) ||
         (is_singular() && (in_category('appreciations') || in_category('relisten_moring_songs') ) && apip_option_check('apip_douban_enable') ) ||
         has_tag('testcode') )
    {
        $css .= "   @font-face {
                      font-family: 'FontAwesome';
                      src: url('".APIP_PLUGIN_URL."/fonts/fontawesome-webfont.eot?v=4.3.0');
                      src: url('".APIP_PLUGIN_URL."/fonts/fontawesome-webfont.eot?#iefix&v=4.3.0') format('embedded-opentype'), url('".APIP_PLUGIN_URL."/fonts/fontawesome-webfont.woff2?v=4.3.0') format('woff2'), url('".APIP_PLUGIN_URL."/fonts/fontawesome-webfont.woff?v=4.3.0') format('woff'), url('".APIP_PLUGIN_URL."/fonts/fontawesome-webfont.ttf?v=4.3.0') format('truetype'), url('".APIP_PLUGIN_URL."/fonts/fontawesome-webfont.svg?v=4.3.0#fontawesomeregular') format('svg');
                      font-weight: normal;
                      font-style: normal;
                    }
        ";
    }

    //0.1 Ctrl+Enter 提交
    if (comments_open() && is_singular() ) {
    wp_enqueue_script('apip-js-singular', APIP_PLUGIN_URL . 'js/apip-singular.js', array(), false, true);
    }
    //07
    if  ( is_singular() && apip_option_check('social_share_enable') )
    {
    wp_enqueue_script('apip-js-social', APIP_PLUGIN_URL . 'js/apip-social.js', array(), false, true);
    //wp_enqueue_style( 'apip-style-social', APIP_PLUGIN_URL . 'css/apip-social.css' );
        $css .= '   #sharebar{
                        clear:both;
                        background: none repeat scroll 0 0 #EEFAF6;
                        line-height: 2em ;
                    }

                    #sharebar span{
                        padding: 0 15px;
                        margin:4px 0 ;
                        color: #5a5a5a;
                    }

                    #sharebar a {
                        background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
                        display: inline;
                        font-size: 1.3em;
                        height: inherit;
                        line-height: inherit;
                        margin: 0;
                        opacity: 0.8;
                        padding-right: 13px;
                        position: relative;
                        text-decoration: none;
                        top: 2px;
                        vertical-align: inherit;
                        width: inherit;
                        cursor:pointer;
                    }

                    #sharebar a:before {
                        font-family: \'FontAwesome\' ;
                        font-variant: normal;
                        font-weight: 400;
                        line-height: 1;
                        text-transform: none;
                    }

                    .sharebar-twitter:before{
                        content: "\f099" ;
                    }
                    .sharebar-weibo:before {
                      content: "\f18a";
                    }
                    .sharebar-tencent-weibo:before {
                      content: "\f1d5";
                    }
                    .sharebar-googleplus:before {
                      content: "\f0d5";
                    }
                    .sharebar-facebook:before {
                      content: "\f230";
                    }';
    }
    //7.1
    if ( is_page('my-tag-cloud') && apip_option_check('apip_tagcloud_enable') )
    {
        $css .= '   ul.tagcloud, ul.tagcloud li {
                        font-size: 1em;
                        list-style-type: none;
                        padding: 0;
                        margin: 0;
                    }
                    ul.tagcloud li {
                        display: inline;
                        line-height: 2.8em;
                    }
                    ul.tagcloud a {
                        text-decoration: none;
                        margin: 5px;
                        border-radius: 3px;
                    }
                    ul.tagcloud a:hover {
                        color:#BF52A9;
                        border-radius: 3px;
                    }
                    a.tagged1 {
                        font-size: 1.00em;
                        color: #AF7E62;
                        font-weight: 300;
                        padding: 6px 11px;
                        background: rgba(240,121,142,0.6);
                        }

                    a.tagged2 {
                        font-size: 1.20em;
                        color: #B25E2A;
                        font-weight: 400;
                        padding: 9px 14px;
                        background: rgba(129,240,127,0.6);
                        }
                    a.tagged3 {
                        font-size: 1.50em;
                        color:#995124;
                        font-weight: 400;
                        padding: 12px 17px;
                        background: rgba(48,120,240,0.6);
                    }
                    a.tagged4 {
                        font-size: 1.80em;
                        color:#663718;
                        font-weight: 500;
                        padding: 15px 20px;
                        background: rgba(231,170,240,0.6);
                        }
                    a.tagged5 {
                        font-size: 2.20em;
                        color:#331C0C;
                        font-weight: 700;
                        padding: 18px 23px;
                        background: rgba(240,76,73,0.6);
                        }';
    }
    //7.2
    if ( is_page('my_links') && apip_option_check('apip_link_enable') )
    {
    //wp_enqueue_style( 'apip-link-style', APIP_PLUGIN_URL . 'css/apip-links.css' );
        $css .= '   .apip-links {
                        display:inline-block;
                    }

                    .apip-links > li {
                        display: inline;
                        float: left;
                        line-height: 80px;
                        text-align: center;
                        width: 128px;
                    }

                    .commenter-link.vcard {
                        padding: 5px 5px 0;
                    }
                    .url::after {
                        color: '.$color_link.';
                        content: "\f0c1";
                        font-family: Fontawesome;
                        font-size: 12px;
                        left: -2px;
                        margin: -5px 0 0 1px;
                        position: relative;
                        top: -7px;
                    }

                    .commenter-link img {
                        border: 2px solid '.$color_link.';
                        border-radius: 100%;
                    }
        ';
    }
    //7.3
    if ( (is_page('archive')||is_page('archives')) && apip_option_check('apip_archive_enable') )
    {
        $css .= '   .apip-no-disp {
                        display: none !important;
                        -webkit-transition: display 0.2s;
                        transition: display 0.2s;
                    }
                    .achp-expanded {
                        font-weight:800;
                    }
                    li.achp-child {
                        position: relative;
                        text-overflow: ellipsis;
                        max-width: 100%;
                        overflow: hidden;
                        max-height: 1.25em;
                    }
                    a.achp-sig {
                        box-shadow: none !important;
                    }
                    span.achp-symbol {
                        font-family: monospace, monospace;
                        font-weight: 800;
                        line-height: inherit;
                        margin: 0 10px 0;
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

        wp_enqueue_script('apip-js-achp', APIP_PLUGIN_URL . 'js/apip-achp.js', array(), false, true);
    }
    //8.1
    if ( (in_category('code_share') || has_tag('testcode')) && apip_option_check('apip_codehighlight_enable') == 1 )
    {
    add_filter('the_content', 'apip_code_highlight') ;
        $css .= '   pre.prettyprint {
                        display: block;
                        background-color: #333;
                        text-shadow: none;
                        }
                    pre .nocode {
                        background-color:none;
                        color: #000;
                        }
                    pre .str {
                        color: #ffa0a0;
                        }
                    pre .kwd {
                        color: #f0e68c;
                        font-weight: 700;
                        }
                    pre .com {
                        color: #87ceeb;
                        }
                    pre .typ {
                        color: #98fb98;
                        }
                    pre .lit {
                        color: #cd5c5c;
                        }
                    pre .pun {
                        color: #fff;
                        }
                    pre .pln {
                        color: #fff;
                        }
                    pre .tag {
                        color: #f0e68c;
                        font-weight: 700;
                        }
                    pre .atn {
                        color: #bdb76b;
                        font-weight: 700;
                        }
                    pre .atv {
                        color: #ffa0a0;
                        }
                    pre.dec {
                        color: #98fb98;
                        }
                    ol.linenums {
                        margin-top: 0;
                        margin-bottom: 0;
                        color: #AEAEAE;
                        }
                    li.L0, li.L1, li.L2, li.L3, li.L5, li.L6, li.L7, li.L8 {
                        list-style-type: none;
                        }';
    wp_enqueue_script('apip-js-prettify', APIP_PLUGIN_URL . 'js/apip-prettify.js', array(), false, true);
    }
    //8.2
    if ( apip_option_check('apip_lazyload_enable') ) {
        $css .= '   img[data-unveil="true"] {
                        opacity: 0;
                        -webkit-transition: opacity .3s ease-in;
                        -moz-transition: opacity .3s ease-in;
                        -o-transition: opacity .3s ease-in;
                        transition: opacity .3s ease-in;
                        }';
    wp_enqueue_script('apip_js_lazyload', APIP_PLUGIN_URL . 'js/unveil-ui.min.js', array(), false, true);
    }

    //8.5
     if (is_singular() && (in_category('appreciations') || in_category('relisten_moring_songs') || has_tag('testcode')) && apip_option_check('apip_douban_enable') ) {
         $css .= '.allstarlight:before,.allstardark:before {
                      font-family:"FontAwesome" !important;
                      font-size:inherit;
                      font-style:normal;
                      -webkit-font-smoothing: antialiased;
                      -webkit-text-stroke-width: 0.2px;
                      -moz-osx-font-smoothing: grayscale;
                    }
                    .allstardark{position:relative;color:#f99b01;display: inline-block;vertical-align: top;}
                    .allstarlight{position:absolute;left:0;color:#f99b01;height:18px;overflow:hidden}
                    .allstarlight:before{content:"\f005\f005\f005\f005\f005"}
                    .allstardark:before{content:"\f006\f006\f006\f006\f006"} ';
     }

    if ( $css !== '' ) {
        wp_add_inline_style('apip-style-all', $css);
    }
}

function apip_admin_scripts() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'apip-style-option', APIP_PLUGIN_URL . 'css/apip-option.css' );
    wp_enqueue_script('apip-js-admin', APIP_PLUGIN_URL . 'js/apip-admin.js', array('wp-color-picker' ), false, true);
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
    $addi = sprintf( '<div style="max-width: 520px; margin:0 auto; padding:5px 30px;margin: 15px; border-top: 1px solid #CCC;"><span style="margin-left: 2px; display:block;">《%1$s》采用<a rel="license" href="//creativecommons.org/licenses/by-nc-nd/3.0/cn/deed.zh">署名-非商业性使用-禁止演绎</a>许可协议进行许可。 『%2$s』期待与您交流。</span><div style="display:table;">%3$s %4$s</div></div>',
                        sprintf( '<a href="%1$s">%2$s</a>' , get_permalink(get_the_ID()), get_the_title() ),
                        sprintf( '<a href="%1$s">%2$s</a>' , get_bloginfo('url'), get_bloginfo('name') ),
                        sprintf('<div style="margin: 5px 25px; display:table-cell; max-width:300px; "><h3 style="font-size:16px; font-weight:800;" >相关推荐:</h3>%s</div>', apip_related_post() ),
                        sprintf('<div style="margin: 5px 25px; display:table-cell; max-width:300px; "><h3 style="font-size:16px; font-weight:800;" >历史同日文章:</h3>%s</div>', apip_sameday_post() )
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
        QTags.addButton( 'eg_mydoubanmovie', '豆瓣电影', '[mydouban id="', '" type="movie"]', 'p' );
        QTags.addButton( 'eg_myimbd', 'imbd', '[myimdb id="', '" cname="" ]', 'p' );
        QTags.addButton( 'eg_mydoubanmusic', '豆瓣音乐', '[mydouban id="', '" type="music"]', 'p' );
        QTags.addButton( 'eg_mydoubanbook', '豆瓣读书', '[mydouban id="', '" type="book"]', 'p' );
        QTags.addButton( 'eg_mygame', '每夜一游', '[mygame id="', '" cname="" ename="" jname="" alias="" year="" publisher=""  platform="" download="" genres="" poster=""]', 'p' );
    </script>
<?php
}

//0.6
/**
 * 作用: 去掉后台的Open Sans
 * 来源: 自产
 * URL:
 */
function apip_remove_open_sans() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
}

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
        $wp_filesystem->copy( APIP_PLUGIN_DIR.'/ext/wp-go-die.php', $wp_dir.'wp-comments-post.php', true );
        $wp_filesystem->copy( APIP_PLUGIN_DIR.'/ext/wp-go-die.php', $wp_dir.'xmlrpc.php', true );
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
    if (is_search()||is_archive()) {
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
        //gravatar.eqoe.cn
        $src = array('www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com','secure.gravatar.com');
        if( is_ssl() ){
            $replace = 'sdn.geekzu.org';
        }
        else{
            $replace = 'fdn.geekzu.org';/*'gravatar.css.network'*/
        }
    $source = str_replace( $src, $replace, $source);
        return $source ;
    }
    $time = 1209600; //The time of cache(seconds)
    preg_match('/avatar\/([a-z0-9]+)\?s=(\d+)/',$source,$tmp);
    $abs = ABSPATH.'wp-content/plugins/feature-in-one-custom/iava/'.$tmp[1].'.jpg';
    $url = get_bloginfo('wpurl').'/wp-content/plugins/feature-in-one-custom/iava/'.$tmp[1].'.jpg';
    $default = get_bloginfo('wpurl').'/wp-content/plugins/feature-in-one-custom/iava/'.'default.png';
    if (!is_file($abs)||(time()-filemtime($abs))>$time){
        copy('http://www.gravatar.com/avatar/'.$tmp[1].'?s=64&d='.$default.'&r=G',$abs);
    }
    if (filesize($abs)<500) { copy($default,$abs); }
    return '<img alt="" src="'.$url.'" class="avatar avatar-'.$tmp[2].'" width="'.$tmp[2].'" height="'.$tmp[2].'" />';
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

//5.2 根据用户留言质量评定用户水平，并进行相应操作
function apip_show_commentator_rate( $actions, $comment ) {
    $desc = null;
    $level = 0;
    $n = 0;
    $query = '';
    //$comment->comment_author_email;
    $n = intval(get_comment_meta($comment->comment_ID, '_boring_rank', true));

    $boring_nonce = wp_create_nonce( "boring-comment_".$comment->comment_ID );
    $selector = '<select id="set-boring-rank" name="boring-rank">
    <option value="0">正常</option><option value="2">哦</option><option value="3">呵呵</option><option value="6">SoWhat</option></select>';
    $format = '<span data-comment-id="%d" data-post-id="%d" wp_nonce="%s" class="%s" ><span class="set-boring-rank-label">无聊等级&nbsp;(%d)&nbsp;</span>%s</span>';
    $actions['boringrank'] = sprintf($format, $comment->comment_ID, $comment->comment_post_ID, $boring_nonce, 'boring-level', $n, $selector );

    return $actions;
}

function apip_replace_placeholder_text( $text ) {
    $commenter = wp_get_current_commenter();
    global $wpdb;
    if ( isset($commenter) ) {
        $email = esc_attr($commenter['comment_author_email']);
    }
    if ( !$email || $email == '' ) {
        return $text;
    }
    $sql = "SELECT `boring_value` FROM {$wpdb->prefix}v_boring_summary WHERE `comment_author_email` = '{$email}' ";
    $vals = $wpdb->get_col( $sql );
    if ( count($vals) >= 1 ) {
        if ( $vals[0] > 12 ) {
            $text = '你留下的废话太多，博主已经决定跟你断绝往来。';
        }
        else if ( $vals[0] >= 6 ) {
            $text = '你最近六个月的回复已经惹得博主不高兴了。请用心回复，谨防友尽。';
        }
    }
    return $text;
}

function apip_check_submit_status( $type ) {
    $commenter = wp_get_current_commenter();
    global $wpdb;
    if ( isset($commenter) ) {
        $email = esc_attr($commenter['comment_author_email']);
    }
    if ( !$email || $email == '' ) {
        return $type;
    }
    $sql = "SELECT `boring_value` FROM {$wpdb->prefix}v_boring_summary WHERE `comment_author_email` = '{$email}' ";
    $vals = $wpdb->get_col( $sql );
    if ( count($vals) >= 1 && $vals[0] > 12 ) {
        $type = 'hidden';
    }
    return $type;
}

function apip_set_boring_comment_rank() {
    $comment_id = $_POST['id'];
    if ( !wp_verify_nonce($_POST['nonce'],"boring-comment_".$comment_id))
        die();
    $old = get_comment_meta($comment_id, '_boring_rank', true);
    if ( $_POST['level'] == 0 ) {
        delete_comment_meta($comment_id, '_boring_rank');
    }
    else if ( is_null($old) || ""=== $old ) {
        add_comment_meta($comment_id, '_boring_rank', $_POST['level'], true);
    }
    else {
        update_comment_meta($comment_id, '_boring_rank', $_POST['level']);
    }

}

function apip_replace_triditional_comment_placeholder_text( $default ) {
    $text = apip_replace_placeholder_text('请不要留下无趣的东西浪费大家时间。');
    $default['field'] = sprintf('<p class="comment-form-comment"><label for="comment">Comment</label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" aria-required="true" required="required" placeholder="%s"></textarea></p>', $text);
    return $default;
}

function apip_add_boring_comment_style( $class ) {
    $comment_id = get_comment_ID();

    $sql = "";
    return $class;
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
        'sizemin' => 1,         // the smallest number applied to the tag class
        'sizemax' => 5          // the largest number applied to the tab class
    ), $params));
    // initialize
    $ret = '<ul class="tagcloud">';
    $min = 9999999; $max = 0;
    // fetch all WordPress tags
    $tags = get_tags(array('orderby' => $orderby, 'order' => $order, 'number' => $number));
    // get minimum and maximum number tag counts
    $index = 0;
    $part = 1;
    $lev = $sizemax ;
    $ori = 0 ;
    foreach ($tags as $tag) {
        $tag->parent = $lev ;
        if ( $index == $part * 13 )
        {
            $lev--;
            $part = $part + $ori;
            $ori = $part ;
            $part++ ;
        }
        $index++ ;
    }

    shuffle($tags) ;
    // generate tag list
    foreach ($tags as $tag) {
        $url = get_tag_link($tag->term_id);
        $title = $tag->count . ' article' . ($tag->count == 1 ? '' : 's');
        $class = $sizeclass . $tag->parent ;
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
                            $link->comment_author_url,
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
    $ret = '<h2 class="apip-h2">日期归档</h2><ul class="achp-widget">';
    $years = $wpdb->get_results("SELECT `year`, `count` FROM `{$wpdb->prefix}v_posts_count_yearly`");
    foreach( $years as $year ){
        $yearLink = get_year_link($year->year);
        $ret .= "<li class=\"achp-parent\">".
                "<a class=\"achp-sig\" title=\"{$year->year}\" href=\"#\">".
                "<span class=\"achp-symbol suffix\">[+]</span>".
                "</a><a href=\"{$yearLink}\" title=\"{$year->year}\">".
                "{$year->year} ({$year->count})".
                "</a><ul class=\"achp-child apip-no-disp\">";
        $months = $wpdb->get_results("SELECT `year`, `month`, `count`, `posts` FROM `{$wpdb->prefix}v_posts_count_monthly` WHERE `year` = '{$year->year}'");
        foreach ($months as $month) {
            $monthLink = get_month_link($month->year, $month->month);
            $monthFormat = $month->month < 10 ? '0'.$month->month : $month->month;
            $ret .= "<li class=\"achp-parent apip-no-disp\" >" .
                    "<a class=\"achp-sig\" href=\"#\" title=\"{$monthFormat}\"><span class=\"achp-symbol suffix\">[+]</span></a>".
                    "<a href=\"{$monthLink}\" title=\"{$monthFormat}\">{$monthFormat}({$month->count})</a>";
            $ret .= "<ul class = \"achp-child apip-no-disp\">";
            $includes = explode(",",$month->posts);
            $getpostsargs = array();
            $getpostsargs['posts_per_page'] = -1;
            $getpostsargs['orderby'] = 'date';
            $getpostsargs['order'] = 'ASC';
            $getpostsargs['include'] = $includes;
            $posts = get_posts($getpostsargs);
            foreach( $posts as $post ) {
                $ret.= "<li class=\"achp-child apip-no-disp\">";
                $ret.= sprintf( "<a href=\"%s\" title=\"%s\">%s</a>",
                    get_permalink($post->ID),
                    htmlspecialchars($post->post_title),
                    $post->post_title
                );
                $ret.='</li>';//achp-child
            }
            $ret .= "</ul>";
            $ret .= "</li>";//achp_months
        }
        $ret .= "</ul></li>";//achp_years
    }//for years
    $ret .= '</ul>';//ul achp-widget
    /* 时间归档 */
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
//8.1codehighlight相关
/**
 * 作用: 在页脚激活JS
 * 来源: 自产
 * URL:
 */
function apip_footer_actions()
{
    global $apip_options ;
    //9.1
    if ( (in_category('code_share') || has_tag('testcode')) && apip_option_check('apip_codehighlight_enable') )
    {
?>
        <script type="text/javascript">
            window.onload = function(){prettyPrint();};
        </script>
<?php
    }
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
    return preg_replace("/<pre(.*?)>(.*?)<\/pre>/ise",
        "'<pre class=\" prettyprint \">'.wch_stripslashes('$2').'</pre>'", $content);
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
function apip_keep_quary(){
    $key = 'apip_aq_'.COOKIEHASH;
    $apip_aq = get_transient($key);
    if ( false === $apip_aq || !$apip_aq->isloaded() ){
        return;
    }
    $apip_aq->keep_query();
    set_transient($key, $apip_aq, 360);
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
        $color_border = isset( $apip_options['border_color'] ) ? $apip_options['border_color'] : "#8a8988";
        $color_link = isset( $apip_options['link_color'] ) ? $apip_options['link_color'] : "#1a5f99";
        $color_font = isset( $apip_options['font_color'] ) ? $apip_options['font_color'] : "#0a161f";
        $color_bg = isset( $apip_options['bg_color'] ) ? $apip_options['bg_color'] : "#ece5df";
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

        $headers .= 'From: 破襪子站长 <webmaster@pewae.com>'. "\r\n";

        //$headers .= 'Bcc: lifishake@gmail.com'. "\r\n";

        wp_mail($comment_parent->comment_author_email,'您在『'.get_option('blogname').'』 的留言有了新回复。',$mailcontent,$headers);
    }
}

//8.5 豆瓣电影
/**
* 作用: 显示来自豆瓣的音乐/电影/图书信息。本函数是主入口。
* 来源: 大发(bigFa)
* URL: http://fatesinger.com/74915
*/
function apip_dou_detail( $atts, $content = null ) {
    extract( shortcode_atts( array( 'id' => '', 'type' => '' ), $atts ) );
    $items =  explode(',', $id);
    foreach ( $items as $item )  {
        if ($type == 'music') {
                $output .= apip_dou_music_detail($item);
        }
        else if ($type == 'book') {
                $output .= apip_dou_book_detail($item);
        }
        else{ //movie
                $output .= apip_dou_movie_detail($item);
        }
    }
    return $output;
}

/**
* 作用: 显示书籍详情的子函数，主要区别是格式和字段。
* 来源: 大发(bigFa)
*/
function apip_dou_book_detail($id) {

    $data = apip_get_dou_content($id,$type = 'book');
    $output = '<div class="apip-item"><div class="mod"><div class="v-overflowHidden doulist-subject"><div class="apiplist-post"><img src="'.  apip_get_saved_images($id,$data['images']['medium'],'douban') .'"></div>';
    $output .= '<div class="title"><a href="'. $data["alt"] .'" class="cute" target="_blank" rel="external nofollow">'. $data["title"] .'</a></div>';
    $output .= '<div class="rating"><span class="allstardark"><span class="allstarlight" style="width:' . $data["rating"]["average"]*10 . '%"></span></span><span class="rating_nums"> ' . $data["rating"]["average"]. ' </span><span>(' . $data["rating"]["numRaters"]. '人评价)</span></div>';
    $output .= '<div class="abstract">作者 : ';
    $authors = $data["author"];
    $output .= implode('/', $authors);

    $output .= '<br>出版社 : ' . $data["publisher"] .'<br>出版年 : ';

    $output .= $data["pubdate"] ;
    $output .= '</div></div></div></div>';
    return $output;
}

/**
* 作用: 显示音乐专辑详情的子函数，主要区别是格式和字段。
* 来源: 大发(bigFa)
*/
function apip_dou_music_detail($id){

    $data = apip_get_dou_content($id,$type = 'music');

    $output = '<div class="apip-item"><div class="mod"><div class="v-overflowHidden doulist-subject"><div class="apiplist-post"><img src="'.  apip_get_saved_images($id,str_replace('spic','mpic',$data['image']),'douban') .'"></div>';
    $output .= '<div class="title"><a href="'. $data["alt"] .'" class="cute" target="_blank" rel="external nofollow">'. $data["title"] .'</a></div>';
    $output .= '<div class="rating"><span class="allstardark"><span class="allstarlight" style="width:' . $data["rating"]["average"]*10 . '%"></span></span><span class="rating_nums"> ' . $data["rating"]["average"]. ' </span><span>(' . $data["rating"]["numRaters"]. '人评价)</span></div>';
    $output .= '<div class="abstract">表演者 : ';
    $authors = $data["author"];
    $authors = wp_list_pluck($authors,'name');
    $output .= implode('/', $authors);
    $output .= '<br>年份 : ' . $data["attrs"]["pubdate"][0] ;
    $output .= '<br>唱片公司 : ' . $data["attrs"]["publisher"][0] ;
    $output .= '</div></div></div></div>';
    return $output;
}

/**
* 作用: 显示电影详情的子函数，主要区别是格式和字段。
* 来源: 大发(bigFa)
*/
function apip_dou_movie_detail($id) {
    $data = apip_get_dou_content($id,$type = 'movie');
    if ( empty($data) ) {
        return '';
    }
    $output = '<div class="apip-item"><div class="mod"><div class="v-overflowHidden doulist-subject"><div class="apiplist-post"><img src="'.  apip_get_saved_images($id,$data['images']['medium'],'douban') .'"></div>';
    $output .= '<div class="title"><a href="'. $data["alt"] .'" class="cute" target="_blank" rel="external nofollow">'. $data["title"] .'</a></div>';
    $output .= '<div class="rating"><span class="allstardark"><span class="allstarlight" style="width:' . $data["rating"]["average"]*10 . '%"></span></span><span class="rating_nums"> ' . $data["rating"]["average"]. ' </span><span>(' . $data["ratings_count"]. '人评价)</span></div>';
    $output .= '<div class="abstract">导演 :';
    $directors = $data["directors"];
    $directors = wp_list_pluck($directors,'name');
    $output .= implode('/', $directors);
    $output .= '<br >演员: ';

    $casts = $data["casts"];
    $casts = wp_list_pluck($casts,'name');
    $output .= implode('/', $casts);

    $output .= '<br >';
    $output .= '类型: ';
    $genres = $data["genres"];
    $output .= implode('/', $genres);

    $output .= '<br >国家/地区: ';
    $countries = $data["countries"];
    $output .= implode('/', $countries);

    $output .= '<br>年份: ' . $data["year"] .'</div></div></div></div>';
    return $output;
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
        return $cache;
    }
    if ( $type == 'movie') {
        $link = "https://api.douban.com/v2/movie/subject/".$id;
    } elseif ( $type == 'book' ) {
	$link = "https://api.douban.com/v2/book/" . $id;
    } else {
        $link = "https://api.douban.com/v2/music/".$id;
    }
    delete_transient($cache_key);
    //从链接取数据
    $ch=@curl_init($link);
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $cexecute=@curl_exec($ch);
    @curl_close($ch);
    if ($cexecute) {
        $cache = json_decode($cexecute,true);
        set_transient($cache_key, $cache, 60*60*24*30);
    } else {
        return false;
    }
	return $cache;
}

/**
* 作用: 用于保存图像缓存的子函数。
* 来源: 大发
*/
function apip_get_saved_images($id, $src, $dst )  {

    if ( 'douban'===$dst ) {
        $thumb_path = APIP_GALLERY_DIR . 'douban_cache/';
    } else {
        $thumb_path = APIP_GALLERY_DIR . 'game_poster/';
    }

    $e = $thumb_path. $id .'.jpg';

    if ( !is_file($e) ) {
        if (!@copy(htmlspecialchars_decode($src), $e))
        {
            $errors= error_get_last();
        }
        $image = new Apip_SimpleImage();
        $image->load($e);
        $image->resize(100, 150);
        $image->save($e);
    }

    if ( 'douban'===$dst ) {
        $url =APIP_GALLERY_URL.'douban_cache/'. $id .'.jpg';
    } else {
        $url =APIP_GALLERY_URL.'game_poster/'. $id .'.jpg';
    }

    return $url;
}

function apip_is_local_mode()
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
* API格式：http://www.theimdbapi.org/api/movie?movie_id=tt4901304
* https://www.omdbapi.com/?i=tt3896198&apikey=36edb41f
*/
function apip_imbd_detail($atts, $content = null){
    extract( shortcode_atts( array( 'id' => '0', 'cname'=>'','alias'=>'' ), $atts ) );
    $cache_key = 'imdb_'.$id;
    $content = get_transient($cache_key);
    global $apip_options;
    //for local debug
    if ( apip_is_local_mode() ){
        $content = array(
            "Title"=>"Guardians of the Galaxy Vol. 2",
            "Year"=>"2017",
            "Rated"=>"PG-13",
            "Released"=>"05 May 2017",
            "Runtime"=>"136 min",
            "Genre"=>"Action, Adventure, Sci-Fi",
            "Director"=>"James Gunn",
            "Writer"=>"James Gunn, Dan Abnett (based on the Marvel comics by), Andy Lanning (based on the Marvel comics by), Steve Englehart (Star-lord created by), Steve Gan (Star-lord created by), Jim Starlin (Gamora and Drax created by), Stan Lee (Groot created by), Larry Lieber (Groot created by), Jack Kirby (Groot created by), Bill Mantlo (Rocket Raccoon created by), Keith Giffen (Rocket Raccoon created by), Steve Gerber (Howard the Duck created by), Val Mayerik (Howard the Duck created by)",
            "Actors"=>"Chris Pratt, Zoe Saldana, Dave Bautista, Vin Diesel",
            "Plot"=>"The Guardians must fight to keep their newfound family together as they unravel the mystery of Peter Quill's true parentage.",
            "Language"=>"English",
            "Country"=>"USA, New Zealand, Canada",
            "Awards"=>"6 wins & 13 nominations.",
            "Poster"=>"https://images-na.ssl-images-amazon.com/images/M/MV5BMTg2MzI1MTg3OF5BMl5BanBnXkFtZTgwNTU3NDA2MTI@._V1_SX300.jpg",
            "Metascore"=>"67",
            "imdbRating"=>"7.8",
            "imdbVotes"=>"301,863",
            "imdbID"=>"tt3896198",
            "Type"=>"movie",
            "DVD"=>"22 Aug 2017",
            "BoxOffice"=>"$389,804,217",
            "Production"=>"Walt Disney Pictures",
            "Website"=>"https=>//marvel.com/guardians",
            "Response"=>"True"
        );
    }
    if ( !$content )
    {
        $apikey = "36edb41f";
        $url = "https://www.omdbapi.com/?movie_id=".$id."&apikey=".$apikey;
        delete_transient($cache_key);
        //从链接取数据
        $response = file_get_contents($url, false);
        if ($response) {
            $content = json_decode($response,true);
            set_transient($cache_key, $content, 60*60*24*30);
        } else {
            return false;
        }
    }
    $img_src = APIP_GALLERY_DIR . 'douban_cache/'.$id.'.jpg';
    $img_url = $content['Poster'];
    if ( !is_file($img_src) && !apip_is_local_mode() ) {
        if (!@copy(htmlspecialchars_decode($img_url), $img_src))
        {
            $errors= error_get_last();
            return false;
        }
        $image = new Apip_SimpleImage();
        $image->load($img_src);
        $image->resize(100, 150);
        $image->save($img_src);
    }
    $imdb_url = "https://www.imdb.com/title/".$id;
    $img_url = APIP_GALLERY_URL.'douban_cache/'. $id .'.jpg';
    $output = '<div class="apip-item"><div class="mod"><div class="v-overflowHidden doulist-subject"><div class="apiplist-post"><img src="'.  $img_url  .'"></div>';
    $output .= '<div class="title"><a href="'. $imdb_url .'" class="cute" target="_blank" rel="external nofollow">'. $cname !== ''?$cname:$content["Title"] .' </a></div>';
    $output .= '<div class="rating"><span class="allstardark"><span class="allstarlight" style="width:' . $content["imdbRating"]*10 . '%"></span></span><span class="rating_nums"> ' . $content["imdbRating"]. ' </span><span>(' . $content["imdbVotes"]. '人评价)</span></div>';
    $output .= '<div class="abstract">';

    if ( $cname !== '' ) {
        $output .='中文名: '.$cname.'<br>';
    }

    $output .= '导演 :'.$content["Director"];

    $output .= '<br >演员: ';

    $casts = $content["Actors"];
    $casts = str_replace(',','/',$casts);
    $output .= $casts;

    $output .= '<br >';
    $output .= '类型: ';
    $genres = $content["Genre"];
    $genres = str_replace(',','/',$genres);
    $output .= $genres;

    $output .= '<br>年份: ' . $content["Year"] .'</div></div></div></div>';
    return $output;
}
/*
function apip_imbd_detail($atts, $content = null){
    extract( shortcode_atts( array( 'id' => '0', 'cname'=>'','alias'=>'' ), $atts ) );
    $cache_key = 'imdb_'.$id;
    $content = get_transient($cache_key);
    if ( !$content )
    {
        $url = "http://www.theimdbapi.org/api/movie?movie_id=".$id;
        delete_transient($cache_key);
        //从链接取数据
        $response = file_get_contents($url, false);
        if ($response) {
            $content = json_decode($response,true);
            set_transient($cache_key, $content, 60*60*24*30);
        } else {
            return false;
        }
    }
    $img_src = APIP_GALLERY_DIR . 'douban_cache/'.$id.'.jpg';
    $img_url = $content['poster']['thumb'];
    if ( !is_file($img_src) ) {
        if (!@copy(htmlspecialchars_decode($img_url), $img_src))
        {
            $errors= error_get_last();
            return false;
        }
        $image = new Apip_SimpleImage();
        $image->load($img_src);
        $image->resize(100, 150);
        $image->save($img_src);
    }
    $img_url = APIP_GALLERY_URL.'douban_cache/'. $id .'.jpg';
    $output = '<div class="apip-item"><div class="mod"><div class="v-overflowHidden doulist-subject"><div class="apiplist-post"><img src="'.  $img_url  .'"></div>';
    $output .= '<div class="title"><a href="'. $content["title"] .'" class="cute" target="_blank" rel="external nofollow">'. $content["title"] .'</a></div>';
    $output .= '<div class="rating"><span class="allstardark"><span class="allstarlight" style="width:' . $content["rating"]*10 . '%"></span></span><span class="rating_nums"> ' . $content["rating"]. ' </span><span>(' . $content["rating_count"]. '人评价)</span></div>';
    $output .= '<div class="abstract">';

    if ( $cname !== '' ) {
        $output .='中文名: '.$cname.'<br>';
    }

    $output .= '导演 :'.$content["director"];

    $output .= '<br >演员: ';

    $casts = $content["cast"];
    $casts = wp_list_pluck($casts,'name');
    $output .= implode('/', $casts);

    $output .= '<br >';
    $output .= '类型: ';
    $genres = $content["genre"];
    $output .= implode('/', $genres);

    $output .= '<br>年份: ' . $content["year"] .'</div></div></div></div>';
    return $output;
}
*/

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
    if( $id == 'x' ) {
        $id = 'nodata_'.get_the_ID();
        $nodata = 1;
    }

    $cache_key = 'game_'.$id;
    $content = get_transient($cache_key);
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
            $context = stream_context_create(['http' => ['user_agent' => 'API Test UA']]);
            $response = file_get_contents($url, false, $context);
            if ($response) {
                $content = json_decode($response,true);
                set_transient($cache_key, $content, 60*60*24*30);
            } else {
                return false;
            }
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
        $context = stream_context_create(['http' => ['user_agent' => 'API Test UA']]);
        $imageString = file_get_contents($img_url, false, $context);
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
    $output = '<div class="apip-item"><div class="mod"><div class="v-overflowHidden doulist-subject"><div class="apiplist-post"><img src="'. $img_url .'"></div>';
    $output .= '<div class="title"><a href="'. $data["site_detail_url"] .'" class="cute" target="_blank" rel="external nofollow">'. ($cname!=''?$cname:$data["name"]) .'</a></div>';
    $output .= '<div class="abstract">';
    if ( $cname !== '' ) {
        $output .='英文名: '.$data["name"].'<br>';
    }
    if ( $jname !== '' ) {
        $output .='日文名: '.$jname.'<br>';
    }
    if ( $alias !== '' ) {
        $output .='别名: '.str_replace(',','/ ',$alias).'<br>';
    }

    if ( $publisher !== '' ) {
        $output .='发行商: '.str_replace(',','/ ',$publisher);
    } else {
        $output .='发行商: ';
        $publishers = $data["publishers"];
        $publishers = wp_list_pluck($publishers,'name');
        $output .= implode('/ ', $publishers);
    }

    $output .='<br>发售日期: ';
    if ( $year !== '' ) {
        $output .= $year;
    } else {
        $output .=substr($data['original_release_date'],0,10);
    }

    $output .=' <br>类型: ';
    if ($genres !=='') {
        $output .= $genres;
    } else{
        $genres = $data['genres'];
        $genres = wp_list_pluck($genres,'name');
        $output .= implode('/ ', $genres);
    }

    $output .=' <br>机种: ';
    if ( $platform !== '' ) {
        $output .= $platform;
    }    else {
        $platforms = $data['platforms'];
        $platforms = wp_list_pluck($platforms,'abbreviation');
        $platform_str = str_replace( array('NES','GEN','SNES'),array('FC','MD','SFC'),$platforms);
        $output .= implode('/ ', $platform_str);
    }

    if ( $download !== '' ){
        $output .='<br><a href="'.$download .'" class="cute" target="_blank" rel="external nofollow">下载</a>';
    }

    $output .= '</div></div></div></div>';
    return $output;
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
    $req=curl_init();
    $addr = 'https://free-api.heweather.com/s6/weather/now?key='.$token.'&location=CN101070211';
    curl_setopt($req, CURLOPT_URL,$addr);
    curl_setopt($req, CURLOPT_TIMEOUT,3);
    curl_setopt($req, CURLOPT_CONNECTTIMEOUT,10);
    $headers=array( "Accept: application/json", "Content-Type: application/json;charset=utf-8" );
    curl_setopt($req, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($req, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($req);
    curl_close($req);
    $got = $data["HeWeather6"][0];
    $weather["time"] = $got["update"]["loc"];
    $weather["result"] = $got["now"];
    add_post_meta($post->ID, $meta_key, $weather, false);
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
