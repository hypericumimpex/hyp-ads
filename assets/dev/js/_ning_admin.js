/**
 *  GLOBAL FUNCTIONS
 */
var Adning_global = {
    // Adning_global.activate_tooltips();
	activate_tooltips: function(_obj){
		_obj.find(".ttip").tooltipster({
			theme: "tooltipster-light",
			functionInit: function(instance, helper){
				var $origin = jQuery(helper.origin),
					dataOptions = $origin.attr('data-tooltipster');
		
				if(dataOptions){
					dataOptions = JSON.parse(dataOptions);
		
					jQuery.each(dataOptions, function(name, option){
						instance.option(name, option);
					});
				}
			}
		});
	},



	/**
	 * CHECK IF EMAIL IS VALID
	 * Adning_global.is_valid_email();
	 */
	is_valid_email: function(email){
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);	
	},



	// Adning_global.adsense_tpl();
	adsense_tpl: function(options){
		var args = jQuery.extend({
			'pub_id': '',
			'slot_id': '',
			'type': 'normal',
			'layout_key': '',
			'layout': '',
			'width': 300,
			'height': 250,
			'google_src': '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'
		}, options );

		var code = '';

		switch ( args.type ) {
			case 'in-feed':
				code = '<script async src="'+args.google_src+'"></script>' +
						'<ins class="adsbygoogle" ' +
							 'style="display:block;" ' +
							 'data-ad-client="ca-' + args.pub_id + '" ' +
							 'data-ad-slot="' + args.slot_id + '" ' +
							 'data-ad-layout-key="' + args.layout_key + '" ';
				if ( args.layout !== '' ) {
					code += 'data-ad-layout="' + args.layout + '" ';
				}
				code += 'data-ad-format="fluid"></ins>' +
						'<script>' +
						'(adsbygoogle = window.adsbygoogle || []).push({});' +
						'</script>';
				break;
			case 'in-article':
				code = '<script async src="'+args.google_src+'"></script>' +
						'<ins class="adsbygoogle" ' +
							 'style="display:block;text-align:center;" ' +
							 'data-ad-client="ca-' + args.pub_id + '" ' +
							 'data-ad-slot="' + args.slot_id + '" ' +
							 'data-ad-layout="in-article" ' +
							 'data-ad-format="fluid"></ins>' +
						'<script>' +
						'(adsbygoogle = window.adsbygoogle || []).push({});' +
						'</script>';
				break;
			case 'matched-content':
				code = '<script async src="'+args.google_src+'"></script>' +
						'<ins class="adsbygoogle" ' +
							 'style="display:block;" ' +
							 'data-ad-client="ca-' + args.pub_id + '" ' +
							 'data-ad-slot="' + args.slot_id + '" ' +
							 'data-ad-format="autorelaxed"></ins>' +
						'<script>' +
						'(adsbygoogle = window.adsbygoogle || []).push({});' +
						'</script>';
				break;
			case 'link-responsive':
				code = '<script async src="'+args.google_src+'"></script>' +
						'<ins class="adsbygoogle" ' +
							 'style="display:block;" ' +
							 'data-ad-client="ca-' + args.pub_id + '" ' +
							 'data-ad-slot="' + args.slot_id + '" ' +
							 'data-ad-format="link"></ins>' +
						'<script>' +
						'(adsbygoogle = window.adsbygoogle || []).push({});' +
						'</script>';
				break;
			case 'link':
				code = '<script async src="'+args.google_src+'"></script>' +
						'<ins class="adsbygoogle" ' +
							 'style="display:block;width:' + args.width + 'px;height:' + args.height + 'px" ' +
							 'data-ad-client="ca-' + args.pub_id + '" ' +
							 'data-ad-slot="' + args.slot_id + '" ' +
							 'data-ad-format="link"></ins>' +
						'<script>' +
						'(adsbygoogle = window.adsbygoogle || []).push({});' +
						'</script>';
				break;
			case 'responsive':
				code = '<script async src="'+args.google_src+'"></script>' +
						'<ins class="adsbygoogle" ' +
							 'style="display:block;" ' +
							 'data-ad-client="ca-' + args.pub_id + '" ' +
							 'data-ad-slot="' + args.slot_id + '" ' +
							 'data-ad-format="auto"></ins>' +
						'<script>' +
						'(adsbygoogle = window.adsbygoogle || []).push({});' +
						'</script>';
				break;
			case 'normal':
				code = '<script async src="'+args.google_src+'"></script>' +
						'<ins class="adsbygoogle" ' +
							 'style="display:inline-block;width:' + args.width + 'px;height:' + args.height + 'px" ' +
							 'data-ad-client="ca-' + args.pub_id + '" ' +
							 'data-ad-slot="' + args.slot_id + '"></ins>' +
						'<script>' +
						'(adsbygoogle = window.adsbygoogle || []).push({});' +
						'</script>';
				break;
			default:
		}

		return code;
	},

 
	/**
	 * File upload content return based on file type
	 * Adning_global.fileContent(file);
	 * file = object( 'url': '', 'type':'video/mp4' )
	 */
	fileContent: function(file) {
		var url = file.url;
		var type = typeof file.type !== 'undefined' ? file.type : '';
		var extension = url.substr( (url.lastIndexOf('.') +1) );
		switch(extension) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'svg':
				//console.log('was jpg jpeg png gif svg'); 
				return '<div class="_ning_elmt"><img src="'+url+'" /></div>';
			break;                        
			case 'zip':
			case 'rar':
				//console.log('was zip rar');
			break;
			case 'pdf':
				//console.log('was pdf');
				return url;
			break;
			case 'mp4':
				//console.log('mp4');
				type = type !== '' ? type : 'video/'+extension;
				return '<video controls><source src="'+url+'" type="'+type+'">Your browser does not support HTML5 video.</video>';
			break;
			default:
				console.log('who knows which filetype this is...');
		}
	}
};


