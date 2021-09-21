<?php

/**
 * 简陋,由工具做成,少量修改
 * 工具URL: http://wpsettingsapi.jeroensormani.com/
*/

add_action( 'admin_menu', 'apip_add_admin_menu' );
add_action( 'admin_init', 'apip_settings_init' );


function apip_add_admin_menu(  ) {
  $myicon = "PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHhtbDpzcGFjZT0icHJlc2VydmUiIHZpZXdCb3g9IjAgMCAxMDAgMTAwIiB5PSIwIiB4PSIwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGlkPSLlnJblsaRfMSIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iMTI4cHgiIGhlaWdodD0iMTI4cHgiIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiBzdHlsZT0id2lkdGg6MTAwJTtoZWlnaHQ6MTAwJTtiYWNrZ3JvdW5kLWNvbG9yOnJnYigyNTUsIDI1NSwgMjU1KTthbmltYXRpb24tcGxheS1zdGF0ZTpwYXVzZWQiID48ZyBjbGFzcz0ibGRsLXNjYWxlIiBzdHlsZT0idHJhbnNmb3JtLW9yaWdpbjo1MCUgNTAlIDBweDt0cmFuc2Zvcm06cm90YXRlKDBkZWcpIHNjYWxlKDAuOCk7YW5pbWF0aW9uLXBsYXktc3RhdGU6cGF1c2VkIiA+PHBhdGggZD0iTTQyLjQgMTBoMzYuNHYxNC4zSDQyLjR6IiBmaWxsPSIjMzMzIiBzdHlsZT0iZmlsbDpyZ2IoNTEsIDUxLCA1MSk7YW5pbWF0aW9uLXBsYXktc3RhdGU6cGF1c2VkIiA+PC9wYXRoPg0KPHBhdGggZmlsbD0iIzMzMyIgZD0iTTQxLjcgNDguM2wtMTIuNSA4LjNjLS40LjMtLjguNi0xLjMuOS4xLjEuMi4xLjMuMi4zLjIuNS40LjguNi40LjMuNy42IDEuMS45LjMuMi41LjUuOC43LjMuMy43LjYgMSAxIC4zLjIuNS41LjcuOC4zLjMuNi43IDEgMSAuMi4zLjUuNS43LjguMy40LjYuNy45IDEuMS4yLjMuNC41LjcuOGwuOSAxLjJjLjIuMy40LjUuNi44LjMuNC42LjkuOSAxLjMuMi4yLjMuNS41LjcuNC43LjggMS4zIDEuMiAyIDAgLjEuMS4yLjEuMi40LjcuNyAxLjMgMSAyIC4xLjMuMi41LjMuOC4yLjUuNCAxIC42IDEuNC4xLjMuMi42LjQuOS4yLjUuMy45LjUgMS40LjEuMy4yLjcuMyAxIC4xLjQuMy45LjQgMS4zLjEuMy4yLjcuMyAxIC4xLjQuMi45LjMgMS40LjEuMy4yLjcuMiAxIC4xLjUuMi45LjMgMS40LjEuMy4xLjcuMiAxIC4xLjUuMSAxIC4yIDEuNCAwIC4zLjEuNy4xIDF2LjNjMS41LS41IDMtMS4yIDQuMy0yLjFsMTQtOS4zYzAtLjItLjEtLjMtLjEtLjUtLjEtLjItLjEtLjUtLjItLjdsLS4zLTEuMmMwLS4yLS4xLS41LS4xLS43LS4xLS40LS4xLS45LS4yLTEuMyAwLS4yLS4xLS40LS4xLS42LS4xLS42LS4xLTEuMy0uMS0xLjkgMC0uNyAwLTEuMy4xLTIgMC0uMi4xLS40LjEtLjYgMC0uNC4xLS45LjItMS4zIDAtLjMuMS0uNS4yLS43LjEtLjQuMS0uOC4yLTEuMS4xLS4zLjEtLjUuMi0uOC4xLS40LjItLjcuMy0xLjEuMS0uMy4yLS41LjMtLjguMS0uMy4yLS43LjQtMSAuMS0uMy4yLS41LjQtLjguMS0uMy4zLS42LjUtLjkuMS0uMi4zLS41LjQtLjcuMi0uMy4zLS42LjUtLjkuMi0uMi4zLS41LjUtLjcuMi0uMy40LS42LjYtLjguMi0uMi4zLS40LjUtLjcuMi0uMy40LS41LjctLjhsLjYtLjYuNy0uNy42LS42Yy4zLS4yLjUtLjUuOC0uNy4yLS4yLjUtLjQuNy0uNS4zLS4yLjUtLjQuOC0uNi4yLS4yLjUtLjMuNy0uNS4zLS4yLjYtLjQuOS0uNS4zLS4yLjUtLjMuOC0uNC4zLS4yLjYtLjMuOS0uNS4zLS4xLjUtLjMuOC0uNC4zLS4xLjYtLjMuOS0uNGwuOS0uM2MuMSAwIC4zLS4xLjQtLjFWMjcuNEg0Mi40VjQ3YzAgLjYtLjIgMS4xLS43IDEuM3oiIHN0eWxlPSJmaWxsOnJnYig1MSwgNTEsIDUxKTthbmltYXRpb24tcGxheS1zdGF0ZTpwYXVzZWQiID48L3BhdGg+DQo8cGF0aCBmaWxsPSIjMzMzIiBkPSJNNzguOSA1NC42di0zLjJjLS4zLjEtLjUuMi0uOC4zbC0uNi4zYy0uNC4yLS43LjMtMSAuNS0uMS4xLS4zLjEtLjQuMi0uNS4zLS45LjYtMS40LjktLjEuMS0uMi4yLS4zLjItLjMuMi0uNy41LTEgLjctLjIuMS0uMy4zLS41LjQtLjMuMi0uNS40LS44LjdsLS41LjUtLjcuNy0uNS41Yy0uMi4yLS40LjUtLjYuNy0uMS4yLS4zLjQtLjQuNi0uMi4yLS40LjUtLjUuNy0uMS4yLS4zLjQtLjQuNi0uMi4zLS4zLjUtLjUuOGwtLjMuNmMtLjEuMy0uMy41LS40LjgtLjEuMi0uMi40LS4zLjctLjEuMy0uMi42LS4zLjgtLjEuMi0uMi40LS4yLjdsLS4zLjljLS4xLjItLjEuNC0uMi43LS4xLjMtLjEuNi0uMiAxIDAgLjItLjEuNC0uMS42LS4xLjQtLjEuNy0uMSAxLjEgMCAuMiAwIC40LS4xLjUgMCAuNi0uMSAxLjEtLjEgMS43IDAgLjUgMCAxLjEuMSAxLjZ2LjRjMCAuNC4xLjguMiAxLjIgMCAuMSAwIC4zLjEuNC4xLjUuMi45LjMgMS40bDIuMS0xLjRjNi43LTQuNCAxMC43LTExLjggMTAuNy0xOS44eiIgc3R5bGU9ImZpbGw6cmdiKDUxLCA1MSwgNTEpO2FuaW1hdGlvbi1wbGF5LXN0YXRlOnBhdXNlZCIgPjwvcGF0aD4NCjxwYXRoIGZpbGw9IiMzMzMiIGQ9Ik0yNC4yIDgxLjljMy45IDUuOSAxMSA4LjkgMTcuOSA3Ljl2LS42YzAtLjQtLjEtLjctLjEtMS4xIDAtLjQtLjEtLjgtLjItMS4zIDAtLjMtLjEtLjctLjItMS0uMS0uNC0uMS0uOC0uMi0xLjItLjEtLjMtLjEtLjctLjItMWwtLjMtMS4yYy0uMS0uMy0uMi0uNy0uMy0xLS4xLS40LS4yLS44LS40LTEuMmwtLjMtLjljLS4xLS40LS4zLS44LS41LTEuM2wtLjMtLjljLS4yLS41LS40LS45LS42LTEuNC0uMS0uMi0uMi0uNS0uMy0uNy0uMy0uNy0uNi0xLjMtMS0xLjkgMCAwIDAtLjEtLjEtLjEtLjQtLjctLjctMS4zLTEuMS0xLjktLjEtLjItLjMtLjQtLjQtLjYtLjMtLjQtLjUtLjgtLjgtMS4zLS4yLS4yLS40LS41LS41LS43LS4zLS40LS41LS43LS44LTEuMS0uMi0uMy0uNC0uNS0uNi0uNy0uMy0uMy0uNi0uNy0uOS0xLS4yLS4yLS40LS41LS43LS43bC0uOS0uOS0uNy0uNy0uOS0uOWMtLjMtLjItLjUtLjUtLjgtLjctLjMtLjMtLjctLjYtMS0uOC0uMy0uMi0uNS0uNC0uOC0uNi0uMi0uMS0uMy0uMi0uNS0uNC01LjQgNS45LTYuMSAxNS0xLjUgMjEuOXoiIHN0eWxlPSJmaWxsOnJnYig1MSwgNTEsIDUxKTthbmltYXRpb24tcGxheS1zdGF0ZTpwYXVzZWQiID48L3BhdGg+DQo8bWV0YWRhdGEgeG1sbnM6ZD0iaHR0cHM6Ly9sb2FkaW5nLmlvL3N0b2NrLyIgc3R5bGU9ImFuaW1hdGlvbi1wbGF5LXN0YXRlOnBhdXNlZCIgPjxkOm5hbWUgc3R5bGU9ImFuaW1hdGlvbi1wbGF5LXN0YXRlOnBhdXNlZCIgPnNvY2tzPC9kOm5hbWU+DQoNCg0KPGQ6dGFncyBzdHlsZT0iYW5pbWF0aW9uLXBsYXktc3RhdGU6cGF1c2VkIiA+c29ja3Msc3RvY2tpbmdzLGJyZWVjaGVzLHdlYXI8L2Q6dGFncz4NCg0KDQo8ZDpsaWNlbnNlIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTpwYXVzZWQiID5ieTwvZDpsaWNlbnNlPg0KDQoNCjxkOnNsdWcgc3R5bGU9ImFuaW1hdGlvbi1wbGF5LXN0YXRlOnBhdXNlZCIgPjZidDF3bTwvZDpzbHVnPjwvbWV0YWRhdGE+PC9nPjwhLS0gZ2VuZXJhdGVkIGJ5IGh0dHBzOi8vbG9hZGluZy5pby8gLS0+PC9zdmc+";
  add_menu_page( 'APIP设置', 'APIP设置', 'manage_options', __FILE__, 'apip_options_page', 'data:image/svg+xml;base64,' . $myicon );
}
/*
支持的功能列表
01. 改进的功能摘要

*/
function apip_settings_init(  ) {

  register_setting( 'apip_option_tab', 'apip_settings' );

  add_settings_section(
  'apip_pluginPage_section',
  'APIP可配置项',
  'apip_settings_section_callback',
  'apip_option_tab'
  );

  add_settings_section(
  'apip_cleaner_section',
  'APIP维护操作',
  'apip_settings_section_callback',
  'apip_cleaner_tab'
  );

  add_settings_section(
  'apip_extra_section',
  'APIP试验台',
  'apip_extra_section_callback',
  'apip_extra_tab'
  );

  //01
  add_settings_field(
  'better_excerpt',
  '颜色设定',
  'apip_color_setting_render',
  'apip_option_tab',
  'apip_pluginPage_section'
  );

  //02
  add_settings_field(
  'advanced_writer_settings',
  '高级编辑选项',
  'apip_advanced_writer_settings_render',
  'apip_option_tab',
  'apip_pluginPage_section'
  );
  //03
  add_settings_field(
  'header_description',
  '文字相关设定',
  'apip_text_content_render',
  'apip_option_tab',
  'apip_pluginPage_section'
  );
  //04
  add_settings_field(
  'local_gravatar',
  'GFW相关',
  'apip_anti_gfw_render',
  'apip_option_tab',
  'apip_pluginPage_section'
  );
  //05
  add_settings_field(
  'blocked_commenters',
  '留言管理',
  'apip_blocked_commenters_render',
  'apip_option_tab',
  'apip_pluginPage_section'
  );
  //06
  add_settings_field(
  'social_share_settings',
  '社会化分享',
  'apip_social_share_render',
  'apip_option_tab',
  'apip_pluginPage_section'
  );

  //07
  add_settings_field(
  'shortcode_settings',
  '自定义的SHORTCODE',
  'apip_shortcodes_render',
  'apip_option_tab',
  'apip_pluginPage_section'

  );
  //08
  add_settings_field(
  'heavy_tools_settings',
  '比较复杂的功能',
  'apip_heavy_tools_render',
  'apip_option_tab',
  'apip_pluginPage_section'
  );
  //99
  add_settings_field(
  'local_definition_count',
  '自定义widget',
  'apip_local_widgets_render',
  'apip_option_tab',
  'apip_pluginPage_section'
  );
   add_settings_field(
  'check_rubbish_options',
  '检查后台option表',
  'apip_check_rubbish_options_field_render',
  'apip_cleaner_tab',
  'apip_cleaner_section'
  );
    //-1
   add_settings_field(
  'test_field_settings',
  '保留功能',
  'apip_test_field_render',
  'apip_extra_tab',
  'apip_extra_section'
  );
  /*
  add_settings_field(
  'apip_text_field_0',
  __( 'Settings field description', 'pewae.com' ),
  'apip_text_field_0_render',
  'pluginPage',
  'apip_pluginPage_section'
  );

  add_settings_field(
  'apip_radio_field_1',
  __( 'Settings field description', 'pewae.com' ),
  'apip_radio_field_1_render',
  'pluginPage',
  'apip_pluginPage_section'
  );


}


function apip_text_field_0_render(  ) {

  $options = get_option( 'apip_settings' );
  ?>
  <input type='text' name='apip_settings[apip_text_field_0]' value='<?php echo $options['apip_text_field_0']; ?>'>
  <?php

}



  add_settings_field(
  'apip_radio_field_1',
  __( 'Settings field description', 'apip' ),
  'apip_radio_field_1_render',
  'apip_option_group',
  'apip_pluginPage_section'
  );

  add_settings_field(
  'apip_textarea_field_2',
  __( 'Settings field description', 'apip' ),
  'apip_textarea_field_2_render',
  'apip_option_group',
  'apip_pluginPage_section'
  );*/


}

