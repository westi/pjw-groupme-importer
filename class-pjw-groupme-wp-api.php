<?php
/**
 * Simple WordPress  API class
 *
 * Wrapper around insterting things into WordPress
 */

// Needed for sideloading images
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

class pjw_groupme_wp_api {
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
	}

	/**
	 * Register a new post type that we can use to store synced messages
	 *
	 */
	public function register_post_type( ) {
		register_post_type(
			'pjw-groupme-message',
			array(
				'label' => 'Messages',
				'public' => true,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'supports' => array( 'title', 'custom-fields' ),
				'capabilities' => array( 'create_posts' => false ),
				'map_meta_cap' => true,
			)
		);
	}

	public function get_last_synced_message_id( $group_id ) {
		
	}

	public function sync_message( $message ) {
		
	}
}
$pjw_groupme_wp_api = new pjw_groupme_wp_api();