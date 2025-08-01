<?php
namespace Desymphony\Helpers;

use Desymphony\Woo\DS_Woo_Stand_Cart;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A collection of utilities for Desymphony.
 */
class DS_Utils {

    /**
     * Convert a EUR amount to RSD using the stored rate.
     */
    public static function eur_to_rsd( ?float $eur = null ) : float {
		$rate_raw = get_option( 'ds_eur_to_rsd_rate', '' );
		$rate     = $rate_raw !== '' ? (float) $rate_raw : 117.5315;

		return $eur === null ? $rate : round( $eur * $rate, 2 );
	}

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


    /**
     * Rich DTO for every invite row – drives the dashboard list.
     *
     * @return array[]
     *   [
     *     id            => (int)   Row ID in wv_exhibitor_links
     *     email         => (string)Invitee e‑mail
     *     invited_ago   => (string)"2 days ago"
     *     status        => (string)pending | accepted | declined
     *     registered    => (bool)  true if WP user created
     *     date_registered=> (string)"13 Jun 2025" | ''
     *     stand         => (string)"2C/22" | ''
     *     company       => (string)Invitee company name (only when registered)
     *     avatar        => (string)Full URL of wv_user‑logo or placeholder
     *   ]
     */
    public static function get_coexhibitor_invites( int $user_id ): array {

        global $wpdb;
        $table = $wpdb->prefix . 'wv_exhibitor_links';

        // Exhibitor sees *their* invites; Co‑Exhibitor sees the one that links to them
        $where_sql = self::is_exhibitor()
            ? $wpdb->prepare( 'exhibitor_id = %d', $user_id )
            : $wpdb->prepare( 'co_id = %d',        $user_id );

        $rows = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY date_invited ASC"
        );

        $dto  = [];
        foreach ( $rows as $row ) {

            $co_id      = (int) $row->co_id;
            $registered = $co_id > 0;

            /* ---------- figure out the avatar ---------- */
            $avatar = 'https://placehold.co/120?text=%20';          // sane default
            if ( $registered ) {
                $logo = get_user_meta( $co_id, 'wv_user-logo', true );
                if ( $logo ) {
                    $avatar = esc_url_raw( $logo );
                }
            }

            $item = [
                'id'              => (int) $row->id,
                'email'           => $row->coemail,
                'invited_ago'     => human_time_diff( strtotime( $row->date_invited ),
                                                    current_time( 'timestamp' ) ) . ' ago',
                'status'          => $row->status,                // pending | accepted | declined
                'registered'      => $registered,
                'date_registered' => $registered && $row->date_responded
                    ? mysql2date( 'd M Y', $row->date_responded )
                    : '',
                'stand'           => $row->stand_code ?: '',
                'avatar'          => $avatar,                     // ← always present!
            ];

            if ( $registered ) {
                $item['company'] = self::get_company_name( $co_id );
            }

            $dto[] = $item;
        }

