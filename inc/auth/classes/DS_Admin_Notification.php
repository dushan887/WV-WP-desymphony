<?php

/**
 * Wine Vision 2025 – transactional e-mails
 * File: wp-content/themes/desymphony/inc/auth/classes/DS_Admin_Notification.php
 */

namespace Desymphony\Auth\Classes;

use Desymphony\Helpers\DS_Utils;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class DS_Admin_Notification {

    /** Capability required to trigger mails by AJAX */
    private const CAP = 'manage_wv_addon';

    /** Map slug → template-builder */
    private const TEMPLATES = [
        /* Exhibitor – account state */
        'exhibitor_validating'          => [ self::class, 'tpl_exhibitor_validating' ],
        'exhibitor_approved'            => [ self::class, 'tpl_exhibitor_approved' ],
        'exhibitor_rejected'            => [ self::class, 'tpl_exhibitor_rejected' ],
        /* Exhibitor – stands reserved */
        'exhibitor_stands_reserved'     => [ self::class, 'tpl_exhibitor_stands_reserved' ], // plural
        'exhibitor_stand_reserved'      => [ self::class, 'tpl_exhibitor_stand_reserved'  ], // singular
        /* Invitations */
        'invite_member'                 => [ self::class, 'tpl_invite_member' ],
        'invite_coexhibitor'            => [ self::class, 'tpl_invite_coexhibitor' ],
        /* Professional Buyer */
        'buyer_validating'              => [ self::class, 'tpl_buyer_validating' ],
        'buyer_approved'                => [ self::class, 'tpl_buyer_approved' ],
        'buyer_approved_hosted'         => [ self::class, 'tpl_buyer_approved_hosted' ],
        'buyer_rejected'                => [ self::class, 'tpl_buyer_rejected' ],
        /* Professional Visitor */
        'provisitor_validating'         => [ self::class, 'tpl_provisitor_validating' ],
        'provisitor_approved'           => [ self::class, 'tpl_provisitor_approved' ],
        'provisitor_approved_hosted'    => [ self::class, 'tpl_provisitor_approved_hosted' ],
        'provisitor_rejected'           => [ self::class, 'tpl_provisitor_rejected' ],
        /* Visitor */
        'visitor_evaluating'            => [ self::class, 'tpl_visitor_evaluating' ],
        'visitor_approved'              => [ self::class, 'tpl_visitor_approved' ],
        'visitor_rejected'              => [ self::class, 'tpl_visitor_rejected' ],
        'coex_invite_accepted'  => [ self::class, 'tpl_coex_invite_accepted'  ],
        'coex_invite_declined'  => [ self::class, 'tpl_coex_invite_declined' ],
    ];

    private static function tpl_coex_invite_accepted( \WP_User $u, array $ctx ): array {
        return self::tpl_invite_accepted( $u, $ctx );
    }

    private static function tpl_coex_invite_declined( \WP_User $u, array $ctx ): array {
        return self::tpl_invite_declined( $u, $ctx );
    }

    public function __construct() {
        add_action( 'wp_ajax_wv_admin_send_notice', [ $this, 'ajax_send' ] );
        add_shortcode('ds_admin_notification_templates', [__CLASS__, 'render_all_templates_shortcode']);
    }

    /* ───────────────── AJAX ───────────────── */
    public function ajax_send(): void {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        if ( ! current_user_can( self::CAP ) && ! current_user_can( 'wv_admin' ) ) {
            wp_send_json_error( 'No cap' );
        }
        $id   = absint( $_POST['user_id'] ?? 0 );
        $tpl  = sanitize_text_field( $_POST['template'] ?? '' );
        $user = get_userdata( $id );
        if ( ! $user ) {
            wp_send_json_error( 'User not found.' );
        }
        /* ── 1)  custom HTML e-mail ────────────────────────── */
        if ( $tpl === 'custom' ) {
            // Accept both keys coming from JS
            $html     = wp_kses_post( $_POST['custom_body'] ?? $_POST['html'] ?? '' );
            if ( $html === '' ) {
                wp_send_json_error( 'Empty body.' );
            }
            $subject  = sanitize_text_field(
                $_POST['subject'] ?? get_bloginfo( 'name' ) . ' – notification'
            );
            $title    = sanitize_text_field( $_POST['title']    ?? $subject );
            $note     = wp_kses_post(      $_POST['note']      ?? '' );
            $btn_text = sanitize_text_field( $_POST['btn_text'] ?? 'Go to website' );
            $btn_link = esc_url_raw(        $_POST['btn_link'] ?? home_url('/') );
            /* ─ wrap in the Wine Vision template ─ */
            [ $subj, $body ] = \Desymphony\Helpers\DS_Utils::email_template(
                $subject,
                [
                    'title'        => sprintf( 'Dear %s %s', $user->first_name, $user->last_name ),
                    'bg'           => '#0b051c',
                    'logo_variant' => 'W',
                ],
                [
                    'title'         => $title,
                    'html'          => $html,
                    'note'          => $note,
                    'btn_text'      => $btn_text,
                    'btn_link'      => $btn_link,
                    'btn_bg'        => '#0b051c',
                    'btn_text_color'=> '#ffffff',
                ]
            );
            add_filter( 'wp_mail_content_type', fn () => 'text/html' );
            wp_mail(
                $user->user_email,
                $subj,
                $body,
                'From: Wine Vision 2025 <no-reply@winevisionfair.com>'
            );
            remove_filter( 'wp_mail_content_type', fn () => 'text/html' );
            wp_send_json_success( 'Sent.' );
        }
        /* ── 2)  predefined template ───────────────────────── */
        if ( ! isset( self::TEMPLATES[ $tpl ] ) ) {
            wp_send_json_error( 'Unknown template.' );
        }
        [ $subject, $html ] = call_user_func( self::TEMPLATES[ $tpl ], $user );
        add_filter( 'wp_mail_content_type', fn () => 'text/html' );
        wp_mail( $user->user_email, $subject, $html );
        remove_filter( 'wp_mail_content_type', fn () => 'text/html' );
        wp_send_json_success( 'Sent.' );
    }

    /* ───────────────────────── Exhibitor – account state ───────────────────────── */
    private static function tpl_exhibitor_validating( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision account status', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Exhibitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#320a64', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>is being validated', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><strong>%s</strong>',
                    esc_html__( 'Thank you for completing your registration. In accordance with the 2025 fair’s participation rules, your account is undergoing a validation process. If any additional information is needed, our support team will contact you promptly. In the meantime, we kindly ask you to review this year’s application form content which you need to complete in order to participate in the 2025 Wine Vision by Open Balkan Fair.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for your patience and understanding!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To review the contents of the 2025<br> Exhibitor Application Form,<br><strong>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#6e0fd7',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_exhibitor_approved( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision account approved', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Exhibitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#6e0fd7', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>has been approved!', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><p><strong>%s</strong></p>',
                    esc_html__( 'We are confident that your participation will be a fantastic opportunity to connect with buyers, enthusiasts, and industry leaders from around the world. Your next task is to complete your Exhibitor Application Form in order to participate in the 2025 Wine Vision by Open Balkan Fair.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for joining our community of esteemed visionaries!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To complete your 2025<br> Exhibitor Application Form,<br><strong>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#6e0fd7',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_exhibitor_rejected( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision application', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Exhibitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#85828e', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>has not been approved', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><p><strong>%s</strong></p>',
                    esc_html__( 'We appreciate your interest, but unfortunately, your account evaluation did not meet our requirements at this time. We encourage you to apply again in the future, and we look forward to the possibility of collaboration. The information you provided during registration will be removed and will not be retained or utilized in the future.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for your time and effort.', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To review our Privacy<br>and Data policy, please<br><strong>visit our official website.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => 'Go to website',
                'btn_link' => home_url( '/' ),
                'btn_bg'   => '#85828e',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_exhibitor_stands_reserved( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision stands are reserved', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Exhibitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#00c864', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your stand are reserved<br>and ready for renting!', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p><strong>%s</strong> %s</p><p>%s</p>',
                    esc_html__( 'The link below leads to Step 1 of your application form – “Stand Rental”. ', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Here, you can start your 2025 Exhibitor Application Form by renting your reserved exhibition stands. Upon completing Step 1, please proceed to Step 2 and invite all your members to register. This will allow you to assign the rented stands to both your registered members and yourself.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To complete Step 1 of your<br><strong>2025 Exhibitor Application Form,<br>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to Step 1', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-application/' ),
                'btn_bg'   => '#6e0fd7',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_exhibitor_stand_reserved( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision stand is reserved', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Exhibitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#00c864', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your stand is reserved<br>and ready for renting!', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p><strong>%s</strong> %s</p><p>%s</p>',
                    esc_html__( 'The link below leads to Step 1 of your application form – “Stand Rental”. ', DS_THEME_TEXTDOMAIN ),
                    esc_html__( "Here, you can start your 2025 Exhibitor Application Form by renting your reserved exhibition stand. Upon completing Step 1, please proceed to Step 2 and create your Exhibitor's Portfolio by adding products for the 2025 Wine Vision by Open Balkan fair.", DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To complete Step 1 of your<br><strong>2025 Exhibitor Application Form,<br>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to Step 1', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-application/' ),
                'btn_bg'   => '#6e0fd7',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    /* ───────────────────────── Invitations ───────────────────────── */
    private static function tpl_invite_member( \WP_User $u ): array {
        $company = DS_Utils::get_company_name( $u->ID );
        $userCategory = DS_Utils::get_user_category( $u->ID );
        return DS_Utils::email_template(
            sprintf( __( 'Invitation from %s', DS_THEME_TEXTDOMAIN ), $company ),
            [ 'title' => __( 'Hello!', DS_THEME_TEXTDOMAIN ), 'bg' => '#6e0fd7', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'An invitation<br>from Head Exhibitor', DS_THEME_TEXTDOMAIN ),
                'html'  => sprintf(
                    // 1st paragraph (intro)
                    '<p>%s</p>' .
                    // 2nd paragraph (company  + category)
                    '<p><strong>%s</strong><br><span style="text-transform:uppercase;">%s</span></p>' .
                    // 3rd paragraph (cta)
                    '<p>%s<br><strong>%s</strong></p>',
                    /* 1 */ esc_html__( 'You have been invited to participate in the 2025 Wine Vision by Open Balkan fair as a Member by', DS_THEME_TEXTDOMAIN ),
                    /* 2 */ esc_html( $company ),
                    /* 3 */ esc_html( $userCategory ),
                    /* 4 */ esc_html__( 'To register your account, follow the link below.', DS_THEME_TEXTDOMAIN ),
                    /* 5 */ esc_html__( 'Thank you and welcome!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To become an Exhibitor,<br>register your account on<br> <strong>our official website</strong>.', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Register account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/register/' ),
                'btn_bg'   => '#6e0fd7',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_invite_coexhibitor( \WP_User $u ): array {
        $company = DS_Utils::get_company_name( $u->ID );
        $userCategory = DS_Utils::get_user_category( $u->ID );
        return DS_Utils::email_template(
            sprintf( __( 'Invitation from %s', DS_THEME_TEXTDOMAIN ), $company ),
            [ 'title' => __( 'Hello!', DS_THEME_TEXTDOMAIN ), 'bg' => '#6e0fd7', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'An invitation<br>from Co-Exhibitor', DS_THEME_TEXTDOMAIN ),
                'html'  => sprintf(
                    // 1st paragraph (intro)
                    '<p>%s</p>' .
                    // 2nd paragraph (company  + category)
                    '<p><strong>%s</strong><br><span style="text-transform:uppercase;">%s</span></p>' .
                    // 3rd paragraph (cta)
                    '<p>%s<br><strong>%s</strong></p>',
                    /* 1 */ esc_html__( 'You have been invited to participate in the 2025 Wine Vision by Open Balkan fair as a Co-Exhibitor by', DS_THEME_TEXTDOMAIN ),
                    /* 2 */ esc_html( $company ),
                    /* 3 */ esc_html( $userCategory ),
                    /* 4 */ esc_html__( 'To register your account, follow the link below.', DS_THEME_TEXTDOMAIN ),
                    /* 5 */ esc_html__( 'Thank you and welcome!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To become an Exhibitor,<br>register your account on<br><strong>our official website.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Register account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/register/' ),
                'btn_bg'   => '#6e0fd7',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    /* ───────────────────────── Professional Buyer ───────────────────────── */
    private static function tpl_buyer_validating( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision account status', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Professional Buyer', DS_THEME_TEXTDOMAIN ), 'bg' => '#a06446', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>is being validated', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><strong>%s</strong>',
                    esc_html__( "Thank you for completing your registration. In accordance with the 2025 fair's participation rules, your account is undergoing a validation process. If any additional information is needed, our support team will contact you promptly", DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for your patience and understanding!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To find out more about 2025<br>Wine Vision by Open Balkan fair<br><strong>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#faa500',
                'btn_text_color' => '#0b051c',
            ]
        );
    }

    private static function tpl_buyer_approved( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision account approved', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Professional Buyer', DS_THEME_TEXTDOMAIN ), 'bg' => '#faa500', 'logo_variant' => 'B' ],
            [
                'title'    => __( 'Your account<br>has been approved!', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><strong>%s</strong>',
                    esc_html__( 'We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for joining our community of esteemed visionaries!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To find out more about 2025<br>Wine Vision by Open Balkan fair<br><strong>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#faa500',
                'btn_text_color' => '#0b051c',
            ]
        );
    }

    private static function tpl_buyer_approved_hosted( \WP_User $u ): array {
        // Prefer the field edited in the Verify modal; fall back to legacy key.
        $category = get_user_meta( $u->ID, 'wv_wvhb_support', true )
                 ?: get_user_meta( $u->ID, 'wv_hosted_category', true )
                 ?: 'Category IV';
        return DS_Utils::email_template(
            __( 'Your Wine Vision account approved', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Professional Buyer', DS_THEME_TEXTDOMAIN ), 'bg' => '#faa500', 'logo_variant' => 'B' ],
            [
                'title'    => __( 'Your account<br>has been approved!', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p style="padding: 8px 0; border-top: 1px solid #0b051c;border-bottom: 1px solid #0b051c;">%s <strong>%s</strong></p><p>%s</p><strong>%s</strong>',
                    esc_html__( 'Wine Vision Hosted Buyers Program granted', DS_THEME_TEXTDOMAIN ),
                    esc_html( $category ),
                    esc_html__( 'We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for joining our community of esteemed visionaries!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To review the Wine Vision<br>Hosted Buyers Program category,<br><strong>go to your account profile.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my profile', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#faa500',
                'btn_text_color' => '#0b051c',
            ]
        );
    }

    private static function tpl_buyer_rejected( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision application', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Professional Buyer', DS_THEME_TEXTDOMAIN ), 'bg' => '#85828e', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>has not been approved', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><p><strong>%s</strong></p>',
                    esc_html__( 'We appreciate your interest, but unfortunately, your account evaluation did not meet our requirements at this time. We encourage you to apply again in the future, and we look forward to the possibility of collaboration. Your registration information will be removed and will not be retained or used in the future.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for your time and effort.', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To review our Privacy<br>and Data policy, please<br><strong>visit our official website.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => 'Go to website',
                'btn_link' => home_url( '/' ),
                'btn_bg'   => '#85828e',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    /* ───────────────────────── Professional Visitor ───────────────────────── */
    private static function tpl_provisitor_validating( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision account status', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Professional Visitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#820028', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>is being validated', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><strong>%s</strong>',
                    esc_html__( 'Thank you for completing your registration. In accordance with the 2025 fair\'s participation rules, your account is undergoing a validation process. If any additional information is needed, our support team will contact you promptly.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for your patience and understanding!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To find out more about 2025 Wine Vision by Open Balkan fair, <br><strong>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#eb0037',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_provisitor_approved( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision account approved', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Professional Visitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#eb0037', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>has been approved!', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><strong>%s</strong>',
                    esc_html__( 'We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for joining our community of esteemed visionaries!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To find out more about 2025<br>Wine Vision by Open Balkan fair<br><strong>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#eb0037',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_provisitor_approved_hosted( \WP_User $u ): array {
        $category = get_user_meta( $u->ID, 'wv_hosted_category', true ) ?: 'Category IV';
        return DS_Utils::email_template(
            __( 'Your Wine Vision account approved', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Professional Visitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#eb0037', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>has been approved!', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p style="padding: 8px 0; border-top: 1px solid #0b051c;border-bottom: 1px solid #0b051c;">%s <strong>%s</strong></p><p>%s</p><strong>%s</strong>',
                    esc_html__( 'Wine Vision Hosted Buyers Program granted', DS_THEME_TEXTDOMAIN ),
                    esc_html( $category ),
                    esc_html__( 'We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for joining our community of esteemed visionaries!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To review the Wine Vision<br>Hosted Buyers Program category,<br><strong>go to your account profile.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my profile', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#eb0037',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_provisitor_rejected( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision application', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Professional Visitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#85828e', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>has not been approved', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><p><strong>%s</strong></p>',
                    esc_html__( 'We appreciate your interest, but unfortunately, your account evaluation did not meet our requirements at this time. We encourage you to apply again in the future, and we look forward to the possibility of collaboration. Your registration information will be removed and will not be retained or used in the future.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for your time and effort.', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To review our Privacy<br>and Data policy, please<br><strong>visit our official website.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => 'Go to website',
                'btn_link' => home_url( '/' ),
                'btn_bg'   => '#85828e',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    /* ───────────────────────── Visitor ───────────────────────── */
    private static function tpl_visitor_evaluating( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision account status', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Visitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#820028', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>is being evaluated', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><strong>%s</strong>',
                    esc_html__( 'Thank you for completing your registration. In accordance with the 2025 fair\'s participation rules, your account is undergoing an evaluation process. If any additional information is needed, our support team will contact you promptly.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for your patience and understanding!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To find out more about 2025<br> Wine Vision by Open Balkan fair, <br><strong>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#eb0037',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_visitor_approved( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision account approved', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Visitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#eb0037', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>has been approved!', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><strong>%s</strong>',
                    esc_html__( 'We are confident that your participation will be a fantastic opportunity to connect with exhibitors, enthusiasts, and industry leaders from around the world.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for joining our community of esteemed visionaries!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To find out more about 2025<br>Wine Vision by Open Balkan fair<br><strong>go to your personal account.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => __( 'Go to my account', DS_THEME_TEXTDOMAIN ),
                'btn_link' => home_url( '/wv-dashboard/' ),
                'btn_bg'   => '#eb0037',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    private static function tpl_visitor_rejected( \WP_User $u ): array {
        return DS_Utils::email_template(
            __( 'Your Wine Vision application', DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Visitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#85828e', 'logo_variant' => 'W' ],
            [
                'title'    => __( 'Your account<br>has not been approved', DS_THEME_TEXTDOMAIN ),
                'html'     => sprintf(
                    '<p>%s</p><p><strong>%s</strong></p>',
                    esc_html__( 'We appreciate your interest, but unfortunately, your account evaluation did not meet our requirements at this time. We encourage you to apply again in the future, and we look forward to the possibility of collaboration. Your registration information will be removed and will not be retained or used in the future.', DS_THEME_TEXTDOMAIN ),
                    esc_html__( 'Thank you for your time and effort.', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => __( 'To review our Privacy<br>and Data policy, please<br><strong>visit our official website.</strong>', DS_THEME_TEXTDOMAIN ),
                'btn_text' => 'Go to website',
                'btn_link' => home_url( '/' ),
                'btn_bg'   => '#85828e',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    /* ───────────────────────── Invitations – Accepted ───────────────────────── */
    private static function tpl_invite_accepted( \WP_User $exhibitor, array $ctx ): array {
        /*
         * $ctx is injected by DS_CoEx_Manager (see §2) and always contains:
         *   company  → invitee company name (string)
         *   category → invitee user-category (string, already uppercase)
         *   email    → invitee e-mail (string, only present on declined)
         *   model    → exhibitor participation model  (Solo | Head)
         */
        $isSolo   = stripos( $ctx['model'], 'solo' ) === 0;
        $labelInv = $isSolo ? __( 'Co-Exhibitor', DS_THEME_TEXTDOMAIN )
                            : __( 'Member',        DS_THEME_TEXTDOMAIN );
        $cta      = $isSolo ? __( 'Co-Exhibitors', DS_THEME_TEXTDOMAIN )
                            : __( 'Members',       DS_THEME_TEXTDOMAIN );
        $bodyCopy = $isSolo
            ? __( 'Make sure you pay his compulsory 2025 participation fee.<br>Thank you!', DS_THEME_TEXTDOMAIN )
            : __( 'Make sure you assign stand to your Member.<br>Thank you!', DS_THEME_TEXTDOMAIN );
        return DS_Utils::email_template(
            __( "{$labelInv} invitation accepted", DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Exhibitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#6e0fd7', 'logo_variant' => 'W' ],
            [
                'title'    => sprintf(
                    /* translators: %s = Member | Co-Exhibitor */
                    __( 'Your %s has<br> successfully registered!', DS_THEME_TEXTDOMAIN ),
                    $labelInv
                ),
                'html'     => sprintf(
                    '<p>%s</p><p><strong>%s</strong><br>%s</p><p>%s</p>',
                    sprintf(
                        /* translators: 1 = Member | Co-Exhibitor */
                        esc_html__( 'The %s you invited has completed his account registration and joined.', DS_THEME_TEXTDOMAIN ),
                        strtolower( $labelInv )
                    ),
                    esc_html( $ctx['company'] ),
                    esc_html( $ctx['category'] ),
                    $bodyCopy
                ),
                'note'     => sprintf(
                    /* translators: %s = Members | Co-Exhibitors */
                    __( 'To see your registered<br> %s accounts<br> go to %s page.', DS_THEME_TEXTDOMAIN ),
                    strtolower( $cta ),
                    $cta
                ),
                'btn_text' => $cta,
                'btn_link' => home_url( '/wv-co-ex/' ),
                'btn_bg'   => '#6e0fd7',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    /* ───────────────────────── Invitations – Declined ───────────────────────── */
    private static function tpl_invite_declined( \WP_User $exhibitor, array $ctx ): array {
        $isSolo   = stripos( $ctx['model'], 'solo' ) === 0;
        $labelInv = $isSolo ? __( 'Co-Exhibitor', DS_THEME_TEXTDOMAIN )
                            : __( 'Member',        DS_THEME_TEXTDOMAIN );
        $cta      = $isSolo ? __( 'Co-Exhibitors', DS_THEME_TEXTDOMAIN )
                            : __( 'Members',       DS_THEME_TEXTDOMAIN );
        return DS_Utils::email_template(
            __( "{$labelInv} invitation declined", DS_THEME_TEXTDOMAIN ),
            [ 'title' => __( 'Dear Exhibitor', DS_THEME_TEXTDOMAIN ), 'bg' => '#85828e', 'logo_variant' => 'W' ],
            [
                'title'    => sprintf(
                    /* translators: %s = Member | Co-Exhibitor */
                    __( 'Your %s has<br> declined your invitation', DS_THEME_TEXTDOMAIN ),
                    $labelInv
                ),
                'html'     => sprintf(
                    '<p>%s</p><p><strong>%s</strong></p><p>%s</p>',
                    sprintf(
                        /* translators: 1 = Member | Co-Exhibitor */
                        esc_html__( 'The %s you invited has declined your invitation to register an account.', DS_THEME_TEXTDOMAIN ),
                        strtolower( $labelInv )
                    ),
                    esc_html( $ctx['email'] ),
                    esc_html__( 'You can contact him personally. Thank you!', DS_THEME_TEXTDOMAIN )
                ),
                'note'     => sprintf(
                    /* translators: %s = Members | Co-Exhibitors */
                    __( 'To see your registered<br> %s accounts<br> go to %s page.', DS_THEME_TEXTDOMAIN ),
                    strtolower( $cta ),
                    $cta
                ),
                'btn_text' => $cta,
                'btn_link' => home_url( '/wv-co-ex/' ),
                'btn_bg'   => '#85828e',
                'btn_text_color' => '#ffffff',
            ]
        );
    }

    /* -------------------------------------------------------------
     * Central sender – called by DS_Auth_Registration, DS_CoEx_Manager, etc.
     * ------------------------------------------------------------ */
    public static function send( int $user_id, string $slug, array $ctx = [] ): void {
        // unknown template → bail
        if ( ! isset( self::TEMPLATES[ $slug ] ) ) {
            return;
        }
        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return;
        }
        /* templates that need the extra context simply declare
         * a second parameter; we detect that on the fly            */
        $cb         = self::TEMPLATES[ $slug ];
        $need_ctx   = ( new \ReflectionMethod( $cb[0], $cb[1] ) )->getNumberOfParameters() > 1;
        [ $subject, $html ] = $need_ctx
            ? call_user_func( $cb, $user, $ctx )
            : call_user_func( $cb, $user );
        add_filter( 'wp_mail_content_type', fn () => 'text/html' );
        wp_mail( $user->user_email, $subject, $html );
        remove_filter( 'wp_mail_content_type', fn () => 'text/html' );
    }

    public static function render_all_templates_shortcode(): string {
        ob_start();
        echo '<div class="ds-admin-notification-templates">';
        $preview_user = wp_get_current_user()->ID ? wp_get_current_user()
            : (object) [
                'ID'           => 1,
                'user_email'   => 'test@example.com',
                'display_name' => 'Test User'
            ];
        foreach ( self::TEMPLATES as $slug => $callback ) {
            $ref       = new \ReflectionMethod( $callback[0], $callback[1] );
            $need_ctx  = $ref->getNumberOfParameters() > 1;
            /* generic dummy context for previewing */
            $ctx = [
                'company'  => 'Sample Company d.o.o.',
                'category' => 'WINEMAKER',
                'email'    => 'invitee@example.com',
                'model'    => 'Solo Exhibitor',
            ];
            [$subject, $html] = $need_ctx
                ? call_user_func( $callback, $preview_user, $ctx )
                : call_user_func( $callback, $preview_user );
            printf(
                '<div style="margin-bottom:40px;border:1px solid #ccc;padding:16px;">
                    <h3 style="margin-top:0;">%1$s <small style="font-weight:400;">– %2$s</small></h3>%3$s
                </div>',
                esc_html( $slug ),
                esc_html( $subject ),
                $html
            );
        }
        echo '</div>';
        return ob_get_clean();
    }

    /** role → array of template slugs allowed */
    private const ALLOWED_BY_ROLE = [
        'exhibitor' => [ 'exhibitor_validating','exhibitor_approved','exhibitor_rejected',
                        'exhibitor_stands_reserved','exhibitor_stand_reserved',
                        'invite_member','invite_coexhibitor' ],
        'buyer'     => [ 'buyer_validating','buyer_approved','buyer_approved_hosted','buyer_rejected' ],
        'visitor'   => [ 'visitor_evaluating','visitor_approved','visitor_rejected' ],
        'provisitor'=> [ 'provisitor_validating','provisitor_approved','provisitor_approved_hosted','provisitor_rejected' ],
    ];

}