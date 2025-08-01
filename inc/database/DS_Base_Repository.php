<?php
namespace Desymphony\Database;

use wpdb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Base class for repository classes that wrap $wpdb queries.
 * You can extend this class in other repositories (Favorites, Products, etc.).
 */
abstract class DS_Base_Repository {

    /** @var wpdb WordPress DB handle */
    protected $wpdb;

    /**
     * Constructor: store a reference to the global $wpdb.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Often you'll have a helper to get the table name.
     * We'll implement `get_table_name()` in child classes.
     *
     * @return string
     */
    abstract protected function get_table_name(): string;

    /**
     * For convenience, a quick method to check for last DB error.
     *
     * @return string
     */
    public function get_last_error(): string {
        return $this->wpdb->last_error;
    }

    /**
     * Similarly, a quick method to do $wpdb->prepare() with consistent usage.
     *
     * @param string $query
     * @param mixed  ...$args
     * @return string Prepared SQL
     */
    protected function prepare( string $query, ...$args ): string {

	    /* if called like  prepare($sql, [ $a, $b ])  →  flatten it */
        if ( count($args) === 1 && is_array($args[0]) ) {
            $args = $args[0];
        }
        return $this->wpdb->prepare( $query, ...$args );
    }


    
}