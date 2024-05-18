<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || die;
do_action( 'woocommerce_before_mini_cart' ); ?>

<?php
	$items = sizeof( WC()->cart->get_cart() );
	echo '<div class="cart-header">';
		/* translators: %s: Items count */
		echo '<h4 class="cart-title">' . esc_html__( 'Shopping Cart', 'alpha' ) . '</h4>';
		echo '<a class="btn btn-dark btn-link btn-icon-right btn-close" href="#">' . esc_html__( 'Close', 'alpha' ) . '<i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-right"></i><span class="sr-only">' . esc_html__( 'Cart', 'alpha' ) . '</span></a>';
	echo '</div>';
?>

<?php if ( ! WC()->cart->is_empty() ) : ?>

	<ul class="mini-list scrollable woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_data()['name'], $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
				<li class="woocommerce-mini-cart-item mini-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
					<div class="cart-item-meta mini-item-meta">
						<?php
						if ( empty( $product_permalink ) ) :
							echo alpha_strip_script_tags( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						else :
							echo '<a href="' . esc_url( $product_permalink ) . '">' . alpha_strip_script_tags( $product_name ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endif;

						echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						if ( ! alpha_get_option( 'cart_show_qty' ) ) {
							$product_quantity = $cart_item['quantity'];
						} else {
							$product_quantity = sprintf(
								'<input type="number" id="quantity_%1$s" name="%1$s" class="qty" value="%2$d" min="%3$d" max="%4$d" /><button class="quantity-minus %5$s-icon-minus" title="Minus"></button><button class="quantity-plus %5$s-icon-plus" title="Plus"></button>',
								$cart_item_key,
								$cart_item['quantity'],
								1,
								$_product->get_max_purchase_quantity() > 0 ? $_product->get_max_purchase_quantity() : 1000000,
								ALPHA_ICON_PREFIX
							);
						}

						echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s <span class="times">&times</span> %s', $product_quantity, $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>
					<?php
					if ( empty( $product_permalink ) ) :
						echo alpha_escaped( $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					else :
						echo '<a href="' . esc_url( $product_permalink ) . '">' . $thumbnail . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					endif;
					?>
					<?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><i class="fas fa-times"></i></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_attr__( 'Remove this item', 'alpha' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						),
						$cart_item_key
					);
					?>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<?php
	/**
	 * Fires for creat mini cart coupon html.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_fbt_mini_cart_coupon_html' );
	?>

	<p class="woocommerce-mini-cart__total total">
		<?php
		/**
		 * Hook: woocommerce_widget_shopping_cart_total.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

	<?php
	else :
		/**
		 * Fires for delete coupon html.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_fbt_no_coupon_html' );
		?>

	<p class="woocommerce-mini-cart__empty-message empty-msg"><?php esc_html_e( 'No products in the cart.', 'alpha' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
