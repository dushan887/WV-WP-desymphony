
<section class="d-block wv-bg-w py-24">
    <div class="container container-1024">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="my-0 lh-1 fw-600 fs-20">2025 Fair Stands</h3>
            <a href="#" id="toggle-stand-prices" class="wv-button wv-button-c2 wv-button--outline wv-button-pill wv-button-sm me-8 d-none d-lg-flex align-items-center px-8">
                Stand Prices
                <span class="wv wv_point-70 ms-4 fs-20" style="margin: -4px">
                    <span class="path1"></span><span class="path2"></span>
                </span>
            </a>

        </div>
    </div>  
</section>

<section id="wv-stand-prices" class="d-none wv-bg-c_10 py-24">
    <div class="container-fluid px-lg-128">
        <div class="d-block w-100">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/DSK_Stand_Prices.svg" alt="" class="img-fluid m-auto d-none d-lg-block">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/MOB_Stand_Prices.svg" alt="" class="img-fluid m-auto d-block d-lg-none">
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('toggle-stand-prices');
    const map = document.getElementById('wv-stand-prices');
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        map.classList.toggle('d-none');
    });
});
</script>