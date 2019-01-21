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

		<?php if (!$loaded) : ?>

			 <h3>The PHP OPcache extension is not installed.</h3>

		<?php elseif (!$enabled) : ?>

			<h3>The PHP OPcache extension is not enabled.</h3>

		<?php else : ?>

			<h3>The PHP Opcache is enabled.</h3>

			<p><input type="button" class="button button-primary clrchs-purge-button clrchs-purge-request" value="Purge Now!" /></p>

		<?php endif; ?>

	<?php }



}