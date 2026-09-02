<?php
/**
 * Plugin Name: Pola Wyboru - GASSU Configurator
 * Plugin URI: https://github.com/Indigogroup/pola-wyboru
 * Description: Advanced product configurator for GASSU shoes with heel height, sole type, width, and custom options
 * Version: 1.0.0
 * Author: Indigogroup
 * Author URI: https://github.com/Indigogroup
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pola_wyboru
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Requires Plugins: woocommerce
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'POLA_WYBORU_VERSION', '1.0.0' );
define( 'POLA_WYBORU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'POLA_WYBORU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'POLA_WYBORU_PLUGIN_FILE', __FILE__ );

/**
 * Class Pola_Wyboru
 *
 * Main plugin class
 */
class Pola_Wyboru {

	/**
	 * Single instance of the class
	 *
	 * @var Pola_Wyboru
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Pola_Wyboru
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	/**
	 * Load plugin dependencies
	 */
	private function load_dependencies() {
		// Load core classes
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-loader.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-product-mapper.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-session-manager.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-configurator.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-order-integration.php';

		// Load admin classes
		if ( is_admin() ) {
			require_once POLA_WYBORU_PLUGIN_DIR . 'admin/class-pola-wyboru-admin.php';
			require_once POLA_WYBORU_PLUGIN_DIR . 'admin/class-pola-wyboru-admin-settings.php';
		}

		// Load public classes
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			require_once POLA_WYBORU_PLUGIN_DIR . 'public/class-pola-wyboru-public.php';
			require_once POLA_WYBORU_PLUGIN_DIR . 'public/class-pola-wyboru-shortcode.php';
			require_once POLA_WYBORU_PLUGIN_DIR . 'public/class-pola-wyboru-ajax.php';
		}
	}

	/**
	 * Register plugin hooks
	 */
	private function register_hooks() {
		// Plugin activation/deactivation
		register_activation_hook( POLA_WYBORU_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( POLA_WYBORU_PLUGIN_FILE, array( $this, 'deactivate' ) );

		// Plugin initialization
		add_action( 'plugins_loaded', array( $this, 'check_dependencies' ) );
		add_action( 'init', array( $this, 'init_plugin' ) );
	}

	/**
	 * Check plugin dependencies
	 */
	public function check_dependencies() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_woocommerce_notice' ) );
			return false;
		}

		return true;
	}

	/**
	 * Initialize plugin
	 */
	public function init_plugin() {
		// Check dependencies
		if ( ! $this->check_dependencies() ) {
			return;
		}

		// Initialize admin
		if ( is_admin() ) {
			new Pola_Wyboru_Admin();
		}

		// Initialize public
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			new Pola_Wyboru_Public();
		}

		// Initialize order integration
		new Pola_Wyboru_Order_Integration();

		// Load text domain for translations
		load_plugin_textdomain( 'pola_wyboru', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Activate plugin
	 */
	public static function activate() {
		// Check WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( esc_html__( 'This plugin requires WooCommerce to be installed and activated.', 'pola_wyboru' ) );
		}

		// Initialize default options
		if ( ! get_option( 'pola_wyboru_heel_display_names' ) ) {
			update_option( 'pola_wyboru_heel_display_names', array() );
		}

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Deactivate plugin
	 */
	public static function deactivate() {
		// Cleanup if needed
		flush_rewrite_rules();
	}

	/**
	 * Display missing WooCommerce notice
	 */
	public function missing_woocommerce_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p>
				<?php
				printf(
					esc_html__( 'Pola Wyboru requires %s to be installed and activated.', 'pola_wyboru' ),
				'<strong>WooCommerce</strong>'
				);
				?>
			</p>
		</div>
		<?php
	}
}

// Initialize plugin
Pola_Wyboru::get_instance();
