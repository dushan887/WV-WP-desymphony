<?php
namespace Desymphony\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Holds definitions for each “image profile” used by the plugin.
 *
 * Example structure:
 *   $profiles = [
 *     'profile' => [
 *       'subdir'     => '/wv/avatars/',
 *       'aspect'     => 1.0,
 *       'outputs'    => [
 *         [
 *           'filename' => 'profile-image-200.jpg',
 *           'width'    => 200,
 *           'height'   => 200,
 *           'quality'  => 85
 *         ],
 *         [
 *           'filename' => 'profile-image-400.jpg',
 *           'width'    => 400,
 *           'height'   => 400,
 *           'quality'  => 85
 *         ]
 *       ],
 *       'overwrite' => true, // or false
 *     ],
 *
 *     'product' => [
 *       'subdir'  => '/wv/products/',
 *       'aspect'  => (9/16),
 *       'outputs' => [
 *         [
 *           'filename' => 'product-image-{{id}}.jpg',
 *           'width'    => 800,
 *           'height'   => 0,  // "0" means auto-scale by width
 *           'quality'  => 85
 *         ]
 *       ]
 *     ],
 *     ...
 *   ];
 */
class DS_Image_Profiles {

    /**
     * Return a static array of profile definitions.
     * In a real plugin, you might read from config or from a DB option.
     */
    public static function get_profiles(): array {
        return [
            'profile' => [
                'subdir'    => '/wv/avatars/{{id}}/',
                'aspect'    => 1.0,
                'overwrite' => true,
                'outputs'   => [
                    [
                        'filename' => 'profile-image-200.jpg',
                        'width'    => 200,
                        'height'   => 200,
                        'quality'  => 85,
                    ],
                    [
                        'filename' => 'profile-image-400.jpg',
                        'width'    => 400,
                        'height'   => 400,
                        'quality'  => 85,
                    ],
                ],
            ],

            'company_logo' => [
                'subdir'    => '/wv/company-logos/{{id}}/',
                'aspect'    => 1.0,
                'overwrite' => true,
                'outputs'   => [
                    [
                        'filename' => 'company-logo-200.jpg',
                        'width'    => 200,
                        'height'   => 200,
                        'quality'  => 85,
                    ],
                    [
                        'filename' => 'company-logo-400.jpg',
                        'width'    => 400,
                        'height'   => 400,
                        'quality'  => 85,
                    ],
                ],
            ],

            'product' => [
                'subdir'    => '/wv/products/',
                'aspect'    => (9 / 16),
                'overwrite' => true,
                'outputs'   => [
                    [
                        'filename' => 'product-image-{{id}}.jpg',
                        'width'    => 450,
                        'height'   => 0,
                        'quality'  => 85,
                    ],
                ],
            ],

            'banner' => [
                'subdir'    => '/wv/banners/',
                'aspect'    => (16 / 9),
                'overwrite' => false,
                'outputs'   => [
                    [
                        'filename' => 'banner-{{slug}}.jpg',
                        'width'    => 1200,
                        'height'   => 675,
                        'quality'  => 80,
                    ],
                ],
            ],
        ];
    }


    /**
     * Helper to fetch a single profile by key.
     */
    public static function get_profile( string $key ): ?array {
        $all = self::get_profiles();
        return $all[$key] ?? null;
    }
}
