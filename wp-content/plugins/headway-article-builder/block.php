<?php
/* This class must be included in another file and included later so we don't get an error about HeadwayBlockAPI class not existing. */

class HeadwayArticleBuilderBlock extends HeadwayBlockAPI {
	
	public $id = 'article-builder';
	
	public $name = 'Article Builder';
	
	public $options_class = 'HeadwayArticleBuilderBlockOptions';

	static public $block = null;

	function __construct() {
		
		//$this->block = $block;

		$blocks = HeadwayBlocksData::get_blocks_by_type('article-builder-block');

		/* return if there are not blocks for this type.. else do the foreach */
		if ( !isset($blocks) || !is_array($blocks) )
			return;
		
		foreach ($blocks as $block_id => $layout_id) {
			self::$block = HeadwayBlocksData::get_block($block_id);
		}

	}

	function init() {
		require_once 'content-display.php';
	}

	function add_builder_css($general_css_fragments) {
		$general_css_fragments[] = dirname(__FILE__).'/css/builder.css';
		return $general_css_fragments;
	}

	function enqueue_action($block_id) {
		$block = HeadwayBlocksData::get_block($block_id);
		add_filter('headway_general_css', array(__CLASS__, 'add_builder_css'));

		if ( version_compare('3.2', HEADWAY_VERSION, '<=') ) {
			add_filter('headway_general_css', array(__CLASS__, 'add_builder_css'));
		} else {
			add_filter('headaway_general_css', array(__CLASS__, 'add_builder_css'));
		}

		$hover_overlay = parent::get_setting($block, 'thumb-hover-overlay', false);
		$align = parent::get_setting($block, 'thumb-align', 'none');

		if ($hover_overlay || $align == 'center')
		wp_enqueue_script('headway-builder-overlay', plugins_url(basename(dirname(__FILE__))) . '/js/hover-overlay.js', array('jquery'));	

		return;
		
	}
	
	function dynamic_js($block_id, $block = false) {

		if ( !$block )
			$block = HeadwayBlocksData::get_block($block_id);
			
			$hover_overlay = parent::get_setting($block, 'thumb-hover-overlay', false);
			$align = parent::get_setting($block, 'thumb-align', 'none');

			$js = '';

			if ($hover_overlay)
			$js .= '
				jQuery(function() {
					jQuery(\'#block-' . $block_id . ' .articles > article\').hoverdir();
				});
			';
			if ($align == 'center')
			$js .= '
			(function ($) {
				$(document).ready(function() {
					$(\'#block-' . $block_id . ' .articles > article figure.aligncenter a\').hAlign();
				});
			})(jQuery);';

			return $js;
		
	}
	
	function dynamic_css($block_id, $block = false) {

		if ( !$block )
			$block = HeadwayBlocksData::get_block($block_id);

			$stack_or_float = parent::get_setting($block, 'stack-or-float', 'float');
			$gutter_width   = parent::get_setting($block, 'gutter-width', '20');
			$min_height     = parent::get_setting($block, 'minimum-height');

			/* A little maths to work out which items will be the first item in a row */
			$count = parent::get_setting($block, 'posts-per-block', '4');
			$columns = parent::get_setting($block, 'columns', '4');

			$rough_row_count = ceil($count / $columns);

			$row_first_classes = array();
			/* Build array with classes */
			for ($i=1; $i <= $rough_row_count; $i++) {
				if ($i == 1) 
					$row_first_classes[] = '#block-' . $block_id . ' .article.article-1';
				if ($i == 2) :
					$item = ($columns)+1;
					$row_first_classes[] = '#block-' . $block_id . ' .article.article-' . $item . '';
				elseif ($i >= 3) :
					$item = $item+$columns;
					$row_first_classes[] = '#block-' . $block_id . ' .article.article-' . $item . '';
				endif;
			}
			$css ='';
			/* css if the items are floated as a grid */
			if ($stack_or_float == 'float') {
				$css .= '#block-' . $block_id . ' .article {
				margin-left: ' . self::widthAsPercentage($gutter_width, $block) . '%;
				float:left;
				width: ' . self::widthAsPercentage(self::getColumnWidth(0, $block), $block) . '%;
				}
				/* remove margin on first items */
				'. implode(',', $row_first_classes) .' {margin-left: 0!important;}';
			} else if ($stack_or_float == 'stack') {
				$css .= '#block-' . $block_id . ' .article {
				margin-left:0;
				width: 100%;
				}';
			}
			$css .= '#block-' . $block_id . ' .article {';
			
			$css .= 'margin-bottom: ' . parent::get_setting($block, 'bottom-margin', '20') . 'px;';

			if ($min_height)
				$css .= 'min-height: ' . parent::get_setting($block, 'minimum-height') . 'px;';

			$css .= '}';

			/* Thumb alignment */
			$auto_size = parent::get_setting($block, 'thumb-size-auto', false);
			$position = parent::get_setting($block, 'thumb-align', 'none');
			$position_css = 'float: ' . $position . '';

				/* Output Thumb CSS */
				if ($position != 'left' || $position != 'right')
				$position_css = false;
				$css .= '#block-' . $block_id . ' .article figure a { 
					' . $position_css;
				if (!$auto_size)
				$css .= '
					width: '. parent::get_setting($block, 'thumb-width') .'px;
					height: auto;';
				$css .= '
				}'; /* end thumb css */

