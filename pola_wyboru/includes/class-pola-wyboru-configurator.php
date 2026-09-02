<?php
/**
 * Configurator class - main business logic for product configuration
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Configurator
 *
 * Main configurator logic
 */
class Pola_Wyboru_Configurator {

	/**
	 * Get display name for heel height
	 *
	 * @param string $heel_height The heel height value.
	 * @return string Display name or original value if not configured.
	 */
	public static function get_heel_display_name( $heel_height ) {
		$heel_display_names = get_option( 'pola_wyboru_heel_display_names', array() );

		if ( isset( $heel_display_names[ $heel_height ] ) && ! empty( $heel_display_names[ $heel_height ] ) ) {
			return $heel_display_names[ $heel_height ];
		}

		return $heel_height;
	}

	/**
	 * Get all heel display names configuration
	 *
	 * @return array Heel display names configuration.
	 */
	public static function get_all_heel_display_names() {
		return get_option( 'pola_wyboru_heel_display_names', array() );
	}

	/**
	 * Update heel display names
	 *
	 * @param array $heel_display_names Heel display names configuration.
	 */
	public static function update_heel_display_names( $heel_display_names ) {
		$sanitized = array();

		foreach ( $heel_display_names as $key => $value ) {
			$sanitized[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
		}

		update_option( 'pola_wyboru_heel_display_names', $sanitized );
	}

	/**
	 * Get available heel heights for current product
	 *
	 * @param int $product_id Product ID.
	 * @return array Array of heel heights with display names.
	 */
	public static function get_heel_options( $product_id ) {
		$heights = Pola_Wyboru_Product_Mapper::get_available_heel_heights( $product_id );
		$options = array();

		foreach ( $heights as $height ) {
			$options[ $height ] = self::get_heel_display_name( $height );
		}

		return $options;
	}

	/**
	 * Validate configuration data
	 *
	 * @param array $data Configuration data to validate.
	 * @return bool|array True if valid, array of errors if not.
	 */
	public static function validate_configuration( $data ) {
		$errors = array();

		// Sole type
		if ( isset( $data['sole_type'] ) && ! in_array( $data['sole_type'], array( 'welur', 'skóra' ), true ) ) {
			$errors[] = __( 'Invalid sole type', 'pola_wyboru' );
		}

		// Shoe width
		if ( isset( $data['shoe_width'] ) && ! in_array( $data['shoe_width'], array( 'wąska', 'standardowa', 'szeroka' ), true ) ) {
			$errors[] = __( 'Invalid shoe width', 'pola_wyboru' );
		}

		// Toe cushion
		if ( isset( $data['toe_cushion'] ) && ! in_array( $data['toe_cushion'], array( 'pianka', 'twardo' ), true ) ) {
			$errors[] = __( 'Invalid toe cushion option', 'pola_wyboru' );
		}

		return empty( $errors ) ? true : $errors;
	}
}
