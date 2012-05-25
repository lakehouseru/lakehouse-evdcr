function utf8_encode ( argString ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // +   bugfixed by: Ulrich
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'

    var string = (argString+''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");

    var utftext = "";
    var start, end;
    var stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;

        if (c1 < 128) {
            end++;
        } else if (c1 > 127 && c1 < 2048) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }

    if (end > start) {
        utftext += string.substring(start, string.length);
    }

    return utftext;
}

function base64_encode (data) {
    // http://kevin.vanzonneveld.net
    // +   original by: Tyler Akins (http://rumkin.com)
    // +   improved by: Bayron Guevara
    // +   improved by: Thunder.m
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Pellentesque Malesuada
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // -    depends on: utf8_encode
    // *     example 1: base64_encode('Kevin van Zonneveld');
    // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='

    // mozilla has this native
    // - but breaks in 2.0.0.12!
    //if (typeof this.window['atob'] == 'function') {
    //    return atob(data);
    //}
        
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, enc="", tmp_arr = [];

    if (!data) {
        return data;
    }

    data = this.utf8_encode(data+'');
    
    do { // pack three octets into four hexets
        o1 = data.charCodeAt(i++);
        o2 = data.charCodeAt(i++);
        o3 = data.charCodeAt(i++);

        bits = o1<<16 | o2<<8 | o3;

        h1 = bits>>18 & 0x3f;
        h2 = bits>>12 & 0x3f;
        h3 = bits>>6 & 0x3f;
        h4 = bits & 0x3f;

        // use hexets to index into b64, and append result to encoded string
        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    } while (i < data.length);
    
    enc = tmp_arr.join('');
    
    switch (data.length % 3) {
        case 1:
            enc = enc.slice(0, -2) + '==';
        break;
        case 2:
            enc = enc.slice(0, -1) + '=';
        break;
    }

    return enc;
}

function wp_carousel_update_ajax_item() {
	jQuery(function($){

		var temp_order = '';
		var temp_pre_response = new Array();
		
		$("#items_in_carousel .item").each(function (index) {
			$("input#order", this).val(index);
			var temp_iteration = this;
			temp_order = 'action=getItemThumbnail&item_id=' + $("#category_id", this).val() + '&item_type=' + $("input#type", this).val();
			$.post($("#current_url_get_js").attr("href"), temp_order, function(theTempResponse){
				temp_pre_response = theTempResponse.split('<p style="display:none;"></p>');
				$(".item_thumbnail", temp_iteration).css("background-image", "url(" + temp_pre_response[1] + ")");
			});
		});
			
		var wp_carousel_update_url = $("#current_url_js").attr("href");
		var wp_carousel_carousel_id = $("#carousel_id").text();

		var order = '';
		$("#sortable_carousel .wp_carousel_ajax_form").each(function(index) {
			if (index != 0)
			{
				order = order + '&';
			}
			order = order + 'iteration_' + index + '=' + base64_encode($(this).serialize());
		});
		order = order + '&action=updateSortableContent&must_backup=yes&internal_type=serialized&carousel_id=' + wp_carousel_carousel_id;
		
		//var order = $(".wp_carousel_ajax_form").serialize() + '&action=updateRecordsListings';
		$.post(wp_carousel_update_url, order, function(theResponse){
			$("#wp_carousel_ajax_response").html(theResponse);
		});
		
		//alert(order);
		
	});
	return false;
}

function wp_carousel_update_ajax_item_order() {
	jQuery(function($){

		var temp_order = '';
		var temp_pre_response = new Array();
		
		$("#items_in_carousel .item").each(function (index) {
			$("input#order", this).val(index);
			var temp_iteration = this;
			temp_order = 'action=getItemThumbnail&item_id=' + $("#category_id", this).val() + '&item_type=' + $("input#type", this).val();
			$.post($("#current_url_get_js").attr("href"), temp_order, function(theTempResponse){
				temp_pre_response = theTempResponse.split('<p style="display:none;"></p>');
				$(".item_thumbnail", temp_iteration).css("background-image", "url(" + temp_pre_response[1] + ")");
			});
		});
			
		var wp_carousel_update_url = $("#current_url_js").attr("href");
		var wp_carousel_carousel_id = $("#carousel_id").text();

		var order = '';
		$("#sortable_carousel .wp_carousel_ajax_form").each(function(index) {
			if (index != 0)
			{
				order = order + '&';
			}
			order = order + 'iteration_' + index + '=' + base64_encode($(this).serialize());
		});
		order = order + '&action=updateSortableContent&must_backup=no&internal_type=serialized&carousel_id=' + wp_carousel_carousel_id;
		
		//var order = $(".wp_carousel_ajax_form").serialize() + '&action=updateRecordsListings';
		$.post(wp_carousel_update_url, order, function(theResponse){
			$("#wp_carousel_ajax_response").html(theResponse);
		});
		
		//alert(order);
		
	});
	return false;
}

