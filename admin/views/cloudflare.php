<?php

// Subpackage namespace
namespace LittleBizzy\ClearCaches\Admin\Views;

// Aliased namespaces
use \LittleBizzy\ClearCaches\Libraries;

/**
 * Displays the "CloudFlare Cache" tab
 *
 * @package Clear Caches
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
			<tr id="clrchs-cloudflare-zone-wrapper">
				<th scope="row"><label>Cloudflare Zone:</label></th>
				<td id="clrchs-cloudflare-zone-info"></td>
			</tr>
			<tr>
				<th scope="row"><label for="clrchs-cloudflare-key">CloudFlare API Key</label></th>
				<td><input type="text" id="clrchs-cloudflare-key" class="regular-text" value="<?php echo esc_attr($key); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="clrchs-cloudflare-email">CloudFlare API Email</label></th>
				<td><input type="text" id="clrchs-cloudflare-email" class="regular-text" value="<?php echo esc_attr($email); ?>" /></td>
			</tr>
		</table>

		<p><input id="clrchs-cloudflare-settings" type="button" class="button button-primary clrchs-purge-button" value="Update API Settings" /></p>

		<div id="clrchs-cloudflare-operations"<?php if (empty($zone['id'])) : ?> style="display: none;"<?php endif; ?>>

			<h2 style="margin-bottom: 0">Development Mode</h2>

			<table class="form-table">
				<tr>
					<th scope="row"><label>Current Status:</label></th>
					<td>
						<p><span id="clrchs-cloudflare-dev-mode"><strong id="clrchs-cloudflare-dev-mode-enabled">Enabled</strong><span id="clrchs-cloudflare-dev-mode-disabled">Disabled</span></span>
						<input id="clrchs-cloudflare-dev-mode-button" type="button" class="button button-primary" value="" data-value="" data-label-on="Turn Off" data-label-off="Turn On" /></p>
						<p id="clrchs-cloudflare-dev-mode-message">Development mode will be disabled automatically after 3 hours from activation.</p>
					</td>
				</tr>
			</table>

			<h2>Purge all Cloudflare cache files</h2>

			<p><input type="button" class="button button-primary clrchs-purge-button clrchs-purge-request" value="Purge Now!" /></p>

			<script type="text/javascript">var clrchs_dev_mode = <?php echo $devMode? 'true' : 'false'; ?>, clrchs_zone = <?php echo @json_encode($zone); ?></script>

		</div>

	<?php }



}