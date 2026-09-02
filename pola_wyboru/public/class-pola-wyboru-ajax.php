<?php
/**
 * AJAX class - handles AJAX requests for configurator
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_AJAX
 *
 * Handles AJAX functionality
 */
class Pola_Wyboru_AJAX {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_pola_wyboru_change_heel_height', array( $this, 'change_heel_height' ) );
		add_action( 'wp_ajax_nopriv_pola_wyboru_change_heel_height', array( $this, 'change_heel_height' ) );
	}

	/**
	 * Handle heel height change via AJAX
	 */
	public function change_heel_height() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'pola_wyboru_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed', 'pola_wyboru' ) );
		}

		// Get POST data
		if ( ! isset( $_POST['product_id'] ) || ! isset( $_POST['heel_height'] ) ) {
			wp_send_json_error( __( 'Missing required parameters', 'pola_wyboru' ) );
		}

		$product_id    = intval( wp_unslash( $_POST['product_id'] ) );
		$heel_height   = sanitize_text_field( wp_unslash( $_POST['heel_height'] ) );
		$current_product = wc_get_product( $product_id );

		if ( ! $current_product ) {
			wp_send_json_error( __( 'Product not found', 'pola_wyboru' ) );
		}

		// Get model and color variant from current product
		$model         = Pola_Wyboru_Product_Mapper::get_product_attribute( $current_product, 'pa_model' );
		$color_variant = Pola_Wyboru_Product_Mapper::get_product_attribute( $current_product, 'pa_wersja_kolorystyczna' );

		if ( ! $model || ! $color_variant ) {
			wp_send_json_error( __( 'Product attributes missing', 'pola_wyboru' ) );
		}

		// Find product with matching heel height
		$target_product = Pola_Wyboru_Product_Mapper::get_product_by_criteria( $model, $color_variant, $heel_height );

		if ( ! $target_product ) {
			wp_send_json_error( __( 'Product with selected heel height not found', 'pola_wyboru' ) );
		}

		// Save configuration to session - handle both array and individual values
		if ( isset( $_POST['configuration'] ) && is_array( $_POST['configuration'] ) ) {
			$config = array();
			foreach ( wp_unslash( $_POST['configuration'] ) as $key => $value ) {
				$key = sanitize_key( $key );
				// Use different sanitization based on field type
				if ( in_array( $key, array( 'custom_colorimetry_text', 'custom_size_text' ), true ) ) {
					$config[ $key ] = sanitize_textarea_field( $value );
				} elseif ( in_array( $key, array( 'custom_colorimetry_enabled', 'custom_size_enabled' ), true ) ) {
					$config[ $key ] = (bool) $value;
				} else {
					$config[ $key ] = sanitize_text_field( $value );
				}
			}
			Pola_Wyboru_Session_Manager::set_config( $config );
		}

		wp_send_json_success(
			array(
				'product_url' => $target_product['url'],
				'product_id'  => $target_product['id'],
			)
		);
	}
}
