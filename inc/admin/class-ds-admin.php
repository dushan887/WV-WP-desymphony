<?php

namespace Desymphony\Admin;

use Desymphony\Admin\Menu\DS_Admin_Menu;
use Desymphony\Admin\Settings\DS_Admin_Auth_Pages;
use Desymphony\Admin\Settings\DS_Admin_Dashboard_Pages;
use Desymphony\Admin\Protection\DS_Admin_Auth_Page_Protection;
use Desymphony\Admin\Protection\DS_Admin_Dashboard_Page_Protection;
use Desymphony\Admin\Tables\DS_Admin_Tables;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DS_Admin {
    private string $version;

    public function __construct( string $version ) {
        $this->version = $version;

        // 1) Register menus & settings pages
        ( new DS_Admin_Menu() )->init();
        ( new DS_Admin_Auth_Pages() )->init();
        ( new DS_Admin_Dashboard_Pages() )->init();

        // 2) Protect assigned pages
        DS_Admin_Auth_Page_Protection::init();
        DS_Admin_Dashboard_Page_Protection::init();

        // 3) Create or migrate custom tables
        DS_Admin_Tables::init();

    }
}
