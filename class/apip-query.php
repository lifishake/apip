<?php

/*
 * 作用: 暂存上一次查询的结果集
 * 来源: 自产
 * URL:  
*/
class Apip_Query{
    private $initiated = false;     //是否初始化
    private $post_ids = array();    //保存的文章号
    private $desc = "";             //保存的描述
    //是否初始化
    public function isloaded(){
        return true == $this->initiated;
    }
    //初始化
    public function init() {
        if ( !$this->initiated ){
            $this->reset();
            $this->initiated = true;
        }
    }
    //重置
    public function reset() {
        $this->post_ids=array();
        $this->desc = "";
    }
    public function get_title() {
        return $this->desc;
    }
    //保存查询结果
    public function keep_query() {
        if (is_search()||is_archive()) {
            global $wp_query;
            //if ($wp_query->post_count == 1 && $wp_query->max_num_pages == 1)
            //    return;
            $desc = "";
            //$this->post_ids = wp_list_pluck( $wp_query->posts, 'ID' );
            if ( is_search() ){
                $desc = "搜索结果:" . the_search_query() ;
            }
            else if ( is_category() ){
                $desc = "分类:" . single_cat_title( '', false );
            }
            else if ( is_tag() ){
                $desc = "标签:" . single_tag_title( '', false );
            }
            else if ( is_year() ) {
                $desc = "年:" . get_the_date('Y') ;
            }
            else if ( is_month() ) {
                $desc = "月:" . get_the_date('F Y');
            }
            else if ( is_day() ) {
                $desc = "日:" . get_the_date(get_option('date_format'));
            }
            else{
                $desc = "";
            }
            if ( "" === $desc ){
                $this->reset();
            }
            if ( $desc != $this->desc ){
                $this->desc = $desc;
                $vars = $wp_query->query_vars;
                $vars['posts_per_page'] = 9999;
                $myquery = new WP_Query( $vars );
                if ($myquery->post_count == 1 && $myquery->max_num_pages == 1)
                    return;
                $this->post_ids = wp_list_pluck( $myquery->posts, 'ID' );
            }
        }
        //非查询页面时重置
        else if (is_404()||is_home()||is_front_page()) {
            $this->reset();
        }
        //is_singular时不保存也不重置
    }
    //取得上一条和下一条
    public function get_neighbor( $ID ){
        $ret = array();
        $ret['got'] = FALSE;
        $ret['prev'] = -1;
        $ret['next'] = -1;
        $count = count($this->post_ids);
        if ( 0 == $count ){
            $this->reset();
            return $ret ;
        }
        $current_pos = array_search($ID, $this->post_ids) ;
        if ( FALSE === $current_pos ){
            $this->reset();
            return $ret ;
        }
        $ret['got'] = TRUE;
        if ( $current_pos != 0 ) {
            $ret['prev'] = $this->post_ids[$current_pos - 1];
        }
        if ( $current_pos < $count - 1 ){
            $ret['next'] = $this->post_ids[$current_pos + 1];
        }
        return $ret;
    }
}
