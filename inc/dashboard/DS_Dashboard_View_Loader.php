<?php
namespace Desymphony\Dashboard;

use Desymphony\Helpers\DS_Utils;
use Desymphony\Favorites\DS_Favorites_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DS_Dashboard_View_Loader {

    /**
     * Renders a dashboard view by loading the layout file.
     *
     * @param string $section The section to load (e.g. 'home', 'meetings').
     * @param array  $data    Optional data to extract for use in the view.
     */
    public function render( string $section, array $data = [] ) : void {
        // 1) Which sections exist & whoâ€™s allowed
        $section_roles_map = [
            'home'        => [ 'administrator', 'exhibitor', 'buyer', 'visitor' ],
            'products'    => [ 'exhibitor' ],
            'profile'     => [ 'exhibitor', 'buyer', 'visitor' ],
            'saved'       => 'comingfall',
            'application' => [ 'exhibitor' ],         
            'co_ex'       => [ 'exhibitor' ],

            'meetings'    => 'comingfall',
            'calendar'    => 'comingfall',
            'services'    => [ 'exhibitor' ],
            'messages'    => 'comingfall',            
            'events'      => 'comingfall',
            'members'     => [ 'exhibitor' ],
            'notifications' => [ 'exhibitor', 'buyer', 'visitor' ],

        ];

        // 2) Force login
        if ( ! is_user_logged_in() ) {
            wp_redirect( home_url() );
            exit;
        }

        // 3) Check valid section
        if ( ! isset( $section_roles_map[ $section ] ) ) {
            $section = 'noaccess';
        }

        $roles_or_status = $section_roles_map[ $section ];
        if (in_array($roles_or_status, ['comingsoon', 'comingfall'], true)) {
            $section = $roles_or_status;
        } else {
            $user_role = DS_Utils::current_user_role();
            if ( ! in_array( $user_role, (array) $roles_or_status, true ) ) {
                $section = 'noaccess';
            } else {
                // visitor => "Company" check for certain sections
                if ( $user_role === 'visitor' && in_array( $section, [ 'meetings', 'calendar', 'saved', 'messages' ], true ) ) {
                    $endeavour = DS_Utils::get_visitor_endeavour();
                    if ( $endeavour !== 'Company' ) {
                        $section = 'noaccess';
                    }
                }
            }
        }

        // 4) If "saved" section, compute favorites counts BEFORE loading layout
        if ( $section === 'saved' ) {
            $current_user_id = get_current_user_id();
            $all_favs = DS_Favorites_Manager::get_user_favorites( $current_user_id );

            // We'll separate them similarly to what's done in dashboard-saved
            $ex = []; $by = [];
            $wine=[]; $rakija=[]; $food=[]; $other=[];

            global $wpdb;
            $table_products = $wpdb->prefix . 'wv_products';

            foreach ( $all_favs as $fav ) {
                if ( $fav->target_type === 'user' ) {
                    $u = get_userdata( $fav->target_id );
                    if ( $u ) {
                        $roles = (array) $u->roles;
                        $role  = reset($roles) ?: '';
                        if ( $role === 'exhibitor' ) {
                            $ex[] = $u;
                        } elseif ( $role === 'buyer' ) {
                            $by[] = $u;
                        }
                    }
                }
                elseif ( $fav->target_type === 'product' ) {
                    $row = $wpdb->get_row(
                        $wpdb->prepare("SELECT * FROM $table_products WHERE id=%d", $fav->target_id)
                    );
                    if ( $row ) {
                        $ptype = in_array($row->type, ['wine','rakija','food']) ? $row->type : 'other';
                        if ( $ptype === 'wine' ) {
                            $wine[]   = $row;
                        } elseif ( $ptype === 'rakija' ) {
                            $rakija[] = $row;
                        } elseif ( $ptype === 'food' ) {
                            $food[]   = $row;
                        } else {
                            $other[]  = $row;
                        }
                    }
                }
            }

            $GLOBALS['wv_saved_counts'] = [
                'exhibitors' => count($ex),
                'buyers'     => count($by),
                'wine'       => count($wine),
                'rakija'     => count($rakija),
                'food'       => count($food),
                'other'      => count($other),
            ];
        }

        // 5) Determine partial
        if ( $section === 'noaccess' ) {
            $dashboard_view = DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-no-access.php';
        }
        elseif ( $section === 'comingfall' ) {
                $dashboard_view = DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-coming-fall.php';
                if (!file_exists($dashboard_view)) {
                    wp_die('Missing coming fall view file at: ' . $dashboard_view);
                }
            }

        elseif ( $section === 'comingsoon' ) {
            $dashboard_view = DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-coming-soon.php';
        }
        else {
            $section_file = DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-' . $section . '.php';
            $dashboard_view = file_exists( $section_file ) ? $section_file : false;
            if ( ! $dashboard_view ) {
                $section = 'noaccess';
                $dashboard_view = DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-no-access.php';
            }
        }

        extract( $data );
        $layout_path = DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-layout.php';

        if ( ! file_exists( $layout_path ) ) {
            wp_die( __( $layout_path. ' - Dashboard layout file missing.', 'wv-addon' ) );
        }

        include $layout_path;
    }
}