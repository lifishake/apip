<?php

/**
 * 作用: 显示heweather情报所包含的文字。
 * 资源: CSS和图标来自，http://erikflowers.github.io/weather-icons，图标字体使用 SIL OFL 1.1 -  http://scripts.sil.org/OFL授权；
 * CSS使用MIT License - http://opensource.org/licenses/mit-license.html授权。
 * 来源: 自产
 * URL:
 */
function apip_get_heweather( $style='')
{
    $ret = '';
    //$weather_result = array();
    $weather_result = get_post_meta(get_the_ID(),'apip_heweather',false);
    if ( empty($weather_result) || null==($weather_result[0]['time']))
    {
        return $ret;
    }
    $then = $weather_result[0]['result'];
    $cond_code = (int)($then['cond_code']);
    switch($cond_code) {
        case	100	:	//	晴
        case	102	:	//	少云
        case	201	:	//	平静
        case	202	:	//	微风
            $w_icon_str = 'wi-day-sunny';
            break;
        case	101	:	//	多云
        case	103	:	//	晴间多云
            $w_icon_str = 'wi-day-cloudy-high';
            break;
        case	104	:	//	阴
            $w_icon_str = 'wi-cloudy';
            break;
        case	200	:	//	有风
            $w_icon_str = 'wi-cloudy';
            break;
        case	203	:	//	和风
        case	204	:	//	清风
            $w_icon_str = 'wi-day-light-wind';
            break;
        case	205	:	//	强风/劲风
        case	206	:	//	疾风
        case	207	:	//	大风
        case	208	:	//	烈风
            $w_icon_str = 'wi-day-windy';
            break;
        case	209	:	//	风暴
        case	210	:	//	狂爆风
        case	211	:	//	飓风
            $w_icon_str = 'wi-strong-wind';
            break;
        case	212	:	//	龙卷风
            $w_icon_str = 'wi-tornado';
            break;
        case	213	:	//	热带风暴
            $w_icon_str = 'wi-hurricane';
            break;
        case	309	:	//	毛毛雨/细雨
        case	300	:	//	阵雨
        case	301	:	//	强阵雨
            $w_icon_str = 'wi-showers';
            break;
        case	302	:	//	雷阵雨
        case	303	:	//	强雷阵雨
            $w_icon_str = 'wi-storm-showers';
            break;
        case	304	:	//	雷阵雨伴有冰雹
            $w_icon_str = 'wi-hail';
            break;
        case	305	:	//	小雨
        case	306	:	//	中雨
            $w_icon_str = 'wi-rain';
            break;
        case	307	:	//	大雨
            $w_icon_str = 'wi-rain-mix';
            break;
        case	308	:	//	极端降雨
        case	310	:	//	暴雨
        case	311	:	//	大暴雨
        case	312	:	//	特大暴雨
            $w_icon_str = 'wi-raindrops';
            break;
        case	313	:	//	冻雨
            $w_icon_str = 'wi-sleet';
            break;
        case	400	:	//	小雪
        case	401	:	//	中雪
        case	402	:	//	大雪
        case	403	:	//	暴雪
        case	404	:	//	雨夹雪
        case	405	:	//	雨雪天气
        case	406	:	//	阵雨夹雪
        case	407	:	//	阵雪
            $w_icon_str = 'wi-snow';
            break;
        case	500	:	//	薄雾
        case	501	:	//	雾
            $w_icon_str = 'wi-fog';
            break;
        case	502	:	//	霾
            $w_icon_str = 'wi-smog';
            break;
        case	503	:	//	扬沙
        case	504	:	//	浮尘
            $w_icon_str = 'wi-dust';
            break;
        case	507	:	//	沙尘暴
        case	508	:	//	强沙尘暴
            $w_icon_str = 'wi-sandstorm';
            break;
        case	900	:	//	热
            $w_icon_str = 'wi-hot';
            break;
        case	901	:	//	冷
            $w_icon_str = 'wi-snowflake-cold';
            break;
        case	999	:	//	未知
        default:
            $w_icon_str = 'wi-na';
            break;
    }
    $wind_str = '';
    if ((int)$then['wind_spd'] > 38) {
        $wind_icon_str = "from-".$then['wind_deg']."-deg";
        $wind_str = $then['wind_dir'].$then['wind_sc']."级 ";
    }
    $ret = '<i class="wi '.$w_icon_str.' icon"></i>';
    if ('notext'!=$style) {
        $ret .= $then['cond_txt'];
    }
    if ( '' !== $wind_str) {
        $ret .= '  <i class="wi wi-wind '.$wind_icon_str.'"></i> ';
        if ('notext'!=$style) {
            $ret .= $wind_str;
        }
    }
    if ('notext'!=$style) {
        $ret .= '  <i class="wi wi-thermometer"></i> ';
        $ret .= $then['tmp'].'&#8451;';
    }
    return $ret;
}

