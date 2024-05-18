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
?>
<div class="post-content">
	<?php the_content(); ?>
	<?php alpha_get_page_links_html(); ?>
</div>
