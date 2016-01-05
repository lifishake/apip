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
