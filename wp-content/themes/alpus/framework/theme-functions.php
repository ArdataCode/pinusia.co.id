<?php
/**
 * Theme Functions
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;
/**
 * Get Theme Option
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_option' ) ) {
	function alpha_get_option( $option, $fallback = '' ) {
		global $alpha_option;

		if ( ! isset( $alpha_option ) ) {
			require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/theme-options.php' );

			$theme_mods = get_theme_mods();
			if ( is_array( $theme_mods ) ) {
				$alpha_option = array_merge( $alpha_option, $theme_mods );
			}

			if ( empty( $alpha_option['conditions'] ) ) {
				$alpha_option['conditions'] = $default_conditions;
			}
		}
		if ( is_customize_preview() ) {
			try {
				if ( isset( $_POST['customized'] ) ) {
					$modified = json_decode( stripslashes_deep( $_POST['customized'] ), true );

					if ( isset( $modified[ $option ] ) ) {
						return $modified[ $option ];
					}
					return get_theme_mod( $option, isset( $alpha_option[ $option ] ) ? $alpha_option[ $option ] : $fallback );
				}
			} catch ( Exception $e ) {
			}
		}
		return isset( $alpha_option[ $option ] ) ? $alpha_option[ $option ] : $fallback;
	}
}

/**
 * Set Default Theme Option
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_set_default_option' ) ) {
	function alpha_set_default_option( $option, $value ) {
		global $alpha_option;

		if ( isset( $alpha_option ) && ! isset( $alpha_option[ $option ] ) ) {
			$alpha_option[ $option ] = $value;
			return true;
		}
		return false;
	}
}

/**
 * Call remove filter callbacks
 *
 * @since 1.0.0
 */
function alpha_call_clean_filter( $hook, $callback, $priority = 10 ) {
	if ( function_exists( 'alpha_clean_filter' ) ) {
		alpha_clean_filter( $hook, $callback, $priority );
	}
}


/**
 * Get page list
 *
 * @since 1.0
 *
 * @return {Array} $pages
 */
function alpha_get_pages_arr() {
	$pages = array();

	foreach ( get_pages() as $page ) {
		$pages[ $page->ID ] = $page->post_title;
	}

	return $pages;
}

/**
 * Get page object
 *
 * @since 1.0
 *
 * @return {Object} $page_got_by_title
 */
function alpha_get_page_by_title( $title ) {
	$query = new WP_Query(
		array(
			'post_type'              => 'page',
			'title'                  => $title,
			'post_status'            => 'all',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'orderby'                => 'post_date ID',
			'order'                  => 'ASC',
		)
	);
	 
	if ( ! empty( $query->post ) ) {
		$page_got_by_title = $query->post;
	} else {
		$page_got_by_title = null;
	}
	return $page_got_by_title;
}

/**
 * Get social shares.
 *
 * @since 1.0
 */
function alpha_get_social_shares() {
	return apply_filters(
		'alpha_social_links',
		array(
			'facebook'  => array(
				'title' => esc_html__( 'Facebook', 'alpha' ),
				'icon'  => 'fab fa-facebook-f',
				'link'  => 'https://www.facebook.com/sharer.php?u=$permalink',
			),
			'twitter'   => array(
				'title' => esc_html__( 'Twitter', 'alpha' ),
				'icon'  => 'fab fa-twitter',
				'link'  => 'https://twitter.com/intent/tweet?text=$title&amp;url=$permalink',
			),
			'linkedin'  => array(
				'title' => esc_html__( 'Linkedin', 'alpha' ),
				'icon'  => 'fab fa-linkedin-in',
				'link'  => 'https://www.linkedin.com/shareArticle?mini=true&amp;url=$permalink&amp;title=$title',
			),
			'email'     => array(
				'title' => esc_html__( 'Email', 'alpha' ),
				'icon'  => 'far fa-envelope',
				'link'  => 'mailto:?subject=$title&amp;body=$permalink',
			),
			'pinterest' => array(
				'title' => esc_html__( 'Pinterest', 'alpha' ),
				'icon'  => 'fab fa-pinterest-p',
				'link'  => 'https://pinterest.com/pin/create/button/?url=$permalink&amp;media=$image',
			),
			'reddit'    => array(
				'title' => esc_html__( 'Reddit', 'alpha' ),
				'icon'  => 'fab fa-reddit-alien',
				'link'  => 'http://www.reddit.com/submit?url=$permalink&amp;title=$title',
			),
			'tumblr'    => array(
				'title' => esc_html__( 'Tumblr', 'alpha' ),
				'icon'  => 'fab fa-tumblr',
				'link'  => 'http://www.tumblr.com/share/link?url=$permalink&amp;name=$title&amp;description=$excerpt',
			),
			'vk'        => array(
				'title' => esc_html__( 'VK', 'alpha' ),
				'icon'  => 'fab fa-vk',
				'link'  => 'https://vk.com/share.php?url=$permalink&amp;title=$title&amp;image=$image&amp;noparse=true',
			),
			'whatsapp'  => array(
				'title' => esc_html__( 'WhatsApp', 'alpha' ),
				'icon'  => 'fab fa-whatsapp',
				'link'  => 'whatsapp://send?text=$title-$permalink',
			),
			'xing'      => array(
				'title' => esc_html__( 'Xing', 'alpha' ),
				'icon'  => 'fab fa-xing',
				'https://www.xing-share.com/app/user?op=share;sc_p=xing-share;url=$permalink',
			),
			'instagram' => array(
				'title' => esc_html__( 'Instagram', 'alpha' ),
				'icon'  => 'fab fa-instagram',
				'link'  => '',
			),
		)
	);
}

/**
 * Load google font
 *
 * @since 1.0
 */
