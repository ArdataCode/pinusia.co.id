<?php
/**
 * The License Template
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;
global $wp_filesystem;
// Initialize the WordPress filesystem, no more using file_put_contents function
if ( empty( $wp_filesystem ) ) {
	require_once ABSPATH . '/wp-admin/includes/file.php';
	WP_Filesystem();
}

$data = array(
	'wp_uploads'     => wp_get_upload_dir(),
	'memory_limit'   => wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) ),
	'time_limit'     => ini_get( 'max_execution_time' ),
	'max_input_vars' => ini_get( 'max_input_vars' ),
);

$status = array(
	'uploads'        => wp_is_writable( $data['wp_uploads']['basedir'] ),
	'fs'             => ( $wp_filesystem || WP_Filesystem() ) ? true : false,
	'zip'            => class_exists( 'ZipArchive' ),
	'suhosin'        => extension_loaded( 'suhosin' ),
	'memory_limit'   => $data['memory_limit'] >= 268435456,
	'time_limit'     => ( ( $data['time_limit'] >= 600 ) || ( 0 == $data['time_limit'] ) ) ? true : false,
	'max_input_vars' => $data['max_input_vars'] >= 2000,
);

?>
	<div class="alpha-admin-panel-body">
		<div class="row alpha-theme-features">
			<?php
			foreach ( $admin_config['links'] as $key => $item ) {
				if ( 'buynow' === $key ) {
					continue;
				}
				$url   = isset( $item['url'] ) ? $item['url'] : '#';
				$label = isset( $item['label'] ) ? $item['label'] : '';
				$icon  = isset( $item['icon'] ) ? $item['icon'] : 'fas fa-th-large';
				$desc  = isset( $item['desc'] ) ? $item['desc'] : '';
				?>
				<div class="alpha-theme-feature-wrapper">
					<div class="alpha-theme-feature">
						<a href="<?php echo esc_url( $url ); ?>">
							<div class="alpha-theme-feature-icon"><?php echo alpha_escaped( $icon ); ?></div>
							<div class="alpha-theme-feautre-content">
								<h3><?php echo alpha_escaped( $label ); ?></h3>
								<p><?php echo alpha_escaped( $desc ); ?></p>
							</div>
						</a>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
<div class="alpha-info-section">
	<div class="alpha-section-left alpha-info">
		<div class="alpha-status-wrapper">
			<h3 class="alpha-section-title"><?php esc_html_e( 'System Info', 'alpha' ); ?></h3>
			<ul class="alpha-system-status">
				<li>
					<?php if ( $status['uploads'] ) : ?>
						<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-check-solid'; ?>"></i>
					<?php else : ?>
						<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-times-solid error'; ?>"></i>
					<?php endif; ?>

					<span class="label"><?php esc_html_e( 'Uploads folder writable', 'alpha' ); ?></span>

					<?php if ( ! $status['uploads'] ) : ?>
					<p class="status-notice status-error"><?php esc_html_e( 'Uploads folder must be writable. Please set write permission to your wp-content/uploads folder.', 'alpha' ); ?></p>
					<?php endif; ?>
				</li>

				<li>
					<?php if ( $status['fs'] ) : ?>
						<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-check-solid'; ?>"></i>
					<?php else : ?>
						<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-times-solid error'; ?>"></i>
					<?php endif; ?>

					<span class="label"><?php esc_html_e( 'WP File System', 'alpha' ); ?></span>

					<?php if ( ! $status['fs'] ) : ?>
						<p class="status-notice status-error"><?php esc_html_e( 'File System access is required for pre-built websites and plugins installation. Please contact your hosting provider.', 'alpha' ); ?></p>
					<?php endif; ?>

				</li>

				<li>
					<?php if ( $status['zip'] ) : ?>
						<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-check-solid'; ?>"></i>
					<?php else : ?>
						<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-times-solid error'; ?>"></i>
					<?php endif; ?>

					<span class="label"><?php esc_html_e( 'ZipArchive', 'alpha' ); ?></span>

					<?php if ( ! $status['zip'] ) : ?>
						<p class="status-notice status-error"><?php esc_html_e( 'ZipArchive is required for pre-built websites and plugins installation. Please contact your hosting provider.', 'alpha' ); ?></p>
					<?php endif; ?>
				</li>

				<?php if ( $status['suhosin'] ) : ?>

					<li>
						<span class="status step-id status-info"></span>
						<span class="label"><?php esc_html_e( 'SUHOSIN Installed', 'alpha' ); ?></span>
						<p class="status-notice"><?php esc_html_e( 'Suhosin may need to be configured to increase its data submission limits.', 'alpha' ); ?></p>
					</li>

				<?php else : ?>

					<li>
						<?php if ( $status['memory_limit'] ) : ?>
							<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-check-solid'; ?>"></i>
						<?php else : ?>
							<?php if ( $data['memory_limit'] < 134217728 ) : ?>
								<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-times-solid error'; ?>"></i>
							<?php else : ?>
								<span class="status step-id status-info"></span>
							<?php endif; ?>
						<?php endif; ?>

						<span class="label"><?php esc_html_e( 'PHP Memory Limit', 'alpha' ); ?></span>

						<?php if ( $status['memory_limit'] ) : ?>
							<span class="desc">(<?php echo size_format( $data['memory_limit'] ); ?>)</span>
						<?php else : ?>
							<?php if ( $data['memory_limit'] < 134217728 ) : ?>
								<span class="desc">(<?php echo size_format( $data['memory_limit'] ); ?>)</span>
								<?php /* translators: opening and closing strong tag */ ?>
								<p class="status-notice status-error"><?php printf( esc_html__( 'Minimum %1$s128 MB%2$s is required, %1$s256 MB%2$s is recommended.', 'alpha' ), '<strong>', '</strong>' ); ?></p>
							<?php else : ?>
								<span class="desc">(<?php echo size_format( $data['memory_limit'] ); ?>)</span>
								<?php /* translators: opening and closing strong tag */ ?>
								<p class="status-notice status-error"><?php printf( esc_html__( 'Current memory limit is OK, however %1$s256 MB%2$s is recommended.', 'alpha' ), '<strong>', '</strong>' ); ?></p>
							<?php endif; ?>
						<?php endif; ?>

					</li>

					<li>
						<?php if ( $status['time_limit'] ) : ?>
							<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-check-solid'; ?>"></i>
						<?php else : ?>
							<?php if ( $data['time_limit'] < 300 ) : ?>
								<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-times-solid error'; ?>"></i>
							<?php else : ?>
								<i class="fa fa-info"></i>
							<?php endif; ?>
						<?php endif; ?>

						<span class="label"><?php esc_html_e( 'PHP max_execution_time', 'alpha' ); ?></span>

						<?php if ( $status['time_limit'] ) : ?>
							<span class="desc">(<?php echo esc_html( $data['time_limit'] ); ?>)</span>
						<?php else : ?>
							<?php if ( $data['time_limit'] < 300 ) : ?>
								<span class="desc">(<?php echo esc_html( $data['time_limit'] ); ?>)</span>
								<?php /* translators: opening and closing strong tag */ ?>
								<p class="status-notice status-error"><?php printf( esc_html__( 'Minimum %1$s300%2$s is required, %1$s600%2$s is recommended.', 'alpha' ), '<strong>', '</strong>' ); ?></p>
							<?php else : ?>
								<span class="desc">(<?php echo esc_html( $data['time_limit'] ); ?>)</span>
								<?php /* translators: opening and closing strong tag */ ?>
								<p class="status-notice status-error"><?php printf( esc_html__( 'Current time limit is OK, however %1$s600%2$s is recommended.', 'alpha' ), '<strong>', '</strong>' ); ?></p>
							<?php endif; ?>
						<?php endif; ?>

					</li>

					<li>
						<?php if ( $status['max_input_vars'] ) : ?>
							<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-check-solid'; ?>"></i>
						<?php else : ?>
							<i class="<?php echo ALPHA_ICON_PREFIX . '-icon-times-solid error'; ?>"></i>
						<?php endif; ?>

						<span class="label"><?php esc_html_e( 'PHP max_input_vars', 'alpha' ); ?></span>

						<?php if ( $status['max_input_vars'] ) : ?>
							<span class="desc">(<?php echo esc_html( $data['max_input_vars'] ); ?>)</span>
						<?php else : ?>
							<span class="desc">(<?php echo esc_html( $data['max_input_vars'] ); ?>)</span>
							<?php /* translators: opening and closing strong tag */ ?>
							<p class="status-notice status-error"><?php printf( esc_html__( 'Minimum %1$s2000%2$s is required', 'alpha' ), '<strong>', '</strong>' ); ?></p>
						<?php endif; ?>
					</li>

				<?php endif; ?>

			</ul>
		</div>
	</div>
	<div class="alpha-section-right">
		<div class="alpha-changelog-section">
			<h3 class="alpha-section-title"><?php esc_html_e( 'Change Log', 'alpha' ); ?></h3>
			<div class="alpha-changelog-wrapper">
				<div class="alpha-changelogs scrollable">
					<?php
						$history_type = 'changelog';
						require ALPHA_PATH . '/inc/history.php';
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- <div class="plugins-section">
	<h3 class="alpha-section-title"><?php esc_html_e( 'Integrated Plugins', 'alpha' ); ?></h3>
	
	<div class="row alpha-integrated-plugins">
		<div class="alpha-integrated-plugins-wrapper">
			<div class="alpha-integrated-plugin">
				<div class="alpha-plugin-icon">
					<a href="#"><i class="admin-svg-hubspot" style="width:118px;"></i></a>
				</div>
				<div class="alpha-plugin-content">
					<h3><?php esc_html_e( 'CRM, Marketing & Sales', 'alpha' ); ?></h3>
					<p><?php esc_html_e( 'This offers a full stack of software for marketing, sales and customer service. Powerful alone, Better together.', 'alpha' ); ?></p>
					<a href="#" class="btn btn-dark"><?php esc_html_e( 'WP Marketing', 'alpha' ); ?></a>
				</div>
			</div>
		</div>
		<div class="alpha-integrated-plugins-wrapper">
			<div class="alpha-integrated-plugin">
				<div class="alpha-plugin-icon">
					<a href="#"><i class="admin-svg-wpml" style="width:111px;"></i></a>
				</div>
				<div class="alpha-plugin-content">
					<h3><?php esc_html_e( 'Multilingual Sites', 'alpha' ); ?></h3>
					<p><?php printf( esc_html__( 'This is the most popular and versatile multilingual plugin available on the market, works perfectly with %s.', 'alpha' ), ALPHA_DISPLAY_NAME ); ?></p>
					<a href="#" class="btn btn-dark"><?php esc_html_e( 'WP Multilingual', 'alpha' ); ?></a>
				</div>
			</div>
		</div>
		<div class="alpha-integrated-plugins-wrapper">
			<div class="alpha-integrated-plugin">
				<div class="alpha-plugin-icon">
					<a href="#"><i class="admin-svg-calendar" style="width:98px;"></i></a>
				</div>
				<div class="alpha-plugin-content">
					<h3><?php esc_html_e( 'The Events Calendar', 'alpha' ); ?></h3>
					<p><?php esc_html_e( 'This is a crafted plugin to create and manage events on your website with ease. Extensible and easy to use.', 'alpha' ); ?></p>
					<a href="#" class="btn btn-dark"><?php esc_html_e( 'WP Events Calendar', 'alpha' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="alpha-others-section">
	<h3 class="alpha-section-title"><?php esc_html_e( 'Recommended Themes', 'alpha' ); ?></h3>
	<div class="swiper nav-top">
		<div class="swiper-container">
			<div class="alpha-products swiper-wrapper">
				<?php
				foreach ( $admin_config['other_products'] as $key => $item ) {
					if ( '#' != $item['url'] ) {
						?>
						<a href="<?php echo esc_url( $item['url'] ); ?>" class="alpha-product">
							<img src="<?php echo esc_url( $item['image'] ); ?>" width="385" height="250" alt="<?php echo esc_attr( $item['name'] ); ?>" />
							<label><?php echo esc_html( $item['name'] ); ?></label>
						</a>
						<?php
					} else {
						?>
						<div class="alpha-product coming-soon">
							<img src="<?php echo esc_url( $item['image'] ); ?>" data-image-src="<?php echo esc_attr( $item['image'] ); ?>" width="385" height="250" alt="<?php echo esc_attr( $item['name'] ); ?>" />
						</div>
						<?php
					}
				}
				?>
			</div>
				<span class="swiper-button-prev"><i class="<?php echo ALPHA_ICON_PREFIX . '-icon-long-arrow-left'; ?>"></i></span>
				<span class="swiper-button-next"><i class="<?php echo ALPHA_ICON_PREFIX . '-icon-long-arrow-right'; ?>"></i></span>
		</div>
	</div>
</div> -->