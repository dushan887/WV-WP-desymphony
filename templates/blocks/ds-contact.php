<?php
/**
 * Template for the DS Contact block.
 *
 * @package Desymphony
 */


if (! defined('ABSPATH')) exit;


$class = 'ds-contact';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    #wv-wrap::before {
        background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Contact_Bck.jpg)!important;
    }
    @media screen and (max-width: 768px) {
        #wv-wrap::before {
            background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_Contact_Bck.jpg)!important;
        }
    }
</style>
<div id="wv-wrap" class="py-0">
    <section class="position-relative">
        <div class="border-bottom wv-bc-w py-48">
            <div class="container container-1024">
                <div class="d-flex">
                    <h1 class="my-0 lh-1 fw-600 wv-color-w wv-color-ww">Contact</g1>
                </div>
            </div>
        </div>

        <!-- Contacts – Wine Vision Fair (all links clickable) -->
        <div class="container container-1024 py-48">
        <!-- Belgrade Fair -->
        <div class="row">
            <div class="col-12">
                
                <h6 class="text-uppercase fw-bold mb-12 fw-600 ls-3 fs-18">Belgrade Fair</h6>
            </div>
            <div class="col-md-6 pb-24">
            <p class="mb-6">Address</p>
            <a href="https://www.google.com/maps/search/?api=1&query=Bulevar+Vojvode+Mišića+14,+11030+Belgrade,+Serbia"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none" target="_blank">
                Bulevar Vojvode Mišića 14, 11030 Belgrade, Serbia
            </a>

            <p class="mt-12 mb-6">Wine Vision by Open Balkan department</p>
            <a href="mailto:ob-winefair@sajam.rs"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none">
                ob-winefair@sajam.rs
            </a>
            <div class="d-block border-bottom pb-24 wv-bc-c_50"></div>
            </div>

            <div class="col-md-6 pb-24">
            <p class="mb-6">Telephones</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="tel:+381112655239"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none me-8">
                + 381&nbsp;11&nbsp;/&nbsp;26&nbsp;55&nbsp;239
                </a>
                <a href="tel:+381112655114"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none">
                + 381&nbsp;11&nbsp;/&nbsp;26&nbsp;55&nbsp;114
                </a>
            </div>

            <p class="mt-12 mb-6">Custom stands production department</p>
            <a href="mailto:arh@sajam.rs"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none">
                arh@sajam.rs
            </a>
            <div class="d-block border-bottom pb-24 wv-bc-c_50"></div>
            </div>
        </div>

        <!-- National Chamber of Commerce -->
        <div class="row">
            <div class="col-12">
                
            </div>
            <div class="col-md-6 pb-24">
            <p class="mb-6">Chamber of Commerce and Industry of Serbia</p>
            <a href="mailto:winevision@pks.rs"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none">
                winevision@pks.rs
            </a>
            <div class="d-block border-bottom pb-24 wv-bc-c_50"></div>
            </div>

            <div class="col-md-6 pb-24">
            <p class="mb-6">Telephones</p>
            <a href="tel:+381638164365"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none">
                + 381&nbsp;63&nbsp;/&nbsp;81&nbsp;64&nbsp;365
            </a>
            <div class="d-block border-bottom pb-24 wv-bc-c_50"></div>
            </div>
        </div>

        <!-- Wine Vision Support -->
        <div class="row">
            <div class="col-12">
                
            <h6 class="text-uppercase fw-bold mb-12 fw-600 ls-3 fs-18">Wine Vision Support</h6>
            </div>
            <div class="col-md-6 pb-24">
            <p class="mb-6">2025 support team</p>
            <a href="mailto:support@winevisionfair.com"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none">
                support@winevisionfair.com
            </a>
            <div class="d-block border-bottom pb-24 wv-bc-c_50"></div>
            </div>

            <div class="col-md-6 pb-24">
            <p class="mb-6">Telephone</p>
            <a href="tel:+381641027177"
                class="badge wv-bg-c_50 fs-16 fw-500 rounded-pill text-decoration-none">
                + 381&nbsp;64&nbsp;/&nbsp;10&nbsp;27&nbsp;177
            </a>
            <div class="d-block border-bottom pb-24 wv-bc-c_50"></div>
            </div>
        </div>
        </div>

    </section>
</div>