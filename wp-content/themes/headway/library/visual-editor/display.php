<?php
class HeadwayVisualEditorDisplay {
	
	
	public static function init() {
						
		//System for scripts/styles		
		add_action('headway_visual_editor_head', array(__CLASS__, 'print_styles'), 12);
		add_action('headway_visual_editor_footer', array(__CLASS__, 'print_scripts'), 12);

		//Enqueue Styles
		remove_all_actions('wp_print_styles'); /* Removes bad plugin CSS */
		add_action('headway_visual_editor_styles', array(__CLASS__, 'enqueue_styles'));

		//Enqueue Scripts
		remove_all_actions('wp_print_scripts'); /* Removes bad plugin JS */
		add_action('headway_visual_editor_scripts', array(__CLASS__, 'enqueue_scripts'));

		//Localize Scripts
		add_action('headway_visual_editor_scripts', array(__CLASS__, 'add_visual_editor_js_vars'));

		//Content
		add_action('headway_visual_editor_modes', array(__CLASS__, 'mode_navigation'));
		add_action('headway_visual_editor_menu_links', array(__CLASS__, 'menu_links'));
		add_action('headway_visual_editor_page_switcher', array(__CLASS__, 'page_switcher_page'));
		add_action('headway_visual_editor_footer', array(__CLASS__, 'layout_selector'));
		add_action('headway_visual_editor_footer', array(__CLASS__, 'block_type_selector'));

		add_action('headway_visual_editor_panel_top', array(__CLASS__, 'panel_top_right'), 12);		
		add_action('headway_visual_editor_panel_top', array(__CLASS__, 'panel_top_mode_buttons'), 13);

		add_action('headway_visual_editor_tips', array(__CLASS__, 'get_a_tip'));

		add_action('headway_visual_editor_panel_top', array(__CLASS__, 'add_default_panel_link'));
		add_action('headway_visual_editor_content', array(__CLASS__, 'add_default_panel'));

	}


	public static function display() {

		do_action('headway_visual_editor_display');

		require_once 'template.php';

	}


