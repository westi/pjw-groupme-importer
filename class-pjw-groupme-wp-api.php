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
		$_message_ids = get_posts(
			array(
				'fields' => 'ids',
				'numberposts' => 1,
				'post_type' => 'pjw-groupme-message',
				'meta_key' => 'pjw_groupme_group_id',
				'meta_value' => $group_id,
				'orderby' => 'id',
				'order' => 'DESC'
			)
		);
		if ( ! empty( $_message_ids ) ) {
			return get_post_meta( $_message_ids[0], 'pjw_groupme_message_id', true );
		} else {
			return false;
		}
	}

	public function sync_message( $message ) {
		$_post = array(
			'post_type' => 'pjw-groupme-message',
			'post_status' => 'publish',
			'post_content' => $message->text,
			'post_date' => date( 'Y-m-d H:i:s', $message->created_at ),
			'post_date_gmt' => date( 'Y-m-d H:i:s', $message->created_at ),
		);

		$_post_meta = array(
			'pjw_groupme_group_id' => $message->group_id,
			'pjw_groupme_message_id' => $message->id,
			'pjw_groupme_source_guid' => $message->source_guid,
			'pjw_groupme_user_id' => $message->user_id,
			'pjw_groupme_name' => $message->name,
			'pjw_groupme_avatar_url' => $message->avatar_url,
		);

		$_post_obj = wp_insert_post( $_post );

		foreach( $_post_meta as $_key => $_value ) {
			add_post_meta( $_post_obj, $_key, $_value, true );
		}
	}
}
$pjw_groupme_wp_api = new pjw_groupme_wp_api();