<?php
$h = '';
if( !is_user_logged_in() )
{
    $h.= '<div style="margin-top:50px;text-align:center;">'.__('Please login to access this area.','adn').'</div>';
    return;
}


$id = isset($_GET['id']) ? $_GET['id'] : 0;
$id = !$id && isset($_POST['post_id']) ? $_POST['post_id'] : $id;
$is_frontend = isset($is_frontend) ? $is_frontend : 0;
$user_id = get_current_user_id();

if( !current_user_can(ADNI_BANNERS_ROLE) && empty($id) )
{
    // If user is purchasing a banner it's ok
    // If not EXIT!
    if( !isset($_GET['oid']) && !isset($_GET['slid']) )
    {
        $h.= '<div style="margin-top:50px;text-align:center;">'.__('Sorry you cannot access this area.','adn').'</div>';
        return;
    }
}


if( isset($_GET['reset_stats']) && !empty($_GET['reset_stats']))
{
    ADNI_Main::reset_stats($id, 'id_1');
}

//echo ADNI_Filters::get_country( ADNI_Main::get_visitor_ip() );

// Create draft post ( just to get a banner ID )
if( !$id )
{
    $id = ADNI_CPT::add_update_post(
        apply_filters('ADNI_new_banner_args', array(
            'post_type' => ADNI_CPT::$banner_cpt,
            'post_id' => 0,
            'post_title' => '',
            'responsive' => 1,
            'df_show_desktop' => 1,
            'df_show_tablet' => 1,
            'df_show_mobile' => 1
        )),
        'draft'
    );

    // Add new ID to url
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $urlf = strpos($current_url, '?') !== false ? '&' : '?';
    $h.= '<script>var url = "'.$current_url.$urlf.'id='.$id.'";document.location = url;</script>';
    //$h.= '<script>var url = document.location.href+"&id='.$id.'";document.location = url;</script>';
}


$banner_post = array();
//$default_banner_content = '<div style="display:table;text-align:center;height:100%;width:100%;"><span style="display: table-cell;vertical-align:middle;">Advertise Here</span></div>';
$default_banner_content = '';

/**
 * POST
*/
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['save_banner']))
	{
        /*
		 * Filter: 'ADNI_save_banner' - Allow other plugins to adjust $_POST variable.
        */
        $post = apply_filters('ADNI_save_banner', $_POST);
        

        //echo '<pre>'.print_r($post,true).'</pre>';
        $id = ADNI_CPT::add_update_post($post);
	}
}

$set_arr = ADNI_Main::settings();
$settings = $set_arr['settings'];


/*
 * Load Post data or default values
*/
$banner_post = ADNI_CPT::load_post($id, array('post_type' => ADNI_CPT::$banner_cpt, 'filter' => 0));

if( empty( $banner_post['post'] ) || !current_user_can(ADNI_BANNERS_ROLE) && $user_id != $banner_post['post']->post_author)
{
    $h.= '<div style="margin-top:50px;text-align:center;">'.__('Sorry, This banner does not exists.','adn').'</div>';
    return $h;
}

//$b = apply_filters('ADNI_single_banner_args', $banner_post['args'], $id, $_GET);
$b = $banner_post['args'];
//echo '<pre>'.print_r($banner_post,true).'</pre>';
//echo '<pre>'.print_r($b,true).'</pre>';
//print_r( get_post_meta($id, '_adning_size') );


/**
 * Check if user has access to this banner
*/
if( !empty( $banner_post['post'] ))
{
    ADNI_CPT::user_has_access(array(
        'id' => $id,
        'author' => $banner_post['post']->post_author,
        'post_type' => ADNI_CPT::$banner_cpt
    ));
}



