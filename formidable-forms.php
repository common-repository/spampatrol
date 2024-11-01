<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once 'lib/SpamPatrolFormidableForms.php';

$spampatrol_formidable = new SpamPatrolFormidableForms();

add_filter( 'frm_to_email', array( $spampatrol_formidable, 'frm_to_email_filter' ), 15, 4 );
add_filter( 'frm_email_subject', array( $spampatrol_formidable, 'frm_email_subject_filter' ), 15, 2 );
add_filter( 'frm_email_header', array( $spampatrol_formidable, 'frm_email_header_filter' ), 10, 2 );
