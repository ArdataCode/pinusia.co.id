<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $product_tabs ) ) :
	/**
	 * Filters tab type in single product page.
	 *
	 * @since 1.0
	 */
	$product_tabs_type = apply_filters( 'alpha_single_product_data_tab_type', 'tab' );
	$wrapper_class     = 'woocommerce-tabs';

	if ( 'accordion' == $product_tabs_type ) {
		$wrapper_class .= ' wc-tabs-wrapper accordion accordion-border accordion-boxed';
	} elseif ( 'tab' == $product_tabs_type ) {
		$wrapper_class .= ' wc-tabs-wrapper tab tab-underline';
	} else {
		$wrapper_class .= ' wc-tabs-wrapper tab-sections';
	}
	?>

	<?php
	/**
	 * Fires before rendering product tabs.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_wc_product_before_tabs' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'alpha_single_product_data_tab_class', $wrapper_class ) ); ?>" role="tablist">

		<?php if ( 'tab' == $product_tabs_type ) : ?>
			<ul class="nav nav-tabs tabs wc-tabs">
				<?php
				$index = 0;
				foreach ( $product_tabs as $key => $product_tab ) :
					?>
					<li class="nav-item <?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
						<a href="#tab-<?php echo esc_attr( $key ); ?>" class="nav-link<?php echo ! $index ? ' active' : ''; ?>">
							<?php echo alpha_strip_script_tags( apply_filters( 'woocommerce_product_' . $key . '_tab_title2', $product_tab['title'], $key ) ); ?>
						</a>
					</li>
					<?php
					$index ++;
				endforeach;
				?>
			</ul>
		<?php endif; ?>

		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
			<?php
			$panel_class = 'entry-content';

			if ( 'accordion' == $product_tabs_type ) {
				$panel_class .= ' card-body collapsed panel wc-tab';

				?>
				<div class="card">
					<div class="card-header <?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
						<a href="#tab-<?php echo esc_attr( $key ); ?>" class="expand">
							<?php echo alpha_strip_script_tags( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
							<span class="toggle-icon opened"><i class="<?php echo ALPHA_ICON_PREFIX; ?>-icon-angle-up"></i></span>
							<span class="toggle-icon closed"><i class="<?php echo ALPHA_ICON_PREFIX; ?>-icon-angle-down"></i></span>
						</a>
					</div>
				<?php
			} elseif ( 'tab' == $product_tabs_type ) {
				$panel_class .= ' tab-pane panel wc-tab';
			} else {
				$panel_class .= ' tab-section';
			}
			$panel_class .= ' woocommerce-Tabs-panel woocommerce-Tabs-panel--' . esc_attr( $key );
			?>

			<div class="<?php echo esc_attr( $panel_class ); ?>" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel"<?php echo 'section' != $product_tabs_type ? ' aria-labelledby="tab-title-' . esc_attr( $key ) . '"' : ''; ?>>
				<?php
				if ( 'section' == $product_tabs_type && ! in_array( $key, array( 'description', 'additional_information', 'vendor', 'reviews' ) ) ) :
					?>
					<h2 class="title-wrapper title-underline">
						<span class="title"><?php echo alpha_strip_script_tags( $product_tab['title'] ); ?></span>
					</h2>
					<?php
				endif;

				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>


			<?php if ( 'accordion' == $product_tabs_type ) { ?>
				</div>
			<?php } ?>

		<?php endforeach; ?>

	</div>

	<?php do_action( 'woocommerce_product_after_tabs' ); ?>

<?php endif; ?>
