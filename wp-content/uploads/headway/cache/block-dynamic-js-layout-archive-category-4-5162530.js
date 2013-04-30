jQuery(document).ready(function(){
					if ( typeof jQuery().tinyNav != "function" )
						return false;

					jQuery("#block-2").find("ul.menu").tinyNav({
						active: "current_page_item"
					}).addClass("tinynav-active");
				});



