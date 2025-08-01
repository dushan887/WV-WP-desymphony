<?php
namespace Desymphony\Dashboard;

use Desymphony\Database\DS_CoEx_Repository;
use Desymphony\Helpers\DS_Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Orchestrates the Exhibitor → Co‑Exhibitor invitation flow.
 *
 * * Existing WP users go straight to an accept/decline screen.
 * * New users land on the registration wizard (coex_token splash).
 */
class DS_CoEx_Manager {

	/** DB table – referenced by DS_Auth_Registration splash template */
	public const TABLE = 'wv_exhibitor_links';

	/** @var DS_CoEx_Repository|null */
	private static ?DS_CoEx_Repository $repo = null;

	private static function repo(): DS_CoEx_Repository {
		return self::$repo ??= new DS_CoEx_Repository();
	}

	/* -----------------------------------------------------------------
	 * Boot
	 * ----------------------------------------------------------------*/
	public static function boot(): void {
		/* AJAX (logged‑in exhibitor / admin) */
		add_action( 'wp_ajax_wv_send_coex_invite',   [ self::class, 'ajax_send_invite' ] );
		add_action( 'wp_ajax_wv_get_coex_invites',   [ self::class, 'ajax_get_invites' ] );
		add_action( 'wp_ajax_wv_delete_coex_invite', [ self::class, 'ajax_delete_invite' ] );
		add_action( 'wp_ajax_wv_assign_coex_stand',  [ self::class, 'ajax_assign_stand' ] );

		/* /register/{token} pretty URL */
		add_action( 'init', [ self::class, 'init_pretty_url' ] );
	}

	/* =========================================================
	 * AJAX handlers
	 * =======================================================*/
	public static function ajax_send_invite(): void {
		check_ajax_referer( 'wv_dashboard_nonce', 'security' );

		$exhib_id = get_current_user_id();
		$email    = sanitize_email( $_POST['email'] ?? '' );

		$res = self::send_invite( $exhib_id, $email );
		is_wp_error( $res )
			? wp_send_json_error( [ 'message' => $res->get_error_message() ] )
			: wp_send_json_success( [ 'id' => $res ] );
	}

	public static function ajax_get_invites(): void {
		check_ajax_referer( 'wv_dashboard_nonce', 'security' );

		$invites = \Desymphony\Helpers\DS_Utils::get_coexhibitor_invites( get_current_user_id() );

		foreach ( $invites as &$inv ) {

			/* 1 — ensure co_id is present */
			if ( empty( $inv['co_id'] ) ) {
				$user = get_user_by( 'email', $inv['email'] ?? '' );
				if ( $user ) $inv['co_id'] = (int) $user->ID;
			}

			/* 2 — attach stand codes if the user occupies any */
			if ( ! empty( $inv['co_id'] ) ) {
				$codes = \Desymphony\Helpers\DS_Utils::get_assigned_stand_codes( (int) $inv['co_id'] );
				if ( $codes ) {
					$inv['stand'] = implode( ', ', $codes );   // e.g. “3/22”  or  “3/22, 4/07”
				}
			}
		}
		unset( $inv );

		wp_send_json_success( [ 'invites' => $invites ] );
	}






	public static function ajax_delete_invite(): void {
		check_ajax_referer( 'wv_dashboard_nonce', 'security' );
		self::repo()->delete_invite( (int) $_POST['id'] );
		wp_send_json_success();
	}

	public static function ajax_assign_stand(): void {
		check_ajax_referer( 'wv_dashboard_nonce', 'security' );
		self::repo()->assign_stand( (int) $_POST['id'], sanitize_text_field( $_POST['stand'] ?? '' ) );
		wp_send_json_success();
	}

	/* =========================================================
	 * Core
	 * =======================================================*/
	/**
	 * Create invite row + e‑mail. Handles duplicate & limit checks.
	 *
	 * @return int|\WP_Error  Row‑ID on success.
	 */
	public static function send_invite( int $exhibitor_id, string $email ) {

		/* ---- Slot limits -------------------------------------- */
		$slots = DS_Utils::get_coexhibitor_slots( $exhibitor_id );
		if ( $slots['slots'] !== -1 && $slots['used'] >= $slots['slots'] ) {
			return new \WP_Error( 'limit', __( 'Invitation limit reached.', 'wv-addon' ) );
		}

		/* ---- Sanity ------------------------------------------- */
		$user  = get_user_by( 'email', $email );
		$co_id = $user ? (int) $user->ID : 0;

		if ( strtolower( $email ) === strtolower( (string) get_userdata( $exhibitor_id )->user_email ) ) {
			return new \WP_Error( 'self', __( 'You cannot invite yourself.', 'wv-addon' ) );
		}

		/* ---- Duplicate / declined checks ---------------------- */
		$existing = self::repo()->get_by_exhibitor_and_email( $exhibitor_id, $email );
		if ( $existing ) {
			if ( in_array( $existing->status, [ 'pending', 'accepted' ], true ) ) {
				return new \WP_Error( 'duplicate', __( 'You have already sent an invitation to this address.', 'wv-addon' ) );
			}
			if ( $existing->status === 'declined' ) {
				return new \WP_Error( 'declined', __( 'This address has already declined your previous invitation.', 'wv-addon' ) );
			}
		}

		/* ---- Initial status ----------------------------------- */
		$inviter_role = DS_Utils::get_exhibitor_participation( $exhibitor_id );
		$status       = 'pending';

		if ( $user ) {
			$part   = DS_Utils::get_exhibitor_participation( $co_id );
			$stage1 = DS_Utils::is_ex_stage1_verified( $co_id );
			if ( $part !== 'Solo Exhibitor' || $stage1 ) {
				$status = 'declined'; // auto‑decline
			}
		}

		/* ---- DB row ------------------------------------------- */
		$token = wp_generate_password( 32, false, false );
		$id    = self::repo()->insert_invite( $exhibitor_id, $email, $token, $co_id, $status );

		/* ---- E‑mail (only when pending) ----------------------- */
		if ( $status === 'pending' ) {
			self::mail_invite( $email, $token, (bool) $user, $inviter_role );
		}

		return $id;
	}

