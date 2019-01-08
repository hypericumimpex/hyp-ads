<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;

if ( ! class_exists( 'ADNI_Shortcodes' ) ) :

class ADNI_Shortcodes {	
	
	
	public function __construct() 
	{
		add_shortcode('ADNI_banner', array(__CLASS__, 'sc_ADNI_banner'));
		add_shortcode('ADNI_adzone', array(__CLASS__, 'sc_ADNI_adzone'));
		add_shortcode('adning', array(__CLASS__, 'sc_adning'));
	}
	
	
	
	/**
	 * shortcode description
	 */
	public static function sc_adning($args = array(), $content = null) 
	{	
		$defaults = array(
			'id' => 0,
			'animation' => '',
			'no_iframe' => 1,
			'stats' => 1
		);
		$args = wp_parse_args( $args, $defaults );
		$html = '';
		$animation = !empty($args['animation']) ? ',"animation":"'.$args['animation'].'"' : '';
		//$post = ADNI_CPT::load_post($args['id']);
		
		//if( !empty($post))
		if( !empty($args['id']))
		{
			$post_type = get_post_type( $args['id'] );
			//$post_type = $post['post']->post_type;
			
			ADNI_Init::enqueue(
				array(
					'files' => array(
						array('file' => '_ning_css', 'type' => 'style'),
						array('file' => '_ning_global', 'type' => 'script')
					)
				)
			);
			
			if( strtolower($post_type) == strtolower(ADNI_CPT::$banner_cpt))
			{
				if(!$args['no_iframe'])
				{
					$html.= '<script type="text/javascript">var _ning_embed = {"id":'.$args['id'].',"width":'.$post['args']['size_w'].',"height":'.$post['args']['size_h'].$animation.'};</script>';
					$html.= '<script type="text/javascript" src="'.get_bloginfo('url').'?_dnembed=true"></script>';
				}
				else
				{
					$html.= self::sc_ADNI_banner($args);
				}
			}
			else
			{
				if(!$args['no_iframe'])
				{
					$html.= '<script type="text/javascript">var _ning_embed = {"id":'.$args['id'].',"width":'.$post['args']['size_w'].',"height":'.$post['args']['size_h'].$animation.'};</script>';
					$html.= '<script type="text/javascript" src="'.get_bloginfo('url').'?_dnembed=true"></script>';
				}
				else
				{
					/*$html.= '<pre>'.print_r($post, true).'</pre>';*/
					$html.= self::sc_ADNI_adzone($args);
				}
			}
		}
		
		return $html;
	}
	
	
	
	public static function sc_ADNI_banner($args = array(), $content = null) 
	{	
		$defaults = array(
			'id' => 0,
			'load_script' => 1,
			'stats' => 1
		);
		$args = wp_parse_args( $args, $defaults );
		
		return ADNI_Templates::banner_tpl($args['id'], $args);
	}
	
	
	
	
	public static function sc_ADNI_adzone($args = array(), $content = null) 
	{	
		$defaults = array(
			'id' => 0,
			'stats' => 1
		);
		$args = wp_parse_args( $args, $defaults );
		
		return ADNI_Templates::adzone_tpl($args['id']);
	}
}

//new ADNI_Shortcodes();

endif;
?>