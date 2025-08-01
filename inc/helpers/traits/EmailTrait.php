<?php

namespace Desymphony\Helpers;

trait EmailTrait {

    /**
     * Build a Wine Vision transactional e-mail (2025 design system).
     *
     * $header = [
     *   'title'       => 'Dear Exhibitor',
     *   'bg'          => '#6e0fd7',
     *   'logo_variant'=> 'B',                      // 'W' (default) or 'B'
     *   // optionally override:
     *   // 'logo'   => 'https://…/custom.png',
     * ];
     *
     * $main   = [ … ]  // unchanged (see previous message)
     */
    public static function email_template(
        string $subject,
        array  $header,
        array  $main,
        string $footer = ''
    ): array {
        /* ── logo (auto) ─────────────────────────────────────────────── */
        $logo = $header['logo'] ?? null;
        if ( ! $logo ) {
            $variant = strtoupper( $header['logo_variant'] ?? 'W' );   // W or B
            $logo    = $variant === 'B'
                ? 'https://winevisionfair.com/wp-content/uploads/2025/06/WV25_E-mail_H_logo_B.png'
                : 'https://winevisionfair.com/wp-content/uploads/2025/06/WV25_E-mail_H_logo_W.png';
                // Set titleColor based on logo variant
                $titleColor = '#fff'; // default white
                if ($variant === 'B') {
                    $titleColor = '#000';
                }
                $header['titleColor'] = $titleColor;
        }
        /* ── minimal CSS (unchanged) ─────────────────────────────────── */
        $css = '
        body{margin:0;padding:0;background:#fff;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
        a{color:inherit;text-decoration:none}
        h1{margin:0;font-size:28px;line-height:34px;font-weight:700}
        @media(prefers-color-scheme:dark){body{background:#000;color:#ddd}}';
        /* ── build HTML ──────────────────────────────────────────────── */
        $html = '
    <!doctype html><html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>'.esc_html($subject).'</title>
    <style>'.preg_replace('/\s+/',' ',$css).'</style>
    </head><body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
    <tr><td align="center" style="padding:24px 12px;">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:100%;border-radius:16px;overflow:hidden;">
        <!-- header bar -->
        <tr>
            <td bgcolor="'.esc_attr($header['bg']).'" style="padding:20px 28px;color:'.esc_attr($header['titleColor']).';font-size:22px;font-weight:500;">'
                .wp_kses_post($header['title']).'
            </td>
            <td bgcolor="'.esc_attr($header['bg']).'" align="right" style="padding:20px 28px;">
            <img src="'.esc_url($logo).'" alt="" width="30" style="display:block;border:0;outline:none;">
            </td>
        </tr>
        <!-- main content -->
        <tr><td colspan="2" bgcolor="#e7e6e8" style="padding:32px;color:#0b051c;">
            <h1 style="margin-bottom: 24px">'.wp_kses_post($main['title']).'</h1>'
            .wp_kses_post($main['html']).'
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:40px 0 0;">
            <tr>
                <td width="50%" style="font-size:12px;line-height:18px; padding-right: 8px;">'.wp_kses_post($main['note']).'</td>
                <td align="right" width="50%" style="padding-left:8px;">
                <a href="'.esc_url($main['btn_link']).'"
                    style="display:inline-block;padding:12px 24px;font-size:14px;line-height:20px;font-weight:600;
                            border-radius:28px;background:'.esc_attr($main['btn_bg']).';
                            color:'.esc_attr($main['btn_text_color']).';">'
                    .esc_html($main['btn_text']).'
                </a>
                </td>
            </tr>
            </table>'
            .($footer ? '<div style="margin-top:32px;">'.wp_kses_post($footer).'</div>' : '').'
        </td></tr>
        <!-- static footer -->
        <tr><td colspan="2" bgcolor="#0b051c" style="padding:24px 32px;color:#ffffff;font-size:12px;line-height:18px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%">
                <img src="https://winevisionfair.com/wp-content/uploads/2025/06/WV25_E-mail_F_logo_W.png" alt="WINE VISION" width="150" style="display:block;border:0;outline:none;">
                </td>
                <td><strong>November 22-25, 2025</strong><br><span style="color:#e7e6e8;">Belgrade Fair, Serbia</span></td>
                <td align="right"><a href="https://winevisionfair.com" style="color:#ffffff;font-weight:600;">winevisionfair.com</a><br><span style="color:#e7e6e8;">Exhibit · Trade · Visit</span></td>
            </tr>
            </table>
        </td></tr>
        </table>
    </td></tr>
    </table>
    </body></html>';
        return [$subject, $html];
    }

}