$h.= '<div class="adning_cont adning_add_new_banner">';
    $h.= '<div class="wrap">';
    
    	//<!-- Wordpress Messages -->
        $h.= '<h2 class="messages-position"></h2>';
        
        $h.= ADNI_Templates::admin_header();

        
        /*
         * Filter: 'adning_single_banner_notice' - Allow other plugins to add notices to the banner page.
        */
        $h.= apply_filters('adning_single_banner_notice', $banner_post, $is_frontend);
        
        
        $h.= '<form action="" method="post" enctype="multipart/form-data">';
            $h.= '<input type="hidden" value='.$id.' name="post_id">';
            $h.= '<input type="hidden" value="'.ADNI_CPT::$banner_cpt.'" name="post_type">';
            
            $h.= '<div class="spr_row">';
        	    $h.= '<div class="spr_column spr_col-4">';
                    $h.= '<div class="spr_column-inner left_column">';
                        $h.= '<div class="spr_wrapper">';
                            $h.= '<div class="option_box">';
                                $h.= '<div class="info_header">';
                                    $h.= '<span class="nr">1</span>';
                            	    $h.= '<span class="text">'.__('Banner Settings','adn').'</span>';
                                $h.= '</div>';
                                $h.= '<div class="input_container">';
                                    $h.= '<h3 class="title">'.__('Title','adn').'</h3>';

                                    $val = !empty($banner_post['post']) ? $banner_post['post']->post_title : '';
                                    $h.= '<div class="input_container_inner">
                                            <input 
                                            type="text" 
                                            class="" 
                                            name="title" 
                                            value="'.$val.'" 
                                            placeholder="'.__('Banner Title','adn').'">
                                        <i class="input_icon fa fa-pencil" aria-hidden="true"></i>
                                    </div>';
                                    $h.= '<span class="description bottom">'.__('Add a banner title.','adn').'</span>';
                                $h.= '</div>';
                                //<!-- end .input_container -->

                            
                                if( current_user_can(ADNI_BANNERS_ROLE) )
                                {
                                    global $current_user;
                                    $advertiser_id = !empty($banner_post['post']) ? $banner_post['post']->post_author : $current_user->ID;
                                    $all_users = get_users();
                                    
                                    $h.= '<div class="input_container">';
                                        $h.= '<h3 class="title">'.__('Advertiser','adn').'</h3>';
                                        $h.= '<select name="advertiser" data-placeholder="Select Advertiser" class="chosen-select">';
                                            
                                            foreach($all_users as $user)
                                            {
                                                $selected = $user->ID == $advertiser_id ? ' selected' : '';
                                                $h.= '<option value="'.$user->ID.'"'.$selected.'>'.$user->display_name.' (#'.$user->ID.')</option>';
                                            }
                                           
                                        $h.= '</select>';
                                            
                                        $h.= '<span class="description bottom">'.__('Assign this banner to a specific advertiser.','adn').'</span>';
                                    $h.= '</div>';
                                    //<!-- end .input_container -->
                                }
                                

                                $h.= '<div class="input_container">';
                                    $h.= '<h3 class="title">'.__('Status','adn').'</h3>';
                                        $h.= '<div class="input_container_inner">';
                                           
                                            if( current_user_can(ADNI_BANNERS_ROLE) )
                                            {
                                                $h.= '<select name="status" class="">';
                                                    $h.= '<option value="active" '.selected( $b['status'], 'active', false).'>'.__('Active','adn').'</option>';
                                                    $h.= '<option value="expired" '.selected( $b['status'], 'expired', false).'>'.__('Expired','adn').'</option>';
                                                    $h.= '<option value="draft" '.selected( $b['status'], 'draft', false).'>'.__('Draft','adn').'</option>';
                                                    $h.= '<option value="review" '.selected( $b['status'], 'review', false).'>'.__('Pending Review','adn').'</option>';
                                                    $h.= '<option value="on-hold" '.selected( $b['status'], 'on-hold', false).'>'.__('On Hold','adn').'</option>';
                                                $h.= '</select>';
                                            }
                                            else
                                            {
                                                $h.= '<div style="font-size: 18px;">'.$b['status'].'</div>';
                                            }
                                           
                                        $h.= '</div>';
                                    $h.= '<span class="description bottom">'.__('Banner status.','adn').'</span>';
                                $h.= '</div>';
                                //<!-- end .input_container -->
                             
                                $h.= '<div class="input_container">';
                                    $h.= '<h3 class="title">'.__('URL','adn').'</h3>';
                                        $h.= '<div class="input_container_inner">';
                                        $h.= '<input 
                                            type="text" 
                                            class="" 
                                            name="banner_url" 
                                            value="'.$b['banner_url'].'" 
                                            placeholder="'.__('http://','adn').'">
                                        <i class="input_icon fa fa-link" aria-hidden="true"></i>';
                                    $h.= '</div>';
                                    $h.= '<span class="description bottom">'.__('Add a banner link (URL).','adn').'</span>';
                                $h.= '</div>';
                                //<!-- end .input_container -->
                             
                                $h.= '<div class="input_container">';
                                    $h.= '<h3 class="title">'.__('Target','adn').'</h3>';
                                        $h.= '<div class="input_container_inner">';
                                            $h.= '<select name="banner_target" class="">';
                                                $h.= '<option value="_blank" '.selected( $b['banner_target'], '_blank', false).'>'.__('_blank, Load in a new window.','adn').'</option>';
                                                $h.= '<option value="_self" '.selected( $b['banner_target'], '_self', false).'>'.__('_self, Load in the same frame as it was clicked.','adn').'</option>';
                                                $h.= '<option value="_parent" '.selected( $b['banner_target'], '_parent', false).'>'.__('_parent, Load in the parent frameset.','adn').'</option>';
                                                $h.= '<option value="_top" '.selected( $b['banner_target'], '_top', false).'>'.__('_top, Load in the full body of the window.','adn').'</option>';
                                            $h.= '</select>';
                                        $h.= '</div>';
                                    $h.= '<span class="description bottom">'.__('Banner link target.','adn').'</span>';
                                $h.= '</div>';
                                //<!-- end .input_container -->
                             
                            
                                $h.= '<div class="spr_row">';
                                    if( current_user_can(ADNI_BANNERS_ROLE) )
                                    {
                                        $h.= ADNI_Templates::spr_column(array(
                                            'col' => 'spr_col-6',
                                            'title' => __('Link Masking','adn'),
                                            'desc' => '',
                                            'content' => ADNI_Templates::switch_btn(array(
                                                'name' => 'banner_link_masking',
                                                'tooltip' => __('Turn Off link masking to link directly to the raw banner url, When turned off its not possible to save statistics for this banner.','adn'),
                                                'checked' => $b['banner_link_masking'],
                                                'value' => 1,
                                                'hidden_input' => 1,
                                                'chk-on' => __('On','adn'),
                                                'chk-off' => __('Off','adn'),
                                                'chk-high' => 0
                                            ))
                                        ));
                                    }
                                        
                                    $h.= ADNI_Templates::spr_column(array(
                                        'col' => 'spr_col-6',
                                        'title' => __('No Follow','adn'),
                                        'desc' => '',
                                        'content' => ADNI_Templates::switch_btn(array(
                                            'name' => 'banner_no_follow',
                                            'tooltip' => __('Add no Follow to banner link.','adn'),
                                            'checked' => $b['banner_no_follow'],
                                            'value' => 1,
                                            'hidden_input' => 1,
                                            'chk-on' => __('On','adn'),
                                            'chk-off' => __('Off','adn'),
                                            'chk-high' => 0
                                        ))
                                    ));
                                    
                                $h.= '</div>';
                                //<!-- end .spr_row -->
                             
                             
                             
                            $h.= '<div class="input_container">';
                                $h.= '<div class="input_container_inner">';
                                    $h.= '<div class="sep_line" style="margin:10px 0 20px 0;"><span><strong>'.__('Save','adn').'</strong></span></div>';
                                    $h.= '<input type="submit" value="'.__('Save Banner','adn').'" class="button-primary" name="save_banner" style="width: auto;">';
                                $h.= '</div>';
                                $h.= '<span class="description bottom"></span>';
                            $h.= '</div>';
                            //<!-- end .input_container -->
                                         
                        $h.= '</div>';
                        //<!-- end .option_box -->


                        
                        /**
                         * BANNER STATS
                        */
                        $h.= ADNI_Templates::stats_settings_tpl(array('id' => $id, 'frontend' => $is_frontend), $b);
                        
                        
                        /**
                         * CAMPAIGNS
                        */
                        $h.= current_user_can(ADNI_BANNERS_ROLE) ? ADNI_Templates::link_campaign_tpl($b) : '';


                        /**
                         * ADZONES
                        */
                        $h.= current_user_can(ADNI_BANNERS_ROLE) ? ADNI_Templates::link_adzone_tpl($b) : '';


                        /**
                         * ALIGNMENT SETTINGS
                        */
                        $h.= current_user_can(ADNI_BANNERS_ROLE) ? ADNI_Templates::alignment_settings_tpl($b) : '';
                        

                        /**
                         * BORDER SETTINGS
                        */
                        $h.= current_user_can(ADNI_BANNERS_ROLE) ? ADNI_Templates::border_settings_tpl($b) : '';
                        

                        /**
                         * EXPORT BANNER
                        */
                        $h.= ADNI_Templates::export_tpl($banner_post);
                        
                    $h.= '</div>
                </div>
            </div>';
            //<!-- end .spr_column -->
            
            
            $h.= '<div class="spr_column spr_col-8">';
                $h.= '<div class="spr_column-inner">';
                    $h.= '<div class="spr_wrapper">';
                        $h.= '<div class="option_box">';
                            $h.= '<div class="info_header">';
                                $h.= '<span class="nr">2</span>';
                                $h.= '<span class="text">'.__('Banner','adn').'</span>';
                                $h.= '<span class="fa tog ttip" title="'.__('Toggle box','adn').'"></span>';
                                $h.= '<input type="submit" value="'.__('Save Banner','adn').'" class="button-primary" name="save_banner" style="width:auto;float:right;margin:8px;">';
                            $h.= '</div>';
                            
                            $h.= '<div class="settings_box_content">';

                                if( current_user_can(ADNI_BANNERS_ROLE) )
                                {
                                    $h.= '<div class="sep_line" style="margin:0 0 15px 0;"><span><strong>'.__('Sizing','adn').'</strong></span></div>';
                                    $h.= '<div class="spr_column spr_col-4">';
                                        $h.= '<div class="spr_column-inner left_column">';
                                            $h.= '<div class="spr_wrapper">';
                                                $h.= '<div class="input_container">';
                                                    $h.= '<h3 class="title"></h3>';
                                                    $h.= '<div class="input_container_inner">';
                                                        $h.= '<select id="ADNI_size" name="size" class="">';
                                                            foreach(ADNI_Main::banner_sizes() as $size)
                                                            {
                                                                $h.= '<option value="'.$size['size'].'" '.selected( $b['size'], $size['size'], false).'>'.$size['name'].' ('.$size['size'].')</option>';
                                                            }
                                                            $h.= '<option value="custom" '.selected( $b['size'], 'custom', false).'>'.__('Custom','adn').'</option>';
                                                        $h.= '</select>';
                                                    $h.= '</div>';
                                                    $h.= '<span class="description bottom">'.__('Select one of the common banner sizes.','adn').'</span>';
                                                $h.= '</div>';
                                                //<!-- end .input_container -->
                                            $h.= '</div>';
                                        $h.= '</div>';
                                    $h.= '</div>';
                                    //<!-- end .spr_column -->
                                    
                                    $h.= ADNI_Templates::spr_column(array(
                                        'col' => 'spr_col-2',
                                        'title' => '',
                                        'desc' => __('Responsive','adn'),
                                        'content' => ADNI_Templates::switch_btn(array(
                                            'name' => 'responsive',
                                            'id' => 'ADNI_responsive',
                                            'tooltip' => __('Responsive banner.','adn'),
                                            'checked' => $b['responsive'],
                                            'value' => 1,
                                            'hidden_input' => 1,
                                            'chk-on' => __('On','adn'),
                                            'chk-off' => __('Off','adn'),
                                            'chk-high' => 1
                                        ))
                                    ));
                                
                                    
                                    $h.= '<div class="spr_column spr_col-6">';
                                        $h.= '<div class="spr_column-inner">';
                                            $h.= '<div class="spr_wrapper">';

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
                                                            'value' => $b['size_w'],
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
                                                            'value' => $b['size_h'],
                                                            'placeholder' => '',
                                                            'icon' => 'arrows-v',
                                                            'show_icon' => 1
                                                        ))
                                                ));
                                                
                                            $h.= '</div>
                                        </div>
                                    </div>';
                                    //<!-- end .spr_column -->
                                }



                                // <!-- BANNER CODE -->
                                $h.= '<div class="spr_column">';
                                    $h.= '<div class="spr_column-inner">';
                                        $h.= '<div class="spr_wrapper">';
                                        
                                            $h.= '<div class="sep_line" style="margin:0 0 5px 0;"><span><strong>'.__('Preview','adn').'</strong></span></div>';
                                            $h.= '<div class="banner_holder clear" style="padding:20px;">';
                                                $h.= '<div class="banner_notice"></div>';
                                            
                                                $h.= ADNI_Templates::banner_tpl($id, array('add_url' => 0, 'filter' => 0, 'stats' => 0));
                                            $h.= '</div>';
                                            // <!-- end .banner_holder -->
                                            
                                            $h.= '<div class="sep_line" style="margin:0 0 25px 0;"><span><strong>'.__('Content','adn').'</strong></span></div>';
                                            
                                            /*$h.= '<div class="spr_column">';
                                                $h.= '<div class="spr_column-inner">';
                                                    $h.= '<div class="spr_wrapper">';
                                                        $h.= '<div class="input_container">';
                                                            $h.= '<div id="HTML5Uploader" class="box" style="border:dashed 1px #d7d7d7;border-radius:3px;padding:15px 5px;background: #FFF;" method="post" action="'.ADNI_AJAXURL.'" enctype="multipart/form-data"></div>';
                                                            $h.= '<span class="description bottom">'.__('Upload banner content.','adn').'</span>';
                                                        $h.= '</div>';
                                                    $h.= '</div>';
                                                $h.= '</div>';
                                            $h.= '</div>';
                                            //<!-- end .spr_column -->*/
                                            
                                            $h.= ADNI_Templates::spr_column(array(
                                                'col' => 'spr_col',
                                                'title' => '',
                                                'desc' => __('Upload banner content.','adn'),
                                                'content' => ADNI_Templates::file_upload(array(
                                                    'class' => 'HTML5Uploader'
                                                ))
                                            ));
                                            
                                            

                                            // IMGMCE Button
                                            if( class_exists('ImgMCE'))
                                            {
                                                $imgmce_logo = IMC_ASSETS_URL.'/images/logo_20.png';
                                                $h.= '<div class="spr_column spr_col-3 left_column">';
                                                    $h.= '<div class="spr_column-inner">';
                                                        $h.= '<div class="spr_wrapper">';
                                                            $h.= '<div class="input_container">';
                                                                $h.= '<h3 class="title"></h3>';
                                                                $h.= '<div class="input_container_inner">';
                                                                    $h.= '<button class="open_imgmce_button button" type="button" style="height:30px;background-color:#fefe7e;border-color: #eee265;box-shadow: 0 1px #CCC364;color: #b6ac24;">';
                                                                        $h.= '<img style="position:absolute;margin-top: 4px;" src="'.$imgmce_logo.'">';
                                                                        $h.= '<span style="margin-left:20px;">'.__('imgMCE Editor', 'adn').'</span>';
                                                                    $h.= '</button>';
                                                                $h.= '</div>';
                                                                $h.= '<span class="description bottom">'.__('Open imgMCE editor.','adn').'</span>';
                                                            $h.= '</div>';
                                                            //<!-- end .input_container -->
                                                        $h.= '</div>';
                                                    $h.= '</div>';
                                                $h.= '</div>';
                                                //<!-- end .spr_column -->
                                            }
                                            
                                            if( !$is_frontend )
                                            {
                                                $h.= '<div class="spr_column spr_col-3 left_column">';
                                                    $h.= '<div class="spr_column-inner">';
                                                        $h.= '<div class="spr_wrapper">';
                                                            $h.= '<div class="input_container">';
                                                                $h.= '<h3 class="title"></h3>';
                                                                $h.= '<div class="input_container_inner">';
                                                                    $h.= '<button class="upload_image_button button" type="button" style="height: 30px;">'.__('Wordpress Media', 'adn').'</button>';
                                                                $h.= '</div>';
                                                                $h.= '<span class="description bottom">'.__('Upload banner image.','adn').'</span>';
                                                            $h.= '</div>';
                                                            //<!-- end .input_container -->
                                                        $h.= '</div>';
                                                    $h.= '</div>';
                                                $h.= '</div>';
                                                //<!-- end .spr_column -->
                                            }


                                            if( current_user_can(ADNI_BANNERS_ROLE) )
                                            {
                                                $h.= '<div class="spr_column spr_col-3 left_column">';
                                                    $h.= '<div class="spr_column-inner">';
                                                        $h.= '<div class="spr_wrapper">';
                                                            $h.= '<div class="input_container">';
                                                                $h.= '<h3 class="title"></h3>';
                                                                $h.= '<div class="input_container_inner">';
                                                                    $h.= '<button class="adsense_btn button" type="button" style="height: 30px;">';
                                                                        $h.= '<svg style="height: 13px;" viewBox="0 0 256 252" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid"><defs><linearGradient x1=".041%" y1="50.091%" x2="100.078%" y2="50.091%" id="a"><stop stop-color="#4284F0" offset="0%"/><stop stop-color="#4487F4" offset="100%"/></linearGradient><linearGradient x1="28.448%" y1="81.561%" x2="55.474%" y2="42.237%" id="b"><stop stop-color="#F5B406" offset="0%"/><stop stop-color="#F4B913" offset="100%"/></linearGradient></defs><path d="M254.2 154.4L209.4 252H102.3l58.6-126h75c14.6 0 24.3 15.1 18.3 28.4z" fill="#437DE6"/><path d="M235.9 127.8c6.3 0 12 3.1 15.4 8.4 3.4 5.3 3.8 11.8 1.2 17.5l-44.2 96.6H107.2l56.9-122.5h71.8zm0-1.8H163l-58.6 126h105l44.7-97.6c6.1-13.3-3.6-28.4-18.2-28.4z" fill="#196CEA"/><path d="M62.1 1.8s56.2 61 69.4 67.3L149.8 29c6.4-12.7-1.8-27.5-17.1-27.3l-70.6.1z" fill="url(#a)" transform="translate(102 126)"/><g><path d="M112.9 10.9L0 252h104.4L165 121.7 221.6 0H130c-7.3 0-14 4.2-17.1 10.9z" fill="url(#b)"/><path d="M218.8 1.8L163.4 121l-60.1 129.3H2.8L114.5 11.6c2.8-6 8.9-9.9 15.5-9.9h88.8v.1zm2.8-1.8H130c-7.3 0-14 4.2-17.1 10.9L0 252h104.4L165 121.7 221.6 0z" fill="#F3AA00"/></g></svg>';
                                                                        $h.= __('Google AdSense', 'adn');
                                                                    $h.= '</button>';
                                                                $h.= '</div>';
                                                                $h.= '<span class="description bottom">'.__('Adsense banner settings.','adn').'</span>';
                                                            $h.= '</div>';
                                                            //<!-- end .input_container -->
                                                        $h.= '</div>
                                                    </div>
                                                </div>';
                                                //<!-- end .spr_column -->


                                                // Link to WP POST EDITOR
                                                if( !$is_frontend )
                                                {
                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col-3',
                                                        'title' => '',
                                                        'desc' => __('Wordpress post editor','adn'),
                                                        'content' => '<a href="post.php?post='.$id.'&action=edit" class="button-secondary" target="_blank">'.__('Wordpress Editor','adn').'</a>'
                                                    ));
                                                }
                                            }
                                            
                                            
                                            
                                            $adsense_pub_id = is_array($b['adsense_settings']) && array_key_exists('pub_id', $b['adsense_settings']) && !empty($b['adsense_settings']['pub_id']) ? $b['adsense_settings']['pub_id'] : $settings['adsense_pubid'];
                                            $adsense_slot_id = is_array($b['adsense_settings']) && array_key_exists('slot_id', $b['adsense_settings']) ? $b['adsense_settings']['slot_id'] : '';
                                            $show_adsense_settings = !empty($adsense_slot_id) ? '' : ' style="display:none;"';
                                            
                                            $h.= '<div class="spr_column google_adsense_container"'.$show_adsense_settings.'>';
                                                $h.= '<div class="spr_column-inner">';
                                                    $h.= '<div class="spr_wrapper">';
                                                        $h.= '<div class="input_container clear">';
                                                            $h.= '<h3 class="title">'.__('Google AdSense','adn').'</h3>';

                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-4',
                                                                'title' => '',
                                                                'desc' => __('AdSense pub ID.','adn'),
                                                                'content' => ADNI_Templates::inpt_cont(array(
                                                                        'type' => 'text',
                                                                        'width' => '100%',
                                                                        'class' => 'adsense_set adsense_pub_id',
                                                                        'name' => 'adsense_pubid',
                                                                        'value' => $adsense_pub_id,
                                                                        'placeholder' => 'pub-xxxxxxx',
                                                                        'icon' => 'pencil',
                                                                        'show_icon' => 1
                                                                    ))
                                                            ));
                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-4',
                                                                'title' => '',
                                                                'desc' => __('AdSense ad slot ID.','adn'),
                                                                'content' => ADNI_Templates::inpt_cont(array(
                                                                        'type' => 'text',
                                                                        'width' => '100%',
                                                                        'class' => 'adsense_set adsense_slot_id',
                                                                        'name' => 'adsense_slotid',
                                                                        'value' => $adsense_slot_id,
                                                                        'placeholder' => 'xxxxxxx',
                                                                        'icon' => 'pencil',
                                                                        'show_icon' => 1
                                                                    ))
                                                            ));
                                                            
                                                            $adsense_type = is_array($b['adsense_settings']) && array_key_exists('type', $b['adsense_settings']) ? $b['adsense_settings']['type'] : '';
                                                            
                                                            $h.= '<div class="spr_column spr_col-4"><div class="spr_column-inner"><div class="input_container_inner">';
                                                                $h.= '<select name="adsense_type" class="adsense_set adsense_type">';
                                                                    $h.= '<option value="" '.selected($adsense_type, '', false).'>'.__('-- Select --','adn').'</option>';
                                                                    $h.= '<option value="normal" '.selected($adsense_type, 'normal', false).'>'.__('Normal','adn').'</option>';
                                                                    $h.= '<option value="responsive" '.selected($adsense_type, 'responsive',false).'>'.__('Responsive','adn').'</option>';
                                                                    $h.= '<option value="matched-content" '.selected($adsense_type, 'matched-content', false).'>'.__('Responsive (Matched Content)','adn').'</option>';
                                                                    $h.= '<option value="link" '.selected($adsense_type, 'link', false).'>'.__('Link ads','adn').'</option>';
                                                                    $h.= '<option value="link-responsive" '.selected($adsense_type, 'link-responsive', false).'>'.__('Link ads (Responsive)','adn').'</option>';
                                                                    $h.= '<option value="in-article" '.selected($adsense_type, 'in-article', false).'>'.__('InArticle','adn').'</option>';
                                                                    $h.= '<option value="in-feed" '.selected($adsense_type, 'in-feed', false).'>'.__('InFeed','adn').'</option>';
                                                                $h.= '</select>';
                                                                $h.= '<span class="description bottom">'.__('AdSense banner type','adn').'</span>';
                                                            $h.= '</div></div></div>';
                                                            //<!-- end .spr_column -->

                                                        
                                                            $h.= '<span class="description bottom"></span>';
                                                        $h.= '</div>';
                                                        //<!-- end .input_container -->
                                                    $h.= '</div>';
                                                $h.= '</div>';
                                            $h.= '</div>';
                                            //<!-- end .spr_column -->
                                            
                                            
                                            $h.= '<div class="spr_column">';
                                                $h.= '<div class="spr_column-inner">';
                                                    $h.= '<div class="spr_wrapper">';
                                                        $h.= '<div class="input_container">';
                                                            $h.= '<h3 class="title"></h3>';
                                                            $h.= '<div class="input_container_inner">';
                                                                $h.= '<textarea id="banner_content" class="code_editor" name="banner_content" data-lang="htmlmixed" style="min-height:200px;font-size: 13px;">'.$b['banner_content'].'</textarea>';
                                                            $h.= '</div>';
                                                            $h.= '<span class="description bottom">'.__('Banner HTML content.','adn').'</span>';
                                                        $h.= '</div>';
                                                        // <!-- end .input_container -->
                                                    $h.= '</div>';
                                                $h.= '</div>';
                                            $h.= '</div>';
                                            //<!-- end .spr_column -->

                                            $h.= ADNI_Templates::spr_column(array(
                                                'col' => 'spr_col-3',
                                                'title' => __('Live Preview','adn'),
                                                'desc' => '',
                                                'content' => ADNI_Templates::checkbox(array(
                                                    'id' => 'dont_render_preview_code',
                                                    'tooltip' => __('When using javascript banners you may need to turn of live preview code rendering.','adn'),
                                                    'checked' => 1,
                                                    'chk-on' => __('Yes','adn'),
                                                    'chk-off' => __('No','adn'),
                                                    'chk-high' => 0
                                                ))
                                            ));

                                            $h.= ADNI_Templates::spr_column(array(
                                                'col' => 'spr_col-3',
                                                'title' => '',
                                                'desc' => __('Scale banner content.','adn'),
                                                'content' => ADNI_Templates::switch_btn(array(
                                                    'name' => 'banner_scale',
                                                    'id' => 'ADNI_scale',
                                                    'tooltip' => __('Scale banner content to match resized banner container.','adn'),
                                                    'checked' => $b['banner_scale'],
                                                    'value' => 1,
                                                    'hidden_input' => 1,
                                                    'chk-on' => __('On','adn'),
                                                    'chk-off' => __('Off','adn'),
                                                    'chk-high' => 0
                                                ))
                                            ));
                                        
                                        $h.= '</div>
                                    </div>
                                </div>';
                                //<!-- end .spr_column -->
                                
                                $h.= ADNI_Templates::spr_column(array(
                                    'col' => 'spr_col',
                                    'title' => '',
                                    'desc' => '',
                                    'content' => '<input type="submit" value="'.__('Save Banner','adn').'" class="button-primary" name="save_banner">'
                                ));

                            $h.= '</div>';
                            // end .settings_box_content
                            
                            
                        $h.= '</div>';
                        //<!-- end .option_box -->
                    $h.= '</div>
                </div>';

                

                
                
                            
                            


                $h.= '<div class="spr_column">';
                    $h.= current_user_can(ADNI_BANNERS_ROLE) ? ADNI_Templates::auto_positioning_template($id, $banner_post) : '';
                    $h.= current_user_can(ADNI_BANNERS_ROLE) ? ADNI_templates::display_filters_tpl($banner_post, $settings) : '';
                $h.= '</div>';
                //<!-- end .spr_column -->

                $h.= current_user_can(ADNI_BANNERS_ROLE) ? ADNI_Templates::parallax_tpl($banner_post, $settings) : '';


            $h.= '</div>';
            //<!-- end .spr_column -->

 
        $h.= '</div>';
        //<!-- end .spr_row -->
        $h.= '</form>';
        
    
    $h.= '</div>';
    // <!-- end .wrap -->
