<?php
/**
 * Exhibitor – Order / receipt overview
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
use Desymphony\Helpers\DS_Utils as Utils;
use Desymphony\Woo\DS_Woo_Stand_Cart;
?>
<?php if ( Utils::get_exhibitor_participation() !== 'Co-Exhibitor' ) : ?>
<div class="container container-1024">
<section class="d-block pt-24">
   <div class="row">
      <div class="col-12">
         <div class="wv-card wv-flex-column br-12 wv-bg-w">
            <div class="wv-card-header p-24 d-flex wv-justify-between wv-align-start" style="border-bottom: 2px solid #eee;">
               <h4 class="m-0 fs-20 fw-600 lh-1-5 ls-3"><?php esc_html_e( 'RECEIPT', 'wv-addon' ); ?></h4>
            </div>

            <div class="wv-card-body p-24">
               <?php echo DS_Woo_Stand_Cart::get_user_receipt_html( get_current_user_id() ); ?>

            </div>

         </div>
         <!-- End Card -->
      </div>
      <!-- End Column -->
   </div>
</section>
</div>
<?php endif;  ?>