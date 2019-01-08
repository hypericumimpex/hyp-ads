<?php
//delete_option( '_adning_settings' );
if( isset($_GET['run_update']) && !empty($_GET['run_update']))
{
    ADNI_Updates::run_update();
}

$set_arr = ADNI_Main::settings();
$settings = $set_arr['settings'];

/**
 * IF POST DATA
*/
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_POST['submit_btn']))
    {
        //echo '<pre>'.print_r($_POST, true).'</pre>';
        // Check for empty positioning post
        if(!isset($_POST['positioning']['post_types']))
        {
            $_POST['positioning']['post_types'] = array();
        }
        
        foreach($_POST as $key => $post){
            $settings[$key] = $_POST[$key];
        }
    
        // UPDATE SETTINGS
        ADNI_Multi::update_option('_adning_settings', $settings);
    }
}

//echo '<pre>'.print_r($settings, true).'</pre>';
?>

<!-- Wordpress Messages -->
<h2 class="messages-position"></h2>

<div class="adning_dashboard adning_cont">
	<div class="wrap">
       
        <?php echo ADNI_Templates::main_admin_header(array(
            'page' => 'settings',
            'title' => 'Adning General Settings',
            'desc' => '⚡ ' . __('Adning is designed in a very modular fashion so that lots of functions are customizable. Should you wish to, you can find most general settings below.','adn')
        )); ?>

        <div class="container">

            <form action="" method="post" id="imc-role-settings">

                <div class="spr_row">  
                    <!-- /**
                    * 6 x 6
                    */ -->
                    <div class="spr_column spr_col-6">

                        <div class="spr_column spr_col">
                            <div class="spr_column-inner left_column">
                                <div class="spr_wrapper">
                                    <div class="option_box">
                                        <div class="info_header">
                                            <span class="nr">1</span>
                                            <span class="text"><?php _e('Disable AD Settings','adn'); ?></span>
                                            <input type="submit" value="<?php _e('Save Changes','adn'); ?>" class="button-primary" name="submit_btn" style="width:auto;float:right;margin:8px;">
                                        </div>
                                        <div class="input_container">
                                            <div class="input_container_inner">
                                                <?php
                                                $html = '';
                                                $html.= '<div class="adn_settings_cont">';
                                                    $html.= '<h4>'.__('Disable Ads','adn').'</h4>';
                                                    $html.= '<div class="adn_settings_cont_inner clear">';
                                                        $html.= '<p>'.__('','adn').'</p>';
                                                        
                                                        $html.= ADNI_Templates::switch_btn(array(
                                                            'title' => __('Disable All Ads','adn'),
                                                            'id' => 'disable_all_ads',
                                                            'name' => 'disable[all_ads]',
                                                            'checked' => $settings['disable']['all_ads'],
                                                            'value' => 1,
                                                            'hidden_input' => 1,
                                                            'chk-on' => __('Yes','adn'),
                                                            'chk-off' => __('No','adn'),
                                                            'column' => array(
                                                                'size' => 'col-6',
                                                                'desc' => __('This will disable all ads on the website.','adn'),
                                                            )
                                                        ));
                                                        
                                                        
                                                        $html.= ADNI_Templates::spr_column(array(
                                                            'col' => 'spr_col-6',
                                                            'title' => __('Hide ads for logged-in users.','adn'),
                                                            'desc' => __('Select the lowest role a user must have in order to not see any ads.','adn'),
                                                            'content' => '<select name="disable[user_role_ads]" class=""><option value="" '.selected( $settings['disable']['user_role_ads'], '', false ).'>'.__('Show ads to everyone','adn').'</option>'.ADNI_Main::dropdown_roles($settings['disable']['user_role_ads']).'</select>'
                                                        ));
                                                        
                                                        
                                                    $html.= '</div>';
                                                $html.= '</div>';

                                                $html.= '<div class="adn_settings_cont">';
                                                    $html.= '<h4>'.__('GDPR - Disable Ads till content is approved','adn').'</h4>';
                                                    $html.= '<div class="adn_settings_cont_inner clear">';
                                                        $html.= '<p>'.__('Disable all Ads until the "content cookie" is approved. You can use the build in cookie message or use a third party option, in that case you can provide the cookie name in value here.','adn').'</p>';
                                                        
                                                        $html.= ADNI_Templates::switch_btn(array(
                                                            'title' => __('Disable all Ads until the "content cookie" is approved.','adn'),
                                                            'id' => 'disable_till_approved',
                                                            'name' => 'gdpr[disable_till_approved]',
                                                            'checked' => $settings['gdpr']['disable_till_approved'],
                                                            'value' => 1,
                                                            'hidden_input' => 1,
                                                            'chk-on' => __('Yes','adn'),
                                                            'chk-off' => __('No','adn'),
                                                            'column' => array(
                                                                'size' => 'col',
                                                                'desc' => __('','adn'),
                                                            )
                                                        ));


                                                        $html.= ADNI_Templates::spr_column(array(
                                                            'col' => 'spr_col-6',
                                                            'title' => __('Cookie name','adn'),
                                                            'desc' => __('The name of the cookie that needs to be available.','adn'),
                                                            'content' => ADNI_Templates::inpt_cont(array(
                                                                    'type' => 'text',
                                                                    'width' => '100%',
                                                                    'name' => 'gdpr[cookie_name]',
                                                                    'value' => $settings['gdpr']['cookie_name'],
                                                                    'placeholder' => ''
                                                                ))
                                                        ));

                                                        $html.= ADNI_Templates::spr_column(array(
                                                            'col' => 'spr_col-6',
                                                            'title' => __('Cookie value','adn'),
                                                            'desc' => __('The required value of the cookie.','adn'),
                                                            'content' => ADNI_Templates::inpt_cont(array(
                                                                    'type' => 'text',
                                                                    'width' => '100%',
                                                                    'name' => 'gdpr[cookie_value]',
                                                                    'value' => $settings['gdpr']['cookie_value'],
                                                                    'placeholder' => ''
                                                                ))
                                                        ));
                                                        
                                                    $html.= '</div>';

                                                    $html.= '<div class="adn_settings_cont_inner clear">';
                                                        
                                                        $html.= ADNI_Templates::switch_btn(array(
                                                            'title' => __('Show build-in gdpr cookie message.','adn'),
                                                            'id' => 'show_cookie_message',
                                                            'name' => 'gdpr[show_cookie_message]',
                                                            'checked' => $settings['gdpr']['show_cookie_message'],
                                                            'value' => 1,
                                                            'hidden_input' => 1,
                                                            'chk-on' => __('Yes','adn'),
                                                            'chk-off' => __('No','adn'),
                                                            'column' => array(
                                                                'size' => 'col-6',
                                                                'desc' => __('This will show a gdpr cookie message modal on your website.','adn'),
                                                            )
                                                        ));

                                                        $html.= ADNI_Templates::spr_column(array(
                                                            'col' => 'spr_col-6',
                                                            'title' => __('Button Text','adn'),
                                                            'desc' => __('The "Approve" button text for the GDPR cookie message.','adn'),
                                                            'content' => ADNI_Templates::inpt_cont(array(
                                                                    'type' => 'text',
                                                                    'width' => '100%',
                                                                    'name' => 'gdpr[cookie_message_approve_btn]',
                                                                    'value' => $settings['gdpr']['cookie_message_approve_btn'],
                                                                    'show_icon' => 1,
                                                                    'icon' => 'pencil',
                                                                    'placeholder' => __('I Accept Cookies','adn')
                                                                ))
                                                        ));

                                                        $html.= ADNI_Templates::textarea_cont(array(
                                                            'title' => __('Message Text','adn'),
                                                            'name' => 'gdpr[cookie_message_text]',
                                                            'value' => stripslashes($settings['gdpr']['cookie_message_text']),
                                                            'placeholder' => __('We use cookies to offer you a better browsing experience. If you continue to use this site, you consent to our use of cookies.','adn'),
                                                            'desc_pos' => 'bottom',
                                                            'desc' => __('The text to show in the GDPR cookie message.','adn')
                                                        ));

                                                        $html.= ADNI_Templates::inpt_cont(array(
                                                            'title' => __('Page button','adn'),
                                                            'desc' => __('(Optional) The URL to a page you want to add in the GDPR cookie message.','adn'),
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'gdpr[cookie_message_page_url]',
                                                            'value' => $settings['gdpr']['cookie_message_page_url'],
                                                            'show_icon' => 1,
                                                            'icon' => 'link',
                                                            'placeholder' => ''
                                                        ));
                                                      
                                                    $html.= '</div>';
                                                $html.= '</div>';

                                                echo $html;
                                                ?>
                                            </div>
                                            <span class="description bottom"><?php _e('','adn'); ?></span>
                                        </div>
                                        <!-- end .input_container -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end .spr_column -->
                        
                        <div class="spr_column spr_col">
                        <div class="spr_column-inner left_column">
                            <div class="spr_wrapper">
                                <div class="option_box">
                                    <div class="info_header">
                                        <span class="nr">2</span>
                                        <span class="text"><?php _e('Placement Settings','adn'); ?></span>
                                        <input type="submit" value="<?php _e('Save Changes','adn'); ?>" class="button-primary" name="submit_btn" style="width:auto;float:right;margin:8px;">
                                    </div>
                                    <div class="input_container">
                                        <div class="input_container_inner">
                                            <?php
                                            $html = '';
                                            $html.= '<div class="adn_settings_cont">';
                                                $html.= '<h4>'.__('Content Ads','adn').'</h4>';
                                                $html.= '<div class="adn_settings_cont_inner clear">';
                                                    $html.= '<p>'.__('Content Ads are ads that get added into the post content (Above content, Inside content, Below content).','adn').'</p>';
                                                
                                                    $html.= ADNI_Templates::switch_btn(array(
                                                        'title' => __('Disable Non-singular Ads','adn'),
                                                        'id' => 'disable_non_singular_ads',
                                                        'name' => 'disable[non_singular_ads]',
                                                        'checked' => $settings['disable']['non_singular_ads'],
                                                        'value' => 1,
                                                        'hidden_input' => 1,
                                                        'chk-on' => __('Yes','adn'),
                                                        'chk-off' => __('No','adn'),
                                                        'column' => array(
                                                            'size' => 'col-6',
                                                            'desc' => __('This will disable all "content" ads on non-singular pages (categories, tags, authors,...)','adn'),
                                                        )
                                                    ));
                                                $html.= '</div>';
                                            $html.= '</div>';
                                                    
                                            
                                            $html.= '<div class="adn_settings_cont">';
                                                $html.= '<h4>'.__('Post Types for ADS','adn').'</h4>';
                                                $html.= '<div class="adn_settings_cont_inner clear">';
                                                    $html.= '<p>'.__('Select the post types where "Auto Positioning" for Ads should be available.','adn').'</p>';
                                                
                                                    // Post types
                                                    $post_types = get_post_types();
                                                    if( !empty($post_types ))
                                                    {
                                                        foreach( $post_types as $post_type )
                                                        {
                                                            $exclude = array('attachment', 'revision', 'nav_menu_item', 'adni_banners', 'adni_adzones', 'adni_campaigns');
                                                            if( !in_array( $post_type, $exclude))
                                                            {
                                                                $html.= ADNI_Templates::checkbox(array(
                                                                    'title' => $post_type,
                                                                    //'tooltip' => __('Run plugin in debug mode.','adn'),
                                                                    'checked' => in_array($post_type, $settings['positioning']['post_types']) ? 1 : 0,
                                                                    'value' => $post_type,
                                                                    'hidden_input' => 0,
                                                                    'name' => 'positioning[post_types][]',
                                                                    'class' => 'option_checkbox'
                                                                ));
                                                                
                                                            }
                                                        }
                                                    }
                                                $html.= '</div>';
                                            $html.= '</div>';

                                            echo $html;
                                            ?>
                                        </div>
                                        <span class="description bottom"><?php _e('','adn'); ?></span>
                                    </div>
                                    <!-- end .input_container -->
                                </div>
                            </div>
                        </div>
                        </div>


                        <div class="spr_column spr_col">
                            <div class="spr_column-inner left_column">
                                <div class="spr_wrapper">
                                    <div class="option_box">
                                        <div class="info_header">
                                            <span class="nr">3</span>
                                            <span class="text"><?php _e('Google AdSense Settings','adn'); ?></span>
                                        </div>
                                        <div class="input_container">
                                            <div class="input_container_inner">
                                                <?php
                                                $html = '';
                                                // ADSENSE
                                                $html.= '<div class="adn_settings_cont">';
                                                    $html.= '<h4>'.__('Google AdSense','adn').'</h4>';
                                                    $html.= '<div class="adn_settings_cont_inner">';
                                                        
                                                        $html.= ADNI_Templates::inpt_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'title' => 'Pub ID',
                                                            'desc' => 'Add your Google AdSense publisher ID',
                                                            'name' => 'adsense_pubid',
                                                            'value' => $settings['adsense_pubid'],
                                                            'placeholder' => 'pub-xxxxxxxxxxxxxx',
                                                            'desc_pos' => 'bottom'
                                                        ));
                                                    $html.= '</div>';
                                                $html.= '</div>';

                                                echo $html;
                                                ?>
                                            </div>
                                            <span class="description bottom"><?php _e('','adn'); ?></span>
                                        </div>
                                        <!-- end .input_container -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end .spr_column -->


                        <div class="spr_column spr_col">
                            <div class="spr_column-inner left_column">
                                <div class="spr_wrapper">
                                    <div class="option_box">
                                        <div class="info_header">
                                            <span class="nr">5</span>
                                            <span class="text"><?php _e('AD Blocker Settings','adn'); ?></span>
                                        </div>
                                        <div class="input_container">
                                            <div class="input_container_inner">
                                                <?php
                                                $html = '';
                                                // ADSENSE
                                                $html.= '<div class="adn_settings_cont">';
                                                    $html.= '<h4>'.__('Enable AD Blocker detection','adn').'</h4>';
                                                    $html.= '<div class="adn_settings_cont_inner">';
                                                        
                                                        $html.= ADNI_Templates::switch_btn(array(
                                                            'title' => __('Check for active ad blockers.','adn'),
                                                            'name' => 'adblock_detect',
                                                            'checked' => $settings['adblock_detect'],
                                                            'value' => 1,
                                                            'hidden_input' => 1,
                                                            'chk-on' => __('Yes','adn'),
                                                            'chk-off' => __('No','adn')
                                                        ));

                                                        $html.= '<span class="description bottom">'.__('Detect when a visitor has an ab blocker enabled.','adn').'</span>';
                                                        
                                                    $html.= '</div>';
                                                $html.= '</div>';

                                                
                                                $html.= '<div class="adn_settings_cont">';
                                                    $html.= '<h4>'.__('AD Blocker detection message','adn').'</h4>';
                                                    $html.= '<div class="adn_settings_cont_inner">';
                                                        
                                                        $html.= ADNI_Templates::textarea_cont(array(
                                                            'title' => __('Message Text','adn'),
                                                            'name' => 'adblock_message',
                                                            'value' => stripslashes($settings['adblock_message']),
                                                            'placeholder' => __('You are using AD Blocker!.','adn'),
                                                            'desc_pos' => 'bottom',
                                                            'desc' => __('Message to show when an ad blocker is detected.','adn')
                                                        ));
                                                    $html.= '</div>';
                                                $html.= '</div>';
                                                echo $html;
                                                ?>
                                            </div>
                                            <span class="description bottom"><?php _e('','adn'); ?></span>
                                        </div>
                                        <!-- end .input_container -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end .spr_column -->

                    </div>
                    <!-- end LEFT COLUMN .spr_column -->






                    <!-- RIGHT COLUMN -->
                    <div class="spr_column spr_col-6 ">
                        <div class="spr_column-inner">
                            <div class="spr_wrapper">
                                <div class="option_box">
                                    <div class="info_header">
                                        <span class="nr">⚡</span>
                                        <span class="text"><?php _e('Server Status','adn'); ?></span>
                                    </div>
                                    <div class="input_container">
                                        
                                        <div class="system_status">
                                            <p style="border-bottom: solid 1px #e2e2e2;padding:0 0 10px 0;margin-bottom: 20px;">
                                                <?php _e("Let's see if your server is ready to run Adning. If any of the options below are not available please install the missing functions or contact your hosting provider.", 'adn'); ?>
                                            </p>
                                            <div class="option_box">
                                                <?php 
                                                _e('Uploads Folder Writable:', 'adn');
                                                $wp_uploads = wp_get_upload_dir();
                                                if ( wp_is_writable( $wp_uploads['basedir'] ) ) {
                                                    _e( '<code class="status-good">Yes</code>', 'adn' ); 
                                                } else {
                                                    echo sprintf( __( '<code class="status-bad">No</code> Uploads folder must be writable to allow WordPress function properly.<br><span>See <a href="%1$s" target="_blank">changing file permissions</a> or contact your hosting provider.</span>', 'adn' ), 'https://codex.wordpress.org/Changing_File_Permissions' );
                                                }
                                                ?>
                                            </div>
                                            <div class="option_box">
                                                <?php
                                                _e('WP Memory Limit:', 'adn');
                                                $wp_memory_limit = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
                                                if ( function_exists( 'memory_get_usage' ) ) {
                                                    $wp_memory_limit = max( $wp_memory_limit, wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) ) );
                                                }
                                                $memory = $wp_memory_limit;
                                                
                                                if ( $memory < 67108864 ) {
                                                    echo sprintf( __( '<code class="status-bad">%1$s</code> Minimum value is <strong>64 MB</strong>. <strong>128 MB</strong> is recommended.', 'adn' ), size_format( $memory ) );
                                                    //echo $tip;
                                                }
                                                else if ( $memory < 134217728 ) {
                                                    echo sprintf( __( '<code class="status-okay">%1$s</code> Current memory limit is sufficient for most tasks. However, recommended value is <strong>128 MB</strong>.', 'adn' ), size_format( $memory ) );
                                                    //echo $tip;
                                                }
                                                else if ( $memory < 268435456 ) {
                                                    echo sprintf( __( '<code class="status-good">%1$s</code>', 'adn' ), size_format( $memory ) );
                                                    //echo $tip;
                                                }
                                                else {
                                                    echo sprintf( __( '<code class="status-good">%1$s</code>', 'adn' ), size_format( $memory ) );
                                                }
                                                ?>
                                            </div>
                                            <div class="option_box">
                                                <?php 
                                                _e('ZipArchive Support:', 'adn');
                                                if ( class_exists( 'ZipArchive' ) ) {
                                                    _e( '<code class="status-good">Yes</code>', 'adn' ); 
                                                } else {
                                                    echo sprintf( __( '<code class="status-bad">No</code> ZipArchive is required for importing/exporting objects and templates.<br><span>Please contact your hosting provider.</span>', 'adn' ));
                                                }
                                                ?>
                                            </div>
                                            <div class="option_box">
                                                <?php 
                                                _e('PHP Version:', 'adn');
                                                if(phpversion() >= 5.3){
                                                    echo '<code class="status-good">'.phpversion().'</code>';
                                                }else{
                                                    echo sprintf(__('<code class="status-okay">%s</code> Adning has not been tested with PHP versions under 5.3.','adn'), phpversion());
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div>
                                            <?php echo ADNI_Templates::checkbox(array(
                                                    'title' => __('Debug','adn'),
                                                    'tooltip' => __('Run plugin in debug mode.','adn'),
                                                    'checked' => array_key_exists('debug',$settings) ? $settings['debug'] : 0,
                                                    'value' => 1,
                                                    'hidden_input' => 1,
                                                    'name' => 'debug',
                                                    'class' => 'option_checkbox _debug'
                                                ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <div class="spr_column spr_col">
                            <div class="spr_column-inner left_column">
                                <div class="spr_wrapper">
                                    <div class="option_box">
                                        <div class="info_header">
                                            <span class="nr">
                                            <svg viewBox="0 0 512 512" style="width: 20px;"><path fill="currentColor" d="M480 32l-64 368-223.3 80L0 400l19.6-94.8h82l-8 40.6L210 390.2l134.1-44.4 18.8-97.1H29.5l16-82h333.7l10.5-52.7H56.3l16.3-82H480z"></path></svg>
                                            </span>
                                            <span class="text"><?php _e('Website Code Injection','adn'); ?></span>
                                            <input type="submit" value="<?php _e('Save Changes','adn'); ?>" class="button-primary" name="submit_btn" style="width:auto;float:right;margin:8px;">
                                        </div>
                                        <div class="input_container">
                                            <div class="input_container_inner">
                                                <?php
                                                $html = '';
                                                $html.= '<div class="adn_settings_cont">';
                                                    $html.= '<h4>'.__('Code Placement Areas','adn').'</h4>';
                                                    $html.= '<div class="adn_settings_cont_inner">';
                                                        $html.= '<p>'.__('Code placement can inject scripts and styles into the header/footer area of your website. Common use cases are header/footer tags from ad networks like <em>Google DoubleClick for Publishers</em> or custom styles and scripts.','adn').'</p>';

                                                        // Header area
                                                        $html.= ADNI_Templates::textarea_cont(array(
                                                            'title' => __('Header Area','adn'),
                                                            'id' => 'placement_area_head',
                                                            'class' => 'code_editor',
                                                            'data' => 'data-lang="htmlmixed"',
                                                            'name' => 'placement_area_head',
                                                            'value' => stripslashes($settings['placement_area_head']),
                                                            'desc_pos' => 'bottom',
                                                            'desc' => sprintf(__('Add code before the closing %s tag.','adn'), htmlentities('</head>'))
                                                        ));
                                                        // Body area
                                                        $html.= ADNI_Templates::textarea_cont(array(
                                                            'title' => __('Footer Area','adn'),
                                                            'id' => 'placement_area_body',
                                                            'class' => 'code_editor',
                                                            'data' => 'data-lang="htmlmixed"',
                                                            'name' => 'placement_area_body',
                                                            'value' => stripslashes($settings['placement_area_body']),
                                                            'desc_pos' => 'bottom',
                                                            'desc' => sprintf(__('Add code before the closing %s tag.','adn'), htmlentities('</body>'))
                                                        ));
                                                    $html.= '</div>';
                                                $html.= '</div>';
                                                
                                                $html.= '<div class="adn_settings_cont">';
                                                    $html.= '<h4>'.__('Custom CSS','adn').'</h4>';
                                                    $html.= '<div class="adn_settings_cont_inner">';
                                                        $html.= ADNI_Templates::textarea_cont(array(
                                                            'title' => '',
                                                            'name' => 'custom_css',
                                                            'class' => 'code_editor',
                                                            'data' => 'data-lang="css"',
                                                            'value' => stripslashes($settings['custom_css']),
                                                            'placeholder' => '',
                                                            'desc_pos' => 'bottom',
                                                            'desc' => __('In case you need to add some custom CSS to your website.','adn')
                                                        ));
                                                    $html.= '</div>';
                                                $html.= '</div>';

                                                echo $html;
                                                ?>
                                            </div>
                                            <span class="description bottom"><?php _e('','adn'); ?></span>
                                        </div>
                                        <!-- end .input_container -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end .spr_column -->


                        <div class="spr_column spr_col">
                            <div class="spr_column-inner left_column">
                                <div class="spr_wrapper">
                                    <div class="option_box">
                                        <div class="info_header">
                                            <span class="nr">4</span>
                                            <span class="text"><?php _e('Uninstall Settings','adn'); ?></span>
                                        </div>
                                        <div class="input_container">
                                            <div class="input_container_inner">
                                                <?php
                                                $html = '';
                                                $html.= ADNI_Templates::switch_btn(array(
                                                    'title' => __('Remove all Adning data when uninstalling','adn'),
                                                    'name' => 'uninstall_remove_data',
                                                    'checked' => $settings['uninstall_remove_data'],
                                                    'value' => 1,
                                                    'hidden_input' => 1,
                                                    'chk-on' => __('Yes','adn'),
                                                    'chk-off' => __('No','adn'),
                                                    'column' => array(
                                                        'size' => 'col-6',
                                                        'desc' => __('This will remove all Adning content + settings when you remove the plugin.','adn'),
                                                    )
                                                ));
                                                echo $html;
                                                ?>
                                            </div>
                                            <span class="description bottom"><?php _e('','adn'); ?></span>
                                        </div>
                                        <!-- end .input_container -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end .spr_column -->


                        


                    </div>
                    <!-- end RIGHT COLUMN .spr_column -->


                </div>
                <!-- end .spr_row -->

                <input type="submit" name="submit_btn" id="submit_btn" class="button button-primary" value="<?php _e('Save Changes','adn'); ?>" style="display: inline-block;width: auto;">
            </form>
         
        </div>
        <!-- end .container -->

    </div>
    <!-- end .wrap -->
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {

    Adning_global.activate_tooltips($('.adning_dashboard'));

	$(".spr_column").inViewport(function(px){
		var animation = $(this).data('animation');
		if( typeof animation !== 'undefined' && animation != ''){
			if(px) $(this).addClass(animation+" spr_visible animated");
		}
	}, {padding:0});
});
</script>