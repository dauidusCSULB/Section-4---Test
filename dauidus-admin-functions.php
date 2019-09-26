<?php
/*
Plugin Name: Dauidus Admin Functions
Plugin URI: https://dauid.us
description: loads Yellow Pelican admin functions
Author: Dave Winter
Version: 0.1
Author URI: https://dauid.us
*/

// allow Sections in custom post types
function generate_add_section_post_types()
{
      return array( 'page','post','product' );
}
add_filter( 'generate_sections_post_types','generate_add_section_post_types' );

// allow Sections in custom post types
function generate_add_section_post_types()
{
      return array( 'page','post','product', 'service', 'user' );
}
add_filter( 'generate_sections_post_types','generate_add_section_post_types' );


// move Collapse Menu to top
function collapse_menu_button() { ?>

    <script type="text/javascript">
        jQuery( document ).ready(function() {
            var element = jQuery('#collapse-menu');
            jQuery('#adminmenu').prepend(element);
        });
    </script>
    <style type="text/css">
        #wpadminbar #collapse-menu {
            width: 130px;
            list-style: none;
        }
        .folded #wpadminbar #collapse-menu {
            width: 30px;
            list-style: none;
        }
    </style>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500" rel="stylesheet">

<?php }
add_action('admin_head', 'collapse_menu_button', 0);


// Displays screen id information on admin pages
// useful for development only
// to turn off, comment out the following line
//add_filter('current_screen', 'my_current_screen' );
 
function my_current_screen($screen) {
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return $screen;
    print_r($screen);
    return $screen;
}


// hide screen options for non-admin users
function wpb_remove_screen_options() { 
    if(!current_user_can('manage_options')) {
        return false;
    }
    //return true; 
}
add_filter('screen_options_show_screen', 'wpb_remove_screen_options'); 


// hide support gravity form for non-admins
// called in function below
function custom_gf_css() {
  echo '<style>
    .toplevel_page_gf_edit_forms #the-list tr:nth-of-type(1) {
        display: none !important;
    }
  </style>';
}

/* hide dashboard pages for users other than admin... */
add_filter( 'parse_query', 'exclude_pages_from_admin' );
function exclude_pages_from_admin($query) {
    global $pagenow,$post_type;
    $whodat = get_current_user_id();
    /* if is not admin user */
    if ( (!($whodat == '1')) && (!($whodat == '2')) && (!($whodat == '4')) ) { 
        if (is_admin() && $pagenow=='edit.php' && $post_type =='page') {
            $query->query_vars['post__not_in'] = array('40','49','770','1391');
        }
        if (is_admin() && $pagenow=='admin.php') {
            add_action('admin_head', 'custom_gf_css');
        }
    }
}
/* ...and hide those pages from listings on the frontend */
add_filter('wp_list_pages_excludes', 'exclude_from_wp_list_pages');
function exclude_from_wp_list_pages($exclude_array) {
    $whodat = get_current_user_id();
    /* if is not admin user */
    if ( (!($whodat == '1')) && (!($whodat == '2')) && (!($whodat == '4')) ) {
        $exclude_array = $exclude_array + array('40','49','770','1391');
        return $exclude_array;
    }
}


// redirect admin dashboard page to custom content dashboard
add_action('load-index.php', function(){
    if(get_current_screen()->base == 'dashboard') {
        wp_redirect(admin_url('index.php?page=content_dashboard'));
    }
});


// set groundskeeper account as the blueprint for all metabox positioning
if (is_admin()) {

    // Make sure plugin is active
    if (class_exists('\GlobalMetaBoxOrder\Config')) {

        // Make a long name short. 
        class_alias('\GlobalMetaBoxOrder\Config', 'MetaBoxConfig');

        // Settings

        MetaBoxConfig::$filter = array('post', 'page', 'events', 'product'); // default
        MetaBoxConfig::$include_cpts = true; // default
        MetaBoxConfig::$getBlueprintUserId = function () { return '4'; };
        //MetaBoxConfig::$exclude = array('acme_product');
        MetaBoxConfig::$remove_screen_options = true;
        //MetaBoxConfig::$lock_meta_box_order = true; 
    }
}
