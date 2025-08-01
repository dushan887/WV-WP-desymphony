<?php
/**
 * Handles Secondary Featured Image meta-box for Posts
 *
 * @package Desymphony
 */

namespace Desymphony\Theme;

defined( 'ABSPATH' ) || exit;

class DS_Secondary_Image {

    public static function init() {
        add_action( 'add_meta_boxes',      [ __CLASS__, 'register_metabox' ] );
        add_filter( 'postbox_classes_post_secondary_featured_image', [ __CLASS__, 'postbox_classes' ] );
        add_action( 'save_post',           [ __CLASS__, 'save_meta' ] );
    }

    public static function register_metabox() {
        add_meta_box(
            'secondary_featured_image',
            __( 'Secondary Featured Image', DS_THEME_TEXTDOMAIN ),
            [ __CLASS__, 'render_metabox' ],
            'post',
            'side',
            'low'
        );
    }

    public static function postbox_classes( array $classes ): array {
        $classes[] = 'postimagediv';
        $classes[] = 'hide-if-no-js';
        return $classes;
    }

    public static function render_metabox( \WP_Post $post ) {
        wp_nonce_field( 'secondary_image_nonce_action', 'secondary_image_nonce' );

        $img_id      = (int) get_post_meta( $post->ID, '_secondary_featured_image_id', true );
        $media_type  = get_post_meta( $post->ID, '_secondary_media_type', true )    ?: 'news';
        $display_opt = get_post_meta( $post->ID, '_secondary_display_option', true ) ?: 'view1';

        $src = $img_id
            ? wp_get_attachment_image_src( $img_id, 'medium' )[0]
            : '';

        echo '<div id="secondary-featured-image-container">';
        if ( $src ) {
            printf( '<img src="%s" style="max-width:100%%;margin-bottom:10px;height:auto;">', esc_url( $src ) );
        }
        echo '</div>';

        printf(
            '<input type="hidden" id="secondary_featured_image_id" name="secondary_featured_image_id" value="%d">',
            esc_attr( $img_id )
        );

        ?>
        <p>
            <button type="button" class="button" id="set-secondary-featured-image">
                <?= esc_html__( 'Set Image', DS_THEME_TEXTDOMAIN ); ?>
            </button>
            <button type="button" class="button" id="remove-secondary-featured-image">
                <?= esc_html__( 'Remove Image', DS_THEME_TEXTDOMAIN ); ?>
            </button>
        </p>

        <hr>

        <p><strong><?= esc_html__( 'Media Type', DS_THEME_TEXTDOMAIN ); ?></strong><br>
            <label><input type="radio" name="secondary_media_type" value="news"    <?= checked( $media_type, 'news', false ); ?>> <?= esc_html__( 'News', DS_THEME_TEXTDOMAIN ); ?></label><br>
            <label><input type="radio" name="secondary_media_type" value="video"   <?= checked( $media_type, 'video', false ); ?>> <?= esc_html__( 'Video', DS_THEME_TEXTDOMAIN ); ?></label>
        </p>

        <p><strong><?= esc_html__( 'Display Option', DS_THEME_TEXTDOMAIN ); ?></strong><br>
            <label><input type="radio" name="secondary_display_option" value="view1" <?= checked( $display_opt, 'view1', false ); ?>> <?= esc_html__( 'View 1', DS_THEME_TEXTDOMAIN ); ?></label><br>
            <label><input type="radio" name="secondary_display_option" value="view2" <?= checked( $display_opt, 'view2', false ); ?>> <?= esc_html__( 'View 2', DS_THEME_TEXTDOMAIN ); ?></label><br>
            <label><input type="radio" name="secondary_display_option" value="view3" <?= checked( $display_opt, 'view3', false ); ?>> <?= esc_html__( 'View 3', DS_THEME_TEXTDOMAIN ); ?></label>
        </p>

        <script>
        jQuery( function( $ ) {
            var frame;
            $('#set-secondary-featured-image').on('click', function(e){
                e.preventDefault();
                if ( frame ) { frame.open(); return; }
                frame = wp.media({
                    title: '<?php echo esc_js( __( 'Select or Upload Image', DS_THEME_TEXTDOMAIN ) ); ?>',
                    button: { text: '<?php echo esc_js( __( 'Use this image', DS_THEME_TEXTDOMAIN ) ); ?>' },
                    multiple: false
                });
                frame.on('select', function(){
                    var selection = frame.state().get('selection').first().toJSON();
                    $('#secondary_featured_image_id').val(selection.id);
                    $('#secondary-featured-image-container').html(
                        '<img src="'+selection.url+'" style="max-width:100%;margin-bottom:10px;height:auto;">'
                    );
                });
                frame.open();
            });

            $('#remove-secondary-featured-image').on('click', function(e){
                e.preventDefault();
                $('#secondary_featured_image_id').val('');
                $('#secondary-featured-image-container').empty();
            });
        });
        </script>
        <?php
    }

    public static function save_meta( int $post_id ) {
        if (
            ! isset( $_POST['secondary_image_nonce'] )
            || ! wp_verify_nonce( $_POST['secondary_image_nonce'], 'secondary_image_nonce_action' )
            || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            || ! current_user_can( 'edit_post', $post_id )
        ) {
            return;
        }

        // Secondary image ID
        if ( isset( $_POST['secondary_featured_image_id'] ) ) {
            update_post_meta( $post_id, '_secondary_featured_image_id', intval( $_POST['secondary_featured_image_id'] ) );
        } else {
            delete_post_meta( $post_id, '_secondary_featured_image_id' );
        }

        // Media type
        if ( isset( $_POST['secondary_media_type'] ) ) {
            update_post_meta( $post_id, '_secondary_media_type', sanitize_text_field( $_POST['secondary_media_type'] ) );
        } else {
            delete_post_meta( $post_id, '_secondary_media_type' );
        }

        // Display option
        if ( isset( $_POST['secondary_display_option'] ) ) {
            update_post_meta( $post_id, '_secondary_display_option', sanitize_text_field( $_POST['secondary_display_option'] ) );
        } else {
            delete_post_meta( $post_id, '_secondary_display_option' );
        }
    }
}
