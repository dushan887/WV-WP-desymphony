<?php
/**
 * Block: ds-8-zones (main + nested carousels)
 *
 * @package Desymphony
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$class = 'ds-8-zones';
if ( ! empty( $block['className'] ) ) { $class .= ' ' . esc_attr( $block['className'] ); }
if ( ! empty( $block['align']    ) ) { $class .= ' align' . esc_attr( $block['align'] ); }

/**
 * One line per hall: slug → [ring, head, gallery[]]
 * Replace gallery[] with real images later.
 */
$zones = [

  /* ── Hall 3 ─────────────────────────────────────────── */
  '3'  => [
    '8_Zones_RING_Hall_3.png',
    '8_Zones_HEAD_Hall_3.svg',
    [ '8_Zones_HEAD_Hall_3.svg', '8_Zones_HALL_3.png' ],
    [
      'At the central square—The Spot of Connections—new friendships are forged, knowledge is exchanged, and new business stories emerge',
      'Inspired by the elements of historical architecture that bring the region’s rich cultural heritage to life, this space blends the past with contemporary experiences, creating an atmosphere where tradition meets innovation.',
    ],
  ],

  /* ── Hall 3A ────────────────────────────────────────── */
  '3A' => [
    '8_Zones_RING_Hall_3A.png',
    '8_Zones_HEAD_Hall_3A.svg',
    [ '8_Zones_HEAD_Hall_3A.svg', '8_Zones_HALL_3A.png' ],
    [
      'Discover The Oasis of Inspiration in Hall 3A, with its perfect setting for relaxation and enjoyment in every sip of wine',
      'Resembling nature and a park, this is an ideal serenity zone, offering an atmosphere that perfectly blends wine with natural harmony.',
    ],
  ],

  /* ── Hall 2B ────────────────────────────────────────── */
  '2B' => [
    '8_Zones_RING_Hall_2B.png',
    '8_Zones_HEAD_Hall_2B.svg',
    [ '8_Zones_HEAD_Hall_2B.svg', '8_Zones_HALL_2B.png' ],
    [
      'A museum-like zone offers a deep understanding of the history of wine, highlighting the richness of tradition, culture, and innovation',
      'This carefully designed exhibition takes you through key moments in the history of wine and reveals its role in shaping the world as we know it today.',
    ],
  ],

  /* ── Halls 2A & 2C (shared slide) ───────────────────── */
  '2A' => [
    '8_Zones_RING_Hall_2A_2C.png',
    '8_Zones_HEAD_Hall_2A_2C.svg',
    [ '8_Zones_HEAD_Hall_2A_2C.svg', '8_Zones_HALLS_2A_2C.png' ],
    [
      'Openness to new knowledge and ideas awaits you in Halls 2A and 2C, where you can discover diverse winemaking techniques and skills',
      'Enjoy Masterclass workshops designed to transfer knowledge and experience from the world’s leading experts in an inspiring way. The appealing exhibition setup, resembling a library of knowledge, allows you to enrich your understanding of the art of winemaking through various interactive features.',
    ],
  ],

  /* ── Hall 1A ────────────────────────────────────────── */
  '1A' => [
    '8_Zones_RING_Hall_1A.png',
    '8_Zones_HEAD_Hall_1A.svg',
    [ '8_Zones_HEAD_Hall_1A.svg', '8_Zones_HALL_1A.png' ],
    [
      'Indulge in the captivating notes of wine and spirits in Hall 1A, a space for relaxation and the creation of unforgettable and exciting moments',
      'This setting is perfect for enjoying an attractive wine bar setup, featuring a large community table that stretches throughout the entire zone. Additionally, this area serves as the Path to the Epicenter.',
    ],
  ],

  /* ── Hall 1 (covers 1 & 1G) ─────────────────────────── */
  '1'  => [
    '8_Zones_RING_Hall_1.png',
    '8_Zones_HEAD_Hall_1.svg',
    [ '8_Zones_HEAD_Hall_1.svg', '8_Zones_HALL_1.png' ],
    [
      'Hall 1 is the fair’s Epicenter and energy hub, a place where visitors are encouraged to exchange experiences and ideas',
      'It serves as the central venue, with a stage hosting the grand opening of the fair and the prestigious competitions awards ceremony. The hall also features a remarkable amphitheater, community zones, and interactive activations, all designed to enhance the overall experience.',
    ],
  ],

  /* ── Hall 4A ────────────────────────────────────────── */
  '4A' => [
    '8_Zones_RING_Hall_4A.png',
    '8_Zones_HEAD_Hall_4A.svg',
    [ '8_Zones_HEAD_Hall_4A.svg', '8_Zones_HALL_4A.png' ],
    [
      'Discover the world of wine through a unique interactive installation that unveils the secrets of wine colors and textures',
      'In this innovative and educational setup, you can learn about different wine varieties, their colors and characteristics, as well as how each texture influences the taste and perception of wine, creating a distinct sensory experience.',
    ],
  ],

  /* ── Hall 4B ────────────────────────────────────────── */
  '4B' => [
    '8_Zones_RING_Hall_4B.png',
    '8_Zones_HEAD_Hall_4B.svg',
    [ '8_Zones_HEAD_Hall_4B.svg', '8_Zones_HALL_4B.png' ],
    [
      'The Food Vision by Open Balkan segment presents a distinct set of standards for exhibition space organization',
      'Food Vision by Open Balkan 2025 is scheduled to take place at the Belgrade Fair, specifically in Hall 4B. Comprehensive details regarding stand types, their accompanying equipment, and pricing will be subsequently provided to all potential exhibitors.',
    ],
  ],

];



