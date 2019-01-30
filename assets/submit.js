jQuery(document).ready(function($) {



	$('#wp-admin-bar-clrchs-menu a').click(function() {
		var scope = $(this).attr('href').split('#')[1];
		purgeRequest(scope, null);
		return false;
	});

	$('.clrchs-purge-request').click(function() {

		var parent = $(this).closest('.clrchs-action')
		var scope = parent.attr('id').replace('clrchs-action-', '');

		value = null
		if ('nginx' == scope) {
			value = $('#clrchs-nginx-path').val();
		}

		purgeRequest(scope, value);

		return false;
	});



	$('#clrchs-form').submit(function() {
		return false;
	});



	function purgeRequest(scope, value) {

		progressShow();
		$('#clrchs-loading-' + scope).show();

		var data = {
			'scope'  : scope,
			'action' : 'clrchs_purge',
			'nonce'  : $('#clrchs-progress').attr('data-nonce')
		}

		if ('nginx' == scope && null !== value) {
			data['nginx_path'] = value;
		}

		$.post(getServerURL(), data, function(e) {

			if ('undefined' == typeof e.status) {
				alert('Unknown error');

			} else if ('error' == e.status) {
				alert(e.reason);

			} else if ('ok' == e.status) {

				$('.clrchs-progress-item').hide();

				if ('all' == scope) {
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



});