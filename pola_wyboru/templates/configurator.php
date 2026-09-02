<?php
/**
 * Configurator template
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product ) {
	return;
}

$product_id = $product->get_id();
$heel_options = Pola_Wyboru_Configurator::get_heel_options( $product_id );
$current_sole_type = Pola_Wyboru_Session_Manager::get_sole_type();
$current_shoe_width = Pola_Wyboru_Session_Manager::get_shoe_width() ?? 'standardowa';
$current_toe_cushion = Pola_Wyboru_Session_Manager::get_toe_cushion();
$current_custom_colorimetry_enabled = Pola_Wyboru_Session_Manager::get_custom_colorimetry_enabled();
$current_custom_colorimetry_text = Pola_Wyboru_Session_Manager::get_custom_colorimetry_text();
$current_custom_size_enabled = Pola_Wyboru_Session_Manager::get_custom_size_enabled();
$current_custom_size_text = Pola_Wyboru_Session_Manager::get_custom_size_text();
?>
<div class="pola-wyboru-configurator pola-wyboru-product-<?php echo esc_attr( $product_id ); ?>">
	<form id="pola_wyboru_form" class="pola-wyboru-form" method="post">
		<?php wp_nonce_field( 'pola_wyboru_nonce', 'pola_wyboru_nonce' ); ?>

		<div class="pola-wyboru-form-content">
			<!-- Heel Height Selector -->
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

			<!-- Sole Type -->
			<div class="pola-wyboru-field pola-wyboru-sole-type">
				<label><?php esc_html_e( 'Sole Type', 'pola_wyboru' ); ?></label>
				<div class="pola-wyboru-radio-group">
					<label class="pola-wyboru-radio-label">
						<input
							type="radio"
							name="pola_wyboru_config[sole_type]"
							value="welur"
							class="pola-wyboru-radio"
							<?php checked( $current_sole_type, 'welur' ); ?>
						/>
						<?php esc_html_e( 'Velvet (Welur)', 'pola_wyboru' ); ?>
					</label>
					<label class="pola-wyboru-radio-label">
						<input
							type="radio"
							name="pola_wyboru_config[sole_type]"
							value="skóra"
							class="pola-wyboru-radio"
							<?php checked( $current_sole_type, 'skóra' ); ?>
						/>
						<?php esc_html_e( 'Leather (Skóra)', 'pola_wyboru' ); ?>
					</label>
				</div>
			</div>

			<!-- Shoe Width -->
			<div class="pola-wyboru-field pola-wyboru-shoe-width">
				<label for="pola_wyboru_shoe_width">
					<?php esc_html_e( 'Shoe Width', 'pola_wyboru' ); ?>
				</label>
				<select id="pola_wyboru_shoe_width" class="pola-wyboru-select pola-wyboru-shoe-width" name="pola_wyboru_config[shoe_width]">
					<option value="standardowa" <?php selected( $current_shoe_width, 'standardowa' ); ?>>
						<?php esc_html_e( 'Standard', 'pola_wyboru' ); ?>
					</option>
					<option value="wąska" <?php selected( $current_shoe_width, 'wąska' ); ?>>
						<?php esc_html_e( 'Narrow', 'pola_wyboru' ); ?>
					</option>
					<option value="szeroka" <?php selected( $current_shoe_width, 'szeroka' ); ?>>
						<?php esc_html_e( 'Wide', 'pola_wyboru' ); ?>
					</option>
				</select>
			</div>

			<!-- Toe Cushion -->
			<div class="pola-wyboru-field pola-wyboru-toe-cushion">
				<label><?php esc_html_e( 'Toe Cushion', 'pola_wyboru' ); ?></label>
				<div class="pola-wyboru-radio-group">
					<label class="pola-wyboru-radio-label">
						<input
							type="radio"
							name="pola_wyboru_config[toe_cushion]"
							value="pianka"
							class="pola-wyboru-radio"
							<?php checked( $current_toe_cushion, 'pianka' ); ?>
						/>
						<?php esc_html_e( 'Foam', 'pola_wyboru' ); ?>
					</label>
					<label class="pola-wyboru-radio-label">
						<input
							type="radio"
							name="pola_wyboru_config[toe_cushion]"
							value="twardo"
							class="pola-wyboru-radio"
							<?php checked( $current_toe_cushion, 'twardo' ); ?>
						/>
						<?php esc_html_e( 'Hard', 'pola_wyboru' ); ?>
					</label>
				</div>
			</div>

			<!-- Custom Colorimetry -->
			<div class="pola-wyboru-field pola-wyboru-custom-colorimetry">
				<label class="pola-wyboru-checkbox-label">
					<input
						type="checkbox"
						name="pola_wyboru_config[custom_colorimetry_enabled]"
						value="1"
						class="pola-wyboru-checkbox pola-wyboru-custom-colorimetry-toggle"
						<?php checked( $current_custom_colorimetry_enabled, true ); ?>
					/>
					<?php esc_html_e( 'Custom Color', 'pola_wyboru' ); ?>
				</label>
				<div class="pola-wyboru-custom-colorimetry-field" style="display: <?php echo $current_custom_colorimetry_enabled ? 'block' : 'none'; };">
					<textarea
						name="pola_wyboru_config[custom_colorimetry_text]"
						class="pola-wyboru-textarea"
						rows="3"
						placeholder="<?php esc_attr_e( 'Describe your custom color requirements...', 'pola_wyboru' ); ?>"
					><?php echo esc_textarea( $current_custom_colorimetry_text ); ?></textarea>
				</div>
			</div>

			<!-- Custom Size -->
			<div class="pola-wyboru-field pola-wyboru-custom-size">
				<label class="pola-wyboru-checkbox-label">
					<input
						type="checkbox"
						name="pola_wyboru_config[custom_size_enabled]"
						value="1"
						class="pola-wyboru-checkbox pola-wyboru-custom-size-toggle"
						<?php checked( $current_custom_size_enabled, true ); ?>
					/>
					<?php esc_html_e( 'Custom Size', 'pola_wyboru' ); ?>
				</label>
				<div class="pola-wyboru-custom-size-field" style="display: <?php echo $current_custom_size_enabled ? 'block' : 'none'; };">
					<textarea
						name="pola_wyboru_config[custom_size_text]"
						class="pola-wyboru-textarea"
						rows="3"
						placeholder="<?php esc_attr_e( 'Describe your custom size requirements...', 'pola_wyboru' ); ?>"
					><?php echo esc_textarea( $current_custom_size_text ); ?></textarea>
				</div>
				<p class="pola-wyboru-help-text">
					<a href="/tabela-rozmiarow/" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'View foot measurement chart', 'pola_wyboru' ); ?>
					</a>
				</p>
			</div>
		</div>

		<input type="hidden" name="pola_wyboru_product_id" value="<?php echo esc_attr( $product_id ); ?>" />
	</form>
</div>
