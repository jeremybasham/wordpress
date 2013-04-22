function addBuilderHeadings(id) {
	jQuery("#sub-tab-builder").nextAll().not('#sub-tab-config, #sub-tab-import-export').addClass('short-code');
	jQuery("#block-" + id + "-tab #input-builder-input-header").before('<div id="builder-wrapper">\
		<h1 id="builder-title"><strong>Article Builder</strong> - all articles in block</h1>\
		</div>\
		<div id="builder-info">\
			<h2 id="builder-sub-title">Click to start building!</h2>\
			<p class="intro">Add elements to your article using the builder. Click on an article element like title, or thumb to add it to the builder automatically.</p>\
			<p>Elements are added to the most logical position as you click, but you can move them around to where you want.</p>\
			<p>Advanced users can add elements along with or wrapped in any html or text. This gives you more control if you need to add some html around an element to further style it with css.</p>\
		</div>');
	jQuery("#block-" + id + "-tab #input-columns").after('<div class="input" id="row-count"><strong>3</strong> Rows</div>');
	jQuery('#input-builder-input-header').appendTo(jQuery('#builder-wrapper'));
	jQuery('#input-builder-input-section').appendTo(jQuery('#builder-wrapper'));
	jQuery('#input-builder-input-footer').appendTo(jQuery('#builder-wrapper'));
}

function setRows(value) {
	var target = jQuery("#row-count");
	var count = jQuery("#input-posts-per-block").find('input').val();
	var columns = jQuery("#input-columns").find('input').val();
	var rows = count / columns;
	target.find("strong").text(Math.ceil(rows));
}

function addRemoveShortcodeOptions(value, id) {

   jQuery('#block-' + id + '-tab #sub-tab-builder').nextAll().not('#sub-tab-config, #sub-tab-import-export').hide();

 	var shortcodes = [
 		'title',
 		'thumb',
 		'excerpt',
 		'readmore',
 		'date',
 		'time',
 		'category',
 		'author',
 		'avatar',
 		'comments'
 	];

 	jQuery.each(shortcodes, function(index, shortcode) {

	   if (value.indexOf(shortcode) >= 0) {
	       jQuery('#block-' + id + '-tab #sub-tab-' + shortcode + '-options').fadeIn(200);
	   }

 	});
    
}