<?php
/**
 * History of theme
 *
 * Here, you can add or remove whats new content and change log.
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

if ( empty( $history_type ) ) {
	return;
}

// What's New Section
if ( 'whatsnew' == $history_type ) {
	?>
	<div class="alpha-whatsnew-item">
		<h3 class="alpha-item-title"><?php printf( esc_html__( 'Step into WordPress %1$s5.7.1%2$s', 'alpha' ), '<span class="text-primary">', '</span>' ); ?></h3>
		<p class="alpha-item-desc">
		<?php
		esc_html_e(
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore ctetur adipis
magna aliqua. Venenatis tellus in metus vulputate eu scelerisque felis. Vel pretium lectus quam id leo in vitae us in metus vulpu
turpis massa. Nunc id cursus metus aliquam. Libero id faucibus nisl tincidunt eget. Aliquam id diam maecenas ero id fauci
ultricies mi eget mauris.',
			'alpha'
		);
		?>
		</p>
	</div>
	<div class="alpha-whatsnew-item">
		<h4 class="alpha-item-title"><?php esc_html_e( 'Maintenance and Security Releases', 'alpha' ); ?></h4>
		<p class="alpha-item-desc">
		<?php
		printf(
			esc_html__(
				'Version 5.7.1 addressed some security issues and fixed 26 bugs. For more information, see %1$sthe release notes%2$s.',
				'alpha'
			),
			'<a href="#">',
			'</a>'
		);
		?>
		</p>
	</div>
	<div class="alpha-whatsnew-item">
		<h4 class="alpha-item-title"><?php esc_html_e( 'Maintenance and Security Releases', 'alpha' ); ?></h4>
		<p class="alpha-item-desc">
		<?php
		printf(
			esc_html__(
				'Version 5.7.1 addressed some security issues and fixed 26 bugs. For more information, see %1$sthe release notes%2$s.',
				'alpha'
			),
			'<a href="#">',
			'</a>'
		);
		?>
		</p>
	</div>
	<?php
} elseif ( 'changelog' == $history_type ) {
	?>
	<div class="alpha-changelog" id="log1">
		<h4 class="alpha-release-version"><?php echo esc_html__( 'Version 1.0.3 (29th May 2023)', 'alpha' ); ?></h4>
		<h5 class="alpha-log-title"><i class="fas fa-star"></i><?php echo esc_html__( 'Added', 'alpha' ); ?></h5>
		<ul>
			<li><?php esc_html_e( 'Reveal entrance animation.', 'alpha' ); ?></li>
			<li><?php esc_html_e( '200+ templates in Alpus Studio.', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Email subscription popup in all demos.', 'alpha' ); ?></li>
		</ul>
		<h5 class="alpha-log-title"><i class="fas fa-bug"></i><?php echo esc_html__( 'Fixed', 'alpha' ); ?></h5>
		<ul>
			<li><?php esc_html_e( 'Studio template importing issue that contains forms.', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Popup does not appear in side header layout.', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Svg alignment option in counters widget.', 'alpha' ); ?></li>
		</ul>
		<h4 class="alpha-release-version" style="margin-top: 40px"><?php echo esc_html__( 'Version 1.0.2 (15th May 2023)', 'alpha' ); ?></h4>
		<h5 class="alpha-log-title"><i class="fas fa-star"></i><?php echo esc_html__( 'Added', 'alpha' ); ?></h5>
		<ul>
			<li><?php esc_html_e( 'Typography option in single builder\'s tag widget.', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Layout builder popup at first save of Alpus template.', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Compatibility with Elementor v.3.13.x.', 'alpha' ); ?></li>
		</ul>
		<h5 class="alpha-log-title"><i class="fas fa-undo-alt"></i><?php esc_html_e( 'Updated', 'alpha' ); ?></h5>
		<ul>
			<li><?php esc_html_e( 'Transparent header option does not working when sticky option is not active.', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Top bar Alpus dropdown when user logged in.', 'alpha' ); ?></li>
		</ul>
		<h5 class="alpha-log-title"><i class="fas fa-bug"></i><?php echo esc_html__( 'Fixed', 'alpha' ); ?></h5>
		<ul>
			<li><?php esc_html_e( 'Menu dropdown box shadow option in menu widget.', 'alpha' );  ?></li>
		</ul>
		<h4 class="alpha-release-version" style="margin-top: 40px"><?php echo esc_html__( 'Version 1.0.1 (28th Apr 2023)', 'alpha' ); ?></h4>
		<h5 class="alpha-log-title"><i class="fas fa-star"></i><?php echo esc_html__( 'Added', 'alpha' ); ?></h5>
		<ul>
			<li><?php esc_html_e( 'Empty cart, wishlist, compare dropdown style.', 'alpha' ); ?></li>
		</ul>
		<h5 class="alpha-log-title"><i class="fas fa-undo-alt"></i><?php esc_html_e( 'Updated', 'alpha' ); ?></h5>
		<ul>
			<li><?php esc_html_e( 'Tab font style option in section tab element.', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Studio popup redesign in elementor editor.', 'alpha' ); ?></li>
		</ul>
		<h5 class="alpha-log-title"><i class="fas fa-bug"></i><?php echo esc_html__( 'Fixed', 'alpha' ); ?></h5>
		<ul>
			<li><?php esc_html_e( 'Live search list style issue in dark mode.', 'alpha' );  ?></li>
			<li><?php esc_html_e( 'Search result style broken in full screen search type.', 'alpha' );  ?></li>
			<li><?php esc_html_e( 'Duplex, ribbon effect for elementor sections.', 'alpha' );  ?></li>
		</ul>
		<h4 class="alpha-release-version" style="margin-top: 40px"><?php esc_html_e( 'Version 1.0.0 (25th Apr 2023)', 'alpha' ); ?></h4>
		<h5 class="alpha-log-title"><i class="fas fa-star"></i><?php esc_html_e( 'Initial Release', 'alpha' ); ?></h5>
		<ul>
			<li>We launched on 25th April, 2023</li>
		</ul>
	</div>
	<?php
}
