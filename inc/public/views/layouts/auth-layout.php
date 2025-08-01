<?php
// Minimal layout for auth forms.
// Expects $view_slug (e.g. "login-form") and optional $data from the controller.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wv-auth-form-container">
	<?php
	// If you have a notifications partial, include it here.
	// include DS_THEME_DIR . '/inc/public/views/partials/auth-notifications.php';
	
	// Include the specific form partial, e.g. "login-form.php"
	$form_file = DS_DIR . "public/views/partials/{$view_slug}.php";
	if ( file_exists( $form_file ) ) {
		include $form_file;
	} else {
		echo '<p>Form partial not found.</p>';
	}
	?>
</div>
