<?php
class HeadwayPostListingsBlockDisplay {
		
	var $count = 0;	
		
	var $query = array();

	private static $block = null;
	
	function __construct($block) {
		self::$block = $block;
	}
	
	/**
	 * Created this function to make the call a little shorter.
	 **/
	function get_setting($setting, $default = null) {
		
		$block = self::$block;
		return HeadwayBlockAPI::get_setting($block, $setting, $default);
		
	}

	
	function display($args = array()) {
		
		$this->loop($args);
		
	}
	
	
	function loop($args = array()) {
						
		if ( !dynamic_loop() ) {
			
			$this->setup_query();
			
			echo '<div class="articles clearfix">';	
			
				while ( $this->query->have_posts() ) {
				
					$this->query->the_post();
					
					$this->count++;
		
					$this->display_item();
				
				}
									
			echo '</div>';
			
		}
							
	}

	function display_item() {
		$builder_input_header = $this->get_setting('builder-input-header', '[title]');
		$builder_input_section = $this->get_setting('builder-input-section', '[thumb][excerpt]');
		$builder_input_footer = $this->get_setting('builder-input-footer', '[readmore]');
		global $post;
		$postid = $post->ID; ?>
		<article id="post-<?php the_ID(); ?>" class="article-<?php echo $this->count ?> article item clearfix hentry">
			
			<?php if(!empty($builder_input_header)) : ?>
			<header class="clearfix">
				<?php echo headway_parse_php(do_shortcode(self::article_shortcodes(stripslashes($builder_input_header), $postid))); ?>
			</header>
			<?php endif; ?>
			
			<?php if(!empty($builder_input_section)) : ?>
			<section class="post-content clearfix">
				<?php echo headway_parse_php(do_shortcode(self::article_shortcodes(stripslashes($builder_input_section), $postid))); ?>
			</section>
			<?php endif; ?>
			
			<?php if(!empty($builder_input_footer)) : ?>
			<footer class="post-meta clearfix">
				<?php echo headway_parse_php(do_shortcode(self::article_shortcodes(stripslashes($builder_input_footer), $postid))); ?>
			</footer>
			<?php endif; ?>
		</article>
		<?php
	}
	
	function setup_query() {
				
		/* Setup Query */
			$query_args = array();

			/* Pagination */
				$paged_var = get_query_var('paged') ? get_query_var('paged') : get_query_var('page');

			/* Categories */
				if ( $this->get_setting('categories-mode', 'include') == 'include' ) 
					$query_args['category__in'] = $this->get_setting('categories', array());

				if ( $this->get_setting('categories-mode', 'include') == 'exclude' ) 
					$query_args['category__not_in'] = $this->get_setting('categories', array());	

			$query_args['post_type'] = $this->get_setting('post-type', false);

			/* Pin limit */
				$query_args['posts_per_page'] = $this->get_setting('posts-per-block', 4);

			/* Author Filter */
				if ( is_array($this->get_setting('author')) )
					$query_args['author'] = trim(implode(',', $this->get_setting('author')), ', ');

			/* Order */
				$query_args['orderby'] = $this->get_setting('order-by', 'date');
				$query_args['order'] = $this->get_setting('order', 'desc');
				$post_in = $this->get_setting('post_id', false);
				$query_args['post__in'] = ($post_in == true) ? explode(', ', $post_in) : false;

			/* Query! */
				$this->query = new WP_Query($query_args);

				global $paged; /* Set paged to the proper number because WordPress pagination SUCKS!  ANGER! */
				$paged = $paged_var;
		/* End Query Setup */
		
	}
		
	static function article_shortcodes($position, $id) {
		/* replace item variables
		*******************************************************/
		$title = self::article_title($id);
		$excerpt = self::article_excerpt($id);
		$readmore = self::article_readmore($id);
		$thumb = self::article_image($id);
		$date = self::article_date($id);
		$time = self::article_time($id);
		$category = self::article_category($id);
		$author = self::article_author($id);
		$comments = self::article_comments($id);
		$avatar = self::article_author_avatar($id);
		
		/* set pattern
		*******************************************************/
		$pattern_match = array ('/\[title\]/', '/\[excerpt\]/', '/\[readmore\]/', '/\[thumb\]/', '/\[date\]/', '/\[time\]/', '/\[category\]/', '/\[author\]/', '/\[comments\]/', '/\[avatar\]/');
		/* replace with
		*******************************************************/
		$replace = array ($title, $excerpt, $readmore, $thumb, $date, $time, $category, $author, $comments, $avatar);
		
		return preg_replace($pattern_match, $replace, $position);
	}

