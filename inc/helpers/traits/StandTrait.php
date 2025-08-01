<?php

namespace Desymphony\Helpers;

use Desymphony\Woo\DS_Woo_Stand_Cart;

trait StandTrait {

    /**
     * Return every stand that is **assigned** to the user via DS_Stand_Assign.
     *
     * @param int  $user_id Optional. Defaults to the current user.
     * @param bool $group   When **true** (default) you get the result grouped
     *                      by hall slug – same structure used elsewhere:
     *                      [ '3' => [ [pid,hall,no,size], … ], … ]
     *                      Set **false** to get a single flat list instead.
     *
     * @return array
     */
    public static function get_assigned_stands( int $user_id = 0,
                                               bool $group = true ) : array {
        $user_id = $user_id ?: get_current_user_id();
        if ( ! $user_id ) {
            return [];
        }
        /* Re‑use the logic we just added to DS_Woo_Stand_Cart */
        $by_hall = DS_Woo_Stand_Cart::get_assigned_stands_by_hall( $user_id );
        if ( $group ) {
            return $by_hall;                       // grouped structure
        }
        // flatten into one array
        $flat = [];
        foreach ( $by_hall as $rows ) {
            foreach ( $rows as $row ) {
                $flat[] = $row;
            }
        }
        return $flat;
    }

    /**
     * All stand codes (“3/22”) the user occupies.
     *
     * @return string[]  Empty ⇢ user has no stand yet.
     */
    public static function get_assigned_stand_codes( int $user_id ) : array {
        $flat = self::get_assigned_stands( $user_id, false );   // existing helper
        $out  = [];
        foreach ( $flat as $st ) {
            $hall = $st['hall'] ?: get_post_meta( $st['pid'], 'wv_hall_only', true );
            $no   = $st['no']   ?: get_post_meta( $st['pid'], 'wv_stand_no',  true );
            if ( $hall && $no ) $out[] = "{$hall}/{$no}";
        }
        return $out;
    }

    /** First stand code or '' */
    public static function first_assigned_stand_code( int $user_id ) : string {
        return self::get_assigned_stand_codes( $user_id )[0] ?? '';
    }

}