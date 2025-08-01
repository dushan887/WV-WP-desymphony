<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="wv-2fa-wrapper">
	<div id="wv-2fa-messages"></div>
	<form id="wv-2fa-form" method="post">
		<?php wp_nonce_field( 'wv_2fa_nonce', 'nonce', true, true ); ?>
		<label for="wv_2fa_code"><?php esc_html_e( 'Enter 2FA Code:', 'wv-addon' ); ?></label>
		<input type="text" name="code" id="wv_2fa_code" required>
		<button type="submit"><?php esc_html_e( 'Verify Code', 'wv-addon' ); ?></button>
	</form>
</div>
