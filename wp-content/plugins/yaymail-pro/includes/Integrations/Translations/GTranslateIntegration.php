<?php

namespace YayMail\Integrations\Translations;

defined( 'ABSPATH' ) || exit;

class GTranslateIntegration extends BaseIntegration {
	public static function get_integration_plugin() {
		return 'gtranslate';
	}
	public static function before_initialize() {
	}
	public static function get_available_languages() {
		$gt_lang_array_json = '{"af":"Afrikaans","sq":"Albanian","am":"Amharic","ar":"Arabic","hy":"Armenian","az":"Azerbaijani","eu":"Basque","be":"Belarusian","bn":"Bengali","bs":"Bosnian","bg":"Bulgarian","ca":"Catalan","ceb":"Cebuano","ny":"Chichewa","zh-CN":"Chinese (Simplified)","zh-TW":"Chinese (Traditional)","co":"Corsican","hr":"Croatian","cs":"Czech","da":"Danish","nl":"Dutch","en":"English","eo":"Esperanto","et":"Estonian","tl":"Filipino","fi":"Finnish","fr":"French","fy":"Frisian","gl":"Galician","ka":"Georgian","de":"German","el":"Greek","gu":"Gujarati","ht":"Haitian Creole","ha":"Hausa","haw":"Hawaiian","iw":"Hebrew","hi":"Hindi","hmn":"Hmong","hu":"Hungarian","is":"Icelandic","ig":"Igbo","id":"Indonesian","ga":"Irish","it":"Italian","ja":"Japanese","jw":"Javanese","kn":"Kannada","kk":"Kazakh","km":"Khmer","ko":"Korean","ku":"Kurdish (Kurmanji)","ky":"Kyrgyz","lo":"Lao","la":"Latin","lv":"Latvian","lt":"Lithuanian","lb":"Luxembourgish","mk":"Macedonian","mg":"Malagasy","ms":"Malay","ml":"Malayalam","mt":"Maltese","mi":"Maori","mr":"Marathi","mn":"Mongolian","my":"Myanmar (Burmese)","ne":"Nepali","no":"Norwegian","ps":"Pashto","fa":"Persian","pl":"Polish","pt":"Portuguese","pa":"Punjabi","ro":"Romanian","ru":"Russian","sm":"Samoan","gd":"Scottish Gaelic","sr":"Serbian","st":"Sesotho","sn":"Shona","sd":"Sindhi","si":"Sinhala","sk":"Slovak","sl":"Slovenian","so":"Somali","es":"Spanish","su":"Sundanese","sw":"Swahili","sv":"Swedish","tg":"Tajik","ta":"Tamil","te":"Telugu","th":"Thai","tr":"Turkish","uk":"Ukrainian","ur":"Urdu","uz":"Uzbek","vi":"Vietnamese","cy":"Welsh","xh":"Xhosa","yi":"Yiddish","yo":"Yoruba","zu":"Zulu"}';
		$gt_lang_array      = get_object_vars( json_decode( $gt_lang_array_json ) );
		$data               = get_option( 'GTranslate' );
		\GTranslate::load_defaults( $data );
		$fincl_langs           = isset( $data['fincl_langs'] ) ? $data['fincl_langs'] : array();
		$gtranslate_plugin_url = preg_replace( '/^https?:/i', '', plugins_url() . '/gtranslate' );
		$languages             = array_map(
			function( $lang ) use ( $gt_lang_array, $gtranslate_plugin_url ) {
				$flag_path = "{$gtranslate_plugin_url}/flags/svg/";
				if ( 'en-us' === $lang ) {
					$flag = "{$flag_path}en-us.svg";
				} elseif ( 'en-ca' === $lang ) {
					$flag = "{$flag_path}en-ca.svg";
				} elseif ( 'pt-br' === $lang ) {
					$flag = "{$flag_path}pt-br.svg";
				} elseif ( 'es-mx' === $lang ) {
					$flag = "{$flag_path}es-mx.svg";
				} elseif ( 'es-ar' === $lang ) {
					$flag = "{$flag_path}es-ar.svg";
				} elseif ( 'es-co' === $lang ) {
					$flag = "{$flag_path}es-co.svg";
				} elseif ( 'fr-qc' === $lang ) {
					$flag = "{$flag_path}fr-qc.svg";
				} else {
					$flag = "{$flag_path}{$lang}.svg";
				}
				return array(
					'code' => $lang,
					'name' => isset( $gt_lang_array[ $lang ] ) ? $gt_lang_array[ $lang ] : 'Unknown',
					'flag' => $flag,
				);
			},
			$fincl_langs
		);

		return $languages;
	}
	public static function get_site_language( $order ) {
		if ( isset( $_SERVER['HTTP_X_GT_LANG'] ) ) {
			return sanitize_text_field( $_SERVER['HTTP_X_GT_LANG'] );
		}
		$language = 'en';
		if ( isset( $_COOKIE['googtrans'] ) ) {
			$googtrans = sanitize_text_field( $_COOKIE['googtrans'] );
			$googtrans = explode( '/', $googtrans );
			$language  = $googtrans[ count( $googtrans ) - 1 ];
		}
		return $language;
	}
}
