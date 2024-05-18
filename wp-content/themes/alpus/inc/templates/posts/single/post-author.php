<?php
/**
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

$author_description = get_the_author_meta( 'description' );
if ( ! $author_description ) {
	return;
}

$author_name = get_the_author_meta( 'display_name' );
$author_link = get_author_posts_url( get_the_author_meta( 'ID' ) );
?>
<div class="post-author-detail">
	<figure class="author-avatar">
		<?php echo get_avatar( get_the_ID(), 50 ); ?>
	</figure>
	<div class="author-body">
		<div class="author-header">
			<div class="author-meta">
				<h4 class="author-name">
					<?php echo esc_html( $author_name ); ?>
				</h4>
			</div>
		</div>
		<div class="author-content">
			<?php echo alpha_strip_script_tags( $author_description ); ?>
			<a class="author-link" href="<?php echo esc_url( $author_link ); ?>">
				<?php printf( esc_html__( 'View all posts by %s', 'alpha' ), esc_html( $author_name ) ); ?>
				<i class="<?php echo ALPHA_ICON_PREFIX; ?>-icon-long-arrow-right-solid"></i>
			</a>
		</div>
	</div>
</div>
