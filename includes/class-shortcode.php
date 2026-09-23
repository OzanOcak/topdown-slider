<?php
// Placeholder. Will be filled in next.<?php
/**
 * [topdown_slider] shortcode.
 *
 * @package TopDownSlider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the slider on the front end.
 */
class TDS_Shortcode {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'topdown_slider', array( $this, 'render' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render( $atts ) {
		$atts = shortcode_atts(
			array( 'id' => 0 ),
			$atts,
			'topdown_slider'
		);

		$id = (int) $atts['id'];
		if ( ! $id ) {
			return '';
		}

		if ( TDS_CPT::POST_TYPE !== get_post_type( $id ) ) {
			return '';
		}

		$slides = TDS_REST::get_slides( $id );
		if ( empty( $slides ) ) {
			return '';
		}

		wp_enqueue_script( 'tds-frontend' );
		wp_enqueue_style( 'tds-frontend' );

		ob_start();
		?>
		<div id="tds-slider-<?php echo esc_attr( $id ); ?>" class="tds-root"></div>
		<script type="application/json" id="tds-data-<?php echo esc_attr( $id ); ?>">
			<?php echo wp_json_encode( array( 'slides' => $slides ) ); ?>
		</script>
		<?php
		return ob_get_clean();
	}
}