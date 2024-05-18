<?php
/**
 * Post Archive
 *
 * @var string $no_heading      "Nothing Found" title from archive builder
 * @var string $no_description  "Nothing Found" description from archive builder
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;

/**
 * Filters if the archive builder is running.
 *
 * @since 1.0
 */
if ( ! empty( $is_builder_rendering ) || ! apply_filters( 'alpha_run_archive_builder', false ) ) {

	?>
	<div class="post-archive">
		<?php

		if ( have_posts() ) {
			/**
			 * Fires before archive posts widget render.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_before_posts_loop', empty( $props ) ? '' : $props );

			alpha_get_template_part( 'posts/post', 'loop-start', array( 'is_archive' => true ) );

			while ( have_posts() ) :
				the_post();
				alpha_get_template_part( 'posts/post' );
			endwhile;

			alpha_get_template_part( 'posts/post', 'loop-end' );

			/**
			 * Fires after archive posts widget render.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_after_posts_loop' );
		} else {

			?>
			<h2 class="entry-title"><?php esc_html_e( 'Nothing Found', 'alpha' ); ?></h2>

			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				<p class="alert alert-light alert-info">
					<?php
					printf(
						// translators: %1$s represents open tag of admin url to create new, %2$s represents close tag.
						esc_html__( 'Ready to publish your first post? %1$sGet started here%2$s.', 'alpha' ),
						sprintf( '<a href="%1$s" target="_blank">', esc_url( admin_url( 'post-new.php' . '?post_type=' . get_post_type() ) ) ),
						'</a>'
					);
					?>
				</p>
			<?php elseif ( is_search() ) : ?>
				<p class="alert alert-light alert-info"><?php echo esc_html( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'alpha' ); ?></p>
			<?php else : ?>
				<p class="alert alert-light alert-info">
					<?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'alpha' ); ?>
				</p>
			<?php endif; ?>
			<?php
		}
		?>
	</div>
	<?php
}