function alpha_load_google_font() {
	/**
	 * Filters google fonts.
	 *
	 * @since 1.0
	 */
	$typos        = apply_filters( 'alpha_google_fonts', array( 'typo_default', 'typo_heading', 'typo_custom1', 'typo_custom2', 'typo_custom3', 'typo_ptb_title', 'typo_ptb_subtitle', 'typo_ptb_breadcrumb' ) );
	$weights      = array();
	$fonts        = array();
	$google_fonts = class_exists( 'Kirki_Fonts' ) ? Kirki_Fonts::get_google_fonts() : array();

	foreach ( $typos as $typo ) {
		$t      = alpha_get_option( $typo );
		$family = empty( $t['font-family'] ) ? '' : $t['font-family'];

		if ( 'inherit' == $family || 'initial' == $family || '' == $family ) {
			continue;
		}

		if ( ! isset( $t['variant'] ) ) {
			$weight = '400';
		} elseif ( 'normal' == $t['variant'] || 'regular' == $t['variant'] ) {
			$weight = '400';
		} elseif ( 'italic' == $t['variant'] ) {
			$weight = '400italic';
		} else {
			$weight = $t['variant'];
		}

		if ( ! array_key_exists( $family, $weights ) ) {
			$weights[ $family ] = array( '300', '400', '500', '600', '700' );
		}

		if ( ! in_array( $weight, $weights[ $family ] ) ) {
			$weights[ $family ][] = $weight;
		}
	}

	global $alpha_layout;
	if ( ! empty( $alpha_layout['used_blocks'] ) ) {
		foreach ( $alpha_layout['used_blocks'] as $block_id => $block_content ) {
			$block_fonts = json_decode( rawurldecode( get_post_meta( $block_id, 'alpha_vc_google_fonts', true ) ), true );
			if ( ! empty( $block_fonts ) ) {
				$weights = array_merge_recursive( $weights, $block_fonts );
			}
		}
	}

	if ( is_singular() ) {
		$page_id    = get_the_ID();
		$page_fonts = json_decode( rawurldecode( get_post_meta( $page_id, 'alpha_vc_google_fonts', true ) ), true );

		if ( ! empty( $page_fonts ) ) {
			$weights = array_merge_recursive( $weights, $page_fonts );
		}
	}

	foreach ( $weights as $family => $weight ) {
		$weight  = array_unique( $weight );
		$fonts[] = str_replace( ' ', '+', $family ) . ( ! empty( $google_fonts ) && isset( $google_fonts[ $family ] ) && 1 >= count( $google_fonts[ $family ]['variants'] ) ? '' : ':' . implode( ',', $weight ) );
	}

	if ( $fonts ) {
		if ( is_admin() || alpha_get_option( 'google_webfont' ) ) {
			$fonts_str = implode( "','", $fonts );
			if ( alpha_get_option( 'font_face_display' ) ) {
				$fonts_str .= '&display=swap';
			}
			?>
			<script>
				WebFontConfig = {
					google: { families: [ '<?php echo alpha_strip_script_tags( $fonts_str ); ?>' ] }
				};
				(function(d) {
					var wf = d.createElement('script'), s = d.scripts[0];
					wf.src = '<?php echo ALPHA_FRAMEWORK_URI . '/assets/js/webfont.js'; ?>';
					wf.async = true;
					s.parentNode.insertBefore(wf, s);
				})(document);
			</script>
			<?php
		} else {
			$g_link = 'https://fonts.googleapis.com/css?family=' . implode( '%7C', $fonts );
			if ( alpha_get_option( 'font_face_display' ) ) {
				$g_link .= '&display=swap';
			}
			wp_enqueue_style( 'alpha-google-fonts', esc_url( $g_link ) );
		}
	}
}

/**
 * icl display language.
 *
 * @since 1.0
 */
function alpha_icl_disp_language( $native_name, $translated_name = false, $lang_native_hidden = false, $lang_translated_hidden = false ) {
	if ( function_exists( 'icl_disp_language' ) ) {
		return icl_disp_language( $native_name, $translated_name, $lang_native_hidden, $lang_translated_hidden );
	}
	$ret = '';

	if ( ! $native_name && ! $translated_name ) {
		$ret = '';
	} elseif ( $native_name && $translated_name ) {
		$hidden1 = '';
		$hidden2 = '';
		$hidden3 = '';
		if ( $lang_native_hidden ) {
			$hidden1 = 'style="display:none;"';
		}
		if ( $lang_translated_hidden ) {
			$hidden2 = 'style="display:none;"';
		}
		if ( $lang_native_hidden && $lang_translated_hidden ) {
			$hidden3 = 'style="display:none;"';
		}

		if ( $native_name != $translated_name ) {
			$ret =
				'<span ' .
				$hidden1 .
				' class="icl_lang_sel_native">' .
				$native_name .
				'</span> <span ' .
				$hidden2 .
				' class="icl_lang_sel_translated"><span ' .
				$hidden1 .
				' class="icl_lang_sel_native">(</span>' .
				$translated_name .
				'<span ' .
				$hidden1 .
				' class="icl_lang_sel_native">)</span></span>';
		} else {
			$ret = '<span ' . $hidden3 . ' class="icl_lang_sel_current">' . esc_html( $native_name ) . '</span>';
		}
	} elseif ( $native_name ) {
		$ret = $native_name;
	} elseif ( $translated_name ) {
		$ret = $translated_name;
	}

	return $ret;
}

