<?php
/**
 * Custom post type registration.
 *
 * @package OocakFullscreenSlider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the tds_slider custom post type.
 */
class TDS_CPT {

	/**
	 * Post type slug.
	 */
	const POST_TYPE = 'tds_slider';

	/**
	 * Constructor. Hooks into WordPress.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
	}

	/**
	 * Register the custom post type.
	 */
	public function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'                => array(
					'name'               => __( 'Sliders', 'oocak-fullscreen-slider' ),
					'singular_name'      => __( 'Slider', 'oocak-fullscreen-slider' ),
					'add_new'            => __( 'Add New', 'oocak-fullscreen-slider' ),
					'add_new_item'       => __( 'Add New Slider', 'oocak-fullscreen-slider' ),
					'edit_item'          => __( 'Edit Slider', 'oocak-fullscreen-slider' ),
					'new_item'           => __( 'New Slider', 'oocak-fullscreen-slider' ),
					'view_item'          => __( 'View Slider', 'oocak-fullscreen-slider' ),
					'search_items'       => __( 'Search Sliders', 'oocak-fullscreen-slider' ),
					'not_found'          => __( 'No sliders found.', 'oocak-fullscreen-slider' ),
					'not_found_in_trash' => __( 'No sliders found in Trash.', 'oocak-fullscreen-slider' ),
				),
				'public'                => false,
				'show_ui'               => false,
				'show_in_menu'          => false,
				'show_in_rest'          => true,
				'rest_base'             => 'tds_slider',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
				'supports'              => array( 'title' ),
				'capability_type'       => 'post',
				'map_meta_cap'          => true,
			)
		);
	}
}