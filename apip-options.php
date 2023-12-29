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
  $agm = array();
  $selected_mirror = "";
  if (isset($options['gravatar_mirror'])) {
      $selected_mirror = $options['gravatar_mirror'];
  }
  if (isset( $options['available_gravatar_mirrors']) && trim($options['available_gravatar_mirrors'])!=="") {
    $agm = explode(",", $options['available_gravatar_mirrors']); }
    if ($selected_mirror===""){
        $selected_mirror = $agm[0];
    }
  ?>
  <span>    gravatar使用本地缓存(不使用则替换网址)：</span>
  <input type='checkbox' name='apip_settings[local_gravatar]' <?php checked( $options['local_gravatar'], 1 ); ?> value='1'/><br />
  <span>    可用镜像网址（逗号分隔）:     </span>
  <br />
    <textarea rows='4' cols='40' name='apip_settings[available_gravatar_mirrors]' ><?php echo htmlspecialchars(stripslashes($options['available_gravatar_mirrors'])); ?></textarea>
    <span>下面有头像显示的为可用镜像:</span><br/>
    <?php $i = 0; foreach( $agm as $mirror) {
        $mirror = trim($mirror);
        $str_output = sprintf('<label><input type="radio" name="apip_settings[gravatar_mirror]" id="agm_id_%1$d" value="%3$s" %2$s/>%3$s</label><img src="%3$s/a57a69bb73b078072b0c7ca576c26fea?s=16&d=mm&r=g"/><br />', $i, $selected_mirror == $mirror ? "checked='checked'":"", $mirror);
        echo $str_output;
    } ?>
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
  <span>    识别code highlight功能的tag（逗号分隔）:     </span>
  <br />
    <textarea rows='4' cols='40' name='apip_settings[available_codehighlight_tags]' ><?php echo htmlspecialchars(stripslashes($options['available_codehighlight_tags'])); ?></textarea>
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
  <div class="description">【基本操作】页是作者认为中文环境下使用WordPress必须使用的一些设置和强化；<br/>【清理工具】可以对后台数据库进行分析和清理。</div>
            <?php //settings_errors(); ?>

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

//https://wp-kama.com/1609/wordpress-options-list
function apip_check_rubbish_options_field_render() {
    ?>
    <span>TODO</span>
    <?php
    $alloptions = wp_load_alloptions();
    $registered_settings = get_registered_settings();
    $arrnew = array_diff_key($alloptions, $registered_settings);
    $default_options = array(
      "admin_email"=>"",
      "new_admin_email"=>"",
      "adminhash"=>"",
      "blogdescription"=>"",
      "blogname"=>"",
      "date_format"=>"",
      "gmt_offset"=>"",
      "home"=>"",
      "siteurl"=>"",
      "start_of_week"=>"",
      "time_format"=>"",
      "timezone_string"=>"",
      "users_can_register"=>"",
      "default_role"=>"",
      "auto_update_core_major"=>"",
      "auto_update_core_minor"=>"",
      "auto_update_core_dev"=>"",
      "Media"=>"",
      "upload_path"=>"",
      "upload_url_path"=>"",
      "uploads_use_yearmonth_folders"=>"",
      "thumbnail_size_w"=>"",
      "thumbnail_size_h"=>"",
      "thumbnail_crop"=>"",
      "medium_size_w"=>"",
      "medium_size_h"=>"",
      "large_size_w"=>"",
      "large_size_h"=>"",
      "medium_large_size_w"=>"",
      "medium_large_size_h"=>"",
      "embed_size_w"=>"",
      "embed_size_h"=>"",
      "Comments"=>"",
      "default_pingback_flag"=>"",
      "default_comment_status"=>"",
      "default_ping_status"=>"",
      "require_name_email"=>"",
      "comment_registration"=>"",
      "close_comments_for_old_posts"=>"",
      "close_comments_days_old"=>"",
      "show_comments_cookies_opt_in"=>"",
      "thread_comments"=>"",
      "thread_comments_depth"=>"",
      "page_comments"=>"",
      "comments_per_page"=>"",
      "default_comments_page"=>"",
      "comment_order"=>"",
      "comments_notify"=>"",
      "moderation_notify"=>"",
      "comment_moderation"=>"",
      "comment_whitelist"=>"",
      "comment_max_links"=>"",
      "moderation_keys"=>"",
      "blacklist_keys"=>"",
      "Avatars"=>"",
      "show_avatars"=>"",
      "avatar_rating"=>"",
      "avatar_default"=>"",
      "Permanent Links"=>"",
      "permalink_structure"=>"",
      "category_base"=>"",
      "tag_base"=>"",
      "Writing"=>"",
      "default_category"=>"",
      "default_post_format"=>"",
      "use_smilies"=>"",
      "use_balanceTags"=>"",
      "use_trackback"=>"",
      "mailserver_url"=>"",
      "mailserver_login"=>"",
      "mailserver_pass"=>"",
      "mailserver_port"=>"",
      "default_email_category"=>"",
      "ping_sites"=>"",
      "Reading"=>"",
      "blog_public"=>"",
      "blog_charset"=>"",
      "page_on_front"=>"",
      "page_for_posts"=>"",
      "show_on_front"=>"",
      "posts_per_page"=>"",
      "posts_per_rss"=>"",
      "rss_use_excerpt"=>"",
      "Theme"=>"",
      "template"=>"",
      "stylesheet"=>"",
      "Others"=>"",
      "site_icon"=>"",
      "active_plugins"=>"",
      "recently_edited"=>"",
      "image_default_link_type"=>"",
      "image_default_size"=>"",
      "image_default_align"=>"",
      "sidebars_widgets"=>"",
      "sticky_posts"=>"",
      "widget_categories"=>"",
      "widget_text"=>"",
      "widget_rss"=>"",
      "html_type"=>"",
      "wp_page_for_privacy_policy"=>"",
      "wp_user_roles"=>"",
      "rewrite_rules"=>"",
      "https_detection_errors"=>"",
      "Links"=>"",
      "links_updated_date_format"=>"",
      "Multisite"=>"",
      "fileupload_maxk"=>"",
      "current_theme"=>"",
      "WPLANG"=>"",
      "theme_switched"=>"",
      "cron"=>"",
      "nav_menu_options"=>"",
      "hack_file"=>"",
      "db_version"=>"",
      "default_link_category"=>"",
      "link_manager_enabled"=>"",
      "finished_splitting_shared_terms"=>"",
      "initial_db_version"=>"",
      "fresh_site"=>"",
      "widget_search"=>"",
      "widget_recent-posts"=>"",
      "widget_recent-comments"=>"",
      "widget_archives"=>"",
      "widget_meta"=>"",
      "widget_pages"=>"",
      "widget_calendar"=>"",
      "widget_media_audio"=>"",
      "widget_media_image"=>"",
      "widget_media_gallery"=>"",
      "widget_media_video"=>"",
      "widget_tag_cloud"=>"",
      "widget_nav_menu"=>"",
      "widget_custom_html"=>"",
      "recently_activated"=>"",
      "widget_apip_widget_related"=>"",
      "widget_apip_widget_sameday_post"=>"",
      "widget_apip_widget_recent"=>"",
      "widget_apip_widget_meta"=>"",
      "widget_apip_widget_hook_list"=>"",
      "category_children"=>"",
      "_transient_doing_cron"=>"",
    );
    $default_options["theme_mods_".get_stylesheet()]="";
    $arrnew = array_diff_key($arrnew, $default_options);
    apip_debug_page($arrnew ,"ccccccccc");
}
?>
