<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$confirm_status = isset($_GET['wv_confirm_status']) ? sanitize_text_field($_GET['wv_confirm_status']) : '';
?>
<div id="wv-email-confirmation-wrapper">
	<?php if ( $confirm_status === 'success' ) : ?>
		<h2><?php esc_html_e( 'Email Confirmed Successfully!', 'wv-addon' ); ?></h2>
		<p><?php esc_html_e( 'You can now log in.', 'wv-addon' ); ?></p>
	<?php elseif ( $confirm_status === 'failed' ) : ?>
		<h2><?php esc_html_e( 'Email Confirmation Failed', 'wv-addon' ); ?></h2>
		<p><?php esc_html_e( 'Invalid link or token.', 'wv-addon' ); ?></p>
	<?php else : ?>
		<h2><?php esc_html_e( 'Check Your Email for Confirmation Link', 'wv-addon' ); ?></h2>
	<?php endif; ?>
</div>
