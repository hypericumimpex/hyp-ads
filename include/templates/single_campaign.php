<?php
if( !is_user_logged_in() )
    return;


$id = isset($_GET['id']) ? $_GET['id'] : 0;
$is_frontend = isset($is_frontend) ? $is_frontend : 0;
$campaign_post = array();
$user_id = get_current_user_id();


/**
 * POST
*/
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['save_campaign']))
	{
        //echo '<pre>'.print_r($_POST, true).'</pre>';
        $id = ADNI_CPT::add_update_post($_POST);
	}
}


/*
 * Load Post data or default values
*/
$campaign_post = ADNI_CPT::load_post($id, array('post_type' => ADNI_CPT::$campaign_cpt, 'filter' => 0));
$c = $campaign_post['args'];
//echo '<pre>'.print_r($c, true).'</pre>';


if( !current_user_can(ADNI_CAMPAIGNS_ROLE) && $user_id != $campaign_post['post']->post_author)
{
    echo '<div style="margin-top:50px;text-align:center;">'.__('Sorry, This campaign does not exists.','adn').'</div>';
    return;
}




?>

<div class="adning_cont adning_add_new_campaign">
	<div class="wrap">
    
    	<!-- Wordpress Messages -->
        <h2 class="messages-position"></h2>
        
        <?php echo ADNI_Templates::admin_header(); ?>
        
        <form action="" method="post" enctype="multipart/form-data"> 
        	<input type="hidden" value="<?php echo $id; ?>" name="post_id">
            <input type="hidden" value="<?php echo ADNI_CPT::$campaign_cpt; ?>" name="post_type">

            <div class="spr_row">  
                <div class="spr_column spr_col-4">
                    <div class="spr_column-inner left_column">
                        <div class="spr_wrapper">
                            <div class="option_box">
                                <div class="info_header">
                                    <span class="nr">1</span>
                                    <span class="text"><?php _e('Campaign Settings','adn'); ?></span>
                                </div>
                                <div class="input_container">
                                    <h3 class="title"><?php _e('Title','adn'); ?></h3>
                                    <div class="input_container_inner">
                                            <input 
                                            type="text" 
                                            class="" 
                                            name="title" 
                                            value="<?php echo !empty($campaign_post['post']) ? $campaign_post['post']->post_title : ''; ?>" 
                                            placeholder="<?php _e('Campaign Title','adn'); ?>">
                                        <i class="input_icon fa fa-pencil" aria-hidden="true"></i>
                                    </div>
                                    <span class="description bottom"><?php _e('Add a campaign title.','adn'); ?></span>
                                </div>
                                <!-- end .input_container -->

                                <div class="input_container">
                                    <h3 class="title"><?php _e('Description','adn'); ?></h3>
                                    <div class="input_container_inner">
                                        <textarea id="campaignDesc" name="description" style="min-height:120px;font-size:11px;"><?php echo $c['description']; ?></textarea>
                                    </div>
                                    <span class="description bottom"><?php _e('Campaign description.','adn'); ?></span>
                                </div>
                                <!-- end .input_container -->

                                <?php
                                /*
                                <div class="input_container">
                                    <h3 class="title"><?php _e('Status','adn'); ?></h3>
                                        <div class="input_container_inner">
                                            <select name="status" class="">
                                                <option value="active" <?php selected( $c['status'], 'active' ); ?>><?php _e('Active','adn'); ?></option>
                                                <option value="expired" <?php selected( $c['status'], 'expired' ); ?>><?php _e('Expired','adn'); ?></option>
                                                <option value="draft" <?php selected( $c['status'], 'draft' ); ?>><?php _e('Draft','adn'); ?></option>
                                                <option value="on-hold" <?php selected( $c['status'], 'on-hold' ); ?>><?php _e('On Hold','adn'); ?></option>
                                            </select>
                                        </div>
                                    <span class="description bottom"><?php _e('Campaign status.','adn'); ?></span>
                                </div>
                                <!-- end .input_container -->
                                */
                                ?>

                            </div>
                        </div>
                    </div>
                    <!-- end .spr_column-inner -->

                    <div class="spr_column-inner left_column">
                        <div class="spr_wrapper">
                            <div class="option_box">
                                <div class="info_header">
                                    <span class="nr">3</span>
                                    <span class="text"><?php _e('Marketing Dates','adn'); ?></span>
                                </div>
                                
                                <?php
                                $h = '';
                                $h.= '<div class="sep_line" style="margin:0 0 15px 0;"><span><strong>'.__('E-Commerce Marketing dates','adn').'</strong></span></div>';
                                $h.= '<div class="clear" style="border-bottom: solid 1px #efefef;margin-bottom: 20px;">
                                    <div class="input_container">
                                        <h3 class="title">'.__('','adn').'</h3>
                                    </div>
                                    <div class="spr_column">
                                        <div class="input_container">';

                                        $h.= '<select id="marketing_calendar" data-placeholder="'.__('Select option', 'adn').'" style="width:100%;">';
                                            $h.= '<option value=""></option>';
                                            
                                            $ecom = array();//array_key_exists('ids', $c['display_filter']['months']) ? $c['display_filter']['months']['ids'] : array();
                                            $all_dates = ADNI_Templates::marketing_dates();

                                            foreach($all_dates as $key => $date)
                                            {
                                                $date_str = '';
                                                $date_str.= array_key_exists('month',$date['date']) && !empty($date['date']['month']) ? ADNI_Templates::months($date['date']['month']) : '';
                                                $date_str.= array_key_exists('day',$date['date']) && !empty($date['date']['day']) ? ' '.$date['date']['day'] : '';
                                                $date_str.= array_key_exists('year',$date['date']) && !empty($date['date']['year']) ? ', '.$date['date']['year'] : '';
                                                $h.= '<option value="'.$key.'" data-did="'.$date['date_id'].'">'.$date['name'].' ('.$date_str.')</option>';
                                            }
                                        $h.= '</select>';
                                        
                                        $h.= '<span class="description bottom">'.__('List with default marketing dates.','adn').'</span>';
                                        $h.= '</div>
                                    </div>
                                </div>';
                                echo $h;
                                ?>
                               
                            </div>
                        </div>
                    </div>
                    <!-- end .spr_column-inner -->


                </div>
                <!-- end .spr_column.spr_col-4 -->

                <div class="spr_column spr_col-8">
                    <div class="spr_column-inner">
                        <div class="spr_wrapper">
                            <div class="option_box">
                                <div class="info_header">
                                    <span class="nr">2</span>
                                    <span class="text"><?php _e('Run Campaign','adn'); ?></span>
                                    <input type="submit" value="<?php _e('Save Campaign','adn'); ?>" class="button-primary" name="save_campaign" style="width:auto;float:right;margin:8px;">
                                </div>

                                <?php
                                $h = '';
                                $h.= '<div class="sep_line" style="margin:0 0 15px 0;"><span><strong>'.__('Date Settings','adn').'</strong></span></div>';

                                // Show / Hide Months
                                $h.= '<div class="clear" style="border-bottom: solid 1px #efefef;margin-bottom: 20px;">
                                    <div class="input_container">
                                        <h3 class="title">'.__('For Months','adn').'</h3>
                                    </div>
                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">';
                                            
                                            $show_hide = array_key_exists('show_hide', $c['display_filter']['months']) ? $c['display_filter']['months']['show_hide'] : 0;
                                            $h.= '<label class="switch switch-slide small input_h ttip" title="'.__('Show/Hide.','adn').'">
                                                <input id="cb_month" class="switch-input" type="checkbox" name="display_filter[months][show_hide]" value="1" '.checked($show_hide,1,false).' />
                                                <span class="switch-label" data-on="'.__('Show','adn').'" data-off="'.__('Hide','adn').'"></span> 
                                                <span class="switch-handle"></span>
                                            </label>';

                                            $h.= '<span class="description bottom">'.__('Show/Hide the campaign during the selected months.','adn').'</span>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->

                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">
                                            <div class="custom_box option_inside_content">
                                                <h3 class="title"></h3>
                                                <div class="input_container_inner">';
                                                    
                                                    $h.= '<select id="df_month" name="display_filter[months][ids][]" data-placeholder="'.__('Select months', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
                                                        $h.= '<option value=""></option>';
                                                        
                                                        $months = array_key_exists('ids', $c['display_filter']['months']) ? $c['display_filter']['months']['ids'] : array();
                                                        
                                                        $all_months = array(
                                                            'jan','feb','mar','apr','may','jun','jul','aug','sep','okt','nov','dec'
                                                        );
                                        
                                                        foreach($all_months as $i => $month)
                                                        {
                                                            $selected = !empty($months) && is_array($months) ? in_array($month, $months) ? 'selected' : '' : '';
                                                            $h.= '<option value="'.$month.'" '.$selected.'>'.ADNI_Templates::months($month).'</option>';
                                                        }
                                                    $h.= '</select>';

                                                $h.= '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->';
                                $h.= '</div>';


                                // Show / Hide Days
                                $h.= '<div class="clear" style="border-bottom: solid 1px #efefef;margin-bottom: 20px;">
                                    <div class="input_container">
                                        <h3 class="title">'.__('For Days','adn').'</h3>
                                    </div>
                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">';
                                            
                                            $show_hide = array_key_exists('show_hide', $c['display_filter']['days']) ? $c['display_filter']['days']['show_hide'] : 0;
                                            $h.= '<label class="switch switch-slide small input_h ttip" title="'.__('Show/Hide.','adn').'">
                                                <input id="cb_day" class="switch-input" type="checkbox" name="display_filter[days][show_hide]" value="1" '.checked($show_hide,1,false).' />
                                                <span class="switch-label" data-on="'.__('Show','adn').'" data-off="'.__('Hide','adn').'"></span> 
                                                <span class="switch-handle"></span>
                                            </label>';

                                            $h.= '<span class="description bottom">'.__('Show/Hide the campaign during the selected days.','adn').'</span>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->

                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">
                                            <div class="custom_box option_inside_content">
                                                <h3 class="title"></h3>
                                                <div class="input_container_inner">';
                                                    
                                                    $h.= '<select id="df_day" name="display_filter[days][ids][]" data-placeholder="'.__('Select days', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
                                                        $h.= '<option value=""></option>';
                                                        
                                                        $days = array_key_exists('ids', $c['display_filter']['days']) ? $c['display_filter']['days']['ids'] : array();
                                                        
                                                        $all_days = array(
                                                            '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'
                                                        );
                                        
                                                        foreach($all_days as $i => $day)
                                                        {
                                                            $selected = !empty($days) && is_array($days) ? in_array($day, $days) ? 'selected' : '' : '';
                                                            $h.= '<option value="'.$day.'" '.$selected.'>'.$day.'</option>';
                                                        }
                                                    $h.= '</select>';

                                                $h.= '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->';
                                $h.= '</div>';


                                // Show / Hide weekdays
                                $h.= '<div class="clear" style="border-bottom: solid 1px #efefef;margin-bottom: 20px;">
                                    <div class="input_container">
                                        <h3 class="title">'.__('For Weekdays','adn').'</h3>
                                    </div>
                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">';
                                            
                                            $show_hide = array_key_exists('show_hide', $c['display_filter']['weekdays']) ? $c['display_filter']['weekdays']['show_hide'] : 0;
                                            $h.= '<label class="switch switch-slide small input_h ttip" title="'.__('Show/Hide.','adn').'">
                                                <input class="switch-input" type="checkbox" name="display_filter[weekdays][show_hide]" value="1" '.checked($show_hide,1,false).' />
                                                <span class="switch-label" data-on="'.__('Show','adn').'" data-off="'.__('Hide','adn').'"></span> 
                                                <span class="switch-handle"></span>
                                            </label>';

                                            $h.= '<span class="description bottom">'.__('Show/Hide the campaign during the selected weekdays.','adn').'</span>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->

                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">
                                            <div class="custom_box option_inside_content">
                                                <h3 class="title"></h3>
                                                <div class="input_container_inner">';
                                                    
                                                    $h.= '<select name="display_filter[weekdays][ids][]" data-placeholder="'.__('Select weekdays', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
                                                        $h.= '<option value=""></option>';
                                                        
                                                        $days = array_key_exists('ids', $c['display_filter']['weekdays']) ? $c['display_filter']['weekdays']['ids'] : array();
                                                        
                                                        $all_days = array(
                                                            'mon','tue','wed','thu','fri','sat','sun'
                                                        );
                                        
                                                        foreach($all_days as $i => $day)
                                                        {
                                                            $selected = !empty($days) && is_array($days) ? in_array($day, $days) ? 'selected' : '' : '';
                                                            $h.= '<option value="'.$day.'" '.$selected.'>'.ADNI_Templates::weekdays($day).'</option>';
                                                        }
                                                    $h.= '</select>';

                                                $h.= '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->';
                                $h.= '</div>';


                                // Show / Hide Time
                                $h.= '<div class="clear" style="border-bottom: solid 1px #efefef;margin-bottom: 20px;">
                                    <div class="input_container">
                                        <h3 class="title">'.__('For Time','adn').'</h3>
                                    </div>
                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">';
                                            
                                            $show_hide = array_key_exists('show_hide', $c['display_filter']['time']) ? $c['display_filter']['time']['show_hide'] : 0;
                                            $h.= '<label class="switch switch-slide small input_h ttip" title="'.__('Show/Hide.','adn').'">
                                                <input class="switch-input" type="checkbox" name="display_filter[time][show_hide]" value="1" '.checked($show_hide,1,false).' />
                                                <span class="switch-label" data-on="'.__('Show','adn').'" data-off="'.__('Hide','adn').'"></span> 
                                                <span class="switch-handle"></span>
                                            </label>';

                                            $h.= '<span class="description bottom">'.__('Show/Hide the campaign during the selected time.','adn').'</span>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->

                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">
                                            <div class="custom_box option_inside_content">
                                                <h3 class="title"></h3>
                                                <div class="input_container_inner">';
                                                    
                                                    $h.= '<select name="display_filter[time][ids][]" data-placeholder="'.__('Select Time', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
                                                        $h.= '<option value=""></option>';
                                                        
                                                        $times = array_key_exists('ids', $c['display_filter']['time']) ? $c['display_filter']['time']['ids'] : array();
                                                        
                                                        $all_times = array(
                                                            '0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23'
                                                        );
                                        
                                                        foreach($all_times as $i => $time)
                                                        {
                                                            $selected = !empty($times) && is_array($times) ? in_array($time, $times) ? 'selected' : '' : '';
                                                            $h.= '<option value="'.$time.'" '.$selected.'>'.ADNI_Templates::time($time).'</option>';
                                                        }
                                                    $h.= '</select>';

                                                    $h.= '<span class="description bottom">'.sprintf(__('Current Time: %s.','adn'), date_i18n( 'l F j, Y - g:i A', current_time( 'timestamp' ) )).'</span>';

                                                $h.= '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->';
                                $h.= '</div>';


                                // Show / Hide Years
                                $h.= '<div class="clear" style="border-bottom: solid 1px #efefef;margin-bottom: 20px;">
                                    <div class="input_container">
                                        <h3 class="title">'.__('For Years','adn').'</h3>
                                    </div>
                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">';
                                            
                                            $show_hide = array_key_exists('show_hide', $c['display_filter']['years']) ? $c['display_filter']['years']['show_hide'] : 0;
                                            $h.= '<label id="cb_year" class="switch switch-slide small input_h ttip" title="'.__('Show/Hide.','adn').'">
                                                <input class="switch-input" type="checkbox" name="display_filter[years][show_hide]" value="1" '.checked($show_hide,1,false).' />
                                                <span class="switch-label" data-on="'.__('Show','adn').'" data-off="'.__('Hide','adn').'"></span> 
                                                <span class="switch-handle"></span>
                                            </label>';

                                            $h.= '<span class="description bottom">'.__('Show/Hide the campaign during the selected years.','adn').'</span>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->

                                    <div class="spr_column spr_col-6">
                                        <div class="input_container">
                                            <div class="custom_box option_inside_content">
                                                <h3 class="title"></h3>
                                                <div class="input_container_inner">';
                                                    
                                                    $h.= '<select id="df_year" name="display_filter[years][ids][]" data-placeholder="'.__('Select years', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
                                                        $h.= '<option value=""></option>';
                                                        
                                                        $years = array_key_exists('ids', $c['display_filter']['years']) ? $c['display_filter']['years']['ids'] : array();
                                                        
                                                        $all_years = array();
                                                        $cur_year = date('Y');
                                                        for($i = 0; $i <= 10; $i++)
                                                        {
                                                            $all_years[] = $cur_year+$i;
                                                        }
                                                        
                                                        foreach($all_years as $i => $year)
                                                        {
                                                            $selected = !empty($years) && is_array($years) ? in_array($year, $years) ? 'selected' : '' : '';
                                                            $h.= '<option value="'.$year.'" '.$selected.'>'.$year.'</option>';
                                                        }
                                                    $h.= '</select>';

                                                $h.= '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end .spr_column -->';
                                $h.= '</div>';


                                $h.= '<div class="clear device_filter_container" style="margin-top: 40px;">
                                    <div class="sep_line" style="margin:0 0 15px 0;"><span><strong>'.__('Country Filters','adn').'</strong></span></div>
                                    <div class="clear">';
                                        $h.= ADNI_Templates::country_options($c, array('desc' => __('Show or Hide the campaign for selected countries.','adn')));		
                                    $h.= '</div>
                                </div>';

                                echo $h;
                                ?>

                                
                                
                            </div>
                            <!-- end.option_box -->
                        </div>
                    </div>
                </div>
                <!-- end .spr_column.spr_col-8 -->

            </div>
            <!-- end .spr_row -->

        </form>

    </div>
    <!-- end .wrap -->
</div>
<!-- end .adning_cont.adning_add_new_campaign -->


<script>
jQuery(document).ready(function($) {

    Adning_global.activate_tooltips($('.adning_dashboard'));


    // Marketing Calendar 
    // More info in ADNI_Templates::marketing_dates()
    // Select option if dates match
    var df_day = $('#df_day').val() !== null ? $('#df_day').val() : 'x',
        df_month = $('#df_month').val() !== null ? $('#df_month').val() : 'x',
        df_year = $('#df_year').val() !== null ? $('#df_year').val() : 'x',
        df_month_str = df_month,
        df_day_str = df_day,
        df_year_str = df_year;
    
    if( $.isArray(df_month) ){
        var i = 0;
        df_month_str = '';
        $.each( df_month, function( key, value ) {
            df_month_str+= i !== 0 ? '_'+value : value;
            i++;
        });
    }
    if( $.isArray(df_day) ){
        var i = 0;
        df_day_str = '';
        $.each( df_day, function( key, value ) {
            df_day_str+= i !== 0 ? '_'+value : value;
            i++;
        });
    }
    if( $.isArray(df_year) ){
        var i = 0;
        df_year_str = '';
        $.each( df_year, function( key, value ) {
            df_year_str+= i !== 0 ? '_'+value : value;
            i++;
        });
    }

    var campaign_date_id = df_day_str+'-'+df_month_str+'-'+df_year_str;
    
    $('#marketing_calendar option[data-did='+campaign_date_id+']').attr('selected', true);
    

    // On change
    $('#marketing_calendar').on('change', function(){
        var val = $(this).val(),
            date_id = $(this).find(':selected').data('did'),
            arr = date_id.split('-');
        
        $("#df_day option:selected").removeAttr("selected");
        $("#df_month option:selected").removeAttr("selected");
        $("#df_year option:selected").removeAttr("selected");

        if( arr[0] !== 'x'){
            if (arr[0].indexOf('_') !== -1){
                $.each( arr[0].split('_'), function( k, v ) {
                    $('#df_day option[value='+v+']').attr('selected', true);
                });
            }else{
                $('#df_day option[value='+arr[0]+']').attr('selected', true);
            }
            $('#cb_day').prop( "checked", true );
        }
        if( arr[1] !== 'x'){
            if (arr[1].indexOf('_') !== -1){
                $.each( arr[1].split('_'), function( k, v ) {
                    $('#df_month option[value='+v+']').attr('selected', true);
                });
            }else{
                $('#df_month option[value='+arr[1]+']').attr('selected', true);
            }
            $('#cb_month').prop( "checked", true );
        }
        if( arr[2] !== 'x'){
            if (arr[2].indexOf('_') !== -1){
                $.each( arr[2].split('_'), function( k, v ) {
                    $('#df_year option[value='+v+']').attr('selected', true);
                });
            }else{
                $('#df_year option[value='+arr[2]+']').attr('selected', true);
            }
            $('#cb_year').prop( "checked", true );
        }
        
        $('.chosen-select').trigger("chosen:updated");
        //console.log(arr);
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