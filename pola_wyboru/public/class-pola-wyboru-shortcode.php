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

	/**
	 * Render heel height selector
	 *
	 * @param array $heel_options Available heel height options.
	 */
	private function render_heel_selector( $heel_options ) {
		?>
		<div class="pola-wyboru-field pola-wyboru-heel-selector">
			<label for="pola_wyboru_heel_height">
				<?php esc_html_e( 'Heel Height', 'pola_wyboru' ); ?>
				<span class="required">*</span>
			</label>
			<select id="pola_wyboru_heel_height" class="pola-wyboru-select pola-wyboru-heel-height" name="pola_wyboru_heel_height" required>
				<option value=""><?php esc_html_e( 'Select heel height', 'pola_wyboru' ); ?></option>
				<?php
				foreach ( $heel_options as $value => $label ) {
					?>
					<option value="<?php echo esc_attr( $value ); ?>">
						<?php echo esc_html( $label ); ?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
		<?php
	}

	/**
	 * Render sole type selector
	 */
	private function render_sole_type_selector() {
		?>
		<div class="pola-wyboru-field pola-wyboru-sole-type">
			<label><?php esc_html_e( 'Sole Type', 'pola_wyboru' ); ?></label>
			<div class="pola-wyboru-radio-group">
				<label class="pola-wyboru-radio-label">
					<input
						type="radio"
						name="pola_wyboru_sole_type"
						value="welur"
						class="pola-wyboru-radio"
					/>
					<?php esc_html_e( 'Velvet (Welur)', 'pola_wyboru' ); ?>
				</label>
				<label class="pola-wyboru-radio-label">
					<input
						type="radio"
						name="pola_wyboru_sole_type"
						value="skóra"
						class="pola-wyboru-radio"
					/>
					<?php esc_html_e( 'Leather (Skóra)', 'pola_wyboru' ); ?>
				</label>
			</div>
		</div>
		<?php
	}

	/**
	 * Render shoe width selector
	 */
	private function render_shoe_width_selector() {
		?>
		<div class="pola-wyboru-field pola-wyboru-shoe-width">
			<label for="pola_wyboru_shoe_width">
				<?php esc_html_e( 'Shoe Width', 'pola_wyboru' ); ?>
			</label>
			<select id="pola_wyboru_shoe_width" class="pola-wyboru-select pola-wyboru-shoe-width" name="pola_wyboru_shoe_width">
				<option value="standardowa" selected><?php esc_html_e( 'Standard', 'pola_wyboru' ); ?></option>
				<option value="wąska"><?php esc_html_e( 'Narrow', 'pola_wyboru' ); ?></option>
				<option value="szeroka"><?php esc_html_e( 'Wide', 'pola_wyboru' ); ?></option>
			</select>
		</div>
		<?php
	}

	/**
	 * Render toe cushion selector
	 */
	private function render_toe_cushion_selector() {
		?>
		<div class="pola-wyboru-field pola-wyboru-toe-cushion">
			<label><?php esc_html_e( 'Toe Cushion', 'pola_wyboru' ); ?></label>
			<div class="pola-wyboru-radio-group">
				<label class="pola-wyboru-radio-label">
					<input
						type="radio"
						name="pola_wyboru_toe_cushion"
						value="pianka"
						class="pola-wyboru-radio"
					/>
					<?php esc_html_e( 'Foam', 'pola_wyboru' ); ?>
				</label>
				<label class="pola-wyboru-radio-label">
					<input
						type="radio"
						name="pola_wyboru_toe_cushion"
						value="twardo"
						class="pola-wyboru-radio"
					/>
					<?php esc_html_e( 'Hard', 'pola_wyboru' ); ?>
				</label>
			</div>
		</div>
		<?php
	}

	/**
	 * Render custom colorimetry section
	 */
	private function render_custom_colorimetry() {
		?>
		<div class="pola-wyboru-field pola-wyboru-custom-colorimetry">
			<label class="pola-wyboru-checkbox-label">
				<input
					type="checkbox"
					name="pola_wyboru_custom_colorimetry_enabled"
					value="1"
					class="pola-wyboru-checkbox pola-wyboru-custom-colorimetry-toggle"
				/>
				<?php esc_html_e( 'Custom Color', 'pola_wyboru' ); ?>
			</label>
			<div class="pola-wyboru-custom-colorimetry-field" style="display: none;">
				<textarea
					name="pola_wyboru_custom_colorimetry_text"
					class="pola-wyboru-textarea"
					rows="3"
					placeholder="<?php esc_attr_e( 'Describe your custom color requirements...', 'pola_wyboru' ); ?>"
				></textarea>
			</div>
		</div>
		<?php
	}

	/**
	 * Render custom size section
	 */
	private function render_custom_size() {
		?>
		<div class="pola-wyboru-field pola-wyboru-custom-size">
			<label class="pola-wyboru-checkbox-label">
				<input
					type="checkbox"
					name="pola_wyboru_custom_size_enabled"
					value="1"
					class="pola-wyboru-checkbox pola-wyboru-custom-size-toggle"
				/>
				<?php esc_html_e( 'Custom Size', 'pola_wyboru' ); ?>
			</label>
			<div class="pola-wyboru-custom-size-field" style="display: none;">
				<textarea
					name="pola_wyboru_custom_size_text"
					class="pola-wyboru-textarea"
					rows="3"
					placeholder="<?php esc_attr_e( 'Describe your custom size requirements...', 'pola_wyboru' ); ?>"
				></textarea>
			</div>
			<p class="pola-wyboru-help-text">
				<a href="/tabela-rozmiarow/" target="_blank"><?php esc_html_e( 'View foot measurement chart', 'pola_wyboru' ); ?></a>
			</p>
		</div>
		<?php
	}
}
