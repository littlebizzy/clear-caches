<?php

// Subpackage namespace
namespace LittleBizzy\PurgeThemAll\Admin\Views;

// Aliased namespaces
use \LittleBizzy\PurgeThemAll\Libraries;

/**
 * Displays the "CloudFlare Cache" tab
 *
 * @package Purge Them All
 * @subpackage Admin
 */
class CloudFlare extends Libraries\View_Display {



	/**
	 * Output
	 */
	protected function display($args) { extract($args); ?>

		<?php if ($isCloudFlare) : ?><h3>You are currently using CloudFlare!</h3><?php endif; ?>

		<h2>Site settings</h2>

		<table class="form-table">
			<tr>
				<th scope="row"><label>Current Domain:</label></th>
				<td><?php echo esc_html($domain); ?></td>
			</tr>
			<tr id="prgtha-cloudflare-zone-wrapper">
				<th scope="row"><label>Cloudflare Zone:</label></th>
				<td id="prgtha-cloudflare-zone-info"></td>
			</tr>
			<tr>
				<th scope="row"><label for="prgtha-cloudflare-key">CloudFlare API Key</label></th>
				<td><input type="text" id="prgtha-cloudflare-key" class="regular-text" value="<?php echo esc_attr($key); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="prgtha-cloudflare-email">CloudFlare API Email</label></th>
				<td><input type="text" id="prgtha-cloudflare-email" class="regular-text" value="<?php echo esc_attr($email); ?>" /></td>
			</tr>
		</table>

		<p><input id="prgtha-cloudflare-settings" type="button" class="button button-primary prgtha-purge-button" value="Update API Settings" /></p>

		<div id="prgtha-cloudflare-operations"<?php if (empty($zone['id'])) : ?> style="display: none;"<?php endif; ?>>

			<h2 style="margin-bottom: 0">Development Mode</h2>

			<table class="form-table">
				<tr>
					<th scope="row"><label>Current Status:</label></th>
					<td>
						<p><span id="prgtha-cloudflare-dev-mode"><strong id="prgtha-cloudflare-dev-mode-enabled">Enabled</strong><span id="prgtha-cloudflare-dev-mode-disabled">Disabled</span></span>
						<input id="prgtha-cloudflare-dev-mode-button" type="button" class="button button-primary" value="" data-value="" data-label-on="Turn Off" data-label-off="Turn On" /></p>
						<p id="prgtha-cloudflare-dev-mode-message">Development mode will be disabled automatically after 3 hours from activation.</p>
					</td>
				</tr>
			</table>

			<h2>Purge all Cloudflare cache files</h2>

			<p><input type="button" class="button button-primary prgtha-purge-button prgtha-purge-request" value="Purge Now!" /></p>

			<script type="text/javascript">var prgtha_dev_mode = <?php echo $devMode? 'true' : 'false'; ?>, prgtha_zone = <?php echo @json_encode($zone); ?></script>

		</div>

	<?php }



}