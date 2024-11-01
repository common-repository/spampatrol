<?php
/**
 * Links handling features
 *
 * @package SpamPatrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

add_filter( 'plugin_action_links_spampatrol/index.php', 'spampatrol_settings_link' );

function spampatrol_settings_link( $links ) {
	$url   = admin_url( 'options-general.php?page=spampatrol-plugin' );
	$link  = '<a href="' . $url . '">' . __( 'Settings' ) . '</a>';
	$links = array_merge( array( $link ), $links );
	return $links;
}
