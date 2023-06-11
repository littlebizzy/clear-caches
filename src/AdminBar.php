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
				'meta'   => array( 'class' => 'clear-caches-links', 'onclick' => 'ClearCaches.purge(event, "all")' ),
			)
		);

		foreach ( Plugin::get_cache_types() as $id => $data ) {
			$wp_admin_bar->add_node(
				array(
					'parent' => 'clear-caches',
					'id'     => 'clear-caches-' . $id,
					'title'  => $data['title'],
					'href'   => '#',
					'meta'   => array( 'onclick' => 'ClearCaches.purge(event, "' . $id . '")' ),
				)
			);
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
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
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'clear-caches' ),
			)
		);
	}
}
