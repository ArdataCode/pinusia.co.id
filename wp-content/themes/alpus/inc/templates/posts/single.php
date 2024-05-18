<?php
/**
 * Single Post
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

$classes = get_post_class();

if ( ! in_array( 'post', $classes ) ) {
	$classes[] = 'post';
}
// The Post
the_post();
/**
 * Filters if the single builder is running.
 *
 * @since 1.0
 */
if ( ! apply_filters( 'alpha_run_single_builder', false ) ) {
	?>

<div class="<?php echo esc_attr( implode( ' ', apply_filters( 'alpha_post_single_class', array( 'post-single' ) ) ) ); ?>">

	<div class="post-wrap">

		<?php
		/**
		 * Fires before rendering post loop item.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_post_loop_before_item', 'default', 'single' );
		?>

		<article class="<?php echo esc_attr( implode( ' ', apply_filters( 'alpha_post_single_classes', $classes ) ) ); ?>">
			<?php alpha_get_template_part( 'posts/single/post', 'media' ); ?>
			<div class="post-details">
				<?php
				alpha_get_template_part( 'posts/single/post', 'title' );

				alpha_get_template_part( 'posts/single/post', 'meta' );

				alpha_get_template_part( 'posts/single/post', 'content' );

				echo '<div class="post-links">';

				alpha_get_template_part( 'posts/single/post', 'tag' );

				if ( function_exists( 'alpha_print_share' ) ) {
					alpha_print_share();
				}

				echo '</div>';

				alpha_get_template_part( 'posts/single/post', 'author' );
				?>
			</div>
		</article>

		<?php
		/**
		 * Fires after rendering post loop item
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_post_loop_after_item', 'default', 'single' );
		?>

	</div>
	<?php

		alpha_get_template_part( 'posts/single/post', 'navigation' );

		echo '<hr class="mb-0">';

		alpha_get_template_part( 'posts/single/post', 'related' );

		comments_template();

	?>
</div>
	<?php
}
