<?php
/**
 * Dashboard “home” view
 *
 * @package Wv_Addon
 */

use Desymphony\Helpers\DS_Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
    .wv_info .path1:before,
    .wv_app-form .path1:before {
        color: var(--wv-w);
    }
    .wv_info .path2:before,
    .wv_app-form .path2:before {
        color: var(--wv-c);
    }
    .wv-exhibitor .wv_info .path2:before,
    .wv-exhibitor .wv_app-form .path2:before {
        color: var(--wv-v);
    }
    .wv-buyer .wv_info .path2:before {
        color: var(--wv-t);
    }
    .wv-visitor .wv_info .path2:before {
        color: var(--wv-r);
    }
    .wv-exhibitor #wv-wrap::before {
        background-image: url(/wp-content/themes/desymphony/src/images/dashboard-home/DSK/DSK_Exhibitor_Evaluation_Bck.jpg);
    }
    .wv-buyer #wv-wrap::before {
        background-image: url(/wp-content/themes/desymphony/src/images/dashboard-home/DSK/DSK_Buyer_Evaluation_Bck.jpg);
    }
    .wv-visitor #wv-wrap::before {
        background-image: url(/wp-content/themes/desymphony/src/images/dashboard-home/DSK/DSK_Visitor_Evaluation_Bck.jpg);
    }
    .wv-declined #wv-wrap::before {
        background-image: url(https://winevisionfair.com/wp-content/uploads/2025/07/DSK_Not_Approved_LANDING_Bck.jpg) !important
    }    
    @media screen and (max-width: 768px) {
        .wv-exhibitor #wv-wrap::before {
            background-image: url(/wp-content/themes/desymphony/src/images/dashboard-home/MOB/MOB_Exhibitor_Evaluation_Bck.jpg);
        }
        .wv-buyer #wv-wrap::before {
            background-image: url(/wp-content/themes/desymphony/src/images/dashboard-home/MOB/MOB_Buyer_Evaluation_Bck.jpg);
        }
        .wv-visitor #wv-wrap::before {
            background-image: url(/wp-content/themes/desymphony/src/images/dashboard-home/MOB/MOB_Visitor_Evaluation_Bck.jpg);
        }
        .wv-declined #wv-wrap::before {
            background-image: url(https://winevisionfair.com/wp-content/uploads/2025/07/MOB_Not_Approved_LANDING_Bck.jpg) !important
        }
    }
