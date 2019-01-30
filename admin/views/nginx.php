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

		<h2>Nginx Cache</h2>

		<table class="form-table">
			<tr>
				<th scope="row"><label for="clrchs-nginx-path">Cache Zone Path</label></th>
				<?php if (!empty($pathByConstant)) : ?><td><input type="text" disabled class="regular-text" id="clrchs-nginx-path" value="<?php echo esc_attr($pathByConstant); ?>" /></td>
				<?php else : ?><td><input type="text" class="regular-text" id="clrchs-nginx-path" placeholder="/data/nginx/cache" value="<?php echo esc_attr($path); ?>" /></td><?php endif; ?>
			</tr>
		</table>

		<p><input type="button" class="button button-primary clrchs-purge-button clrchs-purge-request" value="Purge Now!" /></p>

	<?php }



}