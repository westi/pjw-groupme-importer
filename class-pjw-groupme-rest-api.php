<?php
/**
 * Simple GroupMe REST API class
 *
 */
class pjw_groupme_rest_api {
	private $access_token;
	
	public function __construct( $access_token ) {
		$this->access_token = $access_token;
	}

	protected function make_get_request( $path, $args = array() ) {
		$args['token'] = $this->access_token;
		$url = add_query_arg( $args, 'https://api.groupme.com/v3/' . $path );
		$response = wp_remote_get( $url );
		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			return json_decode( wp_remote_retrieve_body( $response ) )->response;
		} else {
			return array();
		}
	}

	protected function messages_sorter( $a, $b ) {
		if ( $a->created_at == $b->created_at ) {
			return 0;
		} elseif ( $a->created_at < $b->created_at ) {
			return -1;
		} else {
			return 1;
		}
	}

	/** Public API **/
	public function groups() {
		return $this->make_get_request( 'groups' );
	}

	public function group( $group_id ) {
		return $this->make_get_request( 'groups/' . $group_id );
	}

	public function messages( $group_id ) {
		$messages = $this->make_get_request( 'groups/' . $group_id . '/messages' );
		// Ensure we are always chronologically sorted
		usort( $messages->messages, array( $this, 'messages_sorter' ) );
		return $messages;
	}
}