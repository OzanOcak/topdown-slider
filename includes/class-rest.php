<?php
/**
 * REST API endpoints for sliders.
 *
 * @package TopDownSlider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the tds/v1 REST routes.
 */
class TDS_REST {

	/**
	 * REST namespace.
	 */
	const NAMESPACE = 'tds/v1';

	/**
	 * Constructor. Hooks into WordPress.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/slider/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_slider' ),
					'permission_callback' => array( $this, 'can_edit' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_slider' ),
					'permission_callback' => array( $this, 'can_edit' ),
				),
			)
		);
	}

	/**
	 * Permission check.
	 *
	 * @return bool
	 */
	public function can_edit() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * GET /tds/v1/slider/{id}
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_slider( WP_REST_Request $request ) {
		$id = (int) $request['id'];

		if ( TDS_CPT::POST_TYPE !== get_post_type( $id ) ) {
			return new WP_Error(
				'tds_not_found',
				__( 'Slider not found.', 'topdown-slider' ),
				array( 'status' => 404 )
			);
		}

		return rest_ensure_response(
			array(
				'id'     => $id,
				'title'  => get_the_title( $id ),
				'slides' => self::get_slides( $id ),
			)
		);
	}

	/**
	 * POST /tds/v1/slider/{id}
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_slider( WP_REST_Request $request ) {
		$id     = (int) $request['id'];
		$slides = $request->get_param( 'slides' );
		$title  = $request->get_param( 'title' );

		if ( TDS_CPT::POST_TYPE !== get_post_type( $id ) ) {
			return new WP_Error(
				'tds_not_found',
				__( 'Slider not found.', 'topdown-slider' ),
				array( 'status' => 404 )
			);
		}

		if ( ! is_array( $slides ) ) {
			return new WP_Error(
				'tds_bad_slides',
				__( 'Slides must be an array.', 'topdown-slider' ),
				array( 'status' => 400 )
			);
		}

		$clean = self::sanitize_slides( $slides );
		update_post_meta( $id, '_tds_slides', $clean );

		if ( is_string( $title ) && '' !== trim( $title ) ) {
			wp_update_post(
				array(
					'ID'         => $id,
					'post_title' => sanitize_text_field( $title ),
				)
			);
		}

		return rest_ensure_response(
			array(
				'ok'     => true,
				'slides' => $clean,
			)
		);
	}

	/**
	 * Get slides for a slider.
	 *
	 * @param int $id Post ID.
	 * @return array
	 */
	public static function get_slides( $id ) {
		$slides = get_post_meta( $id, '_tds_slides', true );
		return is_array( $slides ) ? $slides : array();
	}

	/**
	 * Sanitize slides.
	 *
	 * @param array $slides Raw.
	 * @return array
	 */
	private static function sanitize_slides( $slides ) {
		$allowed_text     = array( 'fade-up', 'fade-down', 'slide-left', 'slide-right', 'zoom-in', 'none' );
		$allowed_image    = array( 'zoom', 'pan-left', 'pan-right', 'none' );
		$allowed_position = array( 'top-left', 'top-right', 'center', 'bottom-left', 'bottom-right' );

		$clean = array();

		foreach ( $slides as $slide ) {
			if ( ! is_array( $slide ) ) {
				continue;
			}

			$text_anim    = isset( $slide['textAnimation'] )  ? sanitize_text_field( $slide['textAnimation'] )  : 'fade-up';
			$image_anim   = isset( $slide['imageAnimation'] ) ? sanitize_text_field( $slide['imageAnimation'] ) : 'zoom';
			$text_pos     = isset( $slide['textPosition'] )   ? sanitize_text_field( $slide['textPosition'] )   : 'center';
			$button_color = isset( $slide['buttonColor'] )    ? sanitize_hex_color( $slide['buttonColor'] )     : '';

			$clean[] = array(
				'id'             => isset( $slide['id'] )          ? sanitize_text_field( $slide['id'] )  : uniqid( 's_' ),
				'imageId'        => isset( $slide['imageId'] )     ? (int) $slide['imageId']              : 0,
				'imageUrl'       => isset( $slide['imageUrl'] )    ? esc_url_raw( $slide['imageUrl'] )    : '',
				'label'          => isset( $slide['label'] )       ? sanitize_text_field( $slide['label'] ) : '',
				'title'          => isset( $slide['title'] )       ? sanitize_text_field( $slide['title'] ) : '',
				'description'    => isset( $slide['description'] ) ? wp_kses_post( $slide['description'] ) : '',
				'textAnimation'  => in_array( $text_anim, $allowed_text, true )    ? $text_anim  : 'fade-up',
				'imageAnimation' => in_array( $image_anim, $allowed_image, true )  ? $image_anim : 'zoom',
				'textPosition'   => in_array( $text_pos, $allowed_position, true ) ? $text_pos   : 'center',
				'buttonText'     => isset( $slide['buttonText'] )  ? sanitize_text_field( $slide['buttonText'] ) : '',
				'buttonUrl'      => isset( $slide['buttonUrl'] )   ? esc_url_raw( $slide['buttonUrl'] )          : '',
				'buttonColor'    => $button_color ? $button_color : '',
                'buttonEnabled'  => ! empty( $slide['buttonEnabled'] ),
                'buttonLinkMode' => isset( $slide['buttonLinkMode'] ) && in_array( $slide['buttonLinkMode'], array( 'page', 'custom' ), true )
                   ? $slide['buttonLinkMode']
                   : 'page',
			    );
		}

		return $clean;
	}
}