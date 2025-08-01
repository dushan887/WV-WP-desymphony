<?php
namespace Desymphony\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Repository class for the 'wv_favorites' table.
 */
class DS_Favorites_Repository extends DS_Base_Repository {

    /**
     * Return the table name (with prefix).
     */
    protected function get_table_name(): string {
        return $this->wpdb->prefix . 'wv_favorites';
    }

    /**
     * Add a favorite row. Returns true if inserted or already exists, else false.
     */
    public function add_favorite( int $user_id, string $target_type, int $target_id ): bool {
        $table = $this->get_table_name();

        // Check if already favorited
        $sql = $this->prepare("
            SELECT id 
            FROM $table 
            WHERE user_id = %d
              AND target_type = %s
              AND target_id = %d
        ", $user_id, $target_type, $target_id);
        $exists = $this->wpdb->get_var( $sql );
        if ( $exists ) {
            return true; // Already favorited
        }

        // Insert a new favorite
        $inserted = $this->wpdb->insert(
            $table,
            [
                'user_id'     => $user_id,
                'target_type' => $target_type,
                'target_id'   => $target_id,
                'date_added'  => current_time('mysql'),
            ],
            [ '%d','%s','%d','%s' ]
        );
        return (bool) $inserted;
    }

    /**
     * Remove a favorite row. Returns true if deleted, else false.
     */
    public function remove_favorite( int $user_id, string $target_type, int $target_id ): bool {
        $table = $this->get_table_name();
        $deleted = $this->wpdb->delete(
            $table,
            [
                'user_id'     => $user_id,
                'target_type' => $target_type,
                'target_id'   => $target_id
            ],
            [ '%d','%s','%d' ]
        );
        return (bool) $deleted;
    }

    /**
     * Check if user has this item favorited.
     */
    public function is_favorited( int $user_id, string $target_type, int $target_id ): bool {
        $table = $this->get_table_name();
        $sql   = $this->prepare("
            SELECT id 
            FROM $table 
            WHERE user_id = %d
              AND target_type = %s
              AND target_id = %d
        ", $user_id, $target_type, $target_id);

        $exists = $this->wpdb->get_var( $sql );
        return (bool) $exists;
    }

    /**
     * Fetch a user's favorites. If $target_type is provided, filter by that type.
     */
    public function get_user_favorites( int $user_id, ?string $target_type = null ): array {
        $table = $this->get_table_name();
        if ( $target_type ) {
            $sql = $this->prepare("
                SELECT * 
                FROM $table 
                WHERE user_id = %d 
                  AND target_type = %s 
                ORDER BY date_added DESC
            ", $user_id, $target_type );
        } else {
            $sql = $this->prepare("
                SELECT * 
                FROM $table 
                WHERE user_id = %d 
                ORDER BY date_added DESC
            ", $user_id );
        }
        $rows = $this->wpdb->get_results( $sql );
        return $rows ?: [];
    }
}
