<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="wv-password-reset-wrapper">
	<div id="wv-password-reset-messages"></div>
	<form id="wv-password-reset-form" method="post">
		<?php wp_nonce_field( 'DS_password_reset_nonce', 'DS_password_reset_nonce_field' ); ?>
		<label for="wv_reset_email"><?php esc_html_e( 'Email:', 'wv-addon' ); ?></label>
		<input type="email" name="email" id="wv_reset_email" required>
		<button type="submit"><?php esc_html_e( 'Reset Password', 'wv-addon' ); ?></button>
	</form>
</div>
