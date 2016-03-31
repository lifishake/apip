<?php

/**
 * Plugin Name: All plugins in pewae
 * Plugin URI:  http://pewae.com
 * Description: Plugins used by pewae
 * Author:      lifishake
 * Author URI:  http://pewae.com
 * Version:     1.05
 * License:     GNU General Public License 3.0+ http://www.gnu.org/licenses/gpl.html
 */

/*宏定义*/ 
define('APIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('APIP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ) ;
global $apip_options;
/*配置画面*/
if (is_admin())
{
    require_once( APIP_PLUGIN_DIR . '/apip-options.php');
}

/*变量初期化*/
add_action('plugins_loaded', 'apip_init');
function apip_init()
{
    global $apip_options;
    $apip_options = get_option('apip_settings');
	/** 00 */
	//0.1 插件自带脚本控制
    add_action( 'wp_enqueue_scripts', 'apip_scripts' );
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
	/** 01 */
	if( $apip_options['better_excerpt'] == 1 )
    {
        //更好的中文摘要
        add_filter('the_excerpt', 'apip_excerpt', 100);
    }
	
	/** 02 */
    if( $apip_options['save_revisions_disable'] == 1 )
    {
        //2.1停止自动版本更新
        apip_auto_rev_settings();
    }
    if( $apip_options['auto_save_disabled'] == 1 )
    {
		//2.2停止自动保存
        add_action( 'wp_print_scripts', 'apip_auto_save_setting' );
    }
	//2.3是否显示adminbar
    add_filter( 'show_admin_bar', 'apip_admin_bar_setting' );
    if ( $apip_options['forground_chinese']== 1 ) {
		//2.4后台英文前台中文
        add_filter( 'locale', 'apip_locale', 99 );
    }
	if ( $apip_options['block_open_sans'] == 1 )
    {
        //2.5屏蔽已经注册的open sans
        add_action( 'wp_default_styles', 'apip_block_open_sans', 100); 
    }
	if ( $apip_options['show_author_comment'] == 1 )
    {
        //2.6默认留言widget里屏蔽作者
        add_filter( 'widget_comments_args', 'before_get_comments' );
    }
	if ( $apip_options['redirect_if_single'] == 1 )
    {
        //2.7搜索结果只有一条时直接跳入
        add_action('template_redirect', 'redirect_single_post');
    }
    if ( $apip_options['protect_comment_php'] == 1 )
    {
        //2.8禁止直接访问wp_comments.php
        add_action('check_comment_flood', 'check_referrer_comment');
    }
	/** 03 */
    if ( $apip_options['header_description'] == 1 )
    {
        //网站描述和关键字
        add_action( 'wp_head', 'apip_desc_tag' ); 
    }
    
    /** 04 */
    if ( $apip_options['notify_comment_reply']== 1 )
    {       
		//邮件回复
        add_action('wp_insert_comment','apip_comment_inserted',99,2);
    }
	
	/** 05 */
    add_filter('get_avatar','apip_get_cavatar');
	add_filter( 'emoji_url', 'apip_rep_emoji_url', 99, 1);
    /** 06 */
    //抢在akimest前面
    add_filter('preprocess_comment', 'hm_check_user',1);
	/** 07*/
	//social没有添加项,需要外部手动调用
	/** 08 */
	//8.1 TAGcloud 注册
	if ( $apip_options['apip_tagcloud_enable']== 1 )
	{
		add_shortcode('mytagcloud', 'apip_tagcloud_page'); 
	}
    if ( $apip_options['apip_link_enable']== 1 )
	{
		add_shortcode('mylink', 'apip_link_page'); 
	}

	/** 09 */
	//头部动作，一般用于附加css的加载
    //add_action('get_header','apip_header_actions') ;
	//9.1 prettyprint脚本激活
    add_action('get_footer','apip_footer_actions') ;
	
	//9.2 lazyload
	if ( $apip_options['apip_lazyload_enable']== 1 )
	{
		add_filter( 'the_content', 'apip_lazyload_filter',200 );
		add_filter( 'post_thumbnail_html', 'apip_lazyload_filter',200 );
	}
	
    //9.3 结果集内跳转
    if ( $apip_options['range_jump_enable']== 1 )
    {
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
        set_transient( $key, $apip_aq, 360);//保留6分钟
        add_action('template_redirect', 'apip_keep_quary', 9 );//优先级比直接跳转到文章的略高。
    }
    
    //0A    移除没用的过滤项
    remove_action('wp_head','feed_links_extra',3);
    remove_action('wp_head','rsd_link' );
    remove_action('wp_head','wlwmanifest_link' );
    remove_action('wp_head','adjacent_posts_rel_link_wp_head',10,0);
    remove_action('wp_head','wp_generator');
    remove_filter('the_content','capital_P_dangit',11);
    remove_filter('the_title','capital_P_dangit',11);
    remove_filter('wp_title','capital_P_dangit',11);
    remove_filter('comment_text','capital_P_dangit',31);    
    add_filter( 'use_default_gallery_style', '__return_false' );
    add_filter('xmlrpc_enabled', '__return_false');
	
	//0X 暂时不用了
	//三插件冲突
    //add_action( 'wp_print_scripts', 'apip_filter_filter',2 );
	//确认提交前的提示,未添加配置项
    //add_filter('comment_form_defaults' , 'apip_replace_tag_note', 30);
	
	/** 99 */
	if ( $apip_options['local_widget_enable'] == 1 ) {
		require APIP_PLUGIN_DIR.'/apip-widgets.php';
	}
	//包含自定义的函数
	require ( APIP_PLUGIN_DIR.'/apip-func.php') ;
}

