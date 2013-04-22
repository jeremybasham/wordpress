<?php
/* Headway 3.2.5 has better support for the 'headway_element_data_defaults' filter */
if ( version_compare(HEADWAY_VERSION, '3.2.5', '>=') ) {

	add_filter('headway_element_data_defaults', 'article_builder_block_add_default_design_settings');
	function article_builder_block_add_default_design_settings($existing_defaults) {

		return array_merge($existing_defaults, article_builder_block_default_design_settings());

	}


} else {

	add_action('init', 'article_builder_pre325_default_design_settings');

	function article_builder_pre325_default_design_settings() {

		global $headway_default_element_data;

		$headway_default_element_data = array_merge($headway_default_element_data, article_builder_block_default_design_settings());

	}

}


function article_builder_block_default_design_settings() {

	return array(
			/* Article Builder Block */
			'block-article-builder-article-container' => array(
				'properties' => array(
					'padding-top' => '10',
					'padding-right' => '10',
					'padding-bottom' => '10',
					'padding-left' => '10',
					'background-color' => 'fcfcfc',
					'border-top-width' => '1',
					'border-right-width' => '1',
					'border-bottom-width' => '1',
					'border-left-width' => '1',
					'border-color' => 'cccccc',
					'border-style' => 'solid',
					'position' => 'relative'
				)
			),

			'block-article-builder-article-title' => array(
				'properties' => array(
					'font-size' => '21',
					'line-height' => '130',
					'margin-bottom' => '15',
					'text-decoration' => 'none'
				)
			),

			'block-article-builder-thumb-overlay' => array(
				'properties' => array(
					'background-color' => 'rgba(0,0,0,.8)',
				)
			),

			'block-article-builder-thumb-overlay-icon' => array(
				'properties' => array(
					'color' => 'ffffff',
					'text-transform' => 'uppercase',
					'font-weight' => 'normal'
				)
			),

			'block-article-builder-article-title' => array(
				'properties' => array(
					'font-size' => '21',
					'line-height' => '130',
					'margin-bottom' => '15'
				)
			),

			'block-article-builder-article-excerpt' => array(
				'properties' => array(
					'line-height' => '140',
					'margin-top' => '15',
					'margin-bottom' => '15'
				)
			),

			'block-article-builder-article-more-link' => array(
				'properties' => array(
					'padding-top' => '0',
					'padding-right' => '0',
					'padding-bottom' => '0',
					'padding-left' => '0'
				)
			)
		);

}