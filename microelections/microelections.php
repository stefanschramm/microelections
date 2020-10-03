<?php

/**
 * Plugin Name: MicroElections
 * Plugin URI: https://github.com/stefanschramm/microelections
 * Description: Allows everyone to create polls that attempt to be as anonymous and transparent as possible.
 * Version: 1.0.2
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author: Stefan Schramm
 * Author URI: https://github.com/stefanschramm
 * License: GPL v3 or later
 * Text Domain: microelections
 * Domain path: /languages
 */

use MicroElections\Plugin;
use MicroElections\Setup;

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MICROELECTIONS_PATH', plugin_dir_path( __FILE__ ) );

spl_autoload_register( static function ( string $class ) {
	$classComponents = explode( '\\', $class );
	if ( $classComponents[0] !== 'MicroElections' ) {
		return;
	}
	$classComponents = array_map( 'strtolower', $classComponents );
	$classComponents[0] = 'includes';
	$file = MICROELECTIONS_PATH . join( DIRECTORY_SEPARATOR, $classComponents ) . '.php';
	if ( is_file( $file ) ) {
		require_once( $file );
	}
} );

function microelections_activate() {
	Setup::activate();
}

register_activation_hook( __FILE__, 'microelections_activate' );

function microelections_deactivate() {
	Setup::deactivate();
}

register_deactivation_hook( __FILE__, 'microelections_deactivate' );

function microelections_uninstall() {
	Setup::uninstall();
}

register_uninstall_hook( __FILE__, 'microelections_uninstall' );

add_action( 'init', [ Plugin::get_instance(), 'init' ] );