module.exports = {
	Adning_global: Adning_global
};



(function($, win) {
	
	$(document).ready(function(){

		Adning_global.activate_tooltips($('.adning_dashboard'));

		// POSITIONING
		$('#ADNI_align').on('change', function(){
			var pos = $(this).val();
			$('.banner_holder').find('._ning_outer').removeClass('_align_left _align_right _align_center');
			$('.banner_holder').find('._ning_outer').addClass('_align_'+pos);

			if( $('.banner_holder').find('._ning_grid').length ){
				if( pos === 'left'){
					$('.banner_holder').find('.mjs_row').removeClass('justify-content-center justify-content-end');
					$('.banner_holder').find('.mjs_row').addClass('justify-content-start');
				}
				if( pos === 'center'){
					$('.banner_holder').find('.mjs_row').removeClass('justify-content-start justify-content-end');
					$('.banner_holder').find('.mjs_row').addClass('justify-content-center');
				}
				if( pos === 'right'){
					$('.banner_holder').find('.mjs_row').removeClass('justify-content-center justify-content-start');
					$('.banner_holder').find('.mjs_row').addClass('justify-content-end');
				}
			}
		});

		// BORDER/LABEL OPTIONS
		$('#ADNI_label').on('change', function(){
			var label = $(this).val();
			$("._ning_outer").removeClass('has_label');
			$('.banner_holder').find('._ning_label').html('');
			console.log( label );
			if( label !== ''){
				$("._ning_outer").addClass('has_label');
				$('.banner_holder').find('._ning_label').html(label);
			}
		});
		$('#ADNI_label_pos').on('change', function(){
			var pos = $(this).val();
			$('.banner_holder').find('._ning_label').removeClass('_left _right _center');
			$('.banner_holder').find('._ning_label').addClass('_'+pos);
		});
		$('#ADNI_has_border').on('change', function(){
			$("._ning_outer").toggleClass('has_border');
		});

		// POSITIONING OPTIONS
		if( $('.spot_box.selected').data('custom') === 1){
			$('.custom_placement_settings_cont').show();
			$('.custom_placement_settings_cont').find('.option_'+$('.spot_box.selected').data('pos')).show();
		}
		

		// Reset stats
		$('#reset_stats').on('click', function(){
			if (window.confirm($(this).data('msg'))){
				var url = $(this).data('href');
				window.location.replace(url);
			}
		});
		

		// Remove sell order
		$('#_ning_remove_sell_order').on('click', function(){
			if (window.confirm($(this).data('msg'))){
				var url = $(this).data('href');
				window.location.replace(url);
			}
		});

		
		$('.spot_box').on('click', function(){
			var pos = $(this).data('pos'),
				has_custom = $(this).data('custom');

			$('.spot_box').removeClass('selected');
			if( pos !== ''){
				$(this).addClass('selected');
			}

			if( has_custom ){
				$('.custom_placement_settings_cont').show();
				$('.custom_placement_settings_cont').find('.custom_box').hide();
				$('.custom_placement_settings_cont').find('.option_'+pos).show();
			}else{
				$('.custom_placement_settings_cont').hide();
				$('.custom_placement_settings_cont').find('.custom_box').hide();
			}
			console.log(pos);
			$('.adning_auto_position').val(pos);
		});


		/*
		* Media Popup - works for admins only
		*/
		$('.upload_image_button').on('click', function()
		{
			var media_uploader = null;
			
			media_uploader = wp.media({
				frame:    "post", 
				state:    "insert", 
				multiple: false
			});
		
			media_uploader.on("insert", function(){
				var json = media_uploader.state().get("selection").first().toJSON();
				//console.log(json);
				/*var image_url = json.url;
				var image_caption = json.caption;
				var image_title = json.title;*/
				var content = Adning_global.fileContent({'url':json.url,'type':json.mime});
				$('#banner_content').val(content).trigger("change");
				
				// $('#banner_content').val('<div class="_ning_elmt"><img src="'+json.url+'" /></div>').trigger("change");
			});
		
			media_uploader.open();
		});



		$('.ttip').tooltipster({
			theme: 'tooltipster-light',
			multiple:true,
			maxWidth: 200,
			speed:50,
			delay:0,
			contentAsHTML: true,
			interactive: true
		});	
		

		

		var config = {
			'.chosen-select'           : {},
			'.chosen-select-deselect'  : { allow_single_deselect: true },
			'.chosen-select-no-single' : { disable_search_threshold: 10 },
			'.chosen-select-no-results': { no_results_text: 'Oops, nothing found!' },
			'.chosen-select-rtl'       : { rtl: true }
			//'.chosen-select-width'     : { width: '100%' }
		}
		for (var selector in config) {
			$(selector).chosen(config[selector]).chosenSortable();
		}

		$('.chosen-search-input').autocomplete({
			source: function( request, response ) {
				
				var input_obj = this.element;
				input_obj.css({'width': '100%'});
				var select_obj = input_obj.closest('.ning_chosen_select').find('.chosen-select');

				// Start search when string contains at least 3 characters
				if( request.term.length > 2 ){
					console.log('search: '+request.term);
					
					if( select_obj.hasClass('ning_chosen_posttype_select') ){ 
						var post_type = select_obj.data('ptype');
						
						$.ajax({
							type: "POST",
							url: _adn_.ajaxurl,
							dataType: "json",
							data: "action=display_filter_load_posts&key=post_type&search="+request.term+"&type="+post_type
						}).done(function( obj ) {
							
							if( !$.isEmptyObject(obj) )
							{
								var inpt = input_obj.val();
								
								$.map( obj, function( item ) {
									if( !select_obj.find('.opt_'+item.id).length ){
										select_obj.append('<option class="opt_'+item.id+'" value="'+item.id+'">' + item.name + ' - (#'+item.id+')</option>');
										select_obj.trigger("chosen:updated");
										input_obj.val(inpt);
									}
								});
							}
						});
					}
					// end posttype search

					if( select_obj.hasClass('ning_chosen_taxonomy_select') ){
						var taxonomy = select_obj.data('ttype');
						console.log(taxonomy);
						$.ajax({
							type: "POST",
							url: _adn_.ajaxurl,
							dataType: "json",
							data: "action=display_filter_load_posts&key=taxonomy&search="+request.term+"&type="+taxonomy
						}).done(function( obj ) {
							console.log('kak');
							console.log(obj);
							if( !$.isEmptyObject(obj) )
							{
								var inpt = input_obj.val();
								
								$.map( obj, function( item ) {
									if( !select_obj.find('.opt_'+item.id).length ){
										select_obj.append('<option class="opt_'+item.id+'" value="'+item.id+'">' + item.name + ' - (#'+item.id+')</option>');
										select_obj.trigger("chosen:updated");
										input_obj.val(inpt);
									}
								});
							}
						});
					}
					// end taxonomy search

				}
			}
		});



		// EXPORT OPTIONS iframe - embedcode
		$('.export_switcher').on('change', function(){
			console.log($(this).prop("checked"));
			console.log($(this).attr('id'));
			console.log($(this).data('opos'));
			var opos = $(this).prop("checked") ? false : true;


			$('#'+$(this).data('opos')).prop( "checked", opos );
			$('.export_switch_box').toggleClass('visible');
		});
		// Select all text
		$(".export_embed_code").on("focus keyup", function(e){
			var keycode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
			if(keycode === 9 || !keycode){
				var $this = $(this);
				$this.select();
	
				// Chrome Fix
				$this.on("mouseup", function() {
					// Unbindeamos el mouseup
					$this.off("mouseup");
					return false;
				});
			}
		});
	
	});
	

}(jQuery, window));