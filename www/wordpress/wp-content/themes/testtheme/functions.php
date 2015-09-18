<?php

add_action( 'wp_enqueue_scripts', function() {
	function twentyfifteen_stylesheet_uri() {
		remove_action( 'stylesheet_uri', __FUNCTION__ );
		return trailingslashit( get_template_directory_uri() ) . 'style.css';
	}
	add_filter( 'stylesheet_uri', 'twentyfifteen_stylesheet_uri' );
} );

add_action( 'wp_enqueue_scripts', function() {
	$theme = wp_get_theme();
	wp_enqueue_style(
		$theme->get_stylesheet(),
		$theme->get_stylesheet_directory_uri() . '/style.css',
		[],
		$theme->get( 'Version' )
	);
}, 11 );

/**
 *
 */

$repo = mimosafa\WP\Repository\Definition::instance( 'my-test-' );
$repo->post_type( 'aaa', [ 'public' => true, 'menu_icon' => 'fff', 'capability_type' => [ 'eee', 'eees' ] ] );
$repo->post_type( 'bbb' );


