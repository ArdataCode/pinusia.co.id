<?php
/**
 * General function
 *
 * You can override hooks function and general function.
 * And changes functions that are attached hooks.
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

if ( ! function_exists( 'alpha_post_comment' ) ) {
	function alpha_post_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

				<?php if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) { ?>
			<div id="comment-<?php comment_ID(); ?>" class="comment comment-container">
				<p><?php esc_html_e( 'Pingback:', 'alpha' ); ?> <span><span><?php comment_author_link( get_comment_ID() ); ?></span></span> <?php edit_comment_link( esc_html__( '(Edit)', 'alpha' ), '<span class="edit-link">', '</span>' ); ?></p>
			</div>
			<?php } else { ?>
			<div class="comment">
				<figure class="comment-avatar">
					<?php echo get_avatar( $comment, 50 ); ?>
				</figure>

				<div class="comment-text">
					<div class="comment-header">
						<div class="comment-meta">
							<?php /* translators: %s represents the date of the comment. */ ?>
							<h5 class="comment-date"><?php printf( esc_html__( '%1$s at %2$s', 'alpha' ), get_comment_date(), get_comment_time() ); ?></h5>
							<h4 class="comment-name"><?php echo get_comment_author_link( get_comment_ID() ); ?></h4>
						</div>
						<?php
						comment_reply_link(
							array_merge(
								$args,
								array(
									'add_below' => 'comment',
									'depth'     => $depth,
									'max_depth' => $args['max_depth'],
								)
							)
						);
						?>
					</div>

					<?php
					if ( '0' == $comment->comment_approved ) {
						echo '<em>' . esc_html__( 'Your comment is awaiting moderation.', 'alpha' ) . '</em>';
						echo '<br />';
					}
					comment_text();
					?>
				</div>
			</div>
					<?php
			}
	}
}

if ( ! function_exists( 'alpha_search_form' ) ) {
	function alpha_search_form( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'type'             => 'classic',
				'toggle_type'      => 'overlap',
				'search_align'     => 'start',
				'show_categories'  => false,
				'where'            => '',
				'live_search'      => defined( 'ALPHA_PRO_VERSION' ) && (bool) alpha_get_option( 'live_search' ),
				'search_post_type' => defined( 'ALPHA_PRO_VERSION' ) ? alpha_get_option( 'search_post_type' ) : '',
				'label'            => '',
				'placeholder'      => '',
			)
		);
		extract( $args );

		ob_start();
		$class = '';
		if ( 'toggle' == $type ) {
			$class .= ' hs-toggle hs-' . $toggle_type;
		}

		if ( $show_categories ) {
			$class .= ' hs-expanded';
		} else {
			$class .= ' hs-simple';
		}
		if ( 'toggle' == $type && 'dropdown' == $toggle_type ) {
			$class .= ' hs-' . $search_align;
		}

		?>

		<div class="search-wrapper<?php echo esc_attr( $class ); ?>">
			<?php if ( 'toggle' == $type ) : ?>
			<a href="#" class="search-toggle" title="search" aria-label="<?php esc_attr_e( 'Search', 'alpha' ); ?>">
				<i class="<?php echo esc_attr( ALPHA_ICON_PREFIX . '-icon-search' ); ?>"></i>
				<?php if ( $label ) : ?>
				<span><?php echo esc_html( $label ); ?></span>
				<?php endif; ?>
			</a>
			<?php endif; ?>

			<?php if ( 'toggle' != $type || 'fullscreen' != $toggle_type ) : ?>
			<form action="<?php echo esc_url( home_url() ); ?>/" method="get" class="input-wrapper">
				<input type="hidden" name="post_type" value="<?php echo esc_attr( $search_post_type ); ?>"/>

				<?php if ( 'header' == $where && $show_categories ) : ?>
				<div class="select-box">
					<?php
					$args = array(
						'show_option_all' => esc_html__( 'All Categories', 'alpha' ),
						'hierarchical'    => 1,
						'class'           => 'cat',
						'echo'            => 1,
						'value_field'     => 'slug',
						'selected'        => 1,
						'depth'           => 1,
					);
					if ( 'product' == $search_post_type && class_exists( 'WooCommerce' ) ) {
						$args['taxonomy'] = 'product_cat';
						$args['name']     = 'product_cat';
					}
					wp_dropdown_categories( $args );
					?>
				</div>
				<?php endif; ?>

				<input type="search" aria-label="<?php esc_attr_e( 'Search', 'alpha' ); ?>" class="form-control" name="s" placeholder="<?php echo esc_attr( $placeholder ); ?>" required="" autocomplete="off">

				<?php if ( $live_search ) : ?>
					<div class="live-search-list"></div>
				<?php endif; ?>

				<button class="btn btn-search" aria-label="<?php esc_attr_e( 'Search Button', 'alpha' ); ?>" type="submit">
					<i class="<?php echo esc_attr( ALPHA_ICON_PREFIX . '-icon-search' ); ?>"></i>
				</button>

				<?php if ( 'overlap' == $toggle_type ) : ?>
				<div class="hs-close">
					<a href="#">
						<span class="close-wrap">
							<span class="close-line close-line1"></span>
							<span class="close-line close-line2"></span>
						</span>
					</a>
				</div>
				<?php endif; ?>
			<?php endif; ?>

			</form>
		</div>
		<?php

		/**
		 * Filters search form.
		 *
		 * @since 1.0
		 */
		echo apply_filters( 'alpha_get_search_form', ob_get_clean() );
	}
}

