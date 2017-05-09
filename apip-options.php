<?php

/**
 * 简陋,由工具做成,少量修改
 * 工具URL: http://wpsettingsapi.jeroensormani.com/
*/

add_action( 'admin_menu', 'apip_add_admin_menu' );
add_action( 'admin_init', 'apip_settings_init' );


function apip_add_admin_menu(  ) { 

	add_menu_page( 'APIP设置', 'APIP设置', 'manage_options', __FILE__, 'apip_options_page', plugin_dir_url( __FILE__ ).'img/apip-ico.png' );

}

/*
支持的功能列表
01. 改进的功能摘要

*/
function apip_settings_init(  ) { 

	register_setting( 'apip_option_group', 'apip_settings' );

	add_settings_section(
		'apip_pluginPage_section', 
		'APIP设置', 
		'apip_settings_section_callback', 
		'apip_option_group'
	);

	//01
	add_settings_field( 
		'better_excerpt', 
		'改进版中文摘要', 
		'apip_better_excerpt_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	
	//02
	add_settings_field( 
		'advanced_writer_settings', 
		'高级编辑选项', 
		'apip_advanced_writer_settings_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	//03
	add_settings_field( 
		'header_description', 
		'首页和头部追加描述和关键字', 
		'apip_header_description_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	//04
	add_settings_field( 
		'notify_comment_reply', 
		'新回复邮件提示', 
		'apip_notify_comment_reply_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	//05
	add_settings_field( 
		'local_gravatar', 
		'GFW相关', 
		'apip_anti_gfw_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	//06
	add_settings_field( 
		'blocked_commenters', 
		'要替换的留言者黑名单', 
		'apip_blocked_commenters_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	//07
	add_settings_field( 
		'social_share_settings', 
		'社会化分享', 
		'apip_social_share_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	
	//08
	add_settings_field( 
		'shortcode_settings', 
		'自定义的SHORTCODE', 
		'apip_shortcodes_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	//09
	add_settings_field( 
		'heavy_tools_settings', 
		'比较复杂的功能', 
		'apip_heavy_tools_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
	//99
	add_settings_field( 
		'local_definition_count', 
		'自定义widget', 
		'apip_local_widgets_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);
    //-1
    /*add_settings_field( 
		'test_field_settings', 
		'保留功能', 
		'apip_test_field_render', 
		'apip_option_group', 
		'apip_pluginPage_section' 
	);*/
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

function apip_better_excerpt_render(  ) { 
	//01
	$options = get_option( 'apip_settings' );
	?>
	<input type='checkbox' name='apip_settings[better_excerpt]' <?php checked( $options['better_excerpt'], 1 ); ?> value='1'/>
	<span>    摘要长度：</span>
	<input type='text' name='apip_settings[excerpt_length]' size='5' value='<?php echo $options['excerpt_length']; ?>'/></br>
	<span>    结尾字符：</span>
	<input type='text' name='apip_settings[excerpt_ellipsis]' size='10' value='<?php echo $options['excerpt_ellipsis']; ?>'/>
	<?php
}

function apip_advanced_writer_settings_render()
{
	//02
	$options = get_option( 'apip_settings' );
	?>
	<span>    禁止自动保存：</span>
	<input type='checkbox' name='apip_settings[auto_save_disabled]' <?php checked( $options['auto_save_disabled'], 1 ); ?> value='1'/></br>
	<span>    禁止保存版本修订(autorevision)：</span>
	<input type='checkbox' name='apip_settings[save_revisions_disable]' <?php checked( $options['save_revisions_disable'], 1 ); ?> value='1'/></br>
	<span>    显示AdminBar：</span>
	<input type='checkbox' name='apip_settings[show_admin_bar]' <?php checked( $options['show_admin_bar'], 1 ); ?> value='1'/></br>
	<span>    前台显示中文：</span>
	<input type='checkbox' name='apip_settings[forground_chinese]' <?php checked( $options['forground_chinese'], 1 ); ?> value='1'/></br>
	<span>    屏蔽OpenSans字体：</span>
	<input type='checkbox' name='apip_settings[block_open_sans]' <?php checked( $options['block_open_sans'], 1 ); ?> value='1'/></br>
	<span>    默认留言widget中屏蔽作者：</span>
	<input type='checkbox' name='apip_settings[show_author_comment]' <?php checked( $options['show_author_comment'], 1 ); ?> value='1'/></br>
	<span>    搜索结果只有一条时直接跳转：</span>
	<input type='checkbox' name='apip_settings[redirect_if_single]' <?php checked( $options['redirect_if_single'], 1 ); ?> value='1'/></br>
    <span>    保护wp_comments.php：</span>
    <input type='checkbox' name='apip_settings[protect_comment_php]' <?php checked( $options['protect_comment_php'], 1 ); ?> value='1'/></br>
    <span>    搜索结果中屏蔽page：</span>
    <input type='checkbox' name='apip_settings[search_without_page]' <?php checked( $options['search_without_page'], 1 ); ?> value='1'/></br>
	<?php
	
}

function apip_header_description_render(  ) { 
	//03
	$options = get_option( 'apip_settings' );
	?>
	<input type='checkbox' name='apip_settings[header_description]' <?php checked( $options['header_description'], 1 ); ?> value='1'/>
	<span>    网站描述（留空则使用网站副标题）：</span>
	<input type='text' name='apip_settings[hd_home_text]' value='<?php echo htmlspecialchars(stripslashes($options['hd_home_text'])); ?>' /></br>
	<span>    网站标签（【,】分割（留空则使用10个最常用标签）：</span>
	<input type='text' name='apip_settings[hd_home_keyword]' value='<?php echo htmlspecialchars(stripslashes($options['hd_home_keyword'])); ?>' />
	<?php
}

function apip_notify_comment_reply_render(  ) { 
	//04
	$options = get_option( 'apip_settings' );
	?>
	<input type='checkbox' name='apip_settings[notify_comment_reply]' <?php checked( $options['notify_comment_reply'], 1 ); ?> value='1'/>
	<?php
}

function apip_anti_gfw_render()
{
	//05
	$options = get_option( 'apip_settings' );
	?>
	<span>    gravatar使用本地缓存(不使用则替换网址)：</span>
	<input type='checkbox' name='apip_settings[local_gravatar]' <?php checked( $options['local_gravatar'], 1 ); ?> value='1'/></br>
	<span>    替换emojie地址：</span>
	<input type='checkbox' name='apip_settings[replace_emoji]' <?php checked( $options['replace_emoji'], 1 ); ?> value='1'/>
	<?php
}

function apip_blocked_commenters_render()
{
	//06
	$options = get_option( 'apip_settings' );
	?>

    <textarea rows='4' cols='40' name='apip_settings[blocked_commenters]' ><?php echo htmlspecialchars(stripslashes($options['blocked_commenters'])); ?></textarea>
	<?php
}

function apip_social_share_render()
{
	//07
	$options = get_option( 'apip_settings' );
	?>
	<span>    是否允许(css+js)：</span>
	<input type='checkbox' name='apip_settings[social_share_enable]' <?php checked( $options['social_share_enable'], 1 ); ?> value='1'>
	<span>    Twitter：</span>
	<input type='checkbox' name='apip_settings[social_share_twitter]' <?php checked( $options['social_share_twitter'], 1 ); ?> value='1'>
	<span>    新浪微博：</span>
	<input type='checkbox' name='apip_settings[social_share_sina]' <?php checked( $options['social_share_sina'], 1 ); ?> value='1'>
	<span>    腾讯微博：</span>
	<input type='checkbox' name='apip_settings[social_share_tencent]' <?php checked( $options['social_share_tencent'], 1 ); ?> value='1'>
	<span>    facebook：</span>
	<input type='checkbox' name='apip_settings[social_share_facebook]' <?php checked( $options['social_share_facebook'], 1 ); ?> value='1'>
	<span>    google+：</span>
	<input type='checkbox' name='apip_settings[social_share_googleplus]' <?php checked( $options['social_share_googleplus'], 1 ); ?> value='1'>
	<span>    开心：</span>
	<input type='checkbox' name='apip_settings[social_share_kaixin]' <?php checked( $options['social_share_kaixin'], 1 ); ?> value='1'>
	<span>    人人：</span>
	<input type='checkbox' name='apip_settings[social_share_renren]' <?php checked( $options['social_share_renren'], 1 ); ?> value='1'>
	<?php
}

function apip_shortcodes_render()
{
	//08
	$options = get_option( 'apip_settings' );
	?>
	<span>    激活自定义TagCloud页（css）：</span>
	<input type='checkbox' name='apip_settings[apip_tagcloud_enable]' <?php checked( $options['apip_tagcloud_enable'], 1 ); ?> value='1'>
    <span>    激活自定义Link页（css）：</span>
	<input type='checkbox' name='apip_settings[apip_link_enable]' <?php checked( $options['apip_link_enable'], 1 ); ?> value='1'>
    <span>    激活自定义归档页（css）：</span>
	<input type='checkbox' name='apip_settings[apip_archive_enable]' <?php checked( $options['apip_archive_enable'], 1 ); ?> value='1'>
	<?php
}

function apip_heavy_tools_render()
{
	//09
	$options = get_option( 'apip_settings' );
	?>
	<span>    使用code_heighlight(js+css+content_filter)：</span>
	<input type='checkbox' name='apip_settings[apip_codehighlight_enable]' <?php checked( $options['apip_codehighlight_enable'], 1 ); ?> value='1'></br>
	<span>    使用lazy_load(js+content_filter)：</span>
	<input type='checkbox' name='apip_settings[apip_lazyload_enable]' <?php checked( $options['apip_lazyload_enable'], 1 ); ?> value='1'></br>
    <span>    查询、归档页进入单页后，“上一页”、“下一页”在该范围内跳转(js)：</span>
	<input type='checkbox' name='apip_settings[range_jump_enable]' <?php checked( $options['range_jump_enable'], 1 ); ?> value='1'></br>
    <span>    启用用户留言评分(js)：</span>
	<input type='checkbox' name='apip_settings[commentator_rating_enable]' <?php checked( $options['commentator_rating_enable'], 1 ); ?> value='1'></br>
	<?php
}

function apip_local_widgets_render()
{
	//99
	$options = get_option( 'apip_settings' );
	?>
	<span>    使用自定义widget：</span>
	<input type='checkbox' name='apip_settings[local_widget_enable]' <?php checked( $options['local_widget_enable'], 1 ); ?> value='1'></br>
	<span>    默认条数：</span>
	<input type='text' size = '5' name='apip_settings[local_definition_count]' value='<?php echo htmlspecialchars(stripslashes($options['local_definition_count'])); ?>' >
	<?php
}

function apip_settings_section_callback(  ) { 

	echo '一些基本设定项目，抄自多个插件';

}


function apip_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>All plugins in pewae</h2>
		
		<?php
		settings_fields( 'apip_option_group' );
		do_settings_sections( 'apip_option_group' );
		submit_button();
		?>
		
	</form>
	<?php

}

function apip_test_field_render() {
    ?>
    <span>WP->is_ssl = <?php echo is_ssl()? 'YES':'NO'; ?> WP_FS__IS_HTTPS = <?php echo WP_FS__IS_HTTPS?'YES':'NO'; ?> </span>
    <?php
}

?>