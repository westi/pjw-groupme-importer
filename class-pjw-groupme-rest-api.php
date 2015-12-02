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

	protected function make_get_request( $path ) {
		$url = 'https://api.groupme.com/v3/' . $path . '?token=' . $this->access_token;
		$response = wp_remote_get( $url );
		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			return json_decode( wp_remote_retrieve_body( $response ) )->response;
		} else {
			return array();
		}
	}

	/** Public API **/
	public function groups() {
		return $this->make_get_request( 'groups' );
	}
}