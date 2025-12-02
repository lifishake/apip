<?php

/**
 * Plugin Name: All plugins in pewae
 * Plugin URI:  http://pewae.com
 * GitHub Plugin URI: https://github.com/lifishake/apip
 * Description: Plugins used by pewae
 * Author:      lifishake
 * Author URI:  http://pewae.com
 * Version:     1.40.5
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

    //8.7
    if (!apip_is_table_exists('v_weather_tbd')) {
        $prefix = $wpdb->prefix;
        $sql = "CREATE VIEW `{$prefix}v_weather_tbd` AS
    SELECT `{$prefix}posts`.`ID` AS `ID`,
    `{$prefix}posts`.`post_date` AS `post_date`,
    `{$prefix}posts`.`post_title` AS `post_title`,
    CONCAT(MONTH(`{$prefix}posts`.`post_date`),'-', DAYOFMONTH(`{$prefix}posts`.`post_date`)) AS `tdate` from `{$prefix}posts` WHERE (
        1 AND (`{$prefix}posts`.`post_type` = 'post')
        AND (`{$prefix}posts`.`post_status` = 'publish') 
        AND (NOT(`{$prefix}posts`.`ID` IN (
            SELECT `{$prefix}postmeta`.`post_id` FROM `{$prefix}postmeta` WHERE (
                `{$prefix}postmeta`.`meta_key` = 'Apip_Weather')))))
    ORDER BY 
        MONTH(`{$prefix}posts`.`post_date`),
        DAYOFMONTH(`{$prefix}posts`.`post_date`),
        `{$prefix}posts`.`post_date` ";
            //$query = $wpdb->prepare($sql);
            $wpdb->query($sql);
    }
    if (!apip_is_table_exists('v_weather_nearby')) {
        $prefix = $wpdb->prefix;
        $sql = "CREATE VIEW `{$prefix}v_weather_nearby` AS
        SELECT `{$prefix}v_weather_tbd`.`ID` AS `ID`,
        `{$prefix}v_weather_tbd`.`post_date` AS `post_date`,
        `{$prefix}v_weather_tbd`.`post_title` AS `post_title`,
        `{$prefix}v_weather_tbd`.`tdate` AS `tdate` FROM `{$prefix}v_weather_tbd` 
        WHERE ((`{$prefix}v_weather_tbd`.`tdate` = CONCAT(MONTH(NOW()),'-',DAYOFMONTH(NOW()))) 
        OR (`{$prefix}v_weather_tbd`.`tdate` = CONCAT(MONTH((NOW() - INTERVAL 3 DAY)),'-',DAYOFMONTH((NOW() - INTERVAL 3 DAY)))) 
        OR (`{$prefix}v_weather_tbd`.`tdate` = CONCAT(MONTH((NOW() - INTERVAL 2 DAY)),'-',DAYOFMONTH((NOW() - INTERVAL 2 DAY)))) 
        OR (`{$prefix}v_weather_tbd`.`tdate` = CONCAT(MONTH((NOW() - INTERVAL 1 DAY)),'-',DAYOFMONTH((NOW() - INTERVAL 1 DAY)))) 
        OR (`{$prefix}v_weather_tbd`.`tdate` = CONCAT(MONTH((NOW() + INTERVAL 1 DAY)),'-',DAYOFMONTH((NOW() + INTERVAL 1 DAY)))) 
        OR (`{$prefix}v_weather_tbd`.`tdate` = CONCAT(MONTH((NOW() + INTERVAL 2 DAY)),'-',DAYOFMONTH((NOW() + INTERVAL 2 DAY)))) 
        OR (`{$prefix}v_weather_tbd`.`tdate` = CONCAT(MONTH((NOW() + INTERVAL 3 DAY)),'-',DAYOFMONTH((NOW() + INTERVAL 3 DAY)))))
        ";
        $wpdb->query($sql);
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
    if ( isset( $apip_options[$key] ) && intval($apip_options[$key]) == intval($val) ) {
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
//add_action('current_screen', 'apip_remove_admin_help');
//add_action('in_admin_header', 'apip_remove_admin_help');
/*变量初期化*/
add_action('plugins_loaded', 'apip_init', 99);
function apip_init()
{
    /** 00 */
    global $wpdb;
    
    //20250220
    $default_apip_options = array(
        "link_color" => "#1A5F99",
        "font_color" => "#0A161F",
        "border_color" => "#8A8988",
        "bg_color" => "#ECE5DF",
        "tagcloud_link_color" => "#EA3382",
        "tagcloud_bg_color" => "#9ECECF",
        "auto_save_disabled" => "",
        "show_admin_bar" => "",
        "forground_chinese" => "",
        "block_open_sans" => "",
        "show_author_comment" => "",
        "redirect_if_single" => "",
        "protect_comment_php" => "",
        "search_without_page" => "",
        "redirect_external_link" => "",
        "remove_core_updates" => "",
        "better_excerpt" => "",
        "excerpt_length" => "250",
        "excerpt_ellipsis" => "...",
        "header_description" => "",
        "hd_home_text" => "填写网站描述字符串",
        "hd_home_keyword" => "填写网站关键字,用半角,逗号,分隔",
        "available_gravatar_mirrors" => "",
        "replace_emoji" => "",
        "blocked_commenters" => "SEO,网站优化,网赚,大全,",
        "apip_tagcloud_enable" => "",
        "apip_link_enable" => "",
        "apip_archive_enable" => "",
        "apip_codehighlight_enable" => "",
        "available_codehighlight_tags" => "",
        "apip_lazyload_enable" => "",
        "heweather_key" => "",
        "local_gravatar" => "",
        "local_widget_enable" => "0",
        "local_definition_count" => "5",
        "range_jump_enable" => "",
        "notify_comment_reply" => "",
        "social_share_enable" => "",
        "social_share_twitter" => "",
        "social_share_sina" => "",
        "social_share_facebook" => "",
        "apip_commentquiz_enable" => "",
	);
    $option = get_option('apip_settings');
    if (!$option) {
        update_option('apip_settings', $default_apip_options, true);
    }
	
	//20210106统一整理admin_init
	add_action('admin_init','apip_admin_init');
	
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
    //0.5 后台追加的快捷按钮  -->WP6.0之后添加方法更新，移至admin_enqueue_scripts
    //0.6 去掉后台的OpenSans  -->移至统一的admin_enqueue_scripts
    //0.7 自带的TagCloud格式调整  -->暂时不用
    //0.8 移除后台的“作者”列
    add_filter( 'manage_post_posts_columns', 'apip_posts_columns' );
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
    add_filter('the_content_feed', 'apip_code_highlight', 199, 1) ;
    //0.15 移除后台界面的WP版本升级提示 -->因为会引起downgrade失败,所以改为有配置项的2.11
    //0.16 修改AdminBar
    add_action( 'wp_before_admin_bar_render', 'apip_admin_bar', 199 );
    //0.17 针对苹果旧设备的访问，减少404
    add_filter('site_icon_meta_tags','apip_add_apple_touch_icon');
    //0.18 汉字标题自动转utf8字符
	//原来的sanitize_title范围太大，改为生成post slug和term slug的两个filter20211201
	add_filter('wp_unique_term_slug', 'apip_unique_term_slug', 10, 3);
	add_filter('wp_unique_post_slug', 'apip_unique_post_slug', 10, 6);
    //0.19 autop与shortcode冲突问题
    add_filter( 'the_content', 'apip_fix_shortcodes');
    //0.20 改用户profile不需要邮件确认
    remove_action('personal_options_update', 'send_confirmation_on_profile_email');
    //0.21 设置chrome内核浏览器的tab颜色
    add_action('wp_head', 'apip_set_theme_color', 20);
    //0.22 移除后台的help
    add_action('in_admin_header', 'apip_remove_admin_help');

    //0.24 debug时忽略wordpress.org的update检查.
    if ( apip_is_debug_mode() ) {
        remove_action('admin_init', '_maybe_update_core');
        remove_action('admin_init', '_maybe_update_plugins');
        remove_action('admin_init', '_maybe_update_themes');
        remove_action('init', 'wp_schedule_update_checks');
        add_filter('translations_api', '__return_empty_array');
        // add_filter('install_plugins_table_api_args_favorites', '__return_false');
        // add_filter('install_plugins_table_api_args_featured', '__return_false');
        // add_filter('install_plugins_table_api_args_popular', '__return_false');
        // add_filter('install_plugins_table_api_args_recommended', '__return_false');
        // add_filter('install_plugins_table_api_args_upload', '__return_false');
        // add_filter('install_plugins_table_api_args_search', '__return_false');
        // add_filter('install_plugins_table_api_args_beta', '__return_false');
        // add_filter('install_themes_table_api_args_popular', '__return_false');
        // add_filter('install_themes_table_api_args_dashboard', '__return_false');
        // add_filter('install_themes_table_api_args_featured', '__return_false');
        // add_filter('install_themes_table_api_args_new', '__return_false');
        // add_filter('install_themes_table_api_args_search', '__return_false');
        // add_filter('install_themes_table_api_args_updated', '__return_false');
        // add_filter('install_themes_table_api_args_upload', '__return_false');
    }

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
    
    //2.12使能原生的link manager
    if  ( apip_option_check('enable_link_manager') ) {
        add_filter('pre_option_link_manager_enabled','__return_true');
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
    if (apip_option_check('local_gravatar')) {
        //如果选择保存到本地
        add_action( 'draft_to_publish','apip_delete_local_gravatars',99,1);
        add_action( 'new_to_publish','apip_delete_local_gravatars',99,1);
        add_filter( 'cron_schedules', 'apip_add_schdule' );
        if(!wp_get_schedule( 'apip_delete_local_gravatars' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), '10Days', 'apip_delete_local_gravatars' );
        }
    }
    else {
        wp_clear_scheduled_hook( 'apip_delete_local_gravatars' );
    }
    //4.2 禁止wordpress把emoji的unicode转换成图片
    if ( apip_option_check('replace_emoji') ) {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );	
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        add_filter( 'wp_resource_hints', 'apip_disable_emojis_remove_dns_prefetch', 10, 2 );
        //似乎emoji的转换分两步，第一步是是否把 :P 之类的转换后成unicode，第二步是把emoji的unicode转换成svg。
        //目前认为第一步可以保留，第二步是脱裤子放屁。升一个小版本验证。验证后再升一个版本。
    }

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

    //8.7 发帖天气
    //当作每篇文章都会存草稿.草稿转成公开的时刻为发表时刻
    //add_action( 'draft_to_publish','apip_save_heweather',99,1);
    //add_action( 'draft_to_private','apip_save_heweather',99,1);
    //add_action( 'auto-draft_to_publish','apip_save_heweather',99,1);
    //add_action( 'auto-draft_to_private','apip_save_heweather',99,1);
    //add_action( 'new_to_publish','apip_save_heweather',99,1);
    //add_action( 'new_to_private','apip_save_heweather',99,1);
    
	//8.10 特色图主颜色按钮
	//必须在admin_init以前
	add_action('admin_menu','apip_optimize_boxes');

    //8.12 我的引文
    add_shortcode('mysup', 'apip_sup_detail');
    add_filter( 'the_content', 'apip_make_sup_anchors', 101);

    // Add to the admin_init action hook
    //add_filter('current_screen', 'my_current_screen' );

    //0X 暂时不用了
    //三插件冲突
    //add_action( 'wp_print_scripts', 'apip_filter_filter',2 );

    /** 99 */
    if ( apip_option_check('local_widget_enable') ) {
        require APIP_PLUGIN_DIR.'/apip-widgets.php';
    }

}

