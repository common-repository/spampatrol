<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class SpamPatrolNinjaForms {

	private $result = null;
	private $settings;
	private $spam_threshold;

	public function __construct() {
		$this->settings       = get_option( 'spampatrol_settings' );
		$this->spam_threshold = spampatrol_threshold();
	}

	public function run_action_settings_filter( $action_settings, $form_id, $action_id, $form_settings ) {
		$settings = $this->settings;
		if ( 'email' === $action_settings['type'] && $this->result ) {
			$result = $this->result;

			if ( $result->score >= $this->spam_threshold ) {
				if ( $settings['alt_recipient'] ) {
					$action_settings['to'] = $settings['alt_recipient'];
				}

				if ( $settings['subject_prefix'] ) {
					$action_settings['email_subject'] = '[Spam] ' . $action_settings['email_subject'];
				}
			}
		}
		return $action_settings;
	}

	public function action_email_send_filter( $sent, $action_settings, $message, $headers, $attachments ) {
		// no way to set headers for NinjaForms
		// if ($this->result) {
			// $headers[] = 'X-SpamPatrol-Score: ' . $this->result->score;
		// }
		return $sent;
	}


	public function submit_data_filter( $form_data ) {
		$settings = $this->settings;

		$process_list = array_filter(
			$form_data['fields'],
			function( $field ) {
				return preg_match( '#_spampatrol$#i', $field['key'] );
			}
		);

		if ( $process_list ) {
			$visitor_ip = wp_strip_all_tags( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
			foreach ( $process_list as $field ) {
				if ( ! $field['value'] ) {
					continue;
				}
				try {
					$response = spampatrol_analyze(
						array(
							'visitorIp' => $visitor_ip,
							'content'   => $field['value'],
						)
					);
					$result   = $response->result;
					if ( ! isset( $this->result ) || $result->score >= $this->spam_threshold ) {
						$this->result = $result;
					}
				} catch ( Exception $e ) {
					spampatrol_debug( 'There was a problem', $e->getMessage() );
				}
			}
		}

		return $form_data;
	}
}
