<?php
/*
Plugin Name: Teguh
Plugin URI: http://mtasuandi.com
Description: Teguh
Version: 1.0.0
Author: M Teguh
Author URI: http://mtasuandi.com
License: Copyright 2015, All rights reserved
*/

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die( 'Abort!' );
}

use Teguh\Base;
use Teguh\Init;
use Teguh\MainPage;

/**
 * Autoloader
 * @url 	http://php.net/manual/en/function.spl-autoload-register.php
 */
spl_autoload_register( 'teguh_autoloader' );
function teguh_autoloader( $class_name ) {
	if ( false !== strpos( $class_name, 'Teguh' ) ) {
		$classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
		$class_file = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name ) . '.php';
		if ( file_exists( $classes_dir . $class_file ) ) {
			require_once $classes_dir . $class_file;
		}
	}
}

/**
 * Initialize plugin after plugin loaded
 * @url 	https://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded
 */
add_action( 'plugins_loaded', 'teguh_init' );
function teguh_init() {
	$base = new Base();

	/**
	 * Main parameters
	 */
	$base['slug']											= 'teguh';
	$base['api_key_option_name']			= 'teguh_api_key';
	$base['path'] 										= realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
	$base['url'] 											= plugin_dir_url( __FILE__ );
	$base['assets_url']								= $base['url'] . 'assets' . DIRECTORY_SEPARATOR;
	$base['js_url']										= $base['assets_url'] . 'js' . DIRECTORY_SEPARATOR;
	$base['version'] 									= '2.0.0';
	$base['nonce']										= 'teguh-nonce';
	$base['api_key']									=	get_option( $base['api_key_option_name'] );
	$base['autoupdate_endpoint']			= 'http://repositories.mtasuandi.com/teguh/teguh/metadata.json';

	/**
	 * Register Init class
	 */
	$base['init']											= function ( $base ) {
		return new Init( $base['slug'], $base['api_key_option_name'], $base['path'], $base['autoupdate_endpoint'] );
	};

	/**
	 * Properties or parameters required by main page, assigned to main_page_properties
	 * @param 	$page_title 		string
	 * @param 	$menu_title 		string
	 * @param 	$capability 		string
	 * @param 	$menu_slug 			string
	 * @param 	$icon 					string
	 */
	$oMainPageProperties = new \stdClass();
	$oMainPageProperties->page_title 	= __( 'Teguh', $base['slug'] );
	$oMainPageProperties->menu_title 	= __( 'Teguh', $base['slug'] );
	$oMainPageProperties->capability 	= 'manage_options';
	$oMainPageProperties->menu_slug		= $base['slug'];
	$oMainPageProperties->icon 				= 'dashicons-share-alt';
	$base['main_page_properties'] 		= $oMainPageProperties;
	
	/**
	 * Register MainPage class
	 */
	$base['main_page'] 								= function ( $base ) {
		return new MainPage( $base['slug'], $base['api_key_option_name'], $base['js_url'], $base['nonce'], $base['api_key'], $base['main_page_properties'] );
	};

	/**
	 * Execute
	 */
	$base->run();
}