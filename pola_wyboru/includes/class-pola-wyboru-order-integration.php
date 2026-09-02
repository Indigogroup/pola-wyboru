<?php
/**
 * Order Integration class - handles saving configuration data to order items
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Order_Integration
 *
 * Integrates configurator with WooCommerce orders
 */
class Pola_Wyboru_Order_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'display_cart_item_data' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 10, 4 );
	}

	/**
	 * Add configuration data to cart item
	 *
	 * @param array $cart_item_data Cart item data.
	 * @param int   $product_id Product ID.
	 * @return array Modified cart item data.
	 */
	public function add_cart_item_data( $cart_item_data, $product_id ) {
		if ( ! isset( $_POST['pola_wyboru_config'] ) ) {
			return $cart_item_data;
		}

		if ( ! isset( $_POST['pola_wyboru_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pola_wyboru_nonce'] ) ), 'pola_wyboru_nonce' ) ) {
			return $cart_item_data;
		}

		// Validate that configuration is an array
		$config_raw = wp_unslash( $_POST['pola_wyboru_config'] );
		if ( ! is_array( $config_raw ) ) {
			return $cart_item_data;
		}

		// Sanitize configuration with field-specific methods
		$config = array();
		foreach ( $config_raw as $key => $value ) {
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

		$cart_item_data['pola_wyboru_config'] = $config;

		return $cart_item_data;
	}

	/**
	 * Display configuration data in cart
	 *
	 * @param array $item_data Cart item data.
	 * @param array $cart_item Cart item.
	 * @return array Modified item data.
	 */
	public function display_cart_item_data( $item_data, $cart_item ) {
		if ( ! isset( $cart_item['pola_wyboru_config'] ) ) {
			return $item_data;
		}

		$config = $cart_item['pola_wyboru_config'];

		if ( ! empty( $config['heel_height'] ) ) {
			$item_data[] = array(
				'key'   => __( 'Heel Height', 'pola_wyboru' ),
				'value' => $config['heel_height'],
			);
		}

		if ( ! empty( $config['sole_type'] ) ) {
			$item_data[] = array(
				'key'   => __( 'Sole Type', 'pola_wyboru' ),
				'value' => $config['sole_type'],
			);
		}

		if ( ! empty( $config['shoe_width'] ) ) {
			$item_data[] = array(
				'key'   => __( 'Shoe Width', 'pola_wyboru' ),
				'value' => $config['shoe_width'],
			);
		}

		if ( ! empty( $config['toe_cushion'] ) ) {
			$item_data[] = array(
				'key'   => __( 'Toe Cushion', 'pola_wyboru' ),
				'value' => $config['toe_cushion'],
			);
		}

		if ( ! empty( $config['custom_colorimetry_text'] ) ) {
			$item_data[] = array(
				'key'   => __( 'Custom Colorimetry', 'pola_wyboru' ),
				'value' => $config['custom_colorimetry_text'],
			);
		}

		if ( ! empty( $config['custom_size_text'] ) ) {
			$item_data[] = array(
				'key'   => __( 'Custom Size Info', 'pola_wyboru' ),
				'value' => $config['custom_size_text'],
			);
		}

		return $item_data;
	}

	/**
	 * Add configuration data to order item metadata
	 *
	 * @param WC_Order_Item $item Order item.
	 * @param string        $cart_item_key Cart item key.
	 * @param array         $values Cart item values.
	 * @param WC_Order      $order Order object.
	 */
	public function add_order_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( ! isset( $values['pola_wyboru_config'] ) ) {
			return;
		}

		$config = $values['pola_wyboru_config'];

		if ( ! empty( $config['heel_height'] ) ) {
			$item->add_meta_data( '_pola_wyboru_heel_height', $config['heel_height'] );
		}

		if ( ! empty( $config['sole_type'] ) ) {
			$item->add_meta_data( '_pola_wyboru_sole_type', $config['sole_type'] );
		}

		if ( ! empty( $config['shoe_width'] ) ) {
			$item->add_meta_data( '_pola_wyboru_shoe_width', $config['shoe_width'] );
		}

		if ( ! empty( $config['toe_cushion'] ) ) {
			$item->add_meta_data( '_pola_wyboru_toe_cushion', $config['toe_cushion'] );
		}

		if ( ! empty( $config['custom_colorimetry_enabled'] ) ) {
			$item->add_meta_data( '_pola_wyboru_custom_colorimetry_enabled', true );
			if ( ! empty( $config['custom_colorimetry_text'] ) ) {
				$item->add_meta_data( '_pola_wyboru_custom_colorimetry_text', $config['custom_colorimetry_text'] );
			}
		}

		if ( ! empty( $config['custom_size_enabled'] ) ) {
			$item->add_meta_data( '_pola_wyboru_custom_size_enabled', true );
			if ( ! empty( $config['custom_size_text'] ) ) {
				$item->add_meta_data( '_pola_wyboru_custom_size_text', $config['custom_size_text'] );
			}
		}
	}
}
