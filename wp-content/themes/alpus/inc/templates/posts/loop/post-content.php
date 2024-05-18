<?php
/**
 * Post Content
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;
?>
<div class="post-content">
	<?php
	echo alpha_get_excerpt(
		$GLOBALS['post'],
		alpha_get_loop_prop( 'excerpt_length' ),
		alpha_get_loop_prop( 'excerpt_type' )
	);
	?>
</div>
