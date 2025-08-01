<?php

namespace Desymphony\Helpers;

trait ExhibitorTrait {

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

}