/**
 * Get responsive cols
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_responsive_cols' ) ) {
	function alpha_get_responsive_cols( $cols, $type = 'product' ) {
		$result = array();
		$base   = ! empty( $cols['xlg'] ) ? $cols['xlg'] : 4;

		if ( 6 < $base ) { // 7, 8
			$result = array(
				'xlg' => $base,
				'xl'  => $base,
				'lg'  => 6,
				'md'  => 4,
				'sm'  => 3,
				'min' => 2,
			);
		} elseif ( 4 < $base ) { // 5, 6
			$result = array(
				'xlg' => $base,
				'xl'  => $base,
				'lg'  => 4,
				'md'  => 4,
				'sm'  => 3,
				'min' => 2,
			);

		} elseif ( 2 < $base ) { // 3, 4
			$result = array(
				'xlg' => $base,
				'xl'  => $base,
				'lg'  => $base,
				'md'  => 3,
				'sm'  => 2,
				'min' => 2,
			);

			if ( 'post' == $type ) {
				$result['min'] = 1;
			}
		} else { // 1, 2
			$result = array(
				'xlg' => $base,
				'xl'  => $base,
				'lg'  => $base,
				'md'  => $base,
				'sm'  => 1,
				'min' => 1,
			);
		}

		foreach ( $cols as $w => $c ) {
			if ( 'xlg' != $w && $c > 0 ) {
				$result[ $w ] = $c;
			}
		}

		/**
		 * Filters responsive columns.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_filter_reponsive_cols', $result, $cols );
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
		} elseif ( 'lg' == $col_sp ) {
			return 30;
		} elseif ( 'xs' == $col_sp ) {
			return 2;
		} else {
			return 20;
		}
	}
}

/**
 * Get overlay class
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_overlay_class' ) ) {
	function alpha_get_overlay_class( $overlay ) {
		if ( 'light' === $overlay ) {
			return 'overlay-light';
		}
		if ( 'dark' === $overlay ) {
			return 'overlay-dark';
		}
		if ( 'zoom' === $overlay ) {
			return 'overlay-zoom';
		}
		if ( 'zoom_light' === $overlay ) {
			return 'overlay-zoom overlay-light';
		}
		if ( 'zoom_dark' === $overlay ) {
			return 'overlay-zoom overlay-dark';
		}
		if ( 0 == strncmp( $overlay, 'effect-', 7 ) ) {
			return 'overlay-' . $overlay;
		}
		return '';
	}
}

/**
 * Loadmore attributes
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_loadmore_attributes' ) ) {
	function alpha_loadmore_attributes( $cpt, $props, $args, $loadmore_type, $max_num_pages, $is_filter_cat = false ) {
		if ( empty( $args ) ) {
			$args = array();
		}
		$args['cpt'] = $cpt;

		return 'data-load="' . esc_attr(
			json_encode(
				array(
					'props' => $props,
					'args'  => $args,
					'max'   => $max_num_pages,
				)
			)
		) . '"';
	}
}

/**
 * Loadmore html
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_loadmore_html' ) ) {
	function alpha_loadmore_html( $query, $loadmore_type, $loadmore_label, $loadmore_btn_style = '', $name_prefix = '' ) {
		if ( 'button' == $loadmore_type ) {
			$class = 'btn btn-load ';

			if ( $loadmore_btn_style ) {
				$class .= function_exists( 'alpha_widget_button_get_class' ) ? implode( ' ', alpha_widget_button_get_class( $loadmore_btn_style, $name_prefix ) ) : '';
			} else {
				$class .= 'btn-primary';
			}

			$label = empty( $loadmore_label ) ? esc_html__( 'Load More', 'alpha' ) : esc_html( $loadmore_label );
			echo '<button class="' . esc_attr( $class ) . '">' . ( $loadmore_btn_style && function_exists( 'alpha_widget_button_get_label' ) ? alpha_widget_button_get_label( $loadmore_btn_style, null, $label, $name_prefix ) : $label ) . '</button>';
		} elseif ( 'page' == $loadmore_type || ! $loadmore_type ) {
			echo alpha_get_pagination( $query, 'pagination-load' );
		}
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
				'prev_text' => '<i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-left"></i> ' . esc_html__( 'Prev', 'alpha' ),
				'next_text' => esc_html__( 'Next', 'alpha' ) . ' <i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-right"></i>',
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
 * Get page links html
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_page_links_html' ) ) :
	function alpha_get_page_links_html() {
		if ( ! is_singular() ) {
			return;
		}
		global $page, $numpages, $multipage;

		if ( $multipage ) {
			global $wp_rewrite;
			$post       = get_post();
			$query_args = array();
			$prev_link  = '';
			$next_link  = '';

			if ( ! get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
				if ( $page + 1 <= $numpages ) {
					$next_link = add_query_arg( 'page', $page + 1, get_permalink() );
				}
				if ( $page > 1 ) {
					$prev_link = add_query_arg( 'page', $page - 1, get_permalink() );
				}
			} elseif ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID ) {
				if ( $page + 1 <= $numpages ) {
					$next_link = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . ( $page + 1 ), 'single_paged' );
				}
				if ( $page > 1 ) {
					$prev_link = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . ( $page - 1 ), 'single_paged' );
				}
			} else {
				if ( $page + 1 <= $numpages ) {
					$next_link = trailingslashit( get_permalink() ) . user_trailingslashit( $page + 1, 'single_paged' );
				}
				if ( $page > 1 ) {
					$prev_link = trailingslashit( get_permalink() ) . user_trailingslashit( $page - 1, 'single_paged' );
				}
			}
			if ( $prev_link ) {
				$prev_html_escaped = '<a class="prev page-numbers" href="' . esc_url( $prev_link ) . '"><i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-left"></i> ' . esc_html__( 'Prev', 'alpha' ) . '</a>';
			} else {
				$prev_html_escaped = '<span class="prev page-numbers disabled"><i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-left"></i> ' . esc_html__( 'Prev', 'alpha' ) . '</span>';
			}
			if ( $next_link ) {
				$next_html_escaped = '<a class="next page-numbers" href="' . esc_url( $next_link ) . '">' . esc_html__( 'Next', 'alpha' ) . ' <i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-right"></i></a>';
			} else {
				$next_html_escaped = '<span class="next page-numbers disabled">' . esc_html__( 'Next', 'alpha' ) . ' <i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-right"></i></span>';
			}

			wp_link_pages(
				array(
					'before' => '<div class="pagination-footer"><div class="links pagination" role="navigation">' . $prev_html_escaped,
					'after'  => $next_html_escaped . '</div></div>',
				)
			);
		}
	}
endif;

/**
 * Get pagination
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_pagination' ) ) {
	function alpha_get_pagination( $query = '', $class = '' ) {

		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}

		$paged = $query->get( 'paged' ) ? $query->get( 'paged' ) : ( $query->get( 'page' ) ? $query->get( 'page' ) : 1 );
		$total = $query->max_num_pages;

		return alpha_get_pagination_html( $paged, $total, $class );
	}
}

/**
 * Pagination
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_pagination' ) ) {
	function alpha_pagination( $query = '', $class = '' ) {
		echo alpha_get_pagination( $query, $class );
	}
}

/**
 * Get excerpt
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_excerpt' ) ) {
	function alpha_get_excerpt( $post, $excerpt_length = 45, $excerpt_type = 'words', $readmore_btn = '' ) {
		if ( has_excerpt( $post ) ) {
			echo alpha_trim_description( alpha_strip_script_tags( get_the_excerpt( $post ) ), $excerpt_length, $excerpt_type ) . alpha_escaped( $readmore_btn );
		} elseif ( strpos( $post->post_content, '<!--more-->' ) ) {
			$content = apply_filters( 'the_content', get_the_content() );
			if ( $content ) {
				echo alpha_escaped( $content . $readmore_btn );
			}
		} else {
			$content = alpha_trim_description( $post->post_content, $excerpt_length, $excerpt_type );
			if ( $content ) {
				echo alpha_escaped( $content . $readmore_btn );
			}
		}
	}
}


/**
 * Get excerpt.
 *
 * @since 1.0
 *
 * @param  string $text  Whole excerpt content
 * @param  int    $limit Excerpt length
 * @param  string $unit  Excerpt unit
 * @return string Returns exctracted excerpt
 */