/**
* 作用: 显示留言测试问题
* 来源: 插件改编，汉化和格式修改
* URL: https://github.com/nrkbeta/nrkbetaquiz
*/
function apip_commentquiz_form(){
    if ( !is_single() || !comments_open() || !apip_option_check('apip_commentquiz_enable')) {
        return;
    };
    $quizs = get_post_meta(get_the_ID(), 'apipcommentquiz');
    if ( empty($quizs) ) {
        return;
    }
    ?>
  <div class="apipcommentquiz"
    data-apipcommentquiz="<?php echo esc_attr(rawurlencode(json_encode($quizs))); ?>"
    data-apipcommentquiz-error="<?php echo esc_attr('回答错误，请重试', 'apipcommentquiz'); ?>">
    <h2>答对问题，留言框就会出现</h2>
    <p>
      珍爱生命，拒绝尬聊。<br/>只要贵站的订阅通道畅通且言之有物，本人一定会回访。
    </p>
  </div>
<?php }


/**
 * 作用: 显示最近更新文章
 * 来源: 自产
 * URL:
 */
function apip_recent_post()
{
    global $apip_options;
    $limit = $apip_options['local_definition_count'] ? $apip_options['local_definition_count'] : 5 ;
    $ret = '<ul class = "apip-recent-content">' ;
    $recent_posts = get_posts( array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'orderby' => 'modified',
                    'order' => 'DESC',
                    'numberposts' => $limit
               ) );
    foreach ( $recent_posts as $recent_post ) :
    $ret = $ret.'<li> <a class="recent-post" href="'.get_permalink( $recent_post->ID ).'">' ;
    $ret = $ret.$recent_post->post_title.'</a></li>' ;
    endforeach;
    $ret = $ret.'</ul>' ;
    return $ret;
}

/**
 * 作用: 显示历史相同日文章
 * 来源: 自产
 * URL:
 */
function apip_sameday_post()
{
    global $wpdb;
    $month = get_post_time('m');
    $day = get_post_time('j');
    $id = get_the_ID() ;
    global $apip_options;
    $limit = $apip_options['local_definition_count'] ? $apip_options['local_definition_count'] : 5 ;
    $ret = '<ul class = "apip-history-content">' ;
    $sql = "select ID, year(post_date) as h_year, post_title FROM
            $wpdb->posts WHERE post_password = '' AND post_type = 'post' AND post_status = 'publish'
            AND month(post_date)='$month' AND day(post_date)='$day' AND ID != '$id'
            order by post_date LIMIT $limit";
    $history_posts = $wpdb->get_results($sql);
    $rcount = $limit - count( $history_posts ) ;
    if ( $rcount > 0 )
    {
        $random_posts = apip_random_post( get_the_ID(), $rcount ) ;
    }
    foreach ( $history_posts as $history_post ) :
    $ret = $ret.'<li><span class="func-before suffix">['.$history_post->h_year.']</span><a class="sameday-post" href="'.get_permalink( $history_post->ID ).'">' ;
    $ret = $ret.$history_post->post_title.'</a></li>' ;
    endforeach;
    if ( $rcount > 0 )
    {
        foreach ( $random_posts as $random_post ) :
        $ret .= '<li><span class="func-before suffix">[RAND]</span><a class="sameday-post" href="'.get_permalink( $random_post->ID ).'">' ;
        $ret .= $random_post->post_title.'</a></li>' ;
        endforeach;
    }
    $ret .= '</ul>' ;
    return $ret;
}

/**
 * 作用: 显示随机文章
 * 来源: 自产
 * URL:
 */
function apip_random_post( $exclude, $count = 5 )
{
    $ret = array() ;
    if ( 0 == $count )
    {
        return $ret ;
    }
    $random_posts = get_posts( array( 'exclude' => array($exclude,1), 'orderby' => 'rand', 'posts_per_page'=>$count ) ) ;
    return $random_posts ;
}

/**
 * 作用: 显示相关
 * 来源: 自产
 * URL:
 */
