<?php
/**
 * Alpha Customizer
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 *
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Alpha_Customizer_Pro_Section
 *
 * @since 1.0
 */
if ( ! class_exists( 'Alpha_Customizer_Pro_Section' ) ) {

	/**
	 * Alpha_Customizer_Pro_Section Initial setup
	 */
	class Alpha_Customizer_Pro_Section extends WP_Customize_Section {

		/**
		 * The type of customize section being rendered.
		 *
		 * @since  1.0
		 * @access public
		 * @var    string
		 */
		public $type = 'alpha-pro-section';

		/**
		 * Custom pro button URL.
		 *
		 * @since  1.0
		 * @access public
		 * @var    string
		 */
		public $pro_url = '';

		/**
		 * Add custom parameters to pass to the JS via JSON.
		 *
		 * @since  1.0
		 * @access public
		 * @return string
		 */
		public function json() {
			$json            = parent::json();
			$json['pro_url'] = esc_url_raw( $this->pro_url );
			return $json;
		}

		/**
		 * Outputs the Underscore.js template.
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 */
		protected function render_template() {
			?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }} cannot-expand control-section-default">
			<h3 class="accordion-section-title">
				<# if ( data.title && data.pro_url ) { #>
				<a href="{{ data.pro_url }}" class="wp-ui-text-highlight" target="_blank" rel="noopener">{{ data.title }}</a>
				<# } #>
			</h3>
		</li>
			<?php
		}
	}

}
