<?php
if( !is_user_logged_in() )
    return;

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$is_frontend = isset($is_frontend) ? $is_frontend : 0;
$adzone_post = array();
$user_id = get_current_user_id();

if( isset($_GET['reset_stats']) && !empty($_GET['reset_stats']))
{
    ADNI_Main::reset_stats($id, 'id_2');
}

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

if( !current_user_can(ADNI_ADZONES_ROLE) && $user_id != $adzone['post']->post_author)
{
    echo '<div style="margin-top:50px;text-align:center;">'.__('Sorry, This adzone does not exists.','adn').'</div>';
    return;
}
//echo '<pre>'.print_r($adzone['args'],true).'</pre>';
//echo '<pre>'.print_r(ADNI_Sell::adzones_for_sale(),true).'</pre>';


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
                                    <h3 class="title"><?php _e('Description','adn'); ?></h3>
                                    <div class="input_container_inner">
                                        <textarea id="adzoneDesc" name="description" style="min-height:120px;font-size:11px;"><?php echo $adzone['args']['description']; ?></textarea>
                                    </div>
                                    <span class="description bottom"><?php _e('Adzone description.','adn'); ?></span>
                                </div>
                                <!-- end .input_container -->

                                <?php
                                $h = '';
                                $h.= '<div class="input_container">';
                                    $h.= '<h3 class="title">'.__('Status','adn').'</h3>';
                                        $h.= '<div class="input_container_inner">';
                                           
                                            $h.= '<select name="status" class="">';
                                                $h.= '<option value="active" '.selected( $adzone['args']['status'], 'active', false).'>'.__('Active','adn').'</option>';
                                                $h.= '<option value="on-hold" '.selected( $adzone['args']['status'], 'on-hold', false).'>'.__('On Hold','adn').'</option>';
                                            $h.= '</select>';
                                            
                                        $h.= '</div>';
                                    $h.= '<span class="description bottom">'.__('Adzone status.','adn').'</span>';
                                $h.= '</div>';
                                //<!-- end .input_container -->
                                echo $h;
                                ?>
                                 
                                 
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
                            * ADZONE STATS
                            */
                            -->
                            <?php echo ADNI_Templates::stats_settings_tpl(array('id' => $id, 'frontend' => $is_frontend), $adzone['args']); ?>

                            
                            <!--
                            /**
                            * CAMPAIGNS
                            */
                            -->
                            <?php echo ADNI_Templates::link_campaign_tpl($adzone['args']); ?>


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
                            <?php echo ADNI_Templates::export_tpl($adzone); ?>
                            
                             
                             
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
                                    <span class="fa tog ttip" title="<?php _e('Toggle box','adn'); ?>"></span>
                                    <input type="submit" value="<?php _e('Save AD Zone','adn'); ?>" class="button-primary" name="save_adzone" style="width:auto;float:right;margin:8px;">
                                </div>
                                <!-- end .info_header -->

                                <div class="settings_box_content">
                             
                                    <div class="sep_line" style="margin:0 0 15px 0;"><span><strong><?php _e('Sizing','adn'); ?></strong></span></div>
                                    <div class="spr_column spr_col-4">
                                        <div class="spr_column-inner left_column">
                                            <div class="spr_wrapper">
                                                <div class="input_container">
                                                    <h3 class="title"><?php _e('','adn'); ?></h3>
                                                        <div class="input_container_inner">
                                                        <select id="ADNI_size" name="size" class="">
                                                            <?php
                                                                foreach(ADNI_Main::banner_sizes() as $size)
                                                                {
                                                                    echo '<option value="'.$size['size'].'" '.selected( $adzone['args']['size'], $size['size'] ).'>'.$size['name'].' ('.$size['size'].')</option>';
                                                                }
                                                                ?>
                                                        <option value="custom" <?php selected( $adzone['args']['size'], 'custom' ); ?>>Custom</option>
                                                        </select>
                                                    </div>
                                                    <span class="description bottom"><?php _e('Select one of the common banner sizes.','adn'); ?></span>
                                                </div>
                                                <!-- end .input_container -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->
                                    
                                    <?php
                                    $h = '';
                                    $h.= ADNI_Templates::spr_column(array(
                                        'col' => 'spr_col-2',
                                        'title' => '',
                                        'desc' => __('Responsive','adn'),
                                        'content' => ADNI_Templates::switch_btn(array(
                                            'name' => 'responsive',
                                            'id' => 'ADNI_responsive',
                                            'tooltip' => __('Responsive adzone.','adn'),
                                            'checked' => $adzone['args']['responsive'],
                                            'value' => 1,
                                            'hidden_input' => 1,
                                            'chk-on' => __('On','adn'),
                                            'chk-off' => __('Off','adn'),
                                            'chk-high' => 1
                                        ))
                                    ));
                                    echo $h;
                                    ?>
                                    
                                    
                                    <div class="spr_column spr_col-6">
                                        <div class="spr_column-inner">
                                            <div class="spr_wrapper">
                                                
                                                <?php
                                                $h = '';
                                                $h.= ADNI_Templates::spr_column(array(
                                                    'col' => 'spr_col-6',
                                                    'title' => '',
                                                    'desc' => __('width.','adn'),
                                                    'content' => ADNI_Templates::inpt_cont(array(
                                                        'type' => 'text',
                                                        'width' => '100%',
                                                        'class' => '_ning_custom_size',
                                                        'name' => 'size_w',
                                                        'id' => 'ADNI_size_w',
                                                        'value' => $adzone['args']['size_w'],
                                                        'placeholder' => '',
                                                        'icon' => 'arrows-h',
                                                        'show_icon' => 1
                                                    ))
                                                ));
                                                $h.= ADNI_Templates::spr_column(array(
                                                    'col' => 'spr_col-6',
                                                    'title' => '',
                                                    'desc' => __('height.','adn'),
                                                    'content' => ADNI_Templates::inpt_cont(array(
                                                        'type' => 'text',
                                                        'width' => '100%',
                                                        'class' => '_ning_custom_size',
                                                        'name' => 'size_h',
                                                        'id' => 'ADNI_size_h',
                                                        'value' => $adzone['args']['size_h'],
                                                        'placeholder' => '',
                                                        'icon' => 'arrows-v',
                                                        'show_icon' => 1
                                                    ))
                                                ));
                                                echo $h;
                                                ?>
                                                
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
                                                
                                                <?php echo ADNI_Templates::adzone_tpl($id, array('filter' => 0, 'stats' => 0)); ?>  
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
                                                                                'value'   => array($adzone['args']['size']),
                                                                                'compare' => 'IN',
                                                                            ),
                                                                        )
                                                                    ));
                                                                    $not_fitting_banners = ADNI_CPT::get_posts(array(
                                                                        'post__not_in' => $adzone['args']['linked_banners'],
                                                                        'meta_query' => array(
                                                                            array(
                                                                                'key'     => '_adning_size',
                                                                                'value'   => array($adzone['args']['size']),
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
                                                                                echo '<option value="'.$banner_id.'" selected>'.get_the_title($banner_id).' - #'.$banner_id.'</option>';
                                                                            }
                                                                            echo '</optgroup>';
                                                                        }
                                                                        ?>
                                                                        <optgroup label="<?php echo sprintf(__('Fitting Banners (%s)','adn'), $adzone['args']['size']); ?>">
                                                                            <?php
                                                                            if( !empty($fitting_banners))
                                                                            {
                                                                                foreach( $fitting_banners as $banner)
                                                                                {
                                                                                    echo '<option value="'.$banner->ID.'">'.$banner->post_title.' - #'.$banner->ID.'</option>';
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                echo '<option value="" disabled>'.sprintf(__('No %s banners found.','adn'), $adzone['args']['size']).'</option>';
                                                                            }
                                                                            ?>
                                                                        </optgroup>
                                                                        <optgroup label="<?php _e('Other Banners','adn'); ?>">
                                                                            <?php
                                                                            if( !empty($not_fitting_banners))
                                                                            {
                                                                                
                                                                                foreach( $not_fitting_banners as $banner)
                                                                                {
                                                                                    echo '<option value="'.$banner->ID.'">'.$banner->post_title.' - #'.$banner->ID.'</option>';
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
                                                                <!-- end .input_container_inner -->
                                                                <span class="description bottom"><?php _e('Select the banners to link to this adzone. Drag to change the order.','adn'); ?></span>
                                                            </div>
                                                            <!-- end .input_container -->
                                                        </div>
                                                        <!-- end .spr_wrapper -->
                                                </div>
                                                <!-- end .spr_column-inner -->
                                                </div>
                                                <!-- end .spr_column -->

                                                
                                                <div class="spr_column"> <!-- spr_col-4 -->
                                                <div class="spr_column-inner">
                                                        <div class="spr_wrapper">
                                                            <div class="sep_line" style="margin:25px 0 25px 0;"><span><strong><?php _e('Order and Loading settings','adn'); ?></strong></span></div>
                                                            
                                                            <?php
                                                            $h = '';
                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-4',
                                                                'title' => __('Random Order','adn'),
                                                                'desc' => __('Load banners in random order.','adn'),
                                                                'content' => ADNI_Templates::switch_btn(array(
                                                                    'name' => 'random_order',
                                                                    'checked' => $adzone['args']['random_order'],
                                                                    'value' => 1,
                                                                    'hidden_input' => 1,
                                                                    'chk-on' => __('Yes','adn'),
                                                                    'chk-off' => __('No','adn'),
                                                                    'chk-high' => 1
                                                                ))
                                                            ));
                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-4',
                                                                'title' => __('Single banner','adn'),
                                                                'desc' => __('Load one banner at the time (no transition, one banner on page load).','adn'),
                                                                'content' => ADNI_Templates::switch_btn(array(
                                                                    'name' => 'load_single',
                                                                    'checked' => $adzone['args']['load_single'],
                                                                    'value' => 1,
                                                                    'hidden_input' => 1,
                                                                    'chk-on' => __('Yes','adn'),
                                                                    'chk-off' => __('No','adn'),
                                                                    'chk-high' => 1
                                                                ))
                                                            ));
                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-4',
                                                                'title' => __('Allow scrolling','adn'),
                                                                'desc' => __('Allow users to (touch) scroll true the ads.','adn'),
                                                                'content' => ADNI_Templates::switch_btn(array(
                                                                    'name' => 'touch_scroll',
                                                                    'checked' => $adzone['args']['touch_scroll'],
                                                                    'value' => 1,
                                                                    'hidden_input' => 1,
                                                                    'chk-on' => __('Yes','adn'),
                                                                    'chk-off' => __('No','adn'),
                                                                    'chk-high' => 1
                                                                ))
                                                            ));
                                                            echo $h;
                                                            ?>

                                                            <div class="clearFix"></div>
                                                            
                                                            <div class="spr_column spr_col-4">
                                                                <div class="spr_column-inner">
                                                                    <div class="spr_wrapper">
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
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="spr_column spr_col-4">
                                                                <div class="spr_column-inner">
                                                                    <div class="spr_wrapper">

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
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="clearFix"></div>
                                                            <div class="sep_line" style="margin:25px 0 25px 0;"><span><strong><?php _e('AD Grid settings','adn'); ?></strong></span></div>
                                                            
                                                            <?php
                                                            $h = '';
                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-3',
                                                                'title' => __('Load as Grid','adn'),
                                                                'desc' => __('Load multiple banners at the same time.','adn'),
                                                                'content' => ADNI_Templates::switch_btn(array(
                                                                    'name' => 'load_grid',
                                                                    'checked' => $adzone['args']['load_grid'],
                                                                    'value' => 1,
                                                                    'hidden_input' => 1,
                                                                    'chk-on' => __('Yes','adn'),
                                                                    'chk-off' => __('No','adn'),
                                                                    'chk-high' => 1
                                                                ))
                                                            ));
                                                            echo $h;
                                                            ?>

                                                            <div class="spr_column spr_col-3">
                                                                <div class="spr_column-inner">
                                                                    <div class="spr_wrapper">

                                                                        <div class="input_container">
                                                                            <h3 class="title"><?php _e('Grid Columns','adn'); ?></h3>
                                                                            <div class="input_container_inner">
                                                                                <input 
                                                                                    type="text" 
                                                                                    class="" 
                                                                                    name="grid_columns" 
                                                                                    value="<?php echo !empty($adzone['args']['grid_columns']) ? $adzone['args']['grid_columns'] : 2; ?>" 
                                                                                    placeholder="">
                                                                                <i class="input_icon fa fa-arrows-v" aria-hidden="true"></i>
                                                                            </div>
                                                                            <span class="description bottom"><?php _e('Amount of columns (vertical) for the grid (int value).','adn'); ?></span>
                                                                        </div>
                                                                        <!-- end .input_container -->
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="spr_column spr_col-3">
                                                                <div class="spr_column-inner">
                                                                    <div class="spr_wrapper">

                                                                        <div class="input_container">
                                                                            <h3 class="title"><?php _e('Grid Rows','adn'); ?></h3>
                                                                            <div class="input_container_inner">
                                                                                <input 
                                                                                    type="text" 
                                                                                    class="" 
                                                                                    name="grid_rows" 
                                                                                    value="<?php echo !empty($adzone['args']['grid_rows']) ? $adzone['args']['grid_rows'] : 2; ?>" 
                                                                                    placeholder="">
                                                                                <i class="input_icon fa fa-arrows-h" aria-hidden="true"></i>
                                                                            </div>
                                                                            <span class="description bottom"><?php _e('Amount of rows (horizontal) for the grid (int value).','adn'); ?></span>
                                                                        </div>
                                                                        <!-- end .input_container -->
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="clearFix"></div>
                                                            

                                                        </div>
                                                    </div>
                                                </div>
                                            
                                            
                                            </div>
                                            <!-- end .spr_wrapper -->
                                        </div>
                                        <!-- end .spr_column-inner -->
                                    </div>
                                    <!-- end .spr_column -->

                                    <?php
                                    $h = '';
                                    $h.= ADNI_Templates::spr_column(array(
                                        'col' => 'spr_col',
                                        'title' => '',
                                        'desc' => '',
                                        'content' => '<input type="submit" value="'.__('Save AD Zone','adn').'" class="button-primary" name="save_adzone">'
                                    ));

                                $h.= '</div>';
                                // end .settings_box_content
                                echo $h;
                                ?> 
                                
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

                        echo ADNI_Templates::parallax_tpl($adzone);

                        /*
                         * Action: 'adning_single_adzone_settings' - Allow other plugins to add options inside the Adzone settings section.
                        */
                        do_action( 'adning_single_adzone_settings', $adzone );
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

    //Adning_global.activate_tooltips($('.adning_dashboard'));
    
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
	
});
</script>