/**
 * Get the rating link html for compare page.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_rating_link_html_for_product_loop' ) ) {
	function alpha_get_rating_link_html_for_product_loop( $product ) {
		return '<a href="' . esc_url( get_the_permalink( $product->get_id() ) ) . '#reviews" class="woocommerce-review-link scroll-to" rel="nofollow">( ' . $product->get_review_count() . ' )</a>';
	}
}


/**
 * shop page - select form for products count
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_count_per_page' ) ) {
	function alpha_wc_count_per_page() {
		global $alpha_layout;
		/**
		 * Filters the count of showing products.
		 *
		 * @since 1.0
		 */
		$count_select = apply_filters( 'alpha_products_count_select', alpha_get_loop_prop( 'products_count_select', '9, _12, 24, 36' ) );
		$ts           = ! empty( $alpha_layout['top_sidebar'] ) && 'hide' != $alpha_layout['top_sidebar'] && is_active_sidebar( $alpha_layout['top_sidebar'] );
		?>
		<div class="toolbox-item toolbox-show-count select-box">
			<label><?php echo __( 'Show : ', 'alpha' ); ?></label>
			<select name="count" class="count form-control">
				<?php
				if ( ! empty( $count_select ) ) {
					$count_select = explode( ',', str_replace( ' ', '', $count_select ) );
				} else {
					$count_select = array( '9', '_12', '24', '36' );
				}

				$current = alpha_loop_shop_per_page( $count_select );

				foreach ( $count_select as $count ) {
					$num = (int) str_replace( '_', '', $count );
					echo '<option value="' . $num . '" ' . selected( $num == $current, true, false ) . '>' . $num . '</option>';
				}
				?>
			</select>
			<?php
			$except = array( 'count' );
			// Keep query string vars intact
			foreach ( $_GET as $key => $val ) {
				if ( in_array( $key, $except ) ) {
					continue;
				}

				if ( is_array( $val ) ) {
					foreach ( $val as $inner_val ) {
						echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $inner_val ) . '" />';
					}
				} else {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
				}
			}
			?>
		</div>
		<?php
	}
}

