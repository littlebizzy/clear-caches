<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Admin\Views;

// Aliased namespaces
use \LittleBizzy\PurgeThemAll\Libraries;

/**
 * Displays the "Nginx Cache" tab
 *
 * @package Purge Them All
 * @subpackage Admin
 */
class Nginx extends Libraries\View_Display {



	/**
	 * Output
	 */
	protected function display($args) { extract($args); ?>

		<table class="form-table">
			<tr>
				<th scope="row"><label for="prgtha-nginx-path">Cache Zone Path</label></th>
				<td><input type="text" class="regular-text" id="prgtha-nginx-path" placeholder="/data/nginx/cache" value="<?php echo esc_attr($path); ?>" /></td>
			</tr>
		</table>

		<p><input type="button" class="button button-primary prgtha-purge-button prgtha-purge-request" value="Purge Now!" /></p>

	<?php }



}