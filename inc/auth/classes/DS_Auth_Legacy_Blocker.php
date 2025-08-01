<?php
/**
 * Disable legacy WP auth screens and funnel users to the DS‑Auth pages
 * defined in wv_addon_auth_pages.
 *
 * @package Desymphony\Auth
 * @since   1.0
 */

namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DS_Auth_Legacy_Blocker {

	/** Slugs used in option `wv_addon_auth_pages` */
	private const PAGE_KEYS = [
		'login'          => 'login',
		'register'       => 'register',
		'reset_password' => 'reset_password',
		'set_password'   => 'set_password',
	];

	public function __construct() {

		/* 1) Hard redirects ----------------------------------------- */
		add_action( 'login_init', [ $this, 'maybe_block_wp_login' ], 1 );
		add_action( 'init',       [ $this, 'maybe_block_wp_admin' ], 1 );

		/* 2) Helper functions → pretty URLs ------------------------- */
		add_filter( 'login_url',        [ $this, 'filter_login_url' ],    10, 3 );
		add_filter( 'register_url',     [ $this, 'filter_register_url' ] );
		add_filter( 'lostpassword_url', [ $this, 'filter_lost_pw_url' ],  10, 2 );
		add_filter( 'site_url',         [ $this, 'filter_site_url' ],     10, 4 );

		// error_log('[DS_Auth] legacy blocker booted');
	}

	/* ---------------------------------------------------------------------
	 * Helpers
	 * ------------------------------------------------------------------ */

	/** Return permalink for auth‑page key or fall back to /login/ */
	private function url( string $key ): string {
		$pages   = get_option( 'wv_addon_auth_pages', [] );
		$page_id = (int) ( $pages[ $key ] ?? 0 );
		return $page_id ? get_permalink( $page_id ) : home_url( '/login/' );
	}

	/** Generic safe redirect + exit */
	private function redirect( string $dest ): void {
		wp_safe_redirect( $dest );
		exit;
	}

	/* ---------------------------------------------------------------------
	 * 1)  Block core screens
	 * ------------------------------------------------------------------ */

	/** Combined login / logout handler on wp-login.php */
	public function maybe_block_wp_login(): void {

		// error_log('[DS_Auth] maybe_block_wp_login fired');

		if ( defined( 'DOING_AJAX' ) || php_sapi_name() === 'cli' ) {
			return;                                   // let AJAX / CLI run
		}

		$action = $_REQUEST['action']       ?? '';
		$redir  = $_REQUEST['redirect_to']  ?? '';

		/* ---------- genuine logout request ------------------------ */
		if ( $action === 'logout' ) {

			if ( ! check_admin_referer( 'log-out', '_wpnonce', false ) ) {
				return;                             // invalid nonce → core
			}

			wp_logout();                            // destroy WP cookies

			// also close our custom PHP session if any
			if ( session_status() === PHP_SESSION_ACTIVE ) {
				$_SESSION = [];
				session_write_close();
				setcookie( session_name(), '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
			}

			nocache_headers();

			$this->redirect(
				add_query_arg( 'loggedout', 'true', $redir ?: $this->url( 'login' ) )
			);
		}

		/* ---------- everything else (login, register, rp…) -------- */
		$map = [
			'register'     => 'register',
			'lostpassword' => 'reset_password',
			'rp'           => 'set_password',
			'resetpass'    => 'set_password',
		];
		$key = $map[ $action ] ?? 'login';
		$this->redirect( $this->url( $key ) );
	}

	/** Guests hitting /wp-admin/ get bounced to custom login */
	public function maybe_block_wp_admin(): void {
		if ( is_admin()
		     && ! current_user_can( 'read' )
		     && ! defined( 'DOING_AJAX' ) ) {
			$this->redirect( $this->url( 'login' ) );
		}
	}

	/* ---------------------------------------------------------------------
	 * 2) Filters for helper functions & generated links
	 * ------------------------------------------------------------------ */

	/** login_url()  (except real logout links) */
	public function filter_login_url( $login_url, $redirect, $force_reauth ) {

		// Leave core logout link untouched
		if ( str_contains( $login_url, 'action=logout' ) ) {
			return $login_url;
		}

		return add_query_arg( 'redirect_to', $redirect, $this->url( 'login' ) );
	}

	/** register_url() */
	public function filter_register_url( $url ) {
		return $this->url( 'register' );
	}

	/** lostpassword_url() */
	public function filter_lost_pw_url( $url, $redirect ) {
		return add_query_arg( 'redirect_to', $redirect, $this->url( 'reset_password' ) );
	}

	/**
	 * Catch site_url( 'wp-login.php' ) and rewrite – but **keep**
	 * special action= links (logout, resetpass) so WordPress can
	 * process them.
	 */
	public function filter_site_url( $url, $path, $scheme, $blog_id ) {

		if ( strpos( $path, 'wp-login.php' ) === 0 &&
		     ! str_contains( $path, 'action=logout' ) &&
		     ! str_contains( $path, 'action=rp' ) &&
		     ! str_contains( $path, 'action=resetpass' ) ) {
			$url = $this->url( 'login' );
		}

		return $url;
	}
}
