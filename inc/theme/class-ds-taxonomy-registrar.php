<?php
/**
 * Register Custom Taxonomies: WV Year, WV Day, WV Event Category
 *
 * @package Desymphony
 */

namespace Desymphony\Theme;

defined( 'ABSPATH' ) || exit;

class DS_Taxonomy_Registrar {

    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_taxonomies' ], 0 );
    }

    public static function register_taxonomies() {
        // 1) WV Year (shared by all CPTs)
        $labels_year = [
            'name'                       => __( 'WV Years', DS_THEME_TEXTDOMAIN ),
            'singular_name'              => __( 'WV Year', DS_THEME_TEXTDOMAIN ),
            'menu_name'                  => __( 'WV Years', DS_THEME_TEXTDOMAIN ),
            'all_items'                  => __( 'All Years', DS_THEME_TEXTDOMAIN ),
            'edit_item'                  => __( 'Edit Year', DS_THEME_TEXTDOMAIN ),
            'update_item'                => __( 'Update Year', DS_THEME_TEXTDOMAIN ),
            'add_new_item'               => __( 'Add New Year', DS_THEME_TEXTDOMAIN ),
            'new_item_name'              => __( 'New Year Name', DS_THEME_TEXTDOMAIN ),
            'search_items'               => __( 'Search Years', DS_THEME_TEXTDOMAIN ),
            'not_found'                  => __( 'No years found', DS_THEME_TEXTDOMAIN ),
        ];

        register_taxonomy(
            'wv_year',
            [ 'wv_event', 'wv_card', 'wv_lecturer', 'wv_podcast' ],
            [
                'labels'            => $labels_year,
                'hierarchical'      => true,
                'public'            => false,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'rewrite'           => false,
            ]
        );
        self::add_default_year_terms();

        // 2) WV Day (for Events)
        $labels_day = [
            'name'               => __( 'WV Days', DS_THEME_TEXTDOMAIN ),
            'singular_name'      => __( 'WV Day', DS_THEME_TEXTDOMAIN ),
            'menu_name'          => __( 'WV Days', DS_THEME_TEXTDOMAIN ),
            'all_items'          => __( 'All Days', DS_THEME_TEXTDOMAIN ),
            'edit_item'          => __( 'Edit Day', DS_THEME_TEXTDOMAIN ),
            'update_item'        => __( 'Update Day', DS_THEME_TEXTDOMAIN ),
            'add_new_item'       => __( 'Add New Day', DS_THEME_TEXTDOMAIN ),
            'new_item_name'      => __( 'New Day Name', DS_THEME_TEXTDOMAIN ),
            'search_items'       => __( 'Search Days', DS_THEME_TEXTDOMAIN ),
            'not_found'          => __( 'No days found', DS_THEME_TEXTDOMAIN ),
        ];

        register_taxonomy(
            'wv_day',
            [ 'wv_event' ],
            [
                'labels'            => $labels_day,
                'hierarchical'      => false,
                'public'            => false,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'rewrite'           => false,
            ]
        );

        // 3) WV Event Category (for Events)
        $labels_event_cat = [
            'name'               => __( 'WV Event Categories', DS_THEME_TEXTDOMAIN ),
            'singular_name'      => __( 'WV Event Category', DS_THEME_TEXTDOMAIN ),
            'menu_name'          => __( 'Event Categories', DS_THEME_TEXTDOMAIN ),
            'all_items'          => __( 'All Categories', DS_THEME_TEXTDOMAIN ),
            'edit_item'          => __( 'Edit Category', DS_THEME_TEXTDOMAIN ),
            'update_item'        => __( 'Update Category', DS_THEME_TEXTDOMAIN ),
            'add_new_item'       => __( 'Add New Category', DS_THEME_TEXTDOMAIN ),
            'new_item_name'      => __( 'New Category Name', DS_THEME_TEXTDOMAIN ),
            'search_items'       => __( 'Search Categories', DS_THEME_TEXTDOMAIN ),
            'not_found'          => __( 'No categories found', DS_THEME_TEXTDOMAIN ),
        ];

        register_taxonomy(
            'wv_event_category',
            [ 'wv_event' ],
            [
                'labels'            => $labels_event_cat,
                'hierarchical'      => true,
                'public'            => false,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'rewrite'           => false,
            ]
        );
    }

    /**
     * Insert default terms for WV Year (2023–2025).
     */
    protected static function add_default_year_terms() {
        $years = [ '2023', '2024', '2025' ];
        foreach ( $years as $year ) {
            if ( ! term_exists( $year, 'wv_year' ) ) {
                wp_insert_term( $year, 'wv_year' );
            }
        }
    }
}
