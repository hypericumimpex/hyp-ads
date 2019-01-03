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
	}
};


module.exports = {
	Adning_global: Adning_global
};



(function($, win) {
	
	// POSITIONING
	$('#ADNI_align').on('change', function(){
		var pos = $(this).val();
		$('.banner_holder').find('._ning_outer').removeClass('_align_left _align_right _align_center');
		$('.banner_holder').find('._ning_outer').addClass('_align_'+pos);
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
	

}(jQuery, window));