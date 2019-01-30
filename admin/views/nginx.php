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

		<tr class="section">
			<td><h4>NGINX</h4></td>
			<td></td>
		</tr>
		<tr>
			<td><label for="clrchs-nginx-path">Cache Zone Path</label></td>
			<?php if (empty($pathByConstant)) : ?><td id="clrchs-action-nginx-path" class="clrchs-action"><input type="text" class="regular-text code" id="clrchs-nginx-path" placeholder="/data/nginx/cache" value="<?php echo esc_attr($path); ?>" />
			&nbsp; <input type="button" class="button clrchs-purge-button-save clrchs-purge-request" value="Save" /></td>
			<?php else : ?><td><input type="text" disabled class="regular-text code" id="clrchs-nginx-path" value="<?php echo esc_attr($pathByConstant); ?>" /></td><?php endif; ?>
		</tr>
		<tr>
			<td><label>NGINX cache files</label></td>
			<td id="clrchs-action-nginx" class="clrchs-action"><input type="button" class="button button-primary clrchs-purge-button clrchs-purge-request" value="Purge Now!" /></td>
		</tr>

	<?php }



}