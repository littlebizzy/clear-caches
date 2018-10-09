jQuery(document).ready(function($) {

	$('#prgtha-nav-tabs').find('a.nav-tab').click(function() {
		if (!$(this).hasClass('nav-tab-active')) {
			$('#prgtha-nav-tabs').find('a').removeClass('nav-tab-active');
			$('.prgtha-nav-content').removeClass('prgtha-nav-content-active');
			var id = $(this).attr('id').replace('prgtha-nav-tab-', '');
			$('#prgtha-nav-content-' +  id).addClass('prgtha-nav-content-active');
			$(this).addClass('nav-tab-active');
		}
		return false;
	});

	$('#prgtha-form').submit(function() {
		return false;
	});

});