			/* Overlay */
			$hover_overlay = parent::get_setting($block, 'thumb-hover-overlay', false);
			$icon_size = parent::get_setting($block, 'thumb-overlay-iconsize', 36);
			$icon_class = self::get_setting($block, 'thumb-hover-iconclass', 'link');
			if($hover_overlay)
				$css .= '
					@font-face {
				  font-family: \'fontello\';
				  src: url(\''.plugins_url(false, __FILE__).'/css/font/fontello.eot\');
				  src: url(\''.plugins_url(false, __FILE__).'/css/font/fontello.eot?#iefix\') format(\'embedded-opentype\'), url(\''.plugins_url(false, __FILE__).'/css/font/fontello.woff\') format(\'woff\'), url(\''.plugins_url(false, __FILE__).'/css/font/fontello.ttf\') format(\'truetype\'), url(\''.plugins_url(false, __FILE__).'/css/font/fontello.svg#fontello\') format(\'svg\');
				  font-weight: normal;
				  font-style: normal;
				}
				#block-' . $block_id . ' .icon-'. $icon_class .' {width:' . $icon_size . 'px;height:' . $icon_size . 'px;}
				#block-' . $block_id . ' .icon-'. $icon_class .':before { font-size: ' . $icon_size . 'px;}
				';

			/* Mobile CSS - some magic to make the columns work with the smartphone setting */
			$breakpoint_smartphone = parent::get_setting($block, 'breakpoint-smartphone', '600');

				/* Output Mobile CSS */
				$css .= '@media screen and (max-width: ' . $breakpoint_smartphone . 'px) { ';
				
				$css .= '#block-' . $block_id . ' .article {';
					if (parent::get_setting($block, 'columns-smartphone') == 1) :
						$css .= 
						'width: 99.6%;
						float: none;
						margin-left: 0;';
					else :
						$css .= 
						'margin-left: '.self::widthAsPercentage($gutter_width, $block).'%;
						width: '.self::widthAsPercentage(self::getColumnWidth(1, $block), $block).'%;';
					endif;
				
				$css .= '}';//close .article
					
				$css .= '}';//close media query


		return $css;
		
	}
	
	function content($block) {

		self::$block = $block;
		
		$block_display = new HeadwayPostListingsBlockDisplay($block);
		echo parent::get_setting($block, 'before-content', false);
		$block_display->display($block);
		echo parent::get_setting($block, 'after-content', false);
		
	}

	static function getColumnWidth($mobile = false, $block) {
		$block_width = HeadwayBlocksData::get_block_width($block);
		if ($mobile == 1) :
			$columns = parent::get_setting($block, 'columns-smartphone', '2');
		else :
			$columns = parent::get_setting($block, 'columns', '4');
		endif;
		$gutter_width = parent::get_setting($block, 'gutter-width', '20');

		$total_gutter = $gutter_width * ($columns-1);

		$columns_width = (($block_width - $total_gutter) / $columns);

		return $columns_width; 
	}

	/* To make the layout responsive
	 * Works out a percentage value equivalent of the px value 
	 * using common responsive formula: target_width / container_width * 100
	 */	
	static function widthAsPercentage($target = '', $block) {
		$block_width = HeadwayBlocksData::get_block_width($block);
		return ($target / $block_width)*100;
	}

	function setup_elements() {
		
		$this->register_block_element(array(
			'id' => 'articles-wrapper',
			'name' => 'Articles Wrapper',
			'selector' => '.articles'
		));

		$this->register_block_element(array(
			'id' => 'article-container',
			'name' => 'Article Container',
			'selector' => 'article',
			'states' => array(
				'Hover' => 'article:hover',
				'Hover all children' => 'article:hover *'
			),
			'properties' => array('fonts', 'borders', 'background', 'padding', 'rounded-corners', 'box-shadow', 'text-shadow'),
		));

		$this->register_block_element(array(
			'id' => 'article-header',
			'name' => 'Article Header',
			'selector' => 'article header'
		));

		$this->register_block_element(array(
			'id' => 'article-section',
			'name' => 'Article Section',
			'selector' => 'article section'
		));

		$this->register_block_element(array(
			'id' => 'article-footer',
			'name' => 'Artice Footer',
			'selector' => 'article footer'
		));

		$this->register_block_element(array(
			'id' => 'article-title',
			'name' => 'Article Title (Link)',
			'selector' => 'article .entry-title a'
		));

		$this->register_block_element(array(
			'id' => 'article-thumb',
			'name' => 'Article Thumb',
			'selector' => 'article figure img'
		));

		$this->register_block_element(array(
			'id' => 'thumb-overlay',
			'name' => 'Thumb Overlay',
			'selector' => '.article figure a > span',
			'properties' => array('background')
		));

		$this->register_block_element(array(
			'id' => 'thumb-overlay-icon',
			'name' => 'Thumb Overlay Icon',
			'selector' => '.articles article figure a span > span',
			'properties' => array('fonts', 'text-shadow')
		));

		$this->register_block_element(array(
			'id' => 'article-author-link',
			'name' => 'Author Link',
			'selector' => 'article .author-link'
		));

		$this->register_block_element(array(
			'id' => 'article-author-avatar-link',
			'name' => 'Author Avatar Link',
			'selector' => 'article .author-avatar'
		));

		$this->register_block_element(array(
			'id' => 'article-author-avatar-img',
			'name' => 'Author Avatar Image',
			'selector' => 'article .author-avatar img'
		));

		$this->register_block_element(array(
			'id' => 'article-more-link',
			'name' => 'Read More Link',
			'selector' => 'article .more-link',
			'states' => array(
				'Hover' => 'article .more-link:hover'
			),
		));

		$this->register_block_element(array(
			'id' => 'article-date',
			'name' => 'Date Text',
			'selector' => 'article .date'
		));

		$this->register_block_element(array(
			'id' => 'article-comments-link',
			'name' => 'Comments Link',
			'selector' => 'article a.entry-comments'
		));

		$this->register_block_element(array(
			'id' => 'article-comments-time',
			'name' => 'Time Text',
			'selector' => 'article .entry-time'
		));

		$this->register_block_element(array(
			'id' => 'article-time-since',
			'name' => 'Time Since Link',
			'selector' => 'article .time-since a'
		));

		$this->register_block_element(array(
			'id' => 'article-categories-wrapper',
			'name' => 'Categories Wrapper',
			'selector' => 'article .categories-wrap'
		));

		$this->register_block_element(array(
			'id' => 'article-categories',
			'name' => 'Categories Links',
			'selector' => 'article a.categories'
		));

		$this->register_block_element(array(
			'id' => 'article-excerpt',
			'name' => 'Excerpt Text',
			'selector' => 'article .excerpt'
		));
		
	}
	
	
}