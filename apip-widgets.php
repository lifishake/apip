<?php
class APIP_Widget_Related extends WP_Widget {


  public function __construct() {
       $widget_ops = array(
            'classname' => 'APIP_Widget_Related',
            'description' => '在单独post页面显示相关文章'
       );
       parent::__construct( 'APIP_Widget_Related', 'APIP相关文章', $widget_ops );
  }

  public function form( $instance ) {

       $instance = wp_parse_args( (array) $instance, array( 'title' => 'APIP相关文章', 'exclude' => '', 'taxonomy' => 'post_tag', 'post_type' => 'post', 'related_count' => '5' ) );
       $title = $instance['title'];
       $post_type = $instance['post_type'];
       $taxonomy = $instance['taxonomy'];
       $exclude = $instance['exclude'];
       $related_count = $instance['related_count']; ?>
       <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                 <?php _e( 'Title' , 'simply-related-posts'); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo attribute_escape( $title ); ?>" />
            </label>
       </p>
       <p>
            <label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>">
                 <?php _e( 'Related by taxonomy' , 'simply-related-posts'); ?>: <select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>"><?php
                      $taxonomies = get_taxonomies( array( 'show_ui' => true ), 'objects' );
                      foreach ( $taxonomies as $slug => $tax ): ?>
                           <option value="<?php echo $slug; ?>" <?php echo ( $slug == $taxonomy ) ? 'selected="selected"' : ''; ?>><?php echo $tax->labels->name; ?></option><?php
                      endforeach; ?>
                 </select>
            </label>
       </p>
       <p>
            <label for="<?php echo $this->get_field_id( 'post_type' ); ?>">
                 <?php _e( 'Related post-type' , 'simply-related-posts'); ?>: <select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>"><?php
                      $post_types = get_post_types( array( 'show_ui' => true, 'public' => true ), 'objects', 'and' );
                      foreach ( $post_types as $slug => $pt ): ?>
                           <option value="<?php echo $slug; ?>" <?php echo ( $slug == $post_type ) ? 'selected="selected"' : ''; ?>><?php echo $pt->labels->name; ?></option><?php
                      endforeach; ?>
                 </select>
            </label>
       </p>
       <p>
            <label for="<?php echo $this->get_field_id( 'related_count' ); ?>">
                 <?php _e( 'How many posts to show' , 'simply-related-posts'); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'related_count' ); ?>" name="<?php echo $this->get_field_name( 'related_count' ); ?>" type="text" value="<?php echo attribute_escape( $related_count ); ?>" />
            </label>
       </p>
       <p>
            <label for="<?php echo $this->get_field_id( 'exclude' ); ?>">
                 <?php _e( 'Term ids to exclude (e.g 5,4,2)' , 'simply-related-posts'); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" type="text" value="<?php echo attribute_escape( $exclude ); ?>" />
            </label>
       </p><?php

  }


  public function update( $new_instance, $old_instance ) {

       $instance = $old_instance;
       $instance['title'] = $new_instance['title'];
       $instance['post_type'] = $new_instance['post_type'];
       $instance['related_count'] = $new_instance['related_count'];
       $instance['exclude'] = $new_instance['exclude'];
       $instance['taxonomy'] = $new_instance['taxonomy'];
       return $instance;

  }


  public function widget( $args, $instance ) {

       if ( !is_singular() )
            return;

       extract( $args, EXTR_SKIP );

       // Thanks to David Gil (github.com/dgilperez) for the custom-post-type fix
       $post_type = $instance['post_type'];
       $taxonomy = ( $instance['taxonomy'] == "" ) ? 'post_tag' : $instance['taxonomy'];
       $terms = wp_get_post_terms( get_the_ID(), $taxonomy, array( 'fields' => 'ids' ));

       $exclude = ( empty( $instance['exclude'] ) ) ? array() : explode( ',', $instance['exclude'] );
       if ( count( ( $terms = array_diff( $terms, $exclude ) ) ) == 0 )
            return;

       $related_posts = get_posts( array(
            'tax_query' => array(
                 array(
                      'taxonomy' => $taxonomy,
                      'field' => 'id',
                      'terms' => $terms,
                      'operator' => 'IN'
                 )
            ),
            'post_type' => $post_type,
            'posts_per_page' => $instance['related_count'],
            'exclude' => get_the_ID()
       ) );

       if ( count( $related_posts ) == 0 )
            return;

       echo $before_widget;

       $title = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
       if ( !empty( $title ) )
            echo $before_title . $title . $after_title; ?>
       <ul><?php
            foreach ( $related_posts as $related_post ) : ?>
                 <li>
                      <a class="related-post" href="<?php echo get_permalink( $related_post->ID ); ?>">
                           <?php echo apply_filters( 'simply_related_posts_title', $related_post->post_title, $related_post, $instance ); ?>
                      </a>
                 </li><?php
            endforeach; ?>
       </ul><?php

       echo $after_widget;

  }

}
//add_action( 'widgets_init', create_function( '', 'return register_widget( "APIP_Widget_Related" );' ) );


 class APIP_Widget_Sameday_Post extends WP_Widget {


          public function __construct() {

               $widget_ops = array(
                    'classname' => 'APIP_Widget_Sameday_Post',
                    'description' =>'显示历史同日文章'
               );
               parent::__construct( 'APIP_Widget_Sameday_Post', 'APIP历史文章', $widget_ops );
          }

          public function form( $instance ) {

               $instance = wp_parse_args( (array) $instance, array( 'title' => 'APIP历史文章', 'numbers' => '5', 'padtonum' => 'false' ) );
               $title = $instance['title'];
               $numbers = $instance['numbers'];
               $padtonum = $instance['padtonum'];
               ?>
               <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                         <?php _e( 'Title' , 'history-post-on-the-day'); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo attribute_escape( $title ); ?>" />
                    </label>
               </p>
               <p>
                    <label for="<?php echo $this->get_field_id( 'numbers' ); ?>">
                         <?php _e( 'How many posts to show' , 'history-post-on-the-day'); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'numbers' ); ?>" name="<?php echo $this->get_field_name( 'numbers' ); ?>" type="text" value="<?php echo attribute_escape( $numbers ); ?>" />
                    </label>
               </p><?php

          }


          public function update( $new_instance, $old_instance ) {

               $instance = $old_instance;
               $instance['title'] = $new_instance['title'];
               $instance['numbers'] = $new_instance['numbers'];
               $instance['padtonum'] = $new_instance['padtonum'];
               return $instance;

          }


          public function widget( $args, $instance ) {

               if ( !is_singular() )
                    return;
                global $post;
               extract( $args, EXTR_SKIP );
                $number = $instance['numbers'];
               // Thanks to David Gil (github.com/dgilperez) for the custom-post-type fix

               global $wpdb;
                $month = get_the_time('m');
                $day = get_the_time('j');
                $id = get_the_ID() ;
                $sql = "select ID, year(post_date) as h_year, post_title FROM
                        $wpdb->posts WHERE post_password = '' AND post_type = 'post' AND post_status = 'publish'
                        AND month(post_date)='$month' AND day(post_date)='$day' AND ID != '$id'
                        order by post_date LIMIT ". $number;
                $history_post = $wpdb->get_results($sql);

               if ( count( $history_post ) == 0 )
                    return;

               echo $before_widget;

               $title = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
               if ( !empty( $title ) )
                    echo $before_title . $title . $after_title; ?>
               <ul><?php
                    foreach ( $history_post as $related_post ) : ?>
                         <li>
                              <?php echo $related_post->h_year; echo ':&nbsp;&nbsp'; ?><a class="history-post" href="<?php echo get_permalink( $related_post->ID ); ?>">
                                   <?php echo ( $related_post->post_title ); ?>
                              </a>
                         </li><?php
                    endforeach; ?>
               </ul><?php

               echo $after_widget;

          }

     }

     //add_action( 'widgets_init', create_function( '', 'return register_widget( "APIP_Widget_Sameday_Post" );' ) );

