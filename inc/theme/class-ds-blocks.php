<?php
namespace Desymphony\Theme;

defined('ABSPATH') || exit;

/**
 * Registers custom Gutenberg blocks or block categories.
 */
class DS_Blocks {

    public static function init() {
        add_action('init', [__CLASS__, 'register_custom_blocks']);
    }

    /**
     * Register custom blocks, block patterns, or categories here.
     */
    public static function register_custom_blocks() {
        // Example: register a block pattern
        
        acf_register_block_type (
            array (
            'name'              => 'ds-home-hero',
            'title'             => __('DS: Hero Section', 'desymphony'),
            'description'       => __('A custom hero section block.', 'desymphony'),
            'render_template'   => 'templates/blocks/ds-home-hero.php',
            'category'          => 'common',
            'icon'              => 'star-filled',
            'keywords'          => array( 'wv', 'ds', 'hero', 'section' ),
            'enqueue_assets'    => function() {
                // wp_enqueue_style( 'ds-home-hero-style', get_template_directory_uri() . '/blocks/ds-home-hero.css', array(), null );
            },
            )
        );

        acf_register_block_type (
            array (
            'name'              => 'ds-8-zones-wine-experience',
            'title'             => __('DS: 8 Zones of Wine Experience', 'desymphony'),
            'description'       => __('A block showcasing the 8 Zones of Wine Experience.', 'desymphony'),
            'render_template'   => 'templates/blocks/ds-home-8-zones-wine-experience.php',
            'category'          => 'common',
            'icon'              => 'wine',
            'keywords'          => array( 'wv', 'ds', 'wine', 'zones', 'experience' ),
            'enqueue_assets'    => function() {},
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-latest-news-style-1',
                'title'             => __('DS: Latest News Style 1', 'desymphony'),
                'description'       => __('A block for displaying the latest news in style 1.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-latest-news-style-1.php',
                'category'          => 'common',
                'icon'              => 'megaphone',
                'keywords'          => array('wv', 'ds', 'latest', 'news', 'style 1'),
                'enqueue_assets'    => function() {
                    // wp_enqueue_style('ds-latest-news-style-1-style', get_template_directory_uri() . '/blocks/ds-latest-news-style-1.css', array(), null);
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-card-carousel',
                'title'             => __('DS: WV Card Carousel', 'desymphony'),
                'description'       => __('A block for displaying a card carousel.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-card-carousel.php',
                'category'          => 'common',
                'icon'              => 'images-alt2',
                'keywords'          => array('wv', 'card', 'carousel'),
                'enqueue_assets'    => function() {
                    // wp_enqueue_style('wv-card-carousel-style', get_template_directory_uri() . '/blocks/wv-card-carousel.css', array(), null);
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-open-podcast-carousel',
                'title'             => __('DS: Open Podcast Carousel', 'desymphony'),
                'description'       => __('A block for displaying an Open Podcast carousel.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-open-podcast-carousel.php',
                'category'          => 'common',
                'icon'              => 'format-audio',
                'keywords'          => array('wv', 'ds', 'open', 'podcast', 'carousel'),
                'enqueue_assets'    => function() {
                    // wp_enqueue_style('open-podcast-carousel-style', get_template_directory_uri() . '/blocks/open-podcast-carousel.css', array(), null);
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-ig-feed',
                'title'             => __('DS: Instagram Feed', 'desymphony'),
                'description'       => __('A block for displaying an Instagram feed.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-ig-feed.php',
                'category'          => 'common',
                'icon'              => 'instagram',
                'keywords'          => array('wv', 'ds', 'instagram', 'feed'),
                'enqueue_assets'    => function() {
                    // wp_enqueue_style('instagram-feed-style', get_template_directory_uri() . '/blocks/instagram-feed.css', array(), null);
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-partners-grid',
                'title'             => __('DS: Partners Grid', 'desymphony'),
                'description'       => __('A block for displaying partners in a grid layout.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-partners-grid.php',
                'category'          => 'common',
                'icon'              => 'grid-view',
                'keywords'          => array('wv', 'ds', 'partners', 'grid', 'logos'),
                'enqueue_assets'    => function() {
                    // wp_enqueue_style('partners-grid-style', get_template_directory_uri() . '/blocks/partners-grid.css', array(), null);
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-temp1',
                'title'             => __('DS: Temp1', 'desymphony'),
                'description'       => __('A custom block for displaying the DS Temp1 template.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-temp1.php',
                'category'          => 'common',
                'icon'              => 'admin-customizer',
                'keywords'          => array('temp', 'ds', 'template'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-temp1.
                },
            )
        );

        acf_register_block_type(
            array(
            'name'              => 'ds-explore-2025',
            'title'             => __('DS: Explore 2025', 'desymphony'),
            'description'       => __('A custom block for displaying the Explore 2025 section.', 'desymphony'),
            'render_template'   => 'templates/blocks/ds-explore-2025.php',
            'category'          => 'common',
            'icon'              => 'calendar-alt',
            'keywords'          => array('wv', 'ds', 'explore', '2025'),
            'enqueue_assets'    => function() {
                // Optionally enqueue scripts or styles for ds-explore-2025.
            },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-halls',
                'title'             => __('DS: Halls', 'desymphony'),
                'description'       => __('A block for displaying the Halls section.', 'desymphony'),
                'render_template'   => 'templates/fair-map/ds-halls.php',
                'category'          => 'common',
                'icon'              => 'location-alt',
                'keywords'          => array('wv', 'ds', 'halls', 'fair map'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-halls.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-coming-soon',
                'title'             => __('DS: Coming Soon', 'desymphony'),
                'description'       => __('A block for displaying a Coming Soon section.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-coming-soon.php',
                'category'          => 'common',
                'icon'              => 'clock',
                'keywords'          => array('wv', 'ds', 'coming soon', 'soon'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-coming-soon.
                },
            )
        );

         acf_register_block_type(
            array(
                'name'              => 'ds-coming-fall',
                'title'             => __('DS: Coming Fall', 'desymphony'),
                'description'       => __('A block for displaying a Coming Fall section.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-coming-soon.php',
                'category'          => 'common',
                'icon'              => 'clock',
                'keywords'          => array('wv', 'ds', 'coming fall', 'fall'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-coming-soon.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-contact',
                'title'             => __('DS: Contact', 'desymphony'),
                'description'       => __('A block for displaying a contact section.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-contact.php',
                'category'          => 'common',
                'icon'              => 'email',
                'keywords'          => array('wv', 'ds', 'contact', 'form', 'email'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-contact.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-support',
                'title'             => __('DS: Support', 'desymphony'),
                'description'       => __('A block for displaying a support section.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-support.php',
                'category'          => 'common',
                'icon'              => 'admin-users',
                'keywords'          => array('wv', 'ds', 'support', 'help', 'contact'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-support.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-8-zones',
                'title'             => __('DS: 8 Zones', 'desymphony'),
                'description'       => __('A block for displaying the 8 Zones section.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-8-zones.php',
                'category'          => 'common',
                'icon'              => 'location',
                'keywords'          => array('wv', 'ds', '8 zones', 'zones'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-8-zones.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-about',
                'title'             => __('DS: About', 'desymphony'),
                'description'       => __('A block for displaying the About section.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-about.php',
                'category'          => 'common',
                'icon'              => 'info',
                'keywords'          => array('wv', 'ds', 'about', 'info', 'section'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-about.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-b2b-sessions',
                'title'             => __('DS: B2B Sessions', 'desymphony'),
                'description'       => __('A block for displaying B2B Sessions.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-b2b-sessions.php',
                'category'          => 'common',
                'icon'              => 'businessman',
                'keywords'          => array('wv', 'ds', 'b2b', 'sessions'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-b2b-sessions.
                },
            )
        );
        
        acf_register_block_type(
            array(
            'name'              => 'ds-competitions',
            'title'             => __('DS: Competitions', 'desymphony'),
            'description'       => __('A block for displaying competitions.', 'desymphony'),
            'render_template'   => 'templates/blocks/ds-competitions.php',
            'category'          => 'common',
            'icon'              => 'awards',
            'keywords'          => array('wv', 'ds', 'competitions', 'awards'),
            'enqueue_assets'    => function() {
                // Optionally enqueue scripts or styles for ds-competitions.
            },
            )
        );

        acf_register_block_type(
            array(
            'name'              => 'ds-competitions-food',
            'title'             => __('DS: Competitions Food', 'desymphony'),
            'description'       => __('A block for displaying food competitions.', 'desymphony'),
            'render_template'   => 'templates/blocks/ds-competitions-food.php',
            'category'          => 'common',
            'icon'              => 'carrot',
            'keywords'          => array('wv', 'ds', 'competitions', 'food'),
            'enqueue_assets'    => function() {
                // Optionally enqueue scripts or styles for ds-competitions-food.
            },
            )
        );

        acf_register_block_type(
            array(
            'name'              => 'ds-competitions-wine',
            'title'             => __('DS: Competitions Wine', 'desymphony'),
            'description'       => __('A block for displaying wine competitions.', 'desymphony'),
            'render_template'   => 'templates/blocks/ds-competitions-wine.php',
            'category'          => 'common',
            'icon'              => 'wine',
            'keywords'          => array('wv', 'ds', 'competitions', 'wine'),
            'enqueue_assets'    => function() {
                // Optionally enqueue scripts or styles for ds-competitions-wine.
            },
            )
        );

        acf_register_block_type(
            array(
            'name'              => 'ds-competitions-rakija',
            'title'             => __('DS: Competitions Rakija', 'desymphony'),
            'description'       => __('A block for displaying rakija competitions.', 'desymphony'),
            'render_template'   => 'templates/blocks/ds-competitions-rakija.php',
            'category'          => 'common',
            'icon'              => 'glass',
            'keywords'          => array('wv', 'ds', 'competitions', 'rakija'),
            'enqueue_assets'    => function() {
                // Optionally enqueue scripts or styles for ds-competitions-rakija.
            },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-masterclasses',
                'title'             => __('DS: Masterclasses', 'desymphony'),
                'description'       => __('A block for displaying masterclasses.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-masterclasses.php',
                'category'          => 'common',
                'icon'              => 'welcome-learn-more',
                'keywords'          => array('wv', 'ds', 'masterclasses', 'classes', 'education'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-masterclasses.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-awards',
                'title'             => __('DS: Awards', 'desymphony'),
                'description'       => __('A block for displaying awards.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-awards.php',
                'category'          => 'common',
                'icon'              => 'awards',
                'keywords'          => array('wv', 'ds', 'awards', 'prizes', 'honors'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-awards.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-award-cards',
                'title'             => __('DS: Award Cards', 'desymphony'),
                'description'       => __('A block for displaying award cards.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-award-cards.php',
                'category'          => 'common',
                'icon'              => 'awards',
                'keywords'          => array('wv', 'ds', 'award', 'cards', 'prizes'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-award-cards.
                },
            )
        );

        acf_register_block_type(
            array(
                'name'              => 'ds-gallery',
                'title'             => __('DS: Gallery', 'desymphony'),
                'description'       => __('A block for displaying a gallery.', 'desymphony'),
                'render_template'   => 'templates/blocks/ds-gallery.php',
                'category'          => 'common',
                'icon'              => 'format-gallery',
                'keywords'          => array('wv', 'ds', 'gallery', 'images', 'photos'),
                'enqueue_assets'    => function() {
                    // Optionally enqueue scripts or styles for ds-gallery.
                },
            )
        );

        // If building custom blocks, use register_block_type() or
        // a block.json file in /blocks, etc.
    }
}

