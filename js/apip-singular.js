/**
 * @file apip-singular.js
 * @brief Post和Page页面用到的js
 * @since 1.0.0
 * @version 1.41.0
 * @date 2026-02-10
 * @see apip_scripts()
 */

/**
 * @brief 跳转到url=#name格式的页面位置。配合quicktags中的mysup使用。
 * @see apip_sup_detail()
 * @date 2026-02-10
 * @version 1.41.0
 * @since 1.33.2
*/
jQuery(document).ready(function($) {
  $('a[href^="#"]').on('click', function(e) {
    e.preventDefault();
    var target = this.hash;
    if(!document.getElementsByName(target.substring(1)).length) {
      return;
    }
    $jmp = document.getElementsByName(target.substring(1))[0];
    var theTop = $jmp.offsetTop ? $jmp.offsetTop : $jmp.offsetParent.offsetTop; 
    $('html,body').animate({scrollTop: theTop - 10},1000);
  });
});