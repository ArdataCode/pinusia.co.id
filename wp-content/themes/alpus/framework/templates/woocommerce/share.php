<?php
/**
 * Share template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $share_title string Title for share section
 * @var $share_facebook_enabled bool Whether to enable FB sharing button
 * @var $share_twitter_enabled bool Whether to enable Twitter sharing button
 * @var $share_pinterest_enabled bool Whether to enable Pintereset sharing button
 * @var $share_email_enabled bool Whether to enable Email sharing button
 * @var $share_whatsapp_enabled bool Whether to enable WhatsApp sharing button (mobile online)
 * @var $share_url_enabled bool Whether to enable share via url
 * @var $share_link_title string Title to use for post (where applicable)
 * @var $share_link_url string Url to share
 * @var $share_summary string Summary to use for sharing on social media
 * @var $share_image_url string Image to use for sharing on social media
 * @var $share_twitter_summary string Summary to use for sharing on Twitter
 * @var $share_facebook_icon string Icon for facebook sharing button
 * @var $share_twitter_icon string Icon for twitter sharing button
 * @var $share_pinterest_icon string Icon for pinterest sharing button
 * @var $share_email_icon string Icon for email sharing button
 * @var $share_whatsapp_icon string Icon for whatsapp sharing button
 * @var $share_whatsapp_url string Sharing url on whatsapp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$social_shares = alpha_get_social_shares();

?>

<?php do_action( 'yith_wcwl_before_wishlist_share', $wishlist ); ?>

<div class="yith-wcwl-share">
	<h4 class="yith-wcwl-share-title"><?php echo esc_html( $share_title ); ?></h4>
	<ul class="social-icons">
		<?php
		if ( $share_facebook_enabled ) {
			$link = strtr(
				$social_shares['facebook']['link'],
				array(
					'$permalink' => $share_link_url,
					'$title'     => $share_link_title,
					'$image'     => $share_image_url,
				)
			);
			?>
			<li class="share-button">
				<a target="_blank" class="social-icon facebook social-facebook"
					href="<?php echo esc_url( $link ? $link : '#' ); ?>"
					title="<?php esc_attr_e( 'Facebook', 'alpha' ); ?>">
					<?php echo alpha_strip_script_tags( $share_facebook_icon ? $share_facebook_icon : esc_html__( 'Facebook', 'alpha' ) ); ?>
				</a>
			</li>
			<?php
		}

		if ( $share_twitter_enabled ) {
			$link = strtr(
				$social_shares['twitter']['link'],
				array(
					'$permalink' => $share_link_url,
					'$title'     => $share_link_title,
					'$image'     => $share_image_url,
				)
			);
			?>
			<li class="share-button">
				<a target="_blank" class="social-icon twitter social-twitter"
					href="<?php echo esc_url( $link ? $link : '#' ); ?>"
					title="<?php esc_attr_e( 'Twitter', 'alpha' ); ?>">
					<?php echo alpha_strip_script_tags( $share_twitter_icon ? $share_twitter_icon : esc_html__( 'Twitter', 'alpha' ) ); ?>
				</a>
			</li>
			<?php
		}

		if ( $share_pinterest_enabled ) {
			$link = strtr(
				$social_shares['pinterest']['link'],
				array(
					'$permalink' => $share_link_url,
					'$title'     => $share_link_title,
					'$image'     => $share_image_url,
				)
			);
			?>
			<li class="share-button">
				<a target="_blank" class="social-icon pinterest social-pinterest"
					href="<?php echo esc_url( $link ? $link : '#' ); ?>"
					title="<?php esc_attr_e( 'Pinterest', 'alpha' ); ?>" onclick="window.open(this.href); return false;">
					<?php echo alpha_strip_script_tags( $share_pinterest_icon ? $share_pinterest_icon : esc_html__( 'Pinterest', 'alpha' ) ); ?>
				</a>
			</li>
			<?php
		}

		if ( $share_email_enabled ) {
			$link = strtr(
				$social_shares['email']['link'],
				array(
					'$permalink' => $share_link_url,
					'$title'     => $share_link_title,
					'$image'     => $share_image_url,
				)
			);
			?>
			<li class="share-button">
				<a class="social-icon email social-email"
					href="<?php echo esc_attr( $link ? $link : '#' ); ?>"
					title="<?php esc_attr_e( 'Email', 'alpha' ); ?>">
					<?php echo alpha_strip_script_tags( $share_email_icon ? $share_email_icon : esc_html__( 'Email', 'alpha' ) ); ?>
				</a>
			</li>
			<?php
		}

		if ( $share_whatsapp_enabled ) {
			$link = strtr(
				$social_shares['whatsapp']['link'],
				array(
					'$permalink' => $share_link_url,
					'$title'     => $share_link_title,
					'$image'     => $share_image_url,
				)
			);
			?>
			<li class="share-button">
				<a class="social-icon whatsapp social-whatsapp"
					href="<?php echo esc_attr( $link ? $link : '#' ); ?>" data-action="share/whatsapp/share"
					target="_blank" title="<?php esc_attr_e( 'WhatsApp', 'alpha' ); ?>">
					<?php echo alpha_strip_script_tags( $share_whatsapp_icon ? $share_whatsapp_icon : esc_html__( 'Whatsapp', 'alpha' ) ); ?>
				</a>
			</li>
			<?php
		}
		?>
	</ul>

	<?php if ( $share_url_enabled ) : ?>
		<div class="yith-wcwl-after-share-section">
			<input class="copy-target" readonly="readonly" type="url" name="yith_wcwl_share_url" id="yith_wcwl_share_url" value="<?php echo esc_url( $share_link_url ? $share_link_url : '#' ); ?>"/>
			<?php echo ( ! empty( $share_link_url ) ) ? sprintf( '<small>%s <span class="copy-trigger">%s</span> %s</small>', esc_html__( '(Now', 'alpha' ), esc_html__( 'copy', 'alpha' ), esc_html__( 'this wishlist link and share it anywhere)', 'alpha' ) ) : ''; ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'yith_wcwl_after_share_buttons', $share_link_url, $share_title, $share_link_title ); ?>
</div>

<?php do_action( 'yith_wcwl_after_wishlist_share', $wishlist ); ?>
