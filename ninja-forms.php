<?php
/**
 * Filter setup for Ninja Forms
 *
 * @package SpamPatrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once 'lib/SpamPatrolNinjaForms.php';

$spampatrol_ninjaforms = new SpamPatrolNinjaForms();

add_filter( 'ninja_forms_submit_data', array( $spampatrol_ninjaforms, 'submit_data_filter' ), 10, 1 );
add_filter( 'ninja_forms_run_action_settings', array( $spampatrol_ninjaforms, 'run_action_settings_filter' ), 10, 4 );
add_filter( 'ninja_forms_action_email_send', array( $spampatrol_ninjaforms, 'action_email_send_filter' ), 10, 5 );
