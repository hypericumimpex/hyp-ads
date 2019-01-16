<?php
$set_arr = ADNI_Main::settings();
$settings = $set_arr['settings'];
//echo '<pre>'.print_r($settings,true).'</pre>';

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
                                                    $h.= '<div class="adn_settings_cont">';
                                                        $h.= '<h4>'.sprintf(__('%s Settings','adn'), ucfirst($key)).'</h4>';
                                                        $h.= '<div class="adn_settings_cont_inner clear">';
                                                            $h.= '<p>'.__('','adn').'</p>';

                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-2',
                                                                'title' => __('Activate','adn'),
                                                                'desc' => __('Allow Paypal payments.'),
                                                                'content' => ADNI_Templates::switch_btn(array(
                                                                    'name' => 'payment[paypal][active]',
                                                                    'checked' => $settings['sell']['payment']['paypal']['active'],
                                                                    'value' => 1,
                                                                    'hidden_input' => 1,
                                                                    'chk-on' => __('Yes','adn'),
                                                                    'chk-off' => __('No','adn')
                                                                ))
                                                            ));

                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-6',
                                                                'title' => __('Email adress','adn'),
                                                                'desc' => __('Paypal email adress to receive payments.','adn'),
                                                                'content' => ADNI_Templates::inpt_cont(array(
                                                                        'type' => 'text',
                                                                        'width' => '100%',
                                                                        'name' => 'payment[paypal][email]',
                                                                        'value' => $settings['sell']['payment']['paypal']['email'],
                                                                        'placeholder' => '',
                                                                        'icon' => 'at',
                                                                        'show_icon' => 1
                                                                    ))
                                                            ));

                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-2',
                                                                'title' => __('Sandbox','adn'),
                                                                'desc' => __('Run paypal in sandbox mode (for testing).'),
                                                                'content' => ADNI_Templates::switch_btn(array(
                                                                    'name' => 'payment[paypal][sandbox]',
                                                                    'checked' => $settings['sell']['payment']['paypal']['sandbox'],
                                                                    'value' => 1,
                                                                    'hidden_input' => 1,
                                                                    'chk-on' => __('Yes','adn'),
                                                                    'chk-off' => __('No','adn')
                                                                ))
                                                            ));

                                                            $h.= ADNI_Templates::spr_column(array(
                                                                'col' => 'spr_col-2',
                                                                'title' => __('Debug','adn'),
                                                                'desc' => __('Enable paypal debug mode (for testing).'),
                                                                'content' => ADNI_Templates::switch_btn(array(
                                                                    'name' => 'payment[paypal][debug]',
                                                                    'checked' => $settings['sell']['payment']['paypal']['debug'],
                                                                    'value' => 1,
                                                                    'hidden_input' => 1,
                                                                    'chk-on' => __('Yes','adn'),
                                                                    'chk-off' => __('No','adn')
                                                                ))
                                                            ));

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
                                                        'col' => 'spr_col-6',
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
                                                        'col' => 'spr_col-6',
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
        </div>
        <!-- end .container -->
    </div>
    <!-- end .wrap -->
</div>
<!-- end .adning_dashboard -->