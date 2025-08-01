<?php
/**
 * Dashboard Co-Exhibitors view
 *
 * @package Wv_Addon
 */

use Desymphony\Helpers\DS_Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();

// For Status – for now we use a static label.
$status = 'Active';

$slots_data      = Utils::get_coexhibitor_slots( $user_id );
$invites         = Utils::get_coexhibitor_invites( $user_id );

$total_slots   = is_array( $slots_data ) && isset( $slots_data['total'] )
	? (int) $slots_data['total']
	: (int) $slots_data;                               // legacy fallback

$accepted      = 0;
foreach ( $invites as $inv ) {
	if ( isset( $inv['status'] ) && $inv['status'] === 'accepted' ) {
		$accepted++;
	}
}

/**
 * Debug: Dump variables for inspection
 */
// if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
//     echo '<pre style="background:#f8f9fa;border:1px solid #ccc;padding:12px;margin:12px 0;">';
//     echo "<strong>Debug Dump:</strong>\n";
//     echo "<strong>User ID:</strong> ";
//     var_dump( $user_id );
//     echo "\n<strong>Status:</strong> ";
//     var_dump( $status );
//     echo "\n<strong>Slots Data:</strong> ";
//     var_dump( $slots_data );
//     echo "\n<strong>Invites:</strong> ";
//     var_dump( $invites );
//     echo "\n<strong>Total Slots:</strong> ";
//     var_dump( $total_slots );
//     echo "\n<strong>Accepted Invites:</strong> ";
//     var_dump( $accepted );
//     echo '</pre>';
// }
?>
<?php if ( Utils::get_status() !== 'Active') : ?>
    <?php
    wp_safe_redirect( home_url( '/wv-dashboard/' ) );
    exit;
    ?>
<?php endif; ?>



<section class="d-block wv-bg-w py-32">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center d-flex align-items-center justify-content-between">
                <h1 class="fs-20 text-uppercase my-0 ls-3 lh-1 fw-600">
                    <?php if (Utils::get_exhibitor_participation() === 'Head Exhibitor') : ?>
                        INVITE MEMBERS TO REGISTER
                    <?php else : ?>
                        INVITE CO-EXHIBITORS TO REGISTER
                    <?php endif; ?>
                </h1>
                
                    <!-- live counter -->
                <span id="ds-invited-counter" class="fs-14 fw-600 py-4 px-16 wv-bg-v_50 wv-color-w br-4 d-none"
                    data-total="<?php echo esc_attr( $total_slots ); ?>">
                    <?php
                        printf(
                            /* translators: 1 = accepted, 2 = total */
                            esc_html__( 'Invited %1$d/%2$d', 'wv-addon' ),
                            $accepted,
                            $total_slots
                        );
                    ?>
                </span>
               
            </div>
        </div>
    </div>
</section>




<section id="wv-coex-form-module" class="py-48 border-bottom wv-bc-c_50">
    <div class="container container-768">

        <form id="wv-coex-invite-form" class="wv-form">
            <?php wp_nonce_field( 'wv_dashboard_nonce', 'security' ); ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="wv-input-group">
                        <input
                            type="email"
                            id="wv-coex-email"
                            name="email"
                            class="wv-form-input"
                            style="padding: 12px !important;"
                            placeholder="<?php esc_attr_e( 'office@srpskavinarija.rs', 'wv-addon' ); ?>"
                            required
                        />
                    </div>
                </div>
                <div class="col-12 order-lg-1">
                    
                    <label class="wv-custom-checkbox wv-custom-checkbox-small h-auto my-12">
                        <input type="checkbox" id="wv-coex-tos" name="tos" required>
                        <div class="wv-checkbox-card wv-checkbox-card-inline align-items-center justify-content-center w-100">
                            <div class="wv-check"></div>
                            <h6 class="fs-14">I hereby take responsibility for providing my member's personal information.</h6>
                        </div>
                    </label>
                </div>
                <div class="col-lg-4">
                    <button type="submit" class="wv-button wv-button-primary w-100 wv-button-lg">
                        <?php esc_html_e( 'Send invitation', 'wv-addon' ); ?>
                    </button>
                </div>
            </div>                    
        </form>
    </div>
    
</section>


<section id="wv-coex-invites-module" class="py-32">
	<div class="container container-1024">
		<div id="wv-coex-invites-list" class="row"></div>
	</div>
</section>

<?php if (Utils::get_exhibitor_participation() === 'Head Exhibitor') : ?>
<section class="py-48 wv-bg-w">
    <div class="container container-768 text-center">
		<div class="d-inline-block wv-bg-c_50 wv-color-w ls-4 fs-20 text-uppercase mb-24 py-16 px-32 br-8 fw-600">IMPORTANT NOTICE</div>
        <p class="fs-12">Although it is possible to invite more members subsequently, we kindly ask you invite all of your members as soon as possible.<br />
            <strong>Please keep in mind that after stating you have invited all of your members, you will not be able to invite more members to register and participate at the 2025 Wine Vision by Open Balkan fair.</strong></p>
	</div>
</section>
<?php endif; ?>

