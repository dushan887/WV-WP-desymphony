<?php

namespace Desymphony\Helpers;

use Desymphony\Woo\DS_Woo_Stand_Cart;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * A collection of utilities for Desymphony.
 */

// Load traits
require_once __DIR__ . '/traits/CurrencyTrait.php';
require_once __DIR__ . '/traits/UserTrait.php';
require_once __DIR__ . '/traits/ExhibitorTrait.php';
require_once __DIR__ . '/traits/VisitorTrait.php';
require_once __DIR__ . '/traits/StandTrait.php';
require_once __DIR__ . '/traits/PageTrait.php';
require_once __DIR__ . '/traits/EmailTrait.php';

class DS_Utils {

    use CurrencyTrait;
    use UserTrait;
    use ExhibitorTrait;
    use VisitorTrait;
    use StandTrait;
    use PageTrait;
    use EmailTrait;

}