	function article_image($id) {
		$image = '';
		if ( has_post_thumbnail()) {

			$block = self::$block;

			/* Thumb alignment */
			$thumb_align = self::get_setting('thumb-align', 'none');

			$auto_size = self::get_setting('thumb-size-auto', true);

			$crop_images_vertically = self::get_setting('thumb-crop-vertically', 'vertically');
			
			$columns = self::get_setting('columns', 3);
			$approx_img_width = (HeadwayBlocksData::get_block_width($block) / $columns);

			$thumbnail_id = get_post_thumbnail_id();  

			$thumbnail_width = $approx_img_width + 10; /* Add a 10px buffer to insure that image will be large enough */

			if ( $auto_size ) {

				/* all images height depends on ratios so set to '' */
				$thumbnail_height = '';
				/* if crop vertically make all images the same height */
				if ( $crop_images_vertically )
					$thumbnail_height = round($approx_img_width * (self::get_setting('post-thumbnail-height-ratio', 75) * .01));

				$thumbnail_object = wp_get_attachment_image_src($thumbnail_id, 'full'); 
				$thumbnail_url = headway_resize_image($thumbnail_object[0], $thumbnail_width, $thumbnail_height);

			} else {

				$thumbnail_width            = self::get_setting('thumb-width', '140');
				$thumbnail_height           = self::get_setting('thumb-height', '100');

				/* if crop vertically make all images the same height */
				if ( $crop_images_vertically )
					$thumbnail_height = round($thumbnail_height * (self::get_setting('post-thumbnail-height-ratio', 75) * .01));


				$thumbnail_object = wp_get_attachment_image_src($thumbnail_id, 'full');  
				$thumbnail_url    = headway_resize_image($thumbnail_object[0], $thumbnail_width, $thumbnail_height);

			}

			$icon_class = self::get_setting('thumb-hover-iconclass', 'link');
			$image .= '<figure class="align' . $thumb_align . '">';

				$image .= '<a href="' . get_permalink() . '" class="post-thumbnail" title="' . get_the_title() . '">';
					$image .= '<img src="' . esc_url($thumbnail_url) . '" alt="' . get_the_title() . '"  width="'.$thumbnail_width.'" height="'.$thumbnail_height.'"/>';
					if (self::get_setting('thumb-hover-overlay', false))
						$image .= '<span><span class="overlay"><i class="icon icon-' . $icon_class . '"></i></span></span>';
				$image .= '</a>';

			$image .= '</figure>';
		}

		return $image;
	}
	
	function article_excerpt($id) {
		$content_to_show = self::get_setting('content-to-show', 'excerpt');
		if ( $content_to_show == 'excerpt' ) {

			$excerpt_length = self::get_setting('excerpt-length', '50');

			return '<p class="excerpt">' . self::get_trimmed_excerpt($excerpt_length) . '</p>';

		} elseif ( $content_to_show == 'content' ) {

			return '<div class="excerpt">' . get_the_content() . '</div>';

		}
	}

