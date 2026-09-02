<?php
/**
 * Product Mapper class - handles mapping products by model, color variant, and heel height
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Product_Mapper
 *
 * Maps products by their model, color variant, and heel height
 */
class Pola_Wyboru_Product_Mapper {

	/**
	 * Get all products in the same group (model + color variant)
	 *
	 * @param int $product_id The product ID to find group for.
	 * @return array Array of product data in the group.
	 */
	public static function get_product_group( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return array();
		}

		$model = self::get_product_attribute( $product, 'pa_model' );
		$color_variant = self::get_product_attribute( $product, 'pa_wersja_kolorystyczna' );

		if ( ! $model || ! $color_variant ) {
			return array();
		}

		return self::find_products_by_group( $model, $color_variant );
	}

	/**
	 * Get product attribute value (display value)
	 *
	 * @param WC_Product $product The product object.
	 * @param string     $attribute_name The attribute name (e.g., 'pa_model').
	 * @return string|false The attribute display value or false if not found.
	 */
	public static function get_product_attribute( $product, $attribute_name ) {
		$attribute_value = $product->get_attribute( $attribute_name );

		if ( empty( $attribute_value ) ) {
			return false;
		}

		return $attribute_value;
	}

	/**
	 * Get term slug for attribute value
	 *
	 * @param string $attribute_name The attribute name (e.g., 'pa_model').
	 * @param string $attribute_value The attribute display value (e.g., 'Paula' or '8 cm').
	 * @return string|false The term slug or false if not found.
	 */
	private static function get_term_slug( $attribute_name, $attribute_value ) {
		$taxonomy = $attribute_name;
		$term = get_term_by( 'name', $attribute_value, $taxonomy );

		if ( $term && ! is_wp_error( $term ) ) {
			return $term->slug;
		}

		return false;
	}

	/**
	 * Find all products by model and color variant
	 *
	 * @param string $model The model attribute value (display value).
	 * @param string $color_variant The color variant attribute value (display value).
	 * @return array Array of products data with their heel heights.
	 */
	private static function find_products_by_group( $model, $color_variant ) {
		// Convert display values to slugs
		$model_slug = self::get_term_slug( 'pa_model', $model );
		$color_slug = self::get_term_slug( 'pa_wersja_kolorystyczna', $color_variant );

		if ( ! $model_slug || ! $color_slug ) {
			return array();
		}

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'pa_model',
					'field'    => 'slug',
					'terms'    => $model_slug,
				),
				array(
					'taxonomy' => 'pa_wersja_kolorystyczna',
					'field'    => 'slug',
					'terms'    => $color_slug,
				),
			),
		);

		$query = new WP_Query( $args );
		$products = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$product = wc_get_product( get_the_ID() );

				if ( $product && 'publish' === $product->get_status() ) {
					$heel_height = self::get_product_attribute( $product, 'pa_wysokosc_obcasa' );

					$products[] = array(
						'id'            => $product->get_id(),
						'title'         => $product->get_title(),
						'url'           => $product->get_permalink(),
						'heel_height'   => $heel_height,
						'model'         => $model,
						'color_variant' => $color_variant,
					);
				}
			}
			wp_reset_postdata();
		}

		return $products;
	}

	/**
	 * Get product by model, color variant, and heel height
	 *
	 * @param string $model The model attribute display value.
	 * @param string $color_variant The color variant attribute display value.
	 * @param string $heel_height The heel height attribute display value.
	 * @return array|false Product data or false if not found.
	 */
	public static function get_product_by_criteria( $model, $color_variant, $heel_height ) {
		// Convert display values to slugs
		$model_slug = self::get_term_slug( 'pa_model', $model );
		$color_slug = self::get_term_slug( 'pa_wersja_kolorystyczna', $color_variant );
		$heel_slug = self::get_term_slug( 'pa_wysokosc_obcasa', $heel_height );

		if ( ! $model_slug || ! $color_slug || ! $heel_slug ) {
			return false;
		}

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'pa_model',
					'field'    => 'slug',
					'terms'    => $model_slug,
				),
				array(
					'taxonomy' => 'pa_wersja_kolorystyczna',
					'field'    => 'slug',
					'terms'    => $color_slug,
				),
				array(
					'taxonomy' => 'pa_wysokosc_obcasa',
					'field'    => 'slug',
					'terms'    => $heel_slug,
				),
			),
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			$query->the_post();
			$product = wc_get_product( get_the_ID() );

			if ( $product && 'publish' === $product->get_status() ) {
				wp_reset_postdata();

				return array(
					'id'  => $product->get_id(),
					'url' => $product->get_permalink(),
				);
			}
		}

		wp_reset_postdata();

		return false;
	}

	/**
	 * Get available heel heights for a product group
	 *
	 * @param int $product_id The product ID.
	 * @return array Array of heel heights (display values).
	 */
	public static function get_available_heel_heights( $product_id ) {
		$group = self::get_product_group( $product_id );
		$heights = array();

		foreach ( $group as $product ) {
			if ( ! empty( $product['heel_height'] ) && ! in_array( $product['heel_height'], $heights, true ) ) {
				$heights[] = $product['heel_height'];
			}
		}

		// Sort heights numerically
		usort( $heights, function( $a, $b ) {
			$a_num = floatval( str_replace( ',', '.', $a ) );
			$b_num = floatval( str_replace( ',', '.', $b ) );
			return $a_num <=> $b_num;
		});

		return $heights;
	}
}
