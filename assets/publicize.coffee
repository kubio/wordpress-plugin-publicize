
class Publicize_Setting
	constructor: ( post_url )->
		jQuery ($)->
			$('#fb_page_select').change ()->
				$('option:selected', @).each ()->
					token = $( "#fb_" + $(@).val() ).val();
					$('#fb_selected_token').val(token);


pbcz_setting = new Publicize_Setting()