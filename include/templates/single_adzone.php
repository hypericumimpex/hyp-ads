<?php
if( !is_user_logged_in() )
    return;

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$is_frontend = isset($is_frontend) ? $is_frontend : 0;
$adzone_post = array();
$user_id = get_current_user_id();

/**
 * POST
*/
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['save_adzone']))
	{
        $id = ADNI_CPT::add_update_post($_POST);
	}
}

$auto_pos = ADNI_Main::auto_positioning();
//echo '<pre>'.print_r($auto_pos,true).'</pre>';

/*
 * Load Post data or default values
*/
$adzone = ADNI_CPT::load_post($id, array('post_type' => ADNI_CPT::$adzone_cpt));

if( !current_user_can(ADNI_ADMIN_ROLE) && $user_id != $adzone['post']->post_author)
{
    echo '<div style="margin-top:50px;text-align:center;">'.__('Sorry, This adzone does not exists.','adn').'</div>';
    return;
}
//echo '<pre>'.print_r($adzone,true).'</pre>';

/**
 * Check if user has access to this banner
*/
if( !empty( $adzone['post'] ))
{
    ADNI_CPT::user_has_access(array(
        'id' => $id,
        'author' => $adzone['post']->post_author,
        'post_type' => ADNI_CPT::$adzone_cpt
    ));	
}
?>

