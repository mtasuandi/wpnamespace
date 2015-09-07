<?php
namespace Teguh;

use Teguh\Helpers\String;

class MainPage {
	protected $slug;
	protected $api_key_option_name;
	protected $js_url;
	protected $nonce;
	protected $api_key;
	protected $main_page_properties;

	/**
	 * Class Constructor
	 * @param 	$slug 								string
	 * @param 	$api_key_option_name 	string
	 * @param 	$js_url 							string
	 * @param 	$nonce 								string
	 * @param 	$api_key 							string
	 * @param 	$main_page_properties object
	 * @return 	void
	 */
	public function __construct( $slug, $api_key_option_name, $js_url, $nonce, $api_key, $main_page_properties ) {
		$this->slug 								= $slug;
		$this->api_key_option_name 	= $api_key_option_name;
		$this->js_url 							= $js_url;
		$this->nonce 								= $nonce;
		$this->api_key 							= $api_key;
		$this->main_page_properties = $main_page_properties;
	}

	/**
	 * WordPress Hooks
	 */
	public function run() {
		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
		add_action( 'network_admin_menu', [ $this, 'add_menu_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_script' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
		add_action( 'wp_ajax_' . $this->slug . '_generate_new_api_key', [ $this, 'api_key_ajax' ] );
		add_action( 'wp_ajax_nopriv_' . $this->slug . '_generate_new_api_key', [ $this, 'api_key_ajax' ] );
	}

	/**
	 * Add WordPress menu page
	 */
	public function add_menu_page() {
		add_menu_page(
			$this->main_page_properties->page_title,
			$this->main_page_properties->menu_title,
			$this->main_page_properties->capability,
			$this->main_page_properties->menu_slug,
			[ $this, 'render_main_page' ],
			$this->main_page_properties->icon
		);
	}

	/**
	 * Render main page
	 */
	public function render_main_page() {
		?>
		<div class="wrap">
			<h2 class="nav-tab-wrapper" style="padding-left:0px;margin-bottom:10px;">
				<a class="nav-tab nav-tab-active" href="?page=<?php echo $this->slug; ?>">
					<span class="dashicons dashicons-dashboard"></span> Main
				</a>
			</h2>
			<form action="" method="POST" id="<?php echo $this->slug; ?>_form_api_key">
				<div>
					<label for="<?php echo $this->slug; ?>_api_key">
						<strong>Here is your token</strong>
					</label>
					<input type="text" name="<?php echo $this->slug; ?>_api_key" id="<?php echo $this->slug; ?>_api_key" value="<?php echo $this->api_key; ?>" readonly size="40" style="text-align:center">
					<?php wp_nonce_field( $this->nonce, $this->slug . '_nonce' ); ?>
				</div>
				<div style="margin-top:20px;">
					<input type="submit" id="<?php echo $this->slug; ?>_submit" value="Generate new Token" class="button button-primary">
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Include jQuery and internal javascript code
	 * @file location 	assets/js/app.js
	 */
	public function admin_script() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( $this->slug . '-app', $this->js_url . 'app.js', [], '2.0.0', false );
	}

	/**
	 * Initiate javascript code
	 * @related file 		assets/js/app.js
	 */
	public function admin_head() {
		?>
		<script type="text/javascript">
		  Teguh.init(['<?php echo $this->slug; ?>']);
		  Teguh.generateNewToken();
		  Teguh.onTokenFieldFocus();
		</script>
		<?php
	}

	/**
	 * Handle ajax request to generate new API Key
	 * @method 	POST
	 * @param 	nonce 		string
	 * @return 	json encoded
	 */
	public function api_key_ajax() {
		$nonce = sanitize_text_field( $_POST['nonce'] );
		
		if ( ! wp_verify_nonce( $nonce, $this->nonce ) ) {
			die( 'Security check failed!' );
		} else {
			$new_api_key = String::generate_key();
			update_option( $this->api_key_option_name, $new_api_key );
			$array_return = array( 'response_code' => 0, 'new_api_key' => $new_api_key );
			echo json_encode( $array_return );
			die();
		}
	}
}