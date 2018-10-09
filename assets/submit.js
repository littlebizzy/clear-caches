jQuery(document).ready(function($) {



	$('#wp-admin-bar-prgtha-menu a').click(function() {
		var scope = $(this).attr('href').split('#')[1];
		purgeRequest(scope, null);
		return false;
	});

	$('.prgtha-purge-request').click(function() {
		var value = null;
		var nav = $(this).closest('.prgtha-nav-content')
		var scope = nav.attr('id').replace('prgtha-nav-content-', '');
		if ('nginx' == scope)
			value = $('#prgtha-nginx-path').val();
		purgeRequest(scope, value);
		return false;
	});



	function purgeRequest(scope, value) {

		progressShow();
		$('#prgtha-loading-' + scope).show();

		var data = {
			'scope'  : scope,
			'action' : 'prgtha_purge_them_all',
			'nonce'  : $('#prgtha-progress').attr('data-nonce')
		}

		if ('nginx' == scope && null !== value)
			data['nginx_path'] = value;

		$.post(getServerURL(), data, function(e) {

			if ('undefined' == typeof e.status) {
				alert('Unknown error');

			} else if ('error' == e.status) {
				alert(e.reason);

			} else if ('ok' == e.status) {

				$('.prgtha-progress-item').hide();

				if ('all' == scope) {
					purgeResult('cloudflare', e.data);
					purgeResult('opcache', e.data);
					purgeResult('nginx', e.data);
					purgeResult('object', e.data);
				} else {
					purgeResult(scope, e.data)
				}
			}

		}).fail(function() {
			alert('Server communication error.' + "\n" + 'Please try again.');

		}).always(function() {
			$('#prgtha-progress-close').show();
		});
	}

	function purgeResult(scope, data) {
		(1 == data[scope])? $('#prgtha-done-' + scope).show() : $('#prgtha-error-' + scope).html(data[scope]).show();
	}


	$('#prgtha-cloudflare-settings').click(function() {

		progressShow();
		$('#prgtha-loading-cloudflare-settings').show();

		var data = {
			'action' 			: 'prgtha_cloudflare_settings',
			'nonce'  			: $('#prgtha-form').attr('data-nonce'),
			'cloudflare_key' 	: $('#prgtha-cloudflare-key').val(),
			'cloudflare_email' 	: $('#prgtha-cloudflare-email').val()
		}

		$.post(getServerURL(), data, function(e) {

			if ('undefined' == typeof e.status) {
				alert('Unknown error');

			} else if ('error' == e.status) {
				$('.prgtha-progress-item').hide();
				$('#prgtha-error-cloudflare-settings').html(e.reason).show();
				$('#prgtha-cloudflare-zone-wrapper').hide();
				$('#prgtha-cloudflare-operations').hide();

			} else if ('ok' == e.status) {
				$('.prgtha-progress-item').hide();
				$('#prgtha-done-cloudflare-settings').show();
				displayZoneInfo(e.data.zone);
				displayDevMode(e.data.zone['development_mode']);
				$('#prgtha-cloudflare-operations').show();
			}

		}).fail(function() {
			alert('Server communication error.' + "\n" + 'Please try again.');

		}).always(function() {
			$('#prgtha-progress-close').show();
		});

		return false;
	});



	$('#prgtha-cloudflare-dev-mode-button').click(function() {

		progressShow();
		$('#prgtha-loading-cloudflare-dev-mode').show();

		var data = {
			'action' 	: 'prgtha_cloudflare_dev_mode',
			'nonce'  	: $('#prgtha-form').attr('data-nonce'),
			'dev_mode'	: ('on' == $(this).attr('data-value'))? 'off' : 'on'
		}

		$.post(getServerURL(), data, function(e) {

			if ('undefined' == typeof e.status) {
				alert('Unknown error');

			} else if ('error' == e.status) {
				$('.prgtha-progress-item').hide();
				$('#prgtha-error-cloudflare-dev-mode').html(e.reason).show();

			} else if ('ok' == e.status) {
				$('.prgtha-progress-item').hide();
				$('#prgtha-done-cloudflare-dev-mode').show();
				displayDevMode(e.data.dev_mode > 0);
			}

		}).fail(function() {
			alert('Server communication error.' + "\n" + 'Please try again.');

		}).always(function() {
			$('#prgtha-progress-close').show();
		});

		return false;
	});



	function progressShow() {
		$('.prgtha-progress-item').hide();
		$('#prgtha-progress').prgtha_lightboxed({
			centered : true,
			lightboxSpeed : 0,
			overlaySpeed : 0,
			closeClickOutside : true,
			closeEsc : true,
			overlayCSS : {
				background: '#000',
				opacity: .5
			}
		});
	}



	function getServerURL() {
		return $('#prgtha-progress').attr('data-url') + '?_=' + new Date().getTime();
	}



	function displayDevMode(devMode) {
		if (devMode) {
			$('#prgtha-cloudflare-dev-mode-enabled').show();
			$('#prgtha-cloudflare-dev-mode-disabled').hide();
			$('#prgtha-cloudflare-dev-mode-message').show();
		} else {
			$('#prgtha-cloudflare-dev-mode-enabled').hide();
			$('#prgtha-cloudflare-dev-mode-disabled').show();
			$('#prgtha-cloudflare-dev-mode-message').hide();
		}
		var value = devMode? 'on' : 'off';
		$('#prgtha-cloudflare-dev-mode-button').attr('data-value', value);
		$('#prgtha-cloudflare-dev-mode-button').val($('#prgtha-cloudflare-dev-mode-button').attr('data-label-' + value));
	}

	if ('undefined' != typeof prgtha_dev_mode)
		displayDevMode(prgtha_dev_mode);



	function displayZoneInfo(zone) {

		if ('undefined' == typeof zone || '' == zone['id']) {
			$('#prgtha-cloudflare-zone-wrapper').hide();

		} else {

			var html = zone['name'];
			if ('' != zone['status'])
				html += ' (' + zone['status'] + ')';
			if (zone['paused'])
				html += ' <strong>PAUSED</strong>';
			$('#prgtha-cloudflare-zone-info').html(html);

			$('#prgtha-cloudflare-zone-wrapper').show();
		}
	}

	if ('undefined' != typeof prgtha_zone)
		displayZoneInfo(prgtha_zone);



});