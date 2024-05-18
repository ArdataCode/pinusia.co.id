<?php
/**
 * The post default template
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

?>
<div class="post-media-meta">
	<?php
	alpha_get_template_part( 'posts/loop/post', 'media' );
	alpha_get_template_part( 'posts/loop/post', 'meta' );
	?>
</div>
<div class="post-details">
	<?php
	alpha_get_template_part( 'posts/loop/post', 'category' );
	alpha_get_template_part( 'posts/loop/post', 'title' );
	alpha_get_template_part( 'posts/loop/post', 'content' );
	?>
</div>
