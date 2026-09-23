<?php
/**
 * Runs when the plugin is deleted from the plugins screen.
 *
 * @package TopDownSlider
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all slider posts and their meta.
$sliders = get_posts(
	array(
		'post_type'      => 'tds_slider',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	)
);

foreach ( $sliders as $slider_id ) {
	wp_delete_post( $slider_id, true );
}