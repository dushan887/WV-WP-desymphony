<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php esc_html_e( "You've been invited as Co‑Exhibitor", DS_THEME_TEXTDOMAIN ); ?></title>
  <style>
    body { font-family: Arial, sans-serif; color: #333; }
    .container { width: 100%; max-width: 600px; margin: auto; padding: 20px; }
    .btn { display: inline-block; padding: 10px 20px; margin: 10px 5px; text-decoration: none; border-radius: 4px; }
    .btn-accept { background-color: #28a745; color: #fff; }
    .btn-decline { background-color: #dc3545; color: #fff; }
    .footer { font-size: 12px; color: #888; margin-top: 20px; }
  </style>
<body>
  <div class="container">
    <h1><?php esc_html_e( 'You’ve been invited to co‑exhibit!', DS_THEME_TEXTDOMAIN ); ?></h1>
    <p><?php esc_html_e( 'Hello,', DS_THEME_TEXTDOMAIN ); ?></p>
    <p><?php esc_html_e( 'An exhibitor has invited you to join their booth as a Co‑Exhibitor. Please choose to accept or decline below:', DS_THEME_TEXTDOMAIN ); ?></p>
    <?php if ( isset( $accept_url ) && isset( $decline_url ) ) : ?>
      <p>
        <a href="<?php echo esc_url( $accept_url ); ?>" class="btn btn-accept"><?php esc_html_e( 'Accept Invitation', DS_THEME_TEXTDOMAIN ); ?></a>
        <a href="<?php echo esc_url( $decline_url ); ?>" class="btn btn-decline"><?php esc_html_e( 'Decline Invitation', DS_THEME_TEXTDOMAIN ); ?></a>
      </p>
    <?php endif; ?>
    <p class="footer"><?php esc_html_e( 'If these buttons do not work, copy and paste the URL into your browser.', DS_THEME_TEXTDOMAIN ); ?></p>
  </div>
</body>
</html>