function apip_header_actions()
{
	global $apip_options ;
	//8.1
	/*if ( is_page('my-tag-cloud') && $apip_options['apip_tagcloud_enable']== 1 )
	{
		wp_enqueue_style( 'apip_tagcloud_style', APIP_PLUGIN_URL . 'css/apip-tagcloud.css' );
	}*/
	//9.1
    /*if ( in_category('code_share') && $apip_options['apip_codehighlight_enable'] == 1 )
    {
        add_filter('the_content', 'apip_code_highlight') ;
        wp_enqueue_style( 'prettify_style', APIP_PLUGIN_URL . 'css/apip-prettify.css' );
    }*/
}

/*
$options
00.                            无选项，必须选中的内容
0.1                            Ctrl+Enter提交
0.2                            屏蔽不必要的js
0.3                            屏蔽不必要的style
0.4                            feed结尾的追加内容
0.5                            追加的快捷按钮
0.6                            屏蔽后台的OpenSans
0.7                            调整默认的TagCloud Widget
0.8                            移除后台的作者列
01.    better_excerpt          更好的中文摘要
1.1    excerpt_length          摘要长度
1.2    excerpt_ellipsis        摘要结尾字符
02.    高级编辑选项
2.1    save_revisions_disable  阻止自动版本
2.2    auto_save_disabled      阻止自动保存
2.3    show_admin_bar          显示登录用户的admin bar
2.4    apip_locale             后台英文前台中文
2.5    block_open_sans         屏蔽后台的open sans字体
2.6    show_author_comment     屏蔽作者留言
2.7    redirect_if_single      搜索结果只有一条时直接跳入
03.    header_description      头部描述信息
3.1    hd_home_text            首页描述文字
3.2    hd_home_keyword         首页标签
04.    notify_comment_reply    有回复时邮件提示
05.    GFW选项
5.1    local_gravatar          头像本地缓存
5.2    replace_emoji           替换emoji地址
06.    blocked_commenters      替换广告留言用户名和网址
07.    social_share_enable     社会化分享使能
08.    自定义的shortcode
8.1    apip_tagcloud_enable    更好看的标签云
8.2    apip_link_page          自定义友情链接
09.    比较复杂的设定
9.1    apip_codehighlight_enable  代码高亮
9.2    apip_lazyload_enable    LazyLoad
99.    local_widget_enable     自定义小工具
99.1   local_definition_count  自定义widget条目数
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
    global $apip_options ;
    wp_enqueue_style( 'apip_style_all', APIP_PLUGIN_URL . 'css/apip-all.css' );
	//0.1 Ctrl+Enter 提交
	if (comments_open() && is_singular() ) {
		wp_enqueue_script('apip_js_singular', APIP_PLUGIN_URL . 'js/apip-singular.js', array(), false, true);
	}
	//07
    if  ( $apip_options['social_share_enable'] == 1  && is_singular() )
    {
		wp_enqueue_script('apip_js_social', APIP_PLUGIN_URL . 'js/apip-social.js', array(), false, true);
		wp_enqueue_style( 'apip_style_social', APIP_PLUGIN_URL . 'css/apip-social.css' );
    }
	//8.1
	if ( is_page('my-tag-cloud') && $apip_options['apip_tagcloud_enable']== 1 )
	{
		wp_enqueue_style( 'apip_tagcloud_style', APIP_PLUGIN_URL . 'css/apip-tagcloud.css' );
	}
    //8.2
    if ( is_page('my_links') && $apip_options['apip_link_enable']== 1 )
	{
		wp_enqueue_style( 'apip_link_style', APIP_PLUGIN_URL . 'css/apip-links.css' );
	}
	//9.1
	if ( in_category('code_share') && $apip_options['apip_codehighlight_enable'] == 1 )
	{
		add_filter('the_content', 'apip_code_highlight') ;
        wp_enqueue_style( 'prettify_style', APIP_PLUGIN_URL . 'css/apip-prettify.css' );
		wp_enqueue_script('apip_js_prettify', APIP_PLUGIN_URL . 'js/apip-prettify.js', array(), false, true);
	}
	//9.2
	if ( $apip_options['apip_lazyload_enable']== 1 )
	{
		wp_enqueue_style( 'apip_style_lazyload', APIP_PLUGIN_URL . 'css/apip-lazyload.css' );
		wp_enqueue_script('apip_js_lazyload', APIP_PLUGIN_URL . 'js/unveil-ui.min.js', array(), false, true);
	}
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
    foreach ($wp_scripts->registered as $libs){
        $libs->src = str_replace('//ajax.googleapis.com', '//ajax.useso.com', $libs->src);
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
        $libs->src = str_replace('//fonts.googleapis.com', '//fonts.useso.com', $libs->src);
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
    if ( is_singular() )
    {
        if ( !in_category('appreciations') )
        {
			//豆瓣插件的style,仅限评论类别
            wp_dequeue_style( 'wpd-css' );
        }
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
    $addi = sprintf( '<div style="max-width: 520px; margin:0 auto; padding:5px 30px;margin: 15px; border-top: 1px solid #CCC;"><span style="margin-left: 2px; display:block;">《%1$s》采用<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/cn/deed.zh">署名-非商业性使用-禁止演绎</a>许可协议进行许可。 『%2$s』期待与您交流。</span><div style="display:table;">%3$s %4$s</div></div>', 
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
        QTags.addButton( 'eg_pre', 'pre', '<pre>\n', '\n</pre>\n', 'p' );
        QTags.addButton( 'eg_163music', '网易云音乐', '<iframe src="http://music.163.com/outchain/player?type=2&amp;id=', '&amp;auto=0&amp;height=66" width="330" height="86" frameborder="no" marginwidth="0" marginheight="0"></iframe>' );
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

/*                                          00终了                             */

