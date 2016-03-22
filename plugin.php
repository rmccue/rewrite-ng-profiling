<?php
/**
 * Plugin Name: Rewrite Rethink Tester
 * Description: Rethink WordPress rewrites for the modern era.
 * Author Name: Ryan McCue
 * Author URI: https://rmccue.io/
 * Version: 0.1
 */

require __DIR__ . '/class-old-wp.php';
require __DIR__ . '/class-old-wp-rewrite.php';

add_action( 'setup_theme', function () {
	// Ensure rewrites are cleared
	delete_option( 'rewrite_rules' );

	if ( isset( $_GET['rewrite'] ) && $_GET['rewrite'] === 'old' ) {
		unset( $GLOBALS['wp_rewrite'] );
		unset( $GLOBALS['wp'] );
		$GLOBALS['wp_rewrite'] = new Old_WP_Rewrite();
		$GLOBALS['wp'] = new Old_WP();
	}
}, -10000);

/* Add test rewrite rules */
add_filter( 'rewrite_rules_array', function ( $rules ) {
	$new = array();
	for ($i = 0; $i < 1000; $i++) {
		$new[ 'test/' . $i . '/?$' ] = 'index.php?testnum=' . $i;
	}
	$new['measure-rewrites/?$'] = 'index.php?rewrites=measure';

	$GLOBALS['rewriteteststart'] = microtime( true );
	$GLOBALS['rewritememory'] = memory_get_usage();

	return array_merge( $new, $rules );
}, -1000 );

add_action( 'parse_request', function ( $wp ) {
	if ( $wp->matched_rule === 'measure-rewrites/?$' ) {
		header( 'Content-Type: text/plain' );
		$duration = microtime( true ) - $GLOBALS['rewriteteststart'];
		$mem = memory_get_usage() - $GLOBALS['rewritememory'];
		echo $duration . ',' . $mem . "\n";
		exit;
	}
});