$h.= '</div>';
// <!-- end .adning_add_new_banner -->


$h.= '<script>';
$h.= 'jQuery(document).ready(function($) {';

    //$h.= 'window.Adning_global.activate_tooltips($(\'.adning_dashboard\'));';
	
	// Initiate banner
	$h.= '$("._ning_cont").ningResponsive();';
	
    $h.= '$("#ADNI_size").on("change", function(){';
		$h.= 'var size = $(this).val(),';
            $h.= 'sizes = size.split("x");';
		
        $h.= 'console.log("common banner size change");';
		
		$h.= 'if( size !== "custom"){';
			$h.= "$('#ADNI_size_w').val(sizes[0]);";
			$h.= "$('#ADNI_size_h').val(sizes[1]);";

			// Change preview banner size
			$h.= '$("._ning_cont").ningResponsive({width:sizes[0], height:sizes[1]});';
			
			$h.= 'banner_resized_notice();';
        $h.= '}';
    $h.= '});';
	
	$h.= "$('._ning_custom_size').on('change', function(){";
		$h.= "var w = $('#ADNI_size_w').val(),";
            $h.= "h = $('#ADNI_size_h').val();";
		
        $h.= "console.log('custom size change');";
		
		// Select banner size option	
		$h.= 'if($("#ADNI_size option[value=\'"+w+"x"+h+"\']").length > 0){';
			$h.= "$('#ADNI_size option[value=\"'+w+'x'+h+'\"]').attr('selected', 'selected').change();";
        $h.= '}else{';
			$h.= '$(\'#ADNI_size option[value="custom"\').attr(\'selected\', \'selected\').change();';
        $h.= '}';
			
		// Change preview banner size
		$h.= '$("._ning_cont").ningResponsive({width:w, height:h});';
		$h.= 'banner_resized_notice();';
    $h.= '});';
    //$('._ning_custom_size').trigger("change");
    

    
	
	$h.= "$('#ADNI_responsive').on('change', function(){";
        $h.= '$("._ning_cont").toggleClass("responsive");';
        $h.= "var w = $('#ADNI_size_w').val(),";
            $h.= "h = $('#ADNI_size_h').val();";
                
        $h.= 'if( $("._ning_cont").hasClass("responsive") ){';
            $h.= '$("._ning_cont").css({width: "100%", height: h});';
        $h.= '}else{';
            $h.= '$("._ning_cont").css({width: w, height: h});';
        $h.= '}';
		//$("._ning_cont").ningResponsive();
    $h.= '});';
	$h.= "$('#ADNI_scale').on('change', function(){";
		$h.= '$("._ning_cont").toggleClass("scale");';
		//$("._ning_cont").ningResponsive();
    $h.= '});';
	
	
	$h.= '$(window).on("resize", banner_resized_notice);';
	
	
	
	// Banner resied notice
	$h.= 'function banner_resized_notice(){';
		$h.= "var width = $('#ADNI_size').val().split('x')[0],";
            $h.= 'banner_width = $("._ning_cont")[0].getBoundingClientRect().width,';
			$h.= 'banner_height = $("._ning_cont")[0].getBoundingClientRect().height;';
			
        $h.= 'if(banner_width < width){';
			$h.= "$('.banner_notice').html('Resized banner version: '+Math.round(banner_width)+'x'+Math.round(banner_height)+'px');";
        $h.= '}else{';
			$h.= '$(".banner_notice").html("");';
        $h.= '}';
    $h.= '}';



    $h.= "$('.adsense_btn').on('click', function(){";
        $h.= "$('.google_adsense_container').show();";
    $h.= "});";

    $h.= "$('.adsense_set').on('change', function(){";
		$h.= "var w = $('#ADNI_size_w').val(),";
            $h.= "h = $('#ADNI_size_h').val();";
        
        $h.= "var code = Adning_global.adsense_tpl({";
            $h.= "'pub_id': $('.adsense_pub_id').val(),";
            $h.= "'slot_id': $('.adsense_slot_id').val(),";
            $h.= "'type': $('.adsense_type').val()";
        $h.= "});";
        
        $h.= "$('#banner_content').val( code ).trigger( 'change' );";
    $h.= "});";
    
    
    
    $h.= '$(".HTML5Uploader")._ning_file_upload({';
        $h.= "'banner_id': '".$id."',";
        $h.= "'user_id': '".get_current_user_id()."',";
        $h.= "'max_upload_size': 1000,";
        $h.= "'upload': {";
            $h.= "'folder': 'banners/".$id."/',";
            $h.= "'dir': '".ADNI_UPLOAD_DIR."',";
            $h.= "'src': '".ADNI_UPLOAD_SRC."'";
        $h.= "},";
        $h.= "'allowed_file_types': ['zip','jpg','png','gif','svg','mp4'],";
        $h.= "'callback': function(obj){";
            //console.log('after upload callback');
            //console.log(obj);
            $h.= "var w = $('#ADNI_size_w').val(),";
                $h.= "h = $('#ADNI_size_h').val(),";
                $h.= "src = '',";
                $h.= "cont = '';";

            $h.= "if($.isEmptyObject(obj.unzip)){";
                //console.log('NO ZIP FILE');
                $h.= "var uploaded_file = JSON.parse(obj.files);";
                //console.log(uploaded_file);
                $h.= "src = uploaded_file[0].src;";
                $h.= "cont = Adning_global.fileContent({'url':src,'type':''});";
                //cont = '<img src="'+src+'" />';
            $h.= "}else{";
                $h.= "src = obj.unzip.url;";
                $h.= "cont = '<div style=\"max-width:'+w+'px; width:100%; height:'+h+'px;\"><iframe src=\"'+src+'\" border=\"0\" scrolling=\"no\" allowtransparency=\"true\" style=\"width:1px;min-width:100%;*width:100%;height:100%;border:0;\"></iframe></div>';";
                //cont = '<iframe src="'+src+'" style="border:none;width:'+w+'px;height:'+h+'px;"></iframe>';
            $h.= "}";

            $h.= "$('#banner_content').val( cont ).trigger( 'change' );";
        $h.= "}";
    $h.= '});';
	

    
    $h.= "$('.BGADUploader')._ning_file_upload({";
        $h.= "'banner_id': '".$id."',";
        $h.= "'user_id': '".get_current_user_id()."',";
        $h.= "'max_upload_size': 1000,";
        $h.= "'upload': {";
            $h.= "'folder': 'banners/".$id."/',";
            $h.= "'dir': '".ADNI_UPLOAD_DIR."',";
            $h.= "'src': '".ADNI_UPLOAD_SRC."'";
        $h.= "},";
        $h.= "'allowed_file_types': ['jpg','png','gif','svg'],";
        $h.= "'callback': function(obj){";
            $h.= "var src = '';";

            $h.= "var uploaded_file = JSON.parse(obj.files);";
            $h.= "src = uploaded_file[0].src;";

            $h.= "$('#bg_takeover_src').val(src).trigger('change');";
            /*$('.bgad_preview_container').show();

            $('.bgad_preview').bgTakeover({
                bg_image: src,
                bg_color: $('#bg_takeover_bg_color').val(),
                bg_pos: 'absolute',
                top_skin: $('#bg_takeover_top_skin').val(),
                container: '.bgad_prev_content'
            });*/

            $h.= "console.log(src);";
            //$('#banner_content').val( cont ).trigger( "change" );
        $h.= "}";
    $h.= "});";

    $h.= "$('.bg_takeover_prev_obj').on('change', function(){";
        $h.= "$('.bgad_preview_container').show();";
        $h.= "$('.bgad_preview').bgTakeover({";
            $h.= "bg_image: $('#bg_takeover_src').val(),";
            $h.= "bg_color: $('#bg_takeover_bg_color').val(),";
            $h.= "bg_pos: 'absolute',";
            $h.= "top_skin: $('#bg_takeover_top_skin').val(),";
            $h.= "container: '.bgad_prev_content',";
            $h.= "click_url: {";
                $h.= "'top': $('#bg_takeover_top_skin_url').val(),";
                $h.= "'left': $('#bg_takeover_left_skin_url').val(),";
                $h.= "'right': $('#bg_takeover_right_skin_url').val()";
            $h.= "}";
        $h.= "});";
    $h.= "});";

    if( !empty($b['bg_takeover_src']) || !empty($b['bg_takeover_bg_color']) )
    {
        $h.= "$('.bgad_preview_container').show();";
        $h.= "$('.bgad_preview').bgTakeover({";
            $h.= "bg_image: $('#bg_takeover_src').val(),";
            $h.= "bg_color: $('#bg_takeover_bg_color').val(),";
            $h.= "bg_pos: 'absolute',";
            $h.= "top_skin: $('#bg_takeover_top_skin').val(),";
            $h.= "container: '.bgad_prev_content',";
            $h.= "click_url: {";
                $h.= "'top': $('#bg_takeover_top_skin_url').val(),";
                $h.= "'left': $('#bg_takeover_left_skin_url').val(),";
                $h.= "'right': $('#bg_takeover_right_skin_url').val()";
            $h.= "}";
        $h.= "});";
    }



    /**
     * IMGMCE EDITOR
    */
    if( class_exists('ImgMCE'))
    {
        $h.= "$('.open_imgmce_button').on('click', function(){";

            $h.= "var editor = {imgMCE:{}};";
            $h.= "var defaults = {";
                $h.= "'active_editor': '',";
                $h.= "'in_popup':1,";
                $h.= "'save_to_folder': 1,";
                $h.= "'callback': '_imc_save_to_adning' ";
            $h.= "};";
            $h.= "editor.imgMCE = $.extend(defaults, editor.imgMCE);";

            $h.= "ImgMCE_global.load_editor({ 'editor':editor.imgMCE });";
        $h.= "});";
    }

$h.= "});";


$h.= "function _imc_save_to_adning(_obj, arr, settings){";

    $h.= "var $ = jQuery;";

    // Insert content in tinymce editor
    $h.= "var editing = $('body').find('#TMCimgModal').data('editing'),";
        $h.= "load_code = settings.editor.return_all_code;";
        $h.= "active_editor = settings.editor.active_editor,";
        $h.= "msg = JSON.stringify(arr),";
        $h.= "debug = settings.editor.debug;";

    $h.= "console.log('SAVE imgMCE from ADNING');";
    $h.= "console.log(arr);";
    
    $h.= "$('#banner_content').val( '[imgmce_element id=\"'+arr.banner_obj.id+'\" user_id=\"'+arr.banner_obj.user_id+'\" iframe_id=\"_dn".$id."\"]' ).trigger( 'change' );";
    $h.= "$('._ning_inner').html( arr.banner_template );";
$h.= "}";
$h.= "</script>";