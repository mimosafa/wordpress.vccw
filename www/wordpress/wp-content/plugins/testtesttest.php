<?php
/*
Plugin Name: TestTestTest
Description: Only for test
*/

/**
 * カテゴリーという名の投稿
 */
add_action( 'init', 'register_post_the_name_category' );
add_filter( 'option_category_base', 'the_option_category_base' );
function register_post_the_name_category() {
	register_post_type( 'aaa', [ 'public' => true, 'rewrite' => [ 'slug' => 'category' ] ] );
}
function the_option_category_base() {
	return 'topics';
}

/**
 * 標準の投稿を無効に
 */
// Admin Menu
add_action( 'admin_menu', 'admin_menu_remove_post_edit_page' );
function admin_menu_remove_post_edit_page() {
	remove_menu_page( 'edit.php' );
}

add_action( 'admin_init', 'deregister_post_type_post_in_admin' );
function deregister_post_type_post_in_admin() {
	global $pagenow;
	static $pages = array( 'post.php', 'post-new.php', 'edit.php' );
	if ( in_array( $pagenow, $pages, true ) ) {
		global $wp_post_types;
		if ( isset( $wp_post_types['post'] ) ) {
			unset( $wp_post_types['post'] );
		}
	}
}

add_action( 'init', 'deregister_builtin_taxonomies_for_post' );
function deregister_builtin_taxonomies_for_post() {
	global $wp_taxonomies;
	foreach ( array( 'category', 'post_tag' ) as $tax ) {
		foreach ( $wp_taxonomies[$tax]->object_type as $i => $object_type ) {
			if ( $object_type === 'post' ) {
				unset( $wp_taxonomies[$tax]->object_type[$i] );
			}
		}
	}
}
add_filter( 'wp_count_posts', 'wp_count_posts_no_post', 10, 2 );
function wp_count_posts_no_post( $counts, $type ) {
	return $type === 'post' ? 0 : $counts;
}
add_filter( 'dashboard_recent_posts_query_args', function( $query_args ) {
	if ( $query_args['post_type'] === 'post' ) {
		$query_args['post_type'] = get_post_types( array( 'public' => true, '_builtin' => false ) );
	}
	return $query_args;
} );

add_action( 'pre_get_comments', 'pre_get_comments_exclude_post' );
function pre_get_comments_exclude_post( $wp_comments ) {
	$post_types = get_post_types( array( 'public' => true ) );
	$array = array();
	foreach ( $post_types as $post_type ) {
		if ( $post_type === 'post' ) {
			continue;
		}
		if ( post_type_supports( $post_type, 'comments' ) ) {
			$array[] = $post_type;
		}
	}
	$wp_comments->query_vars['post_type'] = $array;
}
add_filter( 'get_the_terms', function( $terms, $id, $taxonomy ) {
	if ( in_array( $taxonomy, array( 'category', 'post_tag' ), true ) && get_post_type( $id ) === 'post' ) {
		return array();
	}
	return $terms;
}, 10, 3 );

add_filter( 'comments_open', function( $open, $post_id ) {
	if ( get_post_type( $post_id ) === 'post' ) {
		return false;
	}
	return $open;
}, 10, 2 );
add_filter( 'get_comments_number', function( $count, $post_id ) {
	if ( get_post_type( $post_id ) === 'post' ) {
		return 0;
	}
	return $count;
}, 10, 2 );
