<?php

namespace Desymphony\Helpers;

trait VisitorTrait {

    public static function get_visitor_participation( int $user_id = 0 ): string {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        // If not visitor, return empty
        if ( ! self::is_visitor() ) {
            return '';
        }
        $participation = get_user_meta( $user_id, 'wv_participationModel', true );
        if ( ! in_array( $participation, [ 'Public Visitor', 'Company' ], true ) ) {
            $participation = 'Public Visitor'; // fallback
        }
        return $participation;
    }

    /**
     * Get the user’s WVHB-support category.
     *
     * Returns **'NONE'** when:
     *   – the user is not a Visitor / Buyer role, **or**
     *   – the dropdown is empty / set to “NONE”.
     * Otherwise returns the selected category (e.g. “Category II support”).
     */
    public static function wvhb_support_category( int $user_id = 0 ): string {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        /* role check -------------------------------------------------- */
        // $roles = (array) get_userdata( $user_id )->roles;
        // if ( ! in_array( 'visitor', $roles, true ) && ! in_array( 'buyer', $roles, true ) ) {
        //     return 'NONE';
        // }
        /* meta check -------------------------------------------------- */
        $value = trim( (string) get_user_meta( $user_id, 'wv_wvhb_support', true ) );
        if ( $value === '' || strtoupper( $value ) === 'NONE' ) {
            return 'NONE';
        }
        return $value;                 // e.g. “Category II support”
    }

    /**
     * True when the Visitor / Buyer has any WVHB-support category selected.
     */
    public static function has_wvhb_support( int $user_id = 0 ): bool {
        return self::wvhb_support_category( $user_id ) !== 'NONE';
    }

}