function apip_color_setting_render(  ) {
  //01
  $options = get_option( 'apip_settings' );
  ?>
  <span> 自定义链接颜色：</span>
  <input type= 'text' name='apip_settings[link_color]' id='link-color'  value='<?php if ( isset( $options['link_color'] ) ) echo $options['link_color']; else echo " #1A5F99"; ?>' /><br />
  <span> 自定义文字颜色：</span>
  <input type= 'text' name='apip_settings[font_color]' id='font-color'  value='<?php if ( isset( $options['font_color'] ) ) echo $options['font_color']; else echo " #0A161F"; ?>' /><br />
  <span> 自定义边框颜色：</span>
  <input type= 'text' name='apip_settings[border_color]' id='border-color'  value='<?php if ( isset( $options['border_color'] ) ) echo $options['border_color']; else echo " #8A8988"; ?>' /><br />
  <span> 自定义背景颜色：</span>
  <input type= 'text' name='apip_settings[bg_color]' id='bg-color'  value='<?php if ( isset( $options['bg_color'] ) ) echo $options['bg_color']; else echo " #ECE5DF"; ?>' /><br />
  <span> 自定义标签云链接颜色：</span>
  <input type= 'text' name='apip_settings[tagcloud_link_color]' id='tagcloud-link-color'  value='<?php if ( isset( $options['tagcloud_link_color'] ) ) echo $options['tagcloud_link_color']; else echo " #ea3382"; ?>' /><br />
  <span> 自定义标签云背景颜色：</span>
  <input type= 'text' name='apip_settings[tagcloud_bg_color]' id='tagcloud-bg-color'  value='<?php if ( isset( $options['tagcloud_bg_color'] ) ) echo $options['tagcloud_bg_color']; else echo " #9eccef"; ?>' /><br />
  <?php
}