function my_current_screen($screen) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return $screen;
	print_r($screen);
	return $screen;
}

register_activation_hook( __FILE__, 'apip_disable_embeds_remove_rewrite_rules' );
register_deactivation_hook( __FILE__, 'apip_disable_embeds_flush_rewrite_rules' );

add_action('init', 'apip_init_actions', 999);
function apip_init_actions()
{   
    //0.A    移除没用的过滤项
    remove_action('wp_head',                            'feed_links_extra',                 3           );
    remove_action('wp_head',                            'rsd_link'                                      );
    remove_action('wp_head',                            'wlwmanifest_link'                              );
    remove_action('wp_head',                            'adjacent_posts_rel_link_wp_head',  10          );
    remove_action('wp_head',                            'wp_generator'                                  );
    remove_action('wp_head',                            'rest_output_link_wp_head'                      );
    remove_action('xmlrpc_rsd_apis',                    'rest_output_rsd'                               );
    remove_action('template_redirect',                  'rest_output_link_header',          11          );
    remove_action('wp_head',                            'wp_oembed_add_discovery_links'                 );
    remove_action('auth_cookie_malformed',              'rest_cookie_collect_status'                    );
    remove_action('auth_cookie_expired',                'rest_cookie_collect_status'                    );
    remove_action('auth_cookie_bad_username',           'rest_cookie_collect_status'                    );
    remove_action('auth_cookie_bad_hash',               'rest_cookie_collect_status'                    );
    remove_action('auth_cookie_valid',                  'rest_cookie_collect_status'                    );
    remove_action('user_request_action_confirmed',      '_wp_privacy_account_request_confirmed'         );
    remove_action('user_request_action_confirmed',      '_wp_privacy_send_request_confirmation_notification', 12);
    remove_action('init',                               'wp_schedule_delete_old_privacy_export_files'   );
    remove_action('wp_privacy_delete_old_export_files', 'wp_privacy_delete_old_export_files'            );
    remove_action('rest_api_init',                      'rest_api_default_filters',         10          );
    remove_action('rest_api_init',                      'register_initial_settings',        10          );
    remove_action('rest_api_init',                      'create_initial_rest_routes',       99          );
    remove_action('init',                               'rest_api_init'                                 );
    remove_action( 'parse_request', 'rest_api_loaded' );
    remove_filter('the_content',                        'capital_P_dangit',                 11          );
    remove_filter('the_title',                          'capital_P_dangit',                 11          );
    remove_filter('wp_title',                           'capital_P_dangit',                 11          );
    remove_filter('comment_text',                       'capital_P_dangit',                 31          );
    remove_filter('rest_authentication_errors',         'rest_cookie_check_errors',         100         );
    remove_filter('wp_privacy_personal_data_exporters', 'wp_register_comment_personal_data_exporter'    );
    remove_filter('wp_privacy_personal_data_exporters', 'wp_register_media_personal_data_exporter'      );
    remove_filter('wp_privacy_personal_data_exporters', 'wp_register_user_personal_data_exporter', 1    );
    remove_filter('wp_privacy_personal_data_erasers',   'wp_register_comment_personal_data_eraser'      );

    add_filter('use_default_gallery_style',         '__return_false'            );//不使用默认gallery
    add_filter('xmlrpc_enabled',                    '__return_false'            );//不使用xmlrpc
    add_filter('feed_links_show_comments_feed',     '__return_false'            );//不输出comments的rss,4.4以上
    add_filter('rest_enabled',                      '__return_false'            );//禁用REST API,4.4以上
    add_filter('rest_jsonp_enabled',                '__return_false'            );//禁用REST API,4.4以上

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
	/*
    if( !session_id() )
    {
        session_start();
    }
	*/
	/*if (session_status()!=PHP_SESSION_ACTIVE) {
		session_start();
	}*/
    if (apip_is_debug_mode()) {
        include (plugin_dir_path( __FILE__ )."apip-local-debug.php");
	}
}

