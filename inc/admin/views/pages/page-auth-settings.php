<?php
/**
 * Content for Auth Settings page.
 *
 * Allows assigning WP pages for:
 * - Login
 * - Register
 * - Reset Password
 * - Email Confirmation
 * - 2FA
 * - Thank You / Confirmation
 *
 * Provides custom success message fields, plus:
 * - "Regenerate (Delete & Recreate) Auth Pages" button
 * - "Create or Update Missing Auth Pages" button
 * - "Save Settings" button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the list of Auth Pages we manage
$auth_pages = [
	'login'           => __( 'Login', 'wv-addon' ),
	'register'        => __( 'Register', 'wv-addon' ),
	'reset_password'  => __( 'Reset Password', 'wv-addon' ),
	'set_password'    => __( 'Set Password', 'wv-addon' ), 
	'email_confirm'   => __( 'Email Confirmation', 'wv-addon' ),
	'2fa'             => __( 'Two-Factor Auth', 'wv-addon' ),
	'thank_you'       => __( 'Thank You (Registration)', 'wv-addon' ),
];

// Retrieve stored assignments/messages
$stored_pages    = get_option( 'wv_addon_auth_pages', [] );
$stored_messages = get_option( 'wv_addon_auth_messages', [] );
?>

<?php echo 'Nonce: ' . esc_html( wp_create_nonce( 'ds_clean_pending' ) ); ?>
<h2><?php esc_html_e( 'Auth Settings', 'wv-addon' ); ?></h2>
<p class="description">
	<?php esc_html_e( 'Configure which WordPress pages handle each step of the authentication workflow.', 'wv-addon' ); ?>
</p>

<div id="wv-addon-auth-settings">

	<!-- TOP BUTTONS -->
	<div class="wv-addon-auth-page-actions" style="margin-bottom: 15px;">
		<button
			type="button"
			class="button button-primary"
			id="wv-addon-regenerate-auth-pages"
		>
			<?php esc_html_e( 'Regenerate (Delete & Recreate) Auth Pages', 'wv-addon' ); ?>
		</button>
		&nbsp;
		<button
			type="button"
			class="button"
			id="wv-addon-create-update-missing-pages"
		>
			<?php esc_html_e( 'Create or Update Missing Auth Pages', 'wv-addon' ); ?>
		</button>
	</div>

	<!-- TABLE OF AUTH PAGE SELECTIONS -->
	<table class="form-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Auth Flow Page', 'wv-addon' ); ?></th>
				<th><?php esc_html_e( 'Assigned Page', 'wv-addon' ); ?></th>
				<th><?php esc_html_e( 'Success Message (HTML allowed)', 'wv-addon' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'wv-addon' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $auth_pages as $slug => $label ) :
			$selected_page_id = ! empty( $stored_pages[ $slug ] ) ? (int) $stored_pages[ $slug ] : 0;
			$success_message  = isset( $stored_messages[ $slug ] ) ? $stored_messages[ $slug ] : '';
			?>
			<tr>
				<td>
					<strong><?php echo esc_html( $label ); ?></strong>
				</td>
				<td>
					<?php
					wp_dropdown_pages( [
						'name'             => "wv_addon_auth_page_{$slug}",
						'selected'         => $selected_page_id,
						'show_option_none' => __( '— Select a page —', 'wv-addon' ),
						'option_none_value'=> 0,
					] );
					?>
				</td>
				<td>
					<textarea
						name="wv_addon_auth_success_msg_<?php echo esc_attr( $slug ); ?>"
						rows="2"
						cols="50"
					><?php echo esc_textarea( $success_message ); ?></textarea>
				</td>
				<td>
					<?php if ( $selected_page_id ) : ?>
                        <a href="<?php echo esc_url( get_edit_post_link( $selected_page_id ) ); ?>" 
                           target="_blank" 
                           class="button button-secondary">
                            <?php esc_html_e( 'Edit Page', 'wv-addon' ); ?>
                        </a>
                        <a href="<?php echo esc_url( get_permalink( $selected_page_id ) ); ?>" 
                           target="_blank" 
                           class="button button-secondary">
                            <?php esc_html_e( 'View Page', 'wv-addon' ); ?>
                        </a>
                    <?php else : ?>
                        <span class="description"><?php esc_html_e( 'No page selected.', 'wv-addon' ); ?></span>
                    <?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<hr/>

	<!-- SAVE SETTINGS BUTTON -->
	<div class="wv-addon-save-auth-settings">
		<button
			type="button"
			class="button button-primary"
			id="wv-addon-save-auth-settings"
		>
			<?php esc_html_e( 'Save Settings', 'wv-addon' ); ?>
		</button>
	</div>

</div>

<p class="description" style="margin-top:15px;">
	<?php esc_html_e( 'Please remember to save changes before re-generating or creating missing pages.', 'wv-addon' ); ?>
</p>