/**
 * Get pagination html
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_pagination_html' ) ) {
	function alpha_get_pagination_html( $paged, $total, $class = '' ) {

		$classes = array( 'pagination' );

		// Set up paginated links.
		$args  = apply_filters(
			'alpha_filter_pagination_args',
			array(
				'current'   => $paged,
				'total'     => $total,
				'end_size'  => 1,
				'mid_size'  => 2,
				'prev_text' => '<i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-left"></i> ',
				'next_text' => '<i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-right"></i>',
			)
		);
		$links = paginate_links( $args );

		if ( $class ) {
			$classes[] = esc_attr( $class );
		}

		if ( $links ) {

			if ( 1 == $paged ) {
				$links = sprintf(
					'<span class="prev page-numbers disabled">%s</span>',
					$args['prev_text']
				) . $links;
			} elseif ( $paged == $total ) {
				$links .= sprintf(
					'<span class="next page-numbers disabled">%s</span>',
					$args['next_text']
				);
			}

			$links = '<div class="' . implode( ' ', $classes ) . '">' . preg_replace( '/^\s+|\n|\r|\s+$/m', '', $links ) . '</div>';
		}

		return $links;
	}
}

/**
 * Update my account menu items
 *
 * @since 1.1.0
 * @see woocommerce_account_menu_items
 * @param array $items
 */
if ( ! function_exists( 'alpha_woocommerce_account_menu_items' ) ) {
	function alpha_woocommerce_account_menu_items( $items ) {
		$has_logout = false;

		// Move customer logout to last
		if ( isset( $items['customer-logout'] ) ) {
			$has_logout = $items['customer-logout'];
			unset( $items['customer-logout'] );
		}

		if ( defined( 'ALPHA_VENDORS' ) ) {
			$items['vendor_dashboard'] = esc_html__( 'Vendor Dashboard', 'alpha' );
		}

		if ( $has_logout ) {
			$items['customer-logout'] = $has_logout;
		}

		return $items;
	}
}

