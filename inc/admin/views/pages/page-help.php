<?php
/**
 * Content for the Help page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2><?php esc_html_e( 'Help & Documentation', 'wv-addon' ); ?></h2>
<p><?php esc_html_e( 'Find useful tips, FAQs, and documentation links here.', 'wv-addon' ); ?></p>

<ul>
    <li>
        <a href="https://example.com/help" target="_blank">
            <?php esc_html_e( 'Official Documentation', 'wv-addon' ); ?>
        </a>
    </li>
    <li>
        <a href="mailto:support@example.com">
            <?php esc_html_e( 'Email Support', 'wv-addon' ); ?>
        </a>
    </li>
</ul>
