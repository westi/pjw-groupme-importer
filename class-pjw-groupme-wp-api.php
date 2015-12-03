<?php
/**
 * Simple WordPress  API class
 *
 * Wrapper around inserting things into WordPress
 */

// Needed for sideloading images
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

class pjw_groupme_wp_api {
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
		$date = date( 'Y-m-d H:i:s', $message->created_at );

		$_post = array(
			'post_type' => 'pjw-groupme-message',
			'post_status' => 'publish',
			'post_content' => $message->text,
			'post_date' => $date,
			'post_date_gmt' => $date,
		);

		$_post_meta = array(
			'pjw_groupme_group_id' => $message->group_id,
			'pjw_groupme_message_id' => $message->id,
			'pjw_groupme_source_guid' => $message->source_guid,
			'pjw_groupme_user_id' => $message->user_id,
			'pjw_groupme_name' => $message->name,
			'pjw_groupme_avatar_url' => $message->avatar_url,
		);

		$_post_id = wp_insert_post( $_post );

		foreach( $_post_meta as $_key => $_value ) {
			add_post_meta( $_post_id, $_key, $_value, true );
		}

		foreach( $message->attachments as $attachment ) {
			$this->sideload_attachment( $attachment, $_post_id, $date );
		}
	}

	private function sideload_attachment( $attachment, $_to_post_id, $date ) {
		if ( 'image' === $attachment->type ) {
			$response = wp_remote_head( $attachment->url );
			if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$_mimes = array_flip( wp_get_mime_types( ) );
				$_content_type = wp_remote_retrieve_header( $response, 'content-type' );
				if ( isset( $_mimes[ $_content_type ] ) ) {
					$_ext = strtok( $_mimes[ $_content_type ], '|' );
					$_temp_file = download_url( $attachment->url );
					// TODO check for WP_Error
					$_new_file = str_replace( '.tmp', '.' . $_ext, $_temp_file );
					rename( $_temp_file, $_new_file );

					$file_array = array();
					$file_array['name'] = basename( $_new_file );
					$file_array['tmp_name'] = $_new_file;

					$attachment_id = media_handle_sideload( $file_array, $_to_post_id, '', array( 'post_date' => $date, 'post_date_gmt' => $date ) );
				}
			}
		}
	}
}