/**
 * Print page title bar.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_print_title_bar' ) ) {
	function alpha_print_title_bar() {
		global $alpha_layout;

		if ( is_front_page() ) {
			// Do not show page title bar and breadcrumb in home page.
			$site_desc = get_option( 'blogdescription' );
			if ( is_home() && ! empty( $site_desc ) ) {
				?>
				<div class="page-header">
					<div class="page-title-bar">
						<div class="page-title-wrap">
							<h2 class="page-title"><?php echo alpha_strip_script_tags( $site_desc ); ?></h2>
						</div>
					</div>
				</div>
				<?php
			}
		} else {
			if ( ! empty( $alpha_layout['ptb'] ) && 'hide' != $alpha_layout['ptb'] ) {
				// Display selected template instead of page title bar.
				$alpha_layout['is_page_header'] = true;
				alpha_print_template( $alpha_layout['ptb'] );
				unset( $alpha_layout['is_page_header'] );
			} elseif ( ( ! empty( $alpha_layout['ptb'] ) && 'hide' == $alpha_layout['ptb'] ) || apply_filters( 'alpha_is_vendor_store', false ) ) {
				// Hide page title bar.
			} elseif ( class_exists( 'WooCommerce' ) && ( is_cart() || is_checkout() ) ) {
				$alpha_layout['show_breadcrumb'] = 'no';
				?>
				<div class="woo-page-header">
					<div class="<?php echo esc_attr( 'full' == $alpha_layout['wrap'] ? 'container' : $alpha_layout['wrap'] ); ?>">
						<ul class="breadcrumb">
							<li class="<?php echo is_cart() ? esc_attr( 'current' ) : ''; ?>">
								<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php echo apply_filters( 'alpha_wc_checkout_ptb_title', esc_html( '1. Shopping Cart', 'alpha' ), 'cart' ); ?></a>
							</li>
							<li class="<?php echo is_checkout() && ! is_order_received_page() ? esc_attr( 'current' ) : ''; ?>">
								<i class="delimiter"></i>
								<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php echo apply_filters( 'alpha_wc_checkout_ptb_title', esc_html( '2. Checkout', 'alpha' ), 'checkout' ); ?></a>
							</li>
							<li class="<?php echo is_order_received_page() ? esc_attr( 'current' ) : esc_attr( 'disable' ); ?>">
								<i class="delimiter"></i>
								<a href="#"><?php echo apply_filters( 'alpha_wc_checkout_ptb_title', esc_html( '3. Order Complete', 'alpha' ), 'order' ); ?></a>
							</li>
						</ul>
					</div>
				</div>
				<?php
			} else {
				// Show page header
				if ( class_exists( 'WooCommerce' ) && is_shop() ) { // Shop Page
					$page_id = wc_get_page_id( 'shop' );
				} elseif ( is_home() && get_option( 'page_for_posts' ) ) { // Blog Page
					$page_id = get_option( 'page_for_posts' );
				} elseif ( is_singular() ) {
					$page_id = get_the_ID();
				} else {
					$page_id = -1;
				}

				Alpha_Layout_Builder::get_instance()->setup_titles();
				$page_title = get_post_meta( $page_id, 'page_title', true );
				if ( ! $page_title ) {
					$page_title = $alpha_layout['title'];
				}
				$page_subtitle = get_post_meta( $page_id, 'page_subtitle', true );
				if ( ! $page_subtitle ) {
					$page_subtitle = $alpha_layout['subtitle'];
				}
				if ( $page_title || $page_subtitle ) {
				?>
				<div class="page-header">
					<div class="page-title-bar">
						<div class="page-title-wrap">
						<?php if ( ! empty( $page_title ) ) : ?>
							<h2 class="page-title"><?php echo alpha_strip_script_tags( $page_title ); ?></h2>
							<?php endif; ?>
						<?php if ( ! empty( $page_subtitle ) ) : ?>
							<h3 class="page-subtitle"><?php echo alpha_strip_script_tags( $page_subtitle ); ?></h3>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php
				}
			}

			if ( empty( $alpha_layout['show_breadcrumb'] ) || 'no' != $alpha_layout['show_breadcrumb'] ) {
				alpha_breadcrumb();
			}
		}
	}
}

/**
 * Load Mobile Menu
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_load_mobile_menu' ) ) {
	function alpha_load_mobile_menu() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		?>
		<!-- Search Form -->
			<div class="search-wrapper hs-simple">
				<form action="<?php echo esc_url( home_url() ); ?>/" method="get" class="input-wrapper">
					<input type="hidden" name="post_type" value="<?php echo esc_attr( defined( 'ALPHA_PRO_VERSION' ) ? alpha_get_option( 'search_post_type' ) : '' ); ?>"/>
					<input type="search" class="form-control" name="s" placeholder="<?php echo esc_attr( esc_html__( 'Search', 'alpha' ) ); ?>" required="" autocomplete="off">

					<?php if ( defined( 'ALPHA_PRO_VERSION' ) && alpha_get_option( 'live_search' ) ) : ?>
						<div class="live-search-list"></div>
					<?php endif; ?>

					<button class="btn btn-search" type="submit">
						<i class="<?php echo ALPHA_ICON_PREFIX; ?>-icon-search"></i>
					</button> 
				</form>
			</div>

		<?php
		$mobile_menus      = alpha_get_option( 'mobile_menu_items' );
		$mobile_menus_objs = array();
		foreach ( $mobile_menus as $menu ) {
			if ( empty( $menu ) ) {
				continue;
			}
			$menu_obj = is_numeric( $menu ) ? get_term( $menu, 'nav_menu' ) : get_term_by( 'slug', $menu, 'nav_menu' );
			if ( empty( $menu_obj ) || is_wp_error( $menu_obj ) ) {
				continue;
			}
			$mobile_menus_objs[] = $menu_obj;
		}

		if ( ! empty( $mobile_menus ) ) {
			?>
			<div class="nav-wrapper">
				<?php
				if ( count( $mobile_menus ) > 1 ) {
					?>
					<div class="tab tab-nav-simple tab-nav-fill">
						<ul class="nav nav-tabs" role="tablist">
							<?php
							$first = true;
							foreach ( $mobile_menus_objs as $menu_obj ) :
								?>
								<li class="nav-item">
									<a class="nav-link<?php echo ! $first ? '' : ' active'; ?>" href="#alpus_mobile_menu_pos_<?php echo absint( $menu_obj->term_id ); ?>"><?php echo esc_html( $menu_obj->name ); ?></a>
								</li>
								<?php $first = false; ?>
							<?php endforeach; ?>
						</ul>
						<div class="tab-content">
							<?php
							$first = true;
							foreach ( $mobile_menus_objs as $menu_obj ) :
								?>
								<div class="tab-pane<?php echo ! $first ? '' : ' active in'; ?>" id="alpus_mobile_menu_pos_<?php echo absint( $menu_obj->term_id ); ?>">
									<?php
									wp_nav_menu(
										array(
											'menu'       => $menu_obj,
											'container'  => 'nav',
											'container_class' => $menu_obj->slug,
											'items_wrap' => '<ul id="%1$s" class="mobile-menu">%3$s</ul>',
											'walker'     => class_exists( 'Alpha_Walker_Nav_Menu' ) ? new Alpha_Walker_Nav_Menu() : new Walker_Nav_Menu(),
											'theme_location' => '',
										)
									);
									$first = false;
									?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php
				} else {
					foreach ( $mobile_menus_objs as $menu_obj ) {
						wp_nav_menu(
							array(
								'menu'            => $menu_obj,
								'container'       => 'nav',
								'container_class' => $menu_obj->slug,
								'items_wrap'      => '<ul id="%1$s" class="mobile-menu">%3$s</ul>',
								'walker'          => class_exists( 'Alpha_Walker_Nav_Menu' ) ? new Alpha_Walker_Nav_Menu() : new Walker_Nav_Menu(),
								'theme_location'  => '',
							)
						);
					}
				}
				?>
			</div>
			<?php
		} elseif ( has_nav_menu( 'main-menu' ) ) {
			?>
			<div class="nav-wrapper">
				<?php
				wp_nav_menu(
					array(
						'container'       => 'nav',
						'container_class' => '',
						'items_wrap'      => '<ul id="%1$s" class="mobile-menu">%3$s</ul>',
						'walker'          => class_exists( 'Alpha_Walker_Nav_Menu' ) ? new Alpha_Walker_Nav_Menu() : new Walker_Nav_Menu(),
						'theme_location'  => 'main-menu',
					)
				);
				?>
			</div>
			<?php
		}

		if ( alpha_doing_ajax() && $_REQUEST['action'] && 'alpha_load_mobile_menu' == $_REQUEST['action'] ) {
			die;
		}

		// phpcs:enable
	}
}

if ( ! function_exists( 'alpha_get_grid_space' ) ) {

	/**
	 * Get columns' gutter size value from size string
	 *
	 * @since 1.0
	 *
	 * @param string $col_sp Columns gutter size string
	 *
	 * @return int Gutter size value
	 */
	function alpha_get_grid_space( $col_sp ) {
		if ( 'no' == $col_sp ) {
			return 0;
		} elseif ( 'sm' == $col_sp ) {
			return 10;
		} elseif ( 'md' == $col_sp ) {
			return 20;
		} elseif ( 'lg' == $col_sp ) {
			return 30;
		} elseif ( 'xs' == $col_sp ) {
			return 2;
		} else {
			return 30;
		}
	}
}

