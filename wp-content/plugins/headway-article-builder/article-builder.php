<?php
/*
Plugin Name: Article Builder
Plugin URI: http://www.headwaylabs.com
Description: Article Builder block for Headway 3.0. Makes it easy to build content blocks with Headway using handy article short codes in the article builder interface.
Version: 1.0
Author: Headway Labs
Author URI: http://www.headwaylabs.com
License: GNU GPL v2
*/

define('ARTICLE_BUILDER_BLOCK_VERSION', '1.0');

add_action('after_setup_theme', 'post_builder_block_register');
function post_builder_block_register() {

	/* Make sure that Headway is activated, otherwise don't register the block because errors will be thrown. */
	if ( !class_exists('Headway') )
		return;
	
	require_once 'block.php';
	require_once 'block-options.php';

	require_once 'design-editor-settings.php';	

	return headway_register_block('HeadwayArticleBuilderBlock', plugins_url(false, __FILE__));

}

add_action('init', 'article_builder_block_extend_updater');
function article_builder_block_extend_updater() {

	if ( !class_exists('HeadwayUpdaterAPI') )
		return;

	$updater = new HeadwayUpdaterAPI(array(
		'slug' => 'article-builder-block',
		'path' => plugin_basename(__FILE__),
		'name' => 'Article Builder Block',
		'type' => 'block',
		'current_version' => ARTICLE_BUILDER_BLOCK_VERSION
	));

}

/* include admin js
***************************************************************/
function builder_admin_js() {
	HeadwayCompiler::register_file(array(
		'name' => 'builder-admin-js',
		'format' => 'js',
		'fragments' => array(
			dirname(__FILE__).'/admin/js/builder-admin.js'
		)
	));
}
add_action('headway_visual_editor_scripts', 'builder_admin_js', 12);

/* include admin css
***************************************************************/
function builder_admin_css() {
	HeadwayCompiler::register_file(array(
		'name' => 'builder-admin-css',
		'format' => 'css',
		'fragments' => array(
			dirname(__FILE__).'/admin/css/builder-admin.css'
		)
	));
}
add_action('headway_visual_editor_styles', 'builder_admin_css', 12);