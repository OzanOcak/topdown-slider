<?php
/**
 * Plugin Name:       TopDown Slider
 * Plugin URI:        https://oocak.com/apps/topdown-slider
 * Description:       A full-screen vertical slider plugin for building single-page scroll experiences.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Your Name
 * Author URI:        https://oocak.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       topdown-slider
 * Domain Path:       /languages
 *
 * @package TopDownSlider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TDS_VERSION', '0.1.0' );
define( 'TDS_FILE',    __FILE__ );
define( 'TDS_PATH',    plugin_dir_path( __FILE__ ) );
define( 'TDS_URL',     plugin_dir_url( __FILE__ ) );
define( 'TDS_SLUG',    'topdown-slider' );

// Load the main plugin class.
require_once TDS_PATH . 'includes/class-plugin.php';

// Boot it.
add_action( 'plugins_loaded', function () {
	TDS_Plugin::instance();
} );