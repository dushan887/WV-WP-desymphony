<?php
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class DS_Auth_Login {

	/* ---------------- Spam / brute-force constants ---------------- */
	private const MAX_ATTEMPTS = 5;      // how many bad logins before lock
	private const LOCK_MINUTES = 15;     // lockout window in minutes
	private const HP_FIELD     = 'wv_hp';// honeypot field name

	public function __construct() {

		add_shortcode( 'wv_addon_login_form', [ $this, 'render_login_form' ] );
		add_action( 'wp_enqueue_scripts',     [ $this, 'enqueue_scripts' ] );

		add_action( 'wp_ajax_nopriv_wv_addon_login', [ $this, 'ajax_handle_login' ] );
	}

	/* ------------------------------------------------------------------
	 *  Front-end helper – expose admin-ajax.php to JS
	 * -----------------------------------------------------------------*/
	public function enqueue_scripts(): void {
		wp_localize_script(
			DS_THEME_TEXTDOMAIN,
			'wvAddonAjax',
			[ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ]
		);
	}

	/* ------------------------------------------------------------------
	 *  Shortcode output
	 * -----------------------------------------------------------------*/
	public function render_login_form(): string {

		ob_start(); ?>
		<style>
			#wv-progress-bar { display: none !important}
			#wv-login-wrapper {background-image:url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Log_in_Bck.jpg)}
			@media(max-width:768px){
				#wv-login-wrapper {background-image:url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_Log_in_Bck.jpg)}
			}
			.auth-box {
				background:
				  linear-gradient(0deg, var(--wv-c) 0%, transparent 100%), /* alpha */
  				  linear-gradient(90deg, var(--wv-v), var(--wv-r), var(--wv-y));  /* colours */
			}
			.wv-input-group { position: relative; }
			.wv-toggle-pass{
				position:absolute;
				top:50%; right:1rem;
				transform:translateY(-50%);
				cursor:pointer;
				line-height:0;
				font-size:1.25rem; 
				color:var(--wv-c_50);
			}


		</style>
		<div id="wv-login-messages" class="text-center"></div>
		<div id="wv-login-wrapper" class="wv-auth-wrapper d-flex justify-content-center py-64 bg-image" style="min-height: 100vh;">

			<div class="container container-1024">
				<div class="row align-items-center justify-content-center">
					<div class="col-lg-6">
						
						<form id="wv-login-form" class="wv-auth-form wv-block br-16 p-32" action="#">

							<div class="text-center">
								<img src="https://winevisionfair.com/wp-content/uploads/2025/06/Header_Logo_Info_LIGHT.svg" alt="<?php esc_attr_e( 'Logo', DS_THEME_TEXTDOMAIN ); ?>" class="w-100">
							</div>

							<h2 class="wv-color-w fw-600 my-32 text-center">Welcome!</h2>

							<div class="d-block p-32 br-16 mb-32 auth-box" >
								<div class="wv-input-group mb-16">
									<input class="wv-bg-w lh-2" type="email" id="wv_login_email" name="wv_login_email" required placeholder="<?php esc_attr_e( 'Enter your e-mail address', DS_THEME_TEXTDOMAIN ); ?>" />
								</div>

								<div class="wv-input-group mb-24 position-relative">
									<input  class="wv-bg-w lh-2 w-100"
											type="password"
											id="wv_login_password"
											name="wv_login_password"
											required
											placeholder="<?php esc_attr_e( 'Enter your password', DS_THEME_TEXTDOMAIN ); ?>" />

									<!-- eye / eye-off icon -->
									<span class="wv-toggle-pass wv wv_show"
										role="button" tabindex="0" aria-label="Show password"></span>
								</div>


								<?php
								// ── Honeypot – visually hidden via inline style
								printf(
									'<input type="text" name="%s" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true" />',
									esc_attr( self::HP_FIELD )
								);

								// Nonce (checked in ajax_handle_login)
								wp_nonce_field( 'wv_addon_login_nonce', 'wv_addon_login_nonce_field' ); ?>

								<div class="d-block text-center pt-24">
									<button type="submit" class="wv-button wv-button-lg wv-button-pill wv-button-gradient py-12">
										<?php esc_html_e( 'Log in', DS_THEME_TEXTDOMAIN ); ?>
									</button>

									<a href="/forgot-password/" class="d-block mt-16 wv-color-c_50 fs-14">
										<?php esc_html_e( 'Forgot password?', DS_THEME_TEXTDOMAIN ); ?>
									</a>
								</div>
							</div>

							
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
		document.addEventListener('DOMContentLoaded', () => {
			const toggle = document.querySelector('.wv-toggle-pass');
			const pwd    = document.getElementById('wv_login_password');

			if (toggle && pwd){
				toggle.addEventListener('click', switchVis);
				toggle.addEventListener('keydown', e => (e.key === 'Enter' || e.key === ' ') && switchVis());

				function switchVis(){
					const isShown = pwd.type === 'text';
					pwd.type = isShown ? 'password' : 'text';

					toggle.classList.toggle('wv_show',  isShown);
					toggle.classList.toggle('wv_hide', !isShown);
					toggle.setAttribute('aria-label', isShown ? 'Show password' : 'Hide password');
				}
			}
		});
		</script>
		<?php
		return ob_get_clean();
	}

	/* ------------------------------------------------------------------
	 *  AJAX handler with spam / brute-force checks
	 * -----------------------------------------------------------------*/
	public function ajax_handle_login(): void {

		check_ajax_referer( 'wv_addon_login_nonce', 'nonce' );

		/* ---------- Honeypot ---------- */
		if ( ! empty( $_POST[ self::HP_FIELD ] ?? '' ) ) {
			// Bots usually fill every field they find
			wp_send_json_error( [ 'message' => __( 'Spam detected.', DS_THEME_TEXTDOMAIN ) ] );
		}

		/* ---------- Rate limiter ---------- */
		$ip = $this->get_client_ip();
		if ( $this->is_locked( $ip ) ) {
			wp_send_json_error( [ 'message' => __( 'Too many failed attempts – try again later.', DS_THEME_TEXTDOMAIN ) ] );
		}

		$email    = isset( $_POST['email'] )    ? sanitize_email( $_POST['email'] )        : '';
		$password = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : '';

		if ( empty( $email ) || empty( $password ) ) {
			wp_send_json_error( [ 'message' => __( 'E-mail and password required.', DS_THEME_TEXTDOMAIN ) ] );
		}

		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			$this->increment_attempts( $ip );
			wp_send_json_error( [ 'message' => __( 'Invalid credentials.', DS_THEME_TEXTDOMAIN ) ] );
		}

		$signon = wp_signon(
			[
				'user_login'    => $user->user_login,
				'user_password' => $password,
				'remember'      => true,
			],
			is_ssl()
		);

		if ( is_wp_error( $signon ) ) {
			$this->increment_attempts( $ip );
			wp_send_json_error( [ 'message' => __( 'Invalid credentials.', DS_THEME_TEXTDOMAIN ) ] );
		}

		/* ---------- Success: clear counter ---------- */
		$this->clear_attempts( $ip );

		wp_send_json_success( [
			'message'  => __( 'Login successful.', DS_THEME_TEXTDOMAIN ),
			'redirect' => home_url( '/wv-dashboard' ),
		] );
	}

	/* ------------------------------------------------------------------
	 *  Brute-force helpers (transients by IP)
	 * -----------------------------------------------------------------*/
	private function attempts_key( string $ip ): string {
		return 'wv_login_' . md5( $ip );
	}

	private function is_locked( string $ip ): bool {
		$attempts = (int) get_transient( $this->attempts_key( $ip ) );
		return $attempts >= self::MAX_ATTEMPTS;
	}

	private function increment_attempts( string $ip ): void {
		$key      = $this->attempts_key( $ip );
		$attempts = (int) get_transient( $key ) + 1;
		set_transient( $key, $attempts, self::LOCK_MINUTES * MINUTE_IN_SECONDS );
	}

	private function clear_attempts( string $ip ): void {
		delete_transient( $this->attempts_key( $ip ) );
	}

	private function get_client_ip(): string {
		foreach ( [ 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ] as $h ) {
			if ( ! empty( $_SERVER[ $h ] ) ) {
				return sanitize_text_field( explode( ',', $_SERVER[ $h ] )[0] );
			}
		}
		return 'unknown';
	}
}
