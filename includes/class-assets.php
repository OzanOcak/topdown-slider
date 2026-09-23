<?php
/**
 * Enqueue admin and frontend assets.
 *
 * @package TopDownSlider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and enqueues scripts and styles.
 */
class TDS_Assets {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend' ) );
	}

	/**
	 * Enqueue the React admin app on our editor page only.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin( $hook ) {
		if ( 'toplevel_page_topdown-slider' !== $hook ) {
			return;
		}

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only page routing.
		$slider_id = isset( $_GET['slider'] ) ? (int) $_GET['slider'] : 0;

		// Only load React on the editor page, not the list page.
		if ( ! $slider_id ) {
			return;
		}

		$asset_file = TDS_PATH . 'build/admin.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}
		$asset = require $asset_file;

		wp_enqueue_script(
			'tds-admin',
			TDS_URL . 'build/admin.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style(
			'tds-admin',
			TDS_URL . 'build/admin.css',
			array(),
			$asset['version']
		);

		wp_localize_script(
			'tds-admin',
			'TDS',
			array(
				'restUrl'  => rest_url( 'tds/v1' ),
				'wpRest'   => rest_url( 'wp/v2' ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'sliderId' => $slider_id,
			)
		);

		wp_enqueue_media();
	}

	/**
	 * Register frontend assets. Enqueued on demand by the shortcode.
	 */
	public function register_frontend() {
		$script_asset = TDS_PATH . 'build/frontend.asset.php';
		if ( ! file_exists( $script_asset ) ) {
			return;
		}
		$asset = require $script_asset;

		wp_register_script(
			'tds-frontend',
			TDS_URL . 'build/frontend.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_register_style(
			'tds-frontend',
			TDS_URL . 'build/frontend.css',
			array(),
			$asset['version']
		);
	}
}