function apip_related_post()
{
    global $apip_options;
    $limit = ($apip_options['local_definition_count'] > 0 )? $apip_options['local_definition_count'] : 5 ;
    global $wpdb ;
    $object_ids = array();
    $post_id = get_the_ID() ;
    $tags = get_the_terms( $post_id, 'post_tag') ;
    $cats = get_the_terms( $post_id, 'category') ;
    if( $tags && count($tags) != 0 )
    {
        if (  count($tags) > 1 )
        {
            $tags = array_merge( $tags, $cats ) ;
        }
        $term_taxonomy_ids = wp_list_pluck( $tags, 'term_taxonomy_id' );
        $term_taxonomy_ids_str = implode( ",", $term_taxonomy_ids  );
        $query = "SELECT rel.`object_id`, SUM(v.`term_weight`) AS `evaluate`
                FROM {$wpdb->term_relationships} rel, `{$wpdb->prefix}v_taxonomy_summary` v, {$wpdb->posts} pp
                WHERE rel.`term_taxonomy_id` IN ({$term_taxonomy_ids_str})
                AND rel.`term_taxonomy_id` = v.`term_taxonomy_id`
                AND rel.`object_id` != '$post_id'
                AND rel.`object_id` = pp.`ID`
                AND pp.`post_status`  = 'publish'
                GROUP BY rel.`object_id`
                ORDER BY `evaluate` DESC,
                rel.`object_id` DESC";
        $weights = $wpdb->get_results($query,OBJECT_K);
        $object_ids = wp_list_pluck( $weights, 'object_id','object_id' );
    }

    if ( count( $object_ids )< $limit )
    {
        $random_posts = apip_random_post( get_the_ID(), $limit - count( $object_ids ) + 1 ) ;
        if ( count($random_posts) > 0 )
        {
            $random_ids = wp_list_pluck( $random_posts, 'ID','ID' );
            $object_ids = array_merge( $object_ids, $random_ids ) ;
        }
    }
    while( count($object_ids) > $limit )
    {
        array_pop($object_ids) ;
    }

    $ret = '<ul class = "apip-ralated-content">' ;
    foreach ( $object_ids as $id ) :
    $ret .= sprintf("<li><a class=\"related-post\" href=\"%s\">%s</a><span class=\"func-after suffix\">[%s&#37;]</span></li>",
            get_permalink( $id ),
            get_the_title( $id ),
            isset($weights[$id]) ? floor(100*$weights[$id]->evaluate/4096) : 0 );
    endforeach;
    $ret .= '</ul>' ;
    return $ret;
}

function apip_is_important_taxonomy( $id ) {
  global $wpdb ;
  $query = "SELECT `term_taxonomy_id`
                  FROM `{$wpdb->prefix}v_taxonomy_summary`
                  WHERE  `term_taxonomy_id` = $id
                  AND  `term_weight` > 1000 ";
  $val = $wpdb->get_col($query);
  return !empty($val);
}
/*
 * 作用: 显示作者最愿意回复的n个留言者的链接
 * 来源: 自产
 * URL:
*/
function apip_get_links()
{
    global $wpdb;
    $limit = 13; //取多少条，可以自己改
    $scope = "6 MONTH"; //可以使用的时间关键字:SECOND,MINUTE,HOUR,DAY,WEEK,MONTH,QUARTER,YEAR...
    $sql = "SELECT comment_author_email, comment_author_url, comment_author, SUM(comment_length) as words_sum
            FROM $wpdb->comments aa
            INNER JOIN
            (
            SELECT comment_parent, char_length(comment_content) as comment_length
            FROM $wpdb->comments
            WHERE  user_id <> 0
            AND DATE_SUB( CURDATE( ) , INTERVAL $scope ) <= comment_date_gmt
            ORDER BY comment_date_gmt DESC
            )
            AS bb
            WHERE aa.comment_ID = bb.comment_parent
            AND aa.comment_author_url <>''
            GROUP BY comment_author_email
            ORDER BY words_sum DESC
            LIMIT $limit";
    $result = $wpdb->get_results($sql);
    return $result;
}

