<?php

/** Tell WordPress to run theme_setup() when the 'after_setup_theme' hook is run. */

if ( ! function_exists( 'theme_setup' ) ):

function theme_setup() {

	/* This theme uses post thumbnails (aka "featured images")
	*  all images will be cropped to thumbnail size (below), as well as
	*  a square size (also below). You can add more of your own crop
	*  sizes with add_image_size. */
	add_theme_support( 'title-page' );
	add_theme_support( 'post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'] );
	add_theme_support( 'custom-background' );
	add_theme_support( 'custom-header' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'starter-content' );
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size(120, 90, true);
	add_image_size('square', 150, 150, true);
	add_image_size('square-large', 300, 300, true);


	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	/* This theme uses wp_nav_menu() in one location.
	* You can allow clients to create multiple menus by
  * adding additional menus to the array. */

	register_nav_menus( array(
		'primary' => esc_html__('Primary Navigation'),
		'footer'=> esc_html__('Footer Navigation'),
		'social' => esc_html__('Social Navigation')
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

}
endif;
add_action( 'after_setup_theme', 'theme_setup' );

/* Load in our CSS */
function my_theme_enqueue_styles() {
	wp_enqueue_style(
	  'font-css',
	  'https://fonts.googleapis.com/css2?family=Barlow:wght@400;700;800&family=Playfair+Display:ital,wght@0,800;1,700&display=swap'
	  );
	  
	  wp_enqueue_style(
		  'main-css',
		  get_stylesheet_directory_uri() . '/style.css',
		  [ 'font-css' ],
		  get_the_time()
	  );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


/* Add all our JavaScript files here.
We'll let WordPress add them to our templates automatically instead
of writing our own script tags in the header and footer. */

function my_theme_scripts() {
	//Don't use WordPress' local copy of jquery, load our own version from a CDN instead
	wp_deregister_script('jquery');
	wp_enqueue_script(
		'jquery',
		"http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js",
		false, //dependencies
		null, //version number
		true //load in footer
	);

	wp_enqueue_script(
		'scripts', //handle
		get_template_directory_uri() . '/js/scripts.js', //source
		array( 'jquery'), //dependencies
		time(), // version number
		true //load in footer
	);

	wp_enqueue_script(
		'fontawesome',
		'https://kit.fontawesome.com/738158de50.js',
		false,
		null,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'my_theme_scripts', 10 );


/* Custom Title Tags */

function base_theme_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() ) {
		return $title;
	}

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title = "$title $sep " . sprintf( __( 'Page %s', 'base_theme' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'base_theme_wp_title', 10, 2 );

/*
  Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function base_theme_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'base_theme_page_menu_args' );


/*
 * Sets the post excerpt length to 40 characters.
 */
function base_theme_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'base_theme_excerpt_length' );

/*
 * Returns a "Continue Reading" link for excerpts
 */
function base_theme_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">Read more</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and base_theme_continue_reading_link().
 */
function base_theme_auto_excerpt_more( $more ) {
	return ' &hellip;' . base_theme_continue_reading_link();
}
add_filter( 'excerpt_more', 'base_theme_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 */
function base_theme_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= base_theme_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'base_theme_custom_excerpt_more' );




/*
 * Register a single widget area.
 * You can register additional widget areas by using register_sidebar again
 * within base_theme_widgets_init.
 * Display in your template with dynamic_sidebar()
 */
function base_theme_widgets_init() {
	// Area 1, located at the top of the sidebar.
	// register_sidebar( array(
	// 	'name' => 'Primary Widget Area',
	// 	'id' => 'primary-widget-area',
	// 	'description' => 'The primary widget area',
	// 	'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
	// 	'after_widget' => '</li>',
	// 	'before_title' => '<h3 class="widget-title">',
	// 	'after_title' => '</h3>',
	// ) );
	register_sidebar([
		'name'          => esc_html__( 'Main Sidebar', 'Starter Theme' ),
		'id'            => 'main-sidebar',
		'description'   => esc_html__( 'Add widgets for main sidebar here', 'Starter Theme' ),
		'before widget' => '<section class="widget">',
		'after widget'  => '</section>',
		'before title'  => '<h2 class="widget-title">',
		'after-title'   => '</h2>',
	]);
}
add_action( 'widgets_init', 'base_theme_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 */
function base_theme_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'base_theme_remove_recent_comments_style' );


if ( ! function_exists( 'base_theme_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post???date/time and author.
 */
function base_theme_posted_on() {
	printf('<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s',
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr( 'View all posts by %s'), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'base_theme_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 */
function base_theme_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.';
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.';
	} else {
		$posted_in = 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.';
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

/* Get rid of junk! - Gets rid of all the crap in the header that you dont need */

function clean_stuff_up() {
	// windows live
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	// wordpress gen tag
	remove_action('wp_head', 'wp_generator');
	// comments RSS
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 3 );
}

add_action('init', 'clean_stuff_up');


/* Here are some utility helper functions for use in your templates! */

/* pre_r() - makes for easy debugging. <?php pre_r($post); ?> */
function pre_r($obj) {
	echo "<pre>";
	print_r($obj);
	echo "</pre>";
}

/* is_blog() - checks various conditionals to figure out if you are currently within a blog page */
function is_blog () {
	global  $post;
	$posttype = get_post_type($post );
	return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post')  ) ? true : false ;
}

/* get_post_parent() - Returns the current posts parent, if current post if top level, returns itself */
function get_post_parent($post) {
	if ($post->post_parent) {
		return $post->post_parent;
	}
	else {
		return $post->ID;
	}
}

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Header & Footer',
		'menu_title' => 'Header & Footer'
	));
	
}

function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}

add_action('acf/init', 'register_our_blocks');

function register_our_blocks() {

acf_register_block_type(array(
'name'            => 'image-text',
'title'           => __('Image and Text'),
'category'        => 'common',
'mode'            => 'auto',
'icon'            => 'nametag',
'render_template' => '/blocks/images-text.php',
'enqueue_style' => get_template_directory_uri() . '/admin/images-text.css'
));

//   === team member query

acf_register_block_type(array(
    'name'            => 'team-member',
    'title'           => __('Team Member'),
    'category'        => 'common',
    'mode'            => 'auto',
    'icon'            => 'nametag',
	'render_template' => '/blocks/team-member.php',
	'enqueue_style' => get_template_directory_uri() . '/admin/team-member.css'
  ));

}