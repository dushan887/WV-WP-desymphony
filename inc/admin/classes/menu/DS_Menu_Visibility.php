<?php

namespace Desymphony\Admin\Menu;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DS_Menu_Visibility {

    public function init(): void {
        if ( is_admin() ) {
            add_action( 'admin_init', [ $this, 'register_menu_item_fields' ] );
        }
        add_filter( 'wp_get_nav_menu_items', [ $this, 'filter_menu_items' ], 10, 3 );
    }

    public function register_menu_item_fields(): void {
        add_action( 'wp_nav_menu_item_custom_fields', [ $this, 'add_visibility_field' ], 10, 4 );
        add_action( 'wp_update_nav_menu_item',        [ $this, 'save_visibility_field' ], 10, 3 );
    }

    public function add_visibility_field( int $item_id, $item, int $depth, $args ): void {
        $value = get_post_meta( $item_id, '_menu_item_visibility', true );
        ?>
        <p class="field-visibility description description-wide">
            <label for="edit-menu-item-visibility-<?php echo esc_attr( $item_id ); ?>">
                <?php esc_html_e( 'Visibility', 'desymphony' ); ?><br/>
                <select id="edit-menu-item-visibility-<?php echo esc_attr( $item_id ); ?>"
                        class="widefat code"
                        name="menu-item-visibility[<?php echo esc_attr( $item_id ); ?>]">
                    <option value=""           <?php selected( $value, '' );          ?>><?php esc_html_e( 'All Users',  'desymphony' ); ?></option>
                    <option value="logged_in"  <?php selected( $value, 'logged_in' ); ?>><?php esc_html_e( 'Logged In',  'desymphony' ); ?></option>
                    <option value="logged_out" <?php selected( $value, 'logged_out'); ?>><?php esc_html_e( 'Logged Out', 'desymphony' ); ?></option>
                </select>
            </label>
        </p>
        <?php
    }

    public function save_visibility_field( int $menu_id, int $menu_item_db_id, $args ): void {
        if ( isset( $_POST['menu-item-visibility'][ $menu_item_db_id ] ) ) {
            $vis = sanitize_text_field( wp_unslash( $_POST['menu-item-visibility'][ $menu_item_db_id ] ) );
            if ( in_array( $vis, [ '', 'logged_in', 'logged_out' ], true ) ) {
                update_post_meta( $menu_item_db_id, '_menu_item_visibility', $vis );
            }
        } else {
            delete_post_meta( $menu_item_db_id, '_menu_item_visibility' );
        }
    }

    /**
     * Only runs on front-end.
     *
     * @param WP_Post[] $items
     * @param WP_Term   $menu
     * @param array     $args
     * @return WP_Post[]
     */
    public function filter_menu_items( array $items, $menu, $args ): array {
        if ( is_admin() ) {
            return $items;
        }
        foreach ( $items as $i => $item ) {
            $vis = get_post_meta( $item->ID, '_menu_item_visibility', true );
            if ( 'logged_in' === $vis && ! is_user_logged_in() ) {
                unset( $items[ $i ] );
                continue;
            }
            if ( 'logged_out' === $vis && is_user_logged_in() ) {
                unset( $items[ $i ] );
            }
        }
        return array_values( $items );
    }
}
