<?php
/**
 * Exhibitor profile view with “Download my stand” (native print → PDF) 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Desymphony\Helpers\DS_Utils as Utils;
?>

<?php if ( Utils::get_status() === 'Disabled' ) : ?>
	<?php wp_safe_redirect( home_url( '/wv-dashboard/' ) ); exit; ?>
<?php endif; ?>

<div class="container-fluid px-0 tab-content">

	<div id="wv-general" class="tab-pane fade container container-1024 px-0 pb-64 show active">
		<?php include DS_THEME_DIR . '/inc/public/views/dashboard/profile/general-main.php'; ?>
	</div>

	<?php if ( Utils::is_exhibitor() ) : ?>

	<div id="wv-stand" class="tab-pane fade">
		<div id="wv-stand-content">
		<?php if ( Utils::get_status() === 'Active' ) : ?>
			<?php
				include DS_THEME_DIR . '/inc/public/views/dashboard/profile/ex-space-stand-1.php';
        		include DS_THEME_DIR . '/inc/public/views/dashboard/profile/ex-space-stand-2.php';
				include DS_THEME_DIR . '/inc/public/views/dashboard/profile/ex-space-stand-3.php';
			?>
		<?php else : ?>
			<?php include DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-no-access.php'; ?>
		<?php endif; ?>
		</div>

		<div class="container py-48 text-center">
			<button id="ds-stand-download"
					class="wv-button wv-button-outline br-4 mt-16">
				<?php esc_html_e( 'Download PDF', 'wv-addon' ); ?>
			</button>
		</div>
	</div>

	<div id="wv-guestlist"   class="tab-pane fade"><?php include DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-coming-fall.php'; ?></div>
	<div id="wv-advertising" class="tab-pane fade"><?php include DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-coming-soon.php'; ?></div>

	<?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>


<!-- ─── Print helper (no pop‑ups, uses @media print) -->
<script>
(() => {
document.getElementById('ds-stand-download').addEventListener('click', async () => {
  const { jsPDF } = window.jspdf;
  const stand = document.getElementById('wv-stand-content');

  const originalWidth = stand.style.width;
  const originalBackground = stand.style.backgroundColor;

  stand.style.width = '1240px';
  stand.style.backgroundColor = 'rgb(231,230,232)';

  try {
    const canvas = await html2canvas(stand, {
      scale: 1, // reduce scale for smaller file
      windowWidth: 1240,
      backgroundColor: 'rgb(231,230,232)',
      scrollY: -window.scrollY,
      useCORS: true
    });

    const imgData = canvas.toDataURL('image/jpeg', 1);

    const pdf = new jsPDF('p', 'pt', [canvas.width, canvas.height]);
    pdf.addImage(imgData, 'JPEG', 0, 0, canvas.width, canvas.height, '', 'FAST');

    pdf.save('stand.pdf');

  } catch (err) {
    console.error('Error generating PDF:', err);
  } finally {
    stand.style.width = originalWidth;
    stand.style.backgroundColor = originalBackground;
  }
});

document.addEventListener('DOMContentLoaded', () => {

  /* run only when the query‑string is .../?tab=wv-stand */
  if (new URLSearchParams(location.search).get('tab') !== 'wv-stand') return;

  /* ①  Prefer Bootstrap’s API (if nav button exists) */
  const btn = document.querySelector('[data-bs-target="#wv-stand"]');
  if (btn && window.bootstrap && bootstrap.Tab) {
    new bootstrap.Tab(btn).show();
    return;
  }

  /* ②  Fallback: toggle classes manually */
  document.querySelectorAll('.tab-pane').forEach(p =>
    p.classList.remove('show', 'active'));

  document.querySelectorAll('[data-bs-toggle="tab"]').forEach(b => {
    b.classList.remove('active');
    b.setAttribute('aria-selected', 'false');
  });

  const pane = document.getElementById('wv-stand');
  if (pane) pane.classList.add('show', 'active');

  if (btn) {
    btn.classList.add('active');
    btn.setAttribute('aria-selected', 'true');
  }
});
})();
</script>
