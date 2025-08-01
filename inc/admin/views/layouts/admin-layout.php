<?php
/**
 * Main layout that includes the header, notifications, sidebar, content, and footer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// We'll prefix $view with "page-" to locate the file.
$target_view = "page-{$view}.php";
?>

<div class="wrap">

	<?php
	// Header
	require_once __DIR__ . '/../partials/admin-header.php';

	// Notifications
	require_once __DIR__ . '/../partials/admin-notifications.php';

	// Sidebar
	require_once __DIR__ . '/../partials/admin-sidebar.php';
	?>

	<div class="ds-admin-content">
		<?php
		// Content area: include the requested page
		$page_path = __DIR__ . '/../pages/' . $target_view;
		if ( file_exists( $page_path ) ) {
			require_once $page_path;
		} else {
			echo '<p>' . esc_html__( 'Page not found.', DS_THEME_TEXTDOMAIN ) . '</p>';
		}
		?>
	</div><!-- .ds-admin-content -->

	<?php
	// Footer
	require_once __DIR__ . '/../partials/admin-footer.php';
	?>

</div><!-- .wrap -->
