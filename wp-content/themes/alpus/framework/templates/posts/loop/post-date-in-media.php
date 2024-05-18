<?php
/**
 * Post Date In Media
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;
?>
<a class="post-calendar" href="<?php echo esc_url( get_day_link( get_post_time( 'Y' ), get_post_time( 'm' ), get_post_time( 'j' ) ) ); ?>">
	<span class="post-day"><?php echo esc_html( get_the_time( 'd' ) ); ?></span>
	<span class="post-month"><?php echo esc_html( get_the_time( 'M' ) ); ?></span>
</a>