/*
 * 作用: 取得上一篇/下一篇.如果在归档/搜索的情况下,在范围内查找.
 * 来源: 自产
 * URL:
*/
function apip_get_post_navagation($args=array()){
   $args = wp_parse_args( $args, array(
        'prev_text'          => '%title',
        'next_text'          => '%title',
        'screen_reader_text' => '文章导航',
    ) );
    //只在singlular的时候有效，因为只有singlular的时候能取到get_the_ID()。
    if ( !is_singular() ){
        return;
    }
    if ( !class_exists('Apip_Query') ){
        the_post_navigation($args);
        return;
    }
    $key = 'apip_aq_'.COOKIEHASH;
    $apip_aq = get_transient($key);
    if ( false === $apip_aq ){
        the_post_navigation($args);
        return;
    }
    $ID = get_the_ID();
    $result = $apip_aq->get_neighbor($ID);
    if ( !$result || !$result['got'] ){
        the_post_navigation($args);
        return;
    }

    //仿照the_post_navigation的格式显示
    if ( $result['prev'] > 0 ) {
         $previous = str_replace( '%title', get_the_title( $result['prev'] ), $args['prev_text'] );		 		 $previous = '<a href="'.get_permalink( $result['prev'] ).'" rel="prev">'.$previous.'</a>';
         $previous = '<div class="nav-previous">'.$previous.'</div>';
    }
    if ( $result['next'] > 0 ) {			$next = str_replace( '%title', get_the_title( $result['next'] ), $args['next_text'] );		 		$next = '<a href="'.get_permalink( $result['next'] ).'" rel="next">'.$next.'</a>';        $next = '<div class="nav-next">'.$next.'</div>';
    }
    if ( "" === $desc = $apip_aq->get_title() ) $desc = $args['screen_reader_text'];
    $navigation = _navigation_markup( $previous . $next, 'post-navigation', $desc );
    echo $navigation;
}

/*
 * 作用: 移除某个类的filter方法
 * 来源: http://wordpress.stackexchange.com/questions/57079/how-to-remove-a-filter-that-is-an-anonymous-object
 * URL: http://wordpress.stackexchange.com/questions/57079/how-to-remove-a-filter-that-is-an-anonymous-object
*/
function apip_remove_anonymous_object_hook( $tag, $class, $method )
{
    $filters = $GLOBALS['wp_filter'][ $tag ];

    if ( empty ( $filters ) )
    {
        return;
    }
    foreach ( $filters as $priority => $filter )
    {
        foreach ( $filter as $identifier => $function )
        {
            if ( is_array( $function)
                and is_a( $function['function'][0], $class )
                and $method === $function['function'][1]
            )
            {
                //action也可以用remove_filter删除。
                remove_filter(
                    $tag,
                    array ( $function['function'][0], $method ),
                    $priority
                );
            }
        }
    }
}

/*
 * 作用: 移除wpembed相关的内部插件
 * 来源: Disable Embeds
 * URL: https://pascalbirchler.com
*/
function apip_disable_embeds_tiny_mce_plugin( $plugins ) {
	return array_diff( $plugins, array( 'wpembed' ) );
}
/* 同上 */
function apip_disable_embeds_rewrites( $rules ) {
	foreach ( $rules as $rule => $rewrite ) {
		if ( false !== strpos( $rewrite, 'embed=true' ) ) {
			unset( $rules[ $rule ] );
		}
	}

	return $rules;
}
/* 同上 */
function apip_disable_embeds_remove_rewrite_rules() {
	add_filter( 'rewrite_rules_array', 'apip_disable_embeds_rewrites' );
	flush_rewrite_rules();
}
/* 同上 */
function apip_disable_embeds_flush_rewrite_rules() {
	remove_filter( 'rewrite_rules_array', 'apip_disable_embeds_rewrites' );
	flush_rewrite_rules();
}

function apip_media_upload_nextgen() {

    // Not in use
    $errors = false;

	// Generate TinyMCE HTML output
	if ( isset($_POST['send']) ) {
		$keys = array_keys($_POST['send']);
		$send_id = (int) array_shift($keys);
		$image = $_POST['image'][$send_id];
		$alttext = stripslashes( htmlspecialchars ($image['alttext'], ENT_QUOTES));
		$description = stripslashes (htmlspecialchars($image['description'], ENT_QUOTES));

		// here is no new line allowed
		$clean_description = preg_replace("/\n|\r\n|\r$/", " ", $description);
		$img = nggdb::find_image($send_id);
		$thumbcode = $img->get_thumbcode();

        // Create a shell displayed-gallery so we can inspect its settings
        $registry = C_Component_Registry::get_instance();
        $mapper   = $registry->get_utility('I_Displayed_Gallery_Mapper');
        $factory  = $registry->get_utility('I_Component_Factory');
        $args = array(
            'display_type' => NGG_BASIC_SINGLEPIC
        );
        $displayed_gallery = $factory->create('displayed_gallery', $args, $mapper);
        $domain = str_replace(array('http://','https://'), '', get_bloginfo('url'));
        $urls = array();
        $urls[] = 'http://'.$domain;
        $urls[] = 'https://'.$domain;
        $image['thumb'] = str_replace($urls, '', $image['thumb']);
        $image['url'] = str_replace($urls, '', $image['url']);
		// Build output
		if ($image['size'] == "thumbnail")
			$html = "<img src='{$image['thumb']}' alt='{$alttext}' />";
        else
            $html = '';

		// Wrap the link to the fullsize image around
		$html = "<a {$thumbcode} href='{$image['url']}' title='{$clean_description}'>{$html}</a>";

		if ($image['size'] == "full" || $image['size'] == "singlepic")
			$html = "<img src='{$image['url']}' alt='{$alttext}' />";


		media_upload_nextgen_save_image();

		// Return it to TinyMCE
		return media_send_to_editor($html);
	}

	// Save button
	if ( isset($_POST['save']) ) {
		media_upload_nextgen_save_image();
	}

	return wp_iframe( 'media_upload_nextgen_form', $errors );
}

