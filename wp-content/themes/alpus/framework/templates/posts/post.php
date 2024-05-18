<?php
/**
 * post.php
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

$posts_layout = alpha_get_loop_prop( 'posts_layout' );
$classes      = array_merge( get_post_class(), alpha_get_loop_prop( 'loop_classes', array() ) );
$wrap_class   = array();
$wrap_attrs   = '';

// For Widget
if ( alpha_get_loop_prop( 'widget' ) ) {
	global $alpha_post_idx;
	++ $alpha_post_idx;

	if ( 'creative' == $posts_layout ) {
		$repeaters    = alpha_get_loop_prop( 'repeaters' );
		$wrap_class[] = 'grid-item';
		if ( ! empty( $repeaters ) ) {
			if ( isset( $repeaters['ids'][ $alpha_post_idx ] ) ) {
				$wrap_class[] = $repeaters['ids'][ $alpha_post_idx ];
			}
			if ( isset( $repeaters['ids'][0] ) ) {
				$wrap_class[] = $repeaters['ids'][0];
			}
			if ( isset( $repeaters['images'][ $alpha_post_idx ] ) ) {
				$GLOBALS['alpha_current_post_img_size'] = $repeaters['images'][ $alpha_post_idx ];
			}
		}
		$wrap_attrs = ' data-grid-idx="' . (int) $alpha_post_idx . '"';

	} elseif ( 'slider' == $posts_layout && alpha_get_loop_prop( 'row_cnt' ) >= 2 && 1 == $alpha_post_idx % alpha_get_loop_prop( 'row_cnt', 2 ) ) {
		echo '<div class="post-col">';
	}
}

/**
 * Filters the class(attrs) of post wrapper.
 *
 * @since 1.0
 */
$wrap_class = apply_filters( 'alpha_post_wrap_class', $wrap_class );
$wrap_attrs = apply_filters( 'alpha_post_wrap_attrs', $wrap_attrs );

// Render
?>
<div class="post-wrap <?php echo esc_attr( implode( ' ', $wrap_class ) ); ?>"<?php echo alpha_escaped( $wrap_attrs ); ?> data-post-image="<?php echo esc_attr( alpha_get_loop_prop( 'image_size' ) ); ?>">
	<?php
	/**
	 * Fires before rendering post loop item.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_post_loop_before_item' );
	?>
	<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php alpha_get_template_part( 'posts/type/post', 'default' ); ?>
	</article>
	<?php
	/**
	 * Fires after rendering post loop item.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_post_loop_after_item' );
	?>
</div>

<?php
if ( 'slider' == $posts_layout && isset( $GLOBALS['alpha_post_idx'] ) && alpha_get_loop_prop( 'row_cnt' ) >= 2 && 0 == $GLOBALS['alpha_post_idx'] % alpha_get_loop_prop( 'row_cnt' ) ) {
	echo '</div>';
}