class APIP_Widget_Recent extends WP_Widget {


          public function __construct() {

               $widget_ops = array(
                    'classname' => 'APIP_Widget_Recent',
                    'description' => '显示最近更新的文章'
               );
               parent::__construct( 'APIP_Widget_Recent', '最近更新', $widget_ops );
          }

          public function form( $instance ) {

               $instance = wp_parse_args( (array) $instance, array( 'title' => __( 'Recent Modify' , 'prokuso'), 'numbers' => '5' ) );
               $title = $instance['title'];
               $numbers = $instance['numbers'];
               ?>
               <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                         <?php _e( 'Title' , 'prokuso'); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo attribute_escape( $title ); ?>" />
                    </label>
               </p>
               <p>
                    <label for="<?php echo $this->get_field_id( 'numbers' ); ?>">
                         <?php _e( 'How many posts to show' , 'prokuso'); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'numbers' ); ?>" name="<?php echo $this->get_field_name( 'numbers' ); ?>" type="text" value="<?php echo attribute_escape( $numbers ); ?>" />
                    </label>
               </p><?php

          }


          public function update( $new_instance, $old_instance ) {
               $instance = $old_instance;
               $instance['title'] = $new_instance['title'];
               $instance['numbers'] = $new_instance['numbers'];
               return $instance;
          }


          public function widget( $args, $instance ) {
                extract( $args, EXTR_SKIP );
                $number = $instance['numbers'] ? : 5 ;
                $related_posts = get_posts( array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'orderby' => 'modified',
                    'order' => 'DESC',
                    'numberposts' => $number
               ) );

               if ( count( $related_posts ) == 0 )
                    return;

               echo $before_widget;

               $title = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
               if ( !empty( $title ) )
                    echo $before_title . $title . $after_title; ?>
               <ul><?php
                    foreach ( $related_posts as $related_post ) : ?>
                         <li>
                              <a class="history-post" href="<?php echo get_permalink( $related_post->ID ); ?>">
                                   <?php echo apply_filters( 'prokuso', $related_post->post_title, $related_post, $instance ); ?>
                              </a>
                         </li><?php
                    endforeach; ?>
               </ul><?php

               echo $after_widget;

          }

     }

 //add_action( 'widgets_init', create_function( '', 'return register_widget( "APIP_Widget_Recent" );' ) );