function apip_advanced_writer_settings_render()
{
  //02
  $options = get_option( 'apip_settings' );
  ?>
  <span>    禁止自动保存：</span>
  <input type='checkbox' name='apip_settings[auto_save_disabled]' <?php checked( $options['auto_save_disabled'], 1 ); ?> value='1'/><br />
  <span>    显示AdminBar：</span>
  <input type='checkbox' name='apip_settings[show_admin_bar]' <?php checked( $options['show_admin_bar'], 1 ); ?> value='1'/><br />
  <span>    前台显示中文：</span>
  <input type='checkbox' name='apip_settings[forground_chinese]' <?php checked( $options['forground_chinese'], 1 ); ?> value='1'/><br />
  <span>    屏蔽OpenSans字体：</span>
  <input type='checkbox' name='apip_settings[block_open_sans]' <?php checked( $options['block_open_sans'], 1 ); ?> value='1'/><br />
  <span>    默认留言widget中屏蔽作者：</span>
  <input type='checkbox' name='apip_settings[show_author_comment]' <?php checked( $options['show_author_comment'], 1 ); ?> value='1'/><br />
  <span>    搜索结果只有一条时直接跳转：</span>
  <input type='checkbox' name='apip_settings[redirect_if_single]' <?php checked( $options['redirect_if_single'], 1 ); ?> value='1'/><br />
  <span>    保护wp_comments.php：</span>
  <input type='checkbox' name='apip_settings[protect_comment_php]' <?php checked( $options['protect_comment_php'], 1 ); ?> value='1'/><br />
  <span>    搜索结果中屏蔽page：</span>
  <input type='checkbox' name='apip_settings[search_without_page]' <?php checked( $options['search_without_page'], 1 ); ?> value='1'/><br />
  <span>    外链转内链：</span>
  <input type='checkbox' name='apip_settings[redirect_external_link]' <?php checked( $options['redirect_external_link'], 1 ); ?> value='1'/><br />
  <span>    移除后台界面的WP版本升级提示：</span>
  <input type='checkbox' name='apip_settings[remove_core_updates]' <?php checked( $options['remove_core_updates'], 1 ); ?> value='1'/><br />
  <?php

}

