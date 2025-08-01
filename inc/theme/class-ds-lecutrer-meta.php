<?php
/**
 * Register and Save "Country" custom field for WV Lecturers
 *
 * @package Desymphony
 */

namespace Desymphony\Theme;

defined( 'ABSPATH' ) || exit;

class DS_Lecturer_Meta {

    /**
     * Hook into WP to register metabox and save data.
     */
    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_metabox' ] );
        add_action( 'save_post_wv_lecturer', [ __CLASS__, 'save_meta' ], 10, 2 );
    }

    /**
     * Add "Country" metabox to WV Lecturer post type.
     */
    public static function register_metabox() {
        add_meta_box(
            'wv_lecturer_country',                        // ID
            __( 'Country', DS_THEME_TEXTDOMAIN ),          // Title
            [ __CLASS__, 'render_metabox' ],               // Callback
            'wv_lecturer',                                 // Post type
            'side',                                        // Context
            'default'                                      // Priority
        );
    }

    /**
     * Render the Country input field.
     *
     * @param \WP_Post $post Current post object.
     */
    public static function render_metabox( \WP_Post $post ) {
        wp_nonce_field( 'wv_lecturer_country_nonce', 'wv_lecturer_country_nonce' );

        $value = get_post_meta( $post->ID, '_wv_lecturer_country', true );

        echo '<label for="wv_lecturer_country_field">' . esc_html__( 'Country', DS_THEME_TEXTDOMAIN ) . '</label>';
        echo '<input type="text" id="wv_lecturer_country_field" name="wv_lecturer_country_field" ' .
             'value="' . esc_attr( $value ) . '" style="width:100%;" />';
    }

    /**
     * Save the Country field when WV Lecturer is saved.
     *
     * @param int     $post_id Post ID.
     * @param \WP_Post $post   Post object.
     */
    public static function save_meta( $post_id, $post ) {
        // Verify nonce
        if ( ! isset( $_POST['wv_lecturer_country_nonce'] ) ||
             ! wp_verify_nonce( $_POST['wv_lecturer_country_nonce'], 'wv_lecturer_country_nonce' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save or delete meta
        if ( isset( $_POST['wv_lecturer_country_field'] ) ) {
            update_post_meta(
                $post_id,
                '_wv_lecturer_country',
                sanitize_text_field( wp_unslash( $_POST['wv_lecturer_country_field'] ) )
            );
        } else {
            delete_post_meta( $post_id, '_wv_lecturer_country' );
        }
    }
}
