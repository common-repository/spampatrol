<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class SpamPatrolFormidableForms {

	private $result = null;

	private $settings;

	private $spam_threshold;

	public function __construct() {
		$this->settings       = get_option( 'spampatrol_settings' );
		$this->spam_threshold = spampatrol_threshold();
	}

	public function frm_to_email_filter( $recipients, $values, $form_id, $args ) {
		$flagged_fields = array_filter(
			$values,
			function( $field ) {
				return preg_match( '#_spampatrol$#i', $field->field_key );
			}
		);

		if ( ! $flagged_fields ) {
			return $recipients;
		}

		$entry      = $args['entry'];
		$item_key   = $entry->item_key;
		$visitor_ip = $entry->ip;
		$settings   = $this->settings;

		foreach ( $flagged_fields as $field ) {
			if ( ! $field->meta_value ) {
				continue;
			}
			try {
				$response = spampatrol_analyze(
					array(
						'visitorIp' => $visitor_ip,
						'content'   => $field->meta_value,
					)
				);
				$result   = $response->result;
				if ( ! isset( $this->result ) || $result->score >= $this->spam_threshold ) {
					$this->result = $result;
				}
				if ( $result->score >= $this->spam_threshold && $settings['alt_recipient'] ) {
					$recipients = array( $settings['alt_recipient'] );
					return $recipients;
				}
			} catch ( Exception $e ) {
				spampatrol_debug( 'There was a problem', $e->getMessage() );
			}
		}

		return $recipients;
	}

	public function frm_email_subject_filter( $subject, $args ) {
		$result = $this->result;
		if ( ! $result ) {
			return $subject;
		}
		if ( $result->score >= $this->spam_threshold && $this->settings['subject_prefix'] ) {
			return '[Spam] ' . $subject;
		}
		return $subject;
	}

	public function frm_email_header_filter( $headers, $args ) {
		if ( $this->result ) {
			$headers[] = 'X-SpamPatrol-Score: ' . $this->result->score;
		}
		return $headers;
	}
}
