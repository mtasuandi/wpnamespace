<?php
namespace Teguh;

use Teguh\Helpers\String;
use Teguh\Helpers\PluginUpdateChecker;

class Init {
	protected $slug;
	protected $api_key_option_name;
	protected $path;
	protected $autoupdate_endpoint;

	/**
	 * Class Constructor
	 * @param 	$slug 								string
	 * @param 	$api_key_option_name 	string
	 * @param 	$path 								string
	 * @param 	$autoupdate_endpoint 	string
	 * @return 	void
	 */
	public function __construct( $slug, $api_key_option_name, $path, $autoupdate_endpoint ) {
		$this->slug 								= $slug;
		$this->api_key_option_name 	= $api_key_option_name;
		$this->path 								= $path;
		$this->autoupdate_endpoint 	= $autoupdate_endpoint;
	}

	/**
	 * WordPress Hooks
	 */
	public function run() {
		register_activation_hook( __FILE__, [ $this, 'check_install' ] );
		add_action( 'init', [ $this, 'autoupdate' ] );
	}

	/**
	 * Check if WordPress is Multisite
	 */
	public function check_install( $networkwide ) {
		global $wpdb, $switched;
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $networkwide ) {
				$old_blog	= $wpdb->blogid;
				$blogids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs" ) );
				foreach ( $blogids as $blogid ) {
					switch_to_blog( $blogid );
					$this->install();
				}
				switch_to_blog( $old_blog );
				return;
			}
		}
		$this->install();
	}

	/**
	 * Generate new API after plugin activation
	 */
	public function install() {
		$api_key = String::generate_key();
		$get_existing_key = get_option( $this->api_key_option_name );
		if ( false == $get_existing_key ) {
			update_option( $this->api_key_option_name, $api_key );
		}
	}

	/**
	 * Perform automatic check for new version
	 */
	public function autoupdate() {
		$uC = new PluginUpdateChecker( $this->autoupdate_endpoint, $this->path . $this->slug . '.php', $this->slug );
	}
}