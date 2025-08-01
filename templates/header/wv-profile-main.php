<?php
/* ---------------------------------------------------------
 *  Dashboard top-nav – dynamic avatar & company / user name
 * --------------------------------------------------------- */
use Desymphony\Helpers\DS_Utils as Utils;

$current_user = wp_get_current_user();
$solo_max_slots = Utils::solo_ex_max_slots( $current_user->ID );

$company_name = $current_user->ID
	? get_user_meta( $current_user->ID, 'wv_company_name', true )
	: '';

$display_name = $company_name ?: trim(
	$current_user->first_name . ' ' . $current_user->last_name
);
if ( empty( $display_name ) ) {
	$display_name = __( 'Guest', 'textdomain' );
}

/* pick logo → avatar → WP avatar → placeholder */
$logo_url   = $current_user->ID ? get_user_meta( $current_user->ID, 'wv_user-logo',   true ) : '';
$avatar_url = $current_user->ID ? get_user_meta( $current_user->ID, 'wv_user-avatar', true ) : '';

$img_url = $logo_url ?: $avatar_url;
if ( empty( $img_url ) && $current_user->ID ) {
	$img_url = get_avatar_url( $current_user->ID, [ 'size' => 100 ] );
}
if ( empty( $img_url ) ) {
	$img_url = 'https://placehold.co/100?text=+';   // final fallback
}

?>

<div id="wv-dashboard-nav" class="collapse w-100">
	<div class="container">
		<div class="row">
			<!-- Left: Avatar + Company / User Name -->
			<div class="col-lg-4 pb-8">
				<div class="d-flex flex-column flex-lg-row d-lg-inline-flex align-items-center justify-content-center justify-content-lg-start pt-32 pt-lg-0">
					<a href="/wv-profile/" class="wv-avatar-circle">
						<img src="<?php echo esc_url( $img_url ); ?>" alt="avatar" />
					</a>

					<div class="wv-company-name ms-lg-12 text-center text-lg-start pt-24 pt-lg-0">
						<h4 class="m-0 ps-4 pt-12 fs-20 fw-400 d-block wv-color-ww">
							<?php echo esc_html( $display_name ); ?>
						</h4>

						<div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-8 pt-12 pt-lg-4">
							<a href="/wv-profile/"  class="wv-button wv-button-c wv-button-pill py-4 px-8 fs-12 text-uppercase lh-1 ls-4 fw-600 my-4">
								<?php esc_html_e( 'My Profile', 'textdomain' ); ?>
							</a>
							
							<?php if ( Utils::get_exhibitor_participation() === 'Head Exhibitor' && Utils::get_status() === 'Active' ) : ?>
							<a href="/wv-co-ex/" class="wv-button wv-button-pill wv-bg-v_dark py-4 px-8 fs-12 text-uppercase lh-1 ls-4 fw-600 my-4">
								<?php esc_html_e( 'Members', 'textdomain' ); ?>
							</a>							
							<?php elseif ( Utils::get_exhibitor_participation() === 'Solo Exhibitor'
							&& Utils::get_status() === 'Active'
							&& $solo_max_slots > 0 ) : ?>
							<a href="/wv-co-ex/" class="wv-button wv-button-pill wv-bg-v_dark py-4 px-8 fs-12 text-uppercase lh-1 ls-4 fw-600 my-4">
								<?php esc_html_e( 'Co-Exhibitors', 'textdomain' ); ?>
							</a>
							<?php elseif ( Utils::get_exhibitor_participation() === 'Co-Exhibitor' && Utils::get_status() === 'Active' ) : ?>
							<a href="/wv-co-ex/" class="wv-button wv-button-pill wv-bg-v_dark py-4 px-8 fs-12 text-uppercase lh-1 ls-4 fw-600 my-4 d-none">
								<?php esc_html_e( 'Head Exhibitor', 'textdomain' ); ?>
							</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div class="d-block d-lg-none border-top wv-bc-w pt-24 mt-24 opacity-75"></div>

			<!-- Right: Navigation -->
			<div class="col-lg-8 pt-0 pb-0 d-flex justify-content-end align-items-end pt-lg-4 pb-lg-8">
				<nav class="navbar py-0 mx-auto mx-lg-0" data-bs-theme="dark">
					<ul class="navbar-nav flex-lg-row ms-auto fs-14 align-items-center justify-content-center justify-content-lg-end gap-0 gap-lg-12">
						<li class="nav-item px-8"><a class="nav-link" href="/wv-dashboard"><?php esc_html_e( 'Home', 'textdomain' ); ?></a></li>
						<?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
						<li class="nav-item px-8"><a class="nav-link" href="/wv-meeting"><?php esc_html_e( 'Meeting requests', 'textdomain' ); ?></a></li>
						<li class="nav-item px-8"><a class="nav-link" href="/wv-calendar"><?php esc_html_e( 'Calendar', 'textdomain' ); ?></a></li>
						<?php endif; ?>
						<li class="nav-item px-8"><a class="nav-link" href="/wv-events"><?php esc_html_e( 'Events', 'textdomain' ); ?></a></li>
						<?php if ( Utils::is_exhibitor() ) : ?>
						<li class="nav-item px-8"><a class="nav-link" href="/wv-products"><?php esc_html_e( 'Products', 'textdomain' ); ?></a></li>
						<li class="nav-item px-8"><a class="nav-link" href="/wv-services"><?php esc_html_e( 'Services', 'textdomain' ); ?></a></li>
						<?php endif; ?>
						<?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
						<li class="nav-item ps-8 d-none d-lg-block">
							<a class="nav-link wv-button wv-button-sm wv-button-pill wv-icon-button p-0" href="/wv-messages">
								<i class="wv wv_msg fs-20"><span class="path1 opacity-0"></span><span class="path2"></span></i>
							</a>
						</li>
						<?php endif; ?>
						<li class="nav-item ps-8 d-none d-lg-block">
							<a class="nav-link wv-button wv-button-sm wv-button-pill wv-icon-button p-0" href="/wv-saved">
								<i class="wv wv_saved fs-20"><span class="path1 opacity-0"></span><span class="path2"></span></i>
							</a>
						</li>
						<li class="nav-item px-8 d-none d-lg-block">
							<a class="nav-link wv-color-c_10 fs-12" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
								<?php esc_html_e( 'Log out', 'textdomain' ); ?>
							</a>
						</li>
					</ul>
				</nav>
			</div>

			<div class="d-block d-lg-none border-top wv-bc-w pt-24 mt-24 opacity-75"></div>
			<div class="col-12 d-flex d-lg-none justify-content-center pb-24">
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="wv-button wv-button-pill wv-bg-c wv-button-sm">
						<?php esc_html_e( 'Log out', 'textdomain' ); ?>
					</a>
				<?php endif; ?>
			</div>

		</div>
	</div>
</div>
