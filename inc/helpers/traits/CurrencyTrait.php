<?php

namespace Desymphony\Helpers;

trait CurrencyTrait {

    /**
     * Convert a EUR amount to RSD using the stored rate.
     */
    public static function eur_to_rsd( ?float $eur = null ) : float {
        $rate_raw = get_option( 'ds_eur_to_rsd_rate', '' );
        $rate     = $rate_raw !== '' ? (float) $rate_raw : 117.5315;
        return $eur === null ? $rate : round( $eur * $rate, 2 );
    }

}