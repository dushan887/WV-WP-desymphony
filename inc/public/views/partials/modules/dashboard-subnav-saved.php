<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// We'll read from $GLOBALS['wv_saved_counts'] which we set in dashboard-saved.php
$counts = $GLOBALS['wv_saved_counts'] ?? [
    'exhibitors'=>0,
    'buyers'=>0,
    'wine'=>0,
    'rakija'=>0,
    'food'=>0,
    'other'=>0
];
?>

<div class="d-block wv-position-relative wv-shadow-lg wv-z-50 py-8" style="background-color: var(--wv-w)">
    <div class="wv-container-1320 px-0">
        <div class="row">
            <div class="col-12 d-flex align-items-center">
                <div class="wv-tab-header">

                    <a href="#"
                       class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-sm active"
                       data-tab="wv-saved-exhibitors"
                    >
                       Exhibitors (<?php echo intval($counts['exhibitors']); ?>)
                    </a>

                    <a href="#"
                       class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-sm"
                       data-tab="wv-saved-buyers"
                    >
                       Buyers (<?php echo intval($counts['buyers']); ?>)
                    </a>

                    <a href="#"
                       class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-sm"
                       data-tab="wv-saved-wine"
                    >
                       Wine (<?php echo intval($counts['wine']); ?>)
                    </a>

                    <a href="#"
                       class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-sm"
                       data-tab="wv-saved-rakija"
                    >
                       Rakija (<?php echo intval($counts['rakija']); ?>)
                    </a>

                    <a href="#"
                       class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-sm"
                       data-tab="wv-saved-food"
                    >
                       Food (<?php echo intval($counts['food']); ?>)
                    </a>

                    <a href="#"
                       class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-sm"
                       data-tab="wv-saved-other"
                    >
                       Other (<?php echo intval($counts['other']); ?>)
                    </a>

                </div><!-- .wv-tab-header -->
            </div>
        </div>
    </div>
</div>
