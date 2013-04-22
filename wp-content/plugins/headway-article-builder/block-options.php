<?php
/* This class must be included in another file and included later so we don't get an error about HeadwayBlockOptionsAPI class not existing. */

class HeadwayArticleBuilderBlockOptions extends HeadwayBlockOptionsAPI {

	function modify_arguments($args) {

		$block = HeadwayBlocksData::get_block($args['block_id']);

		$this->tab_notices['layout'] = '
		If you use the float (grid) layout, you can either display articles in one row, or multiple rows. Example: to add 2 rows of 4 articles you would have 8 articles so set "Articles per block" to 8 and "Articles per row" to 4.
		';

		$this->tab_notices['builder'] = '
			<a href="" class="addtag" rel="[title]" data-position="header" active="0">title</a>
			<a href="" class="addtag" rel="[thumb]" data-position="section" active="0">thumb</a>
			<a href="" class="addtag" rel="[excerpt]" data-position="section" active="0">excerpt</a>
			<a href="" class="addtag" rel="[readmore]" data-position="footer" active="0">readmore</a>
			<a href="" class="addtag" rel="[date]" data-position="header" active="0">date</a>
			<a href="" class="addtag" rel="[time]" data-position="header" active="0">time</a>
			<a href="" class="addtag" rel="[category]" data-position="header" active="0">category</a>
			<a href="" class="addtag" rel="[author]" data-position="footer" active="0">author</a>
			<a href="" class="addtag" rel="[avatar]" data-position="footer" active="0">avatar</a>
			<a href="" class="addtag" rel="[comments]" data-position="footer" active="0">comments</a>';

		$this->inputs['date-options']['meta-date-format']['options'] = array(
			'wordpress-default' => 'WordPress Default',
			'F j, Y' => date('F j, Y'),
			'm/d/y' => date('m/d/y'),
			'd/m/y' => date('d/m/y'),
			'M j' => date('M j'),
			'M j, Y' => date('M j, Y'),
			'F j' => date('F j'),
			'F jS' => date('F jS'),
			'F jS, Y' => date('F jS, Y')
		);

		$this->inputs['time-options']['meta-time-format']['options'] = array(
			'wordpress-default' => 'WordPress Default',
			'g:i A' => date('g:i A'),
			'g:i A T' => date('g:i A T'),
			'g:i:s A' => date('g:i:s A'),
			'G:i' => date('G:i'),
			'G:i T' => date('G:i T')
		);

	}

