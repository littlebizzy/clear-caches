<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Admin\Views;

// Aliased namespaces
use \LittleBizzy\PurgeThemAll\Libraries;

/**
 * Displays the "Object Cache" tab
 *
 * @package Purge Them All
 * @subpackage Admin
 */
class Object_Cache extends Libraries\View_Display {



	/**
	 * Output
	 */
	protected function display($args) { extract($args); ?>

		<?php if (!$enabled) : ?>

			<h3>The Object Cache is not enabled.</h3>

		<?php else : ?>

			<h3>The Object Cache is enabled.</h3>

			<p><input type="button" class="button button-primary prgtha-purge-button prgtha-purge-request" value="Purge Now!" /></p>

		<?php endif; ?>

	<?php }



}