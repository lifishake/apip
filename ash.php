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
public function custom_taxonomy_posts_clauses( $pieces, $query ) {
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

?>
 <span>    豆瓣条目（css+js）<i>CODE:mydouban</i>：</span>
 <input type='checkbox' name='apip_settings[apip_douban_enable]' <?php checked( $options['apip_douban_enable'], 1 ); ?> value='1'><br />
 <span>    douban API KEY<i>CODE:doubankey</i>：</span>
 <input type='text' name='apip_settings[douban_key]' size='64' value='<?php echo $options['douban_key']; ?>'/><br />
 <?php
 ?>