        return $dto;
    }


    	/**
	 * Compute invitation limits and usage.
	 *
	 *  • **Head Exhibitor** → unlimited (slots = -1)  
	 *  • **Solo Exhibitor** → 0 / 1 / 2 based on stand size (24 m² or 49 m²)  
	 *  • Others             → 0
	 *
	 * “Used” = **pending + accepted**. Declined / expired rows free the slot.
	 *
	 * @return array ['slots'=>int, 'used'=>int, 'remaining'=>int]
	 */
    public static function get_coexhibitor_slots( int $user_id ): array {

        $participation = self::get_exhibitor_participation( $user_id );

        if ( $participation === 'Head Exhibitor' ) {
            $limit = -1;                       // unlimited
        } elseif ( $participation === 'Solo Exhibitor' ) {
            $limit = (int) get_user_meta( $user_id, 'wv_coex_slots_purchased', true );
        } else {
            $limit = 0;
        }

        $invites = self::get_coexhibitor_invites( $user_id );
        $used    = 0;
        foreach ( $invites as $i ) {
            if ( in_array( $i['status'], [ 'pending', 'accepted' ], true ) ) {
                $used++;
            }
        }

        return [
            'slots'     => $limit,
            'used'      => $used,
            'remaining' => $limit === -1 ? -1 : max( 0, $limit - $used ),
        ];
    }


	/* ──────────────────────────────────────────────────────────────
	 *  Private helpers
	 * ─────────────────────────────────────────────────────────── */

	/**
	 * Solo‑Exhibitor invite allowance derived from purchased stand size.
	 */
	private static function solo_ex_invite_limit( int $user_id ): int {
		return match ( self::solo_ex_purchased_stand_size( $user_id ) ) {
			24 => 1,
			49 => 2,
			default => 0,
		};
	}

	/* ========================================================================
    *  Solo‑Exhibitor stand‑size detection (24 m² → 1 slot | 49 m² → 2 slots)
    * ===================================================================== */
    private static function solo_ex_purchased_stand_size( int $user_id ): int {
        global $wpdb;

        /* --------------------------------------------------------------
        * Get parent‑product IDs that are really booked by this user:
        *   – wv_reservation_user = $user_id
        *   – wv_stand_status     ∈ { reserved | sold }
        * ----------------------------------------------------------- */
        $ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT pm1.post_id
            FROM {$wpdb->postmeta} pm1
            JOIN {$wpdb->postmeta} pm2
                ON pm2.post_id = pm1.post_id
            WHERE pm1.meta_key   = 'wv_reservation_user'
                AND pm1.meta_value = %d
                AND pm2.meta_key   = 'wv_stand_status'
                AND pm2.meta_value IN ( 'sold', 'reserved' )",
            $user_id
        ) );

        if ( empty( $ids ) ) {
            return 0;                         // no stands booked
        }

        $max = 0;                             // remember the largest size we see

        foreach ( $ids as $pid ) {
            $prod = wc_get_product( $pid );
            if ( ! $prod ) {
                continue;
            }

            /* 1. try the normal WC attribute helper ------------------- */
            $raw = $prod->get_attribute( 'pa_stand-size' );

            /* 2. fall back to low‑level meta (for badly imported posts) */
            if ( $raw === '' ) {
                $raw = get_post_meta( $pid, 'attribute_pa_stand-size', true )
                    ?: get_post_meta( $pid, 'pa_stand-size',          true );
            }

            /* 3. if the value is an array serialised by WC, flatten it */
            if ( is_array( $raw ) ) {
                $raw = implode( '|', $raw );
            }

            /* 4. extract all numbers and pick the biggest ------------- */
            if ( preg_match_all( '/\d+/', (string) $raw, $m ) ) {
                foreach ( $m[0] as $n ) {
                    $n = (int) $n;
                    if ( in_array( $n, [ 24, 49 ], true ) ) {
                        $max = max( $max, $n );
                        if ( $max === 49 ) {           // cannot get larger
                            break 2;                   // break both loops
                        }
                    }
                }
            }
        }

        return $max >= 49 ? 49 : ( $max >= 24 ? 24 : 0 );
    }


    public static function get_and_clear_flash( string $key ): string {
        $msg = get_transient( "ds_coex_flash_{$key}" );
        if ( $msg ) {
            delete_transient( "ds_coex_flash_{$key}" );
        }
        return (string) $msg;
    }

    /**
     * Solo‑Ex maximum slot allowance derived from stand size.
     * 0 (≤ 23 m²), 1 (24 m²), 2 (49 m² or larger).
     */
    public static function solo_ex_max_slots( int $user_id ): int {
        return match ( self::solo_ex_purchased_stand_size( $user_id ) ) {
            49 => 2,
            24 => 1,
            default => 0,
        };
    }

    public static function solo_ex_remaining_slots( int $user_id = 0 ): int {
        if ( ! $user_id ) $user_id = get_current_user_id();
        $max = self::solo_ex_max_slots( $user_id );                       // 0 | 1 | 2
        $p   = (int) get_user_meta( $user_id, 'wv_coex_slots_purchased', true );
        return max( 0, $max - $p );
    }

    /**
     * True when the current page is an authentication page.
     */
    public static function is_auth_page(): bool {
        return is_page( 'wv-auth' ) || is_page( 'wv-register' );
    }

    /**
     * True when the current page is the products page.
     */
    public static function is_products_page(): bool {
        return is_page( 'wv-products' );
    }

    /**
     * True when the current page is the application page.
     */
    public static function is_application_page(): bool {
        return is_page( 'wv-application' );
    }

  
	public static function get_exhibitor_participation( int $user_id = 0 ): string {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		// If not exhibitor/admin, return empty
		if ( ! self::is_exhibitor() && ! self::is_administrator() ) {
			return '';
		}

		$participation = get_user_meta( $user_id, 'wv_participationModel', true );
		if ( ! in_array( $participation, [ 'Solo Exhibitor', 'Head Exhibitor', 'Co-Exhibitor' ], true ) ) {
			$participation = 'Solo Exhibitor'; 
		}
		return $participation;
	}

    public static function is_co_exhibitor( int $user_id ): bool {
       if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		// If not exhibitor/admin, return empty
		if ( ! self::is_exhibitor() && ! self::is_administrator() ) {
			return '';
		}

		$participation = get_user_meta( $user_id, 'wv_participationModel', true );
		
        return $participation === 'Co-Exhibitor';
    }

    
    public static function get_exhibitor_category( int $user_id = 0 ): string {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        if ( ! self::is_exhibitor() ) {
            return '';
        }
        return get_user_meta( $user_id, 'wv_userCategory', true ) ?: '';
    }

    public static function get_exhibitor_field( int $user_id = 0 ): string {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        if ( ! self::is_exhibitor() ) {
            return '';
        }
        // ['Wine', 'Spirits', 'Food']
        return get_user_meta( $user_id, 'wv_fieldOfWork', true ) ?: '';
    }

    public static function get_user_category( int $user_id = 0 ): string {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        return get_user_meta( $user_id, 'wv_userCategory', true ) ?: '';
    }

    public static function is_exhibiting_products( int $user_id = 0 ): bool {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
        if ( ! self::is_exhibitor() ) {
            return false;
        }
        $value = get_user_meta( $user_id, 'wv_exhibitingProducts', true );
        return strtolower( trim( $value ) ) === 'yes';
    }

    /**
	 * True when the current (or given) exhibitor already added ≥ 1 product.
	 */
	public static function exhibitor_has_products( int $user_id = 0 ): bool {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		if ( ! self::is_exhibitor() && ! self::is_administrator() ) {
			return false;                       // buyers / visitors, etc.
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wv_products';

		$count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE exhibitor_id = %d",
				$user_id
			)
		);
		return $count > 0;
	}

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

    // 1) wherever you need all assigned stands, grouped by hall
    // $my_stands_by_hall = Utils::get_assigned_stands();

    // 2) get a flat list (no hall keys)
    // $my_stands_flat = Utils::get_assigned_stands( 0, false );

    // 3) fetch for another user (e.g. admin screen)
    // $coex_stands = Utils::get_assigned_stands( $coex_user_id );


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



  
    
    /**
     * Build a Wine Vision transactional e-mail (2025 design system).
     *
     * $header = [
     *   'title'       => 'Dear Exhibitor',
     *   'bg'          => '#6e0fd7',
     *   'logo_variant'=> 'B',                      // 'W' (default) or 'B'
     *   // optionally override:
     *   // 'logo'   => 'https://…/custom.png',
     * ];
     *
     * $main   = [ … ]  // unchanged (see previous message)
     */
    public static function email_template(
        string $subject,
        array  $header,
        array  $main,
        string $footer = ''
    ): array {

        /* ── logo (auto) ─────────────────────────────────────────────── */
        $logo = $header['logo'] ?? null;

        if ( ! $logo ) {
            $variant = strtoupper( $header['logo_variant'] ?? 'W' );   // W or B
            $logo    = $variant === 'B'
                ? 'https://winevisionfair.com/wp-content/uploads/2025/06/WV25_E-mail_H_logo_B.png'
                : 'https://winevisionfair.com/wp-content/uploads/2025/06/WV25_E-mail_H_logo_W.png';
                // Set titleColor based on logo variant
                $titleColor = '#fff'; // default white
                if ($variant === 'B') {
                    $titleColor = '#000';
                }
                $header['titleColor'] = $titleColor;
        }

        /* ── minimal CSS (unchanged) ─────────────────────────────────── */
        $css = '
        body{margin:0;padding:0;background:#fff;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
        a{color:inherit;text-decoration:none}
        h1{margin:0;font-size:28px;line-height:34px;font-weight:700}
        @media(prefers-color-scheme:dark){body{background:#000;color:#ddd}}';

        /* ── build HTML ──────────────────────────────────────────────── */
        $html = '
    <!doctype html><html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>'.esc_html($subject).'</title>
    <style>'.preg_replace('/\s+/',' ',$css).'</style>
    </head><body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
    <tr><td align="center" style="padding:24px 12px;">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:100%;border-radius:16px;overflow:hidden;">
        <!-- header bar -->
        <tr>
            <td bgcolor="'.esc_attr($header['bg']).'" style="padding:20px 28px;color:'.esc_attr($header['titleColor']).';font-size:22px;font-weight:500;">'
                .wp_kses_post($header['title']).'
            </td>
            <td bgcolor="'.esc_attr($header['bg']).'" align="right" style="padding:20px 28px;">
            <img src="'.esc_url($logo).'" alt="" width="30" style="display:block;border:0;outline:none;">
            </td>
        </tr>

        <!-- main content -->
        <tr><td colspan="2" bgcolor="#e7e6e8" style="padding:32px;color:#0b051c;">
            <h1 style="margin-bottom: 24px">'.wp_kses_post($main['title']).'</h1>'
            .wp_kses_post($main['html']).'

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:40px 0 0;">
            <tr>
                <td width="50%" style="font-size:12px;line-height:18px; padding-right: 8px;">'.wp_kses_post($main['note']).'</td>
                <td align="right" width="50%" style="padding-left:8px;">
                <a href="'.esc_url($main['btn_link']).'"
                    style="display:inline-block;padding:12px 24px;font-size:14px;line-height:20px;font-weight:600;
                            border-radius:28px;background:'.esc_attr($main['btn_bg']).';
                            color:'.esc_attr($main['btn_text_color']).';">'
                    .esc_html($main['btn_text']).'
                </a>
                </td>
            </tr>
            </table>'
            .($footer ? '<div style="margin-top:32px;">'.wp_kses_post($footer).'</div>' : '').'
        </td></tr>

        <!-- static footer -->
        <tr><td colspan="2" bgcolor="#0b051c" style="padding:24px 32px;color:#ffffff;font-size:12px;line-height:18px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%">
                <img src="https://winevisionfair.com/wp-content/uploads/2025/06/WV25_E-mail_F_logo_W.png" alt="WINE VISION" width="150" style="display:block;border:0;outline:none;">
                </td>
                <td><strong>November 22-25, 2025</strong><br><span style="color:#e7e6e8;">Belgrade Fair, Serbia</span></td>
                <td align="right"><a href="https://winevisionfair.com" style="color:#ffffff;font-weight:600;">winevisionfair.com</a><br><span style="color:#e7e6e8;">Exhibit · Trade · Visit</span></td>
            </tr>
            </table>
        </td></tr>
        </table>
    </td></tr>
    </table>
    </body></html>';

        return [$subject, $html];
    }


}