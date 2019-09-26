<?php
/**
 * YellowPelican admin dashboard shortcodes
 *
 * Plugin Name: YellowPelican dashboard shortcodes
 * Description: Custom for YellowPelican.  Dasboard shortcodes.
 * Author:            Dave Winter
 * Version:     1.1.2
 * Domain Path: /languages
 *
 */

function enqueue_dees_scripts($hook) {
    if ( 'dashboard_page_content_dashboard' != $hook ) {
        return;
    }

    // ENQUEUE SCRIPTS…
    the_seo_framework()->init_admin_scripts();
    the_seo_framework()->Scripts()::enqueue(); 

}
add_action( 'admin_enqueue_scripts', 'enqueue_dees_scripts' );


/* list all user-editable pages and edit links */
/* and hide admin pages from non-admins */
function pelican_list_pages($query) {
	$url = get_bloginfo('url');
	$whodat = get_current_user_id();
	$content = '<div id="dash_list_pages" class="list_pages">';

		// if admin user then show admin pages
	    if ( (!($whodat == '1')) && (!($whodat == '2')) ) {
	    	$args = array(
				'exclude' => array('40','49','770','1391'),
				'post_status'  => 'publish,draft'
			);
	    	$pages = get_pages($args); 
		} else {
			$args = array(
				'post_status'  => 'publish,draft'
			);
			$pages = get_pages($args);  
	    }

	    // set page counter
	    $x = 0;
	    foreach ($pages as $page_num) {
	    	$x++;
	    }

	    $content .= '<h2>Pages <span>(';
	    if ($x < '10') {
	    	$content .= 'showing all ' . $x;
	    } else {
	    	$content .= 'showing 10 of ' . $x;
	    }
	    $content .= ')</span></h2> <a class="edit_all_link" href="' . $url . '/wp-admin/edit.php?post_type=page">manage all pages</a>';
	    
	    // define home and blog pages
	    $frontpage_id = get_option( 'page_on_front' );
	    $blog_id = get_option( 'page_for_posts' );

	    $content .= '<ul>';

	    	$x = 0;
		    foreach ($pages as $page_data) {
		    	$x++;
		    	if ($x > '10') {
		    		break;
		    	}

		        $ID = $page_data->ID; 
		        $status = get_post_status($ID);
		        if ($frontpage_id == $ID) {
		        	$home = 'front-page';
		        	$hometitle = ' <span class="post-state">Front Page</span>'; 
		        } else {
		        	$home = '';
		        }
		        if ($blog_id == $ID) {
		        	$blog = 'blog-page';
		        	$blogtitle = ' <span class="post-state">Posts Page</span>'; 
		        } else {
		        	$blog = '';
		        }
		        if ($status == "draft") {
		        	$draft = 'draft';
			        $drafttitle .= ' <span class="post-state draft">Draft</span>'; 
			    } else {
	        		$draft = '';
		        }
		        $title = $page_data->post_title; 
		        $edit = edit_post_link();
		        $alt = get_post_meta( $ID, '_wp_attachment_image_alt', true );
		        $content .= '<li class="page-' . $ID . ' ' . $home . ' ' . $blog . ' ' . $draft . ' ' . $status . '">';
		        	$content .= '<div class="list_title">';
		        		$content .= '<div class="page-title">' . $title;
		        		if ($frontpage_id == $ID) {
				        	$content .= $hometitle;
				        }
				        if ($blog_id == $ID) {
				        	$content .= $blogtitle;
				        }
				        if ($status == "draft") {
				        	$content .= $drafttitle; 
				        }
				        $content .= '</div>';
				        $content .= '<span class="actions"><a class="edit_page_link" href="' . get_edit_post_link( $ID ) . '">edit</a><a class="view_page_link" href="' . _get_page_link( $ID) . '" target="_blank">view</a></span>'; 
		        		$content .= the_seo_framework()->post_status( $ID );
		            $content .= '</div>';
		        $content .= '</li>';
		    }

	    $content .= '</ul>';
	    // $content .= '<span class="small_text">"' . $hometitle . '" and "' . $blogtitle . '" pages are required, and cannot be removed</span><div class="clear"></div>';
	    
    $content .= '</div>';

    return $content;

}
add_shortcode( 'pelican-list-pages', 'pelican_list_pages' );


