"use strict"
jQuery(document).ready(function($){
	var current_items = $('#toplevel_page_eos_quil_settings_page'),
		current_submenu = current_items.find('ul a[href="' + document.location.href + '"]'),
		current_parent = current_submenu.closest('.wp-has-submenu');
	$('#menu-settings-column .accordion-section').removeClass('open');
	$('#menu-settings-column #add-custom-links').addClass('open');
	if($('#menu-appearance.wp-has-current-submenu').length > 0){
		$('#menu-appearance,#menu-appearance a').removeClass('wp-has-current-submenu').removeClass('wp-menu-open');
		current_items.addClass('wp-has-current-submenu').addClass('wp-menu-open').removeClass('wp-not-current-submenu');
		$('.current').removeClass('current');
		current_submenu.closest('li').addClass('current');
		current_parent.addClass('wp-has-current-submenu').addClass('wp-menu-open');
		current_parent.children('a').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu');
	}
	$('#link-target-hide').attr('checked','checked').trigger('change');
	$('#title-attribute-hide').attr('checked','checked').trigger('change');
	$('#css-classes-hide').attr('checked','checked').trigger('change');
	$('.field-title-attribute').removeClass('hidden-field');
	$('.field-link-target').removeClass('hidden-field');
	$('.field-css-classes').removeClass('hidden-field');
	$('#screen-options-wrap,#show-settings-link').css('display','none');
	$(".eos-quil-save-eos_quil_settings_page").on("click", function () {
		$('.eos-quil-opts-msg').addClass('eos-hidden');
		var ajax_loader = $(this).next(".ajax-loader-img");
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_quil_setts").val(),
				"role" : $("#eos-quil-roles option:selected").val(),
				"action" : 'eos_quil_save_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-quil-opts-msg_success').removeClass('eos-hidden');
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-quil-opts-msg_warning').text(response);
						$('.eos-quil-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-quil-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
});