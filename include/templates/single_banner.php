<?php
if( !is_user_logged_in() )
{
    echo '<div style="margin-top:50px;text-align:center;">'.__('Please login to access this area.','adn').'</div>';
    return;
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$id = !$id && isset($_POST['post_id']) ? $_POST['post_id'] : $id;
$is_frontend = isset($is_frontend) ? $is_frontend : 0;
$user_id = get_current_user_id();

// Create draft post ( just to get a banner ID )
if( !$id )
{
    $id = ADNI_CPT::add_update_post(
        array(
            'post_type' => ADNI_CPT::$banner_cpt,
            'post_id' => 0,
            'post_title' => '',
            'banner_responsive' => 1,
            'df_show_desktop' => 1,
            'df_show_tablet' => 1,
            'df_show_mobile' => 1
        ),
        'draft'
    );

    // Add new ID to url
    echo '<script>var url = document.location.href+"&id='.$id.'";document.location = url;</script>';
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
        //print_r($_POST);
		$id = ADNI_CPT::add_update_post($_POST);
	}
}

$set_arr = ADNI_Main::settings();
$settings = $set_arr['settings'];

/*
 * Load Post data or default values
*/
$banner_post = ADNI_CPT::load_post($id, array('post_type' => ADNI_CPT::$banner_cpt));

if( !current_user_can(ADNI_ADMIN_ROLE) && $user_id != $banner_post['post']->post_author || empty( $banner_post['post'] ))
{
    echo '<div style="margin-top:50px;text-align:center;">'.__('Sorry, This banner does not exists.','adn').'</div>';
    return;
}

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
?>

