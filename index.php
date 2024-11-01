<?php
/**
 * Main plugin file.
 *
 * Plugin Name: SpamPatrol
 * Plugin URI: https://spampatrol.io/downloads
 * Version: 1.35.16
 * Description: Intent-based spam detection with lossless handling for your forms. Supports Contact Form 7, Ninja Forms and more.
 * Author: SpamPatrol.io
 * Author URI: https://spampatrol.io
 * License: GPLv2
 *
 * @package SpamPatrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/links.php';
require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/contact-forms-7.php';
require_once __DIR__ . '/formidable-forms.php';
require_once __DIR__ . '/ninja-forms.php';
