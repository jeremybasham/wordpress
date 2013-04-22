<?php

// Uncomment line below if you want to use post formats
//add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

add_action('headway_setup_child_theme', 'churchhype_child_setup');
function churchhype_child_setup() {
    remove_theme_support('headway-design-editor');
    remove_theme_support('headway-structure-css');
}


/* BLOCK STYLE for top navigation */
/*
add_action('init', 'churchhype_child_theme_add_block_styles');
function churchhype_child_theme_add_block_styles() {

    HeadwayChildThemeAPI::register_block_style(array(
		'id' => 'top',
		'name' => 'Top Navigation', 
		'class' => 'top',
		'block-types' => array('navigation')
	));
     
}
*/