if ( ! function_exists( 'alpha_get_slider_attrs' ) ) {

	/**
	 * Get slider data attribute from settings array
	 *
	 * @since 1.0
	 *
	 * @param array $settings Slider settings array from elementor widget.
	 * @param array $col_cnt  Columns count
	 * @param string $id      Hash string for element
	 *
	 * @return string slider data attribute
	 */
	function alpha_get_slider_attrs( $settings, $col_cnt, $id = '' ) {

		$max_breakpoints = alpha_get_breakpoints();

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$kit = get_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, 0 );
			if ( $kit ) {
				$site_settings = get_post_meta( get_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, 0 ), '_elementor_page_settings', true );
			}
		}

		$extra_options = array();

		if ( ! empty( $settings['slide_effect'] ) ) {
			$extra_options['effect'] = $settings['slide_effect'];
		}

		$extra_options['spaceBetween'] = ! empty( $settings['col_sp'] ) ? alpha_get_grid_space( $settings['col_sp'] ) : ( ! empty( $settings['col_sp_custom']['size'] ) ? $settings['col_sp_custom']['size'] : 20 );

		if ( ! empty( $settings['col_sp'] ) ) {
			$extra_options['spaceBetween'] = alpha_get_grid_space( $settings['col_sp'] );
		} elseif ( ! empty( $settings['col_sp_custom']['size'] ) ) {
			$extra_options['spaceBetween'] = $settings['col_sp_custom']['size'];
		} elseif ( ! empty( $settings['col_sp_custom_laptop']['size'] ) ) {
			$extra_options['spaceBetween'] = $settings['col_sp_custom_laptop']['size'];
		} elseif ( ! empty( $settings['col_sp_custom_tablet_extra']['size'] ) ) {
			$extra_options['spaceBetween'] = $settings['col_sp_custom_tablet_extra']['size'];
		} elseif ( ! empty( $site_settings['gutter_space']['size'] ) ) {
			$extra_options['spaceBetween'] = $site_settings['gutter_space']['size'];
		} elseif ( ! empty( $site_settings['gutter_space_laptop']['size'] ) ) {
			$extra_options['spaceBetween'] = $site_settings['gutter_space_laptop']['size'];
		} elseif ( ! empty( $site_settings['gutter_space_tablet_extra']['size'] ) ) {
			$extra_options['spaceBetween'] = $site_settings['gutter_space_tablet_extra']['size'];
		} else {
			$extra_options['spaceBetween'] = 20;
		}

		if ( isset( $settings['centered'] ) && 'yes' == $settings['centered'] ) { // default is false
			$extra_options['centeredSlides'] = true;
		}

		if ( isset( $settings['loop'] ) && 'yes' == $settings['loop'] ) { // default is false
			$extra_options['loop'] = true;
		}

		// Auto play
		if ( isset( $settings['autoplay'] ) && 'yes' == $settings['autoplay'] ) { // default is false
			if ( isset( $settings['autoplay_timeout'] ) ) { // default is 5000
				$extra_options['autoplay'] = array(
					'delay'                => (int) $settings['autoplay_timeout'],
					'disableOnInteraction' => false,
				);
			}
		}

		if ( ! empty( $settings['show_dots'] ) && ! empty( $settings['dots_type'] ) && $id ) {
			if ( 'thumb' == $settings['dots_type'] ) {
				$extra_options['dotsContainer'] = '.slider-thumb-dots-' . $id;
			} else {
				$extra_options['dotsContainer'] = '.slider-custom-html-dots-' . $id;
			}
		}
		if ( ! empty( $settings['show_nav'] ) ) {
			$extra_options['navigation'] = true;
		}
		if ( ! empty( $settings['show_dots'] ) ) {
			$extra_options['pagination'] = true;
		}
		if ( isset( $settings['autoheight'] ) && 'yes' == $settings['autoheight'] ) {
			$extra_options['autoHeight'] = true;
		}

		// Disable Mouse Drag
		if ( isset( $settings['disable_mouse_drag'] ) && 'yes' == $settings['disable_mouse_drag'] ) {
			$extra_options['allowTouchMove'] = false;
		}

		// Effect
		if ( isset( $settings['effect'] ) ) {
			$extra_options['effect'] = $settings['effect'];
		}
		if ( ! empty( $settings['speed'] ) ) {
			$extra_options['speed'] = $settings['speed'];
		}

		$responsive = array();
		$w          = array(
			'min' => 'mobile',
			'sm'  => 'mobile_extra',
			'md'  => 'tablet',
			'lg'  => 'tablet_extra',
			'xl'  => 'laptop',
			'xlg' => '',
			'xxl' => 'widescreen',
		);

		$col_cnt = function_exists( 'alpha_get_responsive_cols' ) ? alpha_get_responsive_cols( $col_cnt ) : $col_cnt;

		$parent_sp_custom = $extra_options['spaceBetween'];
		$parent_sp_global = $extra_options['spaceBetween'];
		foreach ( array_reverse( $w ) as $key => $device ) {
			if ( $device ) {
				$device = '_' . $device;
			}
			if ( ! empty( $col_cnt[ $key ] ) ) {
				$responsive[ $max_breakpoints[ $key ] ] = array(
					'slidesPerView' => $col_cnt[ $key ],
				);
			}
			if ( empty( $settings['col_sp'] ) ) {
				if ( ! empty( $settings[ 'col_sp_custom' . $device ]['size'] ) ) {
					if ( ! isset( $responsive[ $max_breakpoints[ $key ] ] ) ) {
						$responsive[ $max_breakpoints[ $key ] ] = array();
					}
					$parent_sp_custom                                       = $settings[ 'col_sp_custom' . $device ]['size'];
					$responsive[ $max_breakpoints[ $key ] ]['spaceBetween'] = $settings[ 'col_sp_custom' . $device ]['size'];
				} elseif ( $parent_sp_custom != $extra_options['spaceBetween'] ) {
					if ( ! isset( $responsive[ $max_breakpoints[ $key ] ] ) ) {
						$responsive[ $max_breakpoints[ $key ] ] = array();
					}
					$responsive[ $max_breakpoints[ $key ] ]['spaceBetween'] = $parent_sp_custom;
				} elseif ( ! empty( $site_settings[ 'gutter_space' . $device ]['size'] ) ) {
					if ( ! isset( $responsive[ $max_breakpoints[ $key ] ] ) ) {
						$responsive[ $max_breakpoints[ $key ] ] = array();
					}
					$parent_sp_global                                       = $site_settings[ 'gutter_space' . $device ]['size'];
					$responsive[ $max_breakpoints[ $key ] ]['spaceBetween'] = $site_settings[ 'gutter_space' . $device ]['size'];
				} elseif ( $parent_sp_global != $extra_options['spaceBetween'] ) {
					if ( ! isset( $responsive[ $max_breakpoints[ $key ] ] ) ) {
						$responsive[ $max_breakpoints[ $key ] ] = array();
					}
					$responsive[ $max_breakpoints[ $key ] ]['spaceBetween'] = $parent_sp_global;
				}
			}
		}

		if ( isset( $col_cnt['xlg'] ) ) {
			$extra_options['slidesPerView'] = $col_cnt['xlg'];
		} elseif ( isset( $col_cnt['xl'] ) ) {
			$extra_options['slidesPerView'] = $col_cnt['xl'];
		} elseif ( isset( $col_cnt['lg'] ) ) {
			$extra_options['slidesPerView'] = $col_cnt['lg'];
		}

		if ( ! empty( $settings['dots_type'] ) && $id ) {
			$extra_options['pagination'] = false;
			foreach ( $responsive as $w => $c ) {
				$responsive[ $w ]['pagination'] = false;
			}
		}

		$extra_options['breakpoints'] = $responsive;

		$extra_options['statusClass'] = trim( ( empty( $settings['status_class'] ) ? '' : $settings['status_class'] ) . alpha_get_slider_status_class( $settings ) );
		return $extra_options;
	}
}

/**
 * Echo or Return inline css.
 * This function only uses for composed by style tag.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_filter_inline_css' ) ) :
	function alpha_filter_inline_css( $inline_css, $is_echo = true ) {
		if ( ! class_exists( 'Alpha_Optimize_Stylesheets' ) ) {
			if ( $is_echo ) {
				echo alpha_escaped( $inline_css );
				return;
			} else {
				return $inline_css;
			}
		}
		if ( empty( Alpha_Optimize_Stylesheets::get_instance()->is_merged ) ) { // not merge
			if ( $is_echo ) {
				echo alpha_escaped( $inline_css );
			} else {
				return $inline_css;
			}
		} else {
			if ( 'no' == Alpha_Optimize_Stylesheets::get_instance()->has_merged_css() ) {
				global $alpha_body_merged_css;
				if ( isset( $alpha_body_merged_css ) ) {
					$inline_css             = str_replace( PHP_EOL, '', $inline_css );
					$inline_css             = preg_replace( '/<style.*?>/s', '', $inline_css ) ? : $inline_css;
					$inline_css             = preg_replace( '/<\/style.*?>/s', '', $inline_css ) ? : $inline_css;
					$alpha_body_merged_css .= $inline_css;
				}
			}
			return '';
		}
	}
endif;
