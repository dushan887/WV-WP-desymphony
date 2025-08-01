<?php
namespace Desymphony\Woo;
use Desymphony\Woo\DS_Woo_Stand_Map;
use Desymphony\Helpers\DS_Utils as Utils;


defined('ABSPATH') || exit;

class DS_Woo_Stand_Cart {

    public static function init() {
        add_action('wp_ajax_ds_load_fair_hall', [__CLASS__, 'ajax_load_fair_hall']);
        add_action('wp_ajax_nopriv_ds_load_fair_hall', [__CLASS__, 'ajax_load_fair_hall']);

        add_action('wp_ajax_ds_add_stand_to_cart', [__CLASS__, 'ds_add_stand_to_cart']);
        add_action('wp_ajax_nopriv_ds_add_stand_to_cart', [__CLASS__, 'ds_add_stand_to_cart']);

        add_action('wp_ajax_ds_load_stand_addons', [__CLASS__, 'ds_load_stand_addons']);
        add_action('wp_ajax_nopriv_ds_load_stand_addons', [__CLASS__, 'ds_load_stand_addons']);

        add_action('wp_ajax_ds_update_stand_addon', [__CLASS__, 'ds_update_stand_addon']);
        add_action('wp_ajax_nopriv_ds_update_stand_addon', [__CLASS__, 'ds_update_stand_addon']);

        add_action('woocommerce_order_status_completed', [__CLASS__, 'handle_order_completed']);

        add_action('wp_ajax_ds_clear_stand_cart', [__CLASS__, 'ds_clear_stand_cart']);
        add_action('wp_ajax_nopriv_ds_clear_stand_cart', [__CLASS__, 'ds_clear_stand_cart']);

        add_action( 'woocommerce_thankyou', [ __CLASS__, 'empty_cart_after_checkout' ], 5 );
        add_action( 'template_redirect', [ __CLASS__, 'maybe_clear_stands_on_application' ] );

        add_action( 'woocommerce_order_status_completed', [ __CLASS__, 'handle_order_completed' ] );
		add_action( 'woocommerce_thankyou',                [ __CLASS__, 'empty_cart_after_checkout' ], 5 );
		add_action( 'template_redirect',                   [ __CLASS__, 'maybe_clear_stands_on_application' ] );
		add_action( 'woocommerce_checkout_create_order_line_item', [ __CLASS__, 'copy_addons_to_order_item' ], 10, 4 );

        // Runs for ANY “Add to cart” (AJAX, REST Store API, etc.)
		add_filter( 'woocommerce_add_to_cart_validation',
		            [ __CLASS__, 'validate_add_to_cart' ], 10, 5 );

		// Final gate – runs right before order is created
		add_action( 'woocommerce_after_checkout_validation',
		            [ __CLASS__, 'validate_checkout' ], 10, 2 );



    }
    /* =========================================================
	 *  ↓↓↓  ROLE‑/RULE‑helpers
	 * ======================================================= */

	/** Is a given product a “stand”? */
	private static function is_stand_product( int $product_id ) : bool {
		return has_term( 'stand', 'product_cat', $product_id );
	}

