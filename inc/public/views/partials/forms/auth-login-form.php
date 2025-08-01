<?php
/**
 * Minimal login form markup. 
 * Rely on wv-addon-public.js for the AJAX submission.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="wv-login-form">
	<div id="wv-login-messages"></div>
	<form id="wv-addon-login-form" action="#" method="post">
		<div class="wv-input-group">
			<label for="wv_login_email"><?php esc_html_e( 'Email', 'wv-addon' ); ?></label>
			<input type="email" name="email" id="wv_login_email" required />
		</div>

		<div class="wv-input-group">
			<label for="wv_login_password"><?php esc_html_e( 'Password', 'wv-addon' ); ?></label>
			<input type="password" name="password" id="wv_login_password" required />
		</div>

		<div class="wv-input-group">
			<label>
				<input type="checkbox" name="remember_me" />
				<?php esc_html_e( 'Remember me', 'wv-addon' ); ?>
			</label>
		</div>

		<button type="submit" class="wv-btn-primary">
			<?php esc_html_e( 'Log In', 'wv-addon' ); ?>
		</button>
	</form>
</div>