function alpha_trim_description( $text = '', $limit = 45, $unit = 'words' ) {
	$content = wp_strip_all_tags( $text );
	$content = strip_shortcodes( $content );

	if ( ! $limit ) {
		$limit = 45;
	}

	if ( ! $unit ) {
		$unit = 'words';
	}

	if ( 'words' == $unit ) {
		$content = wp_trim_words( $content, $limit );
	} else { // by characters
		$affix   = ( strlen( $content ) < $limit ? '' : ' ...' );
		$content = mb_substr( $content, 0, $limit ) . $affix;
	}

	if ( $content ) {
		$content = '<p>' . wp_strip_all_tags( $content ) . '</p>';
	}

	/**
	 * Filters trim description.
	 *
	 * @since 1.0
	 */
	return apply_filters( 'alpha_filter_trim_description', $content );
}

/**
 * The post comment
 *
 * @since 1.0
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
					<h4 class="comment-name">
						<?php echo get_comment_author_link( get_comment_ID() ); ?>
					</h4>

					<?php /* translators: %s represents the date of the comment. */ ?>
					<h5 class="comment-date"><?php printf( esc_html__( '%1$s at %2$s', 'alpha' ), get_comment_date(), get_comment_time() ); ?></h5>
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<em><?php esc_html_e( 'Your comment is awaiting moderation.', 'alpha' ); ?></em>
						<br />
					<?php endif; ?>
					<?php comment_text(); ?>
					<div class="comment-action">
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
				</div>
			</div>
				<?php
			}
	}
}

/**
 * Doing ajax
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_doing_ajax' ) ) {
	function alpha_doing_ajax() {
		// WordPress ajax
		if ( wp_doing_ajax() ) {
			return true;
		}

		/**
		 * Filters ajax doing.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_core_filter_doing_ajax', false );
	}
}

/**
 * Get period from
 *
 * @since 1.0
 */
function alpha_get_period_from( $time ) {
	$time = time() - $time;     // to get the time since that moment
	$time = ( $time < 1 ) ? 1 : $time;

	$tokens = array(
		31536000 => 'year',
		2592000  => 'month',
		604800   => 'week',
		86400    => 'day',
		3600     => 'hour',
		60       => 'minute',
		1        => 'second',
	);

	foreach ( $tokens as $unit => $text ) {
		if ( $time < $unit ) {
			continue;
		}
		$number_of_units = floor( $time / $unit );
		return $number_of_units . ' ' . $text . ( ( $number_of_units > 1 ) ? 's' : '' );
	}
}

