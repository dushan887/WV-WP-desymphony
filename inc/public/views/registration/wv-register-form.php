<?php
if (!defined('ABSPATH')) {
    exit;
}

// We already have $steps_config and $current_step passed in
if ( ! isset($steps_config[$current_step]) ) {
    $current_step = '1';
}
$step_config = $steps_config[$current_step];
$step_id     = basename($step_config['file'], '.php');

?>
<div id="wv-register-wrapper" class="wv-auth-wrapper position-relative">
	<div id="wv-register-form" class="wv-auth-form d-block">
		<form method="post" action="">
			<?php wp_nonce_field( 'wv_register_nonce', 'security' ); ?>
			<?php if ( ! empty( $_GET['coex_token'] ) ): ?>
			<input type="hidden" name="coex_token" value="<?php echo esc_attr( sanitize_text_field( $_GET['coex_token'] ) ); ?>">
			<?php endif; ?>
			<input type="hidden" name="current_step" value="<?php echo esc_attr($current_step); ?>">
			
			<div id="ds-step-debug" class="container px-0 d-none">
				<div class="alert alert-info">
					<?php echo esc_attr($step_id); ?>
				</div>
			</div>
			
			<div id="wv-reg-steps-container" class="wv-auth-container container my-24 my-lg-48 mx-auto br-16 px-lg-0" data-current-step="<?php echo esc_attr($step_id); ?>">
				
				<div class="wv-reg-step" id="<?php echo esc_attr($step_id); ?>">
					<?php
					if ( file_exists($step_config['file']) ) {
						include $step_config['file'];
					} else {
						echo '<p class="wv-error">Error: Step file not found.</p>';
					}
					?>
				</div>
				<?php
				// Render next/prev/submit buttons
				echo $GLOBALS['wv_register_controller']->render_navigation_buttons($current_step);
				?>
			</div>
		</form>
	</div>
</div>