/******************************************************************************/
/*        01.解决中文摘要问题                                                     */
/******************************************************************************/
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
    global $apip_options;
    if( $apip_options['show_admin_bar'] == 1 )
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
/*                                          02终了                             */

/******************************************************************************/
/*        03.优化html头中的descriptor和tag信息                                    */
/******************************************************************************/
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
        $description = substr(strip_tags($post->post_content),0,240)."...";
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
/*                                          03终了                             */

/******************************************************************************/
/*        04.comment的邮件回复                                                   */
/******************************************************************************/
/**
 * 作用: comment有reply时,通过邮件通知留言发布者.
 * 来源: Comment Email Reply
 * URL:  http://kilozwo.de/wordpress-comment-email-reply-plugin
 */
function apip_comment_inserted($comment_id, $comment_object) {
    if ($comment_object->comment_parent > 0) {

        $comment_parent = get_comment($comment_object->comment_parent);
        $bg_head = '<div style="background:transparent; color: inherit; font-size:12px;"><div style="max-width: 520px; margin:0 auto;background:rgba(220,220,220,0.6); padding:25px 70px;margin: 15px;color: #333; border-top: 3px solid #85FF3F; border-bottom: 3px solid #85FF3F; box-shadow: 0px 15px 25px -17px #E7797A;">' ;
        $content_border_head = '<p style="border: 3px double #E7797A;padding: 20px; margin: 15px 0; width: 60% ; background:rgba(220,220,220,0.6); ">' ;
        $random_posts = apip_random_post( get_the_ID(), 1 ) ;
        foreach ( $random_posts as $random_post ) :
            $random_link = get_permalink( $random_post->ID ) ;
        endforeach;
        
        $mailcontent = '<p>亲爱的 <b style="color:#222; font-weight:800; padding:0 5px ;">'.$comment_parent->comment_author.'</b>， 您的留言：</p>' ;
        $mailcontent = $mailcontent.$content_border_head.$comment_parent->comment_content.'</p><p>有了新回复：</p>';
        $mailcontent = $mailcontent.$content_border_head.$comment_object->comment_content.'</p>';
        $mailcontent = $mailcontent.sprintf( '<p>欢迎<a href="%1$s">继续参与讨论</a>或者<a href="%2$s">随便逛逛</a>。<a href="%3$s">「破襪子」</a>期待您再次赏光。</p>', get_comment_link( $comment_object->comment_ID ), $random_link, get_bloginfo('url') ) ;
        $mailcontent = $bg_head.$mailcontent.'</div></div>' ;

        $headers  = 'MIME-Version: 1.0' . "\r\n";

        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

        $headers .= 'From: 破襪子站长 <webmaster@pewae.com>'. "\r\n";
        
        //$headers .= 'Bcc: lifishake@gmail.com'. "\r\n";

        wp_mail($comment_parent->comment_author_email,'您在『'.get_option('blogname').'』 的留言有了新回复。',$mailcontent,$headers);
    }
}
/*                                          04终了                             */

