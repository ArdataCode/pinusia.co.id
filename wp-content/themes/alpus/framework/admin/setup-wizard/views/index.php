<?php
/**
 * Index panel
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

$step_number  = 1;
$output_steps = $this->steps;
?>
	<ul class="alpha-admin-panel-steps">
		<?php foreach ( $output_steps as $step_key => $step ) : ?>
			<?php
			$show_link        = true;
			$li_class_escaped = '';
			if ( $step_key === $this->step ) {
				$li_class_escaped = 'active';
			} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
				$li_class_escaped = 'done';
			}
			if ( $step_key === $this->step ) {
				$show_link = false;
			}
			?>
			<li class="<?php echo esc_attr( $li_class_escaped ); ?>">
				<?php
				if ( $show_link ) {
					echo '<a href="' . esc_url( $this->get_step_link( $step_key ) ) . '">' . '<span>' . sprintf( '%02d. ', $step_number ) . '</span>' . alpha_escaped( $step['name'] ) . '</a>';
				} else {
					echo '<span>' . sprintf( '%02d. ', $step_number ) . '</span>' . alpha_escaped( $step['name'] );
				}
				?>
			</li>
			<?php $step_number++; ?>
		<?php endforeach; ?>
	</ul>
	<div class="alpha-admin-panel-body alpha-wizard-content alpha-setup-<?php echo esc_attr( str_replace( '_', '-', $this->step ) ); ?>">
		<?php $this->view_step(); ?>
	</div>
