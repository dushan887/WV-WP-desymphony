<?php
/**
 * Meta box for WV Podcast: Video ID.
 *
 * @package Desymphony
 */

namespace Desymphony\Theme;

defined( 'ABSPATH' ) || exit;

class DS_Podcast_Meta {

    /**
     * Hook into WP to register metabox and save data.
     */
    public static function init() {
        add_action( 'add_meta_boxes',       [ __CLASS__, 'register_metabox' ] );
        add_action( 'save_post_wv_podcast', [ __CLASS__, 'save_meta' ], 10, 2 );
    }

    /**
     * Register the Podcast Details metabox (in the sidebar).
     */
    public static function register_metabox() {
        add_meta_box(
            'wv_podcast_meta',
            __( 'Podcast Details', DS_THEME_TEXTDOMAIN ),
            [ __CLASS__, 'render_metabox' ],
            'wv_podcast',
            'side',      // appear in the sidebar
            'default'    // default priority
        );
    }

    /**
     * Render the Podcast Details metabox UI.
     *
     * @param \WP_Post $post The current post object.
     */
    public static function render_metabox( \WP_Post $post ) {
        wp_nonce_field( 'wv_podcast_meta_nonce', 'wv_podcast_meta_nonce' );

        $video_id = get_post_meta( $post->ID, '_wv_podcast_video_id', true );
        ?>
        <p>
            <label for="wv_podcast_video_id"><?php esc_html_e( 'Video ID', DS_THEME_TEXTDOMAIN ); ?></label>
            <input
                type="text"
                id="wv_podcast_video_id"
                name="wv_podcast_video_id"
                value="<?php echo esc_attr( $video_id ); ?>"
                style="width:100%;"
            />
        </p>
        <?php
    }

    /**
     * Save WV Podcast meta fields.
     *
     * @param int      $post_id Post ID.
     * @param \WP_Post $post    Post object.
     */
    public static function save_meta( $post_id, $post ) {
        // Verify nonce
        if ( ! isset( $_POST['wv_podcast_meta_nonce'] ) ||
             ! wp_verify_nonce( $_POST['wv_podcast_meta_nonce'], 'wv_podcast_meta_nonce' ) ) {
            return;
        }

        // Bail on autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Permissions check
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Video ID
        if ( isset( $_POST['wv_podcast_video_id'] ) ) {
            update_post_meta(
                $post_id,
                '_wv_podcast_video_id',
                sanitize_text_field( wp_unslash( $_POST['wv_podcast_video_id'] ) )
            );
        } else {
            delete_post_meta( $post_id, '_wv_podcast_video_id' );
        }
    }
}