<?php
// Uncomment line below if you want to use post formats
//add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

// Test Action Hook and Placement    
//function my_action() {echo 'Testing function output';}
//add_action('headway_after_block_38', 'my_action');

// Remove default headway styling and design editor
add_action('headway_setup_child_theme', 'churchhype_child_setup');
function churchhype_child_setup() {
    remove_theme_support('headway-design-editor');
    remove_theme_support('headway-structure-css');
}
?>
<?php
// Add new meta tags and place above title *******************************************
function new_meta() { ?>
<div class="meta-container">
          <div class="meta-category">
                    <?php the_category(', ') ?>
          </div>
          <div class="date-time">
                    <?php the_Date() ?>
          </div>
          <div class="meta-tags">
                    <p><span class="highlight-tag">Tags:</span> <?php the_tags( ' ') ?></p>
          </div>
</div>
<?php
} add_action('headway_before_entry_title', 'new_meta');
//************************************************************************************
?>