	public $open_js_callback = '
		(function($) {
			$(document).ready(function() {
				addBuilderHeadings(blockID);
				setRows();

				/* add and remove options */
				var header = $("#block-" + blockID + "-tab #input-builder-input-header textarea").text();
				var section = $("#block-" + blockID + "-tab #input-builder-input-section textarea").text();
				var footer = $("#block-" + blockID + "-tab #input-builder-input-footer textarea").text();
				shortcodes = header+section+footer;
				addRemoveShortcodeOptions(shortcodes, blockID);

				/* add tags on click */
				/* set variables */
				window["block_header" + blockID + "textarea"] =  $("#input-" + blockID + "-builder-input-header");
				window["block_section" + blockID + "textarea"] = $("#input-" + blockID + "-builder-input-section");
				window["block_footer" + blockID + "textarea"] = $("#input-" + blockID + "-builder-input-footer");
				window["block" + blockID + "toggler"] = $("#block-" + blockID + "-tab .sub-tabs-content a.addtag");

				/* 1. Set correct values on block options first load */
				$(".addtag").each(function(){
					var addval = $(this).attr("rel");
					var position = $(this).data("position");

					if (position == \'header\') {
						var isContains = $(window["block_header" + blockID + "textarea"]).val().indexOf(addval) > -1;
					}
					if (position == \'section\') {
						var isContains = $(window["block_section" + blockID + "textarea"]).val().indexOf(addval) > -1;
					}
					
					if (position == \'footer\') {
						var isContains = $(window["block_footer" + blockID + "textarea"]).val().indexOf(addval) > -1;
					}

					if(isContains)
					$(this).attr("active", "1").addClass("active");
				});
				
				/* 2. Click event to add remove tags */

				window["block" + blockID + "toggler"].click(function() {
					var active = $(this).attr("active");
					var addval = $(this).attr("rel");
					var position = $(this).data("position");

					/* if not active: add class and shortcode to input */
					if(active == 0) {
						$(this).attr("active", "1").addClass("active");
						
						if (position == \'header\') {
							window["origin" + blockID] = window["block_header" + blockID + "textarea"].val();
							window["block_header" + blockID + "textarea"].val(window["origin" + blockID] +  addval);
							var input = $("#input-" + blockID + "-builder-input-header");
						}
						if (position == \'section\') {
							window["origin" + blockID] = window["block_section" + blockID + "textarea"].val();
							window["block_section" + blockID + "textarea"].val(window["origin" + blockID] +  addval);
							var input = $("#input-" + blockID + "-builder-input-section");
						}
						
						if (position == \'footer\') {
							window["origin" + blockID] = window["block_footer" + blockID + "textarea"].val();
							window["block_footer" + blockID + "textarea"].val(window["origin" + blockID] +  addval);
							var input = $("#input-" + blockID + "-builder-input-footer");
						}

					/* if is active: remove class and value from input */
					} else if(active == 1) {
						$(this).attr("active", "0").removeClass("active");

						if (position == \'header\') {
							window["block_header" + blockID + "textarea"].val( window["block_header" + blockID + "textarea"].val().replace(addval, "") );
							var input = $("#input-" + blockID + "-builder-input-header");
						}
						if (position == \'section\') {
							window["block_section" + blockID + "textarea"].val( window["block_section" + blockID + "textarea"].val().replace(addval, "") );
							var input = $("#input-" + blockID + "-builder-input-section");
						}
						
						if (position == \'footer\') {
							window["block_footer" + blockID + "textarea"].val( window["block_footer" + blockID + "textarea"].val().replace(addval, "") );
							var input = $("#input-" + blockID + "-builder-input-footer");
						}
						
					}

					/* Final Step: Save Values and Reload Panel */
					var inputVal = input.val();
					updatePanelInputHidden({input: input, value: inputVal});
					reloadBlockOptions(blockID);

					return false;
				});
			});
		})(jQuery);
	';
	
	
	public $tabs = array(
		'content' => '1) Filter Content',
		'layout' => '2) Build Layout',
		'builder' => '3) Build Articles',
		'title-options'		=> '- Title',
		'thumb-options'		=> '- Thumb',
		'excerpt-options'		=> '- Excerpt',
		'readmore-options'		=> '- Read More',
		'date-options'		=> '- Date',
		'time-options'		=> '- Time',
		'category-options'		=> '- Category',
		'author-options'		=> '- Author',
		'avatar-options' => '- Avatar',
		'comments-options'		=> '- Comments'
	);

	public $tab_notices = array(
		'layout' => '',
		'builder' => '',		
	);

