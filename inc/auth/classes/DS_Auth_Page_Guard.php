<?php
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class DS_Auth_Page_Guard {

	public function __construct() {
		add_action( 'template_redirect', [ $this, 'guard' ], 1 );
	}

	public function guard(): void {
		$pages = get_option( 'wv_addon_auth_pages', [] );
		if ( empty( $pages ) ) { return; }

		$id = get_queried_object_id();
		if ( ! $id ) { return; }

		$slug = array_search( $id, $pages, true );
		if ( ! $slug ) { return; }

		switch ( $slug ) {
			case 'login':
			case 'register':
				$this->block_when_logged_in();
				break;

			case 'reset_password':
				$this->block_when_logged_in();
				break;

			case 'set_password':    // NEW – must have ?rp_key
				$this->require_query_key( 'rp_key', 'login' );
				break;

			case 'email_confirm':
				$this->require_query_key( 'token', 'login' );
				break;

			case '2fa':
				if ( ! is_user_logged_in() ) {
					$this->go( 'login' );
				}
				break;
			
		}
	}

	private function block_when_logged_in(): void {
		if ( is_user_logged_in() ) {
			$this->go();
		}
	}

	private function require_query_key( string $key, string $fallback ): void {
		if ( isset( $_GET[ $key ] ) ) { return; }
		$this->go( $fallback );
	}

	private function go( string $slug = '' ): void {
		$pages = get_option( 'wv_addon_auth_pages', [] );
		$url   = $slug && isset( $pages[ $slug ] )
			? get_permalink( $pages[ $slug ] )
			: home_url( '/' );

		wp_safe_redirect( $url );
		exit;
	}
}
