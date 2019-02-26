<?php
/**
 * POST
*/
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['save_role_settings']))
	{
		ADNI_CPT::add_custom_caps(array('role' => $_POST['create_banner_role'], 'cpt' => ADNI_CPT::$banner_cpt));
        ADNI_CPT::add_custom_caps(array('role' => $_POST['create_adzone_role'], 'cpt' => ADNI_CPT::$adzone_cpt));
        ADNI_CPT::add_custom_caps(array('role' => $_POST['create_campaign_role'], 'cpt' => ADNI_CPT::$campaign_cpt));
        ADNI_Multi::update_option('_adning_roles', $_POST);
        //update_option('_adning_roles', $_POST);
	}
}

//echo '<pre>'.print_r($role->capabilities,true).'</pre>';//ADNI_Main::capabilities(array('role' => 'administrator'));
$settings = ADNI_Main::settings();
?>


<!-- Wordpress Messages -->
<h2 class="messages-position"></h2>

<div class="adning_dashboard adning_cont">
	<div class="wrap">
       
        <?php echo ADNI_Templates::main_admin_header(array(
            'page' => 'role-manager',
            'title' => 'Adning Role Manager',
            'desc' => '⚡ ' . __('Adning is designed in a very modular fashion so that lots of functions are customizable. Should you wish to, you can find most general settings below.','adn')
        )); ?>

        <div class="container">
            <?php
            /**
             * ROLE SETTINGS
            */
            if( current_user_can(ADNI_ADMIN_ROLE))
            {
            ?>
            <div class="spr_row"> 
                <form action="" method="post" enctype="multipart/form-data">  
                <div class="spr_column">
                <div class="spr_column-inner">
                        <div class="spr_wrapper">
                        
                        <div class="option_box">
                            <div class="info_header">
                                <span class="nr"><i class="input_icon fa fa-cog" aria-hidden="true"></i></span>
                                <span class="text"><?php _e('Admin Role Settings','adn'); ?></span>
                                <input type="submit" value="<?php _e('Save Role Settings','adn'); ?>" class="button-primary" name="save_role_settings" style="width:auto;float:right;margin:8px;">
                            </div>
                            <div class="spr_row"> 

                                <div class="spr_column spr_col-3">
                                    <div class="spr_column-inner" style="padding: 0 0 20px 0;">
                                        <div class="spr_wrapper">
                                            <div class="input_container">
                                                <h3 class="title"><?php _e('Plugin Admin Role','adn'); ?></h3>
                                                <div class="input_container_inner">
                                                    <select name="admin_role" class="active">
                                                        <?php wp_dropdown_roles($settings['roles']['admin_role']); ?>
                                                    </select>
                                                </div>
                                                <span class="description bottom"><?php _e('Who can adjust the main settings.','adn'); ?></span>
                                            </div>
                                            <!-- end .input_container -->
                                        </div>
                                    </div>
                                </div> 
                                <div class="spr_column spr_col-3">
                                    <div class="spr_column-inner" style="padding: 0 0 20px 0;">
                                        <div class="spr_wrapper">
                                            <div class="input_container">
                                                <h3 class="title"><?php _e('Manage Banner Role','adn'); ?></h3>
                                                <div class="input_container_inner">
                                                    <select name="create_banner_role" class="active">
                                                        <?php wp_dropdown_roles($settings['roles']['create_banner_role']); ?>
                                                    </select>
                                                </div>
                                                <span class="description bottom"><?php _e('Specify who has admin privileges to manage all banners.','adn'); ?></span>
                                            </div>
                                            <!-- end .input_container -->
                                        </div>
                                    </div>
                                </div>
                                <div class="spr_column spr_col-3">
                                    <div class="spr_column-inner" style="padding: 0 0 20px 0;">
                                        <div class="spr_wrapper">
                                            <div class="input_container">
                                                <h3 class="title"><?php _e('Manage adzone Role','adn'); ?></h3>
                                                <div class="input_container_inner">
                                                    <select name="create_adzone_role" class="active">
                                                        <?php wp_dropdown_roles($settings['roles']['create_adzone_role']); ?>
                                                    </select>
                                                </div>
                                                <span class="description bottom"><?php _e('Specify who has admin privileges to manage all adzones.','adn'); ?></span>
                                            </div>
                                            <!-- end .input_container -->
                                        </div>
                                    </div>
                                </div>
                                <div class="spr_column spr_col-3">
                                    <div class="spr_column-inner" style="padding: 0 0 20px 0;">
                                        <div class="spr_wrapper">
                                            <div class="input_container">
                                                <h3 class="title"><?php _e('Manage campaigns Role','adn'); ?></h3>
                                                <div class="input_container_inner">
                                                    <select name="create_campaign_role" class="active">
                                                        <?php wp_dropdown_roles($settings['roles']['create_campaign_role']); ?>
                                                    </select>
                                                </div>
                                                <span class="description bottom"><?php _e('Specify who has admin privileges to manage all campaigns.','adn'); ?></span>
                                            </div>
                                            <!-- end .input_container -->
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- end .spr_row -->
                        </div>
                        <!-- end .option_box -->
                        
                    </div>
                    </div>
            </div>
            </form>
            </div>
            <!-- end .spr_row -->
            <?php
            }
            ?>
        
        </div>
        <!-- end .container -->
        
    </div>
    <!-- end .wrap -->
</div>
<!-- end .adning_dashboard -->



<script type="text/javascript">
jQuery(document).ready(function($) {
	$(".spr_column").inViewport(function(px){
		var animation = $(this).data('animation');
		if( typeof animation !== 'undefined' && animation != ''){
			if(px) $(this).addClass(animation+" spr_visible animated");
		}
	}, {padding:0});
});
</script>