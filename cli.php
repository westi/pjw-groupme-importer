<?php
require_once( dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php' );
require_once( __DIR__ . '/class-pjw-groupme-rest-api.php' );

function __parse_cli_args() {
	$short_to_long = array(
		'u:' => 'user-id:',
		'g:' => 'group-id:',
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
		switch ( $args['action'] ) {
			case 'list-groups':
				$groups = $rest_api->groups();
				foreach ( $groups as $group ) {
					echo "{$group->name} - {$group->id} - {$group->messages->count} messages\n";
				}
				break;
			case 'fetch-messages':
				if ( isset( $args['group-id' ] ) ) {
					$messages = $rest_api->messages( $args['group-id'] );

					foreach ( $messages->messages as $message ) {
						echo date( DATE_ISO8601, $message->created_at ) . " {$message->name}: {$message->text}\n";
					}
				} else {
					echo "Usage: --user-id=X --action=fetch-messages --group-id=X ...\n\n";
				}
		}
	} else {
		echo "Usage: --user-id=X --action=Y ...\n\n";
	}
}

__dispatch_request( __parse_cli_args() );