function wp_carousel_toggle_backup_item(form) {
	jQuery(function($){
		
		var this_identifier = ".cell_related_with_carousel_" + form.id.value;
		var this_title_identifier = ".wp_carousel_" + form.id.value + "_toggle_submit";
		var this_status_identifier = ".wp_carousel_" + form.id.value + "_toggle_status";
		
		if (form.status.value == "shown")
		{		
			$(this_identifier, this).slideUp(300);
			$(this_title_identifier, this).val("+");
			$(this_status_identifier, this).val("hide");
		}
		else
		{
			$(this_identifier, this).slideDown(300);
			$(this_title_identifier, this).val("-");
			$(this_status_identifier, this).val("shown");
		}
						
	});
	return false;
}

function wp_carousel_toggle_all_backups() {
	
	jQuery(function($){

		if ($(".wp_carousel_hide_all_form_submit").is(":visible"))
		{
			$(".cell_related_with_carousel", this).slideUp(300);
			$(".wp_carousel_toggle_status", this).val("hide");
			$(".wp_carousel_toggle_submit", this).val("+");
		}
		else
		{
			$(".cell_related_with_carousel", this).slideDown(300);
			$(".wp_carousel_toggle_status", this).val("shown");
			$(".wp_carousel_toggle_submit", this).val("-");
		}
					
		if ($(".wp_carousel_hide_all_form_submit").is(":visible"))
		{
			$(".wp_carousel_hide_all_form_submit").hide();
			$(".wp_carousel_show_all_form_submit").show();
		}
		else
		{
			$(".wp_carousel_show_all_form_submit").hide();
			$(".wp_carousel_hide_all_form_submit").show();
		}
						
	});
		
	return false;
}

function wp_carousel_toggle_item(form) {
	jQuery(function($){
		
		var this_identifier = "#sortable_carousel #" + form.id.value + " .item_content";
		var this_title_identifier = "#sortable_carousel #" + form.id.value + " .wp_carousel_toggle_submit";
		var this_status_identifier = "#sortable_carousel #" + form.id.value + " .wp_carousel_toggle_status";
		
		if (form.status.value == "shown")
		{		
			$(this_identifier, this).slideUp(300);
			$(this_title_identifier, this).val("+");
			$(this_status_identifier, this).val("hide");
		}
		else
		{
			$(this_identifier, this).slideDown(300);
			$(this_title_identifier, this).val("-");
			$(this_status_identifier, this).val("shown");
		}
						
	});
	return false;
}

function wp_carousel_toggle_all() {
	
	jQuery(function($){

		$("#sortable_carousel .item").each(function (index) {

			if ($(".wp_carousel_hide_all_form_submit").is(":visible"))
			{
				$(".item_content", this).slideUp(300);
				$(".wp_carousel_toggle_status", this).val("hide");
				$(".wp_carousel_toggle_submit", this).val("+");
			}
			else
			{
				$(".item_content", this).slideDown(300);
				$(".wp_carousel_toggle_status", this).val("shown");
				$(".wp_carousel_toggle_submit", this).val("-");
			}
			
		});
		
		if ($(".wp_carousel_hide_all_form_submit").is(":visible"))
		{
			$(".wp_carousel_hide_all_form_submit").hide();
			$(".wp_carousel_show_all_form_submit").show();
		}
		else
		{
			$(".wp_carousel_show_all_form_submit").hide();
			$(".wp_carousel_hide_all_form_submit").show();
		}
						
	});
		
	return false;
}

function wp_carousel_update_ajax_carousel_name() {
	jQuery(function($){
		
		var wp_carousel_update_url = $("#current_url_js").attr("href");
		var wp_carousel_carousel_id = $("#carousel_id").text();

		var order = '';
		order = 'action=updateStandardOptions&content=' + base64_encode($('#form_carousel_name').serialize()) + '&internal_type=serialized&carousel_id=' + wp_carousel_carousel_id;
		
		$.post(wp_carousel_update_url, order, function(theResponse){
			$("#wp_carousel_ajax_response").html(theResponse);
		});
				
	});
	return false;
}


function wp_carousel_update_ajax_standard_options() {
	jQuery(function($){
		
		var wp_carousel_update_url = $("#current_url_js").attr("href");
		var wp_carousel_carousel_id = $("#carousel_id").text();

		var order = '';
		order = 'action=updateStandardOptions&content=' + base64_encode($('#theme_standard_options').serialize()) + '&internal_type=serialized&carousel_id=' + wp_carousel_carousel_id;
		
		$.post(wp_carousel_update_url, order, function(theResponse){
			$("#wp_carousel_ajax_response_for_options").html(theResponse);
		});
				
	});
	return false;
}

