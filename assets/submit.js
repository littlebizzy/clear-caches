jQuery(document).ready(function($) {



	$('#wp-admin-bar-clrchs-menu a').click(function() {
		var scope = $(this).attr('href').split('#')[1];
		purgeRequest(scope, null);
		return false;
	});

	$('.clrchs-purge-request').click(function() {
		var value = null;
		var nav = $(this).closest('.clrchs-nav-content')
		var scope = nav.attr('id').replace('clrchs-nav-content-', '');
		if ('nginx' == scope)
			value = $('#clrchs-nginx-path').val();
		purgeRequest(scope, value);
		return false;
	});



	function purgeRequest(scope, value) {

		progressShow();
		$('#clrchs-loading-' + scope).show();

		var data = {
			'scope'  : scope,
			'action' : 'clrchs_purge_them_all',
			'nonce'  : $('#clrchs-progress').attr('data-nonce')
		}

		if ('nginx' == scope && null !== value)
			data['nginx_path'] = value;

		$.post(getServerURL(), data, function(e) {

			if ('undefined' == typeof e.status) {
				alert('Unknown error');

			} else if ('error' == e.status) {
				alert(e.reason);

			} else if ('ok' == e.status) {

				$('.clrchs-progress-item').hide();

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
			$('#clrchs-progress-close').show();
		});
	}

	function purgeResult(scope, data) {
		(1 == data[scope])? $('#clrchs-done-' + scope).show() : $('#clrchs-error-' + scope).html(data[scope]).show();
	}


	$('#clrchs-cloudflare-settings').click(function() {

		progressShow();
		$('#clrchs-loading-cloudflare-settings').show();

		var data = {
			'action' 			: 'clrchs_cloudflare_settings',
			'nonce'  			: $('#clrchs-form').attr('data-nonce'),
			'cloudflare_key' 	: $('#clrchs-cloudflare-key').val(),
			'cloudflare_email' 	: $('#clrchs-cloudflare-email').val()
		}

		$.post(getServerURL(), data, function(e) {

			if ('undefined' == typeof e.status) {
				alert('Unknown error');

			} else if ('error' == e.status) {
				$('.clrchs-progress-item').hide();
				$('#clrchs-error-cloudflare-settings').html(e.reason).show();
				$('#clrchs-cloudflare-zone-wrapper').hide();
				$('#clrchs-cloudflare-operations').hide();

			} else if ('ok' == e.status) {
				$('.clrchs-progress-item').hide();
				$('#clrchs-done-cloudflare-settings').show();
				displayZoneInfo(e.data.zone);
				displayDevMode(e.data.zone['development_mode']);
				$('#clrchs-cloudflare-operations').show();
			}

		}).fail(function() {
			alert('Server communication error.' + "\n" + 'Please try again.');

		}).always(function() {
			$('#clrchs-progress-close').show();
		});

		return false;
	});



	$('#clrchs-cloudflare-dev-mode-button').click(function() {

		progressShow();
		$('#clrchs-loading-cloudflare-dev-mode').show();

		var data = {
			'action' 	: 'clrchs_cloudflare_dev_mode',
			'nonce'  	: $('#clrchs-form').attr('data-nonce'),
			'dev_mode'	: ('on' == $(this).attr('data-value'))? 'off' : 'on'
		}

		$.post(getServerURL(), data, function(e) {

			if ('undefined' == typeof e.status) {
				alert('Unknown error');

			} else if ('error' == e.status) {
				$('.clrchs-progress-item').hide();
				$('#clrchs-error-cloudflare-dev-mode').html(e.reason).show();

			} else if ('ok' == e.status) {
				$('.clrchs-progress-item').hide();
				$('#clrchs-done-cloudflare-dev-mode').show();
				displayDevMode(e.data.dev_mode > 0);
			}

		}).fail(function() {
			alert('Server communication error.' + "\n" + 'Please try again.');

		}).always(function() {
			$('#clrchs-progress-close').show();
		});

		return false;
	});



	function progressShow() {
		$('.clrchs-progress-item').hide();
		$('#clrchs-progress').clrchs_lightboxed({
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
		return $('#clrchs-progress').attr('data-url') + '?_=' + new Date().getTime();
	}



	function displayDevMode(devMode) {
		if (devMode) {
			$('#clrchs-cloudflare-dev-mode-enabled').show();
			$('#clrchs-cloudflare-dev-mode-disabled').hide();
			$('#clrchs-cloudflare-dev-mode-message').show();
		} else {
			$('#clrchs-cloudflare-dev-mode-enabled').hide();
			$('#clrchs-cloudflare-dev-mode-disabled').show();
			$('#clrchs-cloudflare-dev-mode-message').hide();
		}
		var value = devMode? 'on' : 'off';
		$('#clrchs-cloudflare-dev-mode-button').attr('data-value', value);
		$('#clrchs-cloudflare-dev-mode-button').val($('#clrchs-cloudflare-dev-mode-button').attr('data-label-' + value));
	}

	if ('undefined' != typeof clrchs_dev_mode)
		displayDevMode(clrchs_dev_mode);



	function displayZoneInfo(zone) {

		if ('undefined' == typeof zone || '' == zone['id']) {
			$('#clrchs-cloudflare-zone-wrapper').hide();

		} else {

			var html = zone['name'];
			if ('' != zone['status'])
				html += ' (' + zone['status'] + ')';
			if (zone['paused'])
				html += ' <strong>PAUSED</strong>';
			$('#clrchs-cloudflare-zone-info').html(html);

			$('#clrchs-cloudflare-zone-wrapper').show();
		}
	}

	if ('undefined' != typeof clrchs_zone)
		displayZoneInfo(clrchs_zone);



});