<?php
/**
 * A simple loader to render admin views with a consistent layout.
 */
namespace Desymphony\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DS_Admin_View_Loader {

	/**
	 * Renders a page within the admin layout.
	 *
	 * @param string $view The name of the view file (without 'page-' prefix).
	 * @param array  $data Optional data array to pass to the view.
	 */
	public static function render( string $view, array $data = [] ): void {
		// Extract data as variables for convenience: $someVar, $anotherVar, etc.
		extract( $data );

		// The layout includes header, sidebar, footer, and the specified page.
		$layout_path = DS_THEME_DIR . '/inc/admin/views/layouts/admin-layout.php';

		if ( file_exists( $layout_path ) ) {
			// We'll pass $view so layout can include "page-{$view}.php"
			include $layout_path;
		} else {
			// Fallback: no layout found
			wp_die( __( 'Admin layout file missing.', DS_THEME_TEXTDOMAIN ) );
		}
	}
}
