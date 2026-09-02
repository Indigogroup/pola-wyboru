<?php
/**
 * Public class - handles public-facing functionality
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Public
 *
 * Handles public functionality
 */
class Pola_Wyboru_Public {

	/**
	 * Instance of Pola_Wyboru_Shortcode
	 *
	 * @var Pola_Wyboru_Shortcode
	 */
	private $shortcode;

	/**
	 * Instance of Pola_Wyboru_AJAX
	 *
	 * @var Pola_Wyboru_AJAX
	 */
	private $ajax;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->shortcode = new Pola_Wyboru_Shortcode();
		$this->ajax      = new Pola_Wyboru_AJAX();
		$this->register_hooks();
	}

	/**
	 * Register public hooks
	 */
	private function register_hooks() {
		// Enqueue styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// Initialize WooCommerce session
		add_action( 'wp_loaded', array( $this, 'init_woocommerce_session' ) );
	}

	/**
	 * Enqueue public assets
	 */
	public function enqueue_assets() {
		// Enqueue CSS
		wp_enqueue_style(
			'pola-wyboru-public',
			POLA_WYBORU_PLUGIN_URL . 'assets/css/pola-wyboru-public.css',
			array(),
			POLA_WYBORU_VERSION
		);

		// Enqueue JavaScript
		wp_enqueue_script(
			'pola-wyboru-public',
			POLA_WYBORU_PLUGIN_URL . 'assets/js/pola-wyboru-public.js',
			array( 'jquery', 'wc-add-to-cart' ),
			POLA_WYBORU_VERSION,
			true
		);

		// Localize script with AJAX URL and nonce
		wp_localize_script(
			'pola-wyboru-public',
			'pola_wyboru_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'pola_wyboru_nonce' ),
			)
		);
	}

	/**
	 * Initialize WooCommerce session
	 */
	public function init_woocommerce_session() {
		if ( class_exists( 'WooCommerce' ) && ! is_admin() ) {
			if ( null === WC()->session ) {
				WC()->session = new WC_Session_Handler();
				WC()->session->init();
			}
		}
	}
}
