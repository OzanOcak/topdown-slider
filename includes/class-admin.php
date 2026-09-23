<?php
/**
 * Admin menu, list page, editor page.
 *
 * @package TopDownSlider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the admin pages.
 */
class TDS_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_post_tds_create_slider', array( $this, 'handle_create' ) );
		add_action( 'admin_post_tds_delete_slider', array( $this, 'handle_delete' ) );
		add_action( 'admin_post_tds_duplicate_slider', array( $this, 'handle_duplicate' ) );
	}

	/**
	 * Register the top-level menu.
	 */
	public function register_menu() {
		add_menu_page(
			__( 'TopDown Sliders', 'topdown-slider' ),
			__( 'TopDown Sliders', 'topdown-slider' ),
			'edit_posts',
			'topdown-slider',
			array( $this, 'render_page' ),
			'dashicons-images-alt2',
			4
		);
	}

	/**
	 * Route between list page and editor page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'topdown-slider' ) );
		}

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only page routing.
		$slider_id = isset( $_GET['slider'] ) ? (int) $_GET['slider'] : 0;

		if ( $slider_id && TDS_CPT::POST_TYPE === get_post_type( $slider_id ) ) {
			$this->render_editor( $slider_id );
			return;
		}

		$this->render_list();
	}

	/**
	 * Render the editor page (React mounts here).
	 *
	 * @param int $slider_id Slider post ID.
	 */
	private function render_editor( $slider_id ) {
    $back_url = admin_url( 'admin.php?page=topdown-slider' );

    $page_list = array();

    // Pages.
    $pages = get_posts(
        array(
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'orderby'        => 'title',
            'order'          => 'ASC',
        )
    );
    foreach ( $pages as $page ) {
        $page_list[] = array(
            'id'    => $page->ID,
            'title' => get_the_title( $page ),
            'link'  => get_permalink( $page ),
        );
    }

    // Posts.
    $posts = get_posts(
        array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'orderby'        => 'title',
            'order'          => 'ASC',
        )
    );
    foreach ( $posts as $post ) {
        $page_list[] = array(
            'id'    => $post->ID,
            'title' => sprintf( '%s (%s)', get_the_title( $post ), __( 'Post', 'topdown-slider' ) ),
            'link'  => get_permalink( $post ),
        );
    }
    ?>
    <div class="wrap">
        <p style="margin:0 0 8px;">
            <a href="<?php echo esc_url( $back_url ); ?>">&larr; <?php esc_html_e( 'All Sliders', 'topdown-slider' ); ?></a>
        </p>
        <div id="tds-root" data-slider-id="<?php echo esc_attr( $slider_id ); ?>"></div>
        <script type="application/json" id="tds-pages-data">
            <?php echo wp_json_encode( $page_list ); ?>
        </script>
    </div>
    <?php
}

	/**
	 * Render the list page.
	 */
	private function render_list() {
		$sliders = get_posts(
			array(
				'post_type'      => TDS_CPT::POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => array( 'publish', 'draft' ),
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		$create_url = wp_nonce_url(
			admin_url( 'admin-post.php?action=tds_create_slider' ),
			'tds_create_slider'
		);
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'TopDown Sliders', 'topdown-slider' ); ?></h1>
			<a href="<?php echo esc_url( $create_url ); ?>" class="page-title-action">
				<?php esc_html_e( 'Add New Slider', 'topdown-slider' ); ?>
			</a>
			<hr class="wp-header-end">

			<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only success notice.
if ( isset( $_GET['created'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Slider created.', 'topdown-slider' ); ?></p>
				</div>
			<?php endif; ?>

			<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only success notice.
if ( isset( $_GET['deleted'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Slider deleted.', 'topdown-slider' ); ?></p>
				</div>
			<?php endif; ?>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Title', 'topdown-slider' ); ?></th>
						<th style="width:100px;"><?php esc_html_e( 'Slides', 'topdown-slider' ); ?></th>
						<th style="width:340px;"><?php esc_html_e( 'Shortcode', 'topdown-slider' ); ?></th>
						<th style="width:260px;"><?php esc_html_e( 'Actions', 'topdown-slider' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $sliders ) ) : ?>
						<tr>
							<td colspan="4">
								<?php esc_html_e( 'No sliders yet. Click "Add New Slider" to create one.', 'topdown-slider' ); ?>
							</td>
						</tr>
					<?php else : ?>
						<?php foreach ( $sliders as $slider ) : ?>
							<?php
							$slides    = TDS_REST::get_slides( $slider->ID );
							$count     = count( $slides );
							$edit_url  = admin_url( 'admin.php?page=topdown-slider&slider=' . $slider->ID );
							$shortcode = '[topdown_slider id="' . $slider->ID . '"]';
							$del_url   = wp_nonce_url(
								admin_url( 'admin-post.php?action=tds_delete_slider&slider=' . $slider->ID ),
								'tds_delete_slider_' . $slider->ID
							);
							$dup_url   = wp_nonce_url(
								admin_url( 'admin-post.php?action=tds_duplicate_slider&slider=' . $slider->ID ),
								'tds_duplicate_slider_' . $slider->ID
							);
							?>
							<tr>
								<td><strong><?php echo esc_html( get_the_title( $slider ) ); ?></strong></td>
								<td><?php echo esc_html( $count ); ?></td>
								<td><code><?php echo esc_html( $shortcode ); ?></code></td>
								<td>
									<a href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Edit', 'topdown-slider' ); ?></a>
									|
									<a href="<?php echo esc_url( $dup_url ); ?>"><?php esc_html_e( 'Duplicate', 'topdown-slider' ); ?></a>
									|
									<a href="<?php echo esc_url( $del_url ); ?>" style="color:#b32d2e;"
										onclick="return confirm('<?php echo esc_js( __( 'Delete this slider? This cannot be undone.', 'topdown-slider' ) ); ?>');">
										<?php esc_html_e( 'Delete', 'topdown-slider' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Handle "Add New Slider".
	 */
	public function handle_create() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'topdown-slider' ) );
		}
		check_admin_referer( 'tds_create_slider' );

		$post_id = wp_insert_post(
			array(
				'post_type'   => TDS_CPT::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => __( 'Untitled Slider', 'topdown-slider' ),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			wp_die( esc_html( $post_id->get_error_message() ) );
		}

		wp_safe_redirect(
			admin_url( 'admin.php?page=topdown-slider&slider=' . $post_id . '&created=1' )
		);
		exit;
	}

	/**
	 * Handle "Delete".
	 */
	public function handle_delete() {

		if ( ! current_user_can( 'delete_post', $slider_id ) ) {
			wp_die( esc_html__( 'Not allowed.', 'topdown-slider' ) );
		}

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- verified on the next line.
        $slider_id = isset( $_GET['slider'] ) ? (int) $_GET['slider'] : 0;

		check_admin_referer( 'tds_delete_slider_' . $slider_id );

		wp_trash_post( $slider_id );

		wp_safe_redirect( admin_url( 'admin.php?page=topdown-slider&deleted=1' ) );
		exit;
	}

	/**
	 * Handle "Duplicate".
	 */
	public function handle_duplicate() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce verified below.
		$slider_id = isset( $_GET['slider'] ) ? (int) $_GET['slider'] : 0;

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'topdown-slider' ) );
		}
		check_admin_referer( 'tds_duplicate_slider_' . $slider_id );

		$original = get_post( $slider_id );
		if ( ! $original || TDS_CPT::POST_TYPE !== $original->post_type ) {
			wp_die( esc_html__( 'Slider not found.', 'topdown-slider' ) );
		}

		$new_id = wp_insert_post(
			array(
				'post_type'   => TDS_CPT::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => $original->post_title . ' (Copy)',
			),
			true
		);

		if ( is_wp_error( $new_id ) ) {
			wp_die( esc_html( $new_id->get_error_message() ) );
		}

		$slides = get_post_meta( $slider_id, '_tds_slides', true );
		if ( is_array( $slides ) ) {
			update_post_meta( $new_id, '_tds_slides', $slides );
		}

		wp_safe_redirect(
			admin_url( 'admin.php?page=topdown-slider&slider=' . $new_id . '&created=1' )
		);
		exit;
	}
}