function apip_text_content_render(  ) {
  //03
  $options = get_option( 'apip_settings' );
  ?>
  <input type='checkbox' name='apip_settings[better_excerpt]' <?php checked( $options['better_excerpt'], 1 ); ?> value='1'/>
  <span>    摘要长度：</span>
  <input type='text' name='apip_settings[excerpt_length]' size='5' value='<?php echo $options['excerpt_length']; ?>'/><br />
  <span>    结尾字符：</span>
  <input type='text' name='apip_settings[excerpt_ellipsis]' size='10' value='<?php echo $options['excerpt_ellipsis']; ?>'/><br />
  <input type='checkbox' name='apip_settings[header_description]' <?php checked( $options['header_description'], 1 ); ?> value='1'/>
  <span>    网站描述（留空则使用网站副标题）：</span>
  <input type='text' name='apip_settings[hd_home_text]' value='<?php echo htmlspecialchars(stripslashes($options['hd_home_text'])); ?>' /><br />
  <span>    网站标签（【,】分割（留空则使用10个最常用标签）：</span>
  <input type='text' name='apip_settings[hd_home_keyword]' value='<?php echo htmlspecialchars(stripslashes($options['hd_home_keyword'])); ?>' />
  <?php
}

function apip_anti_gfw_render()
{
  //04
  $options = get_option( 'apip_settings' );
  ?>
  <span>    gravatar使用本地缓存(不使用则替换网址)：</span>
  <input type='checkbox' name='apip_settings[local_gravatar]' <?php checked( $options['local_gravatar'], 1 ); ?> value='1'/><br />
  <span>    替换emojie地址：</span>
  <input type='checkbox' name='apip_settings[replace_emoji]' <?php checked( $options['replace_emoji'], 1 ); ?> value='1'/><br />
  <?php
}

function apip_blocked_commenters_render()
{
  //05
  $options = get_option( 'apip_settings' );
  ?>
  <span>    垃圾关键字列表：</span><br />
    <textarea rows='4' cols='40' name='apip_settings[blocked_commenters]' ><?php echo htmlspecialchars(stripslashes($options['blocked_commenters'])); ?></textarea>
  <?php
}

function apip_social_share_render()
{
  //06
  $options = get_option( 'apip_settings' );
  ?>
  <span>    是否允许(css+js)：</span>
  <input type='checkbox' name='apip_settings[social_share_enable]' <?php checked( $options['social_share_enable'], 1 ); ?> value='1'>
  <span>    Twitter：</span>
  <input type='checkbox' name='apip_settings[social_share_twitter]' <?php checked( $options['social_share_twitter'], 1 ); ?> value='1'>
  <span>    新浪微博：</span>
  <input type='checkbox' name='apip_settings[social_share_sina]' <?php checked( $options['social_share_sina'], 1 ); ?> value='1'>
  <span>    facebook：</span>
  <input type='checkbox' name='apip_settings[social_share_facebook]' <?php checked( $options['social_share_facebook'], 1 ); ?> value='1'>
  <?php
}

function apip_shortcodes_render()
{
  //07
  $options = get_option( 'apip_settings' );
  ?>
  <span>    使用自定义TagCloud页（css）<i>CODE:mytagcloud</i>：</span>
  <input type='checkbox' name='apip_settings[apip_tagcloud_enable]' <?php checked( $options['apip_tagcloud_enable'], 1 ); ?> value='1'><br />
  <span>    使用自定义Link页（css）<i>CODE:mylink</i>：</span>
  <input type='checkbox' name='apip_settings[apip_link_enable]' <?php checked( $options['apip_link_enable'], 1 ); ?> value='1'><br />
    <span>    使用自定义归档页（css+js）<i>CODE:myarchive</i>：</span>
  <input type='checkbox' name='apip_settings[apip_archive_enable]' <?php checked( $options['apip_archive_enable'], 1 ); ?> value='1'><br />
  <?php
}