/* list all user-editable pages in dashboard */
function pelican_list_posts() {
	$url = get_bloginfo('url');
	$content = '<div id="dash_list_posts" class="list_pages">';
	    $posts = get_posts( array(
		    'post_status' => 'publish,draft'
		) ); 

		// set page counter
		$x = 0;
	    foreach ($posts as $post_num) { 
	    	$x++;
	    }

	    $content .= '<h2>Posts <span>(';
	    if ($x < '10') {
	    	$content .= 'showing all ' . $x;
	    } else {
	    	$content .= 'showing 10 of ' . $x;
	    }
	    $content .= ')</span></h2> <a class="edit_all_link" href="' . $url . '/wp-admin/edit.php?post_type=post">manage all posts</a>';
	    
	    $content .= '<ul>';

	    	$x = 0;
		    foreach ($posts as $post_data) {
		    	$x++;
		    	if ($x > '10') {
		    		break;
		    	}

		        $ID = $post_data->ID; 
		        $title = $post_data->post_title; 
		        $status = get_post_status($ID);
		        if ($status == "draft") {
		        	$draft = 'draft';
			        $drafttitle .= ' <span class="post-state draft">Draft</span>'; 
			    } else {
	        		$draft = '';
		        }
		        $edit = edit_post_link();
		        $content .= '<li class="page-' . $ID . ' ' . $status . '">';
		            $content .= '<div class="list_title">';
		        		$content .= '<div class="page-title">' . $title;
		        		if ($status == "draft") {
				        	$content .= $drafttitle; 
				        }
				        $content .= '</div>';
				        $content .= '<span class="actions"><a class="edit_page_link" href="' . get_edit_post_link( $ID ) . '">edit</a><a class="view_page_link" href="' . _get_page_link( $ID) . '" target="_blank">view</a></span>'; 
		        		$content .= the_seo_framework()->post_status( $ID );
		            $content .= '</div>';
		        $content .= '</li>';
		    }

	    $content .= '</ul>';
	    
	$content .= '</div>';

    return $content;
}
add_shortcode( 'pelican-list-posts', 'pelican_list_posts' );


/* display latest media uploads in dashboard */
function pelican_list_media() {
	$url = get_bloginfo('url');
	$content = '<div id="dash_list_media" class="list_media">';

		$media = get_posts( array(
		    'post_type' => 'attachment',
		    'posts_per_page' => 10000,
		    'post_status' => null
		) );
		$x = 0;
	    foreach ($media as $media_num) {
	    	$x++;
	    }

	    $content .= '<h2>Media Library <span>(';
	    if ($x < '20') {
	    	$content .= 'showing all ' . $x;
	    } else {
	    	$content .= 'showing 20 of ' . $x;
	    }
	    $content .= ')</span></h2> <a class="edit_all_link" href="' . $url . '/wp-admin/upload.php">manage all media</a>';

		//$content .= '<div class="display_text">';
			//$content .= 'Images with a properly added <b>"alt"</b> tag will appear with a green checkbox, and is a recommended SEO feature for all public images.';
		//$content .= '</div>';

		$attachments = get_posts( array(
		    'post_type' => 'attachment',
		    'posts_per_page' => 10000,
		    'post_status' => null
		) );

		$content .= '<ul>';

			$x = 0;
			foreach ( $attachments as $attachment ) {
				$x++;
		    	if ($x > '20') {
		    		break;
		    	}

				$ID = $attachment->ID; 
				$alt = get_post_meta( $ID, '_wp_attachment_image_alt', true );
				$content .= '<li class="media-' . $ID . '">';
					if (!( $alt == '' )) {
						$content .= '<div class="alt"></div>';
					}
					$content .= '<span><a class="edit_page_link" href="' . get_edit_post_link( $ID ) . '">edit</a></span>'; 
			    	$content .= wp_get_attachment_image( $ID, 'medium' );
			    $content .= '</li>';
			}

		$content .= '</ul>';
		
	$content .= '</div>';

    return $content;
}
add_shortcode( 'pelican-list-media', 'pelican_list_media' );