/**
 * Minify css
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_minify_css' ) ) {
	function alpha_minify_css( $style ) {
		if ( ! $style ) {
			return;
		}

		// Change ::before, ::after to :before, :after
		$style = str_replace( array( '::before', '::after' ), array( ':before', ':after' ), $style );
		$style = preg_replace( '/\s+/', ' ', $style );
		$style = preg_replace( '/;(?=\s*})/', '', $style );
		$style = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $style );
		$style = preg_replace( '/ (,|;|\{|})/', '$1', $style );
		$style = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $style );
		$style = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $style );
		$style = preg_replace( '/\/\*[^\/]*\*\//', '', $style );
		$style = str_replace( array( '}100%{', ',100%{', ' !important', ' >' ), array( '}to{', ',to{', '!important', '>' ), $style );

		// Trim
		$style = trim( $style );
		return $style;
	}
}

if ( ! function_exists( 'alpha_get_page_layout' ) ) {

	/**
	 * Get current page's layout name.
	 *
	 * @since   1.0
	 * @return  {string}
	 */
	function alpha_get_page_layout() {

		/**
		 * Check global variable first
		 *
		 * @since 1.2.0
		 */
		global $alpha_page_layout;
		if ( $alpha_page_layout ) {
			return $alpha_page_layout;
		}

		/**
		 * Filters current page layout.
		 *
		 * @since 1.0
		 */
		$layout = apply_filters( 'alpha_get_page_layout', '' );
		if ( $layout ) {
			return $layout;
		}
		if ( is_404() ) {
			return 'error';
		}
		if ( class_exists( 'WooCommerce' ) && is_cart() ) {
			return 'cart';
		}
		if ( class_exists( 'WooCommerce' ) && is_checkout() ) {
			return 'checkout';
		}
		if ( alpha_is_shop() ) { // product archive
			return 'archive_product';
		}

		$type = get_post_type();
		if ( ! $type ) {
			$type = get_query_var( 'post_type' );
			if ( ! $type ) {
				$type = 'post';
			}
		}

		if ( is_home() || is_archive() || is_search() ) {
			$taxonomy = get_query_var( 'taxonomy' );
			if ( $taxonomy ) {
				$taxonomy = get_taxonomy( $taxonomy );
				if ( ! empty( $taxonomy->object_type ) ) {
					$type = $taxonomy->object_type[0];
				}
			}

			if ( 'post' == $type || 'attachment' == $type || is_search() && 'any' == get_query_var( 'post_type' ) ) {
				return 'archive_post';
			}
			return 'archive_' . $type; // custom post type archive
		}
		if ( is_page() || ALPHA_NAME . '_template' == $type ) { // single page
			return 'single_page';
		}
		if ( alpha_is_product() ) { // product single
			return 'single_product';
		}
		if ( is_single() ) {
			if ( 'post' == $type || 'attachment' == $type ) {
				return 'single_post';
			}
			return 'single_' . $type; // custom post type single page
		}

		return '';
	}
}

