<?php
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Error;
require_once __DIR__ . '/DS_Role_Meta.php';
use Desymphony\Dashboard\DS_CoEx_Manager as CoExMgr;
use Desymphony\Helpers\DS_Utils;

/**
 * Manages multi-step registration for Exhibitors, Buyers, Visitors, etc.
 */
class DS_Auth_Registration {

	public function __construct() {
		add_action('init', [$this, 'maybe_start_session'], 1);

		add_action( 'wp_ajax_nopriv_wv_register_step', [ $this, 'handle_registration_step' ] );
		add_action( 'wp_ajax_wv_register_step', [ $this, 'handle_registration_step' ] );
		add_action( 'wp_ajax_nopriv_wv_check_email', [ $this, 'ajax_check_email' ] );
		add_action( 'wp_ajax_wv_check_email', [ $this, 'ajax_check_email' ] );
		add_shortcode( 'wv_addon_register_form', [ $this, 'render_register_form' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'user_register', [ $this, 'auto_accept_coex_on_register' ] );

		// add_action( 'init', [ __CLASS__, 'maybe_send_test_emails' ] );
	}

	public function maybe_start_session() {
		if (headers_sent()) {
			return;
		}

		$lifetime = 30 * DAY_IN_SECONDS;             // keep for 30 days
		session_set_cookie_params([
			'lifetime' => $lifetime,
			'path'     => COOKIEPATH,
			'domain'   => COOKIE_DOMAIN,
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		]);
		ini_set('session.gc_maxlifetime', $lifetime);

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$_SESSION['wv_reg_path'] ??= [];
	}


	/**
	 * Enqueue any scripts needed for registration.
	 */
	public function enqueue_scripts(): void {
		wp_localize_script(
			DS_THEME_TEXTDOMAIN,
			'WVRegisterData',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wv_register_nonce' ),
			]
		);
	}

	/**
	 * Returns a unique temporary folder name for the current session,
	 * so that we can rename it later once the user is created.
	 */
	private function get_temp_folder_name(): string {
		if ( empty($_SESSION['wv_temp_folder']) ) {
			// Could be "temp_" + random string
			$_SESSION['wv_temp_folder'] = 'temp_' . bin2hex(random_bytes(5));
		}
		return $_SESSION['wv_temp_folder'];
	}


	/**
	 * Renders the registration form.
	 */
	public function render_register_form(): string {
		// Determine current step from query (default to "1")
		$token   = sanitize_text_field( $_GET['coex_token'] ?? '' );
		$accept  = isset( $_GET['accept_invite'] );
		$current_step = isset($_GET['step']) 
			? sanitize_text_field($_GET['step']) 
			: '1';

		/* ------------------------------------------------------------------
		*  DECLINE  (?coex_token=xxx&reject_invite=1)
		* ----------------------------------------------------------------*/
		if ( $token && isset( $_GET['reject_invite'] ) ) {

			if ( $invite = $this->get_coex_invite( $token ) ) {
				global $wpdb;
				$table = $wpdb->prefix . CoExMgr::TABLE;

				$wpdb->update( $table,
					[ 'status' => 'declined', 'date_responded' => current_time( 'mysql' ) ],
					[ 'id' => $invite->id ],
					[ '%s','%s' ],
					[ '%d' ]
				);

				DS_Admin_Notification::send( (int) $invite->exhibitor_id, 'coex_invite_declined' );
			}
			wp_safe_redirect( home_url() );
			exit;
		}


		/* ------------------------------------------------------------------
		*  INVITATION SPLASH  (/register/?coex_token=xxx)
		* ----------------------------------------------------------------*/
		if ( $token && ! $accept && $current_step === '1' ) {

			/* 1. fetch invite + exhibitor */
			if ( ! ( $invite = $this->get_coex_invite( $token ) ) ) {
				return '<p class="wv-error">Invitation link is invalid or expired.</p>';
			}
			$exhib_id  = (int) $invite->exhibitor_id;

			/* 2. data to show */
			$company   = get_user_meta( $exhib_id, 'wv_company_name', true );
			$category  = get_user_meta( $exhib_id, 'wv_userCategory',   true );
			$model     = strtoupper( get_user_meta( $exhib_id, 'wv_participationModel', true ) ?: 'HEAD EXHIBITOR' );

			$upload    = wp_upload_dir();
			$img_url   = $upload['baseurl'] . "/wv/company-logos/{$exhib_id}/company-logo-400.jpg";
			if ( ! file_exists( $upload['basedir'] . "/wv/company-logos/{$exhib_id}/company-logo-400.jpg" ) ) {
				$img_url = get_avatar_url( $exhib_id, [ 'size' => 256 ] );
			}
			
			/* 3. markup */
			ob_start(); 
			
			?>
				<div id="wv-wrap">
					<div id="wv-register-wrapper" class="wv-auth-wrapper position-relative">
						<div class="wv-auth-form d-block">

							<div id="wv-reg-complete"
								class="wv-auth-container container my-48 mx-auto br-16 px-0"
								data-current-step="final">

								<div class="wv-step wv-step-terms" id="final">

									<!-- Header -->
									<div id="wv-step-header"
										class="px-16 py-24 text-center position-relative">
										<h6 class="my-0 text-uppercase ls-3 fw-600 wv-color-w wv-color-ww">
											INVITATION FROM <span><?php echo esc_html( $model ); ?></span>
										</h6>
									</div>

									<!-- Body -->
									<div id="wv-step-body"
										class="position-relative py-48 wv-color-w wv-color-ww wv-reg-complete"
										style="padding-inline:0!important;">
										<div class="container container-1024 px-0 text-center">

											<h1>Hello!</h1>
											<p class="wv-color-w fs-20 px-lg-128">
												YOU HAVE BEEN INVITED TO REGISTER AS A CO‑EXHIBITOR BY
											</p>

											<div class="wv-avatar-circle my-32 p-16 mx-auto">
												<img src="<?php echo esc_url( $img_url ); ?>"
													alt="avatar"
													class="img-fluid rounded-circle">
											</div>

											<h2 class="fw-600 wv-color-w wv-color-ww">
												<?php echo esc_html( $company ); ?>
											</h2>

											<?php if ( $category ) : ?>
												<p class="wv-color-w fs-20 px-lg-128"><?php echo esc_html( $category ); ?></p>
											<?php endif; ?>

										</div>
									</div><!-- /body -->
								</div><!-- /step -->

								<!-- Footer -->
								<div id="wv-step-footer"
									class="wv-step-footer d-flex py-32 px-64 position-relative justify-content-center align-items-center gap-12">
									<a href="<?php echo esc_url( add_query_arg(
											[ 'coex_token' => $token, 'accept_invite' => 1, 'step' => 'wv-ex-step-3' ]
										) ); ?>"
									class="wv-button wv-button-c wv-button-lg px-lg-64 py-lg-24">
										Register account
									</a>

