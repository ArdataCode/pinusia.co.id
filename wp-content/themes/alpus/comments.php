<?php
/**
 * Comments template
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

/**
 * Fires before print comments.
 *
 * @since 1.0
 */
do_action( 'alpha_before_comments' );

if ( post_password_required() ) {
	return;
}

if ( ! comments_open() && get_comments_number() < 1 ) {
	return;
}

if ( get_comments_number() ) {
	?>

<div id="comments" class="comments">

	<h3 class="title title-simple"><?php comments_number( false, esc_html__( 'One Comment', 'alpha' ), esc_html( _n( '% Comment', '% Comments', get_comments_number(), 'alpha' ) ) ); ?></h3>

	<?php if ( have_comments() ) : ?>

		<ol class="commentlist">
			<?php
			// List comments
			wp_list_comments(
				apply_filters(
					'alpha_filter_comment_args',
					array(
						'callback'          => 'alpha_post_comment',
						'style'             => 'ol',
						'format'            => 'html5',
						'short_ping'        => true,
						'reverse_top_level' => true,
					)
				)
			);
			?>
		</ol>

		<?php
		// Are there comments to navigate through?
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			echo '<div class="pagination" data-post-id="' . get_the_ID() . '">';
			/**
			 * Filters html for comments pagination.
			 *
			 * @since 1.0
			 */
			echo apply_filters( 'alpha_get_comments_pagination_html', paginate_comments_links( array( 'echo' => false ) ) );
			echo '</div>';
		endif;

		if ( ! comments_open() && get_comments_number() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'alpha' ); ?></p>
			<?php
		endif;
	endif;
	?>
</div>

	<?php
}

if ( comments_open() ) {

	$submit_label  = alpha_is_product() ? esc_html__( 'Submit', 'alpha' ) : esc_html__( 'Post Comment', 'alpha' );
	$submit_label .= '<i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-' . ( is_rtl() ? 'left' : 'right' ) . '"></i>';

	comment_form(
		apply_filters(
			'alpha_filter_comment_form_args',
			array(
				'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
				'title_reply_after'  => '</h3>',
				'fields'             => array(
					'author' => '<div class="col-md-6"><input name="author" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Name', 'alpha' ) . '*"></div>',
					'email'  => '<div class="col-md-6"><input name="email" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Email', 'alpha' ) . '*"> </div>',
				),
				'comment_field'      => '<textarea name="comment" id="comment" class="form-control" rows="4" maxlength="65525" required="required" placeholder="' . esc_attr__( 'Comment', 'alpha' ) . '*"></textarea>',
				'submit_button'      => '<button type="submit" class="btn btn-dark btn-submit">' . $submit_label . '</button>',
			)
		)
	);
}