/******************************************************************************/
/*        05.GFW有关的内容                                                       */
/******************************************************************************/
//5.1
/**
 * 作用: gravatar本地缓存/v2ex镜像
 * 来源: 邪罗刹
 * URL:  http://www.imevlos.com/
 */
function apip_get_cavatar($source) {
    global $apip_options;
    if( $apip_options['local_gravatar'] != 1 )
    {
        //$source = preg_replace('/\/\/\w+\.gravatar\.com\/avatar/', '//cdn.libravatar.org/avatar', $source);
        //$source = preg_replace('/\/\/\w+\.gravatar\.com\/avatar/', '//cdn.v2ex.com/gravatar', $source);
        //gravatar.eqoe.cn
		$source = str_replace(array('www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com'), 'cn.gravatar.com', $source);
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
//5.2
/**
 * 作用: 替换emoji服务器地址
 * 来源: 自创
 */
function apip_rep_emoji_url( $url )
{
	global $apip_options;
	if ( $apip_options['replace_emoji'] != 1)
		return $url;
    return '//coding.net/u/MinonHeart/p/twemoji/git/raw/gh-pages/72x72/' ;
}
/*                                          05终了                             */ 

/******************************************************************************/
/*        06.屏蔽垃圾留言                                                        */
/******************************************************************************/
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
/*                                          06终了                             */ 

/******************************************************************************/
/*        07.社会化分享                                                         */
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
    global $apip_options ;
    $intro = '<span>分享到:</span>' ;
    if ($apip_options['social_share_enable'] == 1 )
    {
        if ( $apip_options['social_share_twitter'] == 1 )
        {           
            $ret .= '<a class="sharebar-twitter" rel="nofollow" id="twitter-share" title="Twitter" ></a>' ;
            $count++;
        }
        if ( $apip_options['social_share_sina'] == 1 )
        {           
            $ret .= '<a class="sharebar-weibo" rel="nofollow" id="sina-share" title="sina" ></a>' ;
            $count++;
        }
        if ( $apip_options['social_share_tencent'] == 1 )
        {           
            $ret .= '<a class="sharebar-tencent-weibo" rel="nofollow" id="tencent-share" title="tencent" ></a>' ;
            $count++;
        }
        if ( $apip_options['social_share_googleplus'] == 1 )
        {           
            $ret .= '<a class="sharebar-googleplus" rel="nofollow" id="googleplus-share" title="g+" ></a>' ;
            $count++;
        }
        if ( $apip_options['social_share_facebook'] == 1 )
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
/*        08.自定义SHORTCODE                                                   */
/******************************************************************************/
//8.1自定义标签云
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
//8.2自定义友情链接页
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
/*                                          08终了                             */

/******************************************************************************/
/*        09.比较复杂的设置                                                      */
/******************************************************************************/
//9.1codehighlight相关
/**
 * 作用: 在页脚激活JS
 * 来源: 自产
 * URL:  
 */
function apip_footer_actions()
{
	global $apip_options ;
	//9.1
    if ( in_category('code_share') && $apip_options['apip_codehighlight_enable'] == 1 )
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
//9.2 Lazyload相关
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

//9.3 范围内跳转
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