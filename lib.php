<?php
/**
 * Library functions
 *
 * @package SpamPatrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Debugging helper function.
 *
 * @param array ...$args List of values to log.
 */
function spampatrol_debug( ...$args ) {
	// phpcs:disable WordPress.PHP.DevelopmentFunctions
	error_log( gmdate( 'Y-m-d H:i:s' ) . ' - ' . print_r( $args, true ) );
	// phpcs:enable
}

/**
 * Helper function to return the spam threshold configured
 *
 * @return float Spam threshold
 */
function spampatrol_threshold() {
	$settings = get_option( 'spampatrol_settings' );
	return $settings['threshold'] ?? 50;
}

/**
 * Makes API request to SpamPatrol API
 *
 * @param array $params array of request body params.
 * @throws Exception When there is a WP_Error for request or json_decode issue.
 */
function spampatrol_analyze( $params ) {
	$settings = get_option( 'spampatrol_settings' );

	$headers = array();
	if ( $settings['apikey'] ) {
		$headers['Authorization'] = $settings['apikey'];
	}

	$res = wp_remote_post(
		'https://spampatrol.io/api/v1/analyze',
		array(
			'headers'    => $headers,
			'body'       => $params,
			'user-agent' => 'SpamPatrol.io Wordpress Plugin / ' . get_bloginfo( 'version' ),
		)
	);

	if ( is_wp_error( $res ) ) {
		throw new Exception( implode( '. ', $res->errors ) );
	}

	$body = $res['body'];
	$json = json_decode( $body );
	if ( json_last_error() ) {
		throw new Exception( 'Unable to parse json response' );
	}

	return $json;
}
