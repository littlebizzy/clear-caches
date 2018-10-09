<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Admin\Views;

// Aliased namespaces
use \LittleBizzy\PurgeThemAll\Libraries;

/**
 * Displays the "Overview" tab
 *
 * @package Purge Them All
 * @subpackage Admin
 */
class Overview extends Libraries\View_Display {



	/**
	 * Output
	 */
	protected function display($args) { extract($args); ?>

		<p class="prgtha-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a tellus vitae ipsum ullamcorper aliquam id at sapien praesent accumsan.</p>

		<p class="prgtha-center"><input type="button" class="button button-primary button-large prgtha-purge-request" value="PURGE ALL CACHES!" /></p>

	<?php }



}