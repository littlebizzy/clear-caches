<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Admin\Views;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Displays the "Overview" tab
 *
 * @package Clear Caches
 * @subpackage Admin
 */
class Overview extends Libraries\View_Display {



	/**
	 * Output
	 */
	protected function display($args) { extract($args); ?>

		<p class="clrchs-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a tellus vitae ipsum ullamcorper aliquam id at sapien praesent accumsan.</p>

		<p class="clrchs-center"><input type="button" class="button button-primary button-large clrchs-purge-request" value="PURGE ALL CACHES!" /></p>

	<?php }



}