function apip_header_actions()
{
}

function apip_admin_init() {
	/** 08  */
    //8.8 留言验证问题
    if( apip_option_check('apip_commentquiz_enable') ) {
        add_meta_box('apipcommentquiz', '留言验证问题', 'apip_commentquiz_meta_box', 'post', 'side', 'high');
    }
	//增加ajax回调函数
	add_action( 'wp_ajax_apip_accept_color', 'apip_accept_color' );
	add_action( 'wp_ajax_apip_new_thumbnail_color', 'apip_new_thumbnail_color' );
    //8.7
	add_action( 'wp_ajax_apip_weather_manual_update', 'apip_weather_manual_update' );

    //8.11
	//在后台update区域增加不更新gmt的checkbox
    add_action( 'post_submitbox_misc_actions', 'modify_no_gmt_field' );
    add_filter( 'wp_insert_post_data', 'apip_adjust_modified_date_update', 10, 2 );

	/** 09  */
    //9.1 后台taglist增加private和draft计数列
    add_filter( "manage_edit-post_tag_columns", 'apip_edit_post_tag_column_header', 10);
    add_action( "manage_post_tag_custom_column", 'apip_edit_post_tag_content', 10, 3);

    //9.2 post tag的slug转成utf-8
    add_filter( 'post_tag_row_actions', 'apip_convert_post_tag_slug_to_utf', 10, 2 );
	
	//9.3 postlist页增加post_tag的下拉过滤项
	add_action( 'restrict_manage_posts', 'apip_add_post_tag_filter_ddl');

    //9.4 后台增加显示今天要更新天气的贴。
    add_action( 'wp_user_dashboard_setup', 'apip_add_dashboard_widget');
	add_action( 'wp_dashboard_setup', 'apip_add_dashboard_widget');

    //9.5 后台维护option表
    add_action( 'wp_ajax_apip_db_maintain', 'apip_db_maintain' );

    //9.6 图片上传
    add_action('wp_ajax_apip_upload_image', 'apip_upload_image');
    add_action('wp_ajax_nopriv_apip_upload_image','apip_upload_image');

    //0.24 debug时忽略wordpress.org的update检查.
    if ( apip_is_debug_mode() ) {
        remove_action( 'upgrader_process_complete', 'wp_update_plugins');
        remove_action( 'upgrader_process_complete', 'wp_update_themes');
        remove_action( 'load-plugins.php', 'wp_plugin_update_rows', 20 );
        remove_action( 'load-themes.php', 'wp_theme_update_rows', 20 );
        remove_action( 'load-plugins.php', 'wp_update_plugins' );
        remove_action( 'load-themes.php', 'wp_update_themes' );
        wp_unschedule_hook( 'wp_version_check' );
		wp_unschedule_hook( 'wp_update_plugins' );
		wp_unschedule_hook( 'wp_update_themes' );

        remove_action( 'wp_version_check', 'wp_version_check' );
        remove_action( 'load-plugins.php', 'wp_update_plugins' );
        remove_action( 'load-update.php', 'wp_update_plugins' );
        remove_action( 'load-update-core.php', 'wp_update_plugins' );
        remove_action( 'wp_update_plugins', 'wp_update_plugins' );
        remove_action( 'load-themes.php', 'wp_update_themes' );
        remove_action( 'load-update.php', 'wp_update_themes' );
        remove_action( 'load-update-core.php', 'wp_update_themes' );
        remove_action( 'wp_update_themes', 'wp_update_themes' );
        remove_action( 'update_option_WPLANG', 'wp_clean_update_cache', 10, 0 );
        remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
        add_filter('user_has_cap', '_debug_ignore_wp_request', 10, 3);
    }
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
    0.22                                移除后台画面的help
    0.23                                禁止edit_lock
    0.24                                debug时忽略wordpress.org的update检查.
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
    2.12    enable_link_manager         后台打开原生的link manager
03.     header_description              头部描述信息
    3.1     hd_home_text                首页描述文字
    3.2     hd_home_keyword             首页标签
    3.3     excerpt_length              摘要长度
    3.4     excerpt_ellipsis            摘要结尾字符
04.     GFW选项
    4.1     local_gravatar              头像本地缓存
    4.1.1   gravatar_mirror             头像镜像地址
    4.1.2   available_gravatar_mirrors  头像镜像可用地址
    4.2     replace_emoji               禁止wordpress将emoji的unicode转成图片
05.    留言者控制
   5.1  blocked_commenters              替换广告留言用户名和网址
06.     social_share_enable             社会化分享使能
07.     自定义的shortcode
    7.1     apip_tagcloud_enable        更好看的标签云
    7.2     apip_link_page              自定义友情链接
    7.3     apip_achive_page            自定义归档页
08.     比较复杂的设定
    8.1     apip_codehighlight_enable   代码高亮
            available_codehighlight_tags 代码高亮的tag
    8.2     apip_lazyload_enable        LazyLoad
    8.3                                 结果集内跳转
    8.4.    notify_comment_reply        有回复时邮件提示
    8.7     heweather_key               和风天气/发帖时天气信息
    8.8     apip_commentquiz_enable     回复前答题
    8.9     apip_title_hex_meta_box     手动将标题转换成unicode值的按钮
    8.10    apip_colorthief_meta_box    取特色图片主色调相关内容
    8.11    apip_adjust_modified_date_update 在后台update区域增加不更新gmt的checkbox
09.     后台维护相关
    9.1                                 后台taglist增加private和draft计数列
	9.2									post tag的slug转成utf-8
	9.3									后台postlist增加post_tag筛选下拉框
    9.4                                 后台显示今日待追加天气的post
    9.5                                 后台维护option表
    9.6                                 后台上传图片功能
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

    apip_enqueue_custom_style_resources();

    wp_enqueue_style( 'apip-style-all', APIP_PLUGIN_URL . 'css/apip-all.css', array(), APIP_FRONTEND_CSS_VER);
    wp_enqueue_script('apip-js-option', APIP_PLUGIN_URL . 'js/apip-option.js', array(), APIP_FRONTEND_JS_VER, true);
    $css = '';

    wp_enqueue_style( 'apip_weather_style', APIP_PLUGIN_URL . 'css/weather-icons.min.css', array(), APIP_FRONTEND_CSS_VER );
    wp_enqueue_style( 'apip_wind_style', APIP_PLUGIN_URL . 'css/weather-icons-wind.min.css', array(), APIP_FRONTEND_CSS_VER );


    if (is_singular()) {
        wp_enqueue_script('apip-js-singular', APIP_PLUGIN_URL . 'js/apip-singular.js', array(), APIP_FRONTEND_JS_VER, true);
    }

    //0.1 Ctrl+Enter 提交
    //if (is_singular() && comments_open() ) {
        //wp_enqueue_script('apip-comment-form', APIP_PLUGIN_URL . 'js/apip-comment-form.js', array(), "20200417", true);
    //}
    //07
    if  ( is_singular() && apip_option_check('social_share_enable') )
    {
        wp_enqueue_script('apip-js-social', APIP_PLUGIN_URL . 'js/apip-social.js', array(), APIP_FRONTEND_JS_VER, true);
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
    $agm = array();
    if (isset($apip_options['available_codehighlight_tags'])&& trim($apip_options['available_codehighlight_tags'])!=="") {
        $agm = explode(",", $apip_options['available_codehighlight_tags']);
    } else {
        $agm[] = "testcode";
    }
    if ((in_category('code_share') || has_tag($agm)) && apip_option_check('apip_codehighlight_enable') == 1 )
    {
        add_filter('the_content', 'apip_code_highlight') ;
        wp_enqueue_script('apip-js-prettify', APIP_PLUGIN_URL . 'js/apip-prettify.js', array(), "20191101", true);
    }

    //8.2
    if ( apip_option_check('apip_lazyload_enable') ) {
        wp_enqueue_script('apip-js-lazyload', APIP_PLUGIN_URL . 'js/unveil-ui.min.js', array(), '20200413', true);
        wp_localize_script('apip-js-option','apipScriptData', array('lazyload'=>true));
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
    apip_enqueue_custom_style_resources();
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'apip-style-option', APIP_PLUGIN_URL . 'css/apip-option.css', array(), APIP_ADMIN_CSS_VER);
    wp_enqueue_style( 'apip-style-admin', APIP_PLUGIN_URL . 'css/apip-admin.css', array(), APIP_ADMIN_CSS_VER);
    wp_enqueue_script('apip-color-thief', APIP_PLUGIN_URL . 'js/color-thief.js', array(), APIP_ADMIN_JS_VER, true);
    wp_enqueue_script('apip-js-admin', APIP_PLUGIN_URL . 'js/apip-admin.js', array('wp-color-picker' ), APIP_ADMIN_JS_VER, true);
    
    //wp_localize_script('apip-js-admin','yandexkey',$apip_options['yandex_translate_key']);
    //20200416 原0.6功能,移除OpenSans字体
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );

    //0.5 后台追加的快捷按钮
    //20250220增加快捷键的方法在6.0之后发生了变化
    wp_enqueue_script('apip-js-quicktags', APIP_PLUGIN_URL . 'js/apip-quicktags.js', array('jquery', 'quicktags' ), '20250221', true);
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
    if ( !is_object($wp_scripts) || empty($wp_scripts) || empty($wp_scripts->registered) )
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
    foreach ($wp_styles->registered as $index => $libs) {
    //替换google字体
        if(!empty($libs->src)) {
            if (strpos($libs->src, '//fonts.googleapis.com')) {
                if (apip_is_debug_mode()){
                    unset($wp_styles->registered[$index]);
                }
                else{
                    $libs->src = str_replace('//fonts.googleapis.com', '//fonts.loli.net', $libs->src);
                }
            }
        }
    }
    if ( !is_page('gallery') ) {
        wp_dequeue_style( 'jquery-plugins-slider-style' );
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
    $search = array( 'years', 'year', 'months', 'month', 'weeks', 'week', 'days', 'day', 'hours', 'hour', 'minutes', 'minute', 'mins', 'min', 'seconds', 'second', );
    $replace = array( '年', '年', '个月', '个月', '周', '周', '天', '天', '小时', '小时', '分钟', '分钟', '分钟', '分钟', '秒', '秒', );
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
//20211201 原来函数处理的是title,是unicode汉字字符串。现在处理的对象变成了slug，是转换后的%xx形式，所以以前的字符串转换函数不适用。
function apip_unique_term_slug($slug, $term, $original_slug){
	return apip_slug_unicode($slug);
}
function apip_unique_post_slug($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug){
	$public_pts = get_post_types(array( 'public' => true ));
	if (!in_array($post_type, $public_pts)){
		return $slug;
	}
	return apip_slug_unicode($slug);
}

function apip_slug_unicode($strSlug) {
    $c_count = 0;
    $strRet="";
    for ( $i = 0; $i < strlen( $strSlug ); $i++ ) {
        $ch1 = substr( $strSlug, $i, 1 );
        $byte1st = ord( $ch1 );
        if ('%' == $ch1){
            $chs = substr($strSlug, $i, 9);
            $str_tmp = str_replace("%","",$chs);
            $strRet .= $str_tmp."-";
            $i += 8;
        }else{
            $strRet .= $ch1; 
        }
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

//0.22 移除后台各个画面上的help
//来源:分析代码
function apip_remove_admin_help( ) {
    get_current_screen()->remove_help_tabs();
}

//0.23 禁止edit_lock
//来源:https://wordpress.stackexchange.com/questions/120179/how-to-disable-the-post-lock-edit-lock
function apip_remove_post_locked() {
    $current_post_type = get_current_screen()->post_type;   

    // Disable locking for page, post and some custom post type
    $post_types_arr = array(
        'page',
        'post',
        'movie',
        'book',
        'game',
        'album'
    );

    if(in_array($current_post_type, $post_types_arr)) {
        add_filter( 'show_post_locked_dialog', '__return_false' );
        add_filter( 'wp_check_post_lock_window', '__return_false' );
        wp_deregister_script('heartbeat');
    }
}
add_action('load-edit.php', 'apip_remove_post_locked');
add_action('load-post.php', 'apip_remove_post_locked');

//0.24 禁止update相关
//0.24.1 debug时去除update相关权限，因为会有连接服务器超时警告
function _debug_ignore_wp_request ($allcaps, $caps, $args){
    $server_caps = array('install_languages', 'update_themes', 'update_plugins', 'update_core', 'install_themes', 'install_plugins');
    foreach ($caps as $cap) {
        if ( in_array($cap, $server_caps)) {
            $allcaps[$cap] = false;
        }
    }
    return $allcaps;
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
 * URL:  https://pewae.com
 */

function apip_block_open_sans ($styles)
{
    $open_sans = $styles->registered['open-sans'];
    if (is_object($open_sans)) {
        $open_sans->src = null;
    }   
    return $styles;
}
//2.6
 /**
 * 作用: 在comment widget中屏蔽作者.
 * 原理: 访客的user_id = 0
 * 来源: lifishake原创
 * URL:  https://pewae.com
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
    if ($query->is_search && !$query->is_admin) {
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
   if (empty($text)) {
    $text = get_the_content();
   }

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
   $text = wpautop($text, true);//wpautop在前面，此时已经被过滤掉了,不加<p>不好看。
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

    if( !apip_option_check('local_gravatar') && !apip_is_debug_mode() )
    {
        global $apip_options;
        $str_mirror = isset( $apip_options['gravatar_mirror'] ) ? $apip_options['gravatar_mirror'] : '//gravatar.loli.net/avatar';
        $source = preg_replace('/\/\/\w+\.gravatar\.com\/avatar/', $str_mirror, $source);
        return $source ;
    }
    $pos_sch = strpos( $source, 'http' );
    $src = substr( $source, $pos_sch, strpos( $source, '\'', $pos_sch ) - $pos_sch );
    $tmp = array();
    preg_match('/avatar\/([a-z0-9]+)\?s=(\d+)/',$source, $tmp);  
    $abs = APIP_GALLERY_DIR . 'gravatar_cache/'.$tmp[1];
    $dest = APIP_GALLERY_URL.'gravatar_cache/'.$tmp[1];
    $default =  APIP_GALLERY_URL.'gravatar_cache/default.png';
    if (apip_is_debug_mode()) {
        return '<img alt="" src="'.$default.'" class="avatar avatar-'.$tmp[2].'" width="'.$tmp[2].'" height="'.$tmp[2].'" />';
    }

    if (!is_file($abs)){
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
    }
    return '<img alt="" src="'.$dest.'" class="avatar avatar-'.$tmp[2].'" width="'.$tmp[2].'" height="'.$tmp[2].'" />';
}

//4.1 保存到本地时建立计划任务时间
function apip_add_schdule( $schedules ) {
    $schedules['10Days'] = array(
        'interval' => 864000,   //3600*24*10
        'display' => '10 days'
    );
    $schedules['5min'] = array(
        'interval' => 120,  
        'display' => '5mins'
    );
    return $schedules;
}

//4.1 清理超期的gravatar头像
//参照:php遍历路径下的文件 https://blog.csdn.net/u012732259/article/details/41645569
function apip_delete_local_gravatars($post) {
    //其实跟post没什么关系
    $catch_dir =APIP_GALLERY_DIR . 'gravatar_cache/';
    $dirarr1 = glob($catch_dir.'*');
    $dirarr2 = glob($catch_dir.'*.jpg');
    $files = array_merge($dirarr1, $dirarr2);
    foreach($files as $f){
        if (!is_file($f)) {
            unlink($f);
        } else {
            if (time()-filemtime($f) > 3600 * 24 * 91) {
                //删除91天以上的缓存头像
                unlink($f);
            }
        }
    }
}

//4.2
/**
 * 作用: 替换emoji服务器地址,同时会修改'dns-prefetch'
 * 来源: Ryan Hellyer 
 * URL： https://geek.hellyer.kiwi/plugins/disable-emojis/
 * 说明: 这个方法比之前的替换emoji_url的方法介入的早，所以使用这个方法。未来可以扩展到其他需要替换的被强的网址。
 */

function apip_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {

	if ( 'dns-prefetch' == $relation_type ) {

		$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
		foreach ( $urls as $key => $url ) {
			if ( strpos( $url, $emoji_svg_url_bit ) !== false ) {
				unset( $urls[$key] );
			}
		}

	}

	return $urls;
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
    $str_replacement = "癞皮狗" ;
    $show_random = 'false';
    $forbiddens = explode(',',$str_include);
    $f = 0 ;
    foreach ( $forbiddens as $forbidden ) {
        if ( $forbidden && false != strstr($str_author,$forbidden) ) {
            $f = 1;
            break;
        }
        $short_url = str_replace(array('http://', 'https://', 'www.'), '', $str_author_url);
        if ( $forbidden && $short_url && false != strstr($short_url, $forbidden) ) {
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
            $rand_posts = get_posts(array('numberposts'=>1,'orderby'=>'rand'));
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
    $text = '请保持有趣。因为您的意见对我完全没有任何意义。';
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
    $html = sprintf("<li class = \"achp-parent %s \"><a class=\"achp-sig\" href=\"#\" title=\"%s\"><span class=\"achp-symbol suffix \">[+]</span></a><a href=\"%s\" title=\"%s\">%s<span class=\"achp_count\">(%s)</span></a>%s%s</li>",
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
        $getpostsargs['order'] = 'DESC';
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
 * 来源: prettyprint
 * URL: https://github.com/mre/prettyprint
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
    if (!preg_match('/<\s*img\s/i', $content)) {
        return $content;
    }
    //$content = esc_html($content);
    if (preg_match('/&[a-zA-Z0-9#]+;/', $content)) {
        $content = wp_specialchars_decode($content, ENT_QUOTES);
    }
    $dom = new DOMDocument('1.0', 'UTF-8');
    @$dom->loadHTML('<?xml encoding="UTF-8"><!DOCTYPE html><head><meta charset="UTF-8"></head><body>'. $content.'</body></html>', LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
    //@$dom->createElement();
    foreach ($dom->getElementsByTagName('img') as $node) {
        $oldsrc = $node->getAttribute('src');
        $node->setAttribute("data-src", $oldsrc );
        $node->setAttribute("data-unveil", "true" );
        $newsrc = APIP_PLUGIN_URL.'img/blank.gif';//'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        $node->setAttribute("src", $newsrc);
    }
    $body = $dom->getElementsByTagName('body')->item(0);
    if ($body) {
        $newHtml = '';
        foreach($body->childNodes as $node) {
            $newHtml .= $dom->saveHTML($node);
        }
    }
    else {
        $newHtml = $content;
    }
    /*
    $newHtml = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom->saveHTML()));
    $newHtml = esc_html( $newHtml );
    */
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

    if (isset($_COOKIE['last_tax'])) {
        $old_tax = $_COOKIE['last_tax'];
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
            setcookie('last_tax', '', 0);
            setcookie('tax_ids', '', 0);
            return;
        }
        if ($new_tax != $old_tax) {
            $vars = $wp_query->query_vars;
            $vars['posts_per_page'] = 9999;
            $vars['order'] = "ASC";
            $myquery = new WP_Query( $vars );
            if ($myquery->post_count == 1 && $myquery->max_num_pages == 1){
                wp_reset_postdata();
                setcookie('last_tax', '', 0);
                setcookie('tax_ids', '', 0);
                return;
            }
            setcookie('last_tax', $new_tax, 0);
            setcookie('tax_ids', implode(',', wp_list_pluck( $myquery->posts, 'ID' )), 0);
            wp_reset_postdata();
        }
    }
    else if (!is_single()) {
        setcookie('last_tax', '', 0);
        setcookie('tax_ids', '', 0);
    }
    else {
        //single
        $ID = get_the_ID();
        if ( empty($old_tax) || !isset($_COOKIE['tax_ids']) || empty($_COOKIE['tax_ids'])) {
            return;
        }
        $arr_taxes = explode(',', $_COOKIE['tax_ids']);
        if (FALSE===array_search($ID, $arr_taxes)) {
            setcookie('last_tax', '', 0);
            setcookie('tax_ids', '', 0);
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
        $comment_parent = get_comment($comment_object->comment_parent);
        if ($comment_parent->comment_author_email === $comment_object->comment_author_email &&
            $comment_parent->comment_author_email !== get_bloginfo('admin_email'))
        {
            return;
        }
        /*$color_border = isset( $apip_options['border_color'] ) ? $apip_options['border_color'] : "#8a8988";
        $color_link = isset( $apip_options['link_color'] ) ? $apip_options['link_color'] : "#1a5f99";
        $color_font = isset( $apip_options['font_color'] ) ? $apip_options['font_color'] : "#0a161f";
        $color_bg = isset( $apip_options['bg_color'] ) ? $apip_options['bg_color'] : "#ece5df";*/
        $color_border = "#EDEFED";
        $color_link = "#660000";
        $color_font = "#000200";
        $color_bg = "#F7FCF8";
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
    if (isset( $_SERVER['SystemRoot'] ) && strpos($_SERVER['SystemRoot'], "WINDOWS" ) > 0) {
        return 1;
    }
    if (defined( 'WP_DEBUG' ) && WP_DEBUG) {
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
    $meta_key = 'Apip_Weather';
    global $apip_options;
    if($post->post_type != 'post') {
        return;
    }
    $token = $apip_options['heweather_key'];
    if (!$token) {
        return;
    }
    if ( false != get_post_meta($post->ID, $meta_key, false) )
    {
        return;
    }
    $weather = array();
    //$addr = "https://free-api.heweather.com/s6/weather/now?key=".$token."&location=CN101070209";
    $addr = "https://devapi.qweather.com/v7/weather/now?key=".$token."&location=CN101070209";
    $args = array(
        'sslverify' => false,
        'timeout' => 20,
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

    $got = $cache["now"];
    if ( !array_key_exists('windSpeed', $got) ||
         !array_key_exists('wind360', $got) ||
         !array_key_exists('windDir', $got) ||
         !array_key_exists('windScale', $got) ||
         !array_key_exists('icon', $got) ||
         !array_key_exists('text', $got) ||
         !array_key_exists('temp', $got) ||
         !array_key_exists('obsTime', $got) ) {
        return;
    }
    $tmpTime = str_replace(array("T","+08:00"), array(" ",""), $got["obsTime"]);
    $weather["Time"] = $tmpTime;
    $weather["Tmp"] = $got['temp'];
    $weather["Txt"] = $got["text"];
    $weather["Ico"] = $got["icon"];
    $weather["WndScl"] = $got["windScale"];
    $weather["WndDir"] = $got["windDir"];
    $weather["WndDeg"] = $got["wind360"];
    $weather["WndSpd"] = $got["windSpeed"];
    
    add_post_meta($post->ID, $meta_key, $weather, false);
}

//8.11
//在后台update区域增加不更新gmt的checkbox
/**
* 作用: 在后台显示一个不更新修改时间的checkbox。
* 来源: 自作
*/
function modify_no_gmt_field()
{
    global $post;

    if (get_post_type($post) != 'post') return false;

    ?>
        <div class="misc-pub-section">
            <label><input type="checkbox"<?php echo ' unchecked '; ?> value="1" name="keep_modified_gmt" />不更新修改时间</label>
        </div>
    <?php
}
/**
* 作用: 不更新修改时间的钩子函数
* 来源: 自作
* 资料：https://wordpress.stackexchange.com/questions/237878/how-to-prevent-wordpress-from-updating-the-modified-time
*/
function apip_adjust_modified_date_update( $new, $old ) {
    if(isset($_POST['keep_modified_gmt'])){
        $new['post_modified'] = $old['post_modified'];
        $new['post_modified_gmt'] = $old['post_modified_gmt'];
    }
    return $new;
}

function apip_weather_meta_box( $post ){
    if (get_post_type($post) != 'post') return false;

    $value = get_post_meta($post->ID, 'Apip_Weather', true);
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
    delete_post_meta($post_id, 'Apip_Weather');
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
	$post_types = get_post_types(array( 'public' => true ));
    remove_meta_box('authordiv', $post_types, 'normal');//移除[author]，顺道。
    remove_meta_box('trackbacksdiv', $post_types, 'normal');//移除[trackback]，顺道。
    remove_meta_box('postexcerpt', $post_types, 'normal');//移除[excerpt]，顺道。
    remove_meta_box('postcustom', $post_types, 'normal');//移除[custom fields]，顺道。
    remove_meta_box('slugdiv', $post_types, 'normal');//移除原生的[slug]，再扩展一个新的，因为原生的没提供钩子。在edit框后面增加一个按钮。
    //8.7
    add_meta_box('apipweatherdiv', 'Weather', 'apip_weather_meta_box', 'post', 'normal', 'core');
    //8.9
    add_meta_box('apipslugdiv', 'Slug to unicode', 'apip_title_hex_meta_box', $post_types, 'normal', 'core');
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

//9.3 postlist页面增加根据post_tag筛选的下拉框
function apip_add_post_tag_filter_ddl($post_type) {
	if('post'!==$post_type) { //只在post列表显示。如果想在page列表里同样支持，可以更改判断条件。
		return;
	}
	$key='tag';//要针对post_tag进行过滤。
	$selection = '';//特别重要，记录当前选中项，设错了没法过滤。
	if (isset($_GET[$key]) && !empty($_GET[$key])) {
		$selection = $_GET[$key];
	}

	$dropdown_arg = array(
		'show_option_none' => 'No Tag',
		'option_none_value' => '',
		'orderby' => 'count',
		'order' => 'DESC',
		'name' => $key,	//post_tag特殊，内部检索时一定要用“tag”而不是post_tag
		'value_field' => 'slug',
		'taxonomy' => 'post_tag',
		'selected' => $selection,
	);
	wp_dropdown_categories($dropdown_arg);
}

//9.4 后台显示还有多少的天气需要更新
function apip_add_dashboard_widget() {
    wp_add_dashboard_widget( 'dashboard_apip_today_weather', 'APIP Weather today', 'apip_today_weather_widget');
}

//9.5 维护页面的ajax处理
function apip_db_maintain() {
    if(!isset($_POST['id']))
    {
        return;
    }
    $key = $_POST['id'];
    $nonce = "maintain-do-".$key;
    if (!wp_verify_nonce($_POST['nonce'], $nonce)){
        return;
    }
    delete_option ($key);
}

//9.6 上传图片的ajax处理
function apip_upload_image() {
    if (!wp_verify_nonce($_POST['nonce'], 'gallery-upload')){
        wp_send_json_error('invalid nonce.');
        die();
    }
    $dest_path = $_POST['dest_path'];
    foreach ($_FILES as $file) {
        $f_name = $file['name'];
        $f_disk_name = $file['tmp_name'];
        $dest = APIP_GALLERY_DIR.$dest_path."/".$f_name;
        move_uploaded_file($f_disk_name, $dest);
        if ('wptm_utils' === $dest_path) {
            $upload_dir = wp_upload_dir();
            $basedir = $upload_dir['basedir'];
            $thumbs_dir = implode(DIRECTORY_SEPARATOR, array($basedir, 'ngg_featured'));
            $target_path = path_join($thumbs_dir, $f_name);
            if (@file_exists($target_path)) {
                wp_send_json_error('got same name.');
                continue;
            }
            $target_dir = dirname($target_path);
            if (!@is_dir($target_dir)) {
                wp_mkdir_p($target_dir);
            }
            @copy($dest, $target_path);
            $filetype = wp_check_filetype( basename( $dest ), null );
            $guid = implode(DIRECTORY_SEPARATOR, array($upload_dir['base_url'], 'ngg_featured', $f_name));
            $attachment = array(
                'guid'           => $guid,
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $f_name ) ),
                'post_content'   => 'apip upload '.basename( $f_name ),
                'post_status'    => 'attachment',
                'post_parent'    => 0,
            );
            
            $attach_id = wp_insert_attachment( $attachment, $target_path, 0);
            $attach_data = wp_generate_attachment_metadata( $attach_id, $target_path);
            wp_update_attachment_metadata( $attach_id, $attach_data );
        }
    }
    wp_send_json_success();
    die();
}

 /**
 * 作用: 后台显示还有多少的天气需要更新--具体函数
 * 来源: 自创
 * 备注: 表是临时表
 */
/*
SELECT `posts`.`ID` AS `ID`,`posts`.`post_date` AS `post_date`,`posts`.`post_title` AS `post_title`,CONCAT(MONTH(`posts`.`post_date`),'-',DAYOFMONTH(`posts`.`post_date`)) AS `tdate`
FROM `posts`
WHERE (1 AND (`posts`.`post_type` = 'post') 
AND (`posts`.`post_status` = 'publish')
AND (NOT(`posts`.`ID` IN (
    SELECT `postmeta`.`post_id`
    FROM `postmeta`
    WHERE (`postmeta`.`meta_key` = 'apip_heweather')))))
ORDER BY MONTH(`posts`.`post_date`), DAYOFMONTH(`posts`.`post_date`),`posts`.`post_date`
*/
function apip_today_weather_widget() {
    echo '<div id="apip-today-weather-widget">';
    global $wpdb;
    $sql = "SELECT ID FROM ".$wpdb->prefix."v_weather_tbd ";
    $tmp = $wpdb->get_results($sql, ARRAY_N);
    $totals = count($tmp);
    $script_tz = date_default_timezone_get();
    date_default_timezone_set('Asia/Shanghai');
    $script_tz = date_default_timezone_get();
    $thedays = array();
	$today = date('n-j');
	$thedays[] = $today;
    $thedays[] = date('n-j',strtotime('-3 day'));
    $thedays[] = date('n-j',strtotime('-2 day'));
    $thedays[] = date('n-j',strtotime('-1 day'));
    $thedays[] = date('n-j',strtotime('1 day'));
    $thedays[] = date('n-j',strtotime('2 day'));
    $thedays[] = date('n-j',strtotime('3 day'));
	$count = 0;
    echo '<div id="lost-weathers" class="activity-block">';
    echo '<h3>These need to be update.</h3><ul>';
	foreach ($thedays as $theday)
    {
		$sql = $wpdb->prepare("SELECT ID FROM `{$wpdb->prefix}v_weather_tbd` WHERE `tdate` = %s COLLATE utf8mb4_unicode_ci ORDER BY ID ASC", $theday);
		$ids = $wpdb->get_results($sql);
    if ( $ids) {
        foreach ( $ids as $o_id ) {
            $id =$o_id->ID;
            $draft_or_post_title = _draft_or_post_title($id);
			$m_time_t = get_post_time('Y-m-d', false, $id, false);
			printf(
				'<li><span>%1$s</span> <a href="%2$s" aria-label="%3$s">%4$s</a></li>',
					$m_time_t,
				get_edit_post_link($id),
				esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $draft_or_post_title ) ),
				$draft_or_post_title
			);
				$count++;
        }
    }
		if ($count>0 && $theday == $today)
		{
			break;
		}
	}
    if ($count === 0) {
        echo '<li>Today is clearly.</li>';
    }
    printf('<li><b>%d</b> need to be updated.</li>', $totals);	
	echo '</ul></div></div>';
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