/* display current menu details in dashboard */
function pelican_list_menu() {
	$url = get_bloginfo('url');
	$content = '<div class="list_nav">';
		$locations = get_nav_menu_locations();
		$menu = wp_get_nav_menu_object( $locations[ 'primary' ] );
		$menu_items = wp_get_nav_menu_items($menu->term_id);

		$content .= '<h2>Main Navigation Menu</h2> <br><span class="small_text">Menu Name: ' . $menu->name . '</span> <a class="edit_this_link" href="' . $url . '/wp-admin/nav-menus.php?action=edit&menu=' . $menu->term_id . '">manage this menu</a>';
		//$content .= '<div class="display_text">';
			//$content .= 'Your primary menu is "<b>' . $menu->name . '</b>". All menu links, including dropdown items, are listed below for reference.';
		//$content .= '</div>';
		//$content .= '<br>"' . $menu->name . '"';
	    $content .= '<ul class="main-nav">';	 
	        $count = 0;
	        $submenu = false;	         
	        foreach( $menu_items as $menu_item ) {
	            $title = $menu_item->title;	             
	            if ( !$menu_item->menu_item_parent ) {
	                $parent_id = $menu_item->ID;	                 
	                $content .= '<li class="item">';
	                $content .= $title;
	            }	 
	            if ( $parent_id == $menu_item->menu_item_parent ) {	 
	                if ( !$submenu ) {
	                    $submenu = true;
	                    $content .= '<ul class="sub-menu">';
	                }	 
	                $content .= '<li class="item">';
	                $content .= $title;
	                $content .= '</li>';	 
	                if ( $menu_items[ $count + 1 ]->menu_item_parent != $parent_id && $submenu ){
	                    $content .= '</ul>';
	                    $submenu = false;
	                }	 
	            }	 
	            if ( $menu_items[ $count + 1 ]->menu_item_parent != $parent_id ) { 
	                $content .= '</li>';      
	                $submenu = false;
	            }	 
	            $count++;
	        }	         
	    $content .= '</ul>';

	    $content .= '<br>';

	    $menu = wp_get_nav_menu_object( $locations[ 'slideout' ] );
		$menu_items = wp_get_nav_menu_items($menu->term_id);

		$content .= '<h2>Mobile Navigation Menu</h2> <br><span class="small_text">Menu Name: ' . $menu->name . '</span> <a class="edit_this_link" href="' . $url . '/wp-admin/nav-menus.php?action=edit&menu=' . $menu->term_id . '">manage this menu</a>';
		//$content .= '<div class="display_text">';
			//$content .= 'Your mobile menu is "<b>' . $menu->name . '</b>".  All menu links, including dropdown items, are listed below for reference.';
		//$content .= '</div>';
		//$content .= '<br>"' . $menu->name . '"';
	    $content .= '<ul class="main-nav">';	 
	        $count = 0;
	        $submenu = false;	         
	        foreach( $menu_items as $menu_item ) {
	            $title = $menu_item->title;	             
	            if ( !$menu_item->menu_item_parent ) {
	                $parent_id = $menu_item->ID;	                 
	                $content .= '<li class="item">';
	                $content .= $title;
	            }	 
	            if ( $parent_id == $menu_item->menu_item_parent ) {	 
	                if ( !$submenu ) {
	                    $submenu = true;
	                    $content .= '<ul class="sub-menu">';
	                }	 
	                $content .= '<li class="item">';
	                $content .= $title;
	                $content .= '</li>';	 
	                if ( $menu_items[ $count + 1 ]->menu_item_parent != $parent_id && $submenu ){
	                    $content .= '</ul>';
	                    $submenu = false;
	                }	 
	            }	 
	            if ( $menu_items[ $count + 1 ]->menu_item_parent != $parent_id ) { 
	                $content .= '</li>';      
	                $submenu = false;
	            }	 
	            $count++;
	        }
	    $content .= '</ul>';

	    $content .= '<br>';	         

	    $menu = wp_get_nav_menu_object( $locations[ 'secondary' ] );
		$menu_items = wp_get_nav_menu_items($menu->term_id);

		$content .= '<h2>Secondary Navigation Menu</h2> <br><span class="small_text">Menu Name: ' . $menu->name . '</span> <a class="edit_this_link" href="' . $url . '/wp-admin/nav-menus.php?action=edit&menu=' . $menu->term_id . '">manage this menu</a>';
		//$content .= '<div class="display_text">';
			//$content .= 'Your mobile menu is "<b>' . $menu->name . '</b>".  All menu links, including dropdown items, are listed below for reference.';
		//$content .= '</div>';
		//$content .= '<br>"' . $menu->name . '"';
	    $content .= '<ul class="main-nav">';	 
	        $count = 0;
	        $submenu = false;	         
	        foreach( $menu_items as $menu_item ) {
	            $title = $menu_item->title;	             
	            if ( !$menu_item->menu_item_parent ) {
	                $parent_id = $menu_item->ID;	                 
	                $content .= '<li class="item">';
	                $content .= $title;
	            }	 
	            if ( $parent_id == $menu_item->menu_item_parent ) {	 
	                if ( !$submenu ) {
	                    $submenu = true;
	                    $content .= '<ul class="sub-menu">';
	                }	 
	                $content .= '<li class="item">';
	                $content .= $title;
	                $content .= '</li>';	 
	                if ( $menu_items[ $count + 1 ]->menu_item_parent != $parent_id && $submenu ){
	                    $content .= '</ul>';
	                    $submenu = false;
	                }	 
	            }	 
	            if ( $menu_items[ $count + 1 ]->menu_item_parent != $parent_id ) { 
	                $content .= '</li>';      
	                $submenu = false;
	            }	 
	            $count++;
	        }	         
	    $content .= '</ul>';	
	    	
	$content .= '</div>';

    return $content;

    return get_terms( 'nav_menu', array( 'hide_empty' => true ) );
}
add_shortcode( 'pelican-list-menu', 'pelican_list_menu' );


