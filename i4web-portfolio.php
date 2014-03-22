<?php
/*
 * Plugin Name: i-4Web Portfolio Plugin
 * Description: Creates Portfolio Custom Post Type and Displays them on Website
 * Author: Jonathan Rivera
 * Version: 1.0
 */
 
 //Require the metabox class
 require_once( plugin_dir_path( __FILE__ ) . 'includes/metaboxes.php' );
 
 class I4web_Portfolio {

	/**
	 * Constructor
	 */
	function __construct() {
		//register an activation hook for the plugin
		register_activation_hook( __FILE__, array( $this, 'i4web_rewrite_flush' ) );
		
		//Add filter via instance and hook into post_type_link. Priority is set to 0 so it is executed first
		add_filter( 'post_type_link', array($this, 'i4web_portfolio_link' ), 0,2);
		
		//Add actions via instance
		add_action( 'init', array($this, 'cpt_create' ));
		add_action( 'init', array($this, 'create_taxonomies'));		
		add_action('init', array($this, 'i4web_custom_tags'));

	}
	
	/**
	 * Create the Portfolio CPT and register it
	 */
	 function cpt_create(){
	   $labels = array(
        'name' => _x( 'Portfolio', 'my_custom_post','custom' ),
        'singular_name' => _x( 'Portfolio', 'my_custom_post', 'custom' ),
        'add_new' => _x( 'Add New Project', 'my_custom_post', 'custom' ),
        'add_new_item' => _x( 'Add New Project', 'my_custom_post', 'custom' ),
        'edit_item' => _x( 'Edit Project', 'my_custom_post', 'custom' ),
        'new_item' => _x( 'New Project', 'my_custom_post', 'custom' ),
        'view_item' => _x( 'View Project', 'my_custom_post', 'custom' ),
        'search_items' => _x( 'Search Projects', 'my_custom_post', 'custom' ),
        'not_found' => _x( 'No Projects found', 'my_custom_post', 'custom' ),
        'not_found_in_trash' => _x( 'No Projects found in Trash', 'my_custom_post', 'custom' ),
        'parent_item_colon' => _x( 'Parent Project:', 'my_custom_post', 'custom' ),
        'menu_name' => _x( 'Portfolio', 'my_custom_post', 'custom' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Portfolio of Work',
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields','revisions' ), 
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 2,
        'menu_icon' => get_stylesheet_directory_uri() . '/assets/img/portfolio-icon.png',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array( 'slug' => 'portfolio', 'with_front' => false ),
        'public' => true,
        'has_archive' => true,
        'capability_type' => 'post'
    );  	
    register_post_type( 'i4web_portfolio', $args );//max 20 character cannot contain capital letters and spaces	
		
	}
	
	function create_taxonomies() {
	
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Type', 'taxonomy general name' ),
			'singular_name'     => _x( 'Type', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Types' ),
			'all_items'         => __( 'All Types' ),
			'parent_item'       => __( 'Parent Type' ),
			'parent_item_colon' => __( 'Parent Type:' ),
			'edit_item'         => __( 'Edit Type' ),
			'update_item'       => __( 'Update Type' ),
			'add_new_item'      => __( 'Add New Type' ),
			'new_item_name'     => __( 'New Type Name' ),
			'menu_name'         => __( 'Type' ),
		);
	
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'portfolio', 'with_front' => false ),
		);
	
		register_taxonomy( 'type', array( 'i4web_portfolio' ), $args );
		

	}

	function i4web_custom_tags() {
		
		//Rewrite the URL of a CPT to look pretty. i.e. Portfolio/Games/Project-name 
		add_rewrite_rule("^portfolio/([^/]+)/([^/]+)/?",'index.php?post_type=i4web_portfolio&type=$matches[1]&i4web_portfolio=$matches[2]','top');
		
		//Rewrite the URL of a CPT Taxonomy to look pretty. i.e. Portfolio/Games
		add_rewrite_rule("^portfolio/([^/]+)/?",'index.php?type=$matches[1]','top');
		
		
	}
	
	function i4web_portfolio_link( $post_link, $id = 0 ) {
	 
		$post = get_post($id);
	 
		if ( is_wp_error($post) || 'i4web_portfolio' != $post->post_type || empty($post->post_name) )
			return $post_link;
	 
		// Get the type:
		$terms = get_the_terms($post->ID, 'type');
	 
		if( is_wp_error($terms) || !$terms ) {
			$type = 'uncategorised';
		}
		else {
			$type_obj = array_pop($terms);
			$type = $type_obj->slug;
		}
	 
		return home_url(user_trailingslashit( "portfolio/$type/$post->post_name" ));
	}


	function i4web_rewrite_flush() {
    
	// First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    $this->cpt_create();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
	
	}
	
}

new I4web_Portfolio();
?>