<?php
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DS_Meta_Fields {

	/**
	 * Fields used on step 1 (global for all flows)
	 */
	public static function get_global_fields(): array {
		return [
			[
				'field_slug'     => 'wv_profileSelection',
				'field_type'     => 'single_selection',
				'field_question' => 'Profile Selection',
				'field_options'  => ['Exhibitor', 'Buyer', 'Visitor'],
			],
			[
				'field_slug'     => 'wv_status',        
				'field_type'     => 'single_selection',
				'field_question' => 'Account Status',
				'field_options'  => ['Pending', 'Active', 'Disabled'],
			],
		];
	}

	/**
	 * Fields for Exhibitors.
	 */
	public static function get_exhibitor_fields(): array {
		return [
			[
                'field_slug'     => 'wv_ex_stage1_verified',
                'field_type'     => 'checkbox',
                'field_question' => 'Exhibitor – Stage 1 verified',
            ],
            [
                'field_slug'     => 'wv_ex_stage2_verified',
                'field_type'     => 'checkbox',
                'field_question' => 'Exhibitor – Stage 2 verified',
            ],
			[
				'field_slug'     => 'wv_fieldOfWork',
				'field_type'     => 'single_selection',
				'field_question' => 'Wine, Spirits or Food?',
				'field_options'  => ['Wine', 'Spirits', 'Food'],
			],
			[
				'field_slug'     => 'wv_participationModel',
				'field_type'     => 'single_selection',
				'field_question' => 'You Are Applying as:',
				'field_options'  => ['Solo Exhibitor', 'Head Exhibitor', 'Co-Exhibitor'],
			],
			[
				'field_slug'     => 'wv_userCategory',
				'field_type'     => 'single_selection',
				'field_question' => 'User Category',
				'field_options'  => [], // Dynamically set by participation model/field of work
			],
			[
				'field_slug'     => 'wv_userCategoryOtherDescription',
				'field_type'     => 'textarea',
				'field_question' => 'Please describe your User Category (Other)',
			],
			[
				'field_slug'     => 'wv_exhibitingProducts',
				'field_type'     => 'single_selection',
				'field_question' => 'Are You Exhibiting Products?',
				'field_options'  => ['Yes', 'No'],
			],
			[
				'field_slug'     => 'wv_companyDescription',
				'field_type'     => 'textarea',
				'field_question' => 'About Your Company',
			],
			// Company Info
			[
				'field_slug'     => 'wv_company_name',
				'field_type'     => 'text',
				'field_question' => 'Company Full Name',
			],
			[
				'field_slug'     => 'wv_company_pobRegion',
				'field_type'     => 'text',
				'field_question' => 'P.O.B. / Area / Municipality / Region',
			],
			[
				'field_slug'     => 'wv_company_country',
				'field_type'     => 'text',
				'field_question' => 'Country of Residence',
			],
			[
				'field_slug'     => 'wv_company_email',
				'field_type'     => 'email',
				'field_question' => 'E-mail Address',
			],
			[
				'field_slug'     => 'wv_company_city',
				'field_type'     => 'text',
				'field_question' => 'City of Residence',
			],
			[
				'field_slug'     => 'wv_company_website',
				'field_type'     => 'text',
				'field_question' => 'Website',
			],
			[
				'field_slug'     => 'wv_company_address',
				'field_type'     => 'text',
				'field_question' => 'Address (Street & Number)',
			],
			[
				'field_slug'     => 'wv_company_phone',
				'field_type'     => 'text',
				'field_question' => 'Contact (Telephone)',
			],
			[
				'field_slug'     => 'wv_annualProductionLiters',
				'field_type'     => 'number',
				'field_question' => 'Annual Production',
			],
			[
				'field_slug'     => 'wv_currentStockLiters',
				'field_type'     => 'number',
				'field_question' => 'Currently in Stock',
			],
			[
				'field_slug'     => 'wv_company_idRegistryNumber',
				'field_type'     => 'text',
				'field_question' => 'ID Registry Number',
			],
			[
				'field_slug'     => 'wv_company_vatRegistryNumber',
				'field_type'     => 'text',
				'field_question' => 'VAT Registry Number',
			],
			[
				'field_slug'     => 'wv_company_iban',
				'field_type'     => 'text',
				'field_question' => 'IBAN',
			],
			[
				'field_slug'     => 'wv_company_domesticBank',
				'field_type'     => 'text',
				'field_question' => 'Domestic Exchange Bank',
			],
			[
				'field_slug'     => 'wv_company_foreignBank',
				'field_type'     => 'text',
				'field_question' => 'Foreign Exchange Correspondent Bank',
			],
			[
				'field_slug'     => 'wv_company_domesticAccountNumber',
				'field_type'     => 'text',
				'field_question' => 'Domestic Exchange Account Number',
			],
			[
				'field_slug'     => 'wv_company_foreignAccountNumber',
				'field_type'     => 'text',
				'field_question' => 'Foreign Exchange Account Number',
			],
			[
				'field_slug'     => 'wv_company_domesticSwift',
				'field_type'     => 'text',
				'field_question' => 'Beneficiary Swift Code',
			],
			[
				'field_slug'     => 'wv_company_foreignSwift',
				'field_type'     => 'text',
				'field_question' => 'Foreign Exchange Swift Code',
			],
			// Social
			[
				'field_slug'     => 'wv_socInstagram',
				'field_type'     => 'text',
				'field_question' => 'Instagram',
			],
			[
				'field_slug'     => 'wv_socLinkedin',
				'field_type'     => 'text',
				'field_question' => 'LinkedIn',
			],
			[
				'field_slug'     => 'wv_socFacebook',
				'field_type'     => 'text',
				'field_question' => 'Facebook',
			],
			[
				'field_slug'     => 'wv_socX',
				'field_type'     => 'text',
				'field_question' => 'X (ex Twitter)',
			],
			[
				'field_slug'     => 'wv_user-logo',
				'field_type'     => 'file',
				'field_question' => 'Company Logo',
			],
			[
				'field_slug'     => 'wv_user-avatar',
				'field_type'     => 'file',
				'field_question' => 'Profile Photo',
			],
			// Representative
			[
				'field_slug'     => 'wv_firstName',
				'field_type'     => 'text',
				'field_question' => 'First Name',
			],
			[
				'field_slug'     => 'wv_lastName',
				'field_type'     => 'text',
				'field_question' => 'Last Name',
			],
			[
				'field_slug'     => 'wv_professionalOccupation',
				'field_type'     => 'text',
				'field_question' => 'Professional Occupation',
			],
			[
				'field_slug'     => 'wv_yearsOfExperience',
				'field_type'     => 'number',
				'field_question' => 'Years of Professional Experience',
			],
			[
				'field_slug'     => 'wv_nationality',
				'field_type'     => 'text',
				'field_question' => 'Nationality',
			],
			[
				'field_slug'     => 'wv_email',
				'field_type'     => 'email',
				'field_question' => 'E-mail Address',
			],
			[
				'field_slug'     => 'wv_positionInCompany',
				'field_type'     => 'text',
				'field_question' => 'Position in the Company',
			],
			[
				'field_slug'     => 'wv_contactTelephone',
				'field_type'     => 'tel',
				'field_question' => 'Contact (Telephone)',
			],
			[
				'field_slug'     => 'wv_exhibitor_rep_whatsapp',
				'field_type'     => 'checkbox',
				'field_question' => 'Available on WhatsApp',
			],
			[
				'field_slug'     => 'wv_exhibitor_rep_viber',
				'field_type'     => 'checkbox',
				'field_question' => 'Available on Viber',
			],
			// Password & Terms
			[
				'field_slug'     => 'wv_user_password',
				'field_type'     => 'password',
				'field_question' => 'Password',
			],
			[
				'field_slug'     => 'wv_password_confirm',
				'field_type'     => 'password',
				'field_question' => 'Confirm Password',
			],
			[
				'field_slug'     => 'terms_conditions',
				'field_type'     => 'checkbox',
				'field_question' => 'I have read, and Agree to the Terms & Conditions',
			],
		];
	}

	/**
	 * Fields for Buyers & Visitors (most are shared, with a few unique per role).
	 */
	public static function get_buyer_visitor_fields(): array {
		return [
			[
				'field_slug'     => 'wv_wvhb_support',
				'field_type'     => 'single_selection',
				'field_question' => 'Wine Vision Hosted Buyers Program',
				'field_options'  => [
					'Category IV',
					'Category III',
					'Category II',
					'Category I',
					'NONE',
				],
			],
			[
				'field_slug'     => 'wv_userCategory',
				'field_type'     => 'single_selection',
				'field_question' => 'User Category',
				'field_options'  => [], // Dynamic
			],
			[
				'field_slug'     => 'wv_participationModel',
				'field_type'     => 'single_selection',
				'field_question' => 'You Are Applying as:',
				'field_options'  => ['Company', 'Public Visitor'],
			],
			[
				'field_slug'     => 'wv_userCategoryOtherDescription',
				'field_type'     => 'textarea',
				'field_question' => 'Describe Your category',
			],
			[
				'field_slug'     => 'wv_reasonsForVisiting',
				'field_type'     => 'multi_selection',
				'field_question' => 'Reasons for Visiting',
				'field_options'  => [], // Dynamic
			],
			[
				'field_slug'     => 'wv_otherReasonsForVisiting',
				'field_type'     => 'textarea',
				'field_question' => 'Other Reasons For Visiting',
			],
			[
				'field_slug'     => 'wv_pointsOfInterest',
				'field_type'     => 'multi_selection',
				'field_question' => 'Points of Interest',
				'field_options'  => [], // Dynamic (tags)
			],
			[
				'field_slug'     => 'wv_reasonForApplying',
				'field_type'     => 'textarea',
				'field_question' => 'Reason for Applying',
			],
			[
				'field_slug'     => 'wv_companyDescription',
				'field_type'     => 'textarea',
				'field_question' => 'About Your Company',
			],
			[
				'field_slug'     => 'wv_company_name',
				'field_type'     => 'text',
				'field_question' => 'Company Full Name',
			],
			[
				'field_slug'     => 'wv_company_pobRegion',
				'field_type'     => 'text',
				'field_question' => 'P.O.B. / Area / Municipality / Region',
			],
			[
				'field_slug'     => 'wv_company_country',
				'field_type'     => 'text',
				'field_question' => 'Country of Residence',
			],
			[
				'field_slug'     => 'wv_company_email',
				'field_type'     => 'email',
				'field_question' => 'E-mail Address',
			],
			[
				'field_slug'     => 'wv_company_city',
				'field_type'     => 'text',
				'field_question' => 'City of Residence',
			],
			[
				'field_slug'     => 'wv_company_website',
				'field_type'     => 'text',
				'field_question' => 'Website',
			],
			[
				'field_slug'     => 'wv_company_address',
				'field_type'     => 'text',
				'field_question' => 'Address (Street & Number)',
			],
			[
				'field_slug'     => 'wv_company_phone',
				'field_type'     => 'text',
				'field_question' => 'Contact (Telephone)',
			],
			[
				'field_slug'     => 'wv_governmentSupport',
				'field_type'     => 'checkbox',
				'field_question' => 'Government Support Program',
			],
			[
				'field_slug'     => 'wv_socInstagram',
				'field_type'     => 'text',
				'field_question' => 'Instagram',
			],
			[
				'field_slug'     => 'wv_socLinkedin',
				'field_type'     => 'text',
				'field_question' => 'LinkedIn',
			],
			[
				'field_slug'     => 'wv_socFacebook',
				'field_type'     => 'text',
				'field_question' => 'Facebook',
			],
			[
				'field_slug'     => 'wv_socX',
				'field_type'     => 'text',
				'field_question' => 'X (ex Twitter)',
			],
			[
				'field_slug'     => 'wv_user-logo',
				'field_type'     => 'file',
				'field_question' => 'Company Logo',
			],
			[
				'field_slug'     => 'wv_user-avatar',
				'field_type'     => 'file',
				'field_question' => 'Profile Photo',
			],
			[
				'field_slug'     => 'wv_firstName',
				'field_type'     => 'text',
				'field_question' => 'First Name',
			],
			[
				'field_slug'     => 'wv_lastName',
				'field_type'     => 'text',
				'field_question' => 'Last Name',
			],
			[
				'field_slug'     => 'wv_professionalOccupation',
				'field_type'     => 'text',
				'field_question' => 'Professional Occupation',
			],
			[
				'field_slug'     => 'wv_yearsOfExperience',
				'field_type'     => 'number',
				'field_question' => 'Years of Professional Experience',
			],
			[
				'field_slug'     => 'wv_nationality',
				'field_type'     => 'text',
				'field_question' => 'Nationality',
			],
			[
				'field_slug'     => 'wv_email',
				'field_type'     => 'email',
				'field_question' => 'E-mail Address',
			],
			[
				'field_slug'     => 'wv_positionInCompany',
				'field_type'     => 'text',
				'field_question' => 'Position in the Company',
			],
			[
				'field_slug'     => 'wv_contactTelephone',
				'field_type'     => 'tel',
				'field_question' => 'Contact (Telephone)',
			],
			[
				'field_slug'     => 'wv_exhibitor_rep_whatsapp',
				'field_type'     => 'checkbox',
				'field_question' => 'Available on WhatsApp',
			],
			[
				'field_slug'     => 'wv_exhibitor_rep_viber',
				'field_type'     => 'checkbox',
				'field_question' => 'Available on Viber',
			],
			// Password & Terms
			[
				'field_slug'     => 'wv_user_password',
				'field_type'     => 'password',
				'field_question' => 'Password',
			],
			[
				'field_slug'     => 'wv_password_confirm',
				'field_type'     => 'password',
				'field_question' => 'Confirm Password',
			],
			[
				'field_slug'     => 'terms_conditions',
				'field_type'     => 'checkbox',
				'field_question' => 'I have read, and Agree to the Terms & Conditions',
			],
		];
	}

	/** Return the human label for a slug (falls back to slug). */
	public static function label(string $slug): string
	{
		static $map = null;

		if ($map === null) {                         // build once per request
			$defs = array_merge(
				self::get_global_fields(),
				self::get_exhibitor_fields(),
				self::get_buyer_visitor_fields()
			);
			foreach ($defs as $d) {
				$map[$d['field_slug']] = $d['field_question'];
			}
		}

		return $map[$slug] ?? $slug;
	}


}
