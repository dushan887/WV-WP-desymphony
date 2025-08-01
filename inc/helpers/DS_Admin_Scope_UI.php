<?php
/**
 * Adds the “Admin scope” select to the back‑office user‐edit form.
 * File: inc/helpers/DS_Admin_Scope_UI.php
 */
namespace Desymphony\Helpers;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Render select under "Role" ------------------------------------------------ */
add_action( 'edit_user_profile', __NAMESPACE__ . '\\render_admin_scope_field' );
add_action( 'show_user_profile', __NAMESPACE__ . '\\render_admin_scope_field' );

function render_admin_scope_field( \WP_User $user ): void {

	// Super‑admins always have full access; no need to show field.
	if ( user_can( $user, 'administrator' ) ) { return; }

	$scope = get_user_meta( $user->ID, 'wv_admin_scope', true ) ?: 'buyers_visitors';
	?>
	<h2><?php esc_html_e( 'Wine Vision admin permissions', DS_THEME_TEXTDOMAIN ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="wv_admin_scope"><?php esc_html_e( 'Scope', DS_THEME_TEXTDOMAIN ); ?></label></th>
			<td>
				<select name="wv_admin_scope" id="wv_admin_scope">
					<option value="all"             <?php selected( $scope, 'all' ); ?>><?php esc_html_e( 'Can manage ALL', DS_THEME_TEXTDOMAIN ); ?></option>
					<option value="exhibitors"      <?php selected( $scope, 'exhibitors' ); ?>><?php esc_html_e( 'Can manage Exhibitors', DS_THEME_TEXTDOMAIN ); ?></option>
					<option value="buyers_visitors" <?php selected( $scope, 'buyers_visitors' ); ?>><?php esc_html_e( 'Can manage Buyers and Visitors', DS_THEME_TEXTDOMAIN ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'Controls which user rows are visible/editable in the Admin Users table.', DS_THEME_TEXTDOMAIN ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}

/* Persist on save ---------------------------------------------------------- */
add_action( 'personal_options_update', __NAMESPACE__ . '\\save_admin_scope' );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\save_admin_scope' );

function save_admin_scope( int $user_id ): void {

	if ( ! current_user_can( 'manage_wv_addon' ) && ! current_user_can( 'administrator' ) ) {
		return;
	}

	if ( ! isset( $_POST['wv_admin_scope'] ) ) { return; }

	$scope = sanitize_text_field( $_POST['wv_admin_scope'] );
	if ( ! in_array( $scope, [ 'all', 'exhibitors', 'buyers_visitors' ], true ) ) {
		return;
	}

	update_user_meta( $user_id, 'wv_admin_scope', $scope );
}