	public static function enqueue_scripts() {
				
		HeadwayCompiler::register_file(array(
			'name' => 've-js',
			'format' => 'js',
			'fragments' => array(
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.js',

				HEADWAY_LIBRARY_DIR . '/media/js/itstylesheet.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jsuri.js',

				HEADWAY_LIBRARY_DIR . '/media/js/jquery.ui.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.iframe.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.qtip.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.cookie.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.masonry.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.scrollbarpaper.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.tabby.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.hotkeys.js',
				HEADWAY_LIBRARY_DIR . '/media/js/jquery.debounce.js',

				HEADWAY_LIBRARY_DIR . '/media/js/colorpicker.js',
				
				HEADWAY_LIBRARY_DIR . '/media/js/codemirror/codemirror.js',
				HEADWAY_LIBRARY_DIR . '/media/js/codemirror/languages/css.js',
				
				HEADWAY_LIBRARY_DIR . '/visual-editor/js/editor.js',
				HEADWAY_LIBRARY_DIR . '/visual-editor/js/editor.inputs.js',
				HEADWAY_LIBRARY_DIR . '/visual-editor/js/editor.tour.js',
				HEADWAY_LIBRARY_DIR . '/visual-editor/js/editor.fonts.js',
				HEADWAY_LIBRARY_DIR . '/visual-editor/js/editor.functions.js',

				HEADWAY_LIBRARY_DIR . '/visual-editor/js/editor.grid.js',

				HEADWAY_LIBRARY_DIR . '/visual-editor/js/editor.mode.grid.js',
				HEADWAY_LIBRARY_DIR . '/visual-editor/js/editor.mode.design.js'
			),
			'require-hard-flush' => true
		));
				
	}
	
	
	public static function enqueue_styles() {

		HeadwayCompiler::register_file(array(
			'name' => 've',
			'format' => 'less',
			'fragments' => apply_filters('headway_visual_editor_css', array(
				HEADWAY_LIBRARY_DIR . '/visual-editor/css/editor-mixins.less',
				HEADWAY_LIBRARY_DIR . '/visual-editor/css/editor-tooltips.less',
				HEADWAY_LIBRARY_DIR . '/visual-editor/css/editor.less',
				HEADWAY_LIBRARY_DIR . '/visual-editor/css/editor-inputs.less',
				HEADWAY_LIBRARY_DIR . '/visual-editor/css/editor-design.less',
				
				HEADWAY_LIBRARY_DIR . '/media/js/codemirror/codemirror.css',
				HEADWAY_LIBRARY_DIR . '/media/js/codemirror/theme-default.css'
			)),
			'require-hard-flush' => true,
			'enqueue' => false
		));

		$styles = array(
			'reset' => headway_url() . '/library/media/css/reset.css',
			'headway_visual_editor_loading' => headway_url() . '/library/visual-editor/css/editor-loading.css',
			'headway_visual_editor' => HeadwayCompiler::get_url('ve')
		);
		
		wp_enqueue_multiple_styles($styles);
		
	}
	
	
	public static function print_scripts() {
		
		/* Remove all other enqueued scripts from plugins that don't use 'headway_visual_editor_scripts' to reduce conflicts */
			global $wp_scripts;
			$wp_scripts = null;
			remove_all_actions('wp_print_scripts');
		
		do_action('headway_visual_editor_scripts');
		
		wp_print_scripts();
		
	}
	
	
	public static function print_styles() {
		
		/* Remove all other enqueued styles from plugins that don't use 'headway_visual_editor_styles' to reduce conflicts */
			global $wp_styles;
			$wp_styles = null;
			remove_all_actions('wp_print_styles');

		do_action('headway_visual_editor_styles');

		wp_print_styles();
		
	}
	
	
	public static function add_visual_editor_js_vars() {
		
		//Gather the URLs for the block types
		$block_types = HeadwayBlocks::get_block_types();
		$block_type_urls = array();
		
		foreach ( $block_types as $block_type => $block_type_options )
			$block_type_urls[$block_type] = $block_type_options['url'];
			
		$block_styles = class_exists('HeadwayChildThemeAPI') ? HeadwayChildThemeAPI::get_block_style_classes() : array();

		wp_localize_script('headway-ve-js', 'Headway', array( 
			'ajaxURL' => admin_url('admin-ajax.php'),
			'currentLayout' => HeadwayLayout::get_current(),
			'currentLayoutName' => HeadwayLayout::get_name(HeadwayLayout::get_current()),
			'availableBlockID' => HeadwayBlocksData::get_available_block_id(),
			'headwayURL' => get_template_directory_uri(),
			'siteURL' => site_url(),
			'homeURL' => home_url(),
			'adminURL' => admin_url(),
			'mode' => HeadwayVisualEditor::get_current_mode(),
			'designEditorSupport' => current_theme_supports('headway-design-editor'),
			'siteName' => get_bloginfo('name'),
			'siteDescription' => get_bloginfo('description'),
			'security' => wp_create_nonce('headway-visual-editor-ajax'),
			'ranTour' => json_encode(array(
				'legacy' => HeadwayOption::get('ran-tour', false, false),
				'grid' => HeadwayOption::get('ran-tour-grid', false, false),
				'design' => HeadwayOption::get('ran-tour-design', false, false)
			)),
			'gridColumns' => HeadwayGrid::$columns,
			'blockTypeURLs' => json_encode($block_type_urls),
			'allBlockTypes' => json_encode($block_types),
			'disableCodeMirror' => HeadwayOption::get('disable-codemirror', false, false),
			'frontPage' => get_option('show_on_front', 'posts'),
			'gridSupported' => current_theme_supports('headway-grid'),
			'disableTooltips' => HeadwayOption::get('disable-visual-editor-tooltips', false, false),
			'blockStyles' => $block_styles,
			'responsiveGrid' => HeadwayResponsiveGrid::is_enabled(),
			'designEditorProperties' => json_encode(HeadwayElementProperties::get_properties()),
			'colorpickerSwatches' => HeadwayOption::get('colorpicker-swatches', false, array()),
			'gridSafeMode' => HeadwayOption::get('grid-safe-mode', false, false)
		));
		
	}
	
	
	//////////////////    Content   ///////////////////////
	
	
	public static function add_default_panel_link() {
		
		echo '<li><a href="#general-tab">General</a></li>';
		
		
	}
	
	
	public static function add_default_panel() {
		
		echo '<div id="general-tab" class="panel">';
		
			echo '<p class="sub-tab-notice panel-notice">There are currently no options to display.</p>';
		
		echo '</div>';
				
	}
	
	
	public static function panel_top_right() {
		
		echo '<li id="minimize">
			<span title="Minimize Panel &lt;strong&gt;Shortcut: Ctrl + P&lt;/strong&gt;" class="mode-button tooltip-bottom-right">g</span>
		</li>';
		
	}
	
	
	public static function panel_top_mode_buttons() {
		
		switch ( HeadwayVisualEditor::get_current_mode() ) {
			
			case 'design':
			
				if ( current_theme_supports('headway-design-editor') ) {

					$tooltip = '
						<strong>Shortcut:</strong> Ctrl + I<br /><br />
						<strong>How to use:</strong> <em>Right-click</em> highlighted elements to style them.  Once an element is selected, you may nudge it using your arrow keys.<br /><br />
						The faded orange and purple are the margins and padding.  These colors are only visible when the inspector is active.';

					echo '<span class="mode-button mode-button-green mode-button-depressed tooltip-bottom-right" id="toggle-inspector" title="' . htmlspecialchars($tooltip) . '">Disable Inspector</span>';

				}
				
			break;
			
		}
		
	}
	
	
	public static function block_type_selector() {
		
		$block_types = HeadwayBlocks::get_block_types();
						
		echo "\n". '<div class="block-type-selector block-type-selector-original" style="display: none;">' . "\n";
							
				foreach ( $block_types as $block_type_id => $block_type ) {

					echo '
						<div id="block-type-' . $block_type_id . '" class="block-type" title="' . $block_type['description'] . '">
							<h4 style="background-image: url(' . $block_type['url'] . '/icon.png);">' . $block_type['name'] . '</h4>

							<div class="block-type-description">
								<p>' . $block_type['description'] . '</p>
							</div>
						</div>
					';

				}
		
		echo '</div><!-- div.block-type-selector -->' . "\n\n";
		
	}
	
	
	public static function layout_selector() {
		
		echo "\n" . '<div id="layout-selector-offset" class="layout-selector-open">' . "\n";
				
			echo '<div id="layout-selector-container">' . "\n";
												
				echo '<div id="layout-selector">' . "\n";
				
					echo '<div id="layout-selector-tabs"><ul class="tabs">' . "\n";
						
						echo '<li><a href="#layout-selector-pages-container">Pages</a></li>' . "\n";
						echo '<li><a href="#layout-selector-templates-container">Templates</a></li>' . "\n";

						echo '<span id="layout-selector-toggle" title="Toggle Layout Selector">Hide Layout Selector</span>' . "\n";
					
					echo '<form><input type="text" id="layout-selector-search" value="Type to find a layout..." /></form>' . "\n" . '
					</ul></div><!-- #layout-selector-tabs -->' . "\n";
								

					echo '<div id="layout-selector-pages-container">' . "\n";

						echo '<div id="layout-selector-pages" class="layout-selector-content">' . "\n";
							self::list_pages();
						echo '</div><!-- div#layout-selector-pages -->' . "\n";
											
					echo '</div><!-- #layout-selector-pages -->' . "\n";
					
					
					echo '<div id="layout-selector-templates-container" class="ui-tabs-hide">' . "\n";

						echo '<div id="layout-selector-templates" class="layout-selector-content">' . "\n";
							self::list_templates();
						echo '</div><!-- div#layout-selector-templates -->' . "\n";

						echo '<div id="template-name-input-container">
								<input type="text" placeholder="Template Name" value="" id="template-name-input" />
								<span class="layout-selector-button add-template" id="add-template">Add Template</span>
								<span class="layout-selector-button rename-template" id="rename-template" style="display: none;">Rename</span>
							</div>';
										
					echo '</div><!-- #layout-selector-templates -->' . "\n";					
					
		
				echo '</div><!-- #layout-selector -->' . "\n";
			echo '</div><!-- #layout-selector-container -->' . "\n";
			
		echo '</div><!-- #layout-selector-offset -->' . "\n";
		
	}
	
	
	public static function list_pages($pages = null) {
			
		//Since this function is recursive, we must designate the default like this	
		if ( $pages === null ) {
			
			$pages = HeadwayLayout::get_pages();
			$root_pages = true;
			
		}
				
		echo '<ul>' . "\n";
		
			/**
			 * Only show the message the function is being called for the first time (not showing children) and that the 
			 * mode is NOT the grid and the grid is supported still.
			 **/
			if ( isset($root_pages) && HeadwayVisualEditor::get_current_mode() !== 'grid' && current_theme_supports('headway-grid') ) {
				
				echo '<li class="layout-item info-layout-item"><span class="layout"><strong>The layout selector will only show layouts that have blocks.  To add blocks to a new layout, please switch to the Grid mode.</strong></span></li>';
			
			}		
			
			foreach ( $pages as $id => $children ) {
				
				$layout_id_fragments = explode('-', $id);		
				
				$status = HeadwayLayout::get_status($id);	
				
				$class = array('layout-item');
				
				if ( is_array($children) && count($children) !== 0 && HeadwayVisualEditor::get_current_mode() === 'grid' )
					$class[] = 'has-children';
				
				if ( $status['customized'] === true && !$status['template'] && count(HeadwayBlocksData::get_blocks_by_layout($id)) > 0 )
					$class[] = 'layout-item-customized';
					
				if ( $status['template'] )
					$class[] = 'layout-item-template-used';
					
				if ( $id === HeadwayLayout::get_current() )
					$class[] = 'layout-selected';

				$template_id = ( $status['template'] ) ? 'template-' . $status['template'] : 'none'; 				
				$template_name = ( $status['template'] ) ? HeadwayLayout::get_name('template-' . $status['template']) : null; 
				
				/* Take care of layouts that are the front page or blog index */
				if ( (get_option('show_on_front') === 'page' && (isset($layout_id_fragments[1]) && $layout_id_fragments[1] == 'page')) || apply_filters('headway_layout_selector_no_edit_item_' . $id, false) ) {
					
					/* If the page is set as the static homepage or blog page, hide it if they don't have children.  The Blog Index and Front Page layouts will override them. */
					if ( end($layout_id_fragments) == get_option('page_on_front') || end($layout_id_fragments) == get_option('page_for_posts') || apply_filters('headway_layout_selector_no_edit_item_' . $id, false) ) {
						
						/* Layout has children--add the no edit class and has children class. */
						if ( is_array($children) && count($children) !== 0 ) {
							
							$class[] = 'layout-item-no-edit';
							
						/* If the layout doesn't have children, then just hide it. */
						} else {
							
							continue;
							
						}
						
					}
					
				}
				
				/* Hide layouts that aren't customized or using templates from the Design mode */
				if ( HeadwayVisualEditor::get_current_mode() !== 'grid' ) {
					
					/* Handle layouts that aren't customized or have a template */
					if ( headway_get('customized', $status, false) === false || headway_get('template', $status, false) !== false ) {
																								
						/* If there ARE customized children, add the no-edit class */
						if ( is_array($children) && count($children) !== 0 ) {
							
							$show_node = false;	//Get the variable ready
														
							/* Check if the children are customized. */
							if ( self::is_any_layout_child_customized($children) ) {
								
								$class[] = 'layout-item-no-edit';
								$class[] = 'has-children';
								$class[] = 'has-customized-children';

								$show_node = true;
								
							}						
							
							/* If the children aren't customized, then don't display it at all */
							if ( !isset($show_node) || !$show_node )
								continue;
														
						/* If there aren't any children, do not display the node at all */
						} else {
														
							continue;
							
						}
					
					/* Handle layouts that are customized */						
					} else {
						
						/* Add the has children class to customized layouts that have children. */
						if ( is_array($children) && count($children) && self::is_any_layout_child_customized($children) ) {
							$class[] = 'has-children';
							$class[] = 'has-customized-children';
						}
						
					}
 					
				} else {
					
					if ( is_array($children) && count($children) && self::is_any_layout_child_customized($children) ) {
						$class[] = 'has-customized-children';
					}
					
				}
				
				
				/* Output Stuff */						
				echo "\n" . '<li class="' . implode(' ', array_filter($class)) . '">' . "\n";
																			
					echo "\n". '
						<span class="layout-has-customized-children tooltip" title="This layout has customized children.">&deg;</span>
					
						<span data-layout-id="' . $id . '" data-layout-url="' . HeadwayLayout::get_url($id) . '" class="layout layout-page">
							<strong>' . htmlspecialchars(HeadwayLayout::get_name($id)) . '</strong>

							<span class="status status-template" data-template-id="' . $template_id . '">' . $template_name . '</span>							
							<span class="status status-customized">Customized</span>
							<span class="status status-currently-editing">Currently Editing</span>
							
							<span class="remove-template layout-selector-button">Remove Template</span>';
							
							if ( HeadwayVisualEditor::get_current_mode() !== 'design' )
								echo '<span class="edit layout-selector-button">Edit</span>';
							else
								echo '<span class="edit layout-selector-button">View</span>';
						
						echo '<span class="revert layout-selector-button tooltip" title="Resetting a layout will remove all of its blocks&lt;br /&gt;so it inherits the blocks of a parent layout.">Reset</span>';
						
					echo '</span>' . "\n";
					
					if ( is_array($children) && count($children) !== 0 )						
						self::list_pages($children);

				echo '</li>' . "\n";
				
			}
		
		echo '</ul>' . "\n";
		
	}
	
	
	public static function list_templates() {
		
		$templates = HeadwayLayout::get_templates();
				
		echo '<ul>' . "\n";
			
			$no_templates_display = ( count($templates) === 0 ) ? null : ' style="display:none;"';
			
			echo '<li class="layout-item info-layout-item" id="no-templates"' . $no_templates_display . '><span class="layout"><strong>There are no templates to display, add one!</strong></span></li>';
				
			foreach($templates as $id => $name) {

				$class = array('layout-item');

				$class[] = ( $id === HeadwayLayout::get_current() ) ? 'layout-selected' : null;

				//Output stuff							
				echo "\n" . '<li class="' . implode(' ', array_filter($class)) . '">' . "\n";

					echo "\n". '
						<span data-layout-id="template-' . $id . '" class="layout layout-template">
							<strong class="template-name">' . HeadwayLayout::get_name('template-' . $id) . '</strong>

							<span class="delete-template" title="Delete Template">Delete</span>

							<span class="status status-currently-editing">Currently Editing</span>

							<span class="assign-template layout-selector-button">Use Template</span>
							<span class="edit layout-selector-button">Edit</span>
						</span>' . "\n";

				echo '</li>' . "\n";

			}
			
		echo '</ul>' . "\n";
		
	}
	
	
	public static function is_any_layout_child_customized($children) {
		
		if ( !is_array($children) || count($children) == 0 )
			return false;
									
		foreach ( $children as $id => $grand_children ) {
											
			$status = HeadwayLayout::get_status($id);
														
			if ( headway_get('customized', $status) && !headway_get('template', $status) )
				return true;
								
			if ( is_array($grand_children) && count($grand_children) > 0 && self::is_any_layout_child_customized($grand_children) === true )
				return true;
			
		}
		
		return false;
		
	}
	
	
	public static function mode_navigation() {
				
		foreach(HeadwayVisualEditor::get_modes() as $mode => $tooltip){
			
			$current = ( HeadwayVisualEditor::is_mode($mode) ) ? ' class="current-mode"' : null;
		
			$mode_id = strtolower($mode);
			
			echo '
				<li' . $current . ' id="mode-'. $mode_id . '">
					<a href="' . home_url() . '/?visual-editor=true&amp;visual-editor-mode=' . $mode_id . '" title="' . htmlspecialchars($tooltip) . '" class="tooltip-top-left">
						<span>' . ucwords($mode) . '</span>
					</a>
				</li>
			';
			
		}
		
	}
	
	
	public static function menu_links() {
		
		echo '<li id="menu-link-tools" class="menu-link-has-submenu">
				<span>Tools</span>

				<ul>';

					// echo '<li id="tools-undo"><span>Undo <small>Ctrl + Z</small></span></li>
					// <li id="tools-redo"><span>Redo <small>Ctrl + Y</small></span></li>';

					if ( HeadwayVisualEditor::is_mode('grid') )
						echo '<li id="tools-grid-wizard"><span>Grid Wizard</span></li>';
						
					if ( !HeadwayVisualEditor::is_mode('grid') && current_theme_supports('headway-live-css') )
						echo '<li id="tools-live-css"><span>Live CSS <small>Ctrl + E</small></span></li>';
						
					if ( HeadwayCompiler::can_cache() )
						echo '<li id="tools-clear-cache"><span>Clear Cache</span></li>';
						
					echo '<li id="tools-tour"><span>Tour</span></li>
				</ul>

			</li>';


		echo '<li id="menu-link-admin" class="menu-link-has-submenu">
				<span>Admin</span>

				<ul>
					<li><a href="' . admin_url()  . '" target="_blank">Dashboard</a></li>				
					<li><a href="' . admin_url('widgets.php')  . '" target="_blank">Widgets</a></li>
					<li><a href="' . admin_url('nav-menus.php')  . '" target="_blank">Menus</a></li>
					<li><a href="' . admin_url('admin.php?page=headway-options')  . '" target="_blank">Headway Options</a></li>				
					<li><a href="http://codex.headwaythemes.com" target="_blank">Documentation</a></li>
					<li><a href="http://support.headwaythemes.com" target="_blank">Support</a></li>
				</ul>

			</li>';


		echo '<li id="menu-link-view-site"><a href="' . home_url() . '" target="_blank">View Site</a></li>';
		
	}
	
	
	public static function page_switcher_page() {
				
		echo '<strong>Currently Editing: <span>' . HeadwayLayout::get_current_name() . '</span></strong>';
		
	}
	
	
	public static function get_a_tip() {
		
		$tips = array(
			'Speed your WordPress installation up with <a href="http://wordpress.org/extend/plugins/w3-total-cache/" target="_blank">W3 Total Cache</a>.',
			'Need to backup or migrate your site?  Try <a href="http://pluginbuddy.com/purchase/backupbuddy/" target="_blank">BackupBuddy</a>.',
			'Want more control over your loop?  Try <a href="http://pluginbuddy.com/purchase/loopbuddy/" target="_blank">LoopBuddy</a>.',
			'Looking for a powerful slider to display your content?  Try <a href="http://headwaythemes.com/go/slidedeck-lite" target="_blank">SlideDeck</a>',
			'Need to move multiple blocks at a time?  Double-click blocks to enter Mass Block Selection mode.',
			'<strong>Vertical space in the <em>Grid</em> is never taken literally.</strong>  It is only used to determine the general layout.'
		);
		
		echo '<p class="tip"><strong>Tip:</strong> ' . $tips[rand(0, count($tips)-1)] . '</p>';
				
	}
	
	
}