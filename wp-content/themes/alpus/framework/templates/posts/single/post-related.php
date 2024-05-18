<?php
/**
 * Post Related
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;

if ( 'attachment' == get_post_type() ) {
	return;
}

global $alpha_layout;

if ( ! isset( $args ) ) {
	$args = array();
}

$args = wp_parse_args(
	$args,
	array(
		'post_type'           => get_post_type(),
		'post__not_in'        => array( get_the_ID() ),
		'ignore_sticky_posts' => 0,
		'category__in'        => wp_get_post_categories( get_the_ID() ),
		'posts_per_page'      => 4,
		'no_found_rows'       => true,
		'orderby'             => '',
		'order'               => '',
	)
);
/**
 * Filters the arguments of related posts.
 *
 * @since 1.0
 */
$related_posts = new WP_Query( apply_filters( 'alpha_filter_related_posts_args', $args ) );

if ( $related_posts && $related_posts->have_posts() ) :

	$post_type_object = get_post_type_object( get_post_type() );
	?>
	<section class="related-posts">
		<?php // translators: %s represents post type label ?>
		<h3 class="title title-simple"><?php printf( esc_html__( 'Related %s', 'alpha' ), $post_type_object->label ); ?></h3>
		<div>
			<?php
			$props = array(
				'posts_layout' => 'slider',
				'related'      => true,
			);

			/**
			 * Fires before archive posts widget render.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_before_posts_loop', $props );

			alpha_get_template_part( 'posts/post', 'loop-start' );

			while ( $related_posts->have_posts() ) :
				$related_posts->the_post();
				alpha_get_template_part( 'posts/post' );
			endwhile;

			alpha_get_template_part( 'posts/post', 'loop-end' );

			/**
			 * Fires after archive posts widget render.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_after_posts_loop' );

			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php
endif;
