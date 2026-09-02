<?php
/**
 * Shortcode class - handles [gassu_product_configurator] shortcode
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Shortcode
 *
 * Handles product configurator shortcode
 */
class Pola_Wyboru_Shortcode {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'gassu_product_configurator', array( $this, 'render_configurator' ) );
	}

	/**
	 * Render product configurator
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string Rendered HTML.
	 */
	public function render_configurator( $atts, $content = '' ) {
		// Get current product
		global $product;

		if ( ! $product || ! $product->get_id() ) {
			return '';
		}

		$product_id = $product->get_id();

		// Check if product has required attributes
		$model = Pola_Wyboru_Product_Mapper::get_product_attribute( $product, 'pa_model' );
		$color_variant = Pola_Wyboru_Product_Mapper::get_product_attribute( $product, 'pa_wersja_kolorystyczna' );

		if ( ! $model || ! $color_variant ) {
			return '';
		}

		// Get available heel heights
		$heel_options = Pola_Wyboru_Configurator::get_heel_options( $product_id );

		if ( empty( $heel_options ) ) {
			return '';
		}

		// Start output buffering
		ob_start();

		// Include template
		include POLA_WYBORU_PLUGIN_DIR . 'templates/configurator.php';

		return ob_get_clean();
	}
}
