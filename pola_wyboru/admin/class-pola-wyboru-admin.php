<?php
/**
 * Admin class - handles admin initialization
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Admin
 *
 * Handles admin functionality
 */
class Pola_Wyboru_Admin {

	/**
	 * Admin settings instance
	 *
	 * @var Pola_Wyboru_Admin_Settings
	 */
	private $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->settings = new Pola_Wyboru_Admin_Settings();
		$this->register_hooks();
	}

	/**
	 * Register admin hooks
	 */
	private function register_hooks() {
		// Add menu and settings pages
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_product_heel_settings' ) );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'GASSU Configurator Settings', 'pola_wyboru' ),
			__( 'GASSU Configurator', 'pola_wyboru' ),
			'manage_options',
			'pola-wyboru-settings',
			array( $this->settings, 'render_settings_page' )
		);
	}

	/**
	 * Add product heel height display name settings
	 */
	public function add_product_heel_settings() {
		echo '<div class="pola-wyboru-admin-settings">';
		$this->settings->render_heel_display_names();
		echo '</div>';
	}
}