function apip_heavy_tools_render()
{
  //08
  $options = get_option( 'apip_settings' );
  ?>
  <span>    使用code_heighlight(js+css+content_filter)：</span>
  <input type='checkbox' name='apip_settings[apip_codehighlight_enable]' <?php checked( $options['apip_codehighlight_enable'], 1 ); ?> value='1'><br />
  <span>    使用lazy_load(js+content_filter)：</span>
  <input type='checkbox' name='apip_settings[apip_lazyload_enable]' <?php checked( $options['apip_lazyload_enable'], 1 ); ?> value='1'><br />
    <span>    查询、归档页进入单页后，“上一页”、“下一页”在该范围内跳转(js)：</span>
  <input type='checkbox' name='apip_settings[range_jump_enable]' <?php checked( $options['range_jump_enable'], 1 ); ?> value='1'><br />
  <span>    开启邮件回复(js)：</span>
  <input type='checkbox' name='apip_settings[notify_comment_reply]' <?php checked( $options['notify_comment_reply'], 1 ); ?> value='1'/><br />
  <span>    omdb API KEY<i>CODE:myimdb</i>：</span>
  <input type='text' name='apip_settings[omdb_key]' size='64' value='<?php echo $options['omdb_key']; ?>'/><br />
  <span>    gaintbomb API KEY<i>CODE:mygame</i>：</span>
  <input type='text' name='apip_settings[gaintbomb_key]' size='64' value='<?php echo $options['gaintbomb_key']; ?>'/><br />
  <span>    和风天气 API KEY：</span>
  <input type='text' name='apip_settings[heweather_key]' size='64' value='<?php echo $options['heweather_key']; ?>'/><br />
  <span>   使用留言前答题功能： </span>
  <input type='checkbox' name='apip_settings[apip_commentquiz_enable]' <?php checked( $options['apip_commentquiz_enable'], 1 ); ?> value='1'><br />
  <?php
}

function apip_local_widgets_render()
{
  //99
  $options = get_option( 'apip_settings' );
  ?>
  <span>    使用自定义widget：</span>
  <input type='checkbox' name='apip_settings[local_widget_enable]' <?php checked( $options['local_widget_enable'], 1 ); ?> value='1'><br />
  <span>    默认条数：</span>
  <input type='text' size = '5' name='apip_settings[local_definition_count]' value='<?php echo htmlspecialchars(stripslashes($options['local_definition_count'])); ?>' >
  <?php
}

function apip_settings_section_callback(  ) {

  echo '<span>一些基本设定项目，抄自多个插件</span>';

}

function apip_extra_section_callback() {
    echo '<span>一些基本设定项目，抄自多个插件</span>';
}

function apip_options_page(  ) {
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = $_GET[ 'tab' ];
        } else {
            $active_tab = 'tab_option';
        }
        ?>
    <div id="apip_page_content" class="wrap apip-option" >
  <h1><span>A</span>ll <span>P</span>lugins <span>I</span>n <span>P</span>ewae</h1>
  <div class="description">This is description of the page.</div>
            <?php settings_errors(); ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo __FILE__;?>&tab=tab_option" class="nav-tab <?php echo $active_tab == 'tab_option' ? 'nav-tab-active' : ''; ?>">基本功能</a>
                <a href="?page=<?php echo __FILE__;?>&tab=tab_cleaner" class="nav-tab <?php echo $active_tab == 'tab_cleaner' ? 'nav-tab-active' : ''; ?>">清理工具</a>
                <a href="?page=<?php echo __FILE__;?>&tab=tab_extra" class="nav-tab <?php echo $active_tab == 'tab_extra' ? 'nav-tab-active' : ''; ?>">实验台</a>
            </h2>
     <form action='options.php' method='post'>
  <?php
  switch($active_tab) {
      case 'tab_option':
      default:
        settings_fields( 'apip_option_tab' );
        do_settings_sections( 'apip_option_tab' );
        submit_button();
        break;
      case 'tab_cleaner':
        settings_fields( 'apip_cleaner_tab' );
        do_settings_sections( 'apip_cleaner_tab' );
        break;
      case 'tab_extra':
        settings_fields( 'apip_extra_tab' );
        do_settings_sections( 'apip_extra_tab' );
        break;
  }
  /*
  if( $active_tab == 'tab_option' ) {
      settings_fields( 'apip_option_tab' );
      do_settings_sections( 'apip_option_tab' );
  } elseif ( $active_tab == 'tab_cleaner' ) {
      settings_fields( 'apip_cleaner_tab' );
      do_settings_sections( 'apip_cleaner_tab' );
  }
  else {
      settings_fields( 'apip_extra_tab' );
      do_settings_sections( 'apip_extra_tab' );
  }
  */
  ?>

  </form>
</div>
  <?php

}

function apip_test_field_render() {
    ?>
    <span>WP->is_ssl = <?php echo is_ssl()? 'YES':'NO'; ?> "wp_http_supports( array( 'ssl' ) )" = <?php echo wp_http_supports( array( 'ssl' ) )?'YES':'NO'; ?> </span>
    <?php
}


