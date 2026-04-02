<?php 
echo "test"; 
function execheads_theme_setup() {
  register_nav_menus( array( 
    'header' => 'Header menu',
    'about' => 'About menu',
    'services' => 'Services menu',
    'assignments' => 'Assignments menu',
    'knowledgehub' => 'knowledge Hub menu'
  ) );
 }

add_action( 'after_setup_theme', 'execheads_theme_setup' );

add_theme_support( 'post-thumbnails' );



function wpdocs_dequeue_dashicon() {
    if (current_user_can( 'update_core' )) {
        return;
    }
    wp_deregister_style('dashicons');
}
add_action( 'wp_enqueue_scripts', 'wpdocs_dequeue_dashicon' );

function smartwp_remove_wp_block_library_css(){
 wp_dequeue_style( 'wp-block-library' );
} 
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css' );

add_filter('acf/format_value/type=text', 'do_shortcode');

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title' => 'General Settings'
    ));   
}