<div class="adning_cont adning_add_new_banner">
	<div class="wrap">
    
    	<!-- Wordpress Messages -->
        <h2 class="messages-position"></h2>
        
        <?php echo ADNI_Templates::admin_header(); ?>
        
        <form action="" method="post" enctype="multipart/form-data"> 
        	<input type="hidden" value="<?php echo $id; ?>" name="post_id">
            <input type="hidden" value="<?php echo ADNI_CPT::$banner_cpt; ?>" name="post_type">
        <div class="spr_row">  
        	<div class="spr_column spr_col-4">
                <div class="spr_column-inner left_column">
                    <div class="spr_wrapper">
                        <div class="option_box">
                        		<div class="info_header">
                                	<span class="nr">1</span>
                            		<span class="text"><?php _e('Banner Settings','adn'); ?></span>
                             </div>
                             <div class="input_container">
                             	<h3 class="title"><?php _e('Title','adn'); ?></h3>
                             	<div class="input_container_inner">
                                		<input 
                                        type="text" 
                                        class="" 
                                        name="title" 
                                        value="<?php echo !empty($banner_post['post']) ? $banner_post['post']->post_title : ''; ?>" 
                                        placeholder="<?php _e('Banner Title','adn'); ?>">
                                    <i class="input_icon fa fa-pencil" aria-hidden="true"></i>
                                </div>
                                <span class="description bottom"><?php _e('Add a banner title.','adn'); ?></span>
                             </div>
                             <!-- end .input_container -->
                             
                             <div class="input_container">
                             	<h3 class="title"><?php _e('URL','adn'); ?></h3>
                                	<div class="input_container_inner">
                                    <input 
                                        type="text" 
                                        class="" 
                                        name="banner_url" 
                                        value="<?php echo $b['banner_url']; ?>" 
                                        placeholder="<?php _e('http://','adn'); ?>">
                                    <i class="input_icon fa fa-link" aria-hidden="true"></i>
                                </div>
                                <span class="description bottom"><?php _e('Add a banner link (URL).','adn'); ?></span>
                             </div>
                             <!-- end .input_container -->
                             
                             <div class="input_container">
                             	<h3 class="title"><?php _e('Target','adn'); ?></h3>
                                	<div class="input_container_inner">
                                        <select name="banner_target" class="">
                                            <option value="_blank" <?php selected( $b['banner_target'], '_blank' ); ?>><?php _e('_blank, Load in a new window.','adn'); ?></option>
                                            <option value="_self" <?php selected( $b['banner_target'], '_self' ); ?>><?php _e('_self, Load in the same frame as it was clicked.','adn'); ?></option>
                                            <option value="_parent" <?php selected( $b['banner_target'], '_parent' ); ?>><?php _e('_parent, Load in the parent frameset.','adn'); ?></option>
                                            <option value="_top" <?php selected( $b['banner_target'], '_top' ); ?>><?php _e('_top, Load in the full body of the window.','adn'); ?></option>
                                        </select>
                                    </div>
                                <span class="description bottom"><?php _e('Banner link target.','adn'); ?></span>
                             </div>
                             <!-- end .input_container -->
                             
                             
                             <div class="spr_row">  
                                <div class="spr_column spr_col-6">
                                    <div class="spr_column-inner left_column">
                                        <div class="spr_wrapper">
                                             <div class="input_container">
                                                <h3 class="title"><?php _e('Link Masking','adn'); ?></h3>
                                                
                                                <div class="input_container_inner">
                                                    <label class="switch switch-slide small ttip" title="<?php _e('Turn Off link masking to link directly to the raw banner url, When turned off its not possible to save statistics for this banner.','adn'); ?>">
                                                        <input class="switch-input" type="checkbox" name="banner_link_masking" value="1" <?php checked( $b['banner_link_masking'], 1 ); ?> />
                                                        <span class="switch-label" data-on="<?php _e('On','adn'); ?>" data-off="<?php _e('Off','adn'); ?>"></span> 
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                                <span class="description bottom"><?php _e('','adn'); ?></span>
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
                                                <h3 class="title"><?php _e('No Follow','adn'); ?></h3>
                                                <div class="input_container_inner">
                                                    <label class="switch switch-slide small ttip" title="<?php _e('Add no Follow to banner link.','adn'); ?>">
                                                        <input class="switch-input" type="checkbox" name="banner_no_follow" value="1" <?php checked( $b['banner_no_follow'], 1 ); ?> />
                                                        <span class="switch-label" data-on="<?php _e('On','adn'); ?>" data-off="<?php _e('Off','adn'); ?>"></span> 
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                                <span class="description bottom"><?php _e('','adn'); ?></span>
                                            </div>
                                            <!-- end .input_container -->
                                        </div>
                                    </div>
                                </div>
                                <!-- end .spr_column -->
                            </div>
                            <!-- end .spr_row -->
                             
                             
                             
                             <div class="input_container">
                                	<div class="input_container_inner">
                                    <div class="sep_line" style="margin:10px 0 20px 0;"><span><strong><?php _e('Save','adn'); ?></strong></span></div>
                                		<input type="submit" value="<?php _e('Save Banner','adn'); ?>" class="button-primary" name="save_banner" style="width: auto;">
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
                        <?php echo ADNI_Templates::alignment_settings_tpl($b); ?>
                        

                        <!--
                        /**
                         * BORDER SETTINGS
                        */
                        -->
                        <?php echo ADNI_Templates::border_settings_tpl($b); ?>
                        
                        
                        
                        <!--
                        /**
                         * EXPORT BANNER
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
                                        <textarea id="embed_code" style="min-height:120px;font-size:11px;"><script type="text/javascript">var _ning_embed = {"id":"<?php echo $id; ?>","width":<?php echo $b['banner_size_w']; ?>,"height":<?php echo $b['banner_size_h']; ?>};</script><script type="text/javascript" src="<?php echo get_bloginfo('url'); ?>?_dnembed=true"></script></textarea>
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
                </div>
            </div>
            <!-- end .spr_column -->
            
            <div class="spr_column spr_col-8">
                <div class="spr_column-inner">
                    <div class="spr_wrapper">
                        <div class="option_box">
                        		<div class="info_header">
                                	<span class="nr">2</span>
                            		<span class="text"><?php _e('Banner','adn'); ?></span>
                                    <input type="submit" value="<?php _e('Save Banner','adn'); ?>" class="button-primary" name="save_banner" style="width:auto;float:right;margin:8px;">
                                
                                    <?php 
								    if( $id ){
                                		//echo '<a href="'.get_permalink($id).'" target="_blank" class="button" style="width:auto;float:right;margin:8px;">'.__('Preview Banner','adn').'</a>';
									}
									?>
                             </div>
                             
                             <div class="sep_line" style="margin:0 0 15px 0;"><span><strong><?php _e('Sizing','adn'); ?></strong></span></div>
                             <div class="spr_column spr_col-4">
                                <div class="spr_column-inner left_column">
                                    <div class="spr_wrapper">
                                    	<div class="input_container">
                                            <h3 class="title"><?php _e('','adn'); ?></h3>
                                                <div class="input_container_inner">
                                                <select id="ADNI_size" name="banner_size" class="">
                                                	<?php
														foreach(ADNI_Main::banner_sizes() as $size)
														{
															echo '<option value="'.$size['size'].'" '.selected( $b['banner_size'], $size['size'] ).'>'.$size['name'].' ('.$size['size'].')</option>';
														}
													?>
                                                  <option value="custom" <?php selected( $b['banner_size'], 'custom' ); ?>>Custom</option>
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
                                                <label class="switch switch-slide small input_h ttip" title="<?php _e('Responsive banner.','adn'); ?>">
                                                    <input class="switch-input" type="checkbox" id="ADNI_responsive" name="banner_responsive" value="1" <?php checked( $b['banner_responsive'], 1 ); ?> />
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
                                                                name="banner_size_w" 
                                                                value="<?php echo $b['banner_size_w']; ?>" 
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
                                                                name="banner_size_h" 
                                                                value="<?php echo $b['banner_size_h']; ?>" 
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


                            
                            
                            
                            <!-- BANNER CODE -->
                            <div class="spr_column">
                                <div class="spr_column-inner">
                                    <div class="spr_wrapper">
                                    
                                    	<div class="sep_line" style="margin:0 0 5px 0;"><span><strong><?php _e('Preview','adn'); ?></strong></span></div>
                                    	<div class="banner_holder clear" style="padding:20px;">
                                        	<div class="banner_notice"></div>
                                           
                                           <?php echo ADNI_Templates::banner_tpl($id, array('add_url' => 0));  ?>  
                                       	</div>
                                        <!-- end .banner_holder -->
                                        
                                        <div class="sep_line" style="margin:0 0 25px 0;"><span><strong><?php _e('Content','adn'); ?></strong></span></div>
                                        
                                        <div class="spr_column">
                                            <div class="spr_column-inner">
                                                <div class="spr_wrapper">
                                                    <div class="input_container">
                                                        <div id="HTML5Uploader" class="box" style="border:dashed 1px #d7d7d7;border-radius:3px;padding:15px 5px;background: #FFF;" method="post" action="<?php echo ADNI_AJAXURL; ?>" enctype="multipart/form-data"></div>
                                                        <span class="description bottom"><?php _e('Upload banner content.','adn'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end .spr_column -->
                                        

                                        <?php
                                        // IMGMCE Button
                                        if( class_exists('ImgMCE'))
                                        {
                                            $imgmce_logo = IMC_ASSETS_URL.'/images/logo_20.png';
                                            ?>
                                            <div class="spr_column spr_col-3 left_column">
                                                    <div class="spr_column-inner">
                                                        <div class="spr_wrapper">
                                                            <div class="input_container">
                                                                <h3 class="title"><?php _e('','adn'); ?></h3>
                                                                <div class="input_container_inner">
                                                                    <button class="open_imgmce_button button" type="button" style="height:30px;background-color:#fefe7e;border-color: #eee265;box-shadow: 0 1px #CCC364;color: #b6ac24;">
                                                                        <img style="position:absolute;margin-top: 4px;" src="<?php echo $imgmce_logo; ?>">
                                                                        <span style="margin-left:20px;"><?php _e('imgMCE Editor', 'adn'); ?></span>
                                                                    </button>
                                                                </div>
                                                                <span class="description bottom"><?php _e('Open imgMCE editor.','adn'); ?></span>
                                                            </div>
                                                            <!-- end .input_container -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end .spr_column -->
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if( !$is_frontend )
                                        {
                                            ?>
                                            <div class="spr_column spr_col-3 left_column">
                                                <div class="spr_column-inner">
                                                    <div class="spr_wrapper">
                                                        <div class="input_container">
                                                            <h3 class="title"><?php _e('','adn'); ?></h3>
                                                            <div class="input_container_inner">
                                                                <button class="upload_image_button button" type="button" style="height: 30px;"><?php _e('Wordpress Media', 'adn'); ?></button>
                                                            </div>
                                                            <span class="description bottom"><?php _e('Upload banner image.','adn'); ?></span>
                                                        </div>
                                                        <!-- end .input_container -->
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end .spr_column -->
                                            <?php
                                        }
                                        ?>


                                        <div class="spr_column spr_col-3 left_column">
                                            <div class="spr_column-inner">
                                                <div class="spr_wrapper">
                                                	  <div class="input_container">
                                                        <h3 class="title"><?php _e('','adn'); ?></h3>
                                                        <div class="input_container_inner">
                                                   			<button class="adsense_btn button" type="button" style="height: 30px;">
                                                                <svg style="height: 13px;" viewBox="0 0 256 252" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid"><defs><linearGradient x1=".041%" y1="50.091%" x2="100.078%" y2="50.091%" id="a"><stop stop-color="#4284F0" offset="0%"/><stop stop-color="#4487F4" offset="100%"/></linearGradient><linearGradient x1="28.448%" y1="81.561%" x2="55.474%" y2="42.237%" id="b"><stop stop-color="#F5B406" offset="0%"/><stop stop-color="#F4B913" offset="100%"/></linearGradient></defs><path d="M254.2 154.4L209.4 252H102.3l58.6-126h75c14.6 0 24.3 15.1 18.3 28.4z" fill="#437DE6"/><path d="M235.9 127.8c6.3 0 12 3.1 15.4 8.4 3.4 5.3 3.8 11.8 1.2 17.5l-44.2 96.6H107.2l56.9-122.5h71.8zm0-1.8H163l-58.6 126h105l44.7-97.6c6.1-13.3-3.6-28.4-18.2-28.4z" fill="#196CEA"/><path d="M62.1 1.8s56.2 61 69.4 67.3L149.8 29c6.4-12.7-1.8-27.5-17.1-27.3l-70.6.1z" fill="url(#a)" transform="translate(102 126)"/><g><path d="M112.9 10.9L0 252h104.4L165 121.7 221.6 0H130c-7.3 0-14 4.2-17.1 10.9z" fill="url(#b)"/><path d="M218.8 1.8L163.4 121l-60.1 129.3H2.8L114.5 11.6c2.8-6 8.9-9.9 15.5-9.9h88.8v.1zm2.8-1.8H130c-7.3 0-14 4.2-17.1 10.9L0 252h104.4L165 121.7 221.6 0z" fill="#F3AA00"/></g></svg>
                                                               <?php _e('Google AdSense', 'adn'); ?>
                                                            </button>
                                                        </div>
                                                        <span class="description bottom"><?php _e('Adsense banner settings.','adn'); ?></span>
                                                    </div>
                                                    <!-- end .input_container -->
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end .spr_column -->


                                        <div class="spr_column spr_col-3">
                                            <div class="spr_column-inner">
                                                <div class="spr_wrapper">
                                                    <div class="input_container">
                                                        <h3 class="title"><?php _e('','adn'); ?></h3>
                                                        <div class="input_container_inner">
                                                            <label class="switch switch-slide small ttip" title="<?php _e('Scale banner content to match resized banner container.','adn'); ?>">
                                                                <input class="switch-input" type="checkbox" id="ADNI_scale" name="banner_scale" value="1" <?php checked( $b['banner_scale'], 1 ); ?> />
                                                                <span class="switch-label" data-on="<?php _e('On','adn'); ?>" data-off="<?php _e('Off','adn'); ?>"></span> 
                                                                <span class="switch-handle"></span>
                                                            </label>
                                                        </div>
                                                        <span class="description bottom"><?php _e('Scale banner content','adn'); ?></span>
                                                    </div>
                                                    <!-- end .input_container -->
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end .spr_column -->
                                        
                                        <?php
                                        $adsense_pub_id = array_key_exists('pub_id', $b['adsense_settings']) && !empty($b['adsense_settings']['pub_id']) ? $b['adsense_settings']['pub_id'] : $settings['adsense_pubid'];
                                        $adsense_slot_id = array_key_exists('slot_id', $b['adsense_settings']) ? $b['adsense_settings']['slot_id'] : '';
                                        ?>
                                        <div class="spr_column google_adsense_container" <?php echo !empty($adsense_slot_id) ? '' : 'style="display:none;"'; ?>>
                                            <div class="spr_column-inner">
                                                <div class="spr_wrapper">
                                               	<div class="input_container clear">
                                                   	<h3 class="title"><?php _e('Google AdSense','adn'); ?></h3>

                                                        <div class="spr_column spr_col-4"><div class="spr_column-inner left_column"><div class="input_container_inner">
                                                            <input 
                                                                type="text" 
                                                                class="adsense_set adsense_pub_id" 
                                                                name="adsense_pubid" 
                                                                value="<?php echo $adsense_pub_id; ?>" 
                                                                placeholder="pub-xxxxxxx">
                                                            <i class="input_icon fa fa-pencil" aria-hidden="true"></i>

                                                            <span class="description bottom"><?php _e('AdSense pub ID','adn'); ?></span>
                                                        </div></div></div>
                                                        <!-- end .spr_column -->

                                                        <div class="spr_column spr_col-4"><div class="spr_column-inner left_column"><div class="input_container_inner">
                                                            <input 
                                                                type="text" 
                                                                class="adsense_set adsense_slot_id" 
                                                                name="adsense_slotid" 
                                                                value="<?php echo $adsense_slot_id; ?>" 
                                                                placeholder="xxxxxxx">
                                                            <i class="input_icon fa fa-pencil" aria-hidden="true"></i>

                                                            <span class="description bottom"><?php _e('AdSense ad slot ID','adn'); ?></span>
                                                        </div></div></div>
                                                        <!-- end .spr_column -->

                                                        <?php
                                                        $adsense_type = array_key_exists('type', $b['adsense_settings']) ? $b['adsense_settings']['type'] : '';
                                                        ?>
                                                        <div class="spr_column spr_col-4"><div class="spr_column-inner"><div class="input_container_inner">
                                                            <select name="adsense_type" class="adsense_set adsense_type">
                                                                <option value="" <?php selected($adsense_type, ''); ?>>-- Select --</option>
                                                                <option value="normal" <?php selected($adsense_type, 'normal'); ?>>Normal</option>
                                                                <option value="responsive" <?php selected($adsense_type, 'responsive'); ?>>Responsive</option>
                                                                <option value="matched-content" <?php selected($adsense_type, 'matched-content'); ?>>Responsive (Matched Content)</option>
                                                                <option value="link" <?php selected($adsense_type, 'link'); ?>>Link ads</option>
                                                                <option value="link-responsive" <?php selected($adsense_type, 'link-responsive'); ?>>Link ads (Responsive)</option>
                                                                <option value="in-article" <?php selected($adsense_type, 'in-article'); ?>>InArticle</option>
                                                                <option value="in-feed" <?php selected($adsense_type, 'in-feed'); ?>>InFeed</option>
                                                                <?php
                                                                /*
                                                                <option value="_blank" <?php selected( $b['banner_target'], '_blank' ); ?>><?php _e('_blank, Load in a new window.','adn'); ?></option>
                                                                <option value="_self" <?php selected( $b['banner_target'], '_self' ); ?>><?php _e('_self, Load in the same frame as it was clicked.','adn'); ?></option>
                                                                <option value="_parent" <?php selected( $b['banner_target'], '_parent' ); ?>><?php _e('_parent, Load in the parent frameset.','adn'); ?></option>
                                                                <option value="_top" <?php selected( $b['banner_target'], '_top' ); ?>><?php _e('_top, Load in the full body of the window.','adn'); ?></option>
                                                                */
                                                                ?>
                                                            </select>
                                                            <span class="description bottom"><?php _e('AdSense banner type','adn'); ?></span>
                                                        </div></div></div>
                                                        <!-- end .spr_column -->

                                                   	
                                                   	<span class="description bottom"><?php _e('','adn'); ?></span>
                                               	</div>
                                                  <!-- end .input_container -->
                                               </div>
                                            </div>
                                        </div>
                                        <!-- end .spr_column -->
                                        
                                        
                                        <div class="spr_column">
                                            <div class="spr_column-inner">
                                                <div class="spr_wrapper">
                                               	<div class="input_container">
                                                   	<h3 class="title"><?php _e('','adn'); ?></h3>
                                                    	<div class="input_container_inner">
                                                        <textarea id="banner_content" name="banner_content" style="min-height:200px;font-size: 13px;"><?php echo $b['banner_content']; ?></textarea>
                                                   	</div>
                                                   	<span class="description bottom"><?php _e('Banner HTML content.','adn'); ?></span>
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
                            
                            
                            
                        </div>
                        <!-- end .option_box -->
                    </div>
                </div>


                <div class="spr_column">
                    <?php
                    echo ADNI_Templates::auto_positioning_template($id, $banner_post);
                    echo ADNI_templates::display_filters_tpl($banner_post);
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
<!-- end .adning_add_new_banner -->


