<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Admin\Views;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Displays the "Object Cache" tab
 *
 * @package Clear Caches
 * @subpackage Admin
 */
class Object_Cache extends Libraries\View_Display {



	/**
	 * Output
	 */
	protected function display($args) { extract($args); ?>

		<tr class="section">
			<td><label><h4>Object Cache</h4></label></td>
			<?php if (!$enabled) : ?><td>The Object Cache is not enabled.</td>
			<?php else : ?><td id="clrchs-action-object" class="clrchs-action"><input type="button" class="button button-primary clrchs-purge-button clrchs-purge-request" value="Purge Now!" /></td><?php endif; ?>
		</tr>

	<?php }



}