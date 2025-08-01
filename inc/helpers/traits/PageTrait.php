<?php

namespace Desymphony\Helpers;

trait PageTrait {

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

}