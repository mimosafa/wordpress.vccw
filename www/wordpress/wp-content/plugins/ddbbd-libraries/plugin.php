<?php
/*
Plugin Name: Dana Don-Boom-Boom-Doo Libraries
Author: Toshimichi Mimoto
*/

namespace DDBBD\WP;

require_once 'ddbbd/FileLoader.php';
\DDBBD\FileLoader::init( __DIR__ . '/ddbbd', true );

$page1 = new Settings\Page();
$page2 = new Settings\Page( 'plugins.php' );
$page1->init( 'test1' );
$page2->init( 'test2' );
$page1->description( 'aaaa1' );
$page2->description( 'aaaa2' );
$page1->section( 'test_section1', 'Test Section 1' );
$page2->section( 'test_section2', 'Test Section 2' );
$page1->description( 'bbbb1' );
$page2->description( 'bbbb2' );
$page1->field( 'test_field1', 'Test Field 1' );
$page2->field( 'test_field2', 'Test Field 2' );
$page1->option( 'test-option1' );
$page2->option( 'test-option2' );
$page1->callback( 'checkbox' )->misc( [ 'label' => 'Check' ] );
$page2->callback( 'text' );
$page1->description( 'cccc1' );
$page2->description( 'cccc2' );
$page1->done();
$page2->done();

Types\Register::post_type( 'test', [ 'public' => true ] );
Types\Register::taxonomy( 'test_tax', 'test', [ 'public' => true ] );

$a = new Types\Builder( 'test-' );
$a->post_type( 'bbb' )->post_type( 'ccc' )->taxonomy( 'ttt' )->post_type( 'ddd' )->post_type( 'aaa' )->post_type( 'zzz' );
#var_dump( $a );
