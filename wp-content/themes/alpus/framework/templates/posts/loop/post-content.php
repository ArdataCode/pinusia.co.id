<?php
/**
 * Post Content
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;

$readmore_btn = '<a href="' . esc_url( get_the_permalink() ) . '" class="btn-readmore">(' . esc_html__( 'Read More', 'alpha' ) . ')</a>';

?>
<div class="post-content">
	<?php
	echo alpha_get_excerpt(
		$GLOBALS['post'],
		alpha_get_loop_prop( 'excerpt_length' ),
		alpha_get_loop_prop( 'excerpt_type' ),
		$readmore_btn
	);
	?>
</div>
