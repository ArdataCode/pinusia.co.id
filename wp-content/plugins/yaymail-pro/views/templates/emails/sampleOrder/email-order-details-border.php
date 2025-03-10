<?php

defined( 'ABSPATH' ) || exit;

use YayMail\Page\Source\CustomPostType;
$postID          = CustomPostType::postIDByTemplate( $this->template );
$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#7f54b3';

$sent_to_admin = ( isset( $sent_to_admin ) ? $sent_to_admin : false );
$text_align    = is_rtl() ? 'right' : 'left';
$productItemCost  = isset( $yaymail_settings['product_item_cost'] ) ? $yaymail_settings['product_item_cost'] : 0;

?>

<h2 class="yaymail_builder_order" style="color: #7f54b3;">
	<?php
	$before = '<a style="color: ' . esc_attr( $text_link_color ) . '" class="yaymail_builder_link" href="">';
	$after  = '</a>';
	/* translators: %s: Order ID. */
	echo wp_kses_post( $before . sprintf( __( '[Order #%s]', 'woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)', 1, new WC_DateTime(), wc_format_datetime( new WC_DateTime() ) ) );
	?>
</h2>

<table class="yaymail_builder_table_items_content" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;color: inherit;flex-direction:column;border: 1px solid;border-color: inherit;" width="100%">
	<thead>
		<tr style="word-break: normal">
			<th class="td" scope="col" style="text-align:left;">
				<?php esc_html_e( 'Product', 'woocommerce' ); ?>
			</th>
			<?php if ( $productItemCost ) { ?>
				<th class="td" scope="col" style="text-align:left;">
					<?php esc_html_e( 'Cost', 'woocommerce' ); ?>
				</th>
			<?php } ?>
			<th class="td" scope="col" style="text-align:left;">
				<?php esc_html_e( 'Quantity', 'woocommerce' ); ?>
			</th>
			<th class="td" scope="col" style="text-align:left;">
				<?php esc_html_e( 'Price', 'woocommerce' ); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="td" scope="row" style="text-align:left;">
				<?php esc_html_e( 'Happy YayCommerce2', 'yaymail' ); ?>
			</td>
			<td class="td" scope="row" style="text-align:left;">
				<?php esc_html_e( 1, 'yaymail' ); ?>
			<td class="td" scope="row" style="text-align:left;">
				<?php esc_html_e( '£18.00', 'yaymail' ); ?>
			</td>
		</tr>

		<tr>
			<td class="td" scope="row" colspan="<?php echo esc_attr( $productItemCost ? 3 : 2 ); ?>" style="text-align:left;font-weight:700;border-top-width: 4px;">
				<?php esc_html_e( 'Subtotal:', 'yaymail' ); ?>
			</td>
			<td class="td" scope="row" colspan="1" style="text-align:left;border-top-width: 4px;">
				<?php esc_html_e( '£18.00', 'yaymail' ); ?>
			</td>
		</tr>
		<tr>
			<td class="td" scope="row" colspan="<?php echo esc_attr( $productItemCost ? 3 : 2 ); ?>" style="text-align:left;font-weight:700;">
				<?php esc_html_e( 'Payment method:', 'yaymail' ); ?>
			</td>
			<td class="td" scope="row" colspan="1" style="text-align:left;">
				<?php esc_html_e( 'Direct bank transfer', 'yaymail' ); ?>
			</td>
		</tr>
		<tr>
			<td class="td" scope="row" colspan="<?php echo esc_attr( $productItemCost ? 3 : 2 ); ?>" style="text-align:left;font-weight:700;">
				<?php esc_html_e( 'Total:', 'yaymail' ); ?>
			</td>
			<td class="td" scope="row" colspan="1" style="text-align:left;">
				<?php esc_html_e( '£18.00', 'yaymail' ); ?>
			</td>
		</tr>
	</tbody>
</table>
