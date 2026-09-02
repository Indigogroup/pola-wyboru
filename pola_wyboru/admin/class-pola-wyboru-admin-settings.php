<?php
/**
 * Admin Settings class - handles admin settings and configuration
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Admin_Settings
 *
 * Handles admin settings pages and configuration UI
 */
class Pola_Wyboru_Admin_Settings {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting(
			'pola_wyboru_settings_group',
			'pola_wyboru_heel_display_names',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_heel_display_names' ),
			)
		);
	}

	/**
	 * Render main settings page
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'pola_wyboru' ) );
		}

		?>
		<div class="wrap pola-wyboru-settings-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="pola-wyboru-settings-container">
				<h2><?php esc_html_e( 'Heel Height Display Names', 'pola_wyboru' ); ?></h2>
				<p><?php esc_html_e( 'Configure custom display names for heel heights shown to customers.', 'pola_wyboru' ); ?></p>

				<?php $this->render_heel_display_names(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render heel display names configuration
	 */
	public function render_heel_display_names() {
		// Get available heel height terms
		$heel_terms = get_terms(
			array(
				'taxonomy'   => 'pa_wysokosc_obcasa',
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $heel_terms ) || empty( $heel_terms ) ) {
			echo '<p>' . esc_html__( 'No heel height attributes found. Please create heel height product attributes first.', 'pola_wyboru' ) . '</p>';
			return;
		}

		$heel_display_names = Pola_Wyboru_Configurator::get_all_heel_display_names();

		?>
		<form method="post" action="options.php" class="pola-wyboru-heel-form">
			<?php settings_fields( 'pola_wyboru_settings_group' ); ?>

			<table class="widefat striped pola-wyboru-heel-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Heel Height (Technical Value)', 'pola_wyboru' ); ?></th>
						<th><?php esc_html_e( 'Display Name (Customer Facing)', 'pola_wyboru' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $heel_terms as $term ) {
						// Key by term name (display value) to match how display names are stored
						$display_name = isset( $heel_display_names[ $term->name ] ) ? $heel_display_names[ $term->name ] : '';
						?>
						<tr>
							<td><?php echo esc_html( $term->name ); ?></td>
							<td>
								<input
									type="text"
									name="pola_wyboru_heel_display_names[<?php echo esc_attr( $term->name ); ?>]"
									value="<?php echo esc_attr( $display_name ); ?>"
									class="regular-text"
									placeholder="<?php echo esc_attr( $term->name ); ?>"
								/>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>

			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Sanitize heel display names
	 *
	 * @param array $input Input array from form.
	 * @return array Sanitized array.
	 */
	public function sanitize_heel_display_names( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}

		$sanitized = array();

		foreach ( $input as $key => $value ) {
			$sanitized[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
		}

		return $sanitized;
	}
}
