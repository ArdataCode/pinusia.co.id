<?php
/**
 * Post Title
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;
?>
<h3 class="post-title">
	<a href="<?php echo esc_url( get_the_permalink() ); ?>">
		<?php the_title(); ?>
	</a>
</h3>