/**
 * 作用: HEX描述的颜色值转成RGB
 * 来源: Oblique原版
 */
if (!function_exists('hex2rgb'))
{
function hex2rgb($color) {
	if ($color[0] == '#' ) {
		$color = substr( $color, 1 );
	}
	$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    $rgb =  array_map('hexdec', $hex);
	return $rgb;
}
}

/**
 * 作用: RGB颜色值转成HSV描述
 * 来源: http://stackoverflow.com/questions/1773698/rgb-to-hsv-in-php
 * 输出的范围0-360, 0-100, 0-100!!
 */
if (!function_exists('rgb2hsv'))
{
function rgb2hsv(array $rgb)
{
	list($R,$G,$B) = $rgb;
    $R = ($R / 255);
    $G = ($G / 255);
    $B = ($B / 255);

    $maxRGB = max($R, $G, $B);
    $minRGB = min($R, $G, $B);
    $chroma = $maxRGB - $minRGB;

    $computedV = floor(100 * $maxRGB);

    if ($chroma == 0)
        return array(0, 0, $computedV);

    $computedS = floor(100 * ($chroma / $maxRGB));

    if ($R == $minRGB)
        $h = 3 - (($G - $B) / $chroma);
    elseif ($B == $minRGB)
        $h = 1 - (($R - $G) / $chroma);
    else // $G == $minRGB
        $h = 5 - (($B - $R) / $chroma);

    $computedH = floor(60 * $h);

    return array($computedH, $computedS, $computedV);
}
}

if (!function_exists('hsv2rgb'))
{
function hsv2rgb(array $hsv) {
	list($H,$S,$V) = $hsv;
	//1
	$H /= 60;
	//2
	$I = floor($H);
	$F = $H - $I;
	$S /= 100;
	$V /= 100;
	//3
	$M = round( $V * (1 - $S) * 255);
	$N = round( $V * (1 - $S * $F) * 255 );
	$K = round( $V * (1 - $S * (1 - $F)) * 255 );
	$V = round( $V * 255) ;
	//4
	switch ($I) {
		case 0:
			list($R,$G,$B) = array($V,$K,$M);
			break;
		case 1:
			list($R,$G,$B) = array($N,$V,$M);
			break;
		case 2:
			list($R,$G,$B) = array($M,$V,$K);
			break;
		case 3:
			list($R,$G,$B) = array($M,$N,$V);
			break;
		case 4:
			list($R,$G,$B) = array($K,$M,$V);
			break;
		case 5:
		case 6: //for when $H=1 is given
			list($R,$G,$B) = array($V,$M,$N);
			break;
	}
	return array($R, $G, $B);
}
}
function apip_get_link_colors( $color_str)
{
    $rgb = hex2rgb($color_str);
    $hsv = rgb2hsv($rgb);
    $ret = array();
    $hsv_temp = $hsv;
    for($i=4; $i>=0; $i--) {
        if ($hsv[1]<50){
            $hsv_temp[1] = $hsv[1]+$i*8;
        }
        else {
            $hsv_temp[1] = $hsv[1]-$i*8;
            }
        $rgb_temp = hsv2rgb($hsv_temp);
        $ret[] =sprintf("#%1$02X%2$02X%3$02X",$rgb_temp[0],$rgb_temp[1],$rgb_temp[2]) ;
    }
        return $ret;
}
function apip_get_bg_colors($color_str,$trancy = 0.6)
{
    $rgb = hex2rgb($color_str);
    $hsv = rgb2hsv($rgb);
    $ret = array();
    $hsv_temp = $hsv;
    for($i=4; $i>=0; $i--) {
        $hsv_temp[1] = $hsv[1]+100-5*$i;
        if ($hsv_temp[1]>100)
        {
            $hsv_temp[1] -= 100;
        }
        $rgb_temp = hsv2rgb($hsv_temp);
        $ret[] =sprintf("RGBA(%d,%d,%d,%1.1f)",$rgb_temp[0],$rgb_temp[1],$rgb_temp[2],$trancy) ;
    }
    $ret =array_reverse($ret);
    return $ret;
}