<div class="adning_cont adning_add_new_adzone">
	<div class="wrap">
    
    	<!-- Wordpress Messages -->
        <h2 class="messages-position"></h2>
        
        <?php echo ADNI_Templates::admin_header(); ?>
        
        <form action="" method="post" enctype="multipart/form-data"> 
        	<input type="hidden" value="<?php echo $id; ?>" name="post_id">
           <input type="hidden" value="<?php echo ADNI_CPT::$adzone_cpt; ?>" name="post_type">
           
           <div class="spr_row">  
                <div class="spr_column spr_col-4">
                    <div class="spr_column-inner left_column">
                        <div class="spr_wrapper">
                            <div class="option_box">
                                <div class="info_header">
                                    <span class="nr">1</span>
                                    <span class="text"><?php _e('AD Zone Settings','adn'); ?></span>
                                 </div>
                                 <div class="input_container">
                                    <h3 class="title"><?php _e('Title','adn'); ?></h3>
                                    <div class="input_container_inner">
                                            <input 
                                            type="text" 
                                            class="" 
                                            name="title" 
                                            value="<?php echo !empty($adzone['post']) ? $adzone['post']->post_title : ''; ?>" 
                                            placeholder="<?php _e('AD Zone Title','adn'); ?>">
                                        <i class="input_icon fa fa-pencil" aria-hidden="true"></i>
                                    </div>
                                    <span class="description bottom"><?php _e('Add the adzone title.','adn'); ?></span>
                                 </div>
                                 <!-- end .input_container -->
                                 
                                 
                                 <div class="input_container">
                                    <h3 class="title"><?php _e('Transition','adn'); ?></h3>
                                    <div class="input_container_inner">
                                     <select name="adzone_transition" id="ssTransition">
                                     	<?php require_once(ADNI_INC_DIR.'/files/animations.php'); ?>
                                     </select>
                
                						</div>
                                    <span class="description bottom"><?php _e('Transission Effect.','adn'); ?></span>
                                 </div>
                                 <!-- end .input_container -->

                                 <div class="input_container">
                                    <h3 class="title"><?php _e('Transition time','adn'); ?></h3>
                                    <div class="input_container_inner">
                                        <input 
                                            type="text" 
                                            class="" 
                                            name="adzone_transition_time" 
                                            value="<?php echo !empty($adzone['args']['adzone_transition_time']) ? $adzone['args']['adzone_transition_time'] : 5; ?>" 
                                            placeholder="<?php _e('Transition seconds','adn'); ?>">
                                        <i class="input_icon fa fa-pencil" aria-hidden="true"></i>
                                    </div>
                                    <span class="description bottom"><?php _e('Amount of seconds between banner transitions.','adn'); ?></span>
                                 </div>
                                 <!-- end .input_container -->
                                 
                                 <div class="input_container">
                                	 	<div class="input_container_inner">
                                    	<div class="sep_line" style="margin:10px 0 20px 0;"><span><strong><?php _e('Save','adn'); ?></strong></span></div>
                                			<input type="submit" value="<?php _e('Save AD Zone','adn'); ?>" class="button-primary" name="save_adzone" style="width: auto;">
                                 	</div>
                                 	<span class="description bottom"><?php _e('','adn'); ?></span>
                             	 </div>
                             	 <!-- end .input_container -->
                                 
                             </div>
                             <!-- end .option_box -->
                            


                            <!--
                            /**
                            * ALIGNMENT SETTINGS
                            */
                            -->
                            <?php echo ADNI_Templates::alignment_settings_tpl($adzone['args']); ?>


                            <!--
                            /**
                            * BORDER SETTINGS
                            */
                            -->
                            <?php echo ADNI_Templates::border_settings_tpl($adzone['args']); ?>
                             
                             
                            <!--
                            /**
                             * EXPORT adzone
                            */
                            -->
                            <?php
                            if($id)
                            {
                                ?>
                                <div class="option_box">
                                        <div class="info_header">
                                            <span class="icon"><i class="fa fa-code" aria-hidden="true"></i></span>
                                            <span class="text"><?php _e('Export','adn'); ?></span>
                                    </div>
                                    <div class="input_container">
                                        <h3 class="title"><?php _e('','adn'); ?></h3>
                                            <div class="input_container_inner">
                                            <input id="sc_code" style="font-size:11px;" value='[adning id="<?php echo $id; ?>"]' />
                                        </div>
                                        <span class="description bottom"><?php _e('Shortcode.','adn'); ?></span>
                                    </div>
                                    <!-- end .input_container -->

                                    <div class="input_container">
                                        <h3 class="title"><?php _e('','adn'); ?></h3>
                                            <div class="input_container_inner">
                                            <textarea id="embed_code" style="min-height:120px;font-size:11px;"><script type="text/javascript">var _ning_embed = {"id":"<?php echo $id; ?>","width":<?php echo $adzone['args']['adzone_size_w']; ?>,"height":<?php echo $adzone['args']['adzone_size_h']; ?>};</script><script type="text/javascript" src="<?php echo get_bloginfo('url'); ?>?_dnembed=true"></script></textarea>
                                        </div>
                                        <span class="description bottom"><?php _e('Embed code.','adn'); ?></span>
                                    </div>
                                    <!-- end .input_container -->
                                </div>
                                <!-- end .option_box -->
                                <?php
                            }
                            ?>
                             
                             
                             
                         </div>
                         <!-- end .spr_wrapper -->
                     </div>
                     <!-- end .spr_column-inner -->
                 </div>
                 <!-- end .spr_column -->
                 
                 
                 
                 
                <div class="spr_column spr_col-8">
                     <div class="spr_column-inner left_column">
                         <div class="spr_wrapper">
                             <div class="option_box">
                        			<div class="info_header">
                                		<span class="nr">2</span>
                            			<span class="text"><?php _e('AD Zone','adn'); ?></span>
                                		<input type="submit" value="<?php _e('Save AD Zone','adn'); ?>" class="button-primary" name="save_adzone" style="width:auto;float:right;margin:8px;">
                                
										<?php 
										/*if( $id ){
											echo '<a href="'.get_permalink($id).'" target="_blank" class="button" style="width:auto;float:right;margin:8px;">'.__('Preview Banner','adn').'</a>';
										}*/
										?>
                             	</div>
                                <!-- end .info_header -->
                             
                             	<div class="sep_line" style="margin:0 0 15px 0;"><span><strong><?php _e('Sizing','adn'); ?></strong></span></div>
                                <div class="spr_column spr_col-4">
                                    <div class="spr_column-inner left_column">
                                        <div class="spr_wrapper">
                                            <div class="input_container">
                                                <h3 class="title"><?php _e('','adn'); ?></h3>
                                                    <div class="input_container_inner">
                                                    <select id="ADNI_size" name="adzone_size" class="">
                                                        <?php
                                                            foreach(ADNI_Main::banner_sizes() as $size)
                                                            {
                                                                echo '<option value="'.$size['size'].'" '.selected( $adzone['args']['adzone_size'], $size['size'] ).'>'.$size['name'].' ('.$size['size'].')</option>';
                                                            }
                                                            ?>
                                                      <option value="custom" <?php selected( $adzone['args']['adzone_size'], 'custom' ); ?>>Custom</option>
                                                    </select>
                                                </div>
                                                <span class="description bottom"><?php _e('Select one of the common banner sizes.','adn'); ?></span>
                                             </div>
                                             <!-- end .input_container -->
                                        </div>
                                    </div>
                                </div>
                                <!-- end .spr_column -->
                                
                                
                                <div class="spr_column spr_col-2">
                                    <div class="spr_column-inner">
                                        <div class="spr_wrapper">
                                            <div class="input_container">
                                                <h3 class="title"><?php _e('','adn'); ?></h3>
                                                <div class="input_container_inner">
                                                    <label class="switch switch-slide small input_h ttip" title="<?php _e('Responsive adzone.','adn'); ?>">
                                                        <input class="switch-input" type="checkbox" id="ADNI_responsive" name="adzone_responsive" value="1" <?php checked( $adzone['args']['adzone_responsive'], 1 ); ?> />
                                                        <span class="switch-label" data-on="<?php _e('On','adn'); ?>" data-off="<?php _e('Off','adn'); ?>"></span> 
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                                <span class="description bottom"><?php _e('Responsive','adn'); ?></span>
                                            </div>
                                            <!-- end .input_container -->
                                        </div>
                                    </div>
                                </div>
                                <!-- end .spr_column -->
                                
                                
                                <div class="spr_column spr_col-6">
                                    <div class="spr_column-inner">
                                        <div class="spr_wrapper">
                                            
                                            <div class="spr_column spr_col-6">
                                                <div class="spr_column-inner left_column">
                                                    <div class="spr_wrapper">
                                                        <div class="input_container">
                                                            <h3 class="title"><?php _e('','adn'); ?></h3>
                                                            <div class="input_container_inner">
                                                                    <input 
                                                                    type="text" 
                                                                    class="_ning_custom_size" 
                                                                    id="ADNI_size_w" 
                                                                    name="adzone_size_w" 
                                                                    value="<?php echo $adzone['args']['adzone_size_w']; ?>" 
                                                                    placeholder="<?php _e('','adn'); ?>">
                                                                <i class="input_icon fa fa-arrows-h" aria-hidden="true"></i>
                                                            </div>
                                                            <span class="description bottom"><?php _e('width.','adn'); ?></span>
                                                         </div>
                                                         <!-- end .input_container -->
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end .spr_column -->
                                            <div class="spr_column spr_col-6">
                                                <div class="spr_column-inner">
                                                    <div class="spr_wrapper">
                                                        <div class="input_container">
                                                            <h3 class="title"><?php _e('','adn'); ?></h3>
                                                            <div class="input_container_inner">
                                                                    <input 
                                                                    type="text" 
                                                                    class="_ning_custom_size" 
                                                                    id="ADNI_size_h" 
                                                                    name="adzone_size_h" 
                                                                    value="<?php echo $adzone['args']['adzone_size_h']; ?>" 
                                                                    placeholder="<?php _e('','adn'); ?>">
                                                                <i class="input_icon fa fa-arrows-v" aria-hidden="true"></i>
                                                            </div>
                                                            <span class="description bottom"><?php _e('height.','adn'); ?></span>
                                                         </div>
                                                         <!-- end .input_container -->
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end .spr_column -->
                                            
                                        </div>
                                    </div>
                                </div>
                                <!-- end .spr_column -->
                                
                                
                                
                               
                                <div class="spr_column">
                                    <div class="spr_column-inner">
                                        <div class="spr_wrapper">
                                        
                                            <div class="sep_line" style="margin:0 0 5px 0;"><span><strong><?php _e('Preview','adn'); ?></strong></span></div>
                                            <div class="banner_holder clear" style="padding:20px;">
                                                <div class="banner_notice"></div>
                                               
                                               <?php echo ADNI_Templates::adzone_tpl($id, array()); ?>  
                                            </div>
                                            <!-- end .banner_holder -->
                                            
                                           <div class="sep_line" style="margin:0 0 25px 0;"><span><strong><?php _e('Linked Banners','adn'); ?></strong></span></div>
                                           
                                           <!-- LINKED BANNERS BOX -->
                                           <div class="spr_column"> <!-- spr_col-4 -->
                                               <div class="spr_column-inner">
                                                   <div class="spr_wrapper">
                                                   	<div class="input_container">
                                                        <h3 class="title"><?php _e('','adn'); ?></h3>
                                                            <div class="input_container_inner">
                                                            <?php
																	$fitting_banners = ADNI_CPT::get_posts(array(
																		'post__not_in' => $adzone['args']['linked_banners'],
																		'meta_query' => array(
																			array(
																				'key'     => '_adning_size',
																				'value'   => array($adzone['args']['adzone_size']),
																				'compare' => 'IN',
																			),
																		)
																	));
																	$not_fitting_banners = ADNI_CPT::get_posts(array(
																		'post__not_in' => $adzone['args']['linked_banners'],
																		'meta_query' => array(
																			array(
																				'key'     => '_adning_size',
																				'value'   => array($adzone['args']['adzone_size']),
																				'compare' => 'NOT IN',
																			),
																		)
                                                                    ));
																   ?>
                                                            <select name="linked_banners[]" multiple data-placeholder="Select Banners" class="chosen-select chosen-sortable">
                                                             <?php
																	if( !empty($adzone['args']['linked_banners']))
																	{
																		echo '<optgroup label="'.__('Linked Banners','adn').'">';
																		foreach( $adzone['args']['linked_banners'] as $banner_id)
																		{
																			echo '<option value="'.$banner_id.'" selected>'.get_the_title($banner_id).'</option>';
																		}
																		echo '</optgroup>';
																	}
																	?>
                                                            	<optgroup label="<?php echo sprintf(__('Fitting Banners (%s)','adn'), $adzone['args']['adzone_size']); ?>">
                                                                <?php
                                                                    if( !empty($fitting_banners))
                                                                    {
                                                                        foreach( $fitting_banners as $banner)
                                                                        {
                                                                            echo '<option value="'.$banner->ID.'">'.$banner->post_title.'</option>';
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        echo '<option value="" disabled>'.sprintf(__('No %s banners found.','adn'), $adzone['args']['adzone_size']).'</option>';
                                                                    }
                                                                ?>
                                                              </optgroup>
                                                              <optgroup label="<?php _e('Other Banners','adn'); ?>">
                                                                <?php
                                                                    if( !empty($not_fitting_banners))
                                                                    {
                                                                        
                                                                        foreach( $not_fitting_banners as $banner)
                                                                        {
                                                                            echo '<option value="'.$banner->ID.'">'.$banner->post_title.'</option>';
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        echo '<option value="" disabled>'.__('No other banners found.','adn').'</option>';
                                                                    }
                                                                ?>
                                                              </optgroup>
                                                            </select>
                                                        </div>
                                                        <span class="description bottom"><?php _e('Select the banners to link to this adzone. Drag to change the order.','adn'); ?></span>
                                                     </div>
                                                     <!-- end .input_container -->
                                                   </div>
                                                   <!-- end .spr_wrapper -->
                                               </div>
                                               <!-- end .spr_column-inner -->
                                           </div>
                                           <!-- end .spr_column -->
                                           
                                    
                                           
                                           
                                       </div>
                                       <!-- end .spr_wrapper -->
                                   </div>
                                   <!-- end .spr_column-inner -->
                               </div>
                               <!-- end .spr_column -->
                                
                                
                            </div>
                            <!-- end .option_box -->
                        </div>
                        <!-- end .spr_wrapper -->
                    </div>
                 	<!-- end .spr_column-inner -->


                    <div class="spr_column">
                        <?php
                        echo ADNI_Templates::auto_positioning_template($id, $adzone);
                        echo ADNI_templates::display_filters_tpl($adzone);
                        ?>
                    </div>
                    <!-- end .spr_column -->

                </div>
                <!-- end .spr_column -->



                

            </div>
            <!-- end .spr_row -->
         
        </form>
        
    </div>
    <!-- end .wrap -->
</div>
<!-- end .adning_add_new_adzone -->



<script>
jQuery(document).ready(function($) {

    Adning_global.activate_tooltips($('.adning_dashboard'));


    /*// POSITIONING OPTIONS
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
            $('.custom_placement_settings_cont').find('.option_'+pos).show();
        }else{
            $('.custom_placement_settings_cont').hide();
            $('.custom_placement_settings_cont').find('.custom_box').hide();
        }
        console.log(pos);
        $('.adning_auto_position').val(pos);
    });
    */
    
	$('#ADNI_size').on('change', function(){
		var size = $(this).val(),
			sizes = size.split("x");
		
		console.log('common banner size change');
		
		if( size !== 'custom'){
			$('#ADNI_size_w').val(sizes[0]);
			$('#ADNI_size_h').val(sizes[1]);

			// Change preview banner size
			//$("._ning_cont").ningResponsive({width:sizes[0], height:sizes[1]});
			
			//banner_resized_notice();
		}
	});
	//$('#banner_size').trigger("change");
	
	$('._ning_custom_size').on('change', function(){
		var w = $('#ADNI_size_w').val(),
			h = $('#ADNI_size_h').val();
		
		console.log('custom size change');
		
		// Select banner size option	
		if($("#ADNI_size option[value='"+w+"x"+h+"']").length > 0){
			$('#ADNI_size option[value="'+w+'x'+h+'"]').attr('selected', 'selected').change();
		}else{
			$('#ADNI_size option[value="custom"').attr('selected', 'selected').change();
		}
			
		// Change preview banner size
		//$("._ning_cont").ningResponsive({width:w, height:h});
		//banner_resized_notice();
	});
	
	
	/**
	 * TOOLTIPS
	*/
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
	  '.chosen-select-rtl'       : { rtl: true },
	  //'.chosen-select-width'     : { width: '100%' }
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]).chosenSortable();
	}
	
});
</script>