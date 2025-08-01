<?php
namespace Desymphony\Database;

defined( 'ABSPATH' ) || exit;

/**
 * Thin data‑access layer for `wp_wv_exhibitor_links`.
 */
class DS_CoEx_Repository {

	private string $table;

	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'wv_exhibitor_links';
	}

	/* ─────────────────────────── CRUD ─────────────────────────── */

	public function insert_invite(
		int    $exhibitor_id,
		string $email,
		string $token,
		int    $co_id  = 0,
		string $status = 'pending'
	): int {

		global $wpdb;
		$wpdb->insert(
			$this->table,
			[
				'exhibitor_id'  => $exhibitor_id,
				'coemail'       => $email,
				'co_id'         => $co_id,
				'status'        => $status,
				'token'         => $token,
				'date_invited'  => current_time( 'mysql', true ),
			],
			[ '%d','%s','%d','%s','%s','%s' ]
		);
		return (int) $wpdb->insert_id;
	}

	public function set_status( int $id, string $status ): void {
		global $wpdb;
		$wpdb->update(
			$this->table,
			[
				'status'         => $status,
				'date_responded' => current_time( 'mysql', true ),
			],
			[ 'id' => $id ],
			[ '%s','%s' ],
			[ '%d' ]
		);
	}

	public function delete_invite( int $id ): void {
		global $wpdb;
		$wpdb->delete( $this->table, [ 'id' => $id ], [ '%d' ] );
	}

	public function assign_stand( int $id, string $stand ): void {
		global $wpdb;
		$wpdb->update(
			$this->table,
			[ 'stand_code' => $stand ],
			[ 'id' => $id ],
			[ '%s' ],
			[ '%d' ]
		);
	}

	/* ────────────────────────── Look‑ups ───────────────────────── */

	public function get_invite_by_token( string $token ): ?object {
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$this->table} WHERE token = %s", $token )
		) ?: null;
	}

	/**
	 * Latest invite row (if any) for a given exhibitor & e‑mail.
	 */
	public function get_by_exhibitor_and_email( int $exhibitor_id, string $email ): ?object {
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table}
				   WHERE exhibitor_id = %d AND coemail = %s
				ORDER BY id DESC LIMIT 1",
				$exhibitor_id,
				$email
			)
		) ?: null;
	}

	/**
	 * Count invitations currently in “pending” or “accepted” state.
	 */
	public function count_used( int $exhibitor_id ): int {
		global $wpdb;
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table}
				 WHERE exhibitor_id = %d
				   AND status IN ( 'pending', 'accepted' )",
				$exhibitor_id
			)
		);
	}
}