if ( ! function_exists( 'alpha_breadcrumb' ) ) {
	/**
	 * Display breadcrumb using WooCommerce.
	 *
	 * @since   1.0
	 */
	function alpha_breadcrumb() {

		global $alpha_layout;

		$wrap_class = 'breadcrumb-container';
		if ( isset( $alpha_layout['breadcrumb_wrap'] ) && 'full' != $alpha_layout['breadcrumb_wrap'] ) {
			$wrap_class .= 'container-fluid' == $alpha_layout['breadcrumb_wrap'] ? ' container-fluid' : ' container';
		}
		echo '<div class="' . esc_attr( $wrap_class ) . '">';
		if ( function_exists( 'rank_math_the_breadcrumbs' ) && ( RankMath\Helper::get_settings( 'general.breadcrumbs' ) ) ) {
			echo '<div class="breadcrumb">';
			rank_math_the_breadcrumbs();
			echo '</div>';
		} else {
			/**
			 * Fires after run breadcrumb functions.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_breadcrumb' );
		}
		echo '</div>';
	}
}

/**
 * Is shop
 *
 * @since 1.0
 */
function alpha_is_shop() {
	if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_category() || is_product_tag() || ( is_search() && isset( $_POST['post_type'] ) && 'product' == $_POST['post_type'] ) ) ) { // Shop Page
		/**
		 * Filters page is shop page or not.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_is_shop', true );
	} else {
		$term = get_queried_object();
		if ( class_exists( 'WooCommerce' ) && $term && isset( $term->taxonomy ) &&
			in_array(
				$term->taxonomy,
				get_taxonomies(
					array(
						'object_type' => array( 'product' ),
					),
					'names',
					'and'
				)
			) ) {
			return apply_filters( 'alpha_is_shop', true );
		}
	}

	return apply_filters( 'alpha_is_shop', false );
}

/**
 * Is product
 *
 * @since 1.0
 */
function alpha_is_product() {
	if ( class_exists( 'WooCommerce' ) && is_product() ) {
		return true;
	}
	/**
	 * Filters page is product page or not.
	 *
	 * @since 1.0
	 */
	return apply_filters( 'alpha_is_product', false );
}

/**
 * Set loop prop for woocommerce
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_set_loop_prop' ) ) {
	function alpha_wc_set_loop_prop() {
		// Category Props //////////////////////////////////////////////////////////////////////////////
		wc_set_loop_prop( 'category_type', 'default' );

		// Product Props ///////////////////////////////////////////////////////////////////////////////
		wc_set_loop_prop( 'product_type', 'default' );

		if ( alpha_is_shop() || alpha_is_product() ) {
			wc_set_loop_prop( 'show_labels', array( 'hot', 'sale', 'new', 'stock' ) );
		}
	}
}

if ( ! function_exists( 'alpha_wc_get_loop_prop' ) ) {
	/**
	 * Gets a property from the woocommerce_loop global.
	 *
	 * @since 1.0
	 * @param string $prop Prop to get.
	 * @param string $default Default if the prop does not exist.
	 * @return mixed
	 */
	function alpha_wc_get_loop_prop( $prop, $default = '' ) {

		if ( empty( $GLOBALS['woocommerce_loop'] ) ) {
			wc_setup_loop(); // Ensure shop loop is setup.
		}

		return isset( $GLOBALS['woocommerce_loop'], $GLOBALS['woocommerce_loop'][ $prop ] ) ? $GLOBALS['woocommerce_loop'][ $prop ] : $default;
	}
}

if ( ! function_exists( 'alpha_print_mobile_bar' ) ) {

	/**
	 * Print alpha mobile navigation bar
	 *
	 * @since 1.0
	 *
	 */
	function alpha_print_mobile_bar() {
		$mobile_bar = alpha_get_option( 'mobile_bar_icons' );
		$result     = '';
		$cnt        = 0;

		if ( ! $mobile_bar ) {
			return;
		}

		foreach ( $mobile_bar as $item ) {
			$icon  = alpha_get_option( 'mobile_bar_' . $item . '_icon' );
			$label = alpha_get_option( 'mobile_bar_' . $item . '_label' );

			if ( 'menu' == $item ) {
				if ( alpha_get_option( 'mobile_menu_items' ) ) {
					$result .= '<a href="#" class="mobile-menu-toggle mobile-item" aria-label="' . esc_attr__( 'Mobile Menu', 'alpha' ) . '"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
					++ $cnt;
				}
			} elseif ( 'home' == $item ) {
				$result .= '<a href="' . esc_url( home_url() ) . '" class="mobile-item' . ( is_front_page() ? ' active' : '' ) . '"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
				++ $cnt;
			} elseif ( 'shop' == $item && class_exists( 'WooCommerce' ) ) {
				$item_class   = 'mobile-item mobile-item-categories';
				$mobile_menus = alpha_get_option( 'mobile_menu_items' );

				if ( ! empty( $mobile_menus ) && count( $mobile_menus ) ) {
					foreach ( $mobile_menus as $menu ) {
						if ( 'categories' == $menu ) {
							$item_class .= ' show-categories-menu';
							break;
						}
					}
				}
				$result .= '<a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="' . esc_attr( $item_class ) . '"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
				++ $cnt;
			} elseif ( 'wishlist' == $item && class_exists( 'WooCommerce' ) && defined( 'YITH_WCWL' ) ) {
				$result .= '<a href="' . esc_url( YITH_WCWL()->get_wishlist_url() ) . '" class="mobile-item"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
				++ $cnt;
			} elseif ( 'compare' == $item && class_exists( 'WooCommerce' ) ) {
				$result .= '<a href="' . esc_url( wc_get_page_permalink( 'compare' ) ) . '" class="mobile-item compare-open"><i class="' . ALPHA_ICON_PREFIX . '-icon-compare"></i><span>' . esc_html__( 'Compare', 'alpha' ) . '</span></a>';
				$cnt ++;
			} elseif ( 'account' == $item && class_exists( 'WooCommerce' ) ) {
				$result .= '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" class="mobile-item"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
				++ $cnt;
			} elseif ( 'cart' == $item && class_exists( 'WooCommerce' ) ) {
				ob_start();
				?>

				<div class="dropdown cart-dropdown dir-up">
					<a class="cart-toggle mobile-item" href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>">
						<i class="<?php echo esc_attr( $icon ); ?>"></i>
						<span><?php echo esc_html( $label ); ?></span>
					</a>
				</div>

				<?php
				$result .= ob_get_clean();
				++ $cnt;
			} elseif ( 'search' == $item ) {
				ob_start();
				?>
				<div class="search-wrapper hs-toggle rect">
					<a href="#" class="search-toggle mobile-item"><i class="<?php echo esc_attr( $icon ); ?>"></i><span><?php echo esc_html( $label ); ?></span></a>
					<form action="<?php echo esc_url( home_url() ); ?>/" method="get" class="input-wrapper">
						<input type="hidden" name="post_type" value="<?php echo esc_attr( alpha_get_option( 'search_post_type' ) ); ?>"/>
						<input type="search" class="form-control" name="s" placeholder="<?php esc_attr_e( 'Search', 'alpha' ); ?>" required="" autocomplete="off">

						<?php if ( alpha_get_option( 'live_search' ) ) : ?>
							<div class="live-search-list"></div>
						<?php endif; ?>

						<button class="btn btn-search" type="submit" aria-label="<?php esc_attr_e( 'Search Button', 'alpha' ); ?>">
							<i class="<?php echo ALPHA_ICON_PREFIX; ?>-icon-search"></i>
						</button> 
					</form>
				</div>
				<?php
				$result .= ob_get_clean();
			} elseif ( 'top' == $item ) {
				$result .= '<a href="#" class="mobile-item" id="scroll-top"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
				++ $cnt;
			} else {
				$result .= '<a href="#" class="mobile-item"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
				++ $cnt;
			}
		}

		if ( $result ) {
			echo '<div class="mobile-icon-bar sticky-content fix-bottom items-' . $cnt . '">' . $result . '</div>';
		}
	}
}

/**
 * Print Alpha Block
 *
 * @param string $block_name The block name to print.
 * @param bool   $is_menu    Determines whether block is menu or not.
 * @since 1.0
 */
if ( ! function_exists( 'alpha_print_template' ) ) :
	function alpha_print_template( $block_name, $is_menu = false ) {
		if ( $block_name && defined( 'ALPHA_CORE_FRAMEWORK_PATH' ) ) {
			$atts = array(
				'name'    => $block_name,
				'is_menu' => $is_menu,
			);
			require alpha_core_framework_path( ALPHA_CORE_FRAMEWORK_PATH . '/widgets/block/render-block.php' );
		}
	}
endif;

/**
 * Check Used Blocks
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_page_blocks' ) ) :
	function alpha_get_page_blocks() {
		global $alpha_layout;
		$used_blocks = array();
		if ( empty( $alpha_layout ) || ! isset( $alpha_layout['header'] ) ) {
			return array();
		}

		if ( ! empty( $alpha_layout['used_blocks'] ) ) {
			return $alpha_layout['used_blocks'];
		}

		// blocks set from layout builder
		$fields = array( 'header', 'footer', 'ptb', 'top_block', 'inner_top_block', 'inner_bottom_block', 'bottom_block', 'top_bar', 'popup', 'error_block' );
		foreach ( $fields as $field ) {
			if ( ! empty( $alpha_layout[ $field ] ) && 'hide' != $alpha_layout[ $field ] ) {
				$used_blocks[] = $alpha_layout[ $field ];
			}
		}

		// Sidebar blocks
		$widgets         = array();
		$sidebar_widgets = get_option( 'sidebars_widgets' );
		if ( isset( $alpha_layout['top_sidebar'] ) && isset( $sidebar_widgets[ $alpha_layout['top_sidebar'] ] ) ) {
			$widgets = array_merge( $widgets, $sidebar_widgets[ $alpha_layout['top_sidebar'] ] );
		}
		if ( isset( $alpha_layout['left_sidebar'] ) && isset( $sidebar_widgets[ $alpha_layout['left_sidebar'] ] ) ) {
			$widgets = array_merge( $widgets, $sidebar_widgets[ $alpha_layout['left_sidebar'] ] );
		}
		if ( isset( $alpha_layout['right_sidebar'] ) && isset( $sidebar_widgets[ $alpha_layout['right_sidebar'] ] ) ) {
			$widgets = array_merge( $widgets, $sidebar_widgets[ $alpha_layout['right_sidebar'] ] );
		}
		if ( ! empty( $widgets ) ) {
			global $wp_registered_widgets;
			foreach ( $widgets as $key ) {
				if ( isset( $wp_registered_widgets[ $key ] ) && isset( $wp_registered_widgets[ $key ]['callback'] ) ) {
					$widget_obj = $wp_registered_widgets[ $key ]['callback'][0];
					if ( ! empty( $widget_obj ) && 'block-widget' == $widget_obj->id_base ) {
						$args = get_option( $widget_obj->option_name );
						if ( ! empty( $args[ $widget_obj->number ] ) ) {
							$used_blocks[] = $args[ $widget_obj->number ]['id'];
						}
					}
				}
			}
		}

		// single product layout builder
		if ( alpha_is_product() && ! empty( $alpha_layout['single_product_block'] ) ) {
			$used_blocks[] = $alpha_layout['single_product_block'];
		}
		// shop builder
		if ( function_exists( 'alpha_is_shop' ) && alpha_is_shop() &&
		! empty( $alpha_layout['shop_block'] ) ) {
			$used_blocks[] = $alpha_layout['shop_block'];
		}
		// Archive builder
		if ( ( is_home() || is_archive() ) && ! empty( $alpha_layout['archive_block'] ) ) {
			$used_blocks[] = $alpha_layout['archive_block'];
		}

		// Single builder
		if ( is_single() && ! empty( $alpha_layout['single_block'] ) ) {
			$used_blocks[] = $alpha_layout['single_block'];
		}

		// blocks in page-content
		if ( is_singular() ) {
			$page_blocks = get_post_meta( get_the_ID(), '_alpha_vc_blocks_content', true );
			if ( ! empty( $page_blocks ) && is_array( $page_blocks ) ) {
				$used_blocks = array_merge( $used_blocks, $page_blocks );
			}
		}

		return array_fill_keys(
			array_unique( $used_blocks, SORT_NUMERIC ),
			array(
				'css' => false,
				'js'  => false,
			)
		);
	}
endif;

/**
 * Print popup template
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_print_popup_template' ) ) {
	function alpha_print_popup_template( $popup_id, $popup_delay ) {
		if ( alpha_get_feature( 'fs_pb_elementor' ) && defined( 'ELEMENTOR_VERSION' ) && ! empty( get_post_meta( $popup_id, '_elementor_data', true ) ) ) {
			$settings = get_post_meta( $popup_id, '_elementor_page_settings', true );
		}

		echo '<div id="alpha-popup-' . $popup_id . '" class="popup" data-popup-options=' . "'" . json_encode(
			array(
				'popup_id'        => $popup_id,
				'popup_delay'     => $popup_delay,
				'popup_animation' => isset( $settings['popup_animation'] ) ? $settings['popup_animation'] : 'fadeIn',
				'popup_duration'  => isset( $settings['popup_anim_duration'] ) ? $settings['popup_anim_duration'] . 'ms' : '400ms',
			)
		) . "'" . ' style="display: none">';

		echo '<div class="alpha-popup-content">';

		alpha_print_template( $popup_id );

		echo '</div>';

		echo '</div>';
	}
}


/**
 * Clear alpha transient
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_clear_transient' ) ) {
	function alpha_clear_transient() {
		global $wpdb;

		$cache_key        = 'alpha-clear-brand-transient';
		$brand_transients = wp_cache_get( $cache_key, 'alpha-transient' );
		$results          = array();
		if ( false == $brand_transients ) {
			$brand_transients = $wpdb->get_results(
				"SELECT 
					AVG(rating) AS rating,
					term_taxonomy_id,
					term_id,
					COUNT(temp1.post_id) AS review_count
				FROM
					(SELECT 
						post_id,
						meta_value AS rating 
					FROM
						$wpdb->postmeta AS pm 
						INNER JOIN $wpdb->posts AS wpp 
						ON pm.post_id = wpp.ID 
					WHERE wpp.post_type = 'product' 
						AND pm.meta_key = '_wc_average_rating' 
					GROUP BY post_id) AS temp1 
					LEFT JOIN 
						(SELECT 
							wptt.term_taxonomy_id,
							term_id,
							wtr.object_id AS post_id 
						FROM
							$wpdb->term_taxonomy AS wptt 
							INNER JOIN $wpdb->term_relationships AS wtr 
								ON wptt.term_taxonomy_id = wtr.term_taxonomy_id 
						WHERE wptt.taxonomy = 'product_brand' 
						GROUP BY post_id) AS temp2 
						ON temp1.post_id = temp2.post_id
				WHERE term_id IS NOT NULL 
				GROUP BY term_id"
			);
			wp_cache_set( $cache_key, $brand_transients, 'alpha-transient', 3600 * 6 );
		}

		if ( is_array( $brand_transients ) && count( $brand_transients ) > 0 ) {
			foreach ( $brand_transients as $transient ) {
				update_term_meta( $transient->term_id, 'review_count', absint( $transient->review_count ) );
				update_term_meta( $transient->term_id, 'rating', abs( round( $transient->rating, 2 ) ) );
			}
		}

		if ( class_exists( 'Alpha_Product_Brand' ) ) {
			delete_transient( 'wc_layered_nav_counts_product_brand' );
		}
	}
}


/**
 * Get list of templates and sidebars
 *
 * @since 1.0
 */
function alpha_get_global_templates_sidebars() {

	global $wp_registered_sidebars;
	$alpha_templates = array();
	$template_types  = array();

	if ( alpha_get_feature( 'fs_builder_block' ) ) {
		$template_types[] = 'block';
	}
	if ( alpha_get_feature( 'fs_builder_header' ) ) {
		$template_types[] = 'header';
	}
	if ( alpha_get_feature( 'fs_builder_footer' ) ) {
		$template_types[] = 'footer';
	}
	if ( alpha_get_feature( 'fs_builder_popup' ) ) {
		$template_types[] = 'popup';
	}
	if ( class_exists( 'WooCommerce' ) && alpha_get_feature( 'fs_plugin_woocommerce' ) && alpha_get_feature( 'fs_builder_singleproduct' ) ) {
		$template_types[] = 'product_layout';
	}
	if ( class_exists( 'WooCommerce' ) && alpha_get_feature( 'fs_plugin_woocommerce' ) && alpha_get_feature( 'fs_builder_shop' ) ) {
		$template_types[] = 'shop_layout';
	}
	if ( class_exists( 'WooCommerce' ) && alpha_get_feature( 'fs_plugin_woocommerce' ) && alpha_get_feature( 'fs_builder_cart' ) ) {
		$template_types[] = 'cart';
	}
	if ( alpha_get_feature( 'fs_builder_sidebar' ) ) {
		$template_types[] = 'sidebar';
	}
	if ( alpha_get_feature( 'fs_builder_single' ) ) {
		$template_types[] = 'single';
	}
	if ( alpha_get_feature( 'fs_builder_archive' ) ) {
		$template_types[] = 'archive';
	}
	if ( class_exists( 'WooCommerce' ) && alpha_get_feature( 'fs_plugin_woocommerce' ) && alpha_get_feature( 'fs_builder_checkout' ) ) {
		$template_types[] = 'checkout';
	}

	// Get Templates
	foreach ( $template_types as $template_type ) {
		$posts = get_posts(
			array(
				'post_type'   => ALPHA_NAME . '_template',
				'meta_key'    => ALPHA_NAME . '_template_type',
				'meta_value'  => $template_type,
				'numberposts' => -1,
			)
		);
		sort( $posts );
		foreach ( $posts as $post ) {
			$alpha_templates[ $template_type ][ $post->ID ] = $post->post_title;
		}
	}

	// Get Sidebars
	$alpha_templates['sidebar'] = array();
	foreach ( $wp_registered_sidebars as $id => $sidebar ) {
		$alpha_templates['sidebar'][ $id ] = $sidebar['name'];
	}

	return $alpha_templates;
}

/**
 * Check file write permission
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_check_file_write_permission' ) ) {
	function alpha_check_file_write_permission( $filename ) {
		if ( is_writable( dirname( $filename ) ) == false ) {
			@chmod( dirname( $filename ), 0755 );
		}
		if ( file_exists( $filename ) ) {
			if ( is_writable( $filename ) == false ) {
				@chmod( $filename, 0755 );
			}
			@unlink( $filename );
		}
	}
}

/**
 * Get search form
 *
 * @since 1.0
 */
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
				'live_search'      => (bool) alpha_get_option( 'live_search' ),
				'search_post_type' => alpha_get_option( 'search_post_type' ),
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
			<a href="#" class="search-toggle">
				<i class="<?php echo esc_attr( ALPHA_ICON_PREFIX . '-icon-search' ); ?>"></i>
				<?php if ( $label ) : ?>
				<span><?php echo esc_html( $label ); ?></span>
				<?php endif; ?>
			</a>
			<?php endif; ?>
	
			<?php if ( 'fullscreen' == $toggle_type ) : ?>
			<div class="search-form-overlay">
				<div class="close-overlay"></div>
				<div class="search-form-wrapper">
				<div class="search-inner-wrapper">
					<div class="search-form">
			<?php endif; ?>
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
	
			</form>
			<?php if ( 'fullscreen' == $toggle_type ) : ?>
						<div class="search-header">
							<?php esc_html_e( 'Hit enter to search or ESC to close', 'alpha' ); ?>
							<div class="hs-close">
								<a href="#">
									<span class="close-wrap">
										<span class="close-line close-line1"></span>
										<span class="close-line close-line2"></span>
									</span>
								</a>
							</div>
						</div>
					</div>
					<div class="search-container">
						<div class="scrollable">
							<div class="search-results">
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
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

if ( ! function_exists( 'alpha_get_builder_status' ) ) {
	/**
	 * Check builder status
	 *
	 * @since 1.2.0
	 * @param string $builder_id
	 * @return boolean enable or disable
	 */
	function alpha_get_builder_status( $builder_key ) {
		if ( defined( 'ALPHA_CORE_VERSION' ) ) {
			$rc_template_builders = get_theme_mod( 'resource_template_builders' );
			$builders_array       = json_decode( wp_unslash( empty( $rc_template_builders ) ? '' : $rc_template_builders ), true );
			if ( ! empty( $builders_array[ $builder_key ] ) && 'disable' == $builders_array[ $builder_key ] ) {
				return false;
			}
			return true;
		}
		return false;
	}
}