<script>
jQuery(document).ready(function($) {

    window.Adning_global.activate_tooltips($('.adning_dashboard'));
	
	// Initiate banner
	$("._ning_cont").ningResponsive();
	
    $('#ADNI_size').on('change', function(){
		var size = $(this).val(),
			sizes = size.split("x");
		
		console.log('common banner size change');
		
		if( size !== 'custom'){
			$('#ADNI_size_w').val(sizes[0]);
			$('#ADNI_size_h').val(sizes[1]);

			// Change preview banner size
			$("._ning_cont").ningResponsive({width:sizes[0], height:sizes[1]});
			
			banner_resized_notice();
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
		$("._ning_cont").ningResponsive({width:w, height:h});
		banner_resized_notice();
	});
    //$('._ning_custom_size').trigger("change");
    

    
	
	$('#ADNI_responsive').on('change', function(){
        $("._ning_cont").toggleClass('responsive');
        var w = $('#ADNI_size_w').val(),
            h = $('#ADNI_size_h').val();
                
        if( $("._ning_cont").hasClass('responsive') ){
            $("._ning_cont").css({width: '100%', height: h});
        }else{
            $("._ning_cont").css({width: w, height: h});
        }
		//$("._ning_cont").ningResponsive();
	});
	$('#ADNI_scale').on('change', function(){
		$("._ning_cont").toggleClass('scale');
		//$("._ning_cont").ningResponsive();
	});
	
	
	$('#banner_content').on('change', function(){
		$("._ning_cont").find('._ning_inner').html($(this).val()); // .find('._ning_inner')
		console.log('banner_content change');
	});
	
	
	$(window).on('resize', banner_resized_notice);
	
	
	
	// Banner resied notice
	function banner_resized_notice(){
		var width = $('#ADNI_size').val().split("x")[0],
			banner_width = $("._ning_cont")[0].getBoundingClientRect().width,
			banner_height = $("._ning_cont")[0].getBoundingClientRect().height;
			
		if(banner_width < width){
			$('.banner_notice').html('Resized banner version: '+Math.round(banner_width)+'x'+Math.round(banner_height)+'px');
		}else{
			$('.banner_notice').html('');
		}
	}
	
	
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
	
			/*var image_url = json.url;
			var image_caption = json.caption;
			var image_title = json.title;*/
			
			$('#banner_content').val('<div class="_ning_elmt"><img src="'+json.url+'" /></div>').trigger("change");
		});
	
		media_uploader.open();
    });
    


    $('.adsense_btn').on('click', function(){
        $('.google_adsense_container').show();
    });

    $('.adsense_set').on('change', function(){
		var w = $('#ADNI_size_w').val(),
            h = $('#ADNI_size_h').val();
        
        var code = Adning_global.adsense_tpl({
            'pub_id': $('.adsense_pub_id').val(),
            'slot_id': $('.adsense_slot_id').val(),
            'type': $('.adsense_type').val(),
        });
        
        $('#banner_content').val( code ).trigger( "change" );
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
    



    // .ZIP HTML5 uploads
    $('#HTML5Uploader')._ning_file_uploader({
        'banner_id': '<?php echo $id; ?>',
        'user_id': '<?php echo get_current_user_id(); ?>',
        'max_upload_size': 1000,
        'upload': {
            'folder': 'banners/<?php echo $id; ?>/',
            'dir': '<?php echo ADNI_UPLOAD_DIR; ?>',
            'src': '<?php echo ADNI_UPLOAD_SRC; ?>'
        },
        'allowed_file_types': ['zip','jpg','png','gif','svg'],
        'text': {
            //'logo': '<svg viewBox="0 0 384 512" style="width:20px;"><path fill="currentColor" d="M0 32l34.9 395.8L191.5 480l157.6-52.2L384 32H0zm308.2 127.9H124.4l4.1 49.4h175.6l-13.6 148.4-97.9 27v.3h-1.1l-98.7-27.3-6-75.8h47.7L138 320l53.5 14.5 53.7-14.5 6-62.2H84.3L71.5 112.2h241.1l-4.4 47.7z"></path></svg>',
            'logo': '<svg viewBox="0 0 640 512" style="width:30px;"><path fill="currentColor" d="M272 64c60.28 0 111.899 37.044 133.36 89.604C419.97 137.862 440.829 128 464 128c44.183 0 80 35.817 80 80 0 18.55-6.331 35.612-16.927 49.181C572.931 264.413 608 304.109 608 352c0 53.019-42.981 96-96 96H144c-61.856 0-112-50.144-112-112 0-56.77 42.24-103.669 97.004-110.998A145.47 145.47 0 0 1 128 208c0-79.529 64.471-144 144-144m0-32c-94.444 0-171.749 74.49-175.83 168.157C39.171 220.236 0 274.272 0 336c0 79.583 64.404 144 144 144h368c70.74 0 128-57.249 128-128 0-46.976-25.815-90.781-68.262-113.208C574.558 228.898 576 218.571 576 208c0-61.898-50.092-112-112-112-16.734 0-32.898 3.631-47.981 10.785C384.386 61.786 331.688 32 272 32zm48 340V221.255l68.201 68.2c4.686 4.686 12.284 4.686 16.97 0l5.657-5.657c4.687-4.686 4.687-12.284 0-16.971l-98.343-98.343c-4.686-4.686-12.284-4.686-16.971 0l-98.343 98.343c-4.686 4.686-4.686 12.285 0 16.971l5.657 5.657c4.686 4.686 12.284 4.686 16.97 0l68.201-68.2V372c0 6.627 5.373 12 12 12h8c6.628 0 12.001-5.373 12.001-12z"></path></svg>',
            'upload': '<strong>Click here</strong><span class="box__dragndrop"> or drag file to upload</span>.',
            'upload_info': 'Max. size 1000 MB. Allowed files: <strong><em>JPG, PNG, GIF, SVG, ZIP</em></strong>',
        },
        'callback': function(obj){
            //console.log('after upload callback');
            //console.log(obj);
            var w = $('#ADNI_size_w').val(),
                h = $('#ADNI_size_h').val(),
                src = '',
                cont = '';

            if($.isEmptyObject(obj.unzip)){
                console.log('NO ZIP FILE');
                var uploaded_file = JSON.parse(obj.files);
                src = uploaded_file[0].src;
                cont = '<img src="'+src+'" />';
            }else{
                src = obj.unzip.url;
                cont = '<iframe src="'+src+'" style="border:none;width:'+w+'px;height:'+h+'px;"></iframe>';
            }

            $('#banner_content').val( cont ).trigger( "change" );
        }
    });



    $('#BGADUploader')._ning_file_uploader({
        'banner_id': '<?php echo $id; ?>',
        'user_id': '<?php echo get_current_user_id(); ?>',
        'max_upload_size': 1000,
        'upload': {
            'folder': 'banners/<?php echo $id; ?>/',
            'dir': '<?php echo ADNI_UPLOAD_DIR; ?>',
            'src': '<?php echo ADNI_UPLOAD_SRC; ?>'
        },
        'allowed_file_types': ['jpg','png','gif','svg'],
        'text': {
            //'logo': '<svg viewBox="0 0 384 512" style="width:20px;"><path fill="currentColor" d="M0 32l34.9 395.8L191.5 480l157.6-52.2L384 32H0zm308.2 127.9H124.4l4.1 49.4h175.6l-13.6 148.4-97.9 27v.3h-1.1l-98.7-27.3-6-75.8h47.7L138 320l53.5 14.5 53.7-14.5 6-62.2H84.3L71.5 112.2h241.1l-4.4 47.7z"></path></svg>',
            'logo': '<svg viewBox="0 0 640 512" style="width:30px;"><path fill="currentColor" d="M272 64c60.28 0 111.899 37.044 133.36 89.604C419.97 137.862 440.829 128 464 128c44.183 0 80 35.817 80 80 0 18.55-6.331 35.612-16.927 49.181C572.931 264.413 608 304.109 608 352c0 53.019-42.981 96-96 96H144c-61.856 0-112-50.144-112-112 0-56.77 42.24-103.669 97.004-110.998A145.47 145.47 0 0 1 128 208c0-79.529 64.471-144 144-144m0-32c-94.444 0-171.749 74.49-175.83 168.157C39.171 220.236 0 274.272 0 336c0 79.583 64.404 144 144 144h368c70.74 0 128-57.249 128-128 0-46.976-25.815-90.781-68.262-113.208C574.558 228.898 576 218.571 576 208c0-61.898-50.092-112-112-112-16.734 0-32.898 3.631-47.981 10.785C384.386 61.786 331.688 32 272 32zm48 340V221.255l68.201 68.2c4.686 4.686 12.284 4.686 16.97 0l5.657-5.657c4.687-4.686 4.687-12.284 0-16.971l-98.343-98.343c-4.686-4.686-12.284-4.686-16.971 0l-98.343 98.343c-4.686 4.686-4.686 12.285 0 16.971l5.657 5.657c4.686 4.686 12.284 4.686 16.97 0l68.201-68.2V372c0 6.627 5.373 12 12 12h8c6.628 0 12.001-5.373 12.001-12z"></path></svg>',
            'upload': '<strong>Click here</strong><span class="box__dragndrop"> or drag file to upload</span>.',
            'upload_info': 'Max. size 1000 MB. Allowed files: <strong><em>JPG, PNG, GIF, SVG</em></strong>',
        },
        'callback': function(obj){
            var src = '';

            var uploaded_file = JSON.parse(obj.files);
            src = uploaded_file[0].src;

            $('#bg_takeover_src').val(src).trigger('change');
            /*$('.bgad_preview_container').show();

            $('.bgad_preview').bgTakeover({
                bg_image: src,
                bg_color: $('#bg_takeover_bg_color').val(),
                bg_pos: 'absolute',
                top_skin: $('#bg_takeover_top_skin').val(),
                container: '.bgad_prev_content'
            });*/

            console.log(src);
            //$('#banner_content').val( cont ).trigger( "change" );
        }
    });

    $('.bg_takeover_prev_obj').on('change', function(){
        $('.bgad_preview_container').show();
        $('.bgad_preview').bgTakeover({
            bg_image: $('#bg_takeover_src').val(),
            bg_color: $('#bg_takeover_bg_color').val(),
            bg_pos: 'absolute',
            top_skin: $('#bg_takeover_top_skin').val(),
            container: '.bgad_prev_content',
            click_url: {
                'top': $('#bg_takeover_top_skin_url').val(),
                'left': $('#bg_takeover_left_skin_url').val(),
                'right': $('#bg_takeover_right_skin_url').val()
            }
        });
    });

    <?php 
    if( !empty($b['bg_takeover_src']) || !empty($b['bg_takeover_bg_color']) )
    {
        ?>
        $('.bgad_preview_container').show();
        $('.bgad_preview').bgTakeover({
            bg_image: $('#bg_takeover_src').val(),
            bg_color: $('#bg_takeover_bg_color').val(),
            bg_pos: 'absolute',
            top_skin: $('#bg_takeover_top_skin').val(),
            container: '.bgad_prev_content',
            click_url: {
                'top': $('#bg_takeover_top_skin_url').val(),
                'left': $('#bg_takeover_left_skin_url').val(),
                'right': $('#bg_takeover_right_skin_url').val()
            }
        });
        <?php
    }
    ?>

    /*$('#bg_takeover_top_skin').on('change', function(){
        $('.bgad_prev_content').css({ 'margin-top': $(this).val() });         
        $('.skin_bg_top').css({height: $(this).val()});
    });*/


    /**
     * IMGMCE EDITOR
    */
    <?php
    if( class_exists('ImgMCE'))
    {
        ?>
        $('.open_imgmce_button').on('click', function(){

            var editor = {imgMCE:{}};
            var defaults = { 
                'active_editor': '', 
                'in_popup':1,
                'save_to_folder': 1,
                'callback': "_imc_save_to_adning" 
            }
            editor.imgMCE = $.extend(defaults, editor.imgMCE);

            ImgMCE_global.load_editor({ 'editor':editor.imgMCE });
        });
        <?php
    }
    ?>

});


function _imc_save_to_adning(_obj, arr, settings){

    var $ = jQuery;

    // Insert content in tinymce editor
    var editing = $('body').find('#TMCimgModal').data('editing'),
        load_code = settings.editor.return_all_code;
        active_editor = settings.editor.active_editor,
        msg = JSON.stringify(arr),
        debug = settings.editor.debug;

    console.log('SAVE imgMCE from ADNING');
    console.log(arr);
    
    $('._ning_inner').html( arr.banner_template );
    $('#banner_content').val( '[imgmce_element id="'+arr.banner_obj.id+'" user_id="'+arr.banner_obj.user_id+'" iframe_id="_dn<?php echo $id; ?>"]' );
}
</script>