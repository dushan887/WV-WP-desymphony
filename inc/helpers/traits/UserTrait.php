<?php

namespace Desymphony\Helpers;

trait UserTrait {

    /**
     * Return current WP_User object.
     */
    public static function current_user() {
        return wp_get_current_user();
    }

    /**
     * Return array of current user roles.
     */
    public static function current_user_roles(): array {
        $user = self::current_user();
        return (array) $user->roles;
    }

    /**
     * Return the first role of the current user, or false if none.
     */
    public static function current_user_role() {
        $roles = self::current_user_roles();
        return $roles ? reset( $roles ) : false;
    }

    /**
     * Role checks.
     */
    public static function is_administrator(): bool {
        return in_array( 'administrator', self::current_user_roles(), true );
    }

    public static function is_exhibitor( int $user_id = 0 ): bool {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        $user = get_userdata( $user_id );
        return $user && in_array( 'exhibitor', (array) $user->roles, true );
    }

    public static function is_buyer( int $user_id = 0 ): bool {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        $user = get_userdata( $user_id );
        return $user && in_array( 'buyer', (array) $user->roles, true );
    }

    public static function is_visitor( int $user_id = 0 ): bool {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        $user = get_userdata( $user_id );
        return $user && in_array( 'visitor', (array) $user->roles, true );
    }

    /** Possible scope values */
    private const SCOPES = [ 'all', 'exhibitors', 'buyers_visitors' ];

    /**
     * Return the current admin’s scope string.
     * Defaults to `all` for super‑admins.
     */
    public static function current_admin_scope(): string {
        if ( self::is_administrator() ) {
            // WP admins are always full‑access
            return 'all';
        }
        $u   = self::current_user();
        $raw = get_user_meta( $u->ID, 'wv_admin_scope', true ) ?: 'buyers_visitors';
        return in_array( $raw, self::SCOPES, true ) ? $raw : 'buyers_visitors';
    }

    /**
     * Decide if the **current user** may *manage* the `$target_user`.
     * (Read AND write – the UI calls this for row visibility and for edits.)
     */
    public static function can_manage_user( \WP_User $target_user ): bool {
        // Full WordPress admins may always manage.
        if ( self::is_administrator() ) { return true; }
        $scope = self::current_admin_scope();
        if ( $scope === 'all' ) { return true; }
        $target_role = reset( $target_user->roles );
        switch ( $scope ) {
            case 'exhibitors':
                return $target_role === 'exhibitor';
            case 'buyers_visitors':
                return in_array( $target_role, [ 'buyer', 'visitor' ], true );
            default:
                return false;
        }
    }

    public static function get_user_names(int $user_id = 0): string {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $first = get_user_meta($user_id, 'wv_firstName', true) ?: '';
        $last  = get_user_meta($user_id, 'wv_lastName', true) ?: '';
        return trim("$first $last");
    }

    // Verification
    public static function is_admin_verified(int $user_id = 0): bool {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        return get_user_meta($user_id, 'wv_admin_verified', true) === '1';
    }

    public static function get_status(int $user_id = 0): string {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        // 'Pending', 'Active', 'Disabled'
        return get_user_meta($user_id, 'wv_status', true) ?: 'Pending';
    }

    public static function is_ex_stage1_verified(int $user_id = 0): bool {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        return get_user_meta($user_id, 'wv_ex_stage1_verified', true) === '1';
    }

    public static function is_ex_stage2_verified(int $user_id = 0): bool {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        return get_user_meta($user_id, 'wv_ex_stage2_verified', true) === '1';
    }

    public static function has_reserved_stand(int $user_id = 0): bool {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        // must match the key in EDITABLE_META / JS / badges
        return get_user_meta($user_id, 'has_reserved_stand', true) === '1';
    }

    public static function get_wvhb_support(int $user_id = 0): string {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        // 'Category IV','Category III', 'Category II', 'Category I', 'NONE'
        $value = get_user_meta($user_id, 'wv_wvhb_support', true);
        return $value !== '' ? $value : 'NONE';
    }

    /**
     * Get â€œcompany nameâ€ from meta or fallback to first+last name.
     */
    public static function get_company_name( int $user_id = 0 ): string {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        $name = get_user_meta( $user_id, 'wv_company_name', true );
        if ( ! $name ) {
            $user  = get_userdata( $user_id );
            $first = $user->first_name;
            $last  = $user->last_name;
            $name  = trim( "$first $last" );
        }
        return $name ?: '';
    }

    public static function get_user_category( int $user_id = 0 ): string {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        return get_user_meta( $user_id, 'wv_userCategory', true ) ?: '';
    }

}