<?php
	$participation = Utils::get_exhibitor_participation();
	if ( $participation === 'Solo Exhibitor' ) :

		$remaining = Utils::solo_ex_remaining_slots();      // 0 | 1 | 2
		$pid       = Desymphony\Woo\DS_Woo_CoEx_Slots::get_product_id();
        $qty  = (int) get_user_meta( $user_id, 'wv_coex_slots_purchased', true );
        $net  = 70 * $qty;
        $vat  = $net * 0.20;
        $gross= $net + $vat;

		// if they already paid, show the receipt banner and bail
		if ( $qty !== 0 ) :
        /* inside the “remaining === 0” branch */
        
        ?>
        <!-- RECEIPT BANNER -->
        <section class="py-48 wv-bg-w">
            <div class="container container-1024 mb-24">
                <div class="d-block wv-bg-c_95 br-12 p-24 p-lg-48">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="h5 fw-600 ls-4 wv-color-w">CO‑EXHIBITOR SLOT&nbsp;— GRAND&nbsp;TOTAL</h2>
                        <div class="h5 fw-600 ls-4 wv-color-w">
                            CHARGED <span class="wv wv_check-70 ms-4"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                    </div>
                    <div class="wv-bc-v my-24" style="border-top:3px dotted;"></div>
                    <div class="d-flex align-items-center justify-content-between wv-color-w">
                        <div class="h2 my-0 fw-600"><?php echo number_format_i18n( $net, 2 ); ?> €</div>
                        <div class="h2 my-0 fw-600 d-flex align-items-center">
                            <span class="fs-14 wv-bg-c_50 lh-1 p-8 br-4 me-12">+VAT&nbsp;20%</span>
                            <span><?php echo number_format_i18n( $vat, 2 ); ?> €</span>
                        </div>
                        <div class="h2 my-0 fw-600"><?php echo number_format_i18n( $gross, 2 ); ?> €</div>
                    </div>
                    <p class="mt-12 fs-14 wv-color-w">Quantity purchased: <strong><?php echo esc_html( $qty ); ?></strong></p>
                </div>
            </div>
        </section>

    <?php elseif ( $remaining > 0 )  : ?>
        <!-- BUY‑SLOT FORM -->
        <section id="wv-buy-slot-module" class="border-top py-48 wv-bc-c_50"
                data-pid="<?php echo esc_attr( $pid ); ?>"
                data-remaining="<?php echo esc_attr( $remaining ); ?>">

                <div class="container container-1024 mb-24">
                    <div class="d-block wv-bg-v br-12 p-24 p-lg-48">
                        <div class="mb-16 text-center fw-600 fs-16 wv-color-w">Invite and share stand with:</div>
                        <div class="row justify-content-center">
                            <div class="col-6">
                                <label class="wv-custom-radio">
                                    <input
                                        type="radio"
                                        name="slot_option"
                                        value="1"
                                        required
                                    >
                                    <div class="wv-radio-card wv-radio-card-inline wv-radio-card-inline-4">
                                        <div class="wv-check">
                                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                                        </div>
                                        <h3 class="h6 ps-8">1 Co-Exhibitor</h3>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="wv-custom-radio">
                                    <input
                                        type="radio"
                                        name="slot_option"
                                        value="2"
                                        required
                                    >
                                    <div class="wv-radio-card wv-radio-card-inline wv-radio-card-inline-4">
                                        <div class="wv-check">
                                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                                        </div>
                                        <h3 class="h6 ps-8">2 Co-Exhibitors</h3>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container container-1024">
                    <div class="d-block wv-bg-w br-12 p-24 p-lg-48">            
                        <h2 class="h4 fw-600 ls-4 text-center">COMPULSORY 2025 PARTICIPATION FEE</h2>
                        <div class="wv-bc-v my-24 d-block" style="border-top: 3px dotted;"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="h2 my-0 fw-600"><span id="wv-slot-net" class="woocommerce-Price-amount amount">0&nbsp;€</span></div>
                            <div class="h2 my-0 fw-600 d-flex align-items-center"><span class="fs-14 wv-bg-c_50 wv-color-w lh-1 p-8 br-4 me-12">+VAT 20%</span> <span id="wv-slot-vat" class="woocommerce-Price-amount amount">0&nbsp;€</span></div>
                            <div class="h2 my-0 fw-600"><span id="wv-slot-gross" class="woocommerce-Price-amount amount">0&nbsp;€</span></div>
                        </div>
                    </div>
                </div>

                <div class="container container-1024 my-24">
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
                                                I declare hereby that I am aware of the participation conditions, mentioned in the <a target="_blank&quot;" href="https://sajam.rs/wp-content/uploads/pravilnik-2017-ENG.pdf" class="fw-600 wv-color-c">General Rules of Participation at Belgrade Fair Events</a> and the <a target="_blank&quot;" href="https://sajam.rs/wp-content/uploads/Rules_Upon_Participation_at_Wine_Vision_Fair_and_the_Contract_Special_Conditions.pdf" class="fw-600 wv-color-c">Rules Upon Participation at Wine Vision Fair and the Contract Special Conditions</a> and that I fully accept them.
                                            </h6>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="container container-1024 pb-48 text-center">
                    <a id="wv-slot-pay"
                    href="#"
                    class="wv-button wv-button-lg wv-button-primary">
                    Make payment
                    </a>
                </div>
        </section>
        <?php
        endif;
    endif;
?>
