<?php
// templates/sidebar.php
?>
<aside class="border p-3 mb-4">
  <?php if ( is_active_sidebar('sidebar-1') ) : ?>
    <?php dynamic_sidebar('sidebar-1'); ?>
  <?php else: ?>
    <p><?php esc_html_e('Add widgets to Sidebar in Appearance → Widgets.', 'desymphony'); ?></p>
  <?php endif; ?>
</aside>