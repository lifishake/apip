<?php
/*
移动不适用的代码到这里，作为备份
*/

//add_filter('do_shortcode_tag', 'apip_append_linebreak_to_myfv', 10, 2);

//add_action('admin_print_footer_scripts','apip_quicktags_a');

//add_filter( 'sanitize_title', 'apip_title_unicode', 1 );
//来源： https://so-wp.com/plugins/
function apip_title_unicode($strTitle) {
    $PSL = get_option( 'slug_length', 100 );

    $origStrTitle = $strTitle;
    $containsChinese = false;

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

function apip_slug($slug) {
	if (false === strpos($slug, "%")) {
		return $slug;
	}
	$str_tmp = str_replace("-","^^_^^",$slug);
	$str_tmp = str_replace("%","",ltrim($str_tmp,'%'));
	$str_tmp = str_replace("^^_^^","-",$str_tmp);
	return $str_tmp;
}

function apip_quicktags_a()
{
?>
    <script type="text/javascript" charset="utf-8">
        QTags.addButton( 'eg_163music', '网易云音乐', '<iframe frameborder="no" border="0" marginwidth="0" marginheight="0" width=330 height=86 src="//music.163.com/outchain/player?type=2&id=', '&auto=1&height=66"></iframe>' );
        QTags.addButton( 'eg_mydoubanmovie', '豆瓣电影', '[mydouban id="', '" type="movie" nipple="no" /]', 'p' );
        QTags.addButton( 'eg_mydoubanmusic', '豆瓣音乐', '[mydouban id="', '" type="music" /]', 'p' );
        QTags.addButton( 'eg_mydoubanbook', '豆瓣读书', '[mydouban id="', '" type="book" /]', 'p' );
        QTags.addButton( 'eg_mygame', '每夜一游', '[mygame id="', '" cname="" ename="" jname="" alias="" year="" publisher=""  platform="" download="" genres="" poster="" /]', 'p' );
        QTags.addButton( 'eg_myfavbook', '收藏书', '[myfv id="x" type="book" title="', '" img="x" link="" score="99" abs="doulink:;douscore:;作者:;译者:;出版年份:;出版社:;" series="0"/]', 'p' );
        QTags.addButton( 'eg_myfavbooklist', '收藏书系', '[myfv id="x" type="book" title="', '" img="" link="" score="99" abs="作者:;译者:;出版年份:;出版社:;全套册数:" series="1"/]', 'p' );
        QTags.addButton( 'eg_myfavmusic', '收藏音乐', '[myfv id="x" type="music" title="', '" img="x" link="" score="99" abs="出版年份:;出版公司:;表演者:;doulink:;douscore:;" series="0"/]', 'p' );
        QTags.addButton( 'eg_myfavmovie', '收藏电影', '[myfv id="x" type="movie" title="', '" img="x" link="" score="99" abs="年份:;导演:;演员:;类型:;nipple:;doulink:;douscore:;" series="0"/]', 'p' );
        QTags.addButton( 'eg_myfavauto', '收藏自动', '[myfv id="x" type="auto" img="x" link="', '" score="99" /]', 'p' );
    </script>
<?php
}


/*插件激活*/
function apip_plugin_activation()
{
    global $wpdb;
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

/*

    //8.5 豆瓣显示
    if ( !class_exists('Apip_SimpleImage') ) {
        //包跳转类含头文件
        require_once ( APIP_PLUGIN_DIR.'/class/apip-image.php') ;
    }

    //8.6 每夜一游
    add_shortcode('mygame', 'apip_game_detail');

    //8.11 我的收藏第一版
    add_shortcode('myfv', 'apip_myfv_detail');
    add_filter('do_shortcode_tag', 'apip_append_linebreak_to_myfv', 10, 2);
    add_action( 'transition_post_status', 'apip_myfv_filter', 10, 3 );
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

    /*因为是视图，所以每次都创建也无所谓*/
    /*
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

    $sql = "CREATE OR REPLACE VIEW `{$wpdb->prefix}v_boring_summary`
         AS SELECT `comment_author_email`, SUM(`meta_value`) AS `boring_value`
    FROM `{$wpdb->prefix}comments`
    LEFT JOIN `wp_commentmeta` ON `{$wpdb->prefix}comments`.`comment_ID` = `{$wpdb->prefix}commentmeta`.`comment_id`
    WHERE `user_id` = 0 AND `meta_key` = '_boring_rank'
    AND DATE_SUB( CURDATE( ) , INTERVAL 6 MONTH ) <= `comment_date_gmt`
    GROUP BY comment_author_email
    ORDER BY comment_author_email ASC ";
    $wpdb->query($sql);
*/

/*
豆瓣API相关函数，因为豆瓣API已经不适用，所以删除
20200930
    if ( apip_option_check('apip_douban_enable') )  {
        add_shortcode('mydouban', 'apip_dou_detail');
    }
	add_shortcode('myimdb', 'apip_imbd_detail');
 */

 //8.5 豆瓣电影
/**
* 作用: 显示来自豆瓣的音乐/电影/图书信息。本函数是主入口。
* 来源: 大发(bigFa)
* URL: https://github.com/bigfa/wp-douban
*/
function apip_dou_detail( $atts, $content = null ) {
    $atts = shortcode_atts( array( 'id' => '', 'type' => '', 'score'=>'', 'nipple'=>'no', 'link'=>'', 'count'=>'0', 'total'=>'0', 'alt'=>'', 'series'=>'' ), $atts );
    extract( $atts );
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
                        apip_get_saved_images($id,str_replace('spic','mpic',$data['image']),'douban',100,100),
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
* 作用: taxonomy和meta在list中排序。
* 来源: 网络
*/
//add_filter( 'posts_clauses', array($this, 'custom_taxonomy_posts_clauses'), 10, 2 );
function custom_taxonomy_posts_clauses( $pieces, $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return $pieces;
		}
		global $wpdb;
		$post_type = $query->get( 'post_type' );
		$orderby = $query->get( 'orderby' );
		$this->set_working_mode($post_type);
		if (!isset($this->total_items[$orderby])||
			!$this->total_items[$orderby]['show_admin_column']) {
			return $pieces;
		}

		$item = $this->total_items[$orderby];
		$order = strtoupper( $query->get( 'order' ) );
		if ($order != 'DESC') $order = 'ASC';
		if ('tax' == $item['type']) {
			$pieces[ 'join' ] .= ' LEFT JOIN ' . $wpdb->term_relationships . ' AS tr ON ' . $wpdb->posts . '.ID = tr.object_id'
			. ' LEFT JOIN ' . $wpdb->term_taxonomy . ' AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id'
			. ' LEFT JOIN ' . $wpdb->terms . ' AS t ON tt.term_id = t.term_id';
			$pieces[ 'fields' ] .= ', group_concat(t.name ORDER BY t.name ' . $order . ') AS ' . $orderby;
			$pieces[ 'groupby' ] = $wpdb->posts . '.ID';
			$pieces[ 'orderby' ] = $orderby . ' ' . $order . ', ' . $wpdb->posts . '.post_title ASC';
			//$pieces['where'] = 'AND ( tr.object_id IS NULL OR t.name = \'' . $orderby . '\') ' . $pieces['where'];
		}
		if ('meta' == $item['type']) {
			$pieces[ 'groupby' ] = $wpdb->posts . '.ID';
			$pieces[ 'join' ] = ' LEFT JOIN ' . $wpdb->postmeta . ' ON ('. $wpdb->posts .'.ID = '. $wpdb->postmeta .'.post_id AND ' . $wpdb->postmeta . '.meta_key = \'' . $orderby . '\' ) '
			. ' LEFT JOIN ' . $wpdb->postmeta . ' AS mt1 ON ( ' . $wpdb->posts . '.ID = mt1.post_id )';
			$pieces['where'] = 'AND ( ' . $wpdb->postmeta . '.post_id IS NULL OR mt1.meta_key = \'' . $orderby . '\') ' . $pieces['where'];
			if ($item['inputstyle'] == 'number') {
				$pieces[ 'orderby' ] = 'CAST(' . $wpdb->postmeta . '.meta_value AS SIGNED)'. $order;
			} else {
				$pieces[ 'orderby' ] = $wpdb->postmeta . '.meta_value '. $order;
			}
		}

		return $pieces;
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
* 作用: 用于保存图像缓存的子函数。
* 来源: 大发
*/
function apip_get_saved_images($id, $src, $dst, $width = 100, $height = 150 )  {

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
        if ($imglocal->getWidth() != $width) {
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
        $image->resize($width, $height);
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

function apip_trim_fetched_item($value) {
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
        $end_pos = strpos($body, "</title>", $start_pos);
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
    $items = array_map('apip_trim_fetched_item', $items);
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
    //20220528 函数从file_get_content改成curl，放弃对代理的使用
    /*
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
    */
    if ( !$content )
    {
        if ( $nodata  ) {
            $content['error'] = 'OK';
            $content['results']['image']['thumb_url'] = $poster;
            $content['results']["site_detail_url"] = get_the_permalink();
            $content['results']["name"] = $ename!=''?$ename:($cname!=''?$cname: get_the_title());
        } else {
            $url = "https://www.giantbomb.com/api/game/".$id."/?api_key=".$token."&format=json&field_list=site_detail_url,genres,image,platforms,original_release_date,name,publishers";
           
            delete_transient($cache_key);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_USERAGENT, 'API Test UA');
            curl_setopt($curl, CURLOPT_TIMEOUT, 180);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            $response = curl_exec($curl);
            curl_close($curl);

            //从链接取数据            
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
    $str_platform = sprintf('<span class="platform">机种：%s</span>', $str_the_platform);

    $str_download = '';
    if ( $download !== '' ){
        $str_download =sprintf('<span class="platform"><a href="%s" class="cute" target="_blank" rel="external nofollow">下载</a></span>',$download);
    }
    
    $abstract_str = $str_name.$str_pubdate.$str_publisher.$str_genres.$str_platform.$str_download;

    $out = sprintf($template, $img_str, $title_str, $abstract_str, $subject_class);
    return $out;

}

?>
 <span>    豆瓣条目（css+js）<i>CODE:mydouban</i>：</span>
 <input type='checkbox' name='apip_settings[apip_douban_enable]' <?php checked( $options['apip_douban_enable'], 1 ); ?> value='1'><br />
 <span>    douban API KEY<i>CODE:doubankey</i>：</span>
 <input type='text' name='apip_settings[douban_key]' size='64' value='<?php echo $options['douban_key']; ?>'/><br />
 <?php
 ?>