<?php
/**
 * Post Tag
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;

$tags = get_the_tag_list();

if ( $tags ) :
	?>
	<div class="post-tags">
		<?php echo alpha_strip_script_tags( $tags ); ?>
	</div>
	<?php
endif;
