<?php
/**
 * Support for Contact Forms 7.
 *
 * @package SpamPatrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

add_action( 'wpcf7_before_send_mail', 'spampatrol_wpcf7_before_send' );

/**
 * Contact Forms 7 Action before message sending.
 *
 * @param WPCF7_ContactForm $wpcf Contact form instance from Contact Form 7.
 */
function spampatrol_wpcf7_before_send( $wpcf ) {
	$form_tags   = $wpcf->scan_form_tags();
	$has_sp_attr = function( $list ) {
		foreach ( $list as $item ) {
			if ( preg_match( '#^spampatrol:#', $item ) ) {
				return true;
			}
		}
		return false;
	};

	$sp_tags = array_filter(
		$form_tags,
		function( $tag ) use ( $has_sp_attr ) {
			return $tag->options && $has_sp_attr( $tag->options );
		}
	);

	if ( ! $sp_tags ) {
		// do nothing. No tags with attribution for SpamPatrol processing.
		return;
	}

	$submission = WPCF7_Submission::get_instance();

	$original_mail    = $wpcf->prop( 'mail' );
	$original_headers = explode( "\n", $original_mail['additional_headers'] );
	$settings         = get_option( 'spampatrol_settings' );
	$spam_threshold   = spampatrol_threshold();
	foreach ( $sp_tags as $tag ) {
		$value = $submission->get_posted_data( $tag->name );
		if ( in_array( 'spampatrol:content', $tag->options, true ) && $value ) {
			$visitor_ip = $submission->get_meta( 'remote_ip' );
			try {
				$response = spampatrol_analyze(
					array(
						'visitorIp' => $visitor_ip,
						'content'   => $value,
					)
				);
				$result   = $response->result;
				if ( $result->score >= $spam_threshold ) {
					$additional_headers = explode( "\n", $original_mail['additional_headers'] );
					$submission->add_spam_log(
						array(
							'agent'  => 'SpamPatrol.io',
							'reason' => "Score over spam threshold ({$spam_threshold}) for {$tag->name}: {$result->score}",
						)
					);
					$additional_headers = array_merge(
						$additional_headers,
						array(
							'X-SpamPatrol-Score: ' . $result->score,
						)
					);

					$mail                       = $original_mail;
					$mail['additional_headers'] = implode( "\n", $additional_headers );
					if ( $settings['alt_recipient'] ) {
						$mail['recipient'] = $settings['alt_recipient'];
					}
					if ( $settings['subject_prefix'] ) {
						$mail['subject'] = '[Spam] ' . $mail['subject'];
					}
					$properties         = $wpcf->get_properties();
					$properties['mail'] = $mail;
					$wpcf->set_properties( $properties );
					return;
				}
			} catch ( Exception $e ) {
				spampatrol_debug( 'There was a problem', $e->getMessage() );

			}
		}
	}
}