function wp_carousel_update_ajax_theme_options() {
	jQuery(function($){
		
		var wp_carousel_update_url = $("#current_url_js").attr("href");
		var wp_carousel_carousel_id = $("#carousel_id").text();

		var order = '';
		order = 'action=updateThemeOptions&content=' + base64_encode($('#theme_custom_options').serialize()) + '&internal_type=serialized&carousel_id=' + wp_carousel_carousel_id;
		
		$.post(wp_carousel_update_url, order, function(theResponse){
			$("#wp_carousel_ajax_response_for_options").html(theResponse);
		});
				
	});
	return false;
}

function wp_carousel_restore_backup(backup_time) {
	jQuery(function($){

		var wp_carousel_update_url = $("#current_url_js").attr("href");
		
		var order = '';
		order = 'action=restoreBackup&backup_time=' + backup_time;
		
		$.post(wp_carousel_update_url, order, function(theResponse){
			$("#ajax_backup_response").html(theResponse);
		});
				
	});
	return false;
}

jQuery(document).ready(function($) {
		
	$("#items_in_carousel .costumized_content").each(function (index) {
		$("input#category_id", this).val(index);
	});
	
	$("#wp_carousel_ajax_loader, .changes_saved").hide();
	
	$("#wp_carousel_ajax_loader").ajaxStop(function() {
		$(this).hide(300);
		$("#wp_carousel_ajax_response").show(400);
		$("#wp_carousel_ajax_response_for_options").show(400);
		$("#ajax_backup_response").show(400);
	});
	
	$("#wp_carousel_ajax_loader").ajaxStart(function() {
		$(this).show(300);
		$("#wp_carousel_ajax_response").hide(400);
		$("#wp_carousel_ajax_response_for_options").hide(400);
		$("#ajax_backup_response").hide(400);
	});
	
	$(".wp_carousel_tabs_js").tabs();
	$(".js_hide, .add_form").hide();
	$("#content_posts").hide();
	$("#content_pages").hide();
	$("#content_categories").hide();
	$("#show_in_loop_div").hide();
	$("#posts_set_number").hide();
	$("#posts_set_order").hide();
	$("#sortable_items .item").draggable({
		connectToSortable: '.connected',
		placeholder: 'wp_carousel_ui-state-highlight',
		helper: 'clone',
		items: ".item", 
		handle: ".handle",
		revert: 'invalid'
	}).disableSelection();
	$("#sortable_deleted").sortable({
		items: ".item", 
		handle: ".handle",
		connectWith: '.connected2',
		update: function() {
			$("#sortable_deleted div").hide(500);
			wp_carousel_update_ajax_item_order();
		}
	});
	$("#sortable_carousel").sortable({
		connectWith: '.connected, .connected2',
		items: ".item", 
		handle: ".handle",
		placeholder: 'wp_carousel_ui-state-highlight',
		cancel: '.clear, .wp_carousel_disable_drag',
		update: function() {
			$(".add_form", this).show(500);
			$(".pre_dropped", this).hide(500);
			$(".post_dropped", this).show(500);
			$("#items_in_carousel .costumized_content").each(function (index) {
				$("input#category_id", this).val(index);
			});
			wp_carousel_update_ajax_item_order();
		}
	});
	
	$("#themes_carousel").jcarousel({
		scroll: 1,
		wrap: 'both'
	});
	
	$("#themes_carousel div.info-icon").hover(function(){
		$('.info-container', this).show(300);
	}, function() {
		$('.info-container', this).hide(300);
	});
	
	$("#use_jcarousel").change(function(){
		if ($(this).attr('checked') == "checked")
		{
			$(".stepcarousel_feature").hide(300);
			$(".jcarousel_feature").show(300);
		}
		else
		{
			$(".stepcarousel_feature").show(300);
			$(".jcarousel_feature").hide(300);
		}
	});
	
	if ($("#use_jcarousel").attr('checked') == "checked")
	{
		$(".stepcarousel_feature").hide(300);
		$(".jcarousel_feature").show(300);
	}
	else
	{
		$(".stepcarousel_feature").show(300);
		$(".jcarousel_feature").hide(300);
	}
	
	$("#delete_current_carousel").click(function() {
		$("#overlay_wp_carousel_popup").fadeIn(200);
		$("#delete_current_carousel_popup").fadeIn(400);
	});
	
	$("#wp_carousel_show_export_code").click(function() {
		$("#overlay_wp_carousel_popup").fadeIn(200);
		$("#wp_carousel_export_code_popup").fadeIn(400);
	});
	
	$(".preview_carousel").click(function() {
		$("#overlay_wp_carousel_popup").fadeIn(200);
		$("#preview_backup_" + $(this).attr('id')).fadeIn(400);
	});
	
	$("#overlay_wp_carousel_popup").click(function() {
		$("#overlay_wp_carousel_popup").fadeOut(400);
		$("#delete_current_carousel_popup").fadeOut(200);
		$("#wp_carousel_export_code_popup").fadeOut(200);
		$(".preview_backup_popup").fadeOut(200);
	});
	
	$(".restore_carousel").click(function() {
		wp_carousel_restore_backup($(this).attr('id'));
	});
	
});