<?php
class pjw_groupme_wp_admin {
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
}
// Throw us into the wind
new pjw_groupme_wp_admin();