// Base dir for ring/head artwork (unchanged from your other block)
$img_base = get_theme_file_uri( 'src/images/blocks/' );
$img_base2 = get_theme_file_uri( 'src/images/zones/' );
?>
<style>
  #wv-wrap::before{background-image:url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_8_Zones_PAGE_Bck.jpg)!important;}
  .ds-w-100-50 {
    width: calc(100% + 50%);
    margin-left: -25%;
  }
  @media(max-width:768px){
    .ds-w-100-50 {
        width: calc(100% + 30%);
        margin-left: -15%;
    }
    #wv-wrap::before{background-image:url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_8_Zones_PAGE_Bck.jpg)!important;}
  }
</style>

<div id="wv-wrap" class="py-0 <?php echo esc_attr( $class ); ?>">
  <section class="position-relative wv-app-form">
    <div class="hall hall_nav hall_nav_zones d-flex zones-pagination-wrapper justify-content-center align-items-center py-24 px-48 position-relative container container-768">
        <div class="ds-arrow ds-prev swiper-button-prev"><span class="wv wv_point-m35-f wv-color-w"></span></div>
        <?php echo file_get_contents( get_theme_file_path( 'inc/public/views/halls/hall-nav-zones.svg' ) ); ?>         
        <div class="ds-arrow ds-next swiper-button-next"><span class="wv wv_point-m35-f wv-color-w"></span></div>
    </div>     

    <div class="d-flex justify-content-center align-items-center flex-column">
        <h4 class="my-0 fs-20 ls-4 lh-1 wv-color-w wv-color-ww">
            HALL <span id="wv-selected-hall-zone">3</span>
        </h4>
    </div>
    <!-- OUTER / MAIN -->
     <div class="container-fluid px-0 overflow-hidden py-32">
        <div class="d-block ds-w-100-50">
            <div id="zonesSwiper" class="swiper main-zone-swiper h-auto">
            <div class="swiper-wrapper">

                <?php foreach ( $zones as $slug => [ $ring, $head, $gallery ] ) : ?>
                <div class="swiper-slide"
                     data-hall="<?php echo esc_attr( $slug ); ?>"
                     aria-label="Hall <?php echo esc_attr( $slug ); ?>">
                    <div class="position-relative text-center"
                        style="background:url('<?php echo esc_url( $img_base . $ring ); ?>') center/cover no-repeat">
                        <img class="inner-img opacity-0" src="<?php echo esc_url( $img_base . $head ); ?>" alt="" loading="lazy">
                        <!-- NESTED / MINI -->
                        <div class="swiper zone-gallery">
                            <div class="swiper-wrapper">
                                <?php
                                /* unpack the current zone entry */
                                [$ring, $head, $imgs, [$title, $text]] = $zones[$slug];

                                /* image slides */
                                foreach ($imgs as $img) : ?>
                                    <div class="swiper-slide">
                                    <img src="<?php echo esc_url($img_base2 . $img); ?>"
                                        class="img-fluid" alt="">
                                    </div>
                                <?php endforeach; ?>

                                <!-- text slide -->
                                <div class="swiper-slide d-flex align-items-center justify-content-center">
                                <div class="zone-text text-center w-100 d-flex align-items-center justify-content-center flex-column py-24 overflow-hidden">
                                    <h5 class="fw-600 mb-24 wv-color-w fs-24"><?php echo esc_html($title); ?></h5>
                                    <p class="mb-0 wv-color-w"><?php echo esc_html($text); ?></p>
                                </div>
                                </div>
                            </div>

                            <div class="swiper-pagination zone-pagination"></div>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>

            </div>

            </div>
        </div>
    </div>
    
  </section>
</div>

