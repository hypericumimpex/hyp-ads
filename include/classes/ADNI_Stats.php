<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Stats' ) ) :

class ADNI_Stats {

    public static $loaded_ads = array();
    public static $loaded_adzones = array();
    
    public function __construct() 
	{
        // Actions --------------------------------------------------------
        add_action( 'wp_footer', array( __CLASS__, 'loaded_ads_ids' ), PHP_INT_MAX );

        // Filters --------------------------------------------------------
        add_filter('adning_loaded_banners', array(__CLASS__, 'loaded_ads'), 10, 2);
    }


    /**
     * Collect all loaded ad ids on page with active statistics.
     */
    public static function loaded_ads($b, $args = array())
    {
        self::$loaded_ads[] = array($b['post']->ID => array( 'name' => $b['post']->post_title ));
        if(!empty($args['in_adzone']))
        {
            self::$loaded_adzones[$args['in_adzone']['post']->ID] = $args['in_adzone']['post']->post_title;
        }
        
        return count(self::$loaded_ads)-1;
    }



    /**
     * Output loaded ad ids javascript variable
     */
    public static function loaded_ads_ids()
    {
        $set_arr = ADNI_Main::settings();
        $settings = $set_arr['settings'];

        $h = '';
        if( !empty($settings['ga_tracking_id']))
        {
            $h.= '<script type="text/javascript">';
                $h.= 'var ang_tracker = "'.$settings['ga_tracking_id'].'";';
                $h.= 'var loaded_ang = '.json_encode(self::$loaded_ads).';';
                $h.= 'var loaded_angzones = '.json_encode(self::$loaded_adzones).';';
            $h.= '</script>';
        }

        echo $h;
    }


}
endif;
?>
