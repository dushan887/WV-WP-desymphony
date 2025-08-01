<?php
namespace Desymphony\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Repository for the wv_products table.
 */
class DS_Products_Repository extends DS_Base_Repository {

    protected function get_table_name(): string {
        return $this->wpdb->prefix . 'wv_products';
    }

    /* ---------- Basic CRUD ---------- */

    public function get_products_by_exhibitor( int $exhibitor_id ): array {
        $sql = $this->prepare(
            "SELECT * FROM {$this->get_table_name()}
             WHERE exhibitor_id = %d
             ORDER BY date_created DESC",
            $exhibitor_id
        );

        return $this->wpdb->get_results( $sql ) ?: [];
    }

    public function get_product_for_user( int $id, int $exhibitor_id ) {
        $sql = $this->prepare(
            "SELECT * FROM {$this->get_table_name()}
             WHERE id = %d AND exhibitor_id = %d",
            $id,
            $exhibitor_id
        );

        return $this->wpdb->get_row( $sql ) ?: null;
    }

    public function user_owns_product( int $id, int $exhibitor_id ): bool {
        return (bool) $this->wpdb->get_var(
            $this->prepare(
                "SELECT COUNT(*) FROM {$this->get_table_name()}
                 WHERE id = %d AND exhibitor_id = %d",
                $id,
                $exhibitor_id
            )
        );
    }

    public function insert_product( array $fields ): int {
        $ok = $this->wpdb->insert( $this->get_table_name(), $fields );

        return $ok ? (int) $this->wpdb->insert_id : 0;
    }

    public function update_product( int $id, int $exhibitor_id, array $fields ): bool {
        return false !== $this->wpdb->update(
            $this->get_table_name(),
            $fields,
            [ 'id' => $id, 'exhibitor_id' => $exhibitor_id ],
            null,
            [ '%d', '%d' ]
        );
    }

    public function delete_product( int $id, int $exhibitor_id ): bool {
        return false !== $this->wpdb->delete(
            $this->get_table_name(),
            [ 'id' => $id, 'exhibitor_id' => $exhibitor_id ],
            [ '%d', '%d' ]
        );
    }
}
