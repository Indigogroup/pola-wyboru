<?php
/**
 * Plugin Name: Pola Wyboru - GASSU Product Configurator
 * Plugin URI: https://indigogroup.pl
 * Description: WooCommerce product configurator for GASSU footwear - allows customers to configure heel height, sole type, shoe width, and custom requirements.
 * Version: 1.0.0
 * Author: Indigogroup
 * Author URI: https://indigogroup.pl
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * Text Domain: pola_wyboru
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'POLA_WYBORU_VERSION', '1.0.0' );
define( 'POLA_WYBORU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'POLA_WYBORU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'POLA_WYBORU_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main plugin class
 */
class Pola_Wyboru {

	/**
	 * Instance of the plugin
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
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->register_hooks();
	}

	/**
	 * Load plugin dependencies
	 */
	private function load_dependencies() {
		// Core classes
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-loader.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-product-mapper.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-session-manager.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-configurator.php';

		// Admin classes
		require_once POLA_WYBORU_PLUGIN_DIR . 'admin/class-pola-wyboru-admin.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'admin/class-pola-wyboru-admin-settings.php';

		// Public classes
		require_once POLA_WYBORU_PLUGIN_DIR . 'public/class-pola-wyboru-public.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'public/class-pola-wyboru-shortcode.php';
		require_once POLA_WYBORU_PLUGIN_DIR . 'public/class-pola-wyboru-ajax.php';

		// Integrations
		require_once POLA_WYBORU_PLUGIN_DIR . 'includes/class-pola-wyboru-order-integration.php';
	}

	/**
	 * Set plugin text domain
	 */
	private function set_locale() {
		load_plugin_textdomain(
			'pola_wyboru',
			false,
			dirname( POLA_WYBORU_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Register main hooks
	 */
	private function register_hooks() {
		// Admin initialization
		add_action( 'admin_init', array( $this, 'init_admin' ) );

		// Public initialization
		add_action( 'wp_loaded', array( $this, 'init_public' ) );

		// Plugin activation/deactivation
		register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );
	}

	/**
	 * Initialize admin functionality
	 */
	public function init_admin() {
		if ( is_admin() ) {
			new Pola_Wyboru_Admin();
		}
	}

	/**
	 * Initialize public functionality
	 */
	public function init_public() {
		if ( ! is_admin() ) {
			new Pola_Wyboru_Public();
		}
	}

	/**
	 * Plugin activation
	 */
	public function activate_plugin() {
		// Ensure WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( POLA_WYBORU_PLUGIN_BASENAME );
			wp_die( 'This plugin requires WooCommerce to be activated.' );
		}

		// Initialize default options
		$this->init_default_options();

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	public function deactivate_plugin() {
		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Initialize default plugin options
	 */
	private function init_default_options() {
		// Initialize heel display names if not set
		if ( ! get_option( 'pola_wyboru_heel_display_names' ) ) {
			update_option( 'pola_wyboru_heel_display_names', array() );
		}
	}
}

/**
 * Initialize plugin
 */
function pola_wyboru_init() {
	Pola_Wyboru::get_instance();
}

add_action( 'plugins_loaded', 'pola_wyboru_init' );

/**
 * Get plugin instance
 *
 * @return Pola_Wyboru
 */
function pola_wyboru() {
	return Pola_Wyboru::get_instance();
}
