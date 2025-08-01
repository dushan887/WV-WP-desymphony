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


?>
<section class="d-block wv-bg-w py-32">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="fs-20 text-uppercase my-0 ls-3 lh-1 fw-600">INVITE CO-EXHIBITORS TO REGISTER</h1>              
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
    
    <div class="d-block">
        <div class="container container-1024 py-16">
            <div class="row align-items-center">
                <div class="col-6">
                    <h4 class="wv-my-0"><strong id="wv-coex-total-count"><?php echo esc_html( count( $invites ) ); ?></strong> CO-EXHIBITOR INVITED</h4>
                </div>
                <div class="col-6 text-end">
                    <strong class="fs-16"><span id="coex-slots-remaining"><?php echo esc_html( $slots_data['remaining'] ); ?></span> <?php esc_html_e( 'MORE AVAILABLE', 'addon' ); ?></strong>
                </div>
            </div>
        </div>
    </div>    
    <!-- <?php echo esc_html( $slots_data['accepted'] ); ?> -->
    <div class="container container-1024 py-16 text-center">
        <div id="wv-coex-invites-list" class="row">

            <div class="col-12">
                <div class="wv-coex-item d-flex w-100 br-12 shadow-sm position-relative p-12 wv-pending" data-id="1">
                    <div class="wv-coex-left d-flex ps-32">
                        <div class="wv-coex-avatar border rounded-circle d-inline-block position-relative z-3">
                            <div class="wv-coex-index">1</div><img class="p-8 d-block rounded-circle" src="https://placehold.co/120" width="120" height="120" alt="Placeholder"></div>
                    </div>
                    <div class="wv-coex-right px-16 w-100 d-flex flex-column justify-content-between">
                        <div class="wv-coex-top d-flex align-items-start justify-content-between w-100 py-8">
                            <div class="wv-coex-name fs-32 fw-500 lh-1-2">undefined</div>
                            
                        </div>
                        <div class="wv-coex-bottom d-flex align-items-start justify-content-between w-100 py-8">
                            <div class="wv-coex-time wv-color-c_50 d-flex align-items-center fs-14">
                                <i class="fas fa-envelope me-4"></i> 2 days ago
                            </div>
                            <div class="wv-coex-status">
                                <span class="wv-badge-group me-8">
                                    <span class="wv-badge wv-badge-v_10 br-4 br-r-0 me-2">
                                        <i class="fas fa-unlink"></i>
                                    </span>
                                    <span class="wv-badge wv-badge-v_10 br-4 br-l-0">Not Linked</span>
                                </span>
                                <button
                                    class="wv-coex-delete wv-button wv-button-badge wv-button-light-danger br-4" data-id="1">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            <?php if ( empty( $invites ) ) : ?>
                <div class="col-12 text-center">
                    <p>No invites yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="wv-coex-invites-module" class="py-32">
	<div class="container container-1024">
		<div id="wv-coex-invites-list" class="row"></div>
	</div>
</section>