									<a href="<?php echo esc_url( add_query_arg(
											[ 'coex_token' => $token, 'reject_invite' => 1 ]
										) ); ?>"
									class="wv-button wv-button-c2 wv-button-lg px-lg-64 py-lg-24">
										Cancel invitation
									</a>
								</div><!-- /footer -->

							</div><!-- /auth‑container -->
						</div>
					</div>
				</div>
			<?php
			return ob_get_clean();
		}


		/* B) user accepted → pre‑fill session & skip to step‑3 ----------*/
		if ( $token && $accept ) {
			if ( $invite = $this->get_coex_invite( $token ) ) {
				$this->prefill_coex_session( $invite );
				$current_step = 'wv-ex-step-3';          // start here
			}
		}

		// If landing on step “1”, wipe out any previous wv_reg_… session data
		if ($current_step === '1') {
			foreach (array_keys($_SESSION) as $key) {
				if (strpos($key, 'wv_reg_') === 0) {
					unset($_SESSION[$key]);
				}
			}
			// Also clear the navigation path
			unset($_SESSION['wv_reg_path']);
		}

		ob_start();
		echo '<div id="wv-wrap">';
		$steps_config = $this->get_steps_config();
		// Pass $current_step into the included template
		include DS_THEME_DIR . '/inc/public/views/registration/wv-register-form.php';
		echo '</div>';
		return ob_get_clean();
	}


	/**
	 * AJAX: Check if email exists.
	 */
	public function ajax_check_email(): void {
		check_ajax_referer( 'wv_register_nonce', 'security' );

		$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		if ( email_exists( $email ) ) {
			wp_send_json_error([
				'message' => __( 'This email is already registered.', DS_THEME_TEXTDOMAIN ),
			]);
		} else {
			wp_send_json_success([
				'message' => __( 'Email is available.', DS_THEME_TEXTDOMAIN ),
			]);
		}
	}

	/**
	 * Handles each registration step via AJAX.
	 */
	public function handle_registration_step(): void {
		check_ajax_referer( 'wv_register_nonce', 'security' );

		$navigation   = sanitize_text_field( $_POST['navigation'] ?? '' );   // prev | next | submit
		$current_step = sanitize_text_field( $_POST['current_step'] ?? '1' );
		$data         = $_POST['data'] ?? [];

		/* -----------------------------------------------------------
		1) Save the data for the current step in the session
		----------------------------------------------------------- */
		$_SESSION[ "wv_reg_{$current_step}" ] = $data;

		/* -----------------------------------------------------------
		2) Validate only when moving forward or submitting
		----------------------------------------------------------- */
		if ( in_array( $navigation, [ 'next', 'submit' ], true ) ) {
			$errors = $this->validate_step_data( $current_step, $data );
			if ( ! empty( $errors ) ) {
				wp_send_json_error( [
					'message' => $errors['message'] ?? implode( '<br>', (array) $errors ),
					'fields'  => $errors['fields']  ?? [],
				] );
			}
		}

		$steps_config = $this->get_steps_config();

		/* -----------------------------------------------------------
		3) OPTIONAL forcing of condition-field – only when there
			is NO “default” mapping in next-form
		----------------------------------------------------------- */
		if ( $navigation === 'next'
			&& ! empty( $steps_config[ $current_step ]['condition-field'] ) ) {

			$field      = $steps_config[ $current_step ]['condition-field'];
			$next_rules = $steps_config[ $current_step ]['next-form'] ?? [];
			$hasDefault = false;

			if ( is_array( $next_rules ) ) {
				foreach ( $next_rules as $pair ) {
					if ( isset( $pair[0] ) && $pair[0] === 'default' ) {
						$hasDefault = true;
						break;
					}
				}
			}

			/* if no default branch exists, user must choose something */
			if ( ! $hasDefault && empty( $data[ $field ] ) ) {
				wp_send_json_error( [
					'message' => __( 'Please select an option before proceeding.', DS_THEME_TEXTDOMAIN ),
				] );
			}
		}

		/* -----------------------------------------------------------
		4) Work out which step comes next
		----------------------------------------------------------- */
		$next_step_key = false;

		if ( $navigation === 'next' ) {
			$_SESSION['wv_reg_path'][] = $current_step;
			$next_step_key = $this->determine_next_step( $current_step );

		} elseif ( $navigation === 'prev' ) {
			$next_step_key = array_pop( $_SESSION['wv_reg_path'] );
			if ( empty( $next_step_key ) ) {
				$next_step_key = '1';
			}

		} elseif ( $navigation === 'submit' ) {
			$next_step_key = false;   // triggers final processing below
		}

		/* -----------------------------------------------------------
		5) If there is NO next step (or it is “submit”) → finish
		----------------------------------------------------------- */
		if ( ! $next_step_key || $next_step_key === 'submit' ) {

			$result = $this->process_registration();
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( [ 'message' => $result->get_error_message() ] );
			}

			wp_send_json_success( [
				'message'  => __( 'Registration complete.', DS_THEME_TEXTDOMAIN ),
				'redirect' => home_url( '/thank-you/' ),
			] );
		}

		/* -----------------------------------------------------------
		6) Clear data if we loop back to step “1”
		----------------------------------------------------------- */
		if ( $next_step_key === '1' ) {
			foreach ( array_keys( $_SESSION ) as $k ) {
				if ( strpos( $k, 'wv_reg_' ) === 0 ) {
					unset( $_SESSION[ $k ] );
				}
			}
			unset( $_SESSION['wv_reg_path'] );
		}

		/* -----------------------------------------------------------
		7) Render the next-step partial
		----------------------------------------------------------- */
		if ( isset( $steps_config[ $next_step_key ] )
			&& file_exists( $steps_config[ $next_step_key ]['file'] ) ) {

			ob_start();
			$current_step = $next_step_key;        // used by the template
			include $steps_config[ $next_step_key ]['file'];
			$step_html = ob_get_clean();

			$navigation_html = $this->render_navigation_buttons( $next_step_key );

			// ✱ All values that were stored for this step so far
			$prefill_data = $_SESSION[ "wv_reg_{$next_step_key}" ] ?? [];

			wp_send_json_success( [
				'next_step_key' => $next_step_key,
				'step_html'     => $step_html . $navigation_html,
				'prefill'       => $prefill_data,      //  ← NEW
			] );

		} else {
			wp_send_json_error( [
				'message' => __( 'Next step template not found.', DS_THEME_TEXTDOMAIN ),
			] );
		}

	}



	/**
	 * Returns the registration steps configuration.
	 *
	 * Note: This array is used to determine which form file to load for each step.
	 *
	 * @return array
	 */
	private function get_steps_config(): array {
		return [
			// Global Step 1 (Pick Exhibitor / Buyer / Visitor)
			'1' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-start.php',
				'condition-field' => 'wv_profileSelection',
				'next-form'       => [
					[ 'Exhibitor', 'wv-ex-step-1' ],
					[ 'Buyer',     'wv-pb-step-1' ],
					[ 'Visitor',   'wv-vs-step-1' ],
				],
				'required'        => ['wv_profileSelection'],

				// 'condition-field' => false,
				// 'next-form'       => 'final',
				
			],
			// Wine, Spirits or Food? (wv_fieldOfWork)
			'wv-ex-step-1' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-1.php',
				'condition-field' => 'wv_fieldOfWork',
				'next-form'       => [
					[ 'Wine',		'wv-ex-step-2' ],
					[ 'Spirits',	'wv-ex-step-3' ],
					[ 'Food',		'wv-ex-step-3' ],
				],
				'required'        => ['wv_fieldOfWork'],
			],
			
			// You are applying as: (wv_participationModel)
			'wv-ex-step-2' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-2.php',
				'condition-field' => 'wv_participationModel',
				'next-form'       => [
					[ 'prev:wv-ex-step-1:Wine',		'wv-ex-step-3' ],
					[ 'prev:wv-ex-step-1:Spirits',	'wv-ex-step-4' ],
					[ 'prev:wv-ex-step-1:Food',		'wv-ex-step-4' ],
				],
				'required'        => ['wv_participationModel'],
			],

			'wv-ex-step-3' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-3.php',
				'condition-field' => 'wv_userCategory',
				'next-form'       => [
					[ 'includes:Other', 'wv-ex-step-4' ],
					[ 'default', 'wv-ex-step-5' ],
				],
				'required'        => ['wv_userCategory'],
			],

			'wv-ex-step-4' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-4.php',
				'condition-field' => false,
				'next-form'       => 'wv-ex-step-5',
				'required'        => ['wv_userCategoryOtherDescription'],
			],

			'wv-ex-step-5' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-5.php',
				'condition-field' => false,
				'next-form'       => 'wv-ex-step-6',
				'required'        => ['wv_exhibitingProducts'],
			],
			
			'wv-ex-step-6' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-6.php',
				'condition-field' => false,
				'next-form'       => 'wv-ex-step-7',
				'required' => ['wv_companyDescription'],
			],

			'wv-ex-step-7' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-7.php',
				'condition-field' => false,
				'next-form'       => 'wv-ex-step-8',
				'required' => [
					'wv_company_name',
					'wv_company_pobRegion',
					'wv_company_country',
					'wv_company_email',
					'wv_company_city',
					'wv_company_address',
					'wv_company_phone'
				],
				'required_if' => [
					[
						'when'     => ['field' => 'wv_userCategory',
									'in'    => ['Winemaker','Winemaker & Distiller','Distiller']],
						'fields'   => ['wv_annualProductionLiters'],
					],
				],
			],
			
			'wv-ex-step-8' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-8.php',
				'condition-field' => false,
				'next-form'       => 'wv-ex-step-9',
				'required' => [
					// 'wv_company_idRegistryNumber',
					'wv_company_vatRegistryNumber',
					// 'wv_company_iban',
					// 'wv_company_foreignBank',
					// 'wv_company_domesticBank',
					// 'wv_company_foreignAccountNumber',
					// 'wv_company_domesticAccountNumber',
					// 'wv_company_foreignSwift',
					// 'wv_company_domesticSwift',
				],
			],

			'wv-ex-step-9' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-9.php',
				'condition-field' => false,
				'next-form'       => 'wv-ex-step-10',
			],

			'wv-ex-step-10' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-ex-step-10.php',
				'condition-field' => false,
				'next-form'       => 'wv-g-photo-company',
				'required' => [
					'wv_firstName',
					'wv_lastName',
					'wv_nationality',
					'wv_email',
					'wv_positionInCompany',
					'wv_contactTelephone',
				],
			],

			// BUYER
			'wv-pb-step-1' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-1.php',
				'condition-field' => 'wv_userCategory',
				'next-form'       => [
					[ 'includes:Other', 'wv-pb-step-2' ],
					[ 'default', 'wv-pb-step-3' ],
				],
				'required' => ['wv_userCategory'],
			],

			'wv-pb-step-2' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-2.php',
				'condition-field' => false,
				'next-form'       => 'wv-pb-step-3',
				'required' => ['wv_userCategoryOtherDescription'],
			],

			'wv-pb-step-3' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-3.php',
				'condition-field' => 'wv_reasonsForVisiting',
				'next-form'       => [
					[ 'includes:None of the Above', 'wv-pb-step-4' ],
					[ 'default', 'wv-pb-step-5' ],
				],
				'required' => ['wv_reasonsForVisiting[]'],
			],

			'wv-pb-step-4' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-4.php',
				'condition-field' => false,
				'next-form'       => 'wv-pb-step-5',
				'required' => ['wv_otherReasonsForVisiting'],
			],

			'wv-pb-step-5' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-5.php',
				'condition-field' => false,
				'next-form'       => 'wv-pb-step-6',
				'required' => ['wv_pointsOfInterest[]'],
			],

			'wv-pb-step-6' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-6.php',
				'condition-field' => false,
				'next-form'       => 'wv-pb-step-7',
				'required' => ['wv_companyDescription'],
			],

			'wv-pb-step-7' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-7.php',
				'condition-field' => 'wv_governmentSupport',
				'next-form'       => [
					[ 'on', 'wv-pb-step-8' ],
					[ 'default', 'wv-pb-step-9' ],
				],
				'required' => [
					'wv_company_name',
					// 'wv_company_pobRegion',
					'wv_company_country',
					'wv_company_email',
					'wv_company_city',
					// 'wv_company_address',
					'wv_company_phone'
				],
				
			],

			'wv-pb-step-8' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-8.php',
				'condition-field' => false,
				'next-form'       => 'wv-pb-step-9',
				'required' => ['wv_reasonForApplying'],
			],

			'wv-pb-step-9' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-9.php',
				'condition-field' => false,
				'next-form'       => 'wv-pb-step-10',
			],

			'wv-pb-step-10' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-pb-step-10.php',
				'condition-field' => false,
				'next-form'       => 'wv-g-photo-company',
				'required' => [
					'wv_firstName',
					'wv_lastName',
					'wv_nationality',
					'wv_email',
					'wv_contactTelephone',
				],
			],


			// VISITOR
			'wv-vs-step-1' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-vs-step-1.php',
				'condition-field' => 'wv_participationModel',
				'next-form'       => [
					[ 'Public Visitor',		'wv-vs-step-2' ],
					[ 'Company',			'wv-pb-step-1' ],
				],
				'required' => ['wv_participationModel'],
			],

			'wv-vs-step-2' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-vs-step-2.php',
				'condition-field' => false,
				'next-form'       => 'wv-vs-step-3',
				'required' => ['wv_pointsOfInterest[]'],
			],

			'wv-vs-step-3' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-vs-step-3.php',
				'condition-field' => false,
				'next-form'       => 'wv-g-photo-profile',
				'required' => [
					'wv_company_city',
					'wv_company_country',
					'wv_email',
					'wv_firstName',
					'wv_lastName',
				],
			],

			// IMAGES
			'wv-g-photo-company' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-g-photo-company.php',
				'condition-field' => false,
				'next-form'       => 'wv-g-photo-profile',
				'required' => ['wv_user-logo'],
			],

			'wv-g-photo-profile' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-g-photo-profile.php',
				'condition-field' => false,
				'next-form'       => 'wv-g-password',
				'required' => ['wv_user-avatar'],
			],

			// Global Step (Password, Terms and Conditions)
			'wv-g-password' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-g-password.php',
				'condition-field' => false,
				'next-form'       => 'final',
				'required' => [
					'wv_user_password',
					'wv_password_confirm',
					'terms_conditions',
				],
			],

			// Final Step (Password, Terms and Conditions)
			'final' => [
				'file'            => DS_THEME_DIR . '/inc/public/views/registration/wv-final.php',
				'condition-field' => false,
				'next-form'       => 'submit',
				'required' => [
					'terms_conditions_final',
				],
			],
		];
	}	

	/**
	 * Determines the next step key based on the current step data.
	 *
	 * @param string $current_step
	 * @return string|null
	 */
	private function determine_next_step( string $current_step ): ?string {
		$steps = $this->get_steps_config();
		if ( ! isset( $steps[ $current_step ] ) ) {
			return null;
		}

		$cfg = $steps[ $current_step ];
		$current_value = null;
		if ( ! empty( $cfg['condition-field'] ) ) {
			$field = $cfg['condition-field'];
			$data  = $_SESSION["wv_reg_{$current_step}"] ?? [];
			$current_value = $data[ $field ] ?? null;
		}

		if ( isset( $cfg['next-form'] ) && is_array( $cfg['next-form'] ) ) {
			$default = false;
			foreach ( $cfg['next-form'] as $pair ) {
				list( $match, $next ) = $pair;

				if ( strpos( $match, 'prev:' ) === 0 ) {
					// ... unchanged, previous step conditional logic
					$parts = explode( ':', $match, 3 );
					if ( count( $parts ) === 3 ) {
						list( , $prev_step, $expected ) = $parts;
						$prev_cfg = $steps[ $prev_step ] ?? null;
						if ( $prev_cfg && ! empty( $prev_cfg['condition-field'] ) ) {
							$prev_field = $prev_cfg['condition-field'];
							$prev_data  = $_SESSION["wv_reg_{$prev_step}"] ?? [];
							$prev_value = $prev_data[ $prev_field ] ?? null;
							if (
								( is_array( $prev_value ) && in_array( $expected, $prev_value, true ) ) ||
								( ! is_array( $prev_value ) && $prev_value === $expected )
							) {
								return $next;
							}
						}
					}
				} elseif ( is_array( $current_value ) && strpos( $match, 'includes:' ) === 0 ) {
					$expected = substr( $match, 9 );
					if ( in_array( $expected, $current_value, true ) ) {
						return $next;
					}
				} elseif ( ! is_array( $current_value ) && strpos( $match, 'includes:' ) === 0 ) {
					$expected = substr( $match, 9 );
					if ( $expected === $current_value ) {
						return $next;
					}
				} elseif ( $match === 'default' ) {
					$default = $next;
				}
				// Checkbox and radio single value logic:
				elseif ( $match === 'on' && $current_value === 'on' ) {
					return $next;
				} elseif ( $match === 'off' && empty( $current_value ) ) {
					return $next;
				} elseif ( ! is_array( $current_value ) && $match === $current_value ) {
					return $next;
				}
			}
			return $default ?: null;
		}
		if ( isset( $cfg['next-form'] ) && is_string( $cfg['next-form'] ) ) {
			return $cfg['next-form'];
		}
		return null;
	}


	/**
	 * Renders the navigation buttons (Previous, Next, Submit) for the current step.
	 *
	 * @param string $current_step
	 * @return string
	 */
	private function render_navigation_buttons( string $current_step ): string {
		$from_invite = ! empty( $_SESSION['coex_token'] );


		if ( $from_invite && $current_step === 'wv-ex-step-3' ) {
			$show_prev = false;
		} else {
			$show_prev = ( $current_step !== '1' );
		}

		$steps = $this->get_steps_config();
		$html  = '<div id="wv-step-footer" class="wv-step-footer d-flex pt-16 pb-24 pb-lg-64 px-24 px-lg-128 position-relative justify-content-between align-items-center">';

		if ( $show_prev ) {
			$html .= '<button type="submit" name="navigation" value="prev" class="wv-step wv-back"><span class="wv wv_point-50 me-4"><span class="path1"></span><span class="path2"></span></span> Previous</button>';
		}

		if (
			isset( $steps[ $current_step ]['condition-field'] ) &&
			$steps[ $current_step ]['condition-field']
		) {
			$html .= '<button type="submit" name="navigation" value="next" class="wv-step wv-next ms-auto">Next <span class="wv wv_point-50 ms-4"><span class="path1"></span><span class="path2"></span></span></button>';
		} else {
			$next_step = $this->determine_next_step( $current_step );
			if ( $next_step && $next_step !== 'submit' ) {
				$html .= '<button type="submit" name="navigation" value="next" class="wv-step wv-next ms-auto">Next <span class="wv wv_point-50 ms-4"><span class="path1"></span><span class="path2"></span></span></button>';
			} else {
				$html .= '<button type="submit" name="navigation" value="submit" id="wv-submit" class="wv-button wv-button-lg px-lg-64 py-lg-24 wv-button-default">Create account</button>';
			}
		}
		$html .= '</div>';
		return $html;
	}

	/**
	 * Validates one registration step.
	 * Adds explicit messages for password length / complexity / match.
	 *
	 * @return []|array  Empty array = OK, otherwise:
	 *   [ 'message' => 'html-joined string', 'fields' => [slugs] ]
	 */
	private function validate_step_data(string $step_key, array $data): array
	{
		$steps     = $this->get_steps_config();
		$required  = $steps[$step_key]['required'] ?? [];

		/* -------- merge conditional rules -------------------------------- */
		if (!empty($steps[$step_key]['required_if'])) {

			// pull *all* wizard data collected so far
			$global = [];
			foreach ($_SESSION as $k => $v) {
				if (strpos($k, 'wv_reg_') === 0 && is_array($v)) {
					$global += $v;
				}
			}

			foreach ($steps[$step_key]['required_if'] as $rule) {
				$field   = $rule['when']['field']   ?? '';
				$needles = $rule['when']['in']      ?? [];
				$val     = $global[$field]          ?? null;
				$match   = is_array($val)
					? array_intersect($val, $needles)
					: in_array($val, $needles, true);

				if ($match) {
					$required = array_merge($required, $rule['fields'] ?? []);
				}
			}
		}

		$bad_fields = [];          // slugs for JS highlighting
		$msg_lines  = [];          // human messages

		$is_email   = static fn ( $s ) => stripos( $s, 'email' ) !== false;
		$is_number  = static fn ( $s ) =>
			preg_match( '/(_number$|qty|quantity)/i', $s );
		$is_pw      = static fn ( $s ) => $s === 'wv_user_password';
		$is_pw2     = static fn ( $s ) => $s === 'wv_password_confirm';

		foreach ( $required as $slug ) {

			$base = substr( $slug, -2 ) === '[]' ? substr( $slug, 0, -2 ) : $slug;
			$val  = $data[ $base ] ?? null;
			$empty = is_array( $val ) ? empty( $val ) : ( $val === '' || $val === null );

			/* ---------- PASSWORD (primary) ---------- */
			if ( $is_pw( $base ) ) {
				if ( $empty ) {
					$msg_lines[]  = __( 'Password is required.', DS_THEME_TEXTDOMAIN );
					$bad_fields[] = $base;
				} else {
					if ( strlen( $val ) < 10 ) {
						$msg_lines[]  = __( 'Password must be at least 10 characters.', DS_THEME_TEXTDOMAIN );
						$bad_fields[] = $base;
					}
					if ( ! preg_match( '/[A-Z]/', $val ) ) {
						$msg_lines[]  = __( 'Password must include an uppercase letter.', DS_THEME_TEXTDOMAIN );
						$bad_fields[] = $base;
					}
					if ( ! preg_match( '/\d/', $val ) ) {
						$msg_lines[]  = __( 'Password must include a number.', DS_THEME_TEXTDOMAIN );
						$bad_fields[] = $base;
					}
				}
				continue;
			}

			/* ---------- PASSWORD (confirm) ---------- */
			if ( $is_pw2( $base ) ) {
				if ( $empty ) {
					$msg_lines[]  = __( 'Please confirm your password.', DS_THEME_TEXTDOMAIN );
					$bad_fields[] = $base;
				} elseif ( $val !== ( $data['wv_user_password'] ?? '' ) ) {
					$msg_lines[]  = __( 'Passwords do not match.', DS_THEME_TEXTDOMAIN );
					$bad_fields[] = $base;
				}
				continue;
			}

			/* ---------------- EMAIL ---------------- */
			if ( ! $empty && $is_email( $base ) && ! is_email( $val ) ) {
				$msg_lines[]  = __( 'Please enter a valid email address.', DS_THEME_TEXTDOMAIN );
				$bad_fields[] = $base;
				continue;
			}

			/* --------------- NUMBER --------------- */
			if ( ! $empty && $is_number( $base ) && ! is_numeric( $val ) ) {
				$msg_lines[]  = __( 'This field must be a number.', DS_THEME_TEXTDOMAIN );
				$bad_fields[] = $base;
				continue;
			}

			/* ------ generic required empty check ----- */
			if ( $empty ) {
				$label = \Desymphony\Auth\Classes\DS_Meta_Fields::label( $base );
				$msg_lines[]  = sprintf(
					/* translators: %s = field label (already wrapped in <strong>) */
					__( '%s is required.', DS_THEME_TEXTDOMAIN ),
					'<strong class="text-uppercase">' . esc_html( $label ) . '</strong>'
				);
				$bad_fields[] = $base;
			}
		}

		if ( empty( $bad_fields ) ) {
			return [];
		}

		return [
			'message' => implode( '<br>', array_unique( $msg_lines ) ),
			'fields'  => array_values( array_unique( $bad_fields ) ),
		];
	}

	private function get_invite_email(): ?string {
		return ! empty( $_SESSION['coex_token'] )
			? ( $this->get_coex_invite( $_SESSION['coex_token'] )->coemail ?? null )
			: null;
	}

	/**
	 * Processes the registration by creating a new user and saving custom meta.
	 *
	 * @return int|WP_Error The new user ID on success or a WP_Error on failure.
	 */
	private function process_registration() {
		$user_data = [];
		foreach ( $_SESSION as $key => $value ) {
			if ( strpos( $key, 'wv_reg_' ) === 0 && is_array( $value ) ) {
			$user_data = array_merge( $user_data, $value );
			}
		}

		// Retrieve key fields from session data.
		$profile_type = $user_data['wv_profileSelection'] ?? '';

		// Abort if any mandatory data is missing.
		if ( is_wp_error( $err = $this->assert_registration_complete( $user_data ) ) ) {
			return $err;
		}

		$category     = $user_data['wv_userCategory'] ?? '';

		foreach ( $_SESSION as $key => $value ) {
			if ( strpos( $key, 'wv_reg_' ) === 0 && is_array( $value ) ) {
				$user_data = array_merge( $user_data, $value );
			}
		}

		// Retrieve key fields from session data.
		$profile_type = $user_data['wv_profileSelection'] ?? '';
		$category     = $user_data['wv_userCategory'] ?? '';

		$first_name   = sanitize_text_field( $user_data['wv_firstName']  ?? '' );
		$last_name    = sanitize_text_field( $user_data['wv_lastName']   ?? '' );

		if ( $invited = $this->get_invite_email() ) {
			$user_data['wv_email'] = $invited;
		}

		$email        = sanitize_email( $user_data['wv_email'] ?? '' );

		if ( empty( $email ) || ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', __( 'A valid email is required.', DS_THEME_TEXTDOMAIN ) );
		}
		if ( empty( $user_data['wv_user_password'] ) ) {
			return new WP_Error( 'invalid_password', __( 'Password is missing or invalid.', DS_THEME_TEXTDOMAIN ) );
		}

		// Determine user role based on profile type.
		$role = 'subscriber';
		if ( $profile_type === 'Exhibitor' ) {
			$role = 'exhibitor';
		} elseif ( $profile_type === 'Buyer' ) {
			$role = 'buyer';
		} elseif ( $profile_type === 'Visitor' ) {
			$role = 'visitor';
		}

		// Generate username and create new user.
		$username = $this->generate_username( $first_name, $last_name, $email );
		$new_user = [
			'user_login' => $username,
			'user_pass'  => $user_data['wv_user_password'],
			'user_email' => $email,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'role'       => $role,
		];

		$user_id = wp_insert_user( $new_user );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		if ( ! is_wp_error( $user_id ) ) {
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id, true ); // true for persistent login
		}

		// Save basic meta that are handled specially.
		update_user_meta( $user_id, 'wv_profile_selection', sanitize_text_field( $profile_type ) );
		update_user_meta( $user_id, 'wv_category', sanitize_text_field( $category ) );

		// Defaults common to everyone
		update_user_meta( $user_id, 'wv_admin_verified', '0' );   // unchecked
		update_user_meta( $user_id, 'wv_status',         'Pending' );

		// Hosted-Buyer flag only matters for B / V users
		if ( $role === 'buyer' || $role === 'visitor' ) {
			update_user_meta( $user_id, 'wv_wvhb_support', 'NONE' );
		}

		// Exhibitor-only stages
		if ( $role === 'exhibitor' ) {
			update_user_meta( $user_id, 'wv_ex_stage1_verified', '0' );
			update_user_meta( $user_id, 'wv_ex_stage2_verified', '0' );
		}


		// If category is Winemaker or Distiller, update annual production and current stock.
		if ( in_array( $category, [ 'Winemaker', 'Distiller' ], true ) ) {
			$annual_production = sanitize_text_field( $user_data['wv_annualProductionLiters'] ?? '' );
			$current_stock     = sanitize_text_field( $user_data['wv_currentStockLiters'] ?? '' );
			update_user_meta( $user_id, 'wv_annual_production', $annual_production );
			update_user_meta( $user_id, 'wv_current_stock', $current_stock );
		}

		// Define the mapping of form field names to user meta keys.
		$meta_mapping = [
			// Company Info
			'wv_company_name'                 => 'wv_company_name',
			'wv_company_pobRegion'            => 'wv_company_pobRegion',
			'wv_company_country'              => 'wv_company_country',
			'wv_company_email'                => 'wv_company_email',
			'wv_company_city'                 => 'wv_company_city',
			'wv_company_website'              => 'wv_company_website',
			'wv_company_address'              => 'wv_company_address',
			'wv_company_phone'                => 'wv_company_phone',
			'wv_company_idRegistryNumber'     => 'wv_company_idRegistryNumber',
			'wv_company_vatRegistryNumber'    => 'wv_company_vatRegistryNumber',
			'wv_company_iban'                 => 'wv_company_iban',
			'wv_company_domesticBank'         => 'wv_company_domesticBank',
			'wv_company_foreignBank'          => 'wv_company_foreignBank',
			'wv_company_domesticAccountNumber'=> 'wv_company_domesticAccountNumber',
			'wv_company_foreignAccountNumber' => 'wv_company_foreignAccountNumber',
			'wv_company_domesticSwift'        => 'wv_company_domesticSwift',
			'wv_company_foreignSwift'         => 'wv_company_foreignSwift',
			'wv_companyDescription'           => 'wv_companyDescription',

			// Exhibitor specific
			'wv_fieldOfWork'                  => 'wv_fieldOfWork',
			'wv_participationModel'           => 'wv_participationModel',
			'wv_exhibitingProducts'           => 'wv_exhibitingProducts',
			'wv_annualProductionLiters'       => 'wv_annual_production',
			'wv_currentStockLiters'           => 'wv_current_stock',

			// Category & other
			'wv_userCategory'                 => 'wv_userCategory',
			'wv_userCategoryOtherDescription' => 'wv_userCategoryOtherDescription',

			// Socials
			'wv_socInstagram'                 => 'wv_socInstagram',
			'wv_socLinkedin'                  => 'wv_socLinkedin',
			'wv_socFacebook'                  => 'wv_socFacebook',
			'wv_socX'                         => 'wv_socX',

			// Image uploads
			'wv_user-logo'                    => 'wv_user-logo',
			'wv_user-avatar'                  => 'wv_user-avatar',

			// Representative/User info
			'wv_firstName'                    => 'wv_firstName',
			'wv_lastName'                     => 'wv_lastName',
			'wv_professionalOccupation'       => 'wv_professionalOccupation',
			'wv_yearsOfExperience'            => 'wv_yearsOfExperience',
			'wv_nationality'                  => 'wv_nationality',
			'wv_email'                        => 'wv_email',
			'wv_positionInCompany'            => 'wv_positionInCompany',
			'wv_contactTelephone'             => 'wv_contactTelephone',
			'wv_exhibitor_rep_whatsapp'       => 'wv_exhibitor_rep_whatsapp',
			'wv_exhibitor_rep_viber'          => 'wv_exhibitor_rep_viber',

			// Buyer/Visitor
			'wv_reasonsForVisiting'           => 'wv_reasonsForVisiting',
			'wv_otherReasonsForVisiting'      => 'wv_otherReasonsForVisiting',
			'wv_pointsOfInterest'             => 'wv_pointsOfInterest',
			'wv_reasonForApplying'            => 'wv_reasonForApplying',
			'wv_governmentSupport'            => 'wv_governmentSupport',
			
			'terms_conditions'                => 'terms_conditions',
		];


		// Loop through each field in our meta mapping and save it if present.
		foreach ( $meta_mapping as $form_field => $meta_key ) {
			if ( isset( $user_data[ $form_field ] ) ) {
				$value = $user_data[ $form_field ];
				// Check if the value is an array (for multi-selection fields)
				if ( is_array( $value ) ) {
					$sanitized_value = array_map( 'sanitize_text_field', $value );
				} else {
					$sanitized_value = sanitize_text_field( $value );
				}
				update_user_meta( $user_id, $meta_key, $sanitized_value );
			}
		}

		// Clear registration session data.
		foreach ( $_SESSION as $k => $v ) {
			if ( strpos( $k, 'wv_reg_' ) === 0 ) {
				unset( $_SESSION[ $k ] );
			}
		}

		$this->rename_temp_folder_to_user_id( $user_id );

		$participation_model = $user_data['wv_participationModel'] ?? '';
		$this->maybe_auto_verify_coexhibitor($user_id, $role, $participation_model);	

		$this->send_registration_email( $user_id, $user_data ); 
		$this->send_pending_validation_email(
			$user_id,
			$profile_type,
			$participation_model,
			$email
		);

		return $user_id;
	}

	/** Auto‑verify Co‑Exhibitors immediately after account creation */
	private function maybe_auto_verify_coexhibitor(
		int    $user_id,
		string $role,
		string $participation_model
	): void {
		if ($role === 'exhibitor' && $participation_model === 'Co-Exhibitor') {
			update_user_meta($user_id, 'wv_admin_verified',       '1');
			update_user_meta($user_id, 'wv_ex_stage1_verified',   '1');
			update_user_meta($user_id, 'wv_ex_stage2_verified',   '1');
			update_user_meta($user_id, 'wv_status',               'Active');
		}
	}

	/**
	 * Generates a username based on first name, last name or email.
	 *
	 * @param string $first
	 * @param string $last
	 * @param string $email
	 * @return string
	 */
	private function generate_username( string $first, string $last, string $email ): string {
		if ( empty( $first ) && empty( $last ) ) {
			$username_base = sanitize_user( current( explode( '@', $email ) ) );
		} else {
			$username_base = sanitize_user( strtolower( $first . '.' . $last ) );
		}
		$username = $username_base;
		$i        = 1;
		while ( username_exists( $username ) ) {
			$username = $username_base . $i;
			$i++;
		}
		return $username;
	}

	public static function auto_accept_coex_on_register( $user_id ) {
		// token passed via GET or POST during registration
		if ( empty( $_REQUEST['coex_token'] ) ) {
			return;
		}
		$token = sanitize_text_field( $_REQUEST['coex_token'] );

		global $wpdb;
		$table = $wpdb->prefix . CoExMgr::TABLE;

		// find the pending invite
		$invite = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE token = %s AND status = 'pending'",
				$token
			)
		);
		if ( ! $invite ) {
			return;
		}

		// update to accepted
		$now = current_time('mysql');
		$wpdb->update(
			$table,
			[
				'status'         => 'accepted',
				'date_responded' => $now,
				'co_id'          => $user_id,
			],
			[ 'id' => $invite->id ],
			[ '%s','%s','%d' ],
			[ '%d' ]
		);

		// link in usermeta
		update_user_meta( $invite->exhibitor_id, 'wv_linked_coex', $user_id );
		update_user_meta( $user_id,                    'wv_linked_exhib', $invite->exhibitor_id );

		// Send notification to exhibitor that co-exhibitor accepted
		DS_Admin_Notification::send( (int) $invite->exhibitor_id, 'coex_invite_accepted' );
	}

	/**
	 * Once the user is truly created, rename the folder from "temp_xxx" -> real user ID
	 * Also fix any stored meta so it points to the new path.
	 */
	private function rename_temp_folder_to_user_id( int $user_id ): void {
		if ( empty($_SESSION['wv_temp_folder']) ) {
			return; // no temp folder in use, skip
		}
		$tempName = $_SESSION['wv_temp_folder'];
		$upload_dir = wp_upload_dir();

		// e.g. /var/www/.../wp-content/uploads/wv/company-logos/
		$base_company_logos = $upload_dir['basedir'] . '/wv/company-logos/';
		$old_folder = $base_company_logos . $tempName;
		$new_folder = $base_company_logos . $user_id;

		if ( is_dir($old_folder) ) {
			// Create the new folder if needed, then rename
			wp_mkdir_p($new_folder);
			rename($old_folder, $new_folder);
		}

		// For avatars, do likewise if needed:
		$base_avatars = $upload_dir['basedir'] . '/wv/avatars/';
		$old_folder2  = $base_avatars . $tempName;
		$new_folder2  = $base_avatars . $user_id;
		if ( is_dir($old_folder2) ) {
			wp_mkdir_p($new_folder2);
			rename($old_folder2, $new_folder2);
		}

		// If you have any user meta that points to the old folder, update it:
		// Example: wv_user-logo and wv_user-avatar might have URLs with 'temp_xxx' 
		// Replace 'temp_xxx' with the real user ID:
		$meta_keys = ['wv_user-logo','wv_user-avatar'];
		foreach ($meta_keys as $mk) {
			$old_url = get_user_meta($user_id, $mk, true);
			if ( $old_url && strpos($old_url, $tempName) !== false ) {
				$new_url = str_replace($tempName, (string)$user_id, $old_url);
				update_user_meta($user_id, $mk, $new_url);
			}
		}

		// Clear the session var so we don't rename multiple times
		unset($_SESSION['wv_temp_folder']);
	}

	/**
	 * E-mail a registration summary to Admin (and optionally the registrant).
	 *
	 * Uses DS_Meta_Fields to map every slug → human-friendly label and skips
	 * anything that is not a real form field (step keys, nonce, referer, …).
	 *
	 * @param int   $user_id
	 * @param array $user_data  Everything collected in the wizard session
	 */
	private function send_registration_email( int $user_id, array $user_data ): void {

		/* -----------------------------------------------------------
		* 0)  Build “slug → label” lookup table from DS_Meta_Fields
		* ----------------------------------------------------------*/
		$defs = array_merge(
			\Desymphony\Auth\Classes\DS_Meta_Fields::get_global_fields(),
			\Desymphony\Auth\Classes\DS_Meta_Fields::get_exhibitor_fields(),
			\Desymphony\Auth\Classes\DS_Meta_Fields::get_buyer_visitor_fields()
		);

		$slug_to_label = [];
		foreach ( $defs as $d ) {
			$slug_to_label[ $d['field_slug'] ] = $d['field_question'];
		}

		/* -----------------------------------------------------------
		* 1) Keys we never want in the e-mail
		* ----------------------------------------------------------*/
		$skip_keys = [
			'security',
			'_wp_http_referer',
			'current_step',
			'wv_user_password',
			'wv_password_confirm',
		];

		/* -----------------------------------------------------------
		* 2) Build the HTML <table>
		* ----------------------------------------------------------*/
		$rows  = '
			<tr>
				<td style="padding:4px 8px;border:1px solid #ddd;"><strong>User&nbsp;ID</strong></td>
				<td style="padding:4px 8px;border:1px solid #ddd;">' . intval( $user_id ) . '</td>
			</tr>';

		$upload_dir   = wp_upload_dir();
		$base_url     = trailingslashit( $upload_dir['baseurl'] );   // e.g. …/wp-content/uploads/
		$logo_url     = $base_url . "wv/company-logos/{$user_id}/company-logo-400.jpg";
		$avatar_url   = $base_url . "wv/avatars/{$user_id}/profile-image-400.jpg";

		foreach ( $user_data as $slug => $value ) {

			// ignore numeric keys (step-path array) and explicit skips
			if ( is_int( $slug ) || in_array( $slug, $skip_keys, true ) ) {
				continue;
			}

			/* ───── replace the two temp URLs with their final location ───── */
			if ( $slug === 'wv_user-logo'  ) { $value = $logo_url;   }
			if ( $slug === 'wv_user-avatar') { $value = $avatar_url; }

			$label = $slug_to_label[ $slug ] ?? $slug;          // fall back to slug
			$label = esc_html( $label );

			// Pretty-print value
			if ( is_array( $value ) ) {
				$value = implode( ', ', array_map( 'wp_kses_post', $value ) );
			} elseif ( filter_var( $value, FILTER_VALIDATE_URL ) && preg_match( '#^https?://#', $value ) ) {
				$value = '<a href="' . esc_url( $value ) . '" target="_blank">' . esc_html( $value ) . '</a>';
			} elseif ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
				$value = '<a href="mailto:' . esc_attr( $value ) . '">' . esc_html( $value ) . '</a>';
			} else {
				$value = wp_kses_post( $value );
			}

			$rows .= "
				<tr>
					<td style=\"padding:4px 8px;border:1px solid #ddd;\"><strong>{$label}</strong></td>
					<td style=\"padding:4px 8px;border:1px solid #ddd;\">{$value}</td>
				</tr>";
		}
		

		$message = "
			<html><body>
				<h2>New registration completed</h2>
				<table cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;font-size:14px;width:100%;\">
					{$rows}
				</table>
			</body></html>";

		/* -----------------------------------------------------------
		* 3) Fire the e-mail
		* ----------------------------------------------------------*/
		$to = [ get_option( 'admin_email' ), 'admin@winevisionfair.com' ];
		$subject  = 'New WineVision account: #' . $user_id;
		$headers  = [
			'Content-Type: text/html; charset=UTF-8',
			'From: Wine Vision <no-reply@' . $_SERVER['SERVER_NAME'] . '>',
		];

		wp_mail( $to, $subject, $message, $headers );

		/* -----------------------------------------------------------
		* 4) (optional) user confirmation
		* ----------------------------------------------------------*/
		/*
		wp_mail(
			$user_data['wv_email'],
			'Your Wine Vision account is ready',
			"Hi {$user_data['wv_firstName']},\n\nThank you for registering.",
			['Content-Type: text/plain; charset=UTF-8']
		);
		*/
	}
	

	/* -------------------------------------------------------------
	*  SEND PENDING / WELCOME E-MAIL (via DS_Admin_Notification)
	* ---------------------------------------------------------- */
	private function send_pending_validation_email(
		int    $user_id,
		string $profile_type,
		string $participation_model,
		string $email
	): void {

		// 1. Map registration context → DS_Admin_Notification template slug
		$slug = match ( $profile_type ) {
			'Exhibitor' => (
				$participation_model === 'Co-Exhibitor'
					? 'exhibitor_approved'
					: 'exhibitor_validating'
			),
			'Buyer'     => 'buyer_validating',
			'Visitor'   => ( $participation_model === 'Company'
				? 'provisitor_validating'
				: 'visitor_evaluating' ),
			default     => null,
		};

		if ( ! $slug ) {
			return; // nothing to send
		}

		/* 2. Prefer the central notification helper if available
		*    (expected signature: static send( int $user_id, string $slug ): void ) */
		if ( method_exists( DS_Admin_Notification::class, 'send' ) ) {
			DS_Admin_Notification::send( $user_id, $slug );
			return;
		}

		/* 3. Fallback – call the private template builder directly */
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return;
		}

		$ref       = new \ReflectionClass( DS_Admin_Notification::class );
		$templates = $ref->getConstant( 'TEMPLATES' );
		if ( empty( $templates[ $slug ] ) ) {
			return;
		}

		[ $subject, $html ] = call_user_func( $templates[ $slug ], $user );

		add_filter( 'wp_mail_content_type', fn() => 'text/html' );
		wp_mail( $email, $subject, $html );
		remove_filter( 'wp_mail_content_type', fn() => 'text/html' );
	}
	


	/* -------------------------------------------------------------
	*  Simple helper so wp_mail() sends HTML
	* ---------------------------------------------------------- */
	public static function set_html_content_type(): string {
		return 'text/html';
	}


	/* inside DS_Auth_Registration, just below send_pending_validation_email() */
	public function debug_send_pending_email(
		int    $user_id,
		string $profile_type,
		string $participation_model,
		string $email
	): void {
		$this->send_pending_validation_email(
			$user_id,
			$profile_type,
			$participation_model,
			$email
		);
	}

	/* ===================================================================
	* DEBUG: visit /?wv_test_emails=1 to receive all four variants
	* ==================================================================*/
	public static function maybe_send_test_emails(): void {
		if ( empty( $_GET['wv_test_emails'] ) ) {
			return;
		}

		$to  = get_option( 'admin_email' );
		$reg = new self;                       // create a fresh instance

		$cases = [
			[ 'Exhibitor', 'Company' ],
			[ 'Buyer',     'Company' ],
			[ 'Visitor',   'Company' ],
			[ 'Visitor',   'Public Visitor' ],
		];

		foreach ( $cases as [ $type, $model ] ) {
			$reg->debug_send_pending_email( 0, $type, $model, $to );
		}

		wp_die( 'Test e-mails sent to '.$to );
	}


	/**
	 * Abort if any required fields for the current path are missing.
	 *
	 * @param array  $user_data   All session data merged so far
	 * @param string $profileType Exhibitor | Buyer | Visitor
	 * @return true|WP_Error
	 */
	private function assert_registration_complete(array $user_data): true|WP_Error
	{
		// 1. Pull every step that belongs to the current flow -------------
		$steps = $this->get_steps_config();
		$pathSteps = ['1'];    // always starts with global step 1

		// Walk the flow once, following the same logic you use at runtime
		$next = $this->determine_next_step('1');
		while ($next && $next !== 'submit') {
			$pathSteps[] = $next;
			$next = $this->determine_next_step($next);
		}

		// 2. Build a flat list of required slugs for those steps ----------
		$required = [];
		foreach ($pathSteps as $key) {
			$required = array_merge($required, $steps[$key]['required'] ?? []);
		}
		$required = array_unique($required);

		// 3. Detect any that are missing or empty -------------------------
		$missing = array_filter($required, function ($slug) use ($user_data) {
			$base = str_ends_with($slug, '[]') ? substr($slug, 0, -2) : $slug;
			$val  = $user_data[$base] ?? null;
			return is_array($val) ? empty($val) : ('' === $val || null === $val);
		});

		if ($missing) {
			return new WP_Error(
				'incomplete_registration',
				sprintf(
					/* translators: %s = field slugs */
					__('Your session expired – please complete the missing fields: %s', DS_THEME_TEXTDOMAIN),
					implode(', ', $missing)
				)
			);
		}

		return true;
	}


	/* -----------------------------------------------------------------
	 * NEW: small helper – returns the pending invite or `null`
	 * ----------------------------------------------------------------*/
	private function get_coex_invite( string $token ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . CoExMgr::TABLE;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE token = %s AND status = 'pending' LIMIT 1",
				$token
			)
		) ?: null;
	}

	/* -----------------------------------------------------------------
	 * NEW: stash default data in the wizard session and fast‑forward
	 * ----------------------------------------------------------------*/
	private function prefill_coex_session( object $invite ): void {

		$exhib_id = (int) $invite->exhibitor_id;
		$field    = get_user_meta( $exhib_id, 'wv_fieldOfWork', true ) ?: '';

		$_SESSION['wv_reg_path'] = [ '1', 'wv-ex-step-1', 'wv-ex-step-2' ];

		$_SESSION['wv_reg_1']              = [ 'wv_profileSelection'   => 'Exhibitor'      ];
		$_SESSION['wv_reg_wv-ex-step-1']   = [ 'wv_fieldOfWork'        => $field           ];
		$_SESSION['wv_reg_wv-ex-step-2']   = [ 'wv_participationModel' => 'Co-Exhibitor'   ];

		$_SESSION['wv_reg_wv-ex-step-10'] = [ 'wv_email' => $invite->coemail ];
		$_SESSION['coex_email']           = $invite->coemail; 

		// keep invite meta handy for later (e.g. e‑mails, auto‑accept)
		$_SESSION['coex_token']            = $invite->token;
		$_SESSION['coex_exhibitor_id']     = $exhib_id;
	}





}
