<?php
/**
 * The post default template
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.2.0
 */
defined( 'ABSPATH' ) || die;

$term = get_queried_object();
?>

<h3 class="term-title"><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>
