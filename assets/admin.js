jQuery(document).ready(function($) {

	$('#clrchs-nav-tabs').find('a.nav-tab').click(function() {
		if (!$(this).hasClass('nav-tab-active')) {
			$('#clrchs-nav-tabs').find('a').removeClass('nav-tab-active');
			$('.clrchs-nav-content').removeClass('clrchs-nav-content-active');
			var id = $(this).attr('id').replace('clrchs-nav-tab-', '');
			$('#clrchs-nav-content-' +  id).addClass('clrchs-nav-content-active');
			$(this).addClass('nav-tab-active');
		}
		return false;
	});

});