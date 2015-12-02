<?php
require_once( dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php' );
require_once( __DIR__ . '/class-pjw-groupme-rest-api.php' );

function __parse_cli_args() {
	$short_to_long = array(
		'u:' => 'user-id:',
		'dry-run:',
		'verbose:',
		'action:'
	);
	$options = getopt( implode( '', array_keys( $short_to_long ) ), $short_to_long );

	// Defaults
	$parsed = array( 'dry-run' => true, 'verbose' => false );
	$datatype = array( 'dry-run' => 'boolean', 'verbose' => 'boolean' );

	foreach ( $short_to_long as $short => $long ) {
		$short = rtrim( $short, ':' );
		$long = rtrim( $long, ':' );

		if ( isset( $options[$long] ) ) {
			$parsed[$long] = $options[$long];
		} elseif( isset( $options[$short] ) ) {
			$parsed[$long] = $options[$short];
		}
	}

	foreach ( $datatype as $key => $type ) {
		if ( isset( $parsed[$key] ) && gettype( $parsed[$key] ) != $type ) {
			switch ( $type ) {
				case 'boolean':
					$parsed[$key] = 'true' == $parsed[$key] ? true : false;
					break;
				case 'int':
					$parsed[$key] = (int) $parsed[$key];
					break;
			}
		}
	}

	return $parsed;
}

function __dispatch_request( $args ) {
	if ( isset( $args['user-id'] ) && isset( $args['action'] )) {
		wp_set_current_user( $args['user-id'] );
		$_access_token = get_user_meta( get_current_user_id(), 'pjw_groupme_oauth_token', true );
		$rest_api = new pjw_groupme_rest_api( $_access_token );
	} else {
		echo "Usage: --user-id=X --action=Y ...\n\n";
	}
}

__dispatch_request( __parse_cli_args() );