<?php

use Desymphony\Admin\Settings\DS_Woo_Stand_Import;

if (class_exists('\Desymphony\Admin\Settings\DS_Woo_Stand_Import')) {
    DS_Woo_Stand_Import::render_import_page();
} else {
    echo '<div class="notice notice-error">Stand import class not found.</div>';
}
