<?php 
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
    $ret = $ret.'<li><span>'.$history_post->h_year.':&nbsp;&nbsp<span><a class="sameday-post" href="'.get_permalink( $history_post->ID ).'">' ;
    $ret = $ret.$history_post->post_title.'</a></li>' ;
    endforeach; 
    if ( $rcount > 0 )
    {
        foreach ( $random_posts as $random_post ) :
        $ret = $ret.'<li><span>RAND:&nbsp;&nbsp;</span><a class="sameday-post" href="'.get_permalink( $random_post->ID ).'">' ;
        $ret = $ret.$random_post->post_title.'</a></li>' ;
        endforeach;     
    }
    $ret = $ret.'</ul>' ;
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
    $random_posts = get_posts( array( 'exclude' => $exclude, 'orderby' => 'rand', 'posts_per_page'=>$count ) ) ;
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
    $limit = $apip_options['local_definition_count'] ? $apip_options['local_definition_count'] : 5 ;
    global $wpdb ;
    $object_ids = array();
    $post_id = get_the_ID() ;
    $tags = array();
    $tags = get_the_terms( $post_id, 'post_tag') ;
    if( $tags != 0 )
    {
        $term_taxonomy_ids = wp_list_pluck( $tags, 'term_taxonomy_id' );
        $term_taxonomy_ids_str = implode( ",", $term_taxonomy_ids  );
        $object_ids = $wpdb->get_col( "SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( {$term_taxonomy_ids_str} ) AND object_id != '$post_id' " );
        $object_ids = array_count_values( $object_ids );
        arsort( $object_ids );
        $object_ids = array_keys($object_ids) ;
    }   
    
    if ( count( $object_ids )< $limit )
    {
        $random_posts = apip_random_post( get_the_ID(), $limit - count( $object_ids ) + 1 ) ;
        if ( count($random_posts) > 0 )
        {
            $random_ids = wp_list_pluck( $random_posts, 'ID' );
            $object_ids = array_merge( $object_ids, $random_ids ) ;
        }
    }
    while( count($object_ids) > $limit )
    {
        array_pop($object_ids) ;
    }

    $ret = '<ul class = "apip-ralated-content">' ;
    $terms = wp_get_post_terms( get_the_ID(), 'post_tag', array( 'fields' => 'ids' ));
    $inc_str = implode( ",", $object_ids  );
    $related_posts = get_posts( array(
            'include' => $inc_str,
            'posts_per_page'=>  $limit ) ) ;
    foreach ( $related_posts as $related_post ) :
    $ret = $ret.'<li> <a class="related-post" href="'.get_permalink( $related_post->ID ).'">' ;
    $ret = $ret.$related_post->post_title.'</a></li>' ;
    endforeach;     
    $ret = $ret.'</ul>' ;
    return $ret; 
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

