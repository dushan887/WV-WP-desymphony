<?php
/**
 * Content for General Settings page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2><?php esc_html_e( 'General Settings', 'wv-addon' ); ?></h2>

<div id="wv-addon-auth-settings">



	<!-- TOP BUTTONS -->
	<div class="wv-addon-auth-page-actions" style="margin-bottom: 15px;">

        <h3><?php esc_html_e( 'Currency Exchange Rate', 'wv-addon' ); ?></h3>
        <form method="post" action="options.php" style="margin-top:20px;">
            <?php
                settings_fields( 'desymphony_general_settings' );             // same group name as above
            ?>
            <label for="ds_eur_to_rsd_rate" style="display:block;margin:0 0 8px;">
                <?php esc_html_e( 'EUR → RSD rate', 'wv-addon' ); ?>
            </label>

            <input
                type="number"
                step="0.0001"
                min="0"
                name="ds_eur_to_rsd_rate"
                id="ds_eur_to_rsd_rate"
                value="<?php
                    echo esc_attr(
                        number_format(
                            get_option( 'ds_eur_to_rsd_rate', '117.5283' ),
                            4, '.', ''
                        )
                    );
                ?>"
                class="regular-text"
            />

            <?php submit_button(); ?>
        </form>

    
        <h4><?php esc_html_e( 'Exhibitor ↔ Co‑Exhibitor Table', 'wv-addon' ); ?></h4>
        <button
            id="wv-addon-install-exhibitor-links-table"
            class="button button-secondary"
        >
            <?php esc_html_e( 'Create / Update Table', 'wv-addon' ); ?>
        </button>
        <p class="description">
            <?php esc_html_e( 'Creates or migrates the custom table for storing Exhibitor↔Co‑Exhibitor invitations.', 'wv-addon' ); ?>
        </p>

        <h4><?php esc_html_e( 'Exhibitor Products Table', 'wv-addon' ); ?></h4>
        <button
            id="wv-addon-install-exhibitor-products-table"
            class="button button-secondary"
        >
            <?php esc_html_e( 'Create / Update Table', 'wv-addon' ); ?>
        </button>
        <p class="description">
            <?php esc_html_e( 'Creates or migrates the custom table for storing Exhibitor Products.', 'wv-addon' ); ?>
        </p>

        <h4><?php esc_html_e( 'Favorites Table', 'wv-addon' ); ?></h4>
        <button
            id="wv-addon-install-favorites-table"
            class="button button-secondary"
        >
            <?php esc_html_e( 'Create / Update Table', 'wv-addon' ); ?>
        </button>
        <p class="description">
            <?php esc_html_e( 'Creates or migrates the custom table for storing user Favorites.', 'wv-addon' ); ?>
        </p>
    </div>
</div>
