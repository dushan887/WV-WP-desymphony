<?php
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Displays a personalised thank-you page after registration completes.
 */
class DS_Auth_Thank_You {

	public function __construct() {
		add_shortcode( 'wv_addon_thank_you', [ $this, 'render_thank_you' ] );
	}

	public function render_thank_you(): string {
		$current_user = wp_get_current_user();

		/* ───────────── personalised name ───────────── */
		$company_name = $current_user->ID
			? get_user_meta( $current_user->ID, 'wv_company_name', true )
			: '';

		$display_name = $company_name ?: trim(
			$current_user->first_name . ' ' . $current_user->last_name
		);
		if ( empty( $display_name ) ) {
			$display_name = __( 'Guest', DS_THEME_TEXTDOMAIN );
		}

		/* ───────────── avatar / logo ───────────── */
		$logo_url   = $current_user->ID ? get_user_meta( $current_user->ID, 'wv_user-logo',   true ) : '';
		$avatar_url = $current_user->ID ? get_user_meta( $current_user->ID, 'wv_user-avatar', true ) : '';

		$img_url = $logo_url ?: $avatar_url;
		if ( empty( $img_url ) && $current_user->ID ) {
			$img_url = get_avatar_url( $current_user->ID, [ 'size' => 200 ] );
		}
		if ( empty( $img_url ) ) {
			$img_url = 'https://placehold.co/200?text=+';   // final fallback
		}

		/* ───────────── markup ───────────── */
		ob_start(); ?>
		<div id="wv-wrap">
			<div id="wv-register-wrapper" class="wv-auth-wrapper position-relative">
				<div class="wv-auth-form d-block">

					<div id="wv-reg-complete" class="wv-auth-container container my-48 mx-auto br-16 px-0" data-current-step="final">
						<div class="wv-step wv-step-terms" id="final">

							<!-- Step Header -->
							<div id="wv-step-header" class="px-16 py-24 text-center position-relative ">
								<h6 class="my-0 text-uppercase ls-3 fw-600 wv-color-w wv-color-ww">
									<?php esc_html_e( 'ACCOUNT REGISTERED SUCCESSFULLY', DS_THEME_TEXTDOMAIN ); ?>
								</h6>
							</div>

							<!-- Step Body -->
							<div id="wv-step-body" class="position-relative py-48 wv-color-w wv-color-ww wv-reg-complete" style="padding-inline:0!important;">
								<div class="container container-1024 px-0 text-center">

									<div class="wv-avatar-circle mb-24 p-16 mx-auto">
										<img src="<?php echo esc_url( $img_url ); ?>" alt="avatar" class="img-fluid rounded-circle">
									</div>

									<h2 class="display-4 fw-600 wv-color-w wv-color-ww">
										<?php printf( /* translators: %s: user/company name */ __( 'Welcome %s!', DS_THEME_TEXTDOMAIN ), esc_html( $display_name ) ); ?>
									</h2>

									<p class="wv-color-w wv-color-ww fs-20 px-lg-128">
										Thank you for completing your registration. <br /> Please proceed to your personal account page.

									</p>
								</div>
							</div>
						</div>

						<div id="wv-step-footer" class="wv-step-footer d-flex py-32 px-64 position-relative justify-content-center align-items-center">
							<a href="/wv-dashboard/" class="wv-button wv-button-default wv-button-lg px-lg-64 py-lg-24">
								<?php esc_html_e( 'Go to my account', DS_THEME_TEXTDOMAIN ); ?>
							</a>
						</div>
					</div>

				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