/* list all user-editable widgets in dashboard */
function pelican_list_widgets() {
	$url = get_bloginfo('url');
	$content = '<div class="list_widgets">';
	    $content .= '<h2>Widgets</h2> <a class="edit_all_link" href="' . $url . '/wp-admin/widgets.php">manage all widgets</a>'; 
	$content .= '</div>';
	//$content .= '<div class="display_text">';
		//$content .= 'Widgets are content blocks that display in specified areas.  All available areas with active widgets are listed below.';
	//$content .= '</div>';

    global $wp_registered_sidebars;

    $content .= '<div class="list_widgets">';
	    $content .= '<ul>';
	    $x = 0;
		foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
			if ( is_active_sidebar( $sidebar["id"] ) ) {
		    	$content .= '<li class="' . ucwords( $sidebar["id"] ) . '">';
		    		$sidebars_widgets = wp_get_sidebars_widgets();
		            $content .= ucwords( $sidebar["name"] );
		            $num = count( (array) $sidebars_widgets[ $sidebar["id"] ] );
		            if ($num == '0') {
		            	//
		            } else {
		            	$content .= '<span class="widget_num">' . $num . ' widget</span>';
		            	$x++;
		            }
		    	$content .= '</li>';
		    }
		}
		$content .= '</ul>';
		if ($x == 0) {
			$content .= 'No active widgets.';
		}
	$content .= '</div>';

    return $content;
}
add_shortcode( 'pelican-list-widgets', 'pelican_list_widgets' );


/* display events calendar */
function pelican_list_events($query) {
	$url = get_bloginfo('url');
	
	$content = '<div id="dash_list_events" class="list_events">';

	    $content .= '<h2>Events</h2> <a class="edit_all_link" href="' . $url . '/wp-admin/edit.php?post_type=events">manage all events</a>';

	$content .= '</div>';

    return $content; 

}
add_shortcode( 'pelican-list-events', 'pelican_list_events' );


/* list user payments
still trying to figure this one out */
function pelican_list_payments() {
	$url = get_bloginfo('url');
	$content = '<div class="list_widgets">';
	    $content .= 'Your Payment Schedule <a class="edit_all_link" href="' . $url . '/wp-admin/widgets.php">edit all widgets</a><br>Widgets are content blocks that can automatically display dynamic content on your site.  They must be managed from the widgets admin panel.';
	$content .= '</div>';

    return $content;
}
add_shortcode( 'pelican-list-payments', 'pelican_list_payments' );



