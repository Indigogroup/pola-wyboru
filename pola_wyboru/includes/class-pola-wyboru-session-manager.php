<?php
/**
 * Session Manager class - handles WooCommerce session for configurator data
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Session_Manager
 *
 * Manages session data for product configuration
 */
class Pola_Wyboru_Session_Manager {

	/**
	 * Session key for storing configuration
	 *
	 * @var string
	 */
	const SESSION_KEY = 'pola_wyboru_config';

	/**
	 * Get all configuration from session
	 *
	 * @return array Configuration array.
	 */
	public static function get_config() {
		if ( ! WC()->session ) {
			return array();
		}

		$config = WC()->session->get( self::SESSION_KEY );

		return is_array( $config ) ? $config : array();
	}

	/**
	 * Set configuration in session
	 *
	 * @param array $config Configuration array.
	 */
	public static function set_config( $config ) {
		if ( ! WC()->session ) {
			return;
		}

		WC()->session->set( self::SESSION_KEY, $config );
	}

	/**
	 * Get specific configuration value
	 *
	 * @param string $key The configuration key.
	 * @param mixed  $default Default value if key not found.
	 * @return mixed Configuration value.
	 */
	public static function get( $key, $default = null ) {
		$config = self::get_config();

		return isset( $config[ $key ] ) ? $config[ $key ] : $default;
	}

	/**
	 * Set specific configuration value
	 *
	 * @param string $key The configuration key.
	 * @param mixed  $value The configuration value.
	 */
	public static function set( $key, $value ) {
		$config = self::get_config();
		$config[ $key ] = $value;
		self::set_config( $config );
	}

	/**
	 * Clear configuration from session
	 */
	public static function clear() {
		if ( ! WC()->session ) {
			return;
		}

		WC()->session->__unset( self::SESSION_KEY );
	}

	/**
	 * Get sole type
	 *
	 * @return string|null Sole type value.
	 */
	public static function get_sole_type() {
		return self::get( 'sole_type' );
	}

	/**
	 * Set sole type
	 *
	 * @param string $value Sole type value.
	 */
	public static function set_sole_type( $value ) {
		self::set( 'sole_type', sanitize_text_field( $value ) );
	}

	/**
	 * Get shoe width
	 *
	 * @return string|null Shoe width value.
	 */
	public static function get_shoe_width() {
		return self::get( 'shoe_width' );
	}

	/**
	 * Set shoe width
	 *
	 * @param string $value Shoe width value.
	 */
	public static function set_shoe_width( $value ) {
		self::set( 'shoe_width', sanitize_text_field( $value ) );
	}

	/**
	 * Get toe cushion
	 *
	 * @return string|null Toe cushion value.
	 */
	public static function get_toe_cushion() {
		return self::get( 'toe_cushion' );
	}

	/**
	 * Set toe cushion
	 *
	 * @param string $value Toe cushion value.
	 */
	public static function set_toe_cushion( $value ) {
		self::set( 'toe_cushion', sanitize_text_field( $value ) );
	}

	/**
	 * Get custom colorimetry flag
	 *
	 * @return bool Custom colorimetry flag.
	 */
	public static function get_custom_colorimetry_enabled() {
		return (bool) self::get( 'custom_colorimetry_enabled', false );
	}

	/**
	 * Set custom colorimetry flag
	 *
	 * @param bool $value Custom colorimetry flag.
	 */
	public static function set_custom_colorimetry_enabled( $value ) {
		self::set( 'custom_colorimetry_enabled', (bool) $value );
	}

	/**
	 * Get custom colorimetry text
	 *
	 * @return string|null Custom colorimetry text.
	 */
	public static function get_custom_colorimetry_text() {
		return self::get( 'custom_colorimetry_text' );
	}

	/**
	 * Set custom colorimetry text
	 *
	 * @param string $value Custom colorimetry text.
	 */
	public static function set_custom_colorimetry_text( $value ) {
		self::set( 'custom_colorimetry_text', sanitize_textarea_field( $value ) );
	}

	/**
	 * Get custom size flag
	 *
	 * @return bool Custom size flag.
	 */
	public static function get_custom_size_enabled() {
		return (bool) self::get( 'custom_size_enabled', false );
	}

	/**
	 * Set custom size flag
	 *
	 * @param bool $value Custom size flag.
	 */
	public static function set_custom_size_enabled( $value ) {
		self::set( 'custom_size_enabled', (bool) $value );
	}

	/**
	 * Get custom size text
	 *
	 * @return string|null Custom size text.
	 */
	public static function get_custom_size_text() {
		return self::get( 'custom_size_text' );
	}

	/**
	 * Set custom size text
	 *
	 * @param string $value Custom size text.
	 */
	public static function set_custom_size_text( $value ) {
		self::set( 'custom_size_text', sanitize_textarea_field( $value ) );
	}
}
