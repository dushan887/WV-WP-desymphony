
<div id="wv-progress-bar" class="px-16 py-16 text-center">
    
    <?php if ( is_page('thank-you') && ! is_user_logged_in() ) { ?>
        <div class="wv-progress-indicator my-0 text-uppercase ls-3 fw-400">
            <span class="wv-start-progress">REGISTRATION COMPLETE!</span>   
        </div>
    <?php } elseif ( is_page('thank-you') && is_user_logged_in() ) { ?>
        <div class="wv-progress-indicator my-0 text-uppercase ls-3 fw-400">
            <span class="wv-start-progress">REGISTRATION COMPLETE!</span>   
        </div>
    <?php } else { ?>
        <div class="wv-progress-indicator my-0 text-uppercase ls-3 fw-400" id="wv-progress-indicator">
            <span class="wv-start-progress">REGISTER ACCOUNT</span>
            <span class="wv-exhibitor-progress fw-600"> <span class="wv wv_point-m35-f"></span> Exhibitor</span>
            <span class="wv-buyer-progress fw-600"> <span class="wv wv_point-m35-f"></span>Professional Buyer</span>
            <span class="wv-visitor-progress fw-600"> <span class="wv wv_point-m35-f"></span> Visitor</span>        
        </div>
    <?php } ?>

</div>    
<div id="wv-reg-messages"></div>

