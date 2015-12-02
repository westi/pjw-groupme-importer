<?php
/*
 Plugin Name: PJW GroupMe Importer
 Plugin URI: https://github.com/westi/pjw-groupme-importer
 Description: Lets you import from GroupMe
 Author: westi
 Version: 0.1-alpha
 Author URI: http://blog.ftwr.co.uk/
 */
 
require_once( __DIR__ . '/includes/class-pjw-gm-oauth.php' );
require_once( __DIR__ . '/includes/class-pjw-groupme-rest-api.php' );

function pjw_groupme_importer_go() {
	global $pjw;
	$pjw['pjw_groupme_oauth'] = new pjw_groupme_oauth();
}
add_action('plugins_loaded', 'pjw_groupme_importer_go' );