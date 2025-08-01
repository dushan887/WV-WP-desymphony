<?php
namespace Desymphony\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX handler for saving individual profile sections from the dashboard.
 */
class DS_User_Dashboard_Profile {

	public function __construct() {
		add_action( 'wp_ajax_wv_addon_update_profile_section',
			[ $this, 'update_profile_section' ] );
		add_action( 'wp_ajax_nopriv_wv_addon_update_profile_section',
			'__return_false' );
	}

	/* ---------------------------------------------------------------------
	 *  Main dispatcher
	 * ------------------------------------------------------------------ */
	public function update_profile_section() {

		check_ajax_referer( 'wv_dashboard_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => __( 'User not authenticated.', 'wv-addon' ) ] );
		}

		$user_id = get_current_user_id();
		$section = isset( $_POST['section'] )
			? sanitize_text_field( wp_unslash( $_POST['section'] ) )
			: '';

		if ( ! $section ) {
			wp_send_json_error( [ 'message' => __( 'Section not specified.', 'wv-addon' ) ] );
		}

		/* ===============================================================
		 *  Handy helper – bulk meta update
		 * ============================================================ */
		$update_meta = function ( array $keys ) use ( $user_id ) {
			foreach ( $keys as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					$val = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
					update_user_meta( $user_id, $key, $val );
				}
			}
		};

		/* ===============================================================
		 *  Always accept the two image fields – no matter which section
		 * ============================================================ */
		if ( isset( $_POST['wv_user-avatar'] ) ) {
			update_user_meta( $user_id, 'wv_user-avatar',
				sanitize_text_field( wp_unslash( $_POST['wv_user-avatar'] ) ) );
		}
		if ( isset( $_POST['wv_user-logo'] ) ) {
			update_user_meta( $user_id, 'wv_user-logo',
				sanitize_text_field( wp_unslash( $_POST['wv_user-logo'] ) ) );
		}

		/* ===============================================================
		 *  Section‑specific handling
		 * ============================================================ */
		switch ( $section ) {

			/* ----------------------------------------------------------
			 * 1) COMPANY DESCRIPTION (aliases: company_info, company_description)
			 *    Only textarea + optional images.
			 * -------------------------------------------------------- */
			case 'company_info':            // form id = wv-company-info-form
			case 'company_description':
				$desc = isset( $_POST['wv_companyDescription'] )
					? wp_kses_post( wp_unslash( $_POST['wv_companyDescription'] ) )
					: '';
				update_user_meta( $user_id, 'wv_companyDescription', $desc );

				wp_send_json_success( [
					'message' => __( 'Company description updated.', 'wv-addon' ),
				] );
				break;

			/* ----------------------------------------------------------
			 * 2) COMPANY CREDENTIALS / INFORMATION block (address etc.)
			 *    Accepts three different section names for backward compat.
			 * -------------------------------------------------------- */
			case 'company_credentials':     // form id = wv-company-credentials-form
			case 'company_information':     // legacy
				$update_meta( [
					'wv_company_name',
					'wv_company_pobRegion',
					'wv_company_country',
					'wv_company_email',
					'wv_company_city',
					'wv_company_website',
					'wv_company_address',
					'wv_annualProductionLiters',
					'wv_currentStockLiters',
					'wv_companyDescription',       // ← added so it’s saved here too

					/* financial fields */
					'wv_company_idRegistryNumber',
					'wv_company_vatRegistryNumber',
					'wv_company_iban',
					'wv_company_foreignBank',
					'wv_company_domesticBank',
					'wv_company_foreignAccountNumber',
					'wv_company_domesticAccountNumber',
					'wv_company_foreignSwift',
					'wv_company_domesticSwift',

					'wv_socInstagram',
					'wv_socLinkedin',
					'wv_socFacebook',
					'wv_socX',


				] );

				/* keep old aliases for prod/stock in sync */
				if ( isset( $_POST['wv_annualProductionLiters'] ) ) {
					update_user_meta( $user_id, 'wv_annual_production',
						sanitize_text_field( wp_unslash( $_POST['wv_annualProductionLiters'] ) ) );
				}
				if ( isset( $_POST['wv_currentStockLiters'] ) ) {
					update_user_meta( $user_id, 'wv_current_stock',
						sanitize_text_field( wp_unslash( $_POST['wv_currentStockLiters'] ) ) );
				}

				wp_send_json_success( [
					'message' => __( 'Company information updated.', 'wv-addon' ),
				] );
				break;

			/* ----------------------------------------------------------
			 * 3) COMPANY FINANCIAL INFORMATION
			 * -------------------------------------------------------- */
			case 'company_financial_information':
				$update_meta( [
					'wv_company_idRegistryNumber',
					'wv_company_vatRegistryNumber',
					'wv_company_iban',
					'wv_company_foreignBank',
					'wv_company_domesticBank',
					'wv_company_foreignAccountNumber',
					'wv_company_domesticAccountNumber',
					'wv_company_foreignSwift',
					'wv_company_domesticSwift',
				] );

				wp_send_json_success( [
					'message' => __( 'Company financial information updated.', 'wv-addon' ),
				] );
				break;

			/* ----------------------------------------------------------
			 * 4) REPRESENTATIVE CREDENTIALS
			 * -------------------------------------------------------- */
			case 'representative_credentials':

				/* core WP first/last name */
				if ( isset( $_POST['wv_firstName'] ) ) {
					$first = sanitize_text_field( wp_unslash( $_POST['wv_firstName'] ) );
					update_user_meta( $user_id, 'first_name',   $first );
					update_user_meta( $user_id, 'wv_firstName', $first );
				}
				if ( isset( $_POST['wv_lastName'] ) ) {
					$last  = sanitize_text_field( wp_unslash( $_POST['wv_lastName'] ) );
					update_user_meta( $user_id, 'last_name',    $last );
					update_user_meta( $user_id, 'wv_lastName',  $last );
				}

				$update_meta( [
					'wv_nationality',
					'wv_professionalOccupation',
					'wv_yearsOfExperience',
					'wv_positionInCompany',
					'wv_contactTelephone',
					'wv_exhibitor_rep_whatsapp',
					'wv_exhibitor_rep_viber',

					// public‑visitor location
					'wv_countryOfResidence',
					'wv_cityOfResidence',
				] );

				wp_send_json_success( [
					'message' => __( 'Representative credentials updated.', 'wv-addon' ),
				] );
				break;

			/* ----------------------------------------------------------
			 * Default
			 * -------------------------------------------------------- */
			default:
				wp_send_json_error( [ 'message' => __( 'Invalid profile section.', 'wv-addon' ) ] );
		}
	}
}
