<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Admin\Views;

// Aliased namespaces
use \LittleBizzy\PurgeThemAll\Libraries;

/**
 * Displays the "PHP OpCache" tab
 *
 * @package Purge Them All
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

			<p><input type="button" class="button button-primary prgtha-purge-button prgtha-purge-request" value="Purge Now!" /></p>

		<?php endif; ?>

	<?php }



}