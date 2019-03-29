<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Admin\Views;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Displays the "PHP OpCache" tab
 *
 * @package Clear Caches
 * @subpackage Admin
 */
class OpCache extends Libraries\View_Display {



	/**
	 * Output
	 */
	protected function display($args) { extract($args); ?>

		<tr class="section">
			<td class="section"><label><h4>PHP OPcache</h4></label></td>
			<?php if (!$loaded) : ?><td>The PHP OPcache extension is not installed.</td>
			<?php elseif (!$enabled) : ?><td>The PHP OPcache extension is not enabled.</td>
			<?php else : ?><td id="clrchs-action-opcache" class="clrchs-action"><input type="button" class="button button-primary clrchs-purge-button clrchs-purge-request" value="Clear PHP OPcache" /></td><?php endif; ?>
		</tr>

	<?php }



}
