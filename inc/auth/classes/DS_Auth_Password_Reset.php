<?php
/**
 * “Forgot password?” front-end form – sends reset e-mail that points to the
 * Set-New-Password page.
 * Shortcode: [wv_addon_password_reset_form]
 */
namespace Desymphony\Auth\Classes;
use Desymphony\Helpers\DS_Utils; 

if ( ! defined( 'ABSPATH' ) ) { exit; }

class DS_Auth_Password_Reset {

	/* tiny honeypot to divert bots */
	private const HP_FIELD = 'wv_hp';

	/* ───── rate-limit settings ───── */
	private const MAX_REQS   = 3;               // max e-mails per window
	private const WINDOW_SEC = HOUR_IN_SECONDS; // 1 hour

	public function __construct() {
		add_shortcode( 'wv_addon_password_reset_form', [ $this, 'render_form' ] );

		add_action( 'wp_ajax_nopriv_wv_addon_password_reset', [ $this, 'ajax_send_link' ] );
		add_action( 'wp_ajax_wv_addon_password_reset',        [ $this, 'ajax_send_link' ] );

		// replace the URL in core e-mail with our page
		add_filter( 'retrieve_password_message', [ $this, 'filter_reset_email' ], 10, 4 );
		add_filter( 'password_reset_expiration', fn() => HOUR_IN_SECONDS );
	}

	/** transient key helper */
	private function t_key( string $email ): string {
		return 'wv_pwreset_' . md5( strtolower( $email ) );
	}

