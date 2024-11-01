<?php
/**
 * Admin area functionality.
 *
 * @package SpamPatrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

add_action( 'admin_menu', 'spampatrol_admin_menu' );

/**
 * Adds submenu for Admin > Settings
 */
function spampatrol_admin_menu() {
	add_submenu_page(
		'options-general.php',
		'SpamPatrol.io Settings',
		'SpamPatrol',
		'manage_options',
		'spampatrol-plugin',
		'spampatrol_admin_init'
	);
}

/**
 * WP admin page for managing SpamPatrol settings.
 */
function spampatrol_admin_init() {
	$current_user = wp_get_current_user();
	if ( ! isset( $current_user ) || ! in_array( 'administrator', $current_user->roles, true ) ) {
		return;
	}

	add_option(
		'spampatrol_settings',
		array(
			'apikey'    => '',
			'threshold' => 50,
		)
	);
	spampatrol_handle_settings();

	$settings = get_option( 'spampatrol_settings', array() );
	?>
	<h1>SpamPatrol Settings</h1>
	<form method="post">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th>API Key</th>
				<td>
				<input name="api_key" type="text" value="<?php echo esc_attr( $settings['apikey'] ); ?>" />
				</td>
				</tr>

				<tr>
					<th>Score Threshold</th>
					<td>
						<input name="threshold" type="number"  value="<?php echo esc_attr( $settings['threshold'] ); ?>" />
						<p>Messages returning a score that is at least this value will be classified as spam.</p>
					</td>
				</tr>

				<tr>
					<th>Spam Handling</th>
					<td>
						<p>What to do with messages that are spam?  </p>
						<p style="margin-top: 1rem">
							Redirect to a specific email address
							<input name="alt_recipient" type="email" value="<?php echo esc_attr( $settings['alt_recipient'] ) ?? ''; ?>" />
						</p>
						<p style="margin-top: 1rem">
							<label>
								<input
									type="checkbox"
									name="subject_prefix"
									<?php echo true === $settings['subject_prefix'] ? 'checked="checked"' : ''; ?>
								/>
							Add [Spam] Prefix to email subject
						</p>
						<p style="margin-top: 2rem">Note that the <strong>X-SpamPatrol-Score</strong> mime header will be present in messages
						(except for Ninja Forms)
						and can be used for processing in email clients or systems.</p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<?php wp_nonce_field( -1, 'spampatrol-settings-nonce' ); ?>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Settings" />
		</p>
	</form>

	<?php

}

/**
 * Form submission handler for SpamPatrol settings.
 */
function spampatrol_handle_settings() {
	if ( count( $_POST ) && isset( $_POST['spampatrol-settings-nonce'] ) ) {
		if ( ! ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['spampatrol-settings-nonce'] ) ) ) ) ) {
			die( 'Invalid nonce.' );
		}
		$settings = get_option( 'spampatrol_settings', array() );
		$settings = array_merge(
			$settings,
			array(
				'apikey'         => sanitize_text_field( $_POST['api_key'] ),
				'threshold'      => (float) $_POST['threshold'],
				'alt_recipient'  => sanitize_email( $_POST['alt_recipient'] ),
				'subject_prefix' => 'on' === $_POST['subject_prefix'],
			)
		);

		update_option( 'spampatrol_settings', $settings );
	}
}
