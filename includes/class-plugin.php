<?php
/**
 * Main plugin bootstrap.
 *
 * @package TopDownSlider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class. Singleton.
 */
final class TDS_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var TDS_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return TDS_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor. Wires up the plugin.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	/**
	 * Load the class files.
	 */
	private function load_dependencies() {
		require_once TDS_PATH . 'includes/class-cpt.php';
		require_once TDS_PATH . 'includes/class-rest.php';
		require_once TDS_PATH . 'includes/class-admin.php';
		require_once TDS_PATH . 'includes/class-assets.php';
		require_once TDS_PATH . 'includes/class-shortcode.php';
	}

	/**
	 * Instantiate the sub-classes so their hooks register.
	 */
	private function register_hooks() {
		new TDS_CPT();
		new TDS_REST();
		new TDS_Admin();
		new TDS_Assets();
		new TDS_Shortcode();
	}
}