	/** How many stand lines are *currently* in the cart? */
	private static function count_stands_in_cart() : int {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) return 0;
		$cnt = 0;
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( self::is_stand_product( $item['product_id'] ) ) {
				$cnt++;
			}
		}
		return $cnt;
	}

	/** Return first stand error message for the current user, or empty. */
	private static function stand_rule_violation( int $adding_product_id = 0 ) : string {

		$role = Utils::get_exhibitor_participation();

		// Non‑stand → always allowed
		if ( ! self::is_stand_product( $adding_product_id ) ) {
			return '';
		}

		// Rule: Co‑Exhibitor → no stands at all
		if ( $role === 'Co-Exhibitor' ) {
			return __( 'Co‑Exhibitors cannot purchase stands.', 'desymphony' );
		}

		// Rule: Solo → only ONE stand total (in cart / checkout)
		if ( $role === 'Solo Exhibitor' ) {
			$already = self::count_stands_in_cart();
			// when toggling “remove” the product is *already* in cart – allow removal
			$is_already_in_cart = false;
			foreach ( WC()->cart->get_cart() as $item ) {
				if ( (int) $item['product_id'] === $adding_product_id ) {
					$is_already_in_cart = true;
					break;
				}
			}
			if ( $already >= 1 && ! $is_already_in_cart ) {
				return __( 'Solo Exhibitors may have only one stand in the cart.', 'desymphony' );
			}
		}

		// Head Exhibitor → no limits
		return '';
	}

    /* =========================================================
	 *  1.  ADD‑TO‑CART VALIDATION  (runs *before* product is added)
	 * ======================================================= */
	public static function validate_add_to_cart( bool $passed,
	                                             int  $product_id,
	                                             int  $quantity,
	                                             int  $variation_id = 0,
	                                             array $variations = [] ) : bool {

		$msg = self::stand_rule_violation( $product_id );
		if ( $msg ) {
			wc_add_notice( $msg, 'error' );
			return false;
		}
		return $passed;
	}

    /* =========================================================
	 *  2.  CHECKOUT‑TIME VALIDATION  (safety net)
	 * ======================================================= */
	public static function validate_checkout( array $data, \WP_Error $errors ) : void {

		$role        = Utils::get_exhibitor_participation();
		$stand_count = self::count_stands_in_cart();

		if ( $role === 'Co-Exhibitor' && $stand_count > 0 ) {
			$errors->add( 'coex_no_stand', __( 'Co‑Exhibitors cannot purchase stands.', 'desymphony' ) );
		}

		if ( $role === 'Solo Exhibitor' && $stand_count > 1 ) {
			$errors->add( 'solo_one_stand', __( 'Solo Exhibitors may have only one stand per order.', 'desymphony' ) );
		}
	}

    /**
     * Add stand to cart (no add-ons yet). If already in cart, remove it.
     */
    public static function ds_add_stand_to_cart() {

		check_ajax_referer( 'wv_cart_nonce', 'nonce' );

		/* 0️⃣  Ensure cart object exists */
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			wc_load_cart();
		}

		$product_id = intval( $_POST['product_id'] ?? 0 );
		if ( ! $product_id || ! wc_get_product( $product_id ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid product.', 'desymphony' ) ] );
		}

		/* 1️⃣  Role‑based guard (same helper as above) */
		if ( $err = self::stand_rule_violation( $product_id ) ) {
			wp_send_json_error( [ 'message' => $err ] );
		}

		/* 2️⃣  Availability / reservation checks (unchanged) ----- */
		$stand_status     = strtolower( get_post_meta( $product_id, 'wv_stand_status', true ) );
		$reserved_email   = trim( strtolower( get_post_meta( $product_id, 'wv_reservation_email', true ) ) );
		$current_user     = wp_get_current_user();
		$current_user_mail= trim( strtolower( $current_user ? $current_user->user_email : '' ) );

		if ( $stand_status === 'sold'
		     || ( $stand_status === 'reserved' && $reserved_email && $reserved_email !== $current_user_mail ) ) {
			wp_send_json_error( [ 'message' => __( 'This stand is not available.', 'desymphony' ) ] );
		}

		/* 3️⃣  Toggle add / remove (original logic) ---------------- */
		$cart  = WC()->cart;
		$found = false;

		foreach ( $cart->get_cart() as $key => $item ) {
			if ( $item['product_id'] == $product_id ) {
				$cart->remove_cart_item( $key );      // remove
				$found = true;
				break;
			}
		}

		if ( ! $found ) {                             // add
			$add_key = $cart->add_to_cart( $product_id, 1 );

			if ( ! $add_key ) {
				// cart::add_to_cart() already fired validation filter -> fetch its notice
				$notices = wc_get_notices( 'error' );
				$msg     = $notices ? implode( ' ', wp_list_pluck( $notices, 'notice' ) )
				                    : __( 'Could not add to cart.', 'desymphony' );
				wp_send_json_error( [ 'message' => $msg ] );
			}
		}

		/* 4️⃣  Re‑render the cart block --------------------------- */
		ob_start();
		self::ds_render_fair_stands_cart();
		$html = ob_get_clean();

		wp_send_json_success( [
			'html'     => $html,
			'in_cart'  => ! $found,
		] );
	}


    /**
     * Renders the entire cart block. 
     * Dynamically figures out hall, stand #, and size from product meta or attributes,
     * picks the correct background class, and displays all add-ons (with 0 if unselected).
     */
    public static function ds_render_fair_stands_cart() {
        $cart = WC()->cart;
        echo '<section id="stand-cart-container" class="d-block position-relative ds-stand-cart-container">';

        if ( !$cart || $cart->is_empty() ) {
            // echo '<div class="container py-24"><p class="stand-cart-empty"></p></div></section>';
            return;                             // close the <section> and bail
        }

        echo '<div class="container py-12 py-lg-24">';

        $grand_total = 0;

        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            if (!$product) continue;

            $quantity     = $cart_item['quantity'];
            $base_subtotal= $cart_item['line_subtotal'];
            // If you want single-unit price: e.g. $base_price = $base_subtotal / $quantity;
            $base_price = $base_subtotal;

            // 1) Figure out Hall, Stand #, and Size
            $product_id = $product->get_id();

            // (A) Try product meta first:
            $hall_str  = get_post_meta($product_id, 'wv_hall_only', true);
            $stand_num = get_post_meta($product_id, 'wv_stand_no', true);
            $size_num  = get_post_meta($product_id, 'wv_stand_size', true);

            // (B) If meta is empty, fallback to product attributes:
            // e.g. pa_hall => '3', pa_stand-size => '24'
            if (!$hall_str || !$size_num) {
                $attributes = $product->get_attributes();
                foreach ($attributes as $key => $attribute_obj) {
                    if ($key === 'pa_hall') {
                        $options = $attribute_obj->get_options(); 
                        if (!empty($options)) {
                            // Just take the first
                            $hall_str = reset($options);
                        }
                    }
                    if ($key === 'pa_stand-size' || $key === 'pa_stand_size') {
                        $options = $attribute_obj->get_options();
                        if (!empty($options)) {
                            $size_num = reset($options);
                        }
                    }
                }
            }

            // If still empty, fallback to placeholders
            if (!$hall_str)  $hall_str  = '???';
            if (!$stand_num) $stand_num = '???';
            if (!$size_num)  $size_num  = '9'; // default?

            // 2) Determine background class
            $bg_class = 'wv-bg-custom';
            if (in_array($size_num, ['9','12','24','49'])) {
                $bg_class = 'wv-bg-' . $size_num . 'm2';
            }

            // 3) Compute add-ons
            // The user’s selected add-ons:
            $selected_addons = isset($cart_item['stand_addons']) ? $cart_item['stand_addons'] : [];

            // All possible add-ons for this size
            $possible_addons = \Desymphony\Woo\DS_Woo_Stand_Addons::get_addons_for_size($size_num);

            // Sum up selected add-ons cost
            $addons_total = 0;
            foreach ($selected_addons as $addon) {
                $addons_total += ($addon['qty'] * $addon['price']);
            }
            $base_price = $base_price - $addons_total;
            $stand_total = $base_price + $addons_total;
            $grand_total += $stand_total;

            // Unique collapse ID
            $collapse_id = 'collapse-' . $cart_item_key;
            ?>
            <div class="card mb-12 border-0 br-8 overflow-hidden">

            <div class="card-header <?php echo esc_attr($bg_class); ?> d-flex flex-wrap justify-content-between align-items-center">
                <div class="fs-16 fw-600 wv-color-w me-12">
                <?php echo '<span class="d-none d-lg-inline-block">Hall </span>' . esc_html($hall_str); ?>
                </div>

                <div class="ds-stand-info-box ds-stand-info-box-2 ds-stand-info-box-sm d-flex w-100 align-items-center justify-content-between br-4 wv-bc-w w-auto <?php echo esc_attr( $bg_class ); ?>_30" style="white-space: nowrap;">
                <span class="ds-stand-info-label wv-color-c fw-600 fs-14 ls-3 text-uppercase nowrap">Stand</span>
                <span class="ds-stand-info-val wv-bg-w wv-bc-w text-center">
                    <?php echo esc_html($stand_num); ?>
                </span>

                <span class="ds-stand-info-label wv-color-c fw-600 fs-14 ls-3 text-uppercase nowrap">Size</span>
                <span class="ds-stand-info-val wv-bg-w wv-bc-w text-center">
                    <?php echo esc_html($size_num); ?>m²
                </span>

                <span class="ds-stand-info-label wv-color-c fw-600 fs-14 ls-3 text-uppercase nowrap">Price</span>
                <span class="ds-stand-info-val wv-bg-w wv-bc-w text-center me-4">
                    <?php echo wc_price($base_price); ?>
                </span>
                </div>

                <div class="ds-stand-info-box ds-stand-info-box-2 ds-stand-info-box-sm d-flex w-100 align-items-center justify-content-between br-4 wv-bc-w w-auto ms-auto wv-bg-w" style="white-space: nowrap;">
                <span class="ds-stand-info-label wv-color-c wv-bg-w fw-600 fs-14 ls-3 text-uppercase nowrap me-4">Total</span>
                <span class="ds-stand-info-val wv-bg-c_95 wv-color-w wv-bc-c text-center">
                    <?php echo wc_price($stand_total); ?>
                </span>
                </div>

                <a class="wv-button wv-icon-button fs-32 ms-12 br-32"
                data-bs-toggle="collapse"
                href="#<?php echo esc_attr($collapse_id); ?>"
                role="button"
                aria-expanded="false"
                aria-controls="<?php echo esc_attr($collapse_id); ?>">
                <span class="wv wv_point-dd"><span class="path1"></span><span class="path2"></span></span>
                </a>
            </div> <!-- card-header -->

            <div class="collapse" id="<?php echo esc_attr($collapse_id); ?>">
                <div class="card-body">
                <?php
                // Display each add-on from the *full* possible list
                foreach ($possible_addons as $addon) {
                    $slug  = $addon['slug'];
                    $label = $addon['label'];
                    $price = $addon['price'];
                    $unit  = isset($addon['unit']) ? $addon['unit'] : 'Pcs';

                    // check if user has selected it
                    $addon_qty = 0;
                    foreach ($selected_addons as $sel) {
                        if ($sel['slug'] === $slug) {
                            $addon_qty = $sel['qty'];
                            break;
                        }
                    }
                    $row_subtotal = $price * $addon_qty;
                    ?>
                    <div class="row align-items-center justify-content-between m-4 wv-bg-c_10 p-4 br-4">
                        <div class="col-lg-6 d-flex align-items-center justify-content-between">
                             <div class="px-8">
                                <input
                                    class="form-check-input ds-addon-check"
                                    type="checkbox"
                                    data-cart-key="<?php echo esc_attr($cart_item_key); ?>"
                                    data-addon-slug="<?php echo esc_attr($slug); ?>"
                                    data-addon-price="<?php echo esc_attr($price); ?>"
                                    <?php if ($addon_qty>0) echo 'checked'; ?>
                                />
                            </div>
                            <label class="fw-500 me-auto ms-12"><?php echo esc_html($label); ?></label>
                            <div class="fs-14 fw-400 ms-12"><?php echo wc_price($price); ?> / <?php echo esc_html($unit); ?></div>
                        </div>
                        <div class="col-6 col-lg-3 d-flex align-items-center justify-content-start justify-content-lg-center">
                            <span class="fs-14 fw-600 ls-2 wv-color-c_50">AMOUNT</span>
                            <input
                                type="number"
                                min="0"
                                class="form-control form-control-sm mx-8 text-center fw-600 ds-addon-qty"
                                data-cart-key="<?php echo esc_attr($cart_item_key); ?>"
                                data-addon-slug="<?php echo esc_attr($slug); ?>"
                                data-addon-price="<?php echo esc_attr($price); ?>"
                                value="<?php echo esc_attr($addon_qty); ?>"
                                style="width: 70px;"
                            />
                            <span class="fs-14 fw-600 ls-2 wv-color-c_50">PCS</span>
                        </div>
                        <div class="col-6 col-lg-3 d-flex align-items-center justify-content-end justify-content-lg-start"> 
                            <span class="fs-14 fw-600 ls-2 wv-color-c_50">SUBTOTAL</span>
                            <span class="fw-600 px-8 text-end" style="width: 100px;">
                                <?php echo wc_price($row_subtotal); ?>
                            </span>
                        </div>
                    </div>
                    <?php
                } // end each possible add-on
                ?>
                </div> <!-- card-body -->

                <div class="card-footer d-flex flex-wrap justify-content-between align-items-center wv-bg-c_90 wv-color-w">                    
                <div class="fw-400 fs-16">
                    Stand <?php echo esc_html($stand_num); ?> Total <span class="d-none d-lg-inline-block">Cost</span>
                </div>
                <span class="fw-600 fs-16"><?php echo wc_price($stand_total); ?></span>
                <div>
                    <button
                      type="button"
                      class="wv-button wv-button-sm wv-button-outline wv-button-w me-8 ds-confirm-stand"
                      data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">
                      Confirm <span class="d-none d-lg-inline-block">Stand</span>
                    </button>
                    <button class="wv-button wv-button-sm wv-button-w wv-button-outline" data-bs-toggle="collapse"
                    href="#<?php echo esc_attr($collapse_id); ?>"
                    role="button"
                    aria-expanded="false"
                    aria-controls="<?php echo esc_attr($collapse_id); ?>">Cancel</button>
                </div>
                </div>
            </div> <!-- collapse -->
            </div> <!-- card -->
            <?php
        } // end foreach

        // Example final total w/ VAT
        $vat = 0.20 * $grand_total;
        $grand_total_incl_vat = $grand_total + $vat;
        ?>
        </div> <!-- container -->
        
        <div id="ds-cart-grand-total" class="container">
            <div class="d-block wv-bg-w br-12 p-24 p-lg-48">            
                <h2 class="h4 fw-600 ls-4 text-center">STAND RENTAL GRAND TOTAL</h2>
                <div class="wv-bc-v my-24 d-block" style="border-top: 3px dotted;"></div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="h2 my-0 fw-600"><?php echo wc_price($grand_total); ?></div>
                    <div class="h2 my-0 fw-600 d-flex align-items-center"><span class="fs-14 wv-bg-c_50 wv-color-w lh-1 p-8 br-4 me-12">+VAT 20%</span> <?php echo wc_price($vat); ?></div>
                    <div class="h2 my-0 fw-600"><?php echo wc_price($grand_total_incl_vat); ?></div>
                </div>
            </div>
        </div>

        <div class="container my-24">
            <div class="d-block wv-bg-w br-12 p-24 p-lg-48"> 
                <div class="row">
                    <div class="col-12">
                        <div class="d-block mb-12 fs-16 ls-3 text-center wv-color-w wv-bg-v p-12 br-8">COMMERCIAL TERMS AND CONDITIONS</div>
                        <p><strong class="fw-600">Upon payment validation, an email containing a receipt will be sent to the address provided in the exhibitor account registration.</strong> This receipt is considered a legally valid document. The 2025 Exhibitor Application form holds the legal force of a Contract. In case of any disputes, the Contract Parties have agreed to settle such disputes through the Foreign Trade Arbitration with the Serbian Chamber of Commerce in Belgrade. Regulations for Participation at Belgrade Fair Events and the Contract Special Conditions shall form part of the Contract. Belgrade Fair reserves the right to adjust exhibition space rental prices. Prices not included in the application can be found in the Belgrade Fair price list. Prices are subject to change in accordance with market fluctuations. VAT will be added as per law.</p>
                        <hr>
                        <div class="wv-input-group">
                            <label class="wv-custom-checkbox wv-custom-checkbox-small h-auto">
                                <input type="checkbox" id="terms_conditions" name="terms_conditions" required="">
                                <div class="wv-checkbox-card wv-checkbox-card-inline align-items-center justify-content-center w-100">
                                    <div class="wv-check"></div>
                                    <h6 class="fs-14">
                                        I declare hereby that I am aware of the participation conditions, mentioned in the <a target=_blank" href="https://sajam.rs/wp-content/uploads/pravilnik-2017-ENG.pdf" class="fw-600 wv-color-c" target="_blank">General Rules of Participation at Belgrade Fair Events</a> and the <a target=_blank" href="https://sajam.rs/wp-content/uploads/Rules_Upon_Participation_at_Wine_Vision_Fair_and_the_Contract_Special_Conditions.pdf" class="fw-600 wv-color-c" target="_blank">Rules Upon Participation at Wine Vision Fair and the Contract Special Conditions</a> and that I fully accept them.
                                    </h6>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container pb-48 text-center">
            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
                id="wv-stand-cart-submit"
                class="wv-button wv-button-lg wv-button-primary"
                data-skip-clear="1">
                Make Payment &amp; Complete&nbsp;Step&nbsp;1
                </a>
        </div>


        </section>
        
        <?php
    }



   /**
     * AJAX handler: return everything the front‑end needs for one hall
     * (SVG, grouping arrays, highlight IDs, prev/next nav, etc.).
     *
     * Always ends with wp_send_json_*()
     */
    public static function ajax_load_fair_hall() {
        if ( ! wp_doing_ajax() ) {
            wp_die(); // only callable via AJAX
        }

        $slug = isset( $_POST['hall'] ) ? sanitize_text_field( $_POST['hall'] ) : '1';

        $halls_order = require get_theme_file_path( 'inc/public/views/halls/halls-order.php' );
        if ( ! in_array( $slug, $halls_order, true ) ) {
            wp_send_json_error( [ 'message' => 'Invalid hall slug' ] );
        }

        $map_for_hall = DS_Woo_Stand_Map::get_map_for_hall( $slug );
        $stands       = $map_for_hall[ $slug ] ?? [];

        $svg_file = get_theme_file_path( "inc/public/views/halls/hall-{$slug}.svg" );
        $hall_svg = file_exists( $svg_file ) ? file_get_contents( $svg_file ) : '';

        $size_groups   = [ '9' => [], '12' => [], '24' => [], '49' => [], 'other' => [] ];
        $status_groups = [ 'available' => [], 'reserved' => [], 'sold' => [] ];

        $purchased_ids             = [];
        $in_cart_ids               = [];
        $assigned_ids              = [];
        $reserved_current_user_ids = [];

        $current_id       = get_current_user_id();
        $current_user     = wp_get_current_user();
        $current_email_lc = strtolower( trim( $current_user ? $current_user->user_email : '' ) );

        $cart_product_ids = [];
        if ( function_exists( 'WC' ) && WC()->cart ) {
            foreach ( WC()->cart->get_cart() as $ci ) {
                $cart_product_ids[] = (int) $ci['product_id'];
            }
        }

        foreach ( $stands as $st ) {
            $id     = $st['id'];
            $size   = trim( $st['stand_size'] );
            $status = strtolower( $st['status'] );

            $bucket = in_array( $size, [ '9', '12', '24', '49' ], true ) ? $size : 'other';
            $size_groups[ $bucket ][] = $id;

            if ( isset( $status_groups[ $status ] ) ) {
                $status_groups[ $status ][] = $id;
            }

            if ( ! empty( $st['wv_reservation_user'] ) && (int) $st['wv_reservation_user'] === $current_id ) {
                $purchased_ids[] = $id;
            }

            if ( in_array( (int) $st['product_id'], $cart_product_ids, true ) ) {
                $in_cart_ids[] = $id;
            }

            $res_email = '';
            if ( ! empty( $st['wv_reservation_email'] ) ) {
                $res_email = strtolower( trim( $st['wv_reservation_email'] ) );
            } elseif ( ! empty( $st['label'] ) && is_email( $st['label'] ) ) {
                $res_email = strtolower( trim( $st['label'] ) );
            }

            if ( $status === 'reserved' && $res_email && $res_email === $current_email_lc ) {
                $reserved_current_user_ids[] = $id;
            }

            if (
                $status === 'sold' &&
                (
                    (!empty($st['wv_reservation_email']) && strtolower(trim($st['wv_reservation_email'])) === $current_email_lc) ||
                    (!empty($st['label']) && is_email($st['label']) && strtolower(trim($st['label'])) === $current_email_lc)
                )
            ) {
                $purchased_ids[] = $id;
            }

            if ( ! empty( $st['assigned_users'] ) && in_array( $current_id, (array) $st['assigned_users'], true ) ) {
                $reserved_current_user_ids[] = $id;
            }
        }

        $mine_svg = [];
        foreach ( self::user_stand_product_ids( $current_id ) as $pid ) {
            if ( $svg_id = self::svg_id_from_product( $pid ) ) {
                $mine_svg[] = $svg_id;
            }
        }

        $purchased_ids             = array_unique( array_merge( $purchased_ids, $mine_svg ) );
        $reserved_current_user_ids = array_unique( array_merge( $reserved_current_user_ids, $mine_svg ) );

        $purchased_rows = [];
        foreach ( $stands as $s ) {
            if ( in_array( $s['id'], $purchased_ids, true ) ) {
                $purchased_rows[] = [
                    'id'   => $s['id'],
                    'no'   => $s['stand_no']   ?? '',
                    'size' => $s['stand_size'] ?? '',
                ];
            }
        }

        $idx            = array_search( $slug, $halls_order, true );
        $prev_hall_slug = $halls_order[ ( $idx > 0 ) ? $idx - 1 : count( $halls_order ) - 1 ];
        $next_hall_slug = $halls_order[ ( $idx < count( $halls_order ) - 1 ) ? $idx + 1 : 0 ];

        ob_start(); ?>
            <div id="hall-content" data-hall-slug="<?php echo esc_attr( $slug ); ?>">
                <?php
                $hall_svg       = $hall_svg;
                $stands         = $stands;
                $prev_hall_slug = $prev_hall_slug;
                $next_hall_slug = $next_hall_slug;
                $current_slug   = $slug;
                require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-hall-apply.php';
                ?>
            </div>
        <?php
        $html = ob_get_clean();

        // Reindex all arrays before JSON output to avoid JS "not iterable" errors
        $size_groups               = array_map( 'array_values', $size_groups );
        $status_groups             = array_map( 'array_values', $status_groups );
        $purchased_ids             = array_values( $purchased_ids );
        $in_cart_ids               = array_values( $in_cart_ids );
        $assigned_ids              = array_values( $assigned_ids );
        $reserved_current_user_ids = array_values( $reserved_current_user_ids );
        $purchased_rows            = array_values( $purchased_rows );

        wp_send_json_success( [
            'html'                      => $html,
            'hall_label'                => 'Hall ' . $slug,
            'size_groups'               => $size_groups,
            'status_groups'             => $status_groups,
            'purchased_ids'             => $purchased_ids,
            'in_cart_ids'               => $in_cart_ids,
            'assigned_ids'              => $assigned_ids,
            'reserved_current_user_ids' => $reserved_current_user_ids,
            'purchased_rows'            => $purchased_rows,
        ] );
    }
    
    /**
     * When an order is marked completed, mark each stand as sold
     * and fill in reservation meta: user ID, name & email.
     */
    public static function handle_order_completed( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        $user_id = $order->get_customer_id();
        $first   = $order->get_billing_first_name();
        $last    = $order->get_billing_last_name();
        $email   = $order->get_billing_email();

        foreach ( $order->get_items() as $item ) {
            $product_id = $item->get_product_id();

            // 1) mark sold
            update_post_meta( $product_id, 'wv_stand_status', 'sold' );

            // 2) reservation fields
            update_post_meta( $product_id, 'wv_reservation_user',  $user_id );
            update_post_meta( $product_id, 'wv_reservation_name',  trim( "$first $last" ) );
            update_post_meta( $product_id, 'wv_reservation_email', $email );
            update_user_meta( $user_id, 'wv_ex_stage1_verified', '1' ); 

        }
    }

    /**
     * ds_load_stand_addons: if you want to retrieve the add-ons for a stand size, 
     * but you said now it's only for 0 by default, we can keep it for future use if needed
     */
    public static function ds_load_stand_addons() {
        check_ajax_referer('wv_cart_nonce', 'nonce');
        $stand_size = isset($_POST['stand_size']) ? sanitize_text_field($_POST['stand_size']) : '';
        if (!$stand_size) {
            wp_send_json_error(['message'=>'No stand_size given']);
        }
        $addons = \Desymphony\Woo\DS_Woo_Stand_Addons::get_addons_for_size($stand_size);
        if (empty($addons)) {
            wp_send_json_error(['message'=>'No add-ons found']);
        }
        wp_send_json_success(['addons'=>$addons]);
    }

    /**
     * ds_update_stand_addon: updates the quantity in cart
     */
    public static function ds_update_stand_addon() {
        check_ajax_referer('wv_cart_nonce', 'nonce');
        $cart_key    = sanitize_text_field($_POST['cart_key'] ?? '');
        $addon_slug  = sanitize_text_field($_POST['addon_slug'] ?? '');
        $addon_price = floatval($_POST['addon_price'] ?? 0);
        $qty         = isset($_POST['addon_qty']) ? intval($_POST['addon_qty']) : 0;

        if (!function_exists('WC') || !WC()->cart) {
            if (function_exists('wc_load_cart')) {
                wc_load_cart();
            } else {
                global $woocommerce;
                if ($woocommerce && property_exists($woocommerce, 'cart')) {
                    $woocommerce->cart = new \WC_Cart();
                }
            }
        }

        $cart = WC()->cart;
        if (!$cart || !isset($cart->cart_contents[$cart_key])) {
            wp_send_json_error(['message'=>'Invalid cart item or cart not found']);
        }

        $cart_item = $cart->cart_contents[$cart_key];

        if (empty($cart_item['stand_addons'])) {
            $cart_item['stand_addons'] = [];
        }

        if ($qty > 0) {
            // add or update
            $exists = false;
            foreach ($cart_item['stand_addons'] as &$existing) {
                if ($existing['slug'] === $addon_slug) {
                    $existing['qty']   = $qty;
                    $existing['price'] = $addon_price;
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $cart_item['stand_addons'][] = [
                    'slug'  => $addon_slug,
                    'label' => $addon_slug,
                    'qty'   => $qty,
                    'price' => $addon_price
                ];
            }
        } else {
            // remove if exists
            foreach ($cart_item['stand_addons'] as $i => $existing) {
                if ($existing['slug'] === $addon_slug) {
                    unset($cart_item['stand_addons'][$i]);
                    break;
                }
            }
            /* -----------------------------------------------------------
            * Re-calculate full price (stand + all selected add-ons)
            * ---------------------------------------------------------- */
            $base_price   = $cart_item['data']->get_regular_price();  // stand itself
            $addons_total = 0;
            foreach ( $cart_item['stand_addons'] as $ad ) {
                $addons_total += $ad['qty'] * $ad['price'];
            }
            $full_price = $base_price + $addons_total;

            /* store & tell WooCommerce */
            $cart_item['stand_full_price'] = $full_price;     // later copied to order meta
            $cart_item['data']->set_price( $full_price );     // affects cart/checkout UI

        }

        $cart->cart_contents[$cart_key] = $cart_item;
        $cart->calculate_totals();

        ob_start();
        self::ds_render_fair_stands_cart();
        $html = ob_get_clean();

        wp_send_json_success(['html'=>$html]);
    }

    /**
     * Clear every cart line that belongs to category “stand”
     * (used when visitor leaves/refreshes the page).
     */
    public static function ds_clear_stand_cart() {
        if ( ! function_exists('WC') || ! WC()->cart ) {
            wc_load_cart();
        }

        $cart = WC()->cart;
        if ( ! $cart ) wp_send_json_success();   // nothing to do

        foreach ( $cart->get_cart() as $key => $item ) {
            if ( has_term( 'stand', 'product_cat', $item['product_id'] ) ) {
                $cart->remove_cart_item( $key );
            }
        }
        $cart->calculate_totals();
        wp_send_json_success();
    }

    public static function empty_cart_after_checkout( $order_id ) {
        if ( function_exists( 'WC' ) && WC()->cart ) {
            WC()->cart->empty_cart();        // blow away the session cart
        }
    }

    /**
     * If the visitor just landed on the application page,
     * remove every product that belongs to category “stand”.
     */
    public static function maybe_clear_stands_on_application() {

        /* Adjust the slug if your page isn’t literally /wv-application/ */
        if ( ! is_page( 'wv-application' ) ) {
            return;
        }

        if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
            wc_load_cart();                       // make sure the cart object exists
        }
        $cart = WC()->cart;
        if ( ! $cart || $cart->is_empty() ) {
            return;
        }

        foreach ( $cart->get_cart() as $key => $item ) {
            if ( has_term( 'stand', 'product_cat', $item['product_id'] ) ) {
                $cart->remove_cart_item( $key );
            }
        }
        $cart->calculate_totals();
    }

    /**
     * Copy the stand_addons array that lives in the cart line
     * into the order-item meta so we can show it later.
     */
    public static function copy_addons_to_order_item( $item, $cart_item_key, $values, $order ) {

        /* 1) copy the add-on details */
        if ( ! empty( $values['stand_addons'] ) ) {
            $item->add_meta_data( 'stand_addons', $values['stand_addons'], true );
        }

        /* 2) lock the final price into the order */
        if ( isset( $values['stand_full_price'] ) ) {
            $price = (float) $values['stand_full_price'];
            $item->set_subtotal( $price );
            $item->set_total   ( $price );
        }
    }


    public static function get_user_receipt_html( $user_id, $statuses = [ 'wc-completed', 'wc-processing', 'wc-on-hold' ] ) {

        // ------------------------------------------------------------------
        // 0. Guard clauses
        // ------------------------------------------------------------------
        if ( ! $user_id ) {
            return '<p>' . esc_html__( 'You have no purchases yet.', 'wv-addon' ) . '</p>';
        }

        $orders = wc_get_orders( [
            'customer_id' => $user_id,
            'status'      => $statuses,
            'limit'       => -1,
        ] );

        if ( empty( $orders ) ) {
            return '<p>' . esc_html__( 'You have no purchases yet.', 'wv-addon' ) . '</p>';
        }

        // ------------------------------------------------------------------
        ob_start();

        $grand_total_net  = 0;   // ← add

        foreach ( $orders as $order ) {
            foreach ( $order->get_items() as $item_id => $item ) {

                $product = $item->get_product();
                if ( ! $product || ! has_term( 'stand', 'product_cat', $product->get_id() ) ) {
                    continue; // skip everything that isn’t a stand
                }

                /* ----------------------------------------------------------------
                * 1.   Basic meta (same logic as cart)
                * ---------------------------------------------------------------- */
                $product_id = $product->get_id();

                /* A) product-meta first */
                $hall   = get_post_meta( $product_id, 'wv_hall_only',  true );
                $number = get_post_meta( $product_id, 'wv_stand_no',   true );
                $size   = get_post_meta( $product_id, 'wv_stand_size', true );

                if (empty($hall)) {
                    $wv_hall_stand_no = get_post_meta($product_id, 'wv_hall_stand_no', true);
                    if (!empty($wv_hall_stand_no)) {
                        // $wv_hall_stand_no might be an array or string
                        if (is_array($wv_hall_stand_no)) {
                            $wv_hall_stand_no = reset($wv_hall_stand_no);
                        }
                        $parts = explode('/', $wv_hall_stand_no);
                        if (!empty($parts[0])) {
                            $hall = strtoupper(trim((string)$parts[0]));
                        }
                    }
                }

                /* B) fall back to product attributes if meta is empty */
                if ( empty( $hall ) || empty( $size ) ) {
                    foreach ( $product->get_attributes() as $key => $attr ) {
                        if ( $key === 'pa_hall' ) {
                            $opts = $attr->get_options();
                            if ( ! empty( $opts ) && empty( $hall ) ) {
                                $hall = reset( $opts );
                            }
                        }

                        if ( in_array( $key, [ 'pa_stand-size', 'pa_stand_size' ], true ) ) {
                            $opts = $attr->get_options();
                            if ( ! empty( $opts ) && empty( $size ) ) {
                                $size = reset( $opts );
                            }
                        }
                    }
                }

                /* C) graceful fallbacks */
                $hall   = $hall   ?: '???';
                $number = $number ?: '???';
                $size   = $size   ?: '9';


                /* ----------------------------------------------------------------
                * 2.   Add-ons
                * ---------------------------------------------------------------- */
                $selected_addons = $item->get_meta( 'stand_addons', true ) ?: [];
                $possible_addons = \Desymphony\Woo\DS_Woo_Stand_Addons::get_addons_for_size( $size );

                $addons_total = 0;
                foreach ( $selected_addons as $ad ) {
                    $addons_total += $ad['qty'] * $ad['price'];
                }

                $base_price   = $item->get_total();
                $base_price  =  $base_price - $addons_total;
                $stand_total  = $base_price + $addons_total;
                $grand_total_net += $stand_total;

                /* ----------------------------------------------------------------
                * 3.   Layout helpers
                * ---------------------------------------------------------------- */
                $bg_class = 'wv-bg-custom';
                if ( in_array( (string) $size, [ '9', '12', '24', '49' ], true ) ) {
                    $bg_class = 'wv-bg-' . $size . 'm2';
                }
                $collapse_id = 'receipt-collapse-' . $order->get_id() . '-' . $item_id;
                ?>
                <div class="get_user_receipt_html card mb-12 border-0 br-8 overflow-hidden">

                    <!---------------------  CARD HEADER  --------------------------->
                    <div class="card-header <?php echo esc_attr( $bg_class ); ?> d-flex flex-wrap justify-content-between align-items-center">
                        <div class="fs-16 fw-600 wv-color-w me-12">
                            <?php echo 'Hall ' . esc_html( $hall ); ?>
                        </div>

                        <div class="ds-stand-info-box ds-stand-info-box-2 ds-stand-info-box-sm d-flex w-100 align-items-center justify-content-between br-4 wv-bc-t w-auto me-auto <?php echo esc_attr( $bg_class ); ?>_30 " style="white-space: nowrap;">
                            <span class="ds-stand-info-label wv-color-c fw-600 fs-14 ls-3 text-uppercase">Stand</span>
                            <span class="ds-stand-info-val wv-bg-w wv-bc-w text-center"><?php echo esc_html( $number ); ?></span>

                            <span class="ds-stand-info-label wv-color-c fw-600 fs-14 ls-3 text-uppercase">Size</span>
                            <span class="ds-stand-info-val wv-bg-w wv-bc-w text-center"><?php echo esc_html( $size ); ?>m²</span>

                            <span class="ds-stand-info-label wv-color-c fw-600 fs-14 ls-3 text-uppercase">Price</span>
                            <span class="ds-stand-info-val wv-bg-w wv-bc-w text-center me-4"><?php echo wc_price( $base_price ); ?></span>
                        </div>

                        <div class="ds-stand-info-box ds-stand-info-box-2 ds-stand-info-box-sm d-flex w-100 align-items-center justify-content-between br-4 wv-bc-w w-auto ms-auto wv-bg-w" style="white-space: nowrap;">
                            <span class="ds-stand-info-label wv-color-c wv-bg-w fw-600 fs-14 ls-3 text-uppercase nowrap me-4 d-none d-lg-inline-block"><span class="">Total</span></span>
                            <span class="ds-stand-info-val wv-bg-c_95 wv-color-w wv-bc-c text-center">
                                <?php echo wc_price( $stand_total ); ?>
                            </span>
                        </div>
                    </div><!-- /.card-header -->

                    <!-------------------  CARD BODY (add-ons)  --------------------->
                    <div class="collapse show" id="<?php echo esc_attr( $collapse_id ); ?>">
                        <div class="card-body <?php echo esc_attr( $bg_class ); ?>_30 p-12">
                            <?php foreach ( $possible_addons as $addon ) :
                                $slug        = $addon['slug'];
                                $label       = $addon['label'];
                                $price       = $addon['price'];
                                $unit        = $addon['unit'] ?? 'Pcs';

                                // qty if purchased, else 0
                                $qty = 0;
                                foreach ( $selected_addons as $sel ) {
                                    if ( $sel['slug'] === $slug ) {
                                        $qty = (int) $sel['qty'];
                                        break;
                                    }
                                }
                                $row_subtotal = $price * $qty;

                                if ( $qty <= 0 ) {
                                    continue; // skip empty add-ons
                                }

                                ?>
                                <div class="row align-items-center justify-content-between m-4 wv-bg-w p-4 br-4">
                                    <div class="col-lg-6 d-flex align-items-center justify-content-between">
                                        
                                        <label class="fw-500 me-auto ms-12"><?php echo esc_html( $label ); ?></label>
                                        <div class="fs-14 fw-400 ms-12"><?php echo wc_price( $price ); ?> / <?php echo esc_html( $unit ); ?></div>
                                    </div>
                                    <div class="col-6 col-lg-3 d-flex align-items-center justify-content-center">
                                        <span class="fs-14 fw-600 ls-2 wv-color-c_50">AMOUNT</span>
                                        <input type="number"
                                            readonly
                                            disabled
                                            class="form-control form-control-sm mx-8 text-center fw-600"
                                            value="<?php echo esc_attr( $qty ); ?>"
                                            style="width: 70px;" />
                                        <span class="fs-14 fw-600 ls-2 wv-color-c_50">PCS</span>
                                    </div>
                                    <div class="col-6 col-lg-3 d-flex align-items-center justify-content-start">
                                        <span class="fs-14 fw-600 ls-2 wv-color-c_50">SUBTOTAL</span>
                                        <span class="fw-600 px-8 text-end" style="width: 100px;"><?php echo wc_price( $row_subtotal ); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php
                            // Hide collapse if there are no add-ons for this stand
                            // Hide collapse if there are no add-ons with qty > 0 for this stand
                            $has_addon_qty = false;
                            foreach ($possible_addons as $addon) {
                                foreach ($selected_addons as $sel) {
                                    if ($sel['slug'] === $addon['slug'] && intval($sel['qty']) > 0) {
                                        $has_addon_qty = true;
                                        break 2;
                                    }
                                }
                            }
                            if (!$has_addon_qty) {
                                echo '<style>#' . esc_attr($collapse_id) . ' { display: none !important; }</style>';
                            }
                            ?>
                        </div><!-- /.card-body -->

                        
                    </div><!-- /.collapse -->
                </div><!-- /.card -->
                <?php
            }
        }

        /* ==============================================================
        *  FOOTER – overall totals (net / VAT / gross)
        * ============================================================ */
        $vat_total          = $grand_total_net * 0.20;          // 20 %
        $grand_total_gross  = $grand_total_net + $vat_total;
        ?>
        
        <div id="ds-cart-grand-total" class="d-block wv-bg-c_95 br-12 p-24 p-lg-48">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="h5 fw-600 ls-4 wv-color-w"><span class="d-none d-lg-inline-block">STAND RENTAL</span> GRAND TOTAL</h2>
                <div class="h5 fw-600 ls-4 wv-color-w">
                    CHARGED <span class="wv wv_check-70 ms-4"><span class="path1"></span><span class="path2"></span></span>
                </div>
            </div>
            <div class="wv-bc-v my-24 d-block" style="border-top: 3px dotted;"></div>
            <div class="d-flex align-items-center justify-content-between wv-color-w">
                <div class="h2 my-0 fw-600"><?php echo wc_price( $grand_total_net ); ?></div>
                <div class="h2 my-0 fw-600 d-flex align-items-center">
                    <span class="fs-14 wv-bg-c_50 lh-1 p-8 br-4 me-12">+VAT&nbsp;20%</span>
                    <?php echo wc_price( $vat_total ); ?>
                </div>
                <div class="h2 my-0 fw-600"><?php echo wc_price( $grand_total_gross ); ?></div>
            </div>
        </div>
        <?php


        return ob_get_clean();
    }

    /* =========================================================
    *  Stands the user owns or is assigned to, grouped by hall
    * =======================================================*/
    public static function get_user_stands_by_hall( $user_id ) {

        // ➊ collect every *purchased* stand that’s still in a live order
        $orders = wc_get_orders( [
            'customer_id' => $user_id,
            'status'      => [ 'wc-completed', 'wc-processing', 'wc-on-hold' ],
            'limit'       => -1,
        ] );

        /* canonical hall list (adjust if you add more halls later) */
        $valid_halls = require get_theme_file_path(
            'inc/public/views/halls/halls-order.php'
        );

        /* helper:  “30” → “3”; reject hall slugs that are not in the canonical list */
        $normalise_hall = static function ( $raw ) use ( $valid_halls ) {

            $slug = trim( (string) $raw );

            // “30”, “40” … → “3”, “4”
            if ( preg_match( '/^(\d)0$/', $slug, $m ) ) {
                $slug = $m[1];
            }

            return in_array( $slug, $valid_halls, true ) ? $slug : '';
        };

        /* container; $out['3'] = [[ pid, no, size, assigned ]] */
        $out = [];

        /* ------------------------------------------------------------
        * Loop over every order line that is a “stand” product
        * ---------------------------------------------------------- */
        foreach ( $orders as $order ) {
            foreach ( $order->get_items() as $item ) {

                $p = $item->get_product();
                if ( ! $p || ! has_term( 'stand', 'product_cat', $p->get_id() ) ) {
                    continue;                                   // skip non‑stands
                }

                $pid   = $p->get_id();
                $hall  = get_post_meta( $pid, 'wv_hall_only',  true );
                $no    = get_post_meta( $pid, 'wv_stand_no',   true );
                $size  = get_post_meta( $pid, 'wv_stand_size', true );

                /* Current assignments living on that stand */
                $assigned = get_post_meta( $pid, 'wv_assigned_users', true );
                $assigned = is_array( $assigned ) ? array_map( 'intval', $assigned ) : [];

                /* ---------- fall back helpers (imports, attributes) ---------- */
                if ( empty( $hall ) ) {
                    $wv_hall_stand_no = get_post_meta( $pid, 'wv_hall_stand_no', true );
                    if ( $wv_hall_stand_no ) {
                        if ( is_array( $wv_hall_stand_no ) ) $wv_hall_stand_no = reset( $wv_hall_stand_no );
                        $parts = explode( '/', $wv_hall_stand_no );
                        if ( ! empty( $parts[0] ) ) $hall = strtoupper( trim( $parts[0] ) );
                    }
                }

                if ( empty( $hall ) || empty( $size ) ) {
                    foreach ( $p->get_attributes() as $key => $attr ) {

                        if ( ! $hall && $key === 'pa_hall' && $attr->get_options() ) {
                            $hall = reset( $attr->get_options() );
                        }

                        if ( ! $size && in_array( $key, [ 'pa_stand-size', 'pa_stand_size' ], true )
                            && $attr->get_options() ) {
                            $size = reset( $attr->get_options() );
                        }
                    }
                }

                /* ---------- normalise ---------- */
                $hall = $normalise_hall( $hall ) ?: 'unknown';
                $no   = $no   ?: '???';
                $size = $size ?: '?';

                $out[ $hall ][] = [
                    'pid'            => $pid,
                    'hall'           => $hall,
                    'no'             => $no,
                    'size'           => $size,
                    'assigned_users' => $assigned,        // <── NEW FIELD
                ];
            }
        }

        return $out;
    }


    /**
     * Return an array of Woo product‑IDs the user either
     * purchased *or* was assigned to via DS_Stand_Assign.
     */
    protected static function user_stand_product_ids( int $user_id ): array {
        if ( ! class_exists( '\Desymphony\Dashboard\DS_Stand_Assign' ) ) {
            return [];
        }
        return \Desymphony\Dashboard\DS_Stand_Assign::stands_for_user( $user_id );
    }

    /**
     * Convert a stand product‑ID to the <g> element id used in the SVG.
     * Example: product 123 (Hall 3, Stand 4) → "wv_hall_3_04"
     */
    protected static function svg_id_from_product( int $product_id ): string {

        $hall = get_post_meta( $product_id, 'wv_hall_only', true ); // e.g. "3"
        $no   = get_post_meta( $product_id, 'wv_stand_no',  true ); // e.g. "4"

        if ( $hall === '' || $no === '' ) {
            return '';
        }

        /* ① Hall is used as-is  (1, 1A, 4B …)
        ② Stand number is ALWAYS zero‑padded to two digits (“4” → “04”;
            “12” stays “12”; “105” stays “105”)                    */
        $no_padded = str_pad( $no, 2, '0', STR_PAD_LEFT );

        return "wv_hall_{$hall}_{$no_padded}";
    }

    /**
     * Return every stand that the given user is **assigned to** (not purchased),
     * grouped by hall, same structure as get_user_stands_by_hall().
     *
     * @return array [ '3' => [ [ pid, hall, no, size ], … ], … ]
     */
    public static function get_assigned_stands_by_hall( int $user_id ) : array {

        if ( ! $user_id || ! class_exists( '\Desymphony\Dashboard\DS_Stand_Assign' ) ) {
            return [];
        }

        /* All product IDs the Co‑Exhibitor is linked to */
        $pids = \Desymphony\Dashboard\DS_Stand_Assign::stands_for_user( $user_id );
        if ( empty( $pids ) ) return [];

        /* Canonical hall list */
        $valid_halls = require get_theme_file_path( 'inc/public/views/halls/halls-order.php' );

        $normalise = static function ( $raw ) use ( $valid_halls ) {
            $slug = trim( (string) $raw );
            if ( preg_match( '/^(\d)0$/', $slug, $m ) ) $slug = $m[1];   // 30 → 3
            return in_array( $slug, $valid_halls, true ) ? $slug : '';
        };

        $out = [];

        foreach ( $pids as $pid ) {

            $hall = get_post_meta( $pid, 'wv_hall_only',  true );
            $no   = get_post_meta( $pid, 'wv_stand_no',   true );
            $size = get_post_meta( $pid, 'wv_stand_size', true );

            /* fallback helpers (imported data / attributes) ----------- */
            if ( empty( $hall ) ) {
                $raw = get_post_meta( $pid, 'wv_hall_stand_no', true );  // e.g. "3/12"
                if ( $raw ) {
                    if ( is_array( $raw ) ) $raw = reset( $raw );
                    [ $hall ] = explode( '/', $raw );
                }
            }
            if ( empty( $hall ) || empty( $size ) ) {
                $prod = wc_get_product( $pid );
                if ( $prod ) {
                    foreach ( $prod->get_attributes() as $k => $attr ) {
                        if ( ! $hall && $k === 'pa_hall' && $attr->get_options() ) {
                            $hall = reset( $attr->get_options() );
                        }
                        if ( ! $size && in_array( $k, [ 'pa_stand-size', 'pa_stand_size' ], true )
                            && $attr->get_options() ) {
                            $size = reset( $attr->get_options() );
                        }
                    }
                }
            }
            /* --------------------------------------------------------- */

            $hall = $normalise( $hall ) ?: 'unknown';
            if ( $hall === 'unknown' ) {
                continue; // skip if unknown
            }
            $out[ $hall ][] = [
                'pid'  => $pid,
                'hall' => $hall,
                'no'   => $no ?: '???',
                'size' => $size ?: '?',
            ];
        }

        return $out;
    }

    




}
