<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php esc_html_e( 'Register to accept your Co‑Exhibitor invite', DS_THEME_TEXTDOMAIN ); ?></title>
  <style>
    body { font-family: Arial, sans-serif; color: #333; }
    .container { width: 100%; max-width: 600px; margin: auto; padding: 20px; }
    .btn-register { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 4px; }
    .footer { font-size: 12px; color: #888; margin-top: 20px; }
  </style>
<body>
  <div class="container">
    <h1><?php esc_html_e( 'Complete your registration', DS_THEME_TEXTDOMAIN ); ?></h1>
    <p><?php esc_html_e( 'Hello,', DS_THEME_TEXTDOMAIN ); ?></p>
    <p><?php esc_html_e( 'You have been invited to co‑exhibit but do not yet have an account. Click below to register and automatically accept your invitation:', DS_THEME_TEXTDOMAIN ); ?></p>
    <?php if ( isset( $register_url ) ) : ?>
      <p>
        <a href="<?php echo esc_url( $register_url ); ?>" class="btn-register"><?php esc_html_e( 'Register Now', DS_THEME_TEXTDOMAIN ); ?></a>
      </p>
    <?php endif; ?>
    <p class="footer"><?php esc_html_e( 'After registration, you will be linked automatically.', DS_THEME_TEXTDOMAIN ); ?></p>
  </div>
</body>
</html>