	/**
	 * Transactional e‑mail.
	 *
	 * @param string $inviter_role 'Head Exhibitor' | 'Solo Exhibitor'
	 */
	private static function mail_invite(
		string $email,
		string $token,
		bool   $registered,
		string $inviter_role
	): void {

		/* ---- Landing URLs ------------------------------------- */
		$accept = $registered
			? add_query_arg( [ 'coex_token' => $token, 'coex_action' => 'accept' ],  home_url( '/wv-profile/' ) )
			: add_query_arg( [ 'coex_token' => $token ],                             home_url( '/register/' ) );

		$decline = add_query_arg( [ 'coex_token' => $token, 'coex_action' => 'decline' ], home_url( '/wv-profile/' ) );

		/* ---- Copy --------------------------------------------- */
		$is_head = $inviter_role === 'Head Exhibitor';
		$label   = $is_head ? __( 'Member', 'wv-addon' ) : __( 'Co‑Exhibitor', 'wv-addon' );

		$subject = sprintf( __( 'Invitation to join as %s', 'wv-addon' ), $label );
		$header  = sprintf( __( '%s Invitation', 'wv-addon' ), $label );
		$hero    = sprintf( __( 'You have been invited to join Wine Vision as a %s', 'wv-addon' ), $label );
		$btn_txt = sprintf( __( 'Confirm as %s', 'wv-addon' ), $label );

		$intro = $registered
			? sprintf( __( '<p>Please confirm you wish to participate as a %s at the 2025 Wine Vision by Open Balkan fair. If you do not wish to participate, simply click <strong>Decline</strong>.</p>', 'wv-addon' ), $label )
			: sprintf( __( '<p>To accept, create your account and confirm participation as a %s.</p>', 'wv-addon' ), $label );

		$html_body = $intro .
			sprintf(
				'<p style="margin-top:24px;"><a href="%1$s" style="color:#a00;text-decoration:underline;">%2$s</a></p>',
				esc_url( $decline ),
				__( 'Decline invitation', 'wv-addon' )
			);

		[ $subj, $html ] = DS_Utils::email_template(
			$subject,
			[ 'title' => $header, 'bg' => '#6e0fd7', 'logo_variant' => 'W' ],
			[
				'title'          => $hero,
				'html'           => $html_body,
				'note'           => __( 'Questions? <a href="mailto:info@winevision.com">Reply to this e‑mail</a> or contact us at info@winevision.com.', 'wv-addon' ),
				'btn_text'       => $btn_txt,
				'btn_link'       => $accept,
				'btn_bg'         => '#6e0fd7',
				'btn_text_color' => '#ffffff',
			]
		);

		wp_mail( $email, $subj, $html, [ 'Content-Type: text/html; charset=UTF-8' ] );
	}

	/* =========================================================
	 * Public accept / decline
	 * =======================================================*/
	public static function public_handle_response( string $token, string $action ) {

		$invite = self::repo()->get_invite_by_token( $token );
		if ( ! $invite ) {
			return new \WP_Error( 'not_found', __( 'Invitation not found.', 'wv-addon' ) );
		}
		if ( $invite->status !== 'pending' ) {
			return new \WP_Error( 'already', __( 'Invitation already processed.', 'wv-addon' ) );
		}

		$action = strtolower( $action );
		if ( ! in_array( $action, [ 'accept', 'decline' ], true ) ) {
			return new \WP_Error( 'invalid', __( 'Invalid action.', 'wv-addon' ) );
		}

		self::repo()->set_status( (int) $invite->id, $action === 'accept' ? 'accepted' : 'declined' );

		if ( $action === 'accept' && (int) $invite->co_id > 0 ) {
			update_user_meta( (int) $invite->co_id, 'wv_participationModel', 'Co-Exhibitor' );
		}

		return $action === 'accept'
			? __( 'Thank you – your participation has been confirmed.', 'wv-addon' )
			: __( 'Invitation declined. We hope to collaborate in the future.', 'wv-addon' );
	}

	/* =========================================================
	 * /register/{token} rewrite rule
	 * =======================================================*/
	public static function init_pretty_url(): void {
		add_rewrite_rule(
			'^register/([A-Za-z0-9]{32})/?$',
			'index.php?pagename=register&coex_token=$matches[1]',
			'top'
		);

		add_filter( 'query_vars', function ( $vars ) {
			$vars[] = 'coex_token';
			return $vars;
		} );
	}
}

/* Kick‑start hooks */
DS_CoEx_Manager::boot();
