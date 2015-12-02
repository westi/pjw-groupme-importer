<?php
/**
 * Simple GroupMe OAuth class
 *
 */
class pjw_groupme_oauth {
	private $version = 0.1;
	private $debug;
	private $meta_key = 'pjw_groupme_oauth_token';

	public function __construct( $debug = false ) {
		add_action( 'init', array( $this, 'action_init' ) );
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
		add_filter( 'query_vars', array( $this, 'filter_query_vars' ) );
		add_action( 'parse_request', array( $this, 'action_parse_request' ) );
		$this->debug = $debug;
	}

	private function debug_log( $thing ) {
		if ( $this->debug ) {
			error_log( __CLASS__ . ':' . print_r( $thing, true ) );
		}
	}

	public function action_init() {
		add_rewrite_rule( '^groupme-oauth-callback$', 'index.php?_pjw_group_me_oauth=1', 'top' );
	}

	public function action_admin_init() {
		if ( get_option( __CLASS__ . '-version' ) != $this->version ) {
			flush_rewrite_rules();
			update_option( __CLASS__ . '-version', $this->version );
		}
		register_importer('pjw-groupme-importer', 'GroupMe Importer', 'Lets you import groupme group conversations into WordPress', array( $this, 'importer_go' ) );
	}

	public function filter_query_vars( $_query_vars ) {
		$_query_vars[] = '_pjw_group_me_oauth';
		return $_query_vars;
	}


	public function action_parse_request() {
		if ( isset( $GLOBALS['wp']->query_vars[ '_pjw_group_me_oauth' ] ) ) {
			update_user_meta( get_current_user_id(), $this->meta_key, $_REQUEST['access_token'], '' );
			wp_safe_redirect( admin_url( 'admin.php?import=pjw-groupme-importer' ) );
			die();
		}
	}

	public function importer_go() {
		?>
		<div class="wrap">
		<h1>GroupMe Importer</h1>
		
		<ol>
		<?php
			$_access_token = get_user_meta( get_current_user_id(), $this->meta_key, true );
			if ( empty( $_access_token ) ) {
				echo "<li>Connect</li>\n";
				echo admin_url( 'admin.php?import=pjw-groupme-importer' );
			} else {
				echo "<li>Connected &#x2713;</li>\n";
			}
		?>
			<li>Listing Groups
				<ul>
					<?php
						$rest_api = new pjw_groupme_rest_api( $_access_token );
						$groups = $rest_api->groups();
						foreach ( $groups as $group ) {
							echo "<li>{$group->name}</li>\n";	
						}
					?>
				</ul>
			</li>
		</ol>
		</div>
		<?php
	}
}