function apip_check_rubbish_options_field_render() {
    ?>
    <span>TODO</span>
    <?php
    $alloptions = wp_load_alloptions();
    $registered_settings = get_registered_settings();
    $arrnew = array_diff_key($alloptions, $registered_settings);
    apip_debug_page($arrnew ,"ccccccccc");
    /*
     [siteurl] => http://localhost/wordpress
    [home] => http://localhost/wordpress
    [blogname] => 破袜子
    [blogdescription] => 一个脱离不了低级趣味的人
    [users_can_register] => 0
    [admin_email] => lifishake@163.com
    [start_of_week] => 1
    [use_balanceTags] => 0
    [use_smilies] => 1
    [require_name_email] => 1
    [comments_notify] => 1
    [posts_per_rss] => 10
    [rss_use_excerpt] => 0
    [mailserver_url] => mail.example.com
    [mailserver_login] => login@example.com
    [mailserver_pass] => password
    [mailserver_port] => 110
    [default_category] => 1
    [default_comment_status] => open
    [default_ping_status] => open
    [default_pingback_flag] => 0
    [posts_per_page] => 10
    [date_format] => F j, Y
    [time_format] => g:i a
    [links_updated_date_format] => F j, Y g:i a
    [comment_moderation] => 0
    [moderation_notify] => 1
    [permalink_structure] => /index.php/%year%/%monthnum%/%day%/%postname%/
    [rewrite_rules] => a:89:{s:11:"^wp-json/?$";s:22:"index.php?rest_route=/";s:14:"^wp-json/(.*)?";s:33:"index.php?rest_route=/$matches[1]";s:21:"^index.php/wp-json/?$";s:22:"index.php?rest_route=/";s:24:"^index.php/wp-json/(.*)?";s:33:"index.php?rest_route=/$matches[1]";s:57:"index.php/category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?category_name=$matches[1]&feed=$matches[2]";s:52:"index.php/category/(.+?)/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?category_name=$matches[1]&feed=$matches[2]";s:33:"index.php/category/(.+?)/embed/?$";s:46:"index.php?category_name=$matches[1]&embed=true";s:45:"index.php/category/(.+?)/page/?([0-9]{1,})/?$";s:53:"index.php?category_name=$matches[1]&paged=$matches[2]";s:27:"index.php/category/(.+?)/?$";s:35:"index.php?category_name=$matches[1]";s:54:"index.php/tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?tag=$matches[1]&feed=$matches[2]";s:49:"index.php/tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?tag=$matches[1]&feed=$matches[2]";s:30:"index.php/tag/([^/]+)/embed/?$";s:36:"index.php?tag=$matches[1]&embed=true";s:42:"index.php/tag/([^/]+)/page/?([0-9]{1,})/?$";s:43:"index.php?tag=$matches[1]&paged=$matches[2]";s:24:"index.php/tag/([^/]+)/?$";s:25:"index.php?tag=$matches[1]";s:55:"index.php/type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?post_format=$matches[1]&feed=$matches[2]";s:50:"index.php/type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?post_format=$matches[1]&feed=$matches[2]";s:31:"index.php/type/([^/]+)/embed/?$";s:44:"index.php?post_format=$matches[1]&embed=true";s:43:"index.php/type/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?post_format=$matches[1]&paged=$matches[2]";s:25:"index.php/type/([^/]+)/?$";s:33:"index.php?post_format=$matches[1]";s:48:".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\.php$";s:18:"index.php?feed=old";s:20:".*wp-app\.php(/.*)?$";s:19:"index.php?error=403";s:18:".*wp-register.php$";s:23:"index.php?register=true";s:42:"index.php/feed/(feed|rdf|rss|rss2|atom)/?$";s:27:"index.php?&feed=$matches[1]";s:37:"index.php/(feed|rdf|rss|rss2|atom)/?$";s:27:"index.php?&feed=$matches[1]";s:18:"index.php/embed/?$";s:21:"index.php?&embed=true";s:30:"index.php/page/?([0-9]{1,})/?$";s:28:"index.php?&paged=$matches[1]";s:51:"index.php/comments/feed/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?&feed=$matches[1]&withcomments=1";s:46:"index.php/comments/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?&feed=$matches[1]&withcomments=1";s:27:"index.php/comments/embed/?$";s:21:"index.php?&embed=true";s:54:"index.php/search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:40:"index.php?s=$matches[1]&feed=$matches[2]";s:49:"index.php/search/(.+)/(feed|rdf|rss|rss2|atom)/?$";s:40:"index.php?s=$matches[1]&feed=$matches[2]";s:30:"index.php/search/(.+)/embed/?$";s:34:"index.php?s=$matches[1]&embed=true";s:42:"index.php/search/(.+)/page/?([0-9]{1,})/?$";s:41:"index.php?s=$matches[1]&paged=$matches[2]";s:24:"index.php/search/(.+)/?$";s:23:"index.php?s=$matches[1]";s:57:"index.php/author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?author_name=$matches[1]&feed=$matches[2]";s:52:"index.php/author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?author_name=$matches[1]&feed=$matches[2]";s:33:"index.php/author/([^/]+)/embed/?$";s:44:"index.php?author_name=$matches[1]&embed=true";s:45:"index.php/author/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?author_name=$matches[1]&paged=$matches[2]";s:27:"index.php/author/([^/]+)/?$";s:33:"index.php?author_name=$matches[1]";s:79:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$";s:80:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]";s:74:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$";s:80:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]";s:55:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$";s:74:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true";s:67:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$";s:81:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]";s:49:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$";s:63:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]";s:66:"index.php/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$";s:64:"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]";s:61:"index.php/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$";s:64:"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]";s:42:"index.php/([0-9]{4})/([0-9]{1,2})/embed/?$";s:58:"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true";s:54:"index.php/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$";s:65:"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]";s:36:"index.php/([0-9]{4})/([0-9]{1,2})/?$";s:47:"index.php?year=$matches[1]&monthnum=$matches[2]";s:53:"index.php/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?year=$matches[1]&feed=$matches[2]";s:48:"index.php/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?year=$matches[1]&feed=$matches[2]";s:29:"index.php/([0-9]{4})/embed/?$";s:37:"index.php?year=$matches[1]&embed=true";s:41:"index.php/([0-9]{4})/page/?([0-9]{1,})/?$";s:44:"index.php?year=$matches[1]&paged=$matches[2]";s:23:"index.php/([0-9]{4})/?$";s:26:"index.php?year=$matches[1]";s:68:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:78:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:98:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:93:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:93:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:74:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:63:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/embed/?$";s:91:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&embed=true";s:67:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/trackback/?$";s:85:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&tb=1";s:87:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]";s:82:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]";s:75:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$";s:98:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&paged=$matches[5]";s:82:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$";s:98:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&cpage=$matches[5]";s:71:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&page=$matches[5]";s:57:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:67:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:87:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:82:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:82:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:63:"index.php/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:74:"index.php/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$";s:81:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&cpage=$matches[4]";s:61:"index.php/([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$";s:65:"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]";s:48:"index.php/([0-9]{4})/comment-page-([0-9]{1,})/?$";s:44:"index.php?year=$matches[1]&cpage=$matches[2]";s:37:"index.php/.?.+?/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:47:"index.php/.?.+?/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:67:"index.php/.?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:62:"index.php/.?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:62:"index.php/.?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:43:"index.php/.?.+?/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:26:"index.php/(.?.+?)/embed/?$";s:41:"index.php?pagename=$matches[1]&embed=true";s:30:"index.php/(.?.+?)/trackback/?$";s:35:"index.php?pagename=$matches[1]&tb=1";s:50:"index.php/(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:47:"index.php?pagename=$matches[1]&feed=$matches[2]";s:45:"index.php/(.?.+?)/(feed|rdf|rss|rss2|atom)/?$";s:47:"index.php?pagename=$matches[1]&feed=$matches[2]";s:38:"index.php/(.?.+?)/page/?([0-9]{1,})/?$";s:48:"index.php?pagename=$matches[1]&paged=$matches[2]";s:45:"index.php/(.?.+?)/comment-page-([0-9]{1,})/?$";s:48:"index.php?pagename=$matches[1]&cpage=$matches[2]";s:34:"index.php/(.?.+?)(?:/([0-9]+))?/?$";s:47:"index.php?pagename=$matches[1]&page=$matches[2]";}
    [hack_file] => 0
    [blog_charset] => UTF-8
    [active_plugins] => a:4:{i:0;s:13:"apip/apip.php";i:1;s:20:"instrument-hooks.php";i:2;s:25:"jolly-memo/jolly-memo.php";i:3;s:41:"wordpress-importer/wordpress-importer.php";}
    [category_base] => 
    [ping_sites] => http://rpc.pingomatic.com/
    [comment_max_links] => 2
    [gmt_offset] => 
    [default_email_category] => 1
    [template] => sketchy
    [stylesheet] => sketchy
    [comment_whitelist] => 1
    [comment_registration] => 0
    [html_type] => text/html
    [use_trackback] => 0
    [default_role] => subscriber
    [db_version] => 38590
    [uploads_use_yearmonth_folders] => 1
    [upload_path] => 
    [blog_public] => 0
    [default_link_category] => 2
    [show_on_front] => posts
    [tag_base] => 
    [show_avatars] => 1
    [avatar_rating] => G
    [upload_url_path] => 
    [thumbnail_size_w] => 150
    [thumbnail_size_h] => 150
    [thumbnail_crop] => 1
    [medium_size_w] => 300
    [medium_size_h] => 300
    [avatar_default] => mystery
    [large_size_w] => 1024
    [large_size_h] => 1024
    [image_default_link_type] => none
    [image_default_size] => 
    [image_default_align] => 
    [close_comments_for_old_posts] => 0
    [close_comments_days_old] => 14
    [thread_comments] => 1
    [thread_comments_depth] => 5
    [page_comments] => 0
    [comments_per_page] => 50
    [default_comments_page] => newest
    [comment_order] => asc
    [sticky_posts] => a:3:{i:0;i:77;i:1;i:186;i:2;i:352;}
    [widget_categories] => a:2:{i:2;a:4:{s:5:"title";s:0:"";s:5:"count";i:0;s:12:"hierarchical";i:0;s:8:"dropdown";i:0;}s:12:"_multiwidget";i:1;}
    [widget_text] => a:0:{}
    [widget_rss] => a:0:{}
    [timezone_string] => Africa/Abidjan
    [page_for_posts] => 0
    [page_on_front] => 0
    [default_post_format] => 0
    [link_manager_enabled] => 0
    [finished_splitting_shared_terms] => 1
    [site_icon] => 0
    [medium_large_size_w] => 768
    [medium_large_size_h] => 0
    [wp_page_for_privacy_policy] => 3
    [show_comments_cookies_opt_in] => 0
    [initial_db_version] => 38590
    [wp_user_roles] => a:5:{s:13:"administrator";a:2:{s:4:"name";s:13:"Administrator";s:12:"capabilities";a:63:{s:13:"switch_themes";b:1;s:11:"edit_themes";b:1;s:16:"activate_plugins";b:1;s:12:"edit_plugins";b:1;s:10:"edit_users";b:1;s:10:"edit_files";b:1;s:14:"manage_options";b:1;s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:6:"import";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:8:"level_10";b:1;s:7:"level_9";b:1;s:7:"level_8";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;s:12:"delete_users";b:1;s:12:"create_users";b:1;s:17:"unfiltered_upload";b:1;s:14:"edit_dashboard";b:1;s:14:"update_plugins";b:1;s:14:"delete_plugins";b:1;s:15:"install_plugins";b:1;s:13:"update_themes";b:1;s:14:"install_themes";b:1;s:11:"update_core";b:1;s:10:"list_users";b:1;s:12:"remove_users";b:1;s:13:"promote_users";b:1;s:18:"edit_theme_options";b:1;s:13:"delete_themes";b:1;s:6:"export";b:1;s:19:"NextGEN Manage tags";b:1;s:29:"NextGEN Manage others gallery";b:1;}}s:6:"editor";a:2:{s:4:"name";s:6:"Editor";s:12:"capabilities";a:34:{s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;}}s:6:"author";a:2:{s:4:"name";s:6:"Author";s:12:"capabilities";a:10:{s:12:"upload_files";b:1;s:10:"edit_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:4:"read";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;s:22:"delete_published_posts";b:1;}}s:11:"contributor";a:2:{s:4:"name";s:11:"Contributor";s:12:"capabilities";a:5:{s:10:"edit_posts";b:1;s:4:"read";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;}}s:10:"subscriber";a:2:{s:4:"name";s:10:"Subscriber";s:12:"capabilities";a:2:{s:4:"read";b:1;s:7:"level_0";b:1;}}}
    [fresh_site] => 0
    [widget_search] => a:2:{i:2;a:1:{s:5:"title";s:6:"SEARCH";}s:12:"_multiwidget";i:1;}
    [widget_recent-posts] => a:2:{i:2;a:2:{s:5:"title";s:0:"";s:6:"number";i:5;}s:12:"_multiwidget";i:1;}
    [widget_recent-comments] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_archives] => a:2:{i:2;a:3:{s:5:"title";s:0:"";s:5:"count";i:0;s:8:"dropdown";i:0;}s:12:"_multiwidget";i:1;}
    [widget_meta] => a:2:{i:2;a:1:{s:5:"title";s:0:"";}s:12:"_multiwidget";i:1;}
    [sidebars_widgets] => a:4:{s:19:"wp_inactive_widgets";a:0:{}s:9:"sidebar-2";a:2:{i:0;s:20:"apip_widget_recent-2";i:1;s:23:"apip_widget_hook_list-2";}s:9:"sidebar-3";a:2:{i:0;s:8:"search-2";i:1;s:18:"apip_widget_meta-2";}s:13:"array_version";i:3;}
    [widget_pages] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_calendar] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_media_audio] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_media_image] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_media_gallery] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_media_video] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_tag_cloud] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_nav_menu] => a:1:{s:12:"_multiwidget";i:1;}
    [widget_custom_html] => a:1:{s:12:"_multiwidget";i:1;}
    [cron] => a:8:{i:1618558336;a:1:{s:34:"wp_privacy_delete_old_export_files";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:6:"hourly";s:4:"args";a:0:{}s:8:"interval";i:3600;}}}i:1618587136;a:2:{s:16:"wp_version_check";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}s:17:"wp_update_plugins";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}}i:1618587137;a:1:{s:16:"wp_update_themes";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}}i:1618628042;a:1:{s:30:"wp_scheduled_auto_draft_delete";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1618630380;a:1:{s:19:"wp_scheduled_delete";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1618630382;a:1:{s:25:"delete_expired_transients";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1619246572;a:1:{s:27:"apip_delete_local_gravatars";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:6:"10Days";s:4:"args";a:0:{}s:8:"interval";i:864000;}}}s:7:"version";i:2;}
    [theme_mods_twentyseventeen] => a:3:{s:18:"custom_css_post_id";i:-1;s:16:"sidebars_widgets";a:2:{s:4:"time";i:1599533490;s:4:"data";a:4:{s:19:"wp_inactive_widgets";a:0:{}s:9:"sidebar-1";a:0:{}s:9:"sidebar-2";a:6:{i:0;s:8:"search-2";i:1;s:14:"recent-posts-2";i:2;s:17:"recent-comments-2";i:3;s:10:"archives-2";i:4;s:12:"categories-2";i:5;s:6:"meta-2";}s:9:"sidebar-3";a:0:{}}}s:18:"nav_menu_locations";a:0:{}}
    [recently_activated] => a:0:{}
    [ngg_run_freemius] => 1
    [fs_active_plugins] => O:8:"stdClass":0:{}
    [fs_debug_mode] => 
    [fs_accounts] => a:5:{s:11:"plugin_data";a:1:{s:15:"nextgen-gallery";a:12:{s:16:"plugin_main_file";O:8:"stdClass":1:{s:9:"prev_path";s:75:"E:/xampp7/htdocs/wordpress/wp-content/plugins/nextgen-gallery/nggallery.php";}s:17:"install_timestamp";i:1586232329;s:16:"sdk_last_version";N;s:11:"sdk_version";s:7:"1.2.1.5";s:16:"sdk_upgrade_mode";b:1;s:18:"sdk_downgrade_mode";b:0;s:19:"plugin_last_version";N;s:14:"plugin_version";s:5:"3.0.6";s:19:"plugin_upgrade_mode";b:1;s:21:"plugin_downgrade_mode";b:0;s:21:"is_plugin_new_install";b:1;s:17:"was_plugin_loaded";b:1;}}s:13:"file_slug_map";a:1:{s:29:"nextgen-gallery/nggallery.php";s:15:"nextgen-gallery";}s:7:"plugins";a:1:{s:15:"nextgen-gallery";O:9:"FS_Plugin":16:{s:16:"parent_plugin_id";N;s:5:"title";s:15:"NextGEN Gallery";s:4:"slug";s:15:"nextgen-gallery";s:4:"type";N;s:4:"file";s:29:"nextgen-gallery/nggallery.php";s:7:"version";s:5:"3.0.6";s:11:"auto_update";N;s:4:"info";N;s:10:"is_premium";b:0;s:7:"is_live";b:1;s:10:"public_key";s:32:"pk_009356711cd548837f074e1ef60a4";s:10:"secret_key";N;s:2:"id";s:3:"266";s:7:"updated";N;s:7:"created";N;s:22:"
    */
}
?>
