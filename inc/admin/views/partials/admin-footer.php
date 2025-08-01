<?php
/**
 * Admin footer partial for plugin pages.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<hr/>
<div class="">
	<em>
		<?php
		/* translators: %s is plugin name */
		printf( esc_html__( 'Thank you for using %s!', DS_THEME_TEXTDOMAIN ), 'WV Addon' );
		?>
	</em>
</div>
