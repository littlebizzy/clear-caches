<?php
/**
 * Class AdminBar.
 *
 * @package LittleBizzy\ClearCaches
 */

declare( strict_types=1 );

namespace LittleBizzy\ClearCaches;

use WP_Admin_Bar;

/**
 * Class AdminBar.
 *
 * @package LittleBizzy\ClearCaches
 */
class AdminBar {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'add_menu' ), - PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add admin bar menu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
	 *
	 * @return void
	 */
	public function add_menu( WP_Admin_Bar $wp_admin_bar ): void {
		$wp_admin_bar->add_node(
			array(
				'parent' => 'top-secondary',
				'id'     => 'clear-caches',
				'title'  => __( 'Purge All Caches', 'clear-caches' ),
				'href'   => '#',
				'meta'   => array( 'onclick' => 'ClearCaches.purgeAll()' ),
			)
		);
		$wp_admin_bar->add_node(
			array(
				'parent' => 'clear-caches',
				'id'     => 'clear-caches-nginx',
				'title'  => __( 'Purge Nginx Cache', 'clear-caches' ),
				'href'   => '#',
				'meta'   => array( 'onclick' => 'ClearCaches.purgeNginx()' ),
			)
		);
		$wp_admin_bar->add_node(
			array(
				'parent' => 'clear-caches',
				'id'     => 'clear-caches-opcache',
				'title'  => __( 'Purge OPcache cache', 'clear-caches' ),
				'href'   => '#',
				'meta'   => array( 'onclick' => 'ClearCaches.purgeOPcache()' ),
			)
		);
		$wp_admin_bar->add_node(
			array(
				'parent' => 'clear-caches',
				'id'     => 'clear-caches-object',
				'title'  => __( 'Purge Object Cache', 'clear-caches' ),
				'href'   => '#',
				'meta'   => array( 'onclick' => 'ClearCaches.purgeObject()' ),
			)
		);
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'clear-caches',
			plugin_dir_url( CLEAR_CACHE_FILE ) . '/assets/clear-caches.js',
			array( 'jquery' ),
			filemtime( plugin_dir_path( CLEAR_CACHE_FILE ) . '/assets/clear-caches.js' ),
			true
		);

		wp_localize_script(
			'clear-caches',
			'clearCachesData',
			array(
				'clearCachesNonce'       => wp_create_nonce( 'clear-caches' ),
				'clearCachesNginxNonce'  => wp_create_nonce( 'clear-caches-nginx' ),
				'clearCachesOpcache'     => wp_create_nonce( 'clear-caches-opcache' ),
				'clearCachesObjectNonce' => wp_create_nonce( 'clear-caches-object' ),
			)
		);
	}

}
