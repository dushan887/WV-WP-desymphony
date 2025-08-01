<?php
namespace Desymphony\Admin\Settings;

if (!defined('ABSPATH')) exit;

class DS_Woo_Stand_Import {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
    }

    public static function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            'Import Stands from CSV',
            'Import Stands',
            'manage_woocommerce',
            'import-stands',
            [__CLASS__, 'render_import_page']
        );
    }

    public static function render_import_page() {
        ?>
        <div class="wrap">
            <h1>Import Stands from CSV</h1>
            <?php if (empty($_POST['wv_import_stands_continue'])): // Only show file input on first load ?>
                <form method="post" enctype="multipart/form-data">
                    <p>
                        <input type="file" name="wv_import_csv" accept=".csv" required />
                        <input type="submit" name="wv_import_stands" class="button button-primary" value="Import Now" />
                    </p>
                </form>
            <?php endif; ?>
        </div>
        <?php

        // Normal first upload
        if (isset($_POST['wv_import_stands']) && isset($_FILES['wv_import_csv']) && $_FILES['wv_import_csv']['error'] === UPLOAD_ERR_OK) {
            $uploaded_file = $_FILES['wv_import_csv'];
            $ext = pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) !== 'csv') {
                echo '<div class="notice notice-error">Invalid file type. Please upload a CSV file.</div>';
                return;
            }
            $tmp_path = wp_tempnam($uploaded_file['name']);
            if (!$tmp_path || !move_uploaded_file($uploaded_file['tmp_name'], $tmp_path)) {
                echo '<div class="notice notice-error">File error.</div>';
                return;
            }
            self::process_import($tmp_path, 0);
        }
        // Continue import chunk
        elseif (isset($_POST['wv_import_stands_continue']) && !empty($_POST['wv_import_csv_path'])) {
            $tmp_path = $_POST['wv_import_csv_path'];
            $offset = isset($_POST['wv_import_offset']) ? intval($_POST['wv_import_offset']) : 0;
            self::process_import($tmp_path, $offset);
        }
    }


    public static function process_import($file_path) {
        $chunk_size = 25; // Number of rows per request
        $offset = isset($_POST['wv_import_offset']) ? intval($_POST['wv_import_offset']) : 0;

        if (($handle = fopen($file_path, 'r')) !== false) {
            $header = fgetcsv($handle);
            // Seek to offset
            if ($offset > 0) {
                for ($i = 0; $i < $offset; $i++) {
                    if (!fgetcsv($handle)) break;
                }
            }

            $imported = 0;
            $updated = 0;
            $processed = 0;

            while (($row = fgetcsv($handle)) !== false && $processed < $chunk_size) {
                $data = array_combine($header, $row);

                if (empty($data['SKU']) || empty($data['Name'])) continue;

                $product_id = wc_get_product_id_by_sku($data['SKU']);
                if ($product_id) {
                    $product = wc_get_product($product_id);
                    $updated++;
                } else {
                    $product = new \WC_Product_Simple();
                    $product->set_sku($data['SKU']);
                    $product->set_stock_quantity($data['Stock'] ?: 1);
                    $product->set_manage_stock(true);
                    $product->set_catalog_visibility('visible');
                    $product->set_status('publish');
                    $imported++;
                }

                $product->set_name($data['Name']);
                $product->set_price(floatval($data['Regular price']));
                $product->set_regular_price(floatval($data['Regular price']));
                $product->set_category_ids([self::get_stand_category_id()]);
                $product->save();
                $product_id = $product->get_id();

                if (!empty($data['Attribute 1 name']) && !empty($data['Attribute 1 value(s)'])) {
                    self::add_product_attribute($product_id, $data['Attribute 1 name'], $data['Attribute 1 value(s)']);
                }
                if (!empty($data['Attribute 2 name']) && !empty($data['Attribute 2 value(s)'])) {
                    self::add_product_attribute($product_id, $data['Attribute 2 name'], $data['Attribute 2 value(s)']);
                }

                foreach ($data as $key => $value) {
                    if (strpos($key, 'Meta:wv_') === 0 && $value !== '') {
                        $meta_key = strtolower(str_replace('Meta:', '', $key));
                        update_post_meta($product_id, $meta_key, $value);
                    }
                }
                $processed++;
            }
            $has_more = !feof($handle); // <--- check BEFORE fclose
            fclose($handle);

            echo '<div class="notice notice-success">Processed ' . $processed . ' stands. Imported: ' . $imported . ', updated: ' . $updated . '.</div>';

            // Show Continue button if not finished
            if ($has_more) {
                $next_offset = $offset + $processed;
                ?>
                <form method="post">
                    <input type="hidden" name="wv_import_csv_path" value="<?php echo esc_attr($file_path); ?>">
                    <input type="hidden" name="wv_import_offset" value="<?php echo esc_attr($next_offset); ?>">
                    <input type="submit" name="wv_import_stands_continue" class="button button-primary" value="Continue Import" />
                </form>
                <?php
            } else {
                echo '<div class="notice notice-success">Import complete!</div>';
            }

        } else {
            echo '<div class="notice notice-error">Could not open CSV file.</div>';
        }
    }


    // Helper for getting the "Stand" category ID
    private static function get_stand_category_id() {
        $term = get_term_by('slug', 'stand', 'product_cat');
        return $term ? $term->term_id : 0;
    }

    // Helper to add attribute term to product
    private static function add_product_attribute($product_id, $attr_name, $attr_value) {
        $taxonomy = 'pa_' . sanitize_title($attr_name);

        // Register attribute taxonomy if it doesn't exist
        if (!taxonomy_exists($taxonomy)) {
            register_taxonomy(
                $taxonomy,
                'product',
                array(
                    'label' => ucfirst($attr_name),
                    'public' => true,
                    'hierarchical' => false,
                    'show_ui' => false,
                    'query_var' => true,
                )
            );
        }

        // Ensure the term exists
        if (!term_exists($attr_value, $taxonomy)) {
            wp_insert_term($attr_value, $taxonomy);
        }
        wp_set_object_terms($product_id, $attr_value, $taxonomy, true);

        // Save as product attribute (so it shows in Woo admin)
        $product = wc_get_product($product_id);
        $attributes = $product->get_attributes();

        $attribute = new \WC_Product_Attribute();
        $attribute->set_id(0);
        $attribute->set_name($taxonomy);
        $attribute->set_options([$attr_value]);
        $attribute->set_position(0);
        $attribute->set_visible(1);
        $attribute->set_variation(0);
        $attributes[$taxonomy] = $attribute;

        $product->set_attributes($attributes);
        $product->save();
    }

}