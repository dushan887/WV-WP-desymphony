<?php
/**
 * Template for the DS Support block.
 *
 * @package Desymphony
 */


if (! defined('ABSPATH')) exit;


$class = 'ds-support';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    #wv-wrap::before {
        background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Support_Bck.jpg) !important;
    }
    @media screen and (max-width: 768px) {
        #wv-wrap::before {
            background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_Support_Bck.jpg) !important;
        }
    }
</style>
<div id="wv-wrap" class="py-0">
    <section class="position-relative">
        <div class="border-bottom wv-bc-w py-48">
            <div class="container container-1024">
                <div class="d-flex">
                    <h1 class="my-0 lh-1 fw-600 wv-color-w wv-color-ww">Support</g1>
                </div>
            </div>
        </div>

        <div class="container container-1024 py-48">
            <div class="row">
                <div class="col-12">
                    <p class="h3 fw-600">Should you have any inquiries or require assistance, our dedicated team is ready to ensure you have the best possible experience! Send us a question, and we will get back to You as soon as possible</p>
                </div>

                <div class="col-12 pt-32">
                    [gravityform id="1" title="false" description="false" ajax="true"]
                </div>
            </div>
        </div>

    </section>
</div>

