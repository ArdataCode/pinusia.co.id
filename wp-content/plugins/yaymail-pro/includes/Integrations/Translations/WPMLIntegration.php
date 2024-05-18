<?php

namespace YayMail\Integrations\Translations;

defined( 'ABSPATH' ) || exit;

class WPMLIntegration extends BaseIntegration {
	public static function before_initialize() {
		self::turn_off_post_type_translation();
	}
	public static function get_integration_plugin() {
		return 'wpml';
	}
	public static function turn_off_post_type_translation() {
		global $sitepress_settings, $sitepress;
		$custom_posts_sync                     = $sitepress_settings['custom_posts_sync_option'];
		$custom_posts_sync['yaymail_template'] = 0;
		$sitepress->set_setting( 'custom_posts_sync_option', $custom_posts_sync, true );
	}
	public static function get_available_languages() {
		$languages = array();
		if ( function_exists( 'icl_get_languages' ) ) {
			foreach ( icl_get_languages() as $key => $lang ) {
				$languages[] = array(
					'code' => $lang['code'],
					'name' => isset( $lang['translated_name'] ) ? $lang['translated_name'] : $lang['display_name'],
					'flag' => isset( $lang['country_flag_url'] ) ? $lang['country_flag_url'] : '',
				);
			}
		}

		return $languages;
	}
	public static function get_site_language( $order ) {
		$language = \defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'en';
		if ( null !== $order ) {
			$order_language = get_post_meta( $order->get_id(), 'wpml_language', true );
			if ( $order_language ) {
				$language = $order_language;
			}
		}
		return $language;
	}
}