/**
 * Custom Meta class
 *
 * Displays log in/out, RSS feed links, etc.
 *
 * @since 2.8.0
 */
class APIP_Widget_Meta extends WP_Widget {

    public function __construct() {
        $widget_ops = array('classname' => 'APIP_Widget_Meta', 'description' => __( "Some basic info.") );
        parent::__construct('APIP_Widget_Meta', __('BasicInfo'), $widget_ops);
    }

    public function widget( $args, $instance ) {

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters( 'widget_title', empty($instance['title']) ? __( 'BasicInfo' ) : $instance['title'], $instance, $this->id_base );

        echo $args['before_widget'];
        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
?>
            <ul>
            <?php $all_posts = get_posts( array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'order' => 'ASC',
                    'numberposts' => 5
               ) );
               $spam_count = get_option( 'akismet_spam_count' );
               $spam_count += 9334 ;
               $comments_count = wp_count_comments();
               if ( $all_posts )
                $firstpost = $all_posts[0];
               if ( $firstpost ) {
                $days_ago = round( ( date('U') - get_the_time('U', $firstpost->ID) ) / ( 60*60*24 ) );
                } ?>
            <li><?php $count_posts = wp_count_posts(); $published_posts = $count_posts->publish; echo number_format_i18n($published_posts); ?> Posts</li>
            <!--? php echo number_format_i18n($comments_count->approved); ?-->
            <!--?php $count_tags = wp_count_terms('post_tag'); echo number_format_i18n($count_tags); ?-->
            <!--?php echo number_format_i18n($spam_count); ?-->
            <li>Survived for <?php echo number_format_i18n($days_ago); ?> days </li>
            <li class = "custom-login"><?php wp_loginout(); ?></li>
            </ul>
<?php
        echo $args['after_widget'];
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
        $title = strip_tags($instance['title']);
?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
    }
}

//add_action( 'widgets_init', create_function( '', 'return register_widget( "APIP_Widget_Meta" );' ) );

/**
 * 自定义显示当前页面上filter用class
 *
 * 显示指定的自定义filter列表
 *
 * @since APIP 1.11
 */
