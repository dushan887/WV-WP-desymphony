<?php
/**
 * Template for the DS Coming Fall block.
 *
 * @package Desymphony
 */

use Desymphony\Woo\DS_Woo_Stand_Map;

if (! defined('ABSPATH')) exit;

$class = 'ds-coming-fall';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>

<?php include DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-coming-fall.php'; ?>