	/* ────────────────────────────────────────────────────────────
	 *  Shortcode markup (all CSS & JS inline for easy theme drop-in)
	 * ─────────────────────────────────────────────────────────── */
	public function render_form(): string {

		ob_start(); ?>


		<style>
			#wv-progress-bar { display: none !important}
			#wv-reset-wrapper {background-image:url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Reset_password_Bck.jpg)}
			@media(max-width:768px){
				#wv-reset-wrapper {background-image:url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_Reset_password_Bck.jpg)}
			}
			.auth-box {
				background:
					linear-gradient(180deg, var(--wv-c), transparent);  /* colours */
			}		

		</style>
		<div id="wv-notice-messages" class="text-center"></div>
		<div id="wv-reset-wrapper" class="wv-auth-wrapper d-flex justify-content-center py-64 bg-image" style="min-height: 100vh;">
			<div class="container container-1024">
				<div class="row justify-content-center">
					<div class="col-lg-6">

						<form id="wv-reset-form" class="wv-auth-form wv-block br-16 p-32" action="#">
							
							<div class="text-center">
								<img src="https://winevisionfair.com/wp-content/uploads/2025/06/Header_Logo_Info_DARK.svg" alt="<?php esc_attr_e( 'Logo', DS_THEME_TEXTDOMAIN ); ?>" class="w-100">
							</div>

							<h2 class="fw-600 my-32 text-center">Reset password</h2>
							

							<div class="d-block p-32 br-16 my-32 auth-box">

								<p class="wv-color-w fs-14 text-center">
									Type in <strong>exclusively</strong> your representative's e-mail address to receive the password reset link.
								</p>
								
								<div class="wv-input-group my-16">
									<input class="wv-bg-w lh-2" type="email"
									id="wv_reset_email" name="wv_reset_email" required
									placeholder="<?php esc_attr_e( 'Enter your e-mail address', DS_THEME_TEXTDOMAIN ); ?>">
								</div>

								<?php
									printf(
										'<input type="text" name="%s" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true" />',
										esc_attr( self::HP_FIELD )
									);
									wp_nonce_field( 'wv_addon_password_reset_nonce', 'wv_addon_password_reset_nonce_field' );
								?>


								<div class="d-block text-center pt-24">
									<button type="submit" class="wv-button wv-button-lg wv-button-pill wv-button-c2 py-12">
										<?php esc_html_e( 'Send reset link', DS_THEME_TEXTDOMAIN ); ?>
									</button>

									<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wv-color-c fs-14 d-block mt-16">
										<?php esc_html_e( 'Back to homepage', DS_THEME_TEXTDOMAIN ); ?>
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

			/* ───────────── local Spinner + ajax wrapper ───────────── */
			const Spinner = {
				el   : document.getElementById('globalSpinner'),      // add <div id="globalSpinner"></div> anywhere in HTML
				show : () => Spinner.el && Spinner.el.classList.add('active'),
				hide : () => Spinner.el && Spinner.el.classList.remove('active')
			};

			/**
			 * Tiny helper – POST to admin-ajax.php, JSON-decode and
			 * call ok()/fail() with res.data or the raw error.
			 */
			function ajaxRequest( payload, ok, fail = () => {} ) {
				Spinner.show();
				fetch(
					<?php echo json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
					{
						method  : 'POST',
						headers : { 'Content-Type':'application/x-www-form-urlencoded' },
						body    : new URLSearchParams( payload ).toString()
					}
				)
				.then( r => r.json() )
				.then( res => { Spinner.hide(); ( res && res.success ? ok : fail )( res.data || res ); } )
				.catch( err => { Spinner.hide(); fail( err ); } );
			}

			/* ───────────── form logic ───────────── */
			const form   = document.getElementById( 'wv-reset-form' );
			if ( ! form ) { return; }

			const emailI = form.querySelector( '#wv_reset_email' );
			const msgBox = document.getElementById( 'wv-notice-messages' );
			const hpName = <?php echo json_encode( self::HP_FIELD ); ?>;

			form.addEventListener( 'submit', e => {
				e.preventDefault();
				msgBox.innerHTML = '';

				const email = emailI.value.trim();
				if ( ! email ) {
					msgBox.innerHTML =
						'<div class="wv-error"><?php echo esc_js( __( 'Please enter your e-mail.', DS_THEME_TEXTDOMAIN ) ); ?></div>';
					return;
				}

				ajaxRequest(
					{
						action : 'wv_addon_password_reset',
						email  : email,
						nonce  : form.querySelector( '[name="wv_addon_password_reset_nonce_field"]' ).value || '',
						[ hpName ] : form.querySelector( `[name="${ hpName }"]` ).value || ''
					},
					/* success */
					d => {
						form.reset();
						msgBox.innerHTML = `<div class="wv-notice-msg wv-success">${ d.message }</div>`;
					},
					/* error   */
					d => {
						msgBox.innerHTML = `<div class="wv-notice-msg wv-error">${ d.message || 'Error' }</div>`;
					}
				);
			});
		});
		</script>

		<?php
			return ob_get_clean();
	}

	
	/* ───────────────────────────────────────────────────────────
	 *  AJAX handler – validate and trigger WP core e-mail
	 * ────────────────────────────────────────────────────────── */
	public function ajax_send_link(): void {

		check_ajax_referer( 'wv_addon_password_reset_nonce', 'nonce' );

		if ( ! empty( $_POST[ self::HP_FIELD ] ?? '' ) ) {
			wp_send_json_error( [ 'message' => __( 'Spam detected.', DS_THEME_TEXTDOMAIN ) ] );
		}

		$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		if ( empty( $email ) ) {
			wp_send_json_error( [ 'message' => __( 'E-mail is required.', DS_THEME_TEXTDOMAIN ) ] );
		}
		if ( ! is_email( $email ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid e-mail address.', DS_THEME_TEXTDOMAIN ) ] );
		}

		/* ───── rate-limit check ───── */
		$tk  = $this->t_key( $email );
		$cnt = (int) get_transient( $tk );
		if ( $cnt >= self::MAX_REQS ) {
			wp_send_json_error( [ 'message' => __( 'Too many reset requests – please try again later.', DS_THEME_TEXTDOMAIN ) ] );
		}

		$result = retrieve_password( $email );   // WP core

		if ( is_wp_error( $result ) ) {
			$msg = $result->get_error_message() ?: __( 'Could not send e-mail.', DS_THEME_TEXTDOMAIN );
			wp_send_json_error( [ 'message' => $msg ] );
		}

		/* success → bump counter */
		set_transient( $tk, $cnt + 1, self::WINDOW_SEC );

		wp_send_json_success( [
			'message' => __( 'Password-reset link sent. Please check your inbox.', DS_THEME_TEXTDOMAIN ),
		] );
	}

	public static function set_html_content_type() { return 'text/html'; }

	public function filter_reset_email( string $msg,
									string $key,
									string $login,
									object $user ): string {

		/* 1. Build reset-password URL */
		$pages = get_option( 'wv_addon_auth_pages', [] );
		$url   = isset( $pages['set_password'] )
			? get_permalink( $pages['set_password'] )
			: home_url( '/' );

		$url = add_query_arg(
			[
				'rp_key' => $key,
				'login'  => rawurlencode( $login ),
			],
			$url
		);

		/* 2. Compose HTML mail via the new helper */
		[ $subject, $html ] = DS_Utils::email_template(
			__( 'Reset your password', DS_THEME_TEXTDOMAIN ),     // e-mail subject
			[
				'title'        => sprintf( __( 'Hi %s,', DS_THEME_TEXTDOMAIN ), esc_html( $login ) ),
				'bg'           => '#6e0fd7',
				'logo_variant' => 'W',                              // default white icon
			],
			[
				'title'          => __( 'Reset your password', DS_THEME_TEXTDOMAIN ),
				'html'           => sprintf(
					'<p>%s</p>',
					esc_html__( 'We received a request to reset the password for your Wine Vision account.', DS_THEME_TEXTDOMAIN )
				),
				'note'           => esc_html__( 'If you did not request a password reset, please ignore this e-mail.', DS_THEME_TEXTDOMAIN ),
				'btn_text'       => __( 'Choose a new password', DS_THEME_TEXTDOMAIN ),
				'btn_link'       => esc_url( $url ),
				'btn_bg'         => '#6e0fd7',
				'btn_text_color' => '#ffffff',
			]
		);

		/* 3. Ensure the mail is sent as HTML */
		add_filter( 'wp_mail_content_type', [ self::class, 'set_html_content_type' ] );

		/* 4. Return the HTML body for WP to send */
		return $html;
	}

}
