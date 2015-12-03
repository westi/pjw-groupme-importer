<?php
class pjw_groupme_wp_admin {
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'manage_pjw-groupme-message_posts_columns', array( $this, 'register_custom_post_type_columns' ) );
		add_action( 'manage_pjw-groupme-message_posts_custom_column', array( $this, 'display_custom_post_type_columns' ), 10, 2 );
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

	/**
	 * Modify the list of columns that are displayed for posts in wp-admin.
	 *
	 * @param array $_columns
	 * @return array
	 */
	public function register_custom_post_type_columns( $_columns ) {
		unset( $_columns['title'] ); // We don't want the title displayed
		unset( $_columns['date'] ); // Remove the default date position so we can rename and add at the end.
		$_columns['pjw_groupme_name'] = 'User';
		$_columns['pjw_groupme_message'] = 'Message';
		$_columns['date'] = 'Date';
		return $_columns;
	}

	/**
	 * Output the content for the custom columns in wp-admin
	 */
	public function display_custom_post_type_columns( $_column_name, $_post_id ) {
		switch( $_column_name ) {
			case 'pjw_groupme_name':
				echo get_post_meta( $_post_id, $_column_name, true );
				break;
			case 'pjw_groupme_message':
				the_content();
				break;
		}
	}
}
// Throw us into the wind
new pjw_groupme_wp_admin();