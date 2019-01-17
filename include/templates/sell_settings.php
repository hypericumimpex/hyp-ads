<?php
$set_arr = ADNI_Main::settings();
$settings = $set_arr['settings'];

if(isset($_GET['remove_order']) && !empty($_GET['remove_order']))
{
    ADNI_Sell::remove_order($_GET['remove_order']);
}
if(isset($_GET['activate_order']) && !empty($_GET['activate_order']))
{
    ADNI_Sell::activate_order($_GET['activate_order']);
}



/**
 * IF POST DATA
*/
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_POST['submit_btn']))
    {
        unset($settings['submit_btn']);
        foreach($_POST as $key => $post){
            $settings['sell'][$key] = $_POST[$key];
        }

        //echo '<pre>'.print_r($settings,true).'</pre>';
        // UPDATE SETTINGS
        ADNI_Multi::update_option('_adning_settings', $settings);
    }
}
//echo '<pre>'.print_r($settings,true).'</pre>';
?>

<!-- Wordpress Messages -->
<h2 class="messages-position"></h2>

<div class="adning_dashboard adning_cont">
	<div class="wrap">
       
        <?php echo ADNI_Templates::main_admin_header(array(
            'page' => 'sell',
            'title' => 'Adning Sell Settings',
            'desc' => '⚡ ' . __('Allow users to buy advertisement spots on your website and manage their own banners.','adn')
        )); ?>

        <div class="container">

            <p>
                <?php _e('All purchases are handled on the frontend.','adn'); ?> <a href="<?php echo get_bloginfo('url'); ?>/?_ning_front=1" target="_blank"><?php _e('Frontend AD Manager','adn'); ?></a>
            </p>

            <form action="" method="post">
                <div class="spr_row">  
                    <div class="spr_column spr_col-8">
                        <div class="spr_column-inner left_column">
                            <div class="spr_wrapper">
                                <div class="option_box">
                                    <div class="info_header">
                                        <span class="nr">1</span>
                                        <span class="text"><?php _e('Payment Settings','adn'); ?></span>
                                        <input type="submit" value="<?php _e('Save Changes','adn'); ?>" class="button-primary" name="submit_btn" style="width:auto;float:right;margin:8px;">
                                    </div>
                                    <!-- end .info_header -->
                                    <div class="input_container">
                                        <div class="input_container_inner">
                                            <?php
                                            $h = '';
                                            if( !empty($settings['sell']['payment']))
                                            {
                                                foreach( $settings['sell']['payment'] as $key => $payment)
                                                {
                                                    $h.= '<input type="hidden" name="payment['.$key.'][title]" value="'.$payment['title'].'">';
                                                    $h.= '<div class="adn_settings_cont">';
                                                        $h.= '<h4>'.sprintf(__('%s Settings','adn'), $payment['title']).'</h4>';
                                                        $h.= '<div class="adn_settings_cont_inner clear">';
                                                            $h.= '<p>'.__('','adn').'</p>';

                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-2',
                                                                'title' => __('Activate','adn'),
                                                                'desc' => sprintf(__('Allow %s payments.'), $payment['title']),
                                                                'content' => ADNI_Templates::switch_btn(array(
                                                                    'name' => 'payment['.$key.'][active]',
                                                                    'checked' => $payment['active'],
                                                                    'value' => 1,
                                                                    'hidden_input' => 1,
                                                                    'chk-on' => __('Yes','adn'),
                                                                    'chk-off' => __('No','adn')
                                                                ))
                                                            ));
                                                            
                                                            if( array_key_exists('desc',$payment))
                                                            {
                                                                $h.= ADNI_Templates::spr_column(array(
                                                                    'col' => 'spr_col-8',
                                                                    'title' => __('Description','adn'),
                                                                    'desc' => sprintf(__('%s information.','adn'), $payment['title']),
                                                                    'content' => ADNI_Templates::textarea_cont(array(
                                                                            'type' => 'text',
                                                                            'width' => '100%',
                                                                            'name' => 'payment['.$key.'][desc]',
                                                                            'value' => $payment['desc'],
                                                                            'placeholder' => '',
                                                                        ))
                                                                ));
                                                            }

                                                            if( array_key_exists('email',$payment))
                                                            {
                                                                $h.= ADNI_Templates::spr_column(array(
                                                                    'col' => 'spr_col-6',
                                                                    'title' => __('Email adress','adn'),
                                                                    'desc' => sprintf(__('%s email adress to receive payments.','adn'), $payment['title']),
                                                                    'content' => ADNI_Templates::inpt_cont(array(
                                                                            'type' => 'text',
                                                                            'width' => '100%',
                                                                            'name' => 'payment['.$key.'][email]',
                                                                            'value' => $payment['email'],
                                                                            'placeholder' => '',
                                                                            'icon' => 'at',
                                                                            'show_icon' => 1
                                                                        ))
                                                                ));
                                                            }

                                                            if( array_key_exists('sandbox',$payment))
                                                            {
                                                                $h.= ADNI_Templates::spr_column(array(
                                                                    'col' => 'spr_col-2',
                                                                    'title' => __('Sandbox','adn'),
                                                                    'desc' => sprintf(__('Run %s in sandbox mode (for testing).'), $payment['title']),
                                                                    'content' => ADNI_Templates::switch_btn(array(
                                                                        'name' => 'payment['.$key.'][sandbox]',
                                                                        'checked' => $payment['sandbox'],
                                                                        'value' => 1,
                                                                        'hidden_input' => 1,
                                                                        'chk-on' => __('Yes','adn'),
                                                                        'chk-off' => __('No','adn')
                                                                    ))
                                                                ));
                                                            }

                                                            if( array_key_exists('debug',$payment))
                                                            {
                                                                $h.= ADNI_Templates::spr_column(array(
                                                                    'col' => 'spr_col-2',
                                                                    'title' => __('Debug','adn'),
                                                                    'desc' => sprintf(__('Enable %s debug mode (for testing).'), $payment['title']),
                                                                    'content' => ADNI_Templates::switch_btn(array(
                                                                        'name' => 'payment['.$key.'][debug]',
                                                                        'checked' => $payment['debug'],
                                                                        'value' => 1,
                                                                        'hidden_input' => 1,
                                                                        'chk-on' => __('Yes','adn'),
                                                                        'chk-off' => __('No','adn')
                                                                    ))
                                                                ));
                                                            }

                                                        $h.= '</div>';
                                                    $h.= '</div>';
                                                }
                                            }
                                            
                                            echo $h;
                                            ?>
                                        </div>
                                    </div>
                                    <!-- end .input_container -->
                                </div>
                                <!-- end .option_box -->
                                
                                <div class="option_box">
                                    <div class="info_header">
                                        <span class="nr">3</span>
                                        <span class="text"><?php _e('Frontend AD Manager Settings','adn'); ?></span>
                                        <input type="submit" value="<?php _e('Save Changes','adn'); ?>" class="button-primary" name="submit_btn" style="width:auto;float:right;margin:8px;">
                                    </div>
                                    <!-- end .info_header -->
                                    <div class="input_container">
                                        <div class="input_container_inner">
                                            <?php
                                            $h = '';
                                            $h.= '<div class="adn_settings_cont">';
                                                $h.= '<h4>'.__('Page URLs','adn').'</h4>';
                                                $h.= '<div class="adn_settings_cont_inner clear">';
                                                    $h.= '<p>'.__('','adn').'</p>';

                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col',
                                                        'title' => __('Order Form / Available Adzones','adn'),
                                                        'desc' => __('Link to the page with the available adzones/ order form. Shortcode: <strong>[adning_available_adzones]</strong>','adn'),
                                                        'content' => ADNI_Templates::inpt_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'urls[available_adzones]',
                                                            'value' => $settings['sell']['urls']['available_adzones'],
                                                            'placeholder' => '',
                                                            'icon' => 'link',
                                                            'show_icon' => 1
                                                        ))
                                                    ));
                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col',
                                                        'title' => __('User Dashboard','adn'),
                                                        'desc' => __('Link to the user dashboard page. Shortcode: <strong>[adning_user_dashboard]</strong>','adn'),
                                                        'content' => ADNI_Templates::inpt_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'urls[user_dashboard]',
                                                            'value' => $settings['sell']['urls']['user_dashboard'],
                                                            'placeholder' => '',
                                                            'icon' => 'link',
                                                            'show_icon' => 1
                                                        ))
                                                    ));
                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col',
                                                        'title' => __('Edit Banner','adn'),
                                                        'desc' => __('Link to the edit banner page. Shortcode: <strong>[adning_edit_banner]</strong>','adn'),
                                                        'content' => ADNI_Templates::inpt_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'urls[edit_banner]',
                                                            'value' => $settings['sell']['urls']['edit_banner'],
                                                            'placeholder' => '',
                                                            'icon' => 'link',
                                                            'show_icon' => 1
                                                        ))
                                                    ));

                                                $h.= '</div>';
                                            $h.= '</div>';


                                            $h.= '<div class="adn_settings_cont">';
                                                $h.= '<h4>'.__('Template Settings','adn').'</h4>';
                                                $h.= '<div class="adn_settings_cont_inner clear">';
                                                    $h.= '<p>'.__('','adn').'</p>';

                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col-6',
                                                        'title' => __('AD Manager Title','adn'),
                                                        'desc' => __('Your website AD manager title.','adn'),
                                                        'content' => ADNI_Templates::inpt_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'template[logo_title]',
                                                            'value' => $settings['sell']['template']['logo_title'],
                                                            'placeholder' => 'Adning',
                                                            'icon' => 'pencil',
                                                            'show_icon' => 1
                                                        ))
                                                    ));

                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col-6',
                                                        'title' => __('Side Title','adn'),
                                                        'desc' => __('AD Manager side title.','adn'),
                                                        'content' => ADNI_Templates::inpt_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'template[side_title]',
                                                            'value' => $settings['sell']['template']['side_title'],
                                                            'placeholder' => 'Frontend AD Manager',
                                                            'icon' => 'pencil',
                                                            'show_icon' => 1
                                                        ))
                                                    ));

                                                    $h.= '<div class="clearFix"></div>';
                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col-4',
                                                        'title' => __('Footer Info Line','adn'),
                                                        'desc' => __('Description in footer.','adn'),
                                                        'content' => ADNI_Templates::textarea_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'template[footer_info]',
                                                            'value' => stripslashes($settings['sell']['template']['footer_info']),
                                                            'placeholder' => '',
                                                            'icon' => 'pencil',
                                                            'show_icon' => 1
                                                        ))
                                                    ));

                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col-4',
                                                        'title' => __('Footer Copyright','adn'),
                                                        'desc' => __('Name of the copyright.','adn'),
                                                        'content' => ADNI_Templates::inpt_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'template[footer_copy]',
                                                            'value' => $settings['sell']['template']['footer_copy'],
                                                            'placeholder' => '',
                                                            'icon' => 'pencil',
                                                            'show_icon' => 1
                                                        ))
                                                    ));

                                                    $h.= ADNI_Templates::spr_column(array(
                                                        'col' => 'spr_col-4',
                                                        'title' => __('Footer Copyright URL','adn'),
                                                        'desc' => __('URL of the copyright.','adn'),
                                                        'content' => ADNI_Templates::inpt_cont(array(
                                                            'type' => 'text',
                                                            'width' => '100%',
                                                            'name' => 'template[footer_copy_url]',
                                                            'value' => $settings['sell']['template']['footer_copy_url'],
                                                            'placeholder' => '',
                                                            'icon' => 'link',
                                                            'show_icon' => 1
                                                        ))
                                                    ));

                                                $h.= '</div>';
                                            $h.= '</div>';
                                            echo $h;
                                            ?>
                                        </div>
                                    </div>
                                    <!-- end .input_container -->
                                </div>
                                <!-- end .option_box -->

                            </div>
                        </div>
                        <!-- end .spr_column-inner -->
                    </div>
                    <!-- end .spr_column -->

                    <div class="spr_column spr_col-4">
                        <div class="spr_column-inner left_column">
                            <div class="spr_wrapper">
                                <div class="option_box">
                                    <div class="info_header">
                                        <span class="nr">2</span>
                                        <span class="text"><?php _e('Sell Settings','adn'); ?></span>
                                        <input type="submit" value="<?php _e('Save Changes','adn'); ?>" class="button-primary" name="submit_btn" style="width:auto;float:right;margin:8px;">
                                    </div>
                                    <!-- end .info_header -->
                                    <div class="input_container">
                                        <div class="input_container_inner">
                                            <?php
                                            $h = '';
                                            $h.= ADNI_Templates::spr_column(array(
                                                'col' => 'spr_col',
                                                'title' => __('Currency','adn'),
                                                'desc' => __('Select the currency for the payments.'),
                                                'content' => ADNI_Templates::select_cont(array(
                                                    'name' => 'cur',
                                                    'value' => $settings['sell']['cur'],
                                                    'select_opts' => ADNI_Sell::currencies()
                                                ))
                                            ));
                                            
                                            echo $h;
                                            ?>
                                        </div>
                                    </div>
                                    <!-- end .input_container -->
                                </div>
                            </div>
                        </div>
                        <!-- end .spr_column-inner -->
                    </div>
                    <!-- end .spr_column -->

                </div>
                <!-- end .spr_row -->
            </form>

            

            
            <div class="spr_row">  
                <div class="spr_column spr_col">
                    <div class="spr_column-inner left_column">
                        <div class="spr_wrapper">
                            
                            <div id="orders" class="option_box">
                                <div class="info_header">
                                    <span class="nr">4</span>
                                    <span class="text"><?php _e('Orders','adn'); ?></span>
                                </div>
                                <!-- end .info_header -->
                                <div class="input_container">
                                    <div class="input_container_inner">
                                        <?php echo ADNI_Sell::admin_dashboard(); ?>
                                    </div>
                                </div>
                            </div>
                            <!-- end .option_box -->

                        </div>
                    </div>
                </div>
            </div>
            <!-- end .spr_row -->


        </div>
        <!-- end .container -->
    </div>
    <!-- end .wrap -->
</div>
<!-- end .adning_dashboard -->