	function get_trimmed_excerpt($charlength) {
		$excerpt = get_the_excerpt();
		$charlength++;

		if ( mb_strlen( $excerpt ) > $charlength ) {
			/* If string needs to be trimmed */
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				$excerpt = mb_substr( $subex, 0, $excut );
			} else {
				$excerpt = $subex;
			}
			$excerpt = $excerpt.self::get_setting('excerpt-more', '...');
		} else {
			/* Nothing to trim */
			$excerpt = $excerpt;
		}
		return $excerpt;
	}
	
	function article_readmore($id) {
		global $post;
		$more_text = self::get_setting('read-more-text', 'Read more');
		$more_link = '<a href="'. get_permalink($post->ID) . '" class="more-link readon">' . $more_text . '</a>';
		
		return $more_link;
	}
	
	function article_title($id) {
		$html_tag = self::get_setting('title-html-tag', 'h1');
		$linked = self::get_setting('title-link', true);
		$shorten = self::get_setting('title-shorten', true);

		/* Shorten Title */
		$title_text = get_the_title($id);
		$title_length = mb_strlen($title_text);
		$limit = self::get_setting('title-limit', 20);
		$title = substr($title_text, 0, $limit);
		if ($title_length > $limit) 
			$title .= "...";

		if (!$shorten)
			$title = get_the_title($id);

		if($linked)
			return '<' . $html_tag . ' class="entry-title">
			<a href="'. get_post_permalink($id) .'" rel="bookmark" title="'. the_title_attribute (array('echo' => 0) ) .'">'. $title .'</a>
		</' . $html_tag . '>';
		return '<' . $html_tag . ' class="entry-title">
			'. $title .'
		</' . $html_tag . '>';
	}
	
	function article_date($id) {
		$date_format = self::get_setting('meta-date-format', 'wordpress-default');
		$date = ($date_format != 'wordpress-default') ? get_the_time($date_format) : get_the_date();
		$before = self::get_setting('date-before-text', false);
		$date = '<span class="date post-meta"> ' . $before . ' <time datetime="'. get_the_time('c') .'">' . $date . '</time></span>';

		return $date;
	}

	function article_comments($id) {
		if ( (int)get_comments_number($id) === 0 ) 
			$comments_format = stripslashes(self::get_setting('comment-format-0', '%num% Comments'));
		elseif ( (int)get_comments_number($id) == 1 ) 
			$comments_format = stripslashes(self::get_setting('comment-format-1', '%num% Comment'));
		elseif ( (int)get_comments_number($id) > 1 ) 
			$comments_format = stripslashes(self::get_setting('comment-format', '%num% Comments'));
		
		$before = self::get_setting('comments-before-text', false);
		$comments = str_replace('%num%', get_comments_number($id), $comments_format);
		
		$comments_link = $before . '<a href="'.get_comments_link() . '" title="'.get_the_title() . ' Comments" class="entry-comments">' . $comments . '</a>';

		return $comments_link;
	}

	function article_time($id) {
		$time_format = self::get_setting('meta-time-format', 'wordpress-default');
		$time = ($time_format != 'wordpress-default') ? get_the_time($time_format) : get_the_time();
		$before = self::get_setting('time-before-text', false);
		$timesince = self::get_setting('time-timesince', true);

		if ($timesince)
			return self::article_time_since($id);

		return '<span class="entry-time">' . $before . ' ' . $time . '</span>';
	}
	
	function article_time_since($id) {
		$before = self::get_setting('time-before-text', false);
		return '<time class="time-since post-meta" datetime="'. get_the_time('c') .'">
			' . $before . '
			<a href="'. get_post_permalink($id) .'" rel="bookmark" class="time post-meta" title="'. the_title_attribute (array('echo' => 0) ) .'">
				'. self::time_since(get_the_time('U')) .'
			</a>
		</time>';
	}

	/* time passed */
	function time_passed ($t1, $t2)
	{
		if($t1 > $t2) :
		  $time1 = $t2;
		  $time2 = $t1;
		else :
		  $time1 = $t1;
		  $time2 = $t2;
		endif;
		$diff = array(
		  'years' => 0,
		  'months' => 0,
		  'weeks' => 0,
		  'days' => 0,
		  'hours' => 0,
		  'minutes' => 0,
		  'seconds' =>0
		);
		$units = array('years','months','weeks','days','hours','minutes','seconds');
		foreach($units as $unit) :
		  while(true) :
		     $next = strtotime("+1 $unit", $time1);
		     if($next < $time2) :
		        $time1 = $next;
		        $diff[$unit]++;
		     else :
		        break;
		     endif;
		  endwhile;
		endforeach;
		return($diff);
	}

	function time_since($thetime) 
	{
		$diff = self::time_passed($thetime, strtotime('now'));
		$units = 0;
		$time_since = array();
		foreach($diff as $unit => $value) :
		   if($value != 0 && $units < 2) :
				if($value === 1) :
					$unit = substr($unit, 0, -1);
				endif;
			   $time_since[]= $value . ' ' .$unit;
			   ++$units;		
		    endif;
		endforeach;
		$time_since = implode(', ',$time_since);
		$time_since .= ' ago';
		$date = $time_since;
		return $date;
	}
	
	function article_category($id) {
		$cats = '';
		$i = '';
		$c = count(get_the_category($id));
		$cats .= '<span class="categories-wrap">';
		foreach((get_the_category($id)) as $category) {
 			$i++;
		    $cats .= '<a href="'.get_category_link($category->term_id).'" class="post-meta categories '. $category->slug .'">'.$category->cat_name.'</a>';
		    $cats .= ($i == $c) ? ' ' : ', ';
		};
		$cats .= '</span>';
		$before = self::get_setting('category-before-text', false);
		return $before .' '.$cats;
	}

	function article_author($id) {
		global $authordata;
		$linked = self::get_setting('author-link', true);
		$before = self::get_setting('author-before-text', false);
		if(!$linked)
			return $authordata->display_name;
		return $before .' <a class="author-link fn nickname url" href="'.get_author_posts_url($authordata->ID) . '" title="View all posts by ' . $authordata->display_name . '">' . $authordata->display_name . '</a>';
	}

	function article_author_avatar($id) {
		global $authordata;
		$linked = self::get_setting('author-avatar-link', true);
		$before = self::get_setting('author-avatar-before-text', false);
		$avatar_size = self::get_setting('author-avatar-size', 32);
		
		$avatar_img = get_avatar( get_the_author_meta('email'), $avatar_size );

		if(!$linked)
			return $avatar_img;
		return $before .' <a class="author-avatar fn nickname url" href="'.get_author_posts_url($authordata->ID) . '" title="View all posts by ' . $authordata->display_name . '">' . $avatar_img . '</a>';
	}
	
	
}