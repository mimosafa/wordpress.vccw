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


function add_posttype() {
	$post_type = "archives";
	$name = "お知らせ";
	$singular_name = "お知らせ";
	$params = array(
		'labels' => array(
			'name' => $name,
			'singular_name' => $singular_name,
			'add_new' => '新規追加',
			'add_new_item' => $singular_name.'を新規追加',
			'edit_item' => $singular_name.'を編集する',
			'new_item' => '新規'.$singular_name,
			'all_items' => $singular_name.'一覧',
			'view_item' => $singular_name.'を表示',
			'search_items' => '検索する',
			'not_found' => $singular_name.'が見つかりませんでした。',
			'not_found_in_trash' => 'ゴミ箱内に'.$singular_name.'が見つかりませんでした。',
		),
		'public' => true,
		'has_archive' => true,
		'show_ui' => true,
		'menu_position' => 4,
		'supports' => array(
			'title',
			'editor',
			'thumbnail',
		),
	);
	register_post_type($post_type, $params);
}
add_action('init', 'add_posttype');

add_filter( 'wp_get_attachment_image_attributes', 'my_awesome_attachment_add_class' );
function my_awesome_attachment_add_class( $attr ) {
	if ( ! isset( $attr['class'] ) )
		$attr['class'] = '';
	$attr['class'] .= ' aaaa bbbb';
	return $attr;
}

/**
 *
 */

$repo = mimosafa\WP\Repository\Definition::instance( 'my-test-' );
$repo->post_type( 'aaa', [ 'public' => true, 'menu_icon' => 'fff', 'capability_type' => [ 'eee', 'eees' ] ] );
