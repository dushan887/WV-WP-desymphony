<?php
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DS_Role_Manager {

	/**
	 * A custom capability to assign to administrator.
	 */
	private static $plugin_cap = 'manage_wv_addon';

	/**
	 * Updated roles for this project:
	 *  - exhibitor
	 *  - buyer
	 *  - visitor
	 */
	private static $roles = [
		'exhibitor' => [
			'name' => 'Exhibitor',
			'caps' => [
				'read'               => true,
				'publish_exhibitors' => true,
				'edit_exhibitors'    => true,
				'delete_exhibitors'  => false,
			],
		],
		'buyer' => [
			'name' => 'Buyer',
			'caps' => [
				'read' 				=> true,
				'view_products'     => true,
				'purchase_products' => true,
				// Add more buyer capabilities if necessary
			],
		],
		'visitor' => [
			'name' => 'Visitor',
			'caps' => [
				'read' => true,
				// Add more visitor capabilities if necessary
			],
		],
		'wv_admin' => [
			'name' => 'WV Admin',
			'caps' => [
				'read' => true,
			],
		],
	];

	/**
	 * Create roles and add a plugin-specific capability to the administrator.
	 */
	public static function add_roles(): void {
		foreach ( self::$roles as $slug => $data ) {
			add_role( $slug, $data['name'], $data['caps'] );
		}

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( self::$plugin_cap );
		}
	}

	/**
	 * Remove roles and strip the capability from the administrator.
	 * Only do this on uninstall if you want to remove user data entirely.
	 */
	public static function remove_roles(): void {
		foreach ( array_keys( self::$roles ) as $slug ) {
			remove_role( $slug );
		}

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->remove_cap( self::$plugin_cap );
		}
	}
}
