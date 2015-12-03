<?php
/*
 Plugin Name: PJW GroupMe Importer
 Plugin URI: https://github.com/westi/pjw-groupme-importer
 Description: Lets you import from GroupMe
 Author: westi
 Version: 0.1-alpha
 Author URI: http://blog.ftwr.co.uk/
 */
 
require_once( __DIR__ . '/class-pjw-groupme-importer.php' );
require_once( __DIR__ . '/class-pjw-groupme-rest-api.php' );
require_once( __DIR__ . '/class-pjw-groupme-wp-api.php' );

function pjw_groupme_importer_go() {
	global $pjw_groupme_importer;
	$pjw_groupme_importer = new pjw_groupme_importer();
}
add_action('plugins_loaded', 'pjw_groupme_importer_go' );