</style>
<div id="wv-wrap" class="py-0">
    <section class="position-relative">
        <div class="container-fluid border-bottom wv-bc-w py-48 text-center">
            <div class="d-flex align-items-center justify-content-center">
                <span class="wv wv_info d-flex fs-20 lh-1"><span class="path1"></span><span class="path2"></span></span>
                <h4 class="fs-16 my-0 lh-1 ls-3 fw-600 text-uppercase ms-8 wv-color-w wv-color-ww">
                     
                     <?php if ( Utils::get_status() !== 'Disabled' ) { ?>     
                        
                        <?php if ( !Utils::is_admin_verified() ) { ?>     
                            <?php if ( Utils::is_exhibitor() && Utils::get_exhibitor_participation() !== 'Solo Exhibitor' ) { ?>
                                ACCOUNT EVALUATION IN PROCESS
                            <?php } elseif ( Utils::is_exhibitor() && Utils::get_exhibitor_participation() !== 'Head Exhibitor' ) { ?>
                                HEAD EXHIBITOR REQUEST IN PROCESS
                            <?php } elseif ( Utils::is_exhibitor() && Utils::get_exhibitor_category() !== 'Other' ) { ?>
                                EXHIBITOR REQUEST IN PROCESS
                            <?php } elseif ( Utils::is_buyer() ) { ?>
                                ACCOUNT EVALUATION IN PROCESS
                            <?php } elseif ( Utils::is_visitor() && Utils::get_visitor_participation() === 'Company' ) { ?>
                                ACCOUNT EVALUATION IN PROCESS
                            <?php } elseif ( Utils::is_visitor() && Utils::get_visitor_participation() === 'Public Visitor' ) { ?>
                                ACCOUNT EVALUATION IN PROCESS
                            <?php } else { ?>
                                ACCOUNT EVALUATION IN PROCESS
                            <?php } ?>  
                         <?php } else { ?>
                            ACCOUNT VALIDATED & APPROVED
                        <?php } ?>    

                    <?php } else { ?>
                        ACCOUNT NOT APPROVED
                    <?php } ?>      
            </h4>
            </div>
        </div>
        <div class="container container-768 py-48">
        <?php if ( Utils::get_status() !== 'Disabled' ) { ?>     
            <?php if ( !Utils::is_admin_verified() ) { ?>     
                <?php if ( Utils::is_exhibitor() && Utils::get_exhibitor_participation() === 'Solo Exhibitor' ) { ?>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Exhibitor,</h2>
                            <p>As part of the 2025 fair's participation guidelines, your account is currently undergoing a validation process. Upon completion, you will receive an email notification as well as an update on your personal account page. Should any additional details be required, our support team will reach out to you promptly.</p>
                            <p>Meanwhile, we encourage you to review the application form content for this year's fair, which is essential for your participation in the 2025 Wine Vision by Open Balkan Fair.</p>
                            <p class="fw-600">We appreciate your patience and cooperation!</p>
                        </div>
                    </div>
                <?php } elseif ( Utils::is_exhibitor() && Utils::get_exhibitor_participation() === 'Head Exhibitor' ) { ?>

                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Exhibitor,</h2>
                            <p>In accordance with the 2025 fair's participation rules, by choosing Head Exhibitor participation model, you have made a request for the potential rental of multiple exhibition stands. Our support team will contact you promptly for the purpose of determining the number of exhibition stands available for rent, as well as their location at the fair.</p>
                            <p>In the meantime, we kindly ask you to review this year's application form content which you need to complete in order to participate in the 2025 Wine Vision by Open Balkan Fair.</p>
                            <p class="fw-600">Thank you for your patience and understanding!</p>
                        </div>
                    </div>
                <?php } elseif ( Utils::is_exhibitor() && Utils::get_exhibitor_category() !== 'Other' ) { ?>

                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Exhibitor,</h2>
                            <p>In accordance with the 2025 fair's participation rules, by choosing Other category which implies an undefined professional activities category, our support team will contact you promptly for the purpose of defining your exhibition stand location at the fair.</p>
                            <p>In the meantime, we kindly ask you to review this year's application form content which you need to complete in order participate in the 2025 Wine Vision by Open Balkan Fair.</p>
                            <p class="fw-600">Thank you for your patience and understanding!</p>
                        </div>
                    </div>

                <?php } elseif ( Utils::is_buyer() ) { ?> 

                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Professional Buyer,</h2>
                            <p>In accordance with the 2025 fair's participation rules, your account is undergoing a validation process. Once the process is complete, you will receive an e-mail as well as a notification here on your personal account page. If any additional information is needed, our support team will contact you promptly.</p>
                            <p class="fw-600">Thank you for your patience and understanding!</p>
                            <a href="/" class="wv-button wv-button-pill wv-button-dark-op">Return to home page</a>
                        </div>
                    </div>
                
                <?php } elseif ( Utils::is_visitor() && Utils::get_visitor_participation() === 'Company' ) { ?>

                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Professional Visitor,</h2>
                            <p>In accordance with the 2025 fair's participation rules, your account is undergoing a validation process. Once the process is complete, you will receive an e-mail as well as a notification hereon your personal account page. If any additional information is needed, our support team will contact you promptly.</p>
                            <p class="fw-600">Thank you for your patience and understanding!</p>
                            <a href="/" class="wv-button wv-button-pill wv-button-dark-op">Return to home page</a>
                        </div>
                    </div>

                <?php } elseif ( Utils::is_visitor() && Utils::get_visitor_participation() === 'Public Visitor' ) { ?>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Visitor,</h2>
                            <p>In accordance with the 2025 fair's participation rules, your account is undergoing an evaluation process. Once the process is complete, you will receive an e-mail as well as a notification here on your personal account page. If any additional information is needed, our support team will contact you promptly.</p>
                            <p class="fw-600">Thank you for your patience and understanding!</p>
                            <a href="/" class="wv-button wv-button-pill wv-button-dark-op">Return to home page</a>
                        </div>
                    </div>
                <?php } else { 
                    wp_redirect( home_url( '/registered-users/' ) );
                    exit;
                } ?>

                <?php if ( Utils::is_exhibitor() ) { ?>
                <div class="row align-items-center justify-content-center py-48">
                    <div class="col-lg-8">
                        <a href="/explore-2025-application-form/" class="wv-card wv-card-lg br-12 p-24 d-block wv-bg-exhibitor-gradient-light h-auto">
                            <div class="wv-card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <span class="wv wv_info d-flex fs-32"><span class="path1"></span><span class="path2"></span></span>
                                    <span class="fs-14 ls-2 wv-color-w px-12 py-4 wv-bg-c opacity-75 br-32">Explore</span>
                                </div>
                                <h2 class="wv-color-w mt-48 mt-lg-64 ">Explore 2025<br />
                                application form</h2>
                            </div>
                        </a>
                    </div>
                </div>
                <?php } ?>
            <?php } else { ?>
                <?php if ( Utils::is_exhibitor() && Utils::get_status() !== 'Active' ) { ?>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Exhibitor,</h2>
                            <p>Your account has been validated and approved! You will receive an e-mail with your account approval. We are confident that your participation will be a fantastic opportunity to connect with buyers, enthusiasts, and industry leaders from around the world.</p>
                            <p class="fw-600">Your next task is to complete your Exhibitor Application Form in order to participate in the 2025 Wine Vision by Open Balkan Fair.</p>
                            <p class="fw-600">Thank you for joining our community of esteemed visionaries!</p>
                        </div>
                    </div>

                    <div class="row align-items-center justify-content-center py-48">
                        <div class="col-lg-8">
                            <a href="/wv-application/" class="wv-card wv-card-lg br-12 p-24 d-block wv-bg-exhibitor-gradient-light h-auto">
                                <div class="wv-card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <span class="wv wv_app-form d-flex fs-32"><span class="path1"></span><span class="path2"></span></span>
                                        <span class="fs-14 ls-2 wv-color-w px-12 py-4 wv-bg-c opacity-75 br-32">Start</span>
                                    </div>
                                    <h2 class="wv-color-w mt-48 mt-lg-64 ">Start 2025 Exhibitor<br />
                                    Application Form</h2>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } elseif ( Utils::is_exhibitor() && Utils::get_status() === 'Active' ) { ?>
                    
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Exhibitor,</h2>
                            <p>Your account is now fully active and all required steps have been successfully completed. You are officially confirmed as an Exhibitor for the 2025 Wine Vision by Open Balkan Fair.</p>
                            <p class="fw-600">Thank you for your commitment and welcome to our community of esteemed visionaries!</p>
                        </div>
                    </div>
                    
                <?php } elseif ( Utils::is_buyer() ) { ?>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Professional Buyer,</h2>
                            <?php if ( Utils::wvhb_support_category() === 'NONE' ) { ?>
                            <p>Your account has been validated and approved! You will receive an e-mail with your account approval. We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.</p>                            
                            <?php } else { ?>
                                <p>Your account has been validated and approved! You will receive an e-mail with your account approval. You have also been granted support from the Wine Vision Hosted Buyers Program. Our support team will contact you promptly, regarding the program's implementation plan and details</p>     
                                <p>We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.</p>     
                            <?php } ?>
                            <p class="fw-600">Thank you for joining our community of esteemed visionaries!</p>
                        </div>
                    </div>

                    <?php if ( Utils::wvhb_support_category() !== 'NONE' ) { ?>
                
                        <div class="row align-items-center justify-content-center py-48">
                            <div class="col-lg-8 text-center">
                                <div class="d-block wv-bg-c_5 p-24 br-12 text-center">
                                    <img src="https://winevisionfair.com/wp-content/uploads/2025/07/Hosted_Buyers_Logo_UW.png" class="img-fluid mb-24" alt="">
                                    <div class="d-flex align-items-center justify-content-between wv-bg-g br-8 p-12 wv-color-w wv-color-ww text-uppercase fs-16">
                                        <strong><?php echo esc_html( Utils::wvhb_support_category() ); ?></strong>
                                        <span class="ls-4 ms-8 me-auto"> GRANTED</span>
                                        <a href="/wv-profile/" class="wv-button wv-button-dark-op border-0 fs-14 px-12 py-8 ms-8">Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                <?php } elseif ( Utils::is_visitor() && Utils::get_visitor_participation() === 'Company' ) { ?>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Professional Visitor,</h2>
                            <?php if ( Utils::wvhb_support_category() === 'NONE' ) { ?>
                            <p>Your account has been validated and approved! You will receive an e-mail with your account approval. We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.</p>                            
                            <?php } else { ?>
                                <p>Your account has been validated and approved! You will receive an e-mail with your account approval. You have also been granted support from the Wine Vision Hosted Buyers Program. Our support team will contact you promptly, regarding the program's implementation plan and details</p>     
                                <p>We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.</p>     
                            <?php } ?>
                            <p class="fw-600">Thank you for joining our community of esteemed visionaries!</p>
                        </div>
                    </div>

                    <?php if ( Utils::wvhb_support_category() !== 'NONE' ) { ?>
                    
                        <div class="row align-items-center justify-content-center py-48">
                            <div class="col-lg-8 text-center">
                                <div class="d-block wv-bg-c_5 p-24 br-12 text-center">
                                    <img src="https://winevisionfair.com/wp-content/uploads/2025/07/Hosted_Buyers_Logo_UW.png" class="img-fluid mb-24" alt="">
                                    <div class="d-flex align-items-center justify-content-between wv-bg-g br-8 p-12 wv-color-w wv-color-ww text-uppercase fs-16">
                                        <strong><?php echo esc_html( Utils::wvhb_support_category() ); ?></strong>
                                        <span class="ls-4 ms-8 me-auto"> GRANTED</span>
                                        <a href="/wv-profile/" class="wv-button wv-button-dark-op border-0 fs-14 px-12 py-8 ms-8">Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                

                <?php } elseif ( Utils::is_visitor() && Utils::get_visitor_participation() === 'Public Visitor' ) { ?>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="fw-600 display-4 mt-0 mb-32">Dear Visitor,</h2>
                            <p>In accordance with the 2025 fair's participation rules, your account is undergoing an evaluation process. Once the process is complete, you will receive an e-mail as well as a notification here on your personal account page. If any additional information is needed, our support team will contact you promptly.</p>
                            <p class="fw-600">Thank you for your patience and understanding!</p>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } else { ?>
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="fw-600 display-4 mt-0 mb-32">Dear <?php echo Utils::get_user_names(); ?>,</h2>
                    <p>We appreciate your interest, but unfortunately, your account evaluation did not meet our requirements at this time. We encourage you to apply again in the future and we look forward to the possibility of collaboration. The information you provided during registration will be removed and will not be retained or utilized in the future.</p>
                    <p class="fw-600">Thank you for your time and effort.</p>
                    <a href="/" class="wv-button wv-button-pill wv-button-dark-op">Return to home page</a>
                </div>
            </div>
        <?php } ?>


        </div>
       
    </section>
</div>