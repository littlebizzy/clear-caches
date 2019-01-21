<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Admin\Views;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Displays the "Nginx Cache" tab
 *
 * @package Clear Caches
 * @subpackage Admin
 */
class Nginx extends Libraries\View_Display {



	/**
	 * Output
	 */
	protected function display($args) { extract($args); ?>

		<table class="form-table">
			<tr>
				<th scope="row"><label for="clrchs-nginx-path">Cache Zone Path</label></th>
				<td><input type="text" class="regular-text" id="clrchs-nginx-path" placeholder="/data/nginx/cache" value="<?php echo esc_attr($path); ?>" /></td>
			</tr>
		</table>

		<p><input type="button" class="button button-primary clrchs-purge-button clrchs-purge-request" value="Purge Now!" /></p>

	<?php }



}