class APIP_Widget_Hook_List extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'APIP_Widget_Hook_List',
			'description' => '显示当前页面的钩子列表',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'APIP_Widget_Hook_List', '钩子列表', $widget_ops );
	}

	public function widget( $args, $instance ) {
		$title =  empty($instance['title']) ? '钩子列表' : $instance['title'];
        $filters = explode(',',$instance['hooks']);
        if ( !is_super_admin() )
            return;
        if ( empty( $filters ) )
            return;
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
        $content = '<ul>';
        $session = '';
        $line = '';
        foreach ($filters as $filter){
            $tags = $GLOBALS['wp_filter'][ $filter ];
            if (empty($tags))
                continue;
            $session = sprintf('<li><strong>%s</strong></li>', $filter );
            foreach ( $tags as $priority => $tag )
            {
                foreach ( $tag as $identifier => $function )
                {
                    if ( is_string( $function['function'] ) )
                    {
                        $line = sprintf('<li>&nbsp;&nbsp;%1$s</li>', $function['function'] );
                    }
                    else
                    {
                        $cname = is_string($function['function'][0]) ? $function['function'][0] : get_class($function['function'][0]);
                        $line = sprintf('<li>&nbsp;&nbsp;%1$s::%2$s</li>', $cname, $function['function'][1]);
                    }
                    $session .= $line;
                }
            }
            $content .= $session;
        };
        $content .= '</ul>';
        echo $content;

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['hooks'] = sanitize_text_field( $new_instance['hooks'] );

		return $instance;
	}


	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'hooks' => 'the_content,wp_enqueue_scripts,wp_head,wp_footer' ) );
		$title = sanitize_text_field( $instance['title'] );
        $hooks = sanitize_text_field( $instance['hooks'] );
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
            <p><label for="<?php echo $this->get_field_id('hooks'); ?>"><?php echo '要查看的hook,半角逗号分隔:'; ?></label> <input class="widefat" id="<?php echo $this->get_field_id('hooks'); ?>" name="<?php echo $this->get_field_name('hooks'); ?>" type="text" value="<?php echo esc_attr($hooks); ?>" /></p>
<?php
	}
}

function apip_widget_register() {
     $names = array(
          "APIP_Widget_Related",
          "APIP_Widget_Sameday_Post",
          "APIP_Widget_Recent",
          "APIP_Widget_Meta",
          "APIP_Widget_Hook_List",
     );
     foreach ($names as $wd) {
          register_widget($wd);
     }
}

//add_action( 'widgets_init', create_function( '', 'return register_widget( "APIP_Widget_Hook_List" );' ) );
add_action( 'widgets_init', 'apip_widget_register' );
add_action( 'widgets_init', 'apip_widget_unregister', 99 );

function apip_widget_unregister() {
/*
     WP_Widget_Pages = Pages Widget
     WP_Widget_Calendar = Calendar Widget
     WP_Widget_Archives = Archives Widget
     WP_Widget_Links = Links Widget
     WP_Widget_Media_Audio = Audio Player Media Widget
     WP_Widget_Media_Image = Image Media Widget
     WP_Widget_Media_Video = Video Media Widget
     WP_Widget_Media_Gallery = Gallery Media Widget
     WP_Widget_Meta = Meta Widget
     WP_Widget_Search = Search Widget
     WP_Widget_Text = Text Widget
     WP_Widget_Categories = Categories Widget
     WP_Widget_Recent_Posts = Recent Posts Widget
     WP_Widget_Recent_Comments = Recent Comments Widget
     WP_Widget_RSS = RSS Widget
     WP_Widget_Tag_Cloud = Tag Cloud Widget
     WP_Nav_Menu_Widget = Menus Widget
     WP_Widget_Custom_HTML = Custom HTML Widget
    */
    $names = array(
         "WP_Widget_Pages",
         "WP_Widget_Archives",
         "WP_Widget_Links",
         "WP_Widget_Media_Audio",
         "WP_Widget_Media_Image",
         "WP_Widget_Media_Video",
         "WP_Widget_Media_Gallery",
         "WP_Widget_Meta",
         "WP_Widget_Text",
         "WP_Widget_Categories",
         "WP_Widget_Recent_Posts",
         "WP_Widget_RSS",
         "WP_Widget_Tag_Cloud",
         "WP_Widget_Custom_HTML",
         /*NGGallerys */
         "C_Widget_Gallery",
         "C_Widget_MediaRSS",
         "C_Widget_Slideshow",
    );
    foreach ($names as $wd) {
          unregister_widget($wd);
     }
}