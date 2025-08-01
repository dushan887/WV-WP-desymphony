<?php
/**
 * Register Custom Post Types: WV Events, Cards, Lecturers, Podcasts
 *
 * @package Desymphony
 */

namespace Desymphony\Theme;

defined( 'ABSPATH' ) || exit;

class DS_CPT_Registrar {

    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_cpts' ] );
    }

    public static function register_cpts() {
        $post_types = [
            'wv_event'      => [
                'singular'    => __( 'WV Event', DS_THEME_TEXTDOMAIN ),
                'plural'      => __( 'WV Events', DS_THEME_TEXTDOMAIN ),
                'supports'    => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
                'taxonomies'  => [ 'wv_year', 'wv_day', 'wv_event_category' ],
                'menu_icon'   => 'dashicons-calendar-alt',
            ],
            'wv_card'       => [
                'singular'    => __( 'WV Card', DS_THEME_TEXTDOMAIN ),
                'plural'      => __( 'WV Cards', DS_THEME_TEXTDOMAIN ),
                'supports'    => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
                'taxonomies'  => [ 'wv_year' ],
                'menu_icon'   => 'dashicons-format-image',
            ],
            'wv_lecturer'   => [
                'singular'    => __( 'WV Lecturer', DS_THEME_TEXTDOMAIN ),
                'plural'      => __( 'WV Lecturers', DS_THEME_TEXTDOMAIN ),
                'supports'    => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
                'taxonomies'  => [ 'wv_year' ],
                'menu_icon'   => 'dashicons-businessman',
            ],
            'wv_podcast'    => [
                'singular'    => __( 'WV Podcast', DS_THEME_TEXTDOMAIN ),
                'plural'      => __( 'WV Podcasts', DS_THEME_TEXTDOMAIN ),
                'supports'    => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
                'taxonomies'  => [ 'wv_year' ],
                'menu_icon'   => 'dashicons-format-audio',
            ],
            'wv_award' => [
				'singular'   => __( 'WV Award',   DS_THEME_TEXTDOMAIN ),
				'plural'     => __( 'WV Awards',  DS_THEME_TEXTDOMAIN ),
				'supports'   => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
				'taxonomies' => [ 'wv_year' ],
				'menu_icon'  => 'dashicons-awards',
			],
        ];

        foreach ( $post_types as $slug => $data ) {
            $labels = [
                'name'                  => $data['plural'],
                'singular_name'         => $data['singular'],
                'menu_name'             => $data['plural'],
                'name_admin_bar'        => $data['singular'],
                'add_new_item'          => sprintf( __( 'Add New %s', DS_THEME_TEXTDOMAIN ), $data['singular'] ),
                'edit_item'             => sprintf( __( 'Edit %s', DS_THEME_TEXTDOMAIN ), $data['singular'] ),
                'new_item'              => sprintf( __( 'New %s', DS_THEME_TEXTDOMAIN ), $data['singular'] ),
                'view_item'             => sprintf( __( 'View %s', DS_THEME_TEXTDOMAIN ), $data['singular'] ),
                'search_items'          => sprintf( __( 'Search %s', DS_THEME_TEXTDOMAIN ), $data['plural'] ),
                'not_found'             => sprintf( __( 'No %s found', DS_THEME_TEXTDOMAIN ), $data['plural'] ),
                'not_found_in_trash'    => sprintf( __( 'No %s found in Trash', DS_THEME_TEXTDOMAIN ), $data['plural'] ),
            ];

            register_post_type(
                $slug,
                [
                    'labels'             => $labels,
                    'public'             => false,
                    'show_ui'            => true,
                    'has_archive'        => false,
                    'show_in_nav_menus'  => false,
                    'exclude_from_search'=> true,
                    'publicly_queryable' => false,
                    'show_in_rest'       => true,
                    'menu_icon'          => $data['menu_icon'],
                    'supports'           => $data['supports'],
                    'taxonomies'         => $data['taxonomies'],
                    'rewrite'            => false,
                ]
            );
        }
    }
}