	public $inputs = array(
		'builder' => array(
			
			'builder-input-header' => array(
				'type' => 'textarea',
				'name' => 'builder-input-header',
				'label' => 'Header',
				'default' => '[title]',
				'tooltip' => ''
			),
			'builder-input-section' => array(
				'type' => 'textarea',
				'name' => 'builder-input-section',
				'label' => 'Content',
				'default' => '[thumb][excerpt]',
				'tooltip' => ''
			),
			'builder-input-footer' => array(
				'type' => 'textarea',
				'name' => 'builder-input-footer',
				'label' => 'Footer',
				'default' => '[readmore]',
				'tooltip' => ''
			)
		),
		'layout' => array(
			'orientation-header' => array(
				'type' => 'heading',
				'name' => 'orientation-header',
				'label' => 'Orientation',
			),
			'stack-or-float' => array(
				'type' => 'select',
				'name' => 'stack-or-float',
				'label' => 'Stack or float',
				'tooltip' => '',
				'options' => array(
					'stack' => 'Stack',
					'float' => 'Float'
				), 
				'default' => 'float',
				'toggle'    => array(
					'stack' => array(
						'hide' => array(
							'#input-columns-header',
							'#input-posts-per-block',
							'#input-columns',
							'#input-breakpoint-smartphone',
							'#input-columns-smartphone',
							'#input-mobile-header',
							'#row-count'
						),
					),
					'float' => array(
						'show' => array(
							'#input-columns-header',
							'#input-posts-per-block',
							'#input-columns',
							'#input-breakpoint-smartphone',
							'#input-columns-smartphone',
							'#input-mobile-header',
							'#row-count'
						),
					)
				),
				'tooltip' => 'Display articles on top of each other (stack) or side by side as a grid (float)',
				'callback' => 'id = $(input).attr("block_id");'
			),

			'columns-header' => array(
				'type' => 'heading',
				'name' => 'columns-header',
				'label' => 'Columns and Items',
			),

			'posts-per-block' => array(
				'type' => 'slider',
				'name' => 'posts-per-block',
				'label' => 'Articles Per Block',
				'slider-min' => 1,
				'slider-max' => 100,
				'slider-interval' => 1,
				'tooltip' => '',
				'default' => 4,
				'tooltip' => 'How many articles to show per block.',
				'callback' => 'setRows(value)'
			),
			
			'columns' => array(
				'type' => 'slider',
				'name' => 'columns', 
				'label' => 'Articles Per Row (Columns)',
				'slider-min' => 1,
				'slider-max' => 12,
				'slider-interval' => 1,
				'default' => 4,
				'tooltip' => 'Set how many articles to display per row.',
				'callback' => 'setRows(value)'
			),

			'mobile-header' => array(
				'type' => 'heading',
				'name' => 'mobile-header',
				'label' => 'Mobile',
			),

			'columns-smartphone' => array(
				'type' => 'slider',
				'name' => 'columns-smartphone', 
				'label' => 'Columns (iPhone/Smartphone)',
				'slider-min' => 1,
				'slider-max' => 8,
				'slider-interval' => 1,
				'default' => 2,
				'tooltip' => 'NB! Headway needs to be set to responsive in the grid first. Set how many articles to display horizontally for iPhones and smartphones.  <strong>Recommended setting: 1 or 2</strong>'
			),

			'breakpoint-smartphone' => array(
				'type' => 'select',
				'name' => 'breakpoint-smartphone',
				'label' => 'Breakpoint to switch columns',
				'options' => array(
					'640' => '640',
					'600' => '600',
					'480' => '480',
					'400' => '400'
				),
				'tooltip' => 'The device width set here is the point at which the columns will switch from the normal screen size columns to what you set in the previous setting. Any screen smaller that this breakpoint will then display what you set in Columns (iPhone/Smartphone).'
			),

			'spacing-header' => array(
				'type' => 'heading',
				'name' => 'spacing-header',
				'label' => 'Spacing',
			),

			'gutter-width' => array(
				'type' => 'slider',
				'name' => 'gutter-width', 
				'label' => 'Gutter Width',
				'slider-min' => 0,
				'slider-max' => 100,
				'slider-interval' => 1,
				'default' => 15,
				'unit' => 'px',
				'tooltip' => 'The amount of horizontal spacing between articles.'
			),

			'bottom-margin' => array(
				'type' => 'slider',
				'name' => 'bottom-margin', 
				'label' => 'Articles Bottom Margin',
				'slider-min' => 0,
				'slider-max' => 50,
				'slider-interval' => 1,
				'default' => 15,
				'unit' => 'px',
				'tooltip' => 'The amount of space on the bottom of each pin.'
			),

			'minimum-height' => array(
				'type' => 'text',
				'name' => 'minimum-height', 
				'label' => 'Minimum height (px)',
				'default' => '',
				'tooltip' => 'You can set a minimum height in pixels for all articles in this block. If you add a border around each article for example this may be useful to make them all the same height.'
			),

			'before-after-content-header' => array(
				'type' => 'heading',
				'name' => 'before-after-content-header',
				'label' => 'Before and After Articles',
			),

			'before-content' => array(
				'type' => 'wysiwyg',
				'name' => 'before-content', 
				'label' => 'Before Articles Content',
				'default' => '',
				'tooltip' => 'Text that is added before the content articles are added to the block'

			),

			'after-content' => array(
				'type' => 'wysiwyg',
				'name' => 'after-content', 
				'label' => 'After Articles Content',
				'default' => '',
				'tooltip' => 'Text that is added after the content articles are added to the block'
			),
			
		),

		'content' => array(

			'content-filter' => array(
				'type' => 'heading',
				'name' => 'content-filter',
				'label' => 'Filter',
			),

			'categories' => array(
				'type' => 'multi-select',
				'name' => 'categories',
				'label' => 'Categories',
				'tooltip' => '',
				'options' => 'get_categories()',
				'tooltip' => 'Filter the articles that are shown by categories.'
			),
			
			'categories-mode' => array(
				'type' => 'select',
				'name' => 'categories-mode',
				'label' => 'Categories Mode',
				'tooltip' => '',
				'options' => array(
					'include' => 'Include',
					'exclude' => 'Exclude'
				),
				'tooltip' => 'If this is set to <em>include</em>, then only the articles that match the categories filter will be shown.  If set to <em>exclude</em>, all articles that match the selected categories will not be shown.'
			),
			
			'post-type' => array(
				'type' => 'multi-select',
				'name' => 'post-type',
				'label' => 'Post Type',
				'tooltip' => '',
				'options' => 'get_post_types()',
				'tooltip' => 'Select a post type to show articles from.',
				'callback' => 'id = $(input).attr("block_id");
					removePanelTab("block-"+id);openBlockOptions($i("#block-"+id))'
			),
			
			'author' => array(
				'type'    => 'multi-select',
				'name'    => 'author',
				'label'   => 'Author',
				'tooltip' => '',
				'options' => 'get_authors()'
			),

			'post_id' => array(
				'type' => 'text',
				'name' => 'post_id',
				'label' => 'Post ID',
				'default' => '',
				'tooltip' => 'Show only a single post with this ID',
			),

			'content-ordering' => array(
				'type' => 'heading',
				'name' => 'content-ordering',
				'label' => 'Order',
			),
			
			'order-by' => array(
				'type' => 'select',
				'name' => 'order-by',
				'label' => 'Order By',
				'tooltip' => '',
				'options' => array(
					'date' => 'Date',
					'title' => 'Title',
					'rand' => 'Random',
					'id' => 'ID'
				)
			),
			
			'order' => array(
				'type'    => 'select',
				'name'    => 'order',
				'label'   => 'Order',
				'tooltip' => '',
				'options' => array(
					'desc' => 'Descending',
					'asc' => 'Ascending',
				)
			)
		),

		'title-options' => array (
			
			'title-link' => array(
				'type'  => 'checkbox',
				'name'  => 'title-link',
				'label' => 'Link Title?'
			),

			'title-html-tag' => array(
				'type' => 'select',
				'name' => 'title-html-tag',
				'label' => 'Title HTML tag',
				'default' => 'h1',
				'options' => array(
					'h1' => '&lt;H1&gt;',
					'h2' => '&lt;H2&gt;',
					'h3' => '&lt;H3&gt;',
					'h4' => '&lt;H4&gt;',
					'h5' => '&lt;H5&gt;',
					'h6' => '&lt;H6&gt;',
					'span' => '&lt;span&gt;'
				)
			),

			'title-shorten' => array(
				'type'  => 'checkbox',
				'name'  => 'title-shorten',
				'label' => 'Truncate Title?',
				'toggle'    => array(
					'true' => array(
						'show' => array(
							'#input-title-limit'
						),
					),
					'false' => array(
						'hide' => array(
							'#input-title-limit'
						),
					)
				),
			),

			'title-limit' => array(
				'type' => 'text',
				'name' => 'title-limit', 
				'label' => 'Limit characters',
				'default' => '20',
			),


			
		),

		'comments-options' => array (

			'comments-before-text' => array(
				'type'    => 'text',
				'name'    => 'comments-before-text',
				'label'   => 'Comments before text',
				'tooltip' => 'Set text to display before comments show.'
			),
			
			'comment-format' => array(
				'type'    => 'text',
				'label'   => 'Comment Format &ndash; More Than 1 Comment',
				'name'    => 'comment-format',
				'default' => '%num% Comments'
			),
			
			'comment-format-1' => array(
				'type'    => 'text',
				'label'   => 'Comment Format &ndash; 1 Comment',
				'name'    => 'comment-format-1',
				'default' => '%num% Comment'
			),
			
			'comment-format-0' => array(
				'type'    => 'text',
				'label'   => 'Comment Format &ndash; 0 Comments',
				'name'    => 'comment-format-0',
				'default' => '%num% Comments'
			),
			
		),

		'excerpt-options' => array (
			
			'content-to-show' => array(
				'type'    => 'select',
				'name'    => 'content-to-show', 
				'label'   => 'Content To Show',
				'options' => array(
					'excerpt' => 'Excerpts',
					'content' => 'Full Content'
				),
				'toggle'    => array(
					'excerpt' => array(
						'show' => array(
							'#input-excerpt-length',
							'#input-excerpt-more'
						),
					),
					'content' => array(
						'hide' => array(
							'#input-excerpt-length',
							'#input-excerpt-more'
						),
					)
				),
				'default' => 'excerpt',
				'tooltip' => 'The excerpt will display the content but truncate it to the relevant value. Showing full content will display content up to where the &lt;!--more--&gt; tag.'
			),

			'excerpt-length' => array(
				'type'    => 'text',
				'name'    => 'excerpt-length',
				'label'   => 'Excerpt length',
				'default' => '50',
				'tooltip' => 'How many words to trim the excerpt to. If you have chosen excerpt as the content to show.'
			),

			'excerpt-more' => array(
				'type'    => 'text',
				'name'    => 'excerpt-more',
				'label'   => 'Excerpt More',
				'default' => '...',
				'tooltip' => 'What to display when excerpt is truncated?'
			)
			
		),

		'readmore-options' => array (
			
			'read-more-text' => array(
				'type'    => 'text',
				'label'   => 'Read More Text',
				'name'    => 'read-more-text',
				'default' => 'Read more',
				'tooltip' => 'If excerpts are being shown or a featured post is truncated using WordPress\' read more shortcode, then this will be shown after the excerpt or truncated content.'
			),
			
		),

		'date-options' => array (

			'date-before-text' => array(
				'type'    => 'text',
				'name'    => 'date-before-text',
				'label'   => 'Date before text',
				'tooltip' => 'Set text to display before date shows.'
			),
			
			'meta-date-format' => array(
				'type' => 'select',
				'name' => 'meta-date-format',
				'label' => 'Date Format'
			)
			
		),

		'time-options' => array (

			'time-before-text' => array(
				'type'    => 'text',
				'name'    => 'time-before-text',
				'label'   => 'Time before text',
				'tooltip' => 'Set text to display before time shows.'
			),

			'time-timesince' => array(
				'type'  => 'checkbox',
				'name'  => 'time-timesince',
				'default' => 'true',
				'label' => 'Show time as time since?',
				'toggle'    => array(
					'true' => array(
						'hide' => array(
							'#input-meta-time-format'
						),
					),
					'false' => array(
						'show' => array(
							'#input-meta-time-format'
						),
					)
				),
			),

			'meta-time-format' => array(
				'type' => 'select',
				'name' => 'meta-time-format',
				'label' => 'Time Format'
			),
			
		),

		'category-options' => array (

			'category-before-text' => array(
				'type'    => 'text',
				'name'    => 'category-before-text',
				'label'   => 'Category before text',
				'tooltip' => 'Set text to display before categories show.'
			)
			
		),

		'author-options' => array (
			
			'author-before-text' => array(
				'type'    => 'text',
				'name'    => 'author-before-text',
				'label'   => 'Author before text',
				'tooltip' => 'Set text to display before author show.'
			),

			'author-link' => array(
				'type'  => 'checkbox',
				'name'  => 'author-link',
				'label' => 'Link Author?'
			),
			
		),

		'avatar-options' => array (
			
			'author-avatar-before-text' => array(
				'type'    => 'text',
				'name'    => 'author-avatar-before-text',
				'label'   => 'Avatar before text',
				'tooltip' => 'Set text to display before author show.'
			),

			'author-avatar-link' => array(
				'type'  => 'checkbox',
				'name'  => 'author-avatar-link',
				'label' => 'Link Avatar?'
			),

			'author-avatar-size' => array(
				'type' => 'slider',
				'name' => 'author-avatar-size', 
				'label' => 'Thumb Width',
				'slider-min' => 16,
				'slider-max' => 192,
				'slider-interval' => 8,
				'default' => '32',
				'tooltip' => 'Size of the avatar'
			),
			
		),

		'thumb-options' => array (

			'thumb-size-header' => array(
				'type' => 'heading',
				'name' => 'thumb-size-header',
				'label' => 'Thumbnail Size',
			),
			
			'thumb-size-auto' => array(
				'type'    => 'checkbox',
				'name'    => 'thumb-size-auto',
				'label'   => 'Size Thumb Automatically?',
				'default' => true,
				'tooltip' => 'When set to automatic, the image is resized to the width of it\'s container. Disable automatic sizing to add your own thumb width and height. ',
				'toggle'    => array(
					'true' => array(
						'hide' => array(
							'#input-thumb-align',
							'#input-thumb-width',
							'#input-thumb-height'
						),
					),
					'false' => array(
						'show' => array(
							'#input-thumb-align',
							'#input-thumb-width',
							'#input-thumb-height'
						),
					)
				)
			),

			'thumb-align' => array(
				'type'    => 'select',
				'name'    => 'thumb-align',
				'label'   => 'Thumb Alignment',
				'default' => 'none',
				'options' => array(
					'left'          => 'Left',
					'right'         => 'Right',
					'center'         => 'Center',
					'none' => 'No Alignment'
				),
				'tooltip' => 'If you select center, then you need to manually size the image by turning off auto sizing so it will align correctly.'
			),

			'thumb-width' => array(
				'type' => 'slider',
				'name' => 'thumb-width', 
				'label' => 'Thumb Width',
				'slider-min' => 0,
				'slider-max' => 1000,
				'slider-interval' => 1,
				'default' => '120',
				'tooltip' => 'Set a width for the image thumbnail'
			),

			'thumb-height' => array(
				'type' => 'slider',
				'name' => 'thumb-height', 
				'label' => 'Thumb Height',
				'slider-min' => 0,
				'slider-max' => 700,
				'slider-interval' => 1,
				'default' => '80',
				'tooltip' => 'Set a height for the image thumbnail.'
			),

			'thumb-crop-vertically' => array(
				'type'    => 'checkbox',
				'name'    => 'thumb-crop-vertically', 
				'label'   => 'Crop Vertically',
				'default' => true,
				'tooltip' => 'Trim all images to have the same height.  The trimmed/cropped height is roughly 75% of the width.',
				'toggle'    => array(
					'true' => array(
						'show' => '#input-post-thumbnail-height-ratio'
					),
					'false' => array(
						'hide' => '#input-post-thumbnail-height-ratio'
					)
				),
			),

			'post-thumbnail-height-ratio' => array(
				'type' => 'slider',
				'name' => 'post-thumbnail-height-ratio',
				'label' => 'Image Height Ratio',
				'default' => 75,
				'slider-min' => 10,
				'slider-max' => 200,
				'slider-interval' => 5,
				'tooltip' => 'Adjust the height of feature images when set to the above title or above content positions.  This value controls what percent the height of the image will be in regards to the width of the block.<br /><br />Example: If the block width is 500 pixels and the ratio is 50% then the feature image size will be 500px by 250px.',
				'unit' => '%'
			),

			'thumb-hover-header' => array(
				'type' => 'heading',
				'name' => 'thumb-hover-header',
				'label' => 'Thumbnail Hover',
			),
			
			'thumb-hover-overlay' => array(
				'type'    => 'checkbox',
				'name'    => 'thumb-hover-overlay', 
				'label'   => 'Add hover effect',
				'default' => false,
				'toggle'    => array(
					'true' => array(
						'show' => array(
							'#input-thumb-hover-iconclass',
							'#input-thumb-overlay-iconsize'
						),
					),
					'false' => array(
						'hide' => array(
							'#input-thumb-hover-iconclass',
							'#input-thumb-overlay-iconsize'
						),
					)
				),
				'tooltip' => 'Enable this option to add an overlay on hover that overlays the image with an icon that links to the content page.'
			),

			'thumb-hover-iconclass' => array(
				'type' => 'select',
				'name' => 'thumb-hover-iconclass', 
				'label' => 'Icon',
				'default' => 'right-circle',
				'options' => array(
					'right' => 'Right Arrow',
					'plus-circled' => 'Plus Circled',
					'plus-squared' => 'Plus Squares',
					'right-circle' => 'Right Arrow Circle',
					'info-circled' => 'Info Circle',
					'popup' => 'Popup',
					'eye' => 'Eye',
					'export' => 'Go to link',
					'zoom-in-1' => 'Zoom 1',
					'zoom-in-2' => 'Zoom 2',
					'beaker' => 'Labs',
					'newspaper' => 'News'
				)
			),

			'thumb-overlay-iconsize' => array(
				'type' => 'slider',
				'name' => 'thumb-overlay-iconsize',
				'label' => 'Size of icon in overlay',
				'slider-min' => 1,
				'slider-max' => 200,
				'slider-interval' => 1,
				'tooltip' => '',
				'default' => 30,
				'tooltip' => 'Set a size for the icon in the overlay.',
			),
			
		)


	);

	function get_categories() {
		
		$category_options = array();
		
		$categories_select_query = get_categories();
		
		foreach ($categories_select_query as $category)
			$category_options[$category->term_id] = $category->name;

		return $category_options;
		
	}
	
	
	function get_authors() {
		
		$author_options = array();
		
		$authors = get_users(array(
			'orderby' => 'post_count',
			'order' => 'desc',
			'who' => 'authors'
		));
		
		foreach ( $authors as $author )
			$author_options[$author->ID] = $author->display_name;
			
		return $author_options;
		
	}
	
	
	function get_post_types() {
		
		$post_type_options = array();

		$post_types = get_post_types(false, 'objects'); 
			
		foreach($post_types as $post_type_id => $post_type){
			
			//Make sure the post type is not an excluded post type.
			if(in_array($post_type_id, array('revision', 'nav_menu_item'))) 
				continue;
			
			$post_type_options[$post_type_id] = $post_type->labels->name;
		
		}
		
		return $post_type_options;
		
	}
	
	
}