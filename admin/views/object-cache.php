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

		<h2>Object Cache</h2>

		<?php if (!$enabled) : ?>

			<h4>The Object Cache is not enabled.</h4>

		<?php else : ?>

			<h4>The Object Cache is enabled.</h4>

			<p><input type="button" class="button button-primary clrchs-purge-button clrchs-purge-request" value="Purge Now!" /></p>

		<?php endif; ?>

	<?php }



}