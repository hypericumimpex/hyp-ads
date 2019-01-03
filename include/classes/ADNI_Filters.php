<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Filters' ) ) :

class ADNI_Filters {
	
	public function __construct() 
	{
        // Actions --------------------------------------------------------
        add_action( 'wp_head', array( __CLASS__, 'inject_header' ), 20 );
        add_action( 'adning_head', array( __CLASS__, 'debug_marker' ), 2 );
        add_action( 'wp_footer', array( __CLASS__, 'inject_footer' ), 20 );
        
        // Filters --------------------------------------------------------
        add_filter( 'the_content', array(__CLASS__, 'content_filter'));
    }




    /**
     * Check to see if ads disabled.
     * 
     * return (bool)
     */
    public static function disabled_ads( $settings = array())
    {
        $disabled = false;

        if( empty($settings) )
        {
            $set_arr = ADNI_Main::settings();
            $settings = $set_arr['settings'];
        }

        // Disable All
        if( $settings['disable']['all_ads'] )
            return true; // disable ads
        
        // Logged-in Users
        if( is_user_logged_in() ) 
        {
            $user = wp_get_current_user();
            $role = ( array ) $user->roles;
            if( in_array($settings['disable']['user_role_ads'], $role))
            {
                return true; // disable ads
            }
        }

        

        // GDPR Content cookie
        if( !empty( $settings['gdpr']['disable_till_approved']))
        {
            if( !empty($settings['gdpr']['cookie_name']) || !empty($settings['gdpr']['show_cookie_message']))
            {
                $cookie_name = !empty($settings['gdpr']['show_cookie_message']) ? ADNI_GDPR_COOKIE : $settings['gdpr']['cookie_name'];
                $cookie_value = !empty($settings['gdpr']['show_cookie_message']) ? 1 : $settings['gdpr']['cookie_value'];

                if( !isset($_COOKIE[$cookie_name]) )
                    return true; // disable ads
                
                if( $_COOKIE[$cookie_name] != $cookie_value )
                    return true; // disable ads
            }
        }

        return $disabled;
    }



    /**
     * SHOW or HIDE Banner / Adzone
     * 
     */
    public static function show_hide( $ad )
    {
        global $post;
        //echo '<pre>'.print_r($post).'</pre>';
    
        // Show post always when loaded from admin
        if( is_admin() )
            return $ad;

        if( self::disabled_ads() )
            return;
        

        $args = $ad['args'];
        $display = array_key_exists('display_filter',$args) ? $args['display_filter'] : array();

        // AD display filter
        if( !empty($display) )
        {
            // DEVICE FILTER
            if( empty($display['show_desktop']) && self::user_device() === 'desktop' )
                return;

            if( empty($display['show_tablet']) && self::user_device() === 'tablet' )
                return;
            
            if( empty($display['show_mobile']) && self::user_device() === 'mobile' )
                return;
            

            // CONTENT FILTER
            // Make sure a post ID is defined. If not we don't filter.
            if( !is_object($post) || !array_key_exists('ID',$post) )
                return $ad;

            $show = !empty($display['show_hide']) ? $display['show_hide'] : 0;

            if( !empty($display['categories']) )
            {
                $cats = wp_get_post_categories($post->ID);
                $match = array_intersect($display['categories'],$cats);
                //echo '<pre>'.print_r($match,true).'</pre>';
                if( empty($match) && $show || !empty($match) && !$show ){
                    return;
                }
            }
            if( !empty($display['tags']) )
            {
                $tags = wp_get_post_tags($post->ID, array( 'fields' => 'ids' ));
                $match = array_intersect($display['tags'],$tags);
                if( empty($match) && $show || !empty($match) && !$show ){
                    return;
                }
            }
            if( !empty($display['posts']) )
            {
                if( !in_array($post->ID,$display['posts']) && $show || in_array($post->ID,$display['posts']) && !$show ){
                    return;
                }
            }
            if( !empty($display['pages']) )
            {
                if( !in_array($post->ID,$display['pages']) && $show || in_array($post->ID,$display['pages']) && !$show ){
                    return;
                }
            }
        }

        return $ad;
    }







    public static function inject_header()
    {
        $set_arr = ADNI_Main::settings();
        $settings = $set_arr['settings'];

        /*
		 * Action: 'adning_head' - Allow other plugins to output inside the Adning section in the head section.
		*/
        do_action( 'adning_head' );
        
        echo stripslashes($settings['placement_area_head']);
        echo '<style>'.stripslashes($settings['custom_css']).'</style>';

        echo '<!-- / '."Adning. -->\n\n";
    }
    public static function inject_footer()
    {
        $h = '';
        $set_arr = ADNI_Main::settings();
        $settings = $set_arr['settings'];

        $h.= stripslashes($settings['placement_area_body']);

        // GDPR Cookie message
        if( $settings['gdpr']['show_cookie_message'])
        {
            $h.= ADNI_Templates::gdpr_approve_modal_tpl(array(
                'text' => $settings['gdpr']['cookie_message_text'],
                'page_url' => $settings['gdpr']['cookie_message_page_url']
            ));
        }


        if( self::disabled_ads() )
        {
            echo $h;
            return;
        }

        // Check for banners
        $auto_pos = ADNI_Main::auto_positioning();
        if( !empty($auto_pos) )
        {
            foreach($auto_pos as $key => $arr)
            {
                if($arr['pos'] === 'popup')
                {
                    $css = '';
                    $css.= !empty($arr['custom']['popup_shadow_color']) ? '"box-shadow":"'.$arr['custom']['popup_shadow_color'].' 0px 5px 20px 0px"' : '"box-shadow":"none"';
                    
                    $h.= '<div id="mdl-elmt-'.$key.'"><div class="mdl_content">'.ADNI_Multi::do_shortcode('[adning id="'.$key.'"]').'</div></div>';
                    $h.= '<script>';
                        $h.= 'if( jQuery("#mdl-elmt-'.$key.'").find(".mdl_content").html() !== "" ){';
                            $h.= 'jQuery("#mdl-elmt-'.$key.'").modalJS({';
                                $h.= 'width:"'.$arr['custom']['popup_width'].'",';
                                $h.= 'height:"'.$arr['custom']['popup_height'].'",';
                                $h.= !empty( $arr['custom']['popup_bg_color'] ) ? 'bg_color:"'.$arr['custom']['popup_bg_color'].'",' : '';
                                $h.= !empty( $arr['custom']['popup_overlay_color'] ) ? 'overlay_color:"'.$arr['custom']['popup_overlay_color'].'",' : '';
                                $h.= !empty($css) ? 'css:{'.$css.'},' : '';
                                $h.= !empty($arr['custom']['popup_cookie_value']) ? 'cookie: {"expires":"'.$arr['custom']['popup_cookie_value'].'","type":"'.$arr['custom']['popup_cookie_type'].'"}' : '';
                                $h.= !empty( $arr['custom']['popup_custom_json'] ) ? stripslashes($arr['custom']['popup_custom_json']) : '';
                            $h.= '});';
                        $h.= '}';
                    $h.= '</script>';
                }

                if($arr['pos'] === 'cornerpeel')
                {
                    $h.= '<div id="crp-elmt-'.$key.'">'.ADNI_Multi::do_shortcode('[adning id="'.$key.'"]').'</div>';
                    $h.= '<script>jQuery("#crp-elmt-'.$key.'").cornerPeel({cornerAnimate:0});</script>';
                }

                if($arr['pos'] === 'bg_takeover')
                {
                    $b = ADNI_Multi::get_post_meta($key, '_adning_args', array());
                    
                    if( self::show_hide(array('args' => $b)) )
                    {
                        $bg_container = !empty($b['bg_takeover_bg_container']) ? $b['bg_takeover_bg_container'] : 'body';

                        $h.= '<script>';
                            $h.= "jQuery('".$bg_container."').bgTakeover({
                                bg_image: '".$b['bg_takeover_src']."',
                                bg_color: '".$b['bg_takeover_bg_color']."',
                                content_bg_color: '".$b['bg_takeover_content_bg_color']."',
                                bg_pos: '".$b['bg_takeover_position']."',
                                top_skin: '".$b['bg_takeover_top_skin']."',
                                container: '".$b['bg_takeover_content_container']."',
                                click_url: {
                                    'top': '".$b['bg_takeover_top_skin_url']."',
                                    'left': '".$b['bg_takeover_left_skin_url']."',
                                    'right': '".$b['bg_takeover_right_skin_url']."'
                                }
                            });";
                        $h.= '</script>';
                    }
                }
            }
        }

        echo $h;
    }




    

    



    public static function content_filter( $content )
    {
        $set_arr = ADNI_Main::settings();
        $settings = $set_arr['settings'];

        $auto_pos = ADNI_Main::auto_positioning();
        if( empty($auto_pos) )
            return $content;
        
        if( !is_singular() && $settings['disable_non_singular_ads'] )
            return $content;
        
        $above_content = '';
        $below_content = '';

        foreach($auto_pos as $key => $arr)
        {
            if($arr['pos'] === 'above_content')
            {
                $above_content.= '[adning id="'.$key.'" no_iframe="1"]';
            }
            if( $arr['pos'] === 'inside_content')
            {
                if ( is_single() && !is_admin() ) 
                {
                    $after_x_p = array_key_exists('position_after_x_p', $arr['custom']) ? $arr['custom']['position_after_x_p'] : 2;
                    $content = self::insert_after_paragraph( '[adning id="'.$key.'" no_iframe="1"]', $after_x_p, $content );
                }
            } 
            if($arr['pos'] === 'below_content')
            {
                $below_content.= '[adning id="'.$key.'" no_iframe="1"]';
            }
        }
        return $above_content.$content.$below_content;
    }



    // INSERT AD AFTER X PARAGRAPHS
    public static function insert_after_paragraph( $insertion, $paragraph_id, $content ) 
    {
        $closing_p = '</p>';
        $paragraphs = explode( $closing_p, $content );
        if (count($paragraphs) >= $insertion) {
            foreach ($paragraphs as $index => $paragraph) {
    
                if ( trim( $paragraph ) ) {
                    $paragraphs[$index] .= $closing_p;
                }
    
                if ( $paragraph_id == $index + 1 ) {
                    $paragraphs[$index] .= $insertion;
                }
            }
    
            return implode( '', $paragraphs );
        }
    
        return $content;
    }




    /**
	 * Return the user device
	 *
	 */
    public static function user_device()
    {
        if( !self::device(array('is' => 'is_tablet')) && !self::device(array('is' => 'is_mobile')) )
            return 'desktop';
        
        if( self::device(array('is' => 'is_tablet')) )
            return 'tablet';
        
        if( self::device(array('is' => 'is_mobile')) )
            return 'mobile';
    }



    /**
	 * FUNCTION TO CHECK USER DEVICE
	 *
	 */
	public static function device($args = array())
	{
		$detect = new ADNI_Mobile_Detect();
		
		$defaults = array(
			'is' => 'is_mobile'
		);
		$args = wp_parse_args( $args, $defaults );
		
		if( $args['is'] == 'is_mobile')
		{
			return apply_filters( 'is_mobile', $detect->isMobile(), $detect );
		}
		elseif( $args['is'] == 'is_tablet')
		{
			return apply_filters( 'is_tablet', $detect->isTablet(), $detect );	
		}
		elseif( $args['is'] == 'is_iphone')
		{
			return apply_filters( 'is_iphone', $detect->isIphone(), $detect );	
		}
		elseif( $args['is'] == 'is_ipad')
		{
			return apply_filters( 'is_ipad', $detect->isIpad(), $detect );
		}
		elseif( $args['is'] == 'is_ipod')
		{
			return apply_filters( 'is_ipod', $detect->is( 'iPod' ), $detect );
		}
		elseif( $args['is'] == 'is_ios')
		{
			return apply_filters( 'is_ios', $detect->isiOS(), $detect );	
		}
		elseif( $args['is'] == 'is_android')
		{
			return apply_filters( 'is_android', $detect->isAndroidOS(), $detect );	
		}
		elseif( $args['is'] == 'is_blackberry')
		{
			return apply_filters( 'is_blackberry', $detect->isBlackBerry(), $detect );	
		}
		elseif( $args['is'] == 'is_windows_mobile')
		{
			return apply_filters(
				'is_windows_mobile',
				( $detect->is( 'WindowsMobileOS' ) || $detect->is( 'WindowsPhoneOS' ) ),
				$detect
			);
		}
		elseif( $args['is'] == 'is_motorola')
		{
			return apply_filters( 'is_motorola', $detect->is( 'Motorola' ), $detect );	
		}
		elseif( $args['is'] == 'is_samsung')
		{
			return apply_filters( 'is_samsung', $detect->is( 'Samsung' ), $detect );	
		}
		elseif( $args['is'] == 'is_sony_ericsson')
		{
			return apply_filters( 'is_sony_ericsson', $detect->is( 'Sony' ), $detect );	
		}
    }
    




    /*
	 * Outputs or returns the debug marker.
	 *
	 * @param bool $echo Whether or not to echo the debug marker.
	 *
	 * @return string
	*/
	public static function debug_marker( $echo = true ) 
	{
		$marker = '<!-- Ads on this site are served by Adning v' . ADNI_VERSION . ' - adning.com -->';
		if ( $echo === false ) {
			return $marker;
		}
		else {
			echo "\n${marker}\n";
		}
	}
}

endif;
?>