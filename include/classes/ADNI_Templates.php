<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Templates' ) ) :

// Fix for Chrome bug: https://stackoverflow.com/a/44687900/3481803
header('X-XSS-Protection:0');

class ADNI_Templates {

	public static function logo_svg($args = array())
	{
		$defaults = array(
			'width' => '100%',
			'height' => '100%'
		);
		$args = wp_parse_args($args, $defaults);

		return '<svg width="'.$args['width'].'" height="'.$args['height'].'" viewBox="0 0 310 426"><g> <g id="bottom_xA0_Image_3_"> <g id="XMLID_2_"> <g> <path fill="#DEFF00" d="M237,225c-0.33,0-0.67,0-1,0C182.27,284.93,127.15,343.49,73,403c-0.851,0.18-0.94-0.39-1-1 c26.08-58.92,51.7-118.3,77-178C178,224.67,209.67,222.67,237,225z"/> </g> </g> </g> <g id="top_xA0_Image_3_"> <g id="XMLID_3_"> <g> <path fill="#DEFF00" d="M289,165c0,1.33,0,2.67,0,4c-17.86,18.48-34.641,38.03-52,57c-56,0-112,0-168,0c0-1,0-2,0-3 c21.5-64.83,42.62-130.05,63-196c46.33,0,92.67,0,139,0c-27.25,45.75-54.78,91.22-81,138C223,165,256,165,289,165z"/> </g> </g> </g> </g> <g id="BACK_1_"> <g id="bottom_xA0_Image_1_"> <g id="XMLID_5_"> <g> <path fill="#84BA12" d="M149,225c0,1.33,0,2.67,0,4c-26.73,57.27-50.59,117.41-77,175c-16.021-1.31-37.3,2.63-50-2 c25.93-59.4,52.979-117.689,79-177C117,225,133,225,149,225z"/> </g> </g> </g> <g id="top_xA0_Image_1_"> <g id="XMLID_4_"> <g> <path fill="#84BA12" d="M133,27c0,1,0,2,0,3c-21.17,64.5-41.92,129.41-62,195c-16.33,0-32.67,0-49,0c0-1,0-2,0-3 C43.17,157.5,63.92,92.59,84,27C100.33,27,116.67,27,133,27z"/> </g> </g> </g> </g> </svg>';
	}

	
	public static function admin_header()
	{
		$html = '';
		$html.= '<div class="spr_row">';
       
			$html.= '<div class="spr_column">';
           	$html.= '<div class="spr_column-inner">';
            		$html.= '<div class="spr_wrapper">';
        				$html.= '<div style="margin: 0 0 10px; text-align:left; padding:10px; background-color:#FFF;">';
                     		$html.= '<div style="float:left;"><span class="adning_logo"></span></div>';
                        	$html.= '<div style="float:left; margin: 25px 0 0 15px;">';
                         		$html.= '<h3 style="margin:0;font-size:2.9em;display:inline-block;color:#32373c;">ADNING</h3>';
                            	$html.= '<h4 style="margin:0px 0 0 5px;font-size:1.5em;display:inline-block;color:#32373c;">- '.__('Advertising Ready To Strike','adn').'</h4>';
                         $html.= '</div>';
                        	$html.= '<div style="float:right;">';
                        		$html.= '<small>'.ADNI_VERSION.'</small> ';
                        	$html.= '</div>';
                        	$html.= '<div class="clearFix"></div>';
                    	$html.= '</div>';
                  $html.= '</div>';
                  //end .spr_wrapper
              $html.= '</div>';
              //end .spr_column-inner 
          $html.= '</div>';
          //end .spr_column
      $html.= '</div>';
      //end .spr_row -->';
		
		return $html;	
	}




	public static function main_admin_header($args = array())
	{
		$defaults = array(
			'page' => 'dashboard',
			'title' => 'Adning Premium Advertising',
			'desc' => '⚡ ' . __('Welcome! You are using the most powerful Advertising plugin for Wordpress. Let\'s get started!','adn'),
			'tabs' => 1
		);
		$args = wp_parse_args($args, $defaults);

		//$activation = get_option('adning_activation', array());
		$activation = ADNI_Multi::get_option('adning_activation', array());

		$html = '';
		$html.= '<div class="imc-heading-section adning-header">';
			$html.= '<h1>'.$args['title'].'</h1>';
			$html.= '<h3>'.$args['desc'].'</h3>';
			$html.= '<div class="adn-head-logo">';
				$html.= '<div class="logo" style="height: 80px;">'.self::logo_svg().'</div>';
				$html.= '<div class="adn-product-ver">';
					$html.= '<div>'.__('Version','adn').' '.ADNI_VERSION.'</div>';
				$html.= '</div>';
			$html.= '</div>';
		$html.= '</div>';

		// Menu options
		if( $args['tabs'])
		{
			$m_about = $args['page'] == 'dashboard' ? ' nav-tab-active' : '';
			$m_settings = $args['page'] == 'settings' ? ' nav-tab-active' : '';
			$m_roles = $args['page'] == 'role-manager' ? ' nav-tab-active' : '';
			$m_updates = $args['page'] == 'updates' ? ' nav-tab-active' : '';

			$activated = !empty($activation) ? '' : ' style="background-color:#d4ff00;"';
			$activation_title = !empty($activation) ? '' : ' title="'.__('Your license has not yet been activated.','adn').'"';

			$html.= '<div class="adning-settings-wrapper">';
				$html.= '<h2 class="nav-tab-wrapper">';
					$html.= '<a href="?page=adning" data-tab="about-adning" class="nav-tab'.$m_about.'"> '.__('About','adn').' </a>';
					$html.= '<a href="?page=adning-settings" data-tab="adning-settings" class="nav-tab'.$m_settings.'"> '.__('General Settings','adn').' </a>';
					$html.= '<a href="?page=adning-role-manager" data-tab="adning-role-manager" class="nav-tab'.$m_roles.'"> '.__('Role Manager','adn').' </a>';
					$html.= '<a href="?page=adning-updates" data-tab="adning-updates" class="nav-tab ttip'.$m_updates.'"'.$activated.$activation_title.'> '.__('Product License','adn').' </a>';
				$html.= '</h2>';
			$html.= '</div>';
		}
		
		return $html;	
	}


	// ABOUT TABS
	public static function about_tabs($args = array())
	{
		$defaults = array(
			'tab' => 'faq'
		);
		$args = wp_parse_args($args, $defaults);

		$h = '';
		$tab = $args['tab'];
		//$activation = get_option('adning_activation', array());
		$activation = ADNI_Multi::get_option('adning_activation', array());

		// Menu options
		$m_new = $tab == 'new' ? ' nav-tab-active' : '';
		$m_faq = $tab == 'faq' ? ' nav-tab-active' : '';
		$m_addons = $tab == 'addons' ? ' nav-tab-active' : '';
		$m_resources = $tab == 'resources' ? ' nav-tab-active' : '';

		$h.= '<div class="adning-about-tabs">';
			$h.= '<h2 class="nav-tab-wrapper">';
				//$h.= '<a href="?page=adning&tab=new" data-tab="whats-new" class="nav-tab'.$m_new.'"> '.__('What\'s New','adn').' </a>';
				$h.= '<a href="?page=adning&tab=faq" data-tab="faq" class="nav-tab'.$m_faq.'"> '.__('FAQ','adn').' </a>';
				$h.= current_user_can(ADNI_ADMIN_ROLE) ? '<a href="?page=adning&tab=addons" data-tab="addons" class="nav-tab'.$m_addons.'"> '.__('Add-Ons','adn').' </a>' : '';
				$h.= '<a href="?page=adning&tab=resources" data-tab="resources" class="nav-tab'.$m_resources.'"> '.__('Resources','adn').' </a>';
				$h.= empty($activation) && current_user_can(ADNI_ADMIN_ROLE) ? '<a href="?page=adning-updates" data-tab="updates" class="nav-tab" style="background-color:#d4ff00;"> '.__('Product License','adn').' </a>' : '';
			$h.= '</h2>';
		$h.= '</div>';
		
		return $h;	
	}


	
	
	/**
	 * Main banner container
	*/
	public static function banner_tpl($id, $args = array())
	{
		$defaults = array(
			'add_url' => 1,
			'load_script' => 1,
			'animation' => '',
			'in_adzone' => 0
		);
		$args = wp_parse_args($args, $defaults);
		
		$banner = ADNI_CPT::load_post( $id );
		if( empty($banner) )
			return '';

		$b = $banner['args'];
		$html = '';
		
		$url = !empty($b['banner_url']) ? $b['banner_url'] : '';
		//$url = preg_replace( "/\r|\n/", "", $url );
		//$html.= '<pre>'.print_r($b,true).'</pre>';
		$content = $b['banner_content'];
		
		// Sizes
		$banner_w = is_numeric($b['banner_size_w']) ? $b['banner_size_w'].'px' : '100%';
		
		$url = $b['banner_link_masking'] && !empty($url) ? ADNI_Main::link_masking($id) : $url;
		$nofollow = $b['banner_no_follow'] ? ' rel="nofollow"' : '';
		$responsive_class = $b['banner_responsive'] ? ' responsive' : '';
		$scale_class = $b['banner_scale'] ? ' scale' : '';
		$inner_size = $b['banner_scale'] ? 'width:'.$banner_w.';height:'.$b['banner_size_h'].'px;' : '';
		$animation = !empty($args['animation']) ? ' data-animation="'.$args['animation'].'"' : '';
		$label = !$args['in_adzone'] ? $b['cont_label'] : '';
		$label_color = !empty($b['cont_label_color']) ? 'color:'.$b['cont_label_color'].';' : '';
		$label_pos = ' _'.$b['cont_label_pos'];
		$has_label = !empty($label) ? ' has_label' : '';
		$has_border = !$args['in_adzone'] && !empty($b['cont_border']) ? ' has_border' : '';
		$border_color = !empty($b['cont_border_color']) ? ' background:'.$b['cont_border_color'].';' : '';
		
		$ning_outer_class = !$args['in_adzone'] ? ' _ning_outer' : '';
		$align_class = ' _align_'.$b['align'];
		$clearfix_div = !$b['wrap_text'] ? '<div class="clear"></div>' : '';
		
		// Banner content
		//$html.= '<div class="_ning_outer _ning_cont _ning_hidden'.$responsive_class.$scale_class.'" data-size="'.$b['banner_size'].'"'.$animation.' style="max-width:'.$banner_w.'; width:100%; max-height:'.$b['banner_size_h'].'px; height: '.$b['banner_size_h'].'px;">';
		$html.= '<div class="_ning_cont _ning_hidden'.$ning_outer_class.$align_class.$responsive_class.$scale_class.$has_label.$has_border.'" data-size="'.$b['banner_size'].'"'.$animation.' style="max-width:'.$banner_w.'; width:100%; height: inherit;'.$border_color.'">';
			$html.= !$args['in_adzone'] ? '<div class="_ning_label'.$label_pos.'" style="'.$label_color.'">'.$label.'</div>' : '';
			$html.= '<div class="_ning_inner" style="'.$inner_size.'">';
				// Banner_url
				$html.= !empty($url) && $args['add_url'] ? '<a href="'.$url.'" target="'.$b['banner_target'].'"'.$nofollow.'>' : '';
					$html.= ADNI_Multi::do_shortcode($content);
				$html.= !empty($url) && $args['add_url'] ? '</a>' : '';
			$html.= '</div>';
		$html.= '</div>';
		$html.= $clearfix_div;
			
		
		// JS
		if($args['load_script'])
		{
			$html.= '<script>';
				$html.= 'jQuery(document).ready(function($){';
					$html.= '$("._ning_cont").ningResponsive();';
				$html.= '});';
			$html.= '</script>';
		}
		
		return $html;
	}
	
	
	
	public static function adzone_tpl($id = 0, $args = array())
	{
		$defaults = array(
			//'adzone_size' => '728x90'
		);
		$args = wp_parse_args($args, $defaults);
		
		$html = '';
		
		if( !empty($id))
		{
			$adzone = ADNI_CPT::load_post( $id );
			if( empty($adzone) )
				return '';

			$a = $adzone['args'];
			$rand_id = $id.'_'.rand(); // To fix conflicts with same adzones on one page.
			$transition_time = !empty($a['adzone_transition_time']) ? $a['adzone_transition_time'] : 5;
			$label = $a['cont_label'];
			$label_color = !empty($a['cont_label_color']) ? 'color:'.$a['cont_label_color'].';' : '';
			$label_pos = ' _'.$a['cont_label_pos'];
			$has_label = !empty($label) ? ' has_label' : '';
			$has_border = !empty($a['cont_border']) ? ' has_border' : '';
			$border_color = !empty($a['cont_border_color']) ? ' background:'.$a['cont_border_color'].';' : '';

			$align_class = ' _align_'.$a['align'];
			$clearfix_div = !$a['wrap_text'] ? '<div class="clear"></div>' : '';
			
			//$html.= '<div class="_ning_outer _ning_jss_zone" style="max-width:'.$a['adzone_size_w'].'px; max-height:'.$a['adzone_size_h'].'px;">';
			$html.= '<div class="_ning_outer _ning_jss_zone'.$has_label.$has_border.$align_class.'" style="max-width:'.$a['adzone_size_w'].'px; height:inherit;'.$border_color.'">';
				$html.= '<div class="_ning_label'.$label_pos.'" style="'.$label_color.'">'.$label.'</div>';
				$html.= '<div id="_ning_zone_'.$rand_id.'" class="_ning_zone_inner" style="width:'.$a['adzone_size_w'].'px; height:'.$a['adzone_size_h'].'px; position:relative;">';
					$html.= '<div u="slides" style="position:absolute; overflow:hidden; left:0px; top:0px; width:'.$a['adzone_size_w'].'px; height:'.$a['adzone_size_h'].'px;">';
						// Load banners		
						if(!empty($a['linked_banners']))
						{
							foreach($a['linked_banners'] as $i => $banner_id)
							{
								$html.= '<div class="slide_'.$i.' slide" idle="'.($transition_time*1000).'">';
									$html.= ADNI_Multi::do_shortcode('[ADNI_banner id="'.$banner_id.'" in_adzone=1 load_script=0]');
								$html.= '</div>';
							}
						}
					$html.= '</div>';
				$html.= '</div>';
			$html.= '</div>';
			$html.= $clearfix_div;
			
			$html.= '<script>';
				$html.= 'jQuery(document).ready(function($){';

					// Remove empty slides. (in case banners got filtered out using ADNI_Filters::show_hide)
					$html.= '$("#_ning_zone_'.$rand_id.'").find(".slide:empty").remove();';
					
					$html.= 'var _SlideshowTransitions_'.$rand_id.' = ['.$a['adzone_transition'].'];';
					$html.= 'var options_'.$rand_id.' = {';
						$html.= '$AutoPlay:1,';
						$html.= '$ArrowKeyNavigation:false,';
						$html.= '$DragOrientation:0,';
						$html.= '$SlideshowOptions:{';
							$html.= '$Class:$JssorSlideshowRunner$,';
							$html.= '$Transitions:_SlideshowTransitions_'.$rand_id.',';
							$html.= '$TransitionsOrder:1,';
							$html.= '$ShowLink:true';
						$html.= '}';
					$html.= '};';
					$html.= 'var _ning_slider_'.$rand_id.' = new $JssorSlider$(\'_ning_zone_'.$rand_id.'\', options_'.$rand_id.');';
							
					/*$html.= 'function SliderPositionChangeEventHandler(position, fromPosition, virtualPosition, virtualFromPosition)
					{
						console.log("changing position "+position);
						var imc_id = $(".slide_"+position).find("._dn_cont").data("id");
						console.log(imc_id);
						//window["bnr_"+imc_id+"_in_animation"]();
						
						//continuously fires while carousel sliding
						//position: current position of the carousel
						//fromPosition: previous position of the carousel
						//virtualPosition: current virtual position of the carousel
						//virtualFromPosition: previous virtual position of the carousel
					}
					_ning_slider.$On($JssorSlider$.$EVT_POSITION_CHANGE, SliderPositionChangeEventHandler);';
					*/


					//Scale slider after document ready
					$html.= 'ScaleSlider();';
					$html.= 'function ScaleSlider() {';
						$html.= 'var parentWidth = $(\'#_ning_zone_'.$rand_id.'\').parent().width();';
						$html.= 'if(parentWidth){';
							$html.= '_ning_slider_'.$rand_id.'.$ScaleWidth(parentWidth);';
						$html.= '}else{';
							$html.= 'window.setTimeout(ScaleSlider, 30);';
						$html.= '}';
						
						$html.= '$("._ning_cont").ningResponsive();';
					$html.= '}';
												
					//Scale slider while window load/resize/orientationchange.
					$html.= '$(window).bind("load", ScaleSlider);';
					$html.= '$(window).bind("resize", ScaleSlider);';
					$html.= '$(window).bind("orientationchange", ScaleSlider);';
					
				$html.= '});';
			$html.= '</script>';
		}
		
		return $html;
	}
	
	
	
	
	public static function linked_banners_box($args = array())
	{
		$defaults = array(
			'banners' => array()
		);
		$args = wp_parse_args($args, $defaults);
		//print_r($args);
		
		$html = '';
		$html.= '<ul>';
		if(!empty($args['banners']))
		{
			foreach($args['banners'] as $banner_id)
			{
				$html.= '<li>'.get_the_title($banner_id).'</li>';
			}
		}
		else
		{
			$html.= '<li>'.__('No banners linked to this adzone yet.','adn').'</li>';
		}
		$html.= '</ul>';
		
		return $html;
	}




	public static function spr_column($args = array())
	{
		$defaults = array(
			'col' => '', // spr_col-6
			'class' => '',
			'title' => '',
			'content' => '',
			'desc' => ''
		);
		$args = wp_parse_args($args, $defaults);

		$col = !empty($args['col']) ? ' '.$args['col'] : '';

		$h = '';
		$h.= '<div class="spr_column'.$col.'">';
			$h.= '<div class="input_container">';
				$h.= '<h3 class="title">'.$args['title'].'</h3>';
				$h.= '<div class="input_container_inner">';
					$h.= $args['content'];
				$h.= '</div>';
				$h.= '<span class="description bottom">'.$args['desc'].'</span>';
			$h.= '</div>';
		$h.= '</div>';

		return $h;
	}



	public static function itm_defaults()
	{
		$defaults = array(
			'title'       => '',
			'name'        => '', 
			'slug'        => '',
			'type'        => 'text',
			'id'          => '',
			'data'        => '',
			'placeholder' => '',
			'value'       => '',
			'class'       => '',
			'style'       => '',
			'script'      => '',
			'text-align'  => 'left',
			'height'      => '100px', // for textareas
			'width'       => 'auto',
			'size'        => '', // one_third
			'desc_pos'    => 'bottom', // bottom, top
			'desc'        => '',
			'content'     => '',
			'show_icon'   => 0,
			'icon'        => '',
			'icon_type'   => 'fa',
			'select_opts' => array(),
			'chk-on'      => __('ON','adn'),
			'chk-off'     => __('OFF','adn'),
			'chk_width'   => 50,
			'chk_height'  => 30,
			'chk_btn_width' => 30,
			'chk_labels_placement' => 'right',
			'chk_data-values' => '',
			'color_change' => '',
			'trigger_switch_button' => 0
		);
		
		return $defaults;
	}


	/**
	 * Tooltip
	*/
	public static function tooltip($args = array())
	{
		$defaults = array(
			'class' => '',
			'title' => '',
			'icon' => '<svg viewBox="0 0 512 512"><path fill="currentColor" d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0 6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z" class=""></path></svg>'
		);
		$args = wp_parse_args($args, $defaults);

		$t = '';
		$class = !empty($args['class']) ? ' '.$args['class'] : '';
		$t.= '<span class="ttip'.$class.'" title="'.$args['title'].'">'.$args['icon'].'</span>';

		return $t;
	}



	/**
	 * Checkbox
	 * 
	*/
	public static function checkbox($args = array())
	{
		$defaults = array(
			'title' => '',
			'tooltip' => '',
			'class' => '',
			'name' => '',
			'data' => array(),
			'value' => '',
			'checked' => '',
			'disabled' => 0,
			'hidden_input' => 0 // to return 0 when check box is unchecked. will only work when a value is provided.
		);
		$args = wp_parse_args($args, $defaults);

		$check = !empty($args['checked']) ? ' checked="checked"' : '';
		$disabled = !empty($args['disabled']) ? ' disabled="disabled"' : '';
		$disabled_class = !empty($args['disabled']) ? ' disabled' : '';
		$data_string = ADNI_Main::create_data_attributes($args['data']);
		$name = !empty($args['name']) ? ' name="'.$args['name'].'"' : '';
		$value = $args['value'] !== '' ? ' value="'.$args['value'].'"' : '';

		$h = '';
		$h.= '<label class="spr_check_container'.$disabled_class.'">';
			$h.= $args['title'];
			$h.= !empty($args['tooltip']) ? self::tooltip(array('class' => '_dn_quest_tooltip', 'title' => $args['tooltip'])) : '';
			$h.= $args['hidden_input'] ? '<input type="hidden" value="0"'.$name.' />' : '';  
			$h.= '<input class="'.$args['class'].'"'.$data_string.$name.$value.' type="checkbox"'.$check.$disabled.'>';
	  		$h.= '<span class="checkmark"></span>';
		$h.= '</label>';

		return $h;
	}



	/**
	 * SWITCH Button
	 * 
	*/
	public static function switch_btn($args = array())
	{
		$defaults = array(
			'id' => '',
			'title' => '',
			'tooltip' => '',
			'class' => '',
			'name' => '',
			'data' => array(),
			'value' => '',
			'checked' => '',
			'disabled' => 0,
			'chk-on' => __('ON','adn'),
			'chk-off' => __('OFF','adn'),
			'chk-high' => 0, // input_h, class to make the switch btn high
			'column' => array(),
			'hidden_input' => 0 // to return 0 when check box is unchecked. will only work when a value is provided.
		);
		$args = wp_parse_args($args, $defaults);

		$id = !empty($args['id']) ? ' id="'.$args['id'].'"' : '';
		$high = !empty($args['chk-high']) ? ' input_h' : '';
		//$checked = !empty($args['checked']) ? $args['checked'] : $args['value'];
		//$check = !empty($checked) ? ' checked="checked"' : '';
		$check = !empty($args['checked']) ? ' checked="checked"' : '';
		$disabled = !empty($args['disabled']) ? ' disabled="disabled"' : '';
		$disabled_class = !empty($args['disabled']) ? ' disabled' : '';
		$data_string = ADNI_Main::create_data_attributes($args['data']);
		$name = !empty($args['name']) ? ' name="'.$args['name'].'"' : '';
		$value = $args['value'] !== '' ? ' value="'.$args['value'].'"' : '';
		$class = !empty($args['class']) ? ' '.$args['class'] : '';

		$h = '';
		$h.= '<label class="switch switch-slide small'.$high.' ttip" title="'.$args['title'].'">';
			$h.= $args['hidden_input'] ? '<input type="hidden" value="0"'.$name.' />' : '';  
			$h.= '<input class="switch-input'.$class.'" type="checkbox" '.$id.$name.$value.$check.$disabled.' />
			<span class="switch-label" data-on="'.$args['chk-on'].'" data-off="'.$args['chk-off'].'"></span> 
			<span class="switch-handle"></span>
		</label>';

		if( !empty($args['column']))
		{
			$col_def = array(
				'size' => '',
				'desc' => ''
			);
			$col_args = wp_parse_args($args['column'], $col_def);

			return self::spr_column(array(
				'col' => !empty($col_args['size']) ? 'spr_'.$col_args['size'] : '',
				'title' => $args['title'],
				'desc' => $col_args['desc'],
				'content' => $h
			));
		}

		return $h;
	}
	


	/** 
	 * INPUT CONTAINER
	 *
	 */
	public static function inpt_cont($args = array())
	{
		$defaults = self::itm_defaults();
		$args = wp_parse_args( $args, $defaults );
		
		$class = !empty($args['class']) ? ' class="'.$args['class'].'"' : '';
		$id = !empty($args['id']) ? ' id="'.$args['id'].'"' : '';
		$name = !empty($args['name']) ? ' name="'.$args['name'].'"' : '';
		$placeholder = !empty($args['placeholder']) ? ' placeholder="'.$args['placeholder'].'"' : '';
		$input_style = !empty($args['width']) ? ' style="width:'.$args['width'].';"' : '';
		
		$html = '';
		$html.= '<span class="input_container_box '.$args['size'].'">';
			$html.= !empty($args['title']) ? '<h3 class="title">'.$args['title'].'</h3>' : '';
			$html.= $args['desc_pos'] == 'top' && !empty($args['desc']) ? '<span class="description top">'.$args['desc'].'</span>' : '';
			$html.= '<div style="position: relative;">';
				$html.= '<input type="'.$args['type'].'"'.$class.$id.$input_style.$name.$placeholder.' value="'.$args['value'].'" />';
				$html.= $args['show_icon'] ? '<i class="input_icon fa fa-'.$args['icon'].'" aria-hidden="true"></i>' : '';
			$html.= '</div>';
			$html.= $args['desc_pos'] == 'bottom' && !empty($args['desc']) ? '<span class="description bottom">'.$args['desc'].'</span>' : '';
		$html.= '</span>';
		
		return $html;
	} 
	
	
	
	
	/** 
	 * TEXTAREA
	 *
	 */
	public static function textarea_cont($args = array())
	{
		$defaults = self::itm_defaults();
		$args = wp_parse_args( $args, $defaults );

		$data = !empty($args['data']) ? ' '.$args['data'] : '';
		
		$html = '';
		$html.= '<span class="input_container_box '.$args['size'].'">';
			$html.= !empty($args['title']) ? '<h3 class="title">'.$args['title'].'</h3>' : '';
			$html.= $args['desc_pos'] == 'top' && !empty($args['desc']) ? '<span class="description top">'.$args['desc'].'</span>' : '';
			$html.= '<div style="position: relative;">';
				$html.= '<textarea type="text" class="'.$args['class'].'" id="'.$args['id'].'" name="'.$args['name'].'"'.$data.' placeholder="'.$args['placeholder'].'" style="width:100%; height:'.$args['height'].';">'.$args['value'].'</textarea>';
			$html.= '</div>';
			$html.= $args['desc_pos'] == 'bottom' && !empty($args['desc']) ? '<span class="description bottom">'.$args['desc'].'</span>' : '';
		$html.= '</span>';
		
		return $html;
	} 








	public static function auto_positioning_template($id, $adzone)
	{
		$auto_pos = ADNI_Main::auto_positioning();
		$type = $adzone['args']['type'];
		$save_name = 'save_'.$type;
		$h = '';
		//$h.= '<pre>'.print_r($adzone,true).'</pre>';
		$h.= '<div class="spr_column-inner left_column">
			<div class="spr_wrapper">
				<div class="option_box">
					<div class="info_header">
						<span class="nr">3</span>
						<span class="text">'.__('Auto Positioning','adn').'</span>
						<input type="submit" value="'.sprintf(__('Save %s','adn'), ucfirst($type)).'" class="button-primary" name="'.$save_name.'" style="width:auto;float:right;margin:8px;">
					</div>
					<!-- end .info_header -->
					
					<div class="spr_column">
						<div class="spr_column-inner">
							<div class="spr_wrapper">
								<div class="input_container">
									<p>
										'.__('Placements are physically places on your website. Banners and AD Zones can be added to these places automatically.','adn').'
									</p>
								</div>
							</div>
						</div>
					</div>

					
					
					<div class="spr_column">
						<div class="spr_column-inner">
							<div class="spr_wrapper">
							<div class="sep_line" style="margin:0 0 15px 0;"><span><strong>'.__('Default AD placements','adn').'</strong></span></div>
							<div class="input_container">';

								$val = !empty($adzone['args']['positioning']) ? $adzone['args']['positioning'] : '';
								$h.= '<input class="adning_auto_position" type="hidden" value="'.$val.'" name="positioning" />
								
								<h3 class="title">'.__('','adn').'</h3>
								<div class="input_container_inner">
									
									<div class="clear">
										<!-- Manuall -->
										<div class="spot_box ttip" data-pos="" data-custom="0" title="'.__('Manually','adn').'">
											<div class="ad_cont" style="width:100%;position:relative;">
												<div class="ad_box" style="background:transparent;text-align: center;margin: 26px 0;font-size: 10px;">[adning]</div>
											</div>
										</div>';

										$selected = $adzone['args']['positioning'] === 'above_content' ? ' selected' : '';
										$h.= '<div class="spot_box ttip'.$selected.'" data-pos="above_content" data-custom="0" title="'.__('Above Content','adn').'">
											<div class="ad_cont" style="width:100%;height:17px;">
												<div class="ad_box" style="width:95%;height:15px;margin: 17px auto;"></div>
											</div>
											<svg viewBox="0 0 402.532 334.177"> <path fill="#D6D6D6" d="M393.671,17.72c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,11.391,393.671,14.225,393.671,17.72L393.671,17.72z"></path> <path fill="#D6D6D6" d="M393.671,44.732c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,38.403,393.671,41.237,393.671,44.732L393.671,44.732z"></path> <path fill="#D6D6D6" d="M393.671,71.885c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,65.556,393.671,68.389,393.671,71.885L393.671,71.885z"></path> <path fill="#D6D6D6" d="M393.671,99.999c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,93.67,393.671,96.503,393.671,99.999L393.671,99.999z"></path> <path fill="#D6D6D6" d="M393.671,127.011c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,120.682,393.671,123.516,393.671,127.011L393.671,127.011z"></path> <path fill="#D6D6D6" d="M393.671,154.163c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.495,2.833-6.328,6.329-6.328h372.152C390.837,147.835,393.671,150.668,393.671,154.163L393.671,154.163z"></path> <path fill="#D6D6D6" d="M393.671,182.288c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,175.959,393.671,178.792,393.671,182.288L393.671,182.288z"></path> <path fill="#D6D6D6" d="M393.671,209.3c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,202.971,393.671,205.805,393.671,209.3L393.671,209.3z"></path> <path fill="#D6D6D6" d="M393.671,236.453c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,230.124,393.671,232.957,393.671,236.453L393.671,236.453z"></path> <path fill="#D6D6D6" d="M393.671,264.567c0,3.495-2.834,6.328-6.329,6.328H15.19c-3.496,0-6.329-2.833-6.329-6.328l0,0 c0-3.496,2.833-6.33,6.329-6.33h372.152C390.837,258.237,393.671,261.071,393.671,264.567L393.671,264.567z"></path> <path fill="#D6D6D6" d="M393.671,291.579c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,285.25,393.671,288.083,393.671,291.579L393.671,291.579z"></path> <path fill="#D6D6D6" d="M393.671,318.731c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.328,6.329-6.328h372.152C390.837,312.403,393.671,315.235,393.671,318.731L393.671,318.731z"></path> <path display="none" opacity="0.4" d="M412.595,329.455c0,6.627-5.373,12-12,12h-403c-6.627,0-12-5.373-12-12v-329 c0-6.627,5.373-12,12-12h403c6.627,0,12,5.373,12,12V329.455z"></path></svg>
										</div>';

										$selected = $adzone['args']['positioning'] === 'inside_content' ? ' selected' : '';
										$h.= '<div class="spot_box ttip'.$selected.'" data-pos="inside_content" data-custom="1" title="'.__('Inside Content','adn').'">
											<div class="ad_cont" style="width:100%;height:17px;background:transparent;">
												<div class="ad_box" style="width:95%;height:15px;margin: 18px auto;"></div>
											</div>
											<svg viewBox="0 0 402.532 334.177"> <path fill="#D6D6D6" d="M393.671,17.72c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,11.391,393.671,14.225,393.671,17.72L393.671,17.72z"></path> <path fill="#D6D6D6" d="M393.671,44.732c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,38.403,393.671,41.237,393.671,44.732L393.671,44.732z"></path> <path fill="#D6D6D6" d="M393.671,71.885c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,65.556,393.671,68.389,393.671,71.885L393.671,71.885z"></path> <path fill="#D6D6D6" d="M393.671,99.999c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,93.67,393.671,96.503,393.671,99.999L393.671,99.999z"></path> <path fill="#D6D6D6" d="M393.671,127.011c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,120.682,393.671,123.516,393.671,127.011L393.671,127.011z"></path> <path fill="#D6D6D6" d="M393.671,154.163c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.495,2.833-6.328,6.329-6.328h372.152C390.837,147.835,393.671,150.668,393.671,154.163L393.671,154.163z"></path> <path fill="#D6D6D6" d="M393.671,182.288c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,175.959,393.671,178.792,393.671,182.288L393.671,182.288z"></path> <path fill="#D6D6D6" d="M393.671,209.3c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,202.971,393.671,205.805,393.671,209.3L393.671,209.3z"></path> <path fill="#D6D6D6" d="M393.671,236.453c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,230.124,393.671,232.957,393.671,236.453L393.671,236.453z"></path> <path fill="#D6D6D6" d="M393.671,264.567c0,3.495-2.834,6.328-6.329,6.328H15.19c-3.496,0-6.329-2.833-6.329-6.328l0,0 c0-3.496,2.833-6.33,6.329-6.33h372.152C390.837,258.237,393.671,261.071,393.671,264.567L393.671,264.567z"></path> <path fill="#D6D6D6" d="M393.671,291.579c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,285.25,393.671,288.083,393.671,291.579L393.671,291.579z"></path> <path fill="#D6D6D6" d="M393.671,318.731c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.328,6.329-6.328h372.152C390.837,312.403,393.671,315.235,393.671,318.731L393.671,318.731z"></path> <path display="none" opacity="0.4" d="M412.595,329.455c0,6.627-5.373,12-12,12h-403c-6.627,0-12-5.373-12-12v-329 c0-6.627,5.373-12,12-12h403c6.627,0,12,5.373,12,12V329.455z"></path></svg>
										</div>';

										$selected = $adzone['args']['positioning'] === 'below_content' ? ' selected' : '';
										$h.= '<div class="spot_box ttip'.$selected.'" data-pos="below_content" data-custom="0" title="'.__('Below Content','adn').'">
											<div class="ad_cont" style="width:100%;height:30px;bottom:0;">
												<div class="ad_box" style="width:95%;height:15px;margin:0 auto;"></div>
											</div>
											<svg viewBox="0 0 402.532 334.177"> <path fill="#D6D6D6" d="M393.671,17.72c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,11.391,393.671,14.225,393.671,17.72L393.671,17.72z"></path> <path fill="#D6D6D6" d="M393.671,44.732c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,38.403,393.671,41.237,393.671,44.732L393.671,44.732z"></path> <path fill="#D6D6D6" d="M393.671,71.885c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,65.556,393.671,68.389,393.671,71.885L393.671,71.885z"></path> <path fill="#D6D6D6" d="M393.671,99.999c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,93.67,393.671,96.503,393.671,99.999L393.671,99.999z"></path> <path fill="#D6D6D6" d="M393.671,127.011c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,120.682,393.671,123.516,393.671,127.011L393.671,127.011z"></path> <path fill="#D6D6D6" d="M393.671,154.163c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.495,2.833-6.328,6.329-6.328h372.152C390.837,147.835,393.671,150.668,393.671,154.163L393.671,154.163z"></path> <path fill="#D6D6D6" d="M393.671,182.288c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,175.959,393.671,178.792,393.671,182.288L393.671,182.288z"></path> <path fill="#D6D6D6" d="M393.671,209.3c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,202.971,393.671,205.805,393.671,209.3L393.671,209.3z"></path> <path fill="#D6D6D6" d="M393.671,236.453c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,230.124,393.671,232.957,393.671,236.453L393.671,236.453z"></path> <path fill="#D6D6D6" d="M393.671,264.567c0,3.495-2.834,6.328-6.329,6.328H15.19c-3.496,0-6.329-2.833-6.329-6.328l0,0 c0-3.496,2.833-6.33,6.329-6.33h372.152C390.837,258.237,393.671,261.071,393.671,264.567L393.671,264.567z"></path> <path fill="#D6D6D6" d="M393.671,291.579c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,285.25,393.671,288.083,393.671,291.579L393.671,291.579z"></path> <path fill="#D6D6D6" d="M393.671,318.731c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.328,6.329-6.328h372.152C390.837,312.403,393.671,315.235,393.671,318.731L393.671,318.731z"></path> <path display="none" opacity="0.4" d="M412.595,329.455c0,6.627-5.373,12-12,12h-403c-6.627,0-12-5.373-12-12v-329 c0-6.627,5.373-12,12-12h403c6.627,0,12,5.373,12,12V329.455z"></path></svg>
										</div>';

										$selected = $adzone['args']['positioning'] === 'popup' ? ' selected' : '';
										$h.= '<div class="spot_box ttip'.$selected.'" data-pos="popup" data-custom="1" title="'.__('Popup','adn').'">
											<div class="ad_cont" style="width: 100%;height: 80px;background: rgba(0, 0, 0, 0.25);">
												<div class="ad_box" style="width: 50%;position: absolute;top: 20px;left: 20px;height: 25px;"></div>
											</div>
											<svg viewBox="0 0 402.532 334.177"> <path fill="#D6D6D6" d="M393.671,17.72c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,11.391,393.671,14.225,393.671,17.72L393.671,17.72z"></path> <path fill="#D6D6D6" d="M393.671,44.732c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,38.403,393.671,41.237,393.671,44.732L393.671,44.732z"></path> <path fill="#D6D6D6" d="M393.671,71.885c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,65.556,393.671,68.389,393.671,71.885L393.671,71.885z"></path> <path fill="#D6D6D6" d="M393.671,99.999c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,93.67,393.671,96.503,393.671,99.999L393.671,99.999z"></path> <path fill="#D6D6D6" d="M393.671,127.011c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,120.682,393.671,123.516,393.671,127.011L393.671,127.011z"></path> <path fill="#D6D6D6" d="M393.671,154.163c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.495,2.833-6.328,6.329-6.328h372.152C390.837,147.835,393.671,150.668,393.671,154.163L393.671,154.163z"></path> <path fill="#D6D6D6" d="M393.671,182.288c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,175.959,393.671,178.792,393.671,182.288L393.671,182.288z"></path> <path fill="#D6D6D6" d="M393.671,209.3c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,202.971,393.671,205.805,393.671,209.3L393.671,209.3z"></path> <path fill="#D6D6D6" d="M393.671,236.453c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,230.124,393.671,232.957,393.671,236.453L393.671,236.453z"></path> <path fill="#D6D6D6" d="M393.671,264.567c0,3.495-2.834,6.328-6.329,6.328H15.19c-3.496,0-6.329-2.833-6.329-6.328l0,0 c0-3.496,2.833-6.33,6.329-6.33h372.152C390.837,258.237,393.671,261.071,393.671,264.567L393.671,264.567z"></path> <path fill="#D6D6D6" d="M393.671,291.579c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,285.25,393.671,288.083,393.671,291.579L393.671,291.579z"></path> <path fill="#D6D6D6" d="M393.671,318.731c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.328,6.329-6.328h372.152C390.837,312.403,393.671,315.235,393.671,318.731L393.671,318.731z"></path> <path display="none" opacity="0.4" d="M412.595,329.455c0,6.627-5.373,12-12,12h-403c-6.627,0-12-5.373-12-12v-329 c0-6.627,5.373-12,12-12h403c6.627,0,12,5.373,12,12V329.455z"></path></svg>
										</div>';

										$selected = $adzone['args']['positioning'] === 'cornerpeel' ? ' selected' : '';
										$h.= '<div class="spot_box ttip'.$selected.'" data-pos="cornerpeel" data-custom="0" title="'.__('Corner Peel','adn').'">
											<div class="ad_cont" style="width: 100%;height: 80px;background:transparent;">
												<div class="ad_box" style="width: 25px;position: absolute;top: -8px;right: -8px;height: 25px;background: #FFF;"></div>
												<div class="peel" style="width: 25px;height: 25px;background: #c7ff00;position: absolute;right: -12px;top: -12px;-ms-transform: rotate(20deg);-webkit-transform: rotate(20deg);transform: rotate(45deg);"></div>
											</div>
											<svg viewBox="0 0 402.532 334.177"> <path fill="#D6D6D6" d="M393.671,17.72c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,11.391,393.671,14.225,393.671,17.72L393.671,17.72z"></path> <path fill="#D6D6D6" d="M393.671,44.732c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,38.403,393.671,41.237,393.671,44.732L393.671,44.732z"></path> <path fill="#D6D6D6" d="M393.671,71.885c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,65.556,393.671,68.389,393.671,71.885L393.671,71.885z"></path> <path fill="#D6D6D6" d="M393.671,99.999c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,93.67,393.671,96.503,393.671,99.999L393.671,99.999z"></path> <path fill="#D6D6D6" d="M393.671,127.011c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,120.682,393.671,123.516,393.671,127.011L393.671,127.011z"></path> <path fill="#D6D6D6" d="M393.671,154.163c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.495,2.833-6.328,6.329-6.328h372.152C390.837,147.835,393.671,150.668,393.671,154.163L393.671,154.163z"></path> <path fill="#D6D6D6" d="M393.671,182.288c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,175.959,393.671,178.792,393.671,182.288L393.671,182.288z"></path> <path fill="#D6D6D6" d="M393.671,209.3c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,202.971,393.671,205.805,393.671,209.3L393.671,209.3z"></path> <path fill="#D6D6D6" d="M393.671,236.453c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,230.124,393.671,232.957,393.671,236.453L393.671,236.453z"></path> <path fill="#D6D6D6" d="M393.671,264.567c0,3.495-2.834,6.328-6.329,6.328H15.19c-3.496,0-6.329-2.833-6.329-6.328l0,0 c0-3.496,2.833-6.33,6.329-6.33h372.152C390.837,258.237,393.671,261.071,393.671,264.567L393.671,264.567z"></path> <path fill="#D6D6D6" d="M393.671,291.579c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,285.25,393.671,288.083,393.671,291.579L393.671,291.579z"></path> <path fill="#D6D6D6" d="M393.671,318.731c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.328,6.329-6.328h372.152C390.837,312.403,393.671,315.235,393.671,318.731L393.671,318.731z"></path> <path display="none" opacity="0.4" d="M412.595,329.455c0,6.627-5.373,12-12,12h-403c-6.627,0-12-5.373-12-12v-329 c0-6.627,5.373-12,12-12h403c6.627,0,12,5.373,12,12V329.455z"></path></svg>
										</div>';

										if( $adzone['args']['type'] === 'banner' ){
											$selected = $adzone['args']['positioning'] === 'bg_takeover' ? ' selected' : '';
											$h.= '<div class="spot_box ttip'.$selected.'" data-pos="bg_takeover" data-custom="1" title="'.__('Background Takeover AD','adn').'">
												<div class="ad_cont" style="width: 100%;height: 80px;background: rgba(0, 0, 0, 0);">
													<div class="ad_box" style="width: 12px;position: absolute;top: 0;left: 0;height: 100%;background: #c7ff00;border-right: solid #f9f9f9;"></div>
													<div class="ad_box" style="width: 12px;height: 100%;background: #c7ff00;position: absolute;right: 0;top: 0;border-left: solid #f9f9f9;"></div>
												</div>
												<svg viewBox="0 0 402.532 334.177"> <path fill="#D6D6D6" d="M393.671,17.72c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,11.391,393.671,14.225,393.671,17.72L393.671,17.72z"></path> <path fill="#D6D6D6" d="M393.671,44.732c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,38.403,393.671,41.237,393.671,44.732L393.671,44.732z"></path> <path fill="#D6D6D6" d="M393.671,71.885c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,65.556,393.671,68.389,393.671,71.885L393.671,71.885z"></path> <path fill="#D6D6D6" d="M393.671,99.999c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,93.67,393.671,96.503,393.671,99.999L393.671,99.999z"></path> <path fill="#D6D6D6" d="M393.671,127.011c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,120.682,393.671,123.516,393.671,127.011L393.671,127.011z"></path> <path fill="#D6D6D6" d="M393.671,154.163c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.495,2.833-6.328,6.329-6.328h372.152C390.837,147.835,393.671,150.668,393.671,154.163L393.671,154.163z"></path> <path fill="#D6D6D6" d="M393.671,182.288c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,175.959,393.671,178.792,393.671,182.288L393.671,182.288z"></path> <path fill="#D6D6D6" d="M393.671,209.3c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,202.971,393.671,205.805,393.671,209.3L393.671,209.3z"></path> <path fill="#D6D6D6" d="M393.671,236.453c0,3.496-2.834,6.329-6.329,6.329H15.19c-3.496,0-6.329-2.833-6.329-6.329l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,230.124,393.671,232.957,393.671,236.453L393.671,236.453z"></path> <path fill="#D6D6D6" d="M393.671,264.567c0,3.495-2.834,6.328-6.329,6.328H15.19c-3.496,0-6.329-2.833-6.329-6.328l0,0 c0-3.496,2.833-6.33,6.329-6.33h372.152C390.837,258.237,393.671,261.071,393.671,264.567L393.671,264.567z"></path> <path fill="#D6D6D6" d="M393.671,291.579c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.329,6.329-6.329h372.152C390.837,285.25,393.671,288.083,393.671,291.579L393.671,291.579z"></path> <path fill="#D6D6D6" d="M393.671,318.731c0,3.496-2.834,6.33-6.329,6.33H15.19c-3.496,0-6.329-2.834-6.329-6.33l0,0 c0-3.496,2.833-6.328,6.329-6.328h372.152C390.837,312.403,393.671,315.235,393.671,318.731L393.671,318.731z"></path> <path display="none" opacity="0.4" d="M412.595,329.455c0,6.627-5.373,12-12,12h-403c-6.627,0-12-5.373-12-12v-329 c0-6.627,5.373-12,12-12h403c6.627,0,12,5.373,12,12V329.455z"></path></svg>
											</div>';
										}

									$h.= '</div>
									
									<span class="description bottom">'.__('','adn').'</span>
								</div>
							</div>
						</div>
					</div>
					<!-- end .spr_column -->

					<div class="spr_column">
						<div class="spr_column-inner">
							<div class="spr_wrapper custom_placement_settings_cont">
								<div class="sep_line" style="margin:0 0 15px 0;"><span><strong>'.__('Custom Placement Settings','adn').'</strong></span></div>';
									
									// Inside Content - Settings
									$h.= '<div class="clear custom_box option_inside_content">';
										$h.= '<div class="input_container">
											<h2 class="title">'.__('Inside Content, Settings','adn').'</h2>
										</div>';
										$h.= '<div class="spr_column spr_col-4 left_column">';
											$h.= '<div class="input_container">
												<h3 class="title">'.__('Insert after X Paragraphs (int)','adn').'</h3>
												<div class="input_container_inner">';
													
													$after_x_p = '';
													if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
													{
														$after_x_p = array_key_exists('position_after_x_p', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['position_after_x_p'] : '';
													}
													
													$h.= '<input 
														type="text" 
														class="" 
														name="position_after_x_p" 
														value="'.$after_x_p.'" 
														placeholder="'.__('2','adn').'" />
													<i class="input_icon fa fa-pencil" aria-hidden="true"></i>
												</div>
												<span class="description bottom">'.__('Select after how many paragraphs the ad should show.','adn').'</span>
											</div>';
										$h.= '</div>';
										// end .spr_column 
									$h.= '</div>';
									

									// Popup - Settings
									$h.= '<div class="clear custom_box option_popup">';
										$h.= '<div class="input_container">
											<h2 class="title">'.__('Popup, Settings','adn').'</h2>
										</div>';
										$h.= '<div class="input_container">
											<h3 class="title">'.__('Popup Options','adn').'</h3>
										</div>';
										$h.= '<div class="spr_column spr_col-3 left_column">';
											$h.= '<div class="input_container">
												<h3 class="title">'.__('Width','adn').'</h3>
												<div class="input_container_inner">';
													$popup_width = '';
													if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
													{
														$popup_width = array_key_exists('popup_width', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['popup_width'] : '';
													}
													
													$h.= '<input 
														type="text" 
														class="" 
														name="popup_width" 
														value="'.$popup_width.'" 
														placeholder="" />';
													$h.= '<i class="input_icon fa fa-arrows-h" aria-hidden="true"></i>';
													
												$h.= '</div>
												<span class="description bottom">'.__('Width of the popup. (Leave empty to use banner size)','adn').'</span>
											</div>';
										$h.= '</div>';
										// end .spr_column 
										$h.= '<div class="spr_column spr_col-3">';
											$h.= '<div class="input_container">
												<h3 class="title">'.__('Height','adn').'</h3>
												<div class="input_container_inner">';
													$popup_height = '';
													if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
													{
														$popup_height = array_key_exists('popup_height', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['popup_height'] : '';
													}
													
													$h.= '<input 
														type="text" 
														class="" 
														name="popup_height" 
														value="'.$popup_height.'" 
														placeholder="" />';
													$h.= '<i class="input_icon fa fa-arrows-v" aria-hidden="true"></i>';
													
												$h.= '</div>
												<span class="description bottom">'.__('Height of the popup. (Leave empty to use banner size)','adn').'</span>
											</div>';
										$h.= '</div>';
										// end .spr_column
										$h.= '<div class="spr_column spr_col-3">';
											$h.= '<div class="input_container">
												<h3 class="title">'.__('Bg Color','adn').'</h3>
												<div class="input_container_inner">';
													$popup_bg_color = '';
													if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
													{
														$popup_bg_color = array_key_exists('popup_bg_color', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['popup_bg_color'] : '';
													}
													
													$h.= '<input id="popup_bg_color" name="popup_bg_color" type="text" value="'.$popup_bg_color.'">';
													$h.= "<script>jQuery(document).ready(function($){ $('#popup_bg_color').coloringPick(); });</script>";
										
												$h.= '</div>
												<span class="description bottom">'.__('Popup background color.','adn').'</span>
											</div>';
										$h.= '</div>';
										// end .spr_column  
										$h.= '<div class="spr_column spr_col-3">';
											$h.= '<div class="input_container">
												<h3 class="title">'.__('Shadow Color','adn').'</h3>
												<div class="input_container_inner">';
													$popup_shadow_color = '';
													if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
													{
														$popup_shadow_color = array_key_exists('popup_shadow_color', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['popup_shadow_color'] : '';
													}
													
													$h.= '<input id="popup_shadow_color" name="popup_shadow_color" type="text" value="'.$popup_shadow_color.'">';
													$h.= "<script>jQuery(document).ready(function($){ $('#popup_shadow_color').coloringPick({'picker':'solid','picker_changeable':false}); });</script>";
										
												$h.= '</div>
												<span class="description bottom">'.__('Popup shadow color.','adn').'</span>
											</div>';
										$h.= '</div>';
										// end .spr_column 
										$h.= '<div class="spr_column spr_col-3 clearFix">';
											$h.= '<div class="input_container">
												<h3 class="title">'.__('Overlay Color','adn').'</h3>
												<div class="input_container_inner">';
													$popup_overlay_color = '';
													if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
													{
														$popup_overlay_color = array_key_exists('popup_overlay_color', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['popup_overlay_color'] : '';
													}
													
													$h.= '<input id="popup_overlay_color" name="popup_overlay_color" type="text" value="'.$popup_overlay_color.'">';
													$h.= "<script>jQuery(document).ready(function($){ $('#popup_overlay_color').coloringPick(); });</script>";
										
												$h.= '</div>
												<span class="description bottom">'.__('Background overlay color for the popup.','adn').'</span>
											</div>';
										$h.= '</div>';
										// end .spr_column 
										$h.= '<div class="spr_column spr_col-6">';
											$h.= '<div class="input_container">
												<h3 class="title">'.__('Custom Attributes','adn').'</h3>
												<div class="input_container_inner">';
													$popup_custom_json = '';
													if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
													{
														$popup_custom_json = array_key_exists('popup_custom_json', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['popup_custom_json'] : '';
													}
													
													$h.= '<input 
														type="text" 
														class="" 
														name="popup_custom_json" 
														value="'.str_replace('"',"'", stripslashes($popup_custom_json)).'" 
														placeholder="animatedIn:\'tada\'" />';
													$h.= '<i class="input_icon fa fa-pencil" aria-hidden="true"></i>';
													
												$h.= '</div>
												<span class="description bottom">'.sprintf(__('Add custom %s attributes.','adn'), '<a href="http://modaljs.com/installation/#attributes" target="_blank">ModalJS</a>').'</span>
											</div>';
										$h.= '</div>';
										// end .spr_column 

										// Popup Cookie settings
										$h.= '<div class="clearFix">';
											$h.= '<div class="input_container">
												<h3 class="title">'.__('Popup Cookie Settings','adn').'</h3>
											</div>';

											$h.= '<div class="spr_column spr_col-3 left_column">';
												$h.= '<div class="input_container">
													<div class="input_container_inner">';
														$popup_cookie_value = '';
														if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
														{
															$popup_cookie_value = array_key_exists('popup_cookie_value', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['popup_cookie_value'] : '';
														}
														
														$h.= '<input 
															type="text" 
															class="" 
															name="popup_cookie_value" 
															value="'.$popup_cookie_value.'" 
															placeholder="0" />';
														//$h.= '<svg viewBox="0 0 512 512" class="input_icon"><path fill="currentColor" d="M204.3 5C104.9 24.4 24.8 104.3 5.2 203.4c-37 187 131.7 326.4 258.8 306.7 41.2-6.4 61.4-54.6 42.5-91.7-23.1-45.4 9.9-98.4 60.9-98.4h79.7c35.8 0 64.8-29.6 64.9-65.3C511.5 97.1 368.1-26.9 204.3 5zM96 320c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32zm32-128c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32zm128-64c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32zm128 64c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32z"></path></svg>';
														
													$h.= '</div>
													<span class="description bottom">'.__('Numeric value in how long the cookie should expire.','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column 
											$h.= '<div class="spr_column spr_col-3">';
												$h.= '<div class="input_container">
													<div class="input_container_inner">';
														$popup_cookie_type = '';
														if( !empty($auto_pos) && array_key_exists($id, $auto_pos) )
														{
															$popup_cookie_type = array_key_exists('popup_cookie_type', $auto_pos[$id]['custom']) ? $auto_pos[$id]['custom']['popup_cookie_type'] : '';
														}
														
														$h.= '<select name="popup_cookie_type">';
															$h.= '<option value="minutes"'.selected( $popup_cookie_type, 'minutes', false ).'>'.__('Minutes','adn').'</option>';
															$h.= '<option value="days"'.selected( $popup_cookie_type, 'days', false ).'>'.__('Days','adn').'</option>';
														$h.= '</select>';

													$h.= '</div>
													<span class="description bottom">'.__('Set a cookie to only show the popup every x amount of time.','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column 

										$h.= '</div>';
									$h.= '</div>';
									// end popup settings

									// Background Takeover AD - Settings
									if( $adzone['args']['type'] === 'banner' ){
										$h.= '<div class="clear custom_box option_bg_takeover">';
											$h.= '<div class="input_container">
												<h2 class="title">'.__('Background Takeover, Settings','adn').'</h2>
											</div>';
											$h.= '<div class="spr_column spr_col-4 left_column">';
												$h.= '<div class="input_container">
													<h3 class="title">'.__('Background Takover Image','adn').'</h3>
													<div class="input_container_inner">';
														
														$h.= '<input 
															type="text" 
															class="bg_takeover_prev_obj"
															id="bg_takeover_src"
															name="bg_takeover_src" 
															value="'.$adzone['args']['bg_takeover_src'].'" 
															placeholder="'.__('','adn').'" />
														<i class="input_icon fa fa-pencil" aria-hidden="true"></i>

														<div id="BGADUploader" class="box" style="border:dashed 1px #d7d7d7;border-radius:3px;padding:15px 5px;background: #FFF;" method="post" action="'.ADNI_AJAXURL.'" enctype="multipart/form-data"></div>
													</div>
													<span class="description bottom">'.__('Upload or Insert the background takeover image URL (JPG, PNG, GIF, SVG).','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column
											$h.= '<div class="spr_column spr_col-4">';
												$h.= '<div class="input_container">
													<h3 class="title">'.__('Background Container','adn').'</h3>
													<div class="input_container_inner">';
														
														$h.= '<input 
															type="text" 
															class="" 
															id="bg_takeover_bg_container"
															name="bg_takeover_bg_container" 
															value="'.$adzone['args']['bg_takeover_bg_container'].'" 
															placeholder="'.__('body','adn').'" />
														<i class="input_icon fa fa-pencil" aria-hidden="true"></i>
													</div>
													<span class="description bottom">'.__('The object/class/ID of the background container. (default body).','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column  
											$h.= '<div class="spr_column spr_col-4">';
												$h.= '<div class="input_container">
													<h3 class="title">'.__('Content Container','adn').'</h3>
													<div class="input_container_inner">';
														
														$h.= '<input 
															type="text" 
															class="" 
															id="bg_takeover_content_container"
															name="bg_takeover_content_container" 
															value="'.$adzone['args']['bg_takeover_content_container'].'" 
															placeholder="'.__('.content','adn').'" />
														<i class="input_icon fa fa-pencil" aria-hidden="true"></i>
													</div>
													<span class="description bottom">'.__('The class/ID of the main content container for the background AD to wrap around.','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column  
											$h.= '<div class="spr_column spr_col-4">';
												$h.= '<div class="input_container">
													<h3 class="title">'.__('Top Skin (optional)','adn').'</h3>
													<div class="input_container_inner">';
														$h.= '<input 
															type="text" 
															class="bg_takeover_prev_obj"
															id="bg_takeover_top_skin"
															name="bg_takeover_top_skin" 
															value="'.$adzone['args']['bg_takeover_top_skin'].'" 
															placeholder="'.__('100px','adn').'" />
														<i class="input_icon fa fa-pencil" aria-hidden="true"></i>
													</div>
													<span class="description bottom">'.__('Show top of the background takeover ad. This will add a top margin to the container.','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column 
											$h.= '<div class="spr_column spr_col-4">';
												$h.= '<div class="input_container">
													<h3 class="title">'.__('Background Position','adn').'</h3>
													<div class="input_container_inner">';
														$h.= '<select id="ADNI_label_pos" name="bg_takeover_position">
															<option value="absolute"'.selected( $adzone['args']['bg_takeover_position'], 'absolute', false ).'>'.__('Scroll with page','adn').'</option>
															<option value="fixed"'.selected( $adzone['args']['bg_takeover_position'], 'fixed', false ).'>'.__('Fixed','adn').'</option>
														</select>
													</div>
													<span class="description bottom">'.__('Background takeover position (fixed or scrolling with the page).','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column 
											$h.= '<div class="spr_column spr_col-4">';
												$h.= '<div class="input_container">
													<h3 class="title">'.__('Background Color','adn').'</h3>
													<div class="input_container_inner">';
														
														$h.= '<input class="bg_takeover_prev_obj" id="bg_takeover_bg_color" name="bg_takeover_bg_color" type="text" value="'.$adzone['args']['bg_takeover_bg_color'].'">';
														$h.= "<script>jQuery(document).ready(function($){ $('#bg_takeover_bg_color').coloringPick({ on_select:function(){ $('.bg_takeover_prev_obj').trigger('change') } }); });</script>";
											
													$h.= '</div>
													<span class="description bottom">'.__('(optional) Background color.','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column 
											$h.= '<div class="spr_column spr_col-4">';
												$h.= '<div class="input_container">
													<h3 class="title">'.__('Content Background Color','adn').'</h3>
													<div class="input_container_inner">';
														
														$h.= '<input class="bg_takeover_prev_obj" id="bg_takeover_content_bg_color" name="bg_takeover_content_bg_color" type="text" value="'.$adzone['args']['bg_takeover_content_bg_color'].'">';
														$h.= "<script>jQuery(document).ready(function($){ $('#bg_takeover_content_bg_color').coloringPick({ on_select:function(){ $('.bg_takeover_prev_obj').trigger('change') } }); });</script>";
											
													$h.= '</div>
													<span class="description bottom">'.__('(optional) Just in case the content background has no background color.','adn').'</span>
												</div>';
											$h.= '</div>';
											// end .spr_column 

											$h.= '<div class="clear">';
												$h.= '<div class="spr_column spr_col-4">';
													$h.= '<div class="input_container">
														<h3 class="title">'.__('Top Skin URL','adn').'</h3>
														<div class="input_container_inner">';
															$h.= '<input 
																type="text" 
																class="bg_takeover_prev_obj" 
																id="bg_takeover_top_skin_url"
																name="bg_takeover_top_skin_url" 
																value="'.$adzone['args']['bg_takeover_top_skin_url'].'" 
																placeholder="'.__('','adn').'" />
															<i class="input_icon fa fa-link" aria-hidden="true"></i>
														</div>
														<span class="description bottom">'.__('Link URL for the Top Skin.','adn').'</span>
													</div>';
												$h.= '</div>';
												// end .spr_column 
												$h.= '<div class="spr_column spr_col-4">';
													$h.= '<div class="input_container">
														<h3 class="title">'.__('Left Skin URL','adn').'</h3>
														<div class="input_container_inner">';
															$h.= '<input 
																type="text" 
																class="bg_takeover_prev_obj" 
																id="bg_takeover_left_skin_url"
																name="bg_takeover_left_skin_url" 
																value="'.$adzone['args']['bg_takeover_left_skin_url'].'" 
																placeholder="'.__('','adn').'" />
															<i class="input_icon fa fa-link" aria-hidden="true"></i>
														</div>
														<span class="description bottom">'.__('Link URL for the Left Skin.','adn').'</span>
													</div>';
												$h.= '</div>';
												// end .spr_column 
												$h.= '<div class="spr_column spr_col-4">';
													$h.= '<div class="input_container">
														<h3 class="title">'.__('Right Skin URL','adn').'</h3>
														<div class="input_container_inner">';
															$h.= '<input 
																type="text" 
																class="bg_takeover_prev_obj" 
																id="bg_takeover_right_skin_url"
																name="bg_takeover_right_skin_url" 
																value="'.$adzone['args']['bg_takeover_right_skin_url'].'" 
																placeholder="'.__('','adn').'" />
															<i class="input_icon fa fa-link" aria-hidden="true"></i>
														</div>
														<span class="description bottom">'.__('Link URL for the Right Skin.','adn').'</span>
													</div>';
												$h.= '</div>';
												// end .spr_column 
											$h.= '</div>';
											
											$h.= '<div class="clear">';
												$h.= '<div class="spr_column left_column">';
													$show = !empty($adzone['args']['bg_takeover_src']) || !empty($adzone['args']['bg_takeover_bg_color']) ? '' : ' style="display:none;"';
													$h.= '<div class="input_container bgad_preview_container"'.$show.'>
														<h3 class="title">'.__('Preview','adn').'</h3>
														<p>
															'.__('Note: This is just a quick preview to give you an idea. The actual result on your website may look different depending on the sizing.','adn').'
														</p>
														<div class="input_container_inner">';
															$h.= '<div class="bgad_preview" style="position:relative;">
																<div class="bgad_prev_content" style="position: relative;width: 50%;margin: 0 auto;background: #FFF;padding: 10px;font-size: 10px;">
																	<h5>Demo Content</h5>We’ve packed everything you need for managing your advertisements in one easy to use, professional WordPress plugin.We’ve packed everything you need for managing your advertisements in one easy to use, professional WordPress plugin.We’ve packed everything you need for managing your advertisements in one easy to use, professional WordPress plugin.We’ve packed everything you need for managing your advertisements in one easy to use, professional WordPress plugin. We’ve packed everything you need for managing your advertisements in one easy to use, professional WordPress plugin.We’ve packed everything you need for managing your advertisements in one easy to use, professional WordPress plugin.We’ve packed everything you need for managing your advertisements in one easy to use, professional WordPress plugin.We’ve packed everything you need for managing your advertisements in one easy to use, professional WordPress plugin.
																</div>
															</div>';
														$h.= '</div>
													</div>
												</div>';
												// end .spr_column 
											$h.= '</div>';

										$h.= '</div>';
									}
									// end background takeover settings

									$h.= '<span class="description bottom">'.__('','adn').'</span>
								</div>
							</div>
						</div>
					</div>
					<!-- end .spr_column -->

				</div>
				<!-- end .option_box -->
			</div>
		</div>';
		return $h;
	}






	public static function display_filters_tpl($adzone)
	{
		$type = $adzone['args']['type'];
		$save_name = 'save_'.$type;
		$h = '';
		$h.= '<div class="spr_column">
			<div class="spr_column-inner left_column">
				<div class="spr_wrapper">
					<div class="option_box">
						<div class="info_header">
							<span class="nr">5</span>
							<span class="text">'.__('Display Filters','adn').'</span>
							<input type="submit" value="'.sprintf(__('Save %s','adn'), ucfirst($type)).'" class="button-primary" name="'.$save_name.'" style="width:auto;float:right;margin:8px;">
						</div>
						<!-- end .info_header -->
						
						<div class="spr_column">
							<div class="spr_column-inner">
								<div class="spr_wrapper">
									<div class="input_container">
										<p>
											'.__('Display filters','adn').'
										</p>
									</div>
								</div>
							</div>
						</div>
						<!-- end .spr_column -->
						<div class="clear">
							<div class="sep_line" style="margin:0 0 15px 0;"><span><strong>'.__('Content Filters','adn').'</strong></span></div>
							<div class="spr_column">
								<div class="input_container">';
									$show_hide = array_key_exists('show_hide', $adzone['args']['display_filter']) ? $adzone['args']['display_filter']['show_hide'] : 0;
									$h.= '<label class="switch switch-slide small input_h ttip" title="'.__('Show/Hide.','adn').'">
										<input class="switch-input" type="checkbox" name="display_filter_show_hide" value="1" '.checked($show_hide,1,false).' />
										<span class="switch-label" data-on="'.__('Show','adn').'" data-off="'.__('Hide','adn').'"></span> 
										<span class="switch-handle"></span>
									</label>';

									$h.= '<span class="description bottom">'.__('Show or Hide the banner for the selected options.','adn').'</span>
								</div>
							</div>
							<!-- end .spr_column -->


							<div class="spr_column spr_col-6">
								<div class="input_container">
									<div class="custom_box option_inside_content">
										<h3 class="title">'.__('For Categories','adn').'</h3>
										<div class="input_container_inner">';

											$h.= '<select id="wppas_adzone_hide_categories" name="display_filter_categories[]" data-placeholder="'.__('Select Categories', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
												$h.= '<option value=""></option>';

												$categories = array_key_exists('categories', $adzone['args']['display_filter']) ? $adzone['args']['display_filter']['categories'] : '';
												$taxonomies = get_taxonomies();
												$allowed_taxonomies = apply_filters( 'adning_limit_categories', array('category'));
												$allowed_taxonomies = apply_filters( 'adning_hide_categories', $allowed_taxonomies);
												
												foreach($taxonomies as $i => $taxonomy)
												{
													$terms = get_terms($taxonomy);
													foreach($terms as $cat)
													{
														if(in_array($cat->taxonomy, $allowed_taxonomies))
														{
															$selected = !empty($categories) && is_array($categories) ? in_array($cat->term_id, $categories) ? 'selected' : '' : '';
															$h.= '<option value="'.$cat->term_id.'" '.$selected.'>'.$cat->name.' - (ID:'.$cat->term_id.')</option>';
														}
													}
												}
											$h.= '</select>';
										
										$h.= '</div>
									</div>
								</div>
							</div>
							<!-- end .spr_column -->

							<div class="spr_column spr_col-6">
								<div class="input_container">
									<div class="custom_box option_inside_content">
										<h3 class="title">'.__('For Tags','adn').'</h3>
										<div class="input_container_inner">';
											
											$h.= '<select id="wppas_adzone_hide_tags" name="display_filter_tags[]" data-placeholder="'.__('Select Tags', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
												$h.= '<option value=""></option>';
												
												//$tags = $adzone['args']['display_filter']['tags'];
												$tags = array_key_exists('tags', $adzone['args']['display_filter']) ? $adzone['args']['display_filter']['tags'] : '';
												$taxonomies = get_taxonomies();
												$allowed_taxonomies = apply_filters( 'adning_hide_tags', array('post_tag'));
												
												foreach($taxonomies as $i => $taxonomy)
												{
													$terms = get_terms($taxonomy);
													foreach($terms as $tag)
													{
														if(in_array($tag->taxonomy, $allowed_taxonomies))
														{
															$selected = !empty($tags) && is_array($tags) ? in_array($tag->term_id, $tags) ? 'selected' : '' : '';
															$h.= '<option value="'.$tag->term_id.'" '.$selected.'>'.$tag->name.' - (ID:'.$tag->term_id.')</option>';
														}
													}
												}
											$h.= '</select>';
											
										$h.= '</div>
									</div>
								</div>
							</div>
							<!-- end .spr_column -->

							<div class="spr_column spr_col-6">
								<div class="input_container">
									<div class="custom_box option_inside_content">
										<h3 class="title">'.__('For Posts','adn').'</h3>
										<div class="input_container_inner">';
											
											$h.= '<select id="wppas_adzone_hide_posts" name="display_filter_posts[]" data-placeholder="'.__('Select Posts', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
												$h.= '<option value=""></option>';
												
												//$posts = $adzone['args']['display_filter']['posts'];
												$posts = array_key_exists('posts', $adzone['args']['display_filter']) ? $adzone['args']['display_filter']['posts'] : '';
												$all_posts = get_posts(array(
													'posts_per_page'   => -1,
													'post_status'      => 'publish',
													'post_type'        => apply_filters( 'adning_hide_posts', array('post'))
												));
								
												foreach($all_posts as $i => $post)
												{
													$selected = !empty($posts) && is_array($posts) ? in_array($post->ID, $posts) ? 'selected' : '' : '';
													$h.= '<option value="'.$post->ID.'" '.$selected.'>'.$post->post_name.' - (ID:'.$post->ID.')</option>';
												}
											$h.= '</select>';

										$h.= '</div>
									</div>
								</div>
							</div>
							<!-- end .spr_column -->

							<div class="spr_column spr_col-6">
								<div class="input_container">
									<div class="custom_box option_inside_content">
										<h3 class="title">'.__('For Pages','adn').'</h3>
										<div class="input_container_inner">';
											
											$h.= '<select id="wppas_adzone_hide_pages" name="display_filter_pages[]" data-placeholder="'.__('Select Pages', 'adn').'" style="width:100%;" class="chosen-select" multiple>';
												$h.= '<option value=""></option>';
												
												//$pages = $adzone['args']['display_filter']['pages'];
												$pages = array_key_exists('pages', $adzone['args']['display_filter']) ? $adzone['args']['display_filter']['pages'] : '';
												$all_pages = get_posts(array(
													'posts_per_page'   => -1,
													'post_status'      => 'publish',
													'post_type'        => apply_filters( 'adning_hide_pages', array('page'))
												));
								
												foreach($all_pages as $i => $page)
												{
													$selected = !empty($pages) && is_array($pages) ? in_array($page->ID, $pages) ? 'selected' : '' : '';
													$h.= '<option value="'.$page->ID.'" '.$selected.'>'.$page->post_name.' - (ID:'.$page->ID.')</option>';
												}
											$h.= '</select>';

										$h.= '</div>
									</div>
								</div>
							</div>
							<!-- end .spr_column -->
								
						</div>

						<div class="clear device_filter_container" style="margin-top: 40px;">
							<div class="sep_line" style="margin:0 0 15px 0;"><span><strong>'.__('Device Filters','adn').'</strong></span></div>
							<div class="spr_column">';
								$h.= self::devices_options($adzone);		
							$h.= '</div>
							<!-- end .spr_column -->
						</div>

					</div>
				</div>
			</div>
		</div>';

		return $h;
	}






	public static function border_settings_tpl($b = array())
	{
		$h = '';
		$h.= '<div class="option_box">
			<div class="info_header">
				<span class="nr">4</span>
				<span class="text">'.__('Border Settings','adn').'</span>
			</div>

			<div class="spr_row">  
				<div class="spr_column spr_col-6">
					<div class="spr_column-inner left_column">
						<div class="spr_wrapper">
							<div class="input_container">
								<h3 class="title">'.__('Add Border','adn').'</h3>
								
								<div class="input_container_inner">
									<label class="switch switch-slide small ttip" title="'.__('Add a border arround the banner.','adn').'">
										<input id="ADNI_has_border" class="switch-input" type="checkbox" name="cont_border" value="1" '.checked( $b['cont_border'], 1, false ).' />
										<span class="switch-label" data-on="'.__('ON','adn').'" data-off="'.__('OFF','adn').'"></span> 
										<span class="switch-handle"></span>
									</label>
								</div>
								<span class="description bottom">'.__('','adn').'</span>
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
								<h3 class="title">'.__('Border Color','adn').'</h3>
								<div class="input_container_inner small_coloringPick">';
									//$border_color = !empty($b['cont_border_color']) ? $b['cont_border_color'] : '';
									
									$h.= '<input id="cont_border_color" name="cont_border_color" type="text" value="'.$b['cont_border_color'].'">';
									$h.= "<script>jQuery(document).ready(function($){ $('#cont_border_color').coloringPick({'on_select': function(color){ $('.banner_holder').find('._ning_outer').css({'background': color}); } }); });</script>";
								$h.= '</div>
								<span class="description bottom">'.__('','adn').'</span>
							</div>
							<!-- end .input_container -->
						</div>
					</div>
				</div>
				<!-- end .spr_column -->
			</div>
			<!-- end .spr_row -->


			<div class="spr_row">  
				<div class="spr_column spr_col-6">
					<div class="spr_column-inner left_column">
						<div class="spr_wrapper">
							<div class="input_container">
								<h3 class="title">'.__('Label Text','adn').'</h3>';

								$cont_label = !empty($b['cont_label']) ? $b['cont_label'] : '';
								$h.= '<div class="input_container_inner">
										<input 
										type="text" 
										class="" 
										id="ADNI_label"
										name="cont_label" 
										value="'.$cont_label.'" 
										placeholder="'.__('Advertisement','adn').'">
									<i class="input_icon fa fa-pencil" aria-hidden="true"></i>
								</div>
								<span class="description bottom">'.__('Label to show in the banner container.','adn').'</span>
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
								<h3 class="title">'.__('Label Position','adn').'</h3>
								<div class="input_container_inner">';
									$h.= '<select id="ADNI_label_pos" name="cont_label_pos" class="">
										<option value="left"'.selected( $b['cont_label_pos'], 'left', false ).'>'.__('Left','adn').'</option>
										<option value="center"'.selected( $b['cont_label_pos'], 'center', false ).'>'.__('Center','adn').'</option>
										<option value="right"'.selected( $b['cont_label_pos'], 'right', false ).'>'.__('Right','adn').'</option>
									</select>';
								$h.= '</div>
								<span class="description bottom">'.__('','adn').'</span>
							</div>
							<!-- end .input_container -->
						</div>
					</div>
				</div>
				<!-- end .spr_column -->
			</div>
			<!-- end .spr_row -->


			<div class="spr_row">  
				<div class="spr_column spr_col-6">
					<div class="spr_column-inner">
						<div class="spr_wrapper">
							<div class="input_container">
								<h3 class="title">'.__('Label Font Color','adn').'</h3>
								<div class="input_container_inner small_coloringPick">';
									//$border_color = !empty($b['cont_border_color']) ? $b['cont_border_color'] : '';
									
									$h.= '<input id="cont_label_color" name="cont_label_color" type="text" value="'.$b['cont_label_color'].'">';
									$h.= "<script>jQuery(document).ready(function($){ $('#cont_label_color').coloringPick({'picker':'solid','picker_changeable':false, 'on_select': function(color){ $('.banner_holder').find('._ning_label').css({'color': color}); } }); });</script>";
								$h.= '</div>
								<span class="description bottom">'.__('','adn').'</span>
							</div>
							<!-- end .input_container -->
						</div>
					</div>
				</div>
				<!-- end .spr_column -->
			</div>
			<!-- end .spr_row -->';

		$h.= '</div>
		<!-- end .option_box -->';

		return $h;
	}






	public static function alignment_settings_tpl($b = array())
	{
		$h = '';
		$h.= '<div class="option_box">
			<div class="info_header">
				<span class="nr">3</span>
				<span class="text">'.__('Alignment Settings','adn').'</span>
			</div>

			<div class="spr_row">  
				<div class="spr_column spr_col-6">
					<div class="spr_column-inner left_column">
						<div class="spr_wrapper">
							<div class="input_container">
								<h3 class="title">'.__('Banner Alignment','adn').'</h3>
								
								<div class="input_container_inner">';
									$h.= '<select id="ADNI_align" name="align" class="">
										<option value="left"'.selected( $b['align'], 'left', false ).'>'.__('Left','adn').'</option>
										<option value="center"'.selected( $b['align'], 'center', false ).'>'.__('Center','adn').'</option>
										<option value="right"'.selected( $b['align'], 'right', false ).'>'.__('Right','adn').'</option>
									</select>';
								$h.= '</div>
								<span class="description bottom">'.__('','adn').'</span>
							</div>
							<!-- end .input_container -->
						</div>
					</div>
				</div>
				<!-- end .spr_column -->
				
				<div class="spr_column spr_col-6">
					<div class="spr_column-inner left_column">
						<div class="spr_wrapper">
							<div class="input_container">
								<h3 class="title">'.__('Wrap Text','adn').'</h3>
								
								<div class="input_container_inner">
									<label class="switch switch-slide small ttip" title="'.__('Wrap text around the banner.','adn').'">
										<input id="ADNI_wrap_text" class="switch-input" type="checkbox" name="wrap_text" value="1" '.checked( $b['wrap_text'], 1, false ).' />
										<span class="switch-label" data-on="'.__('YES','adn').'" data-off="'.__('NO','adn').'"></span> 
										<span class="switch-handle"></span>
									</label>
								</div>
								<span class="description bottom">'.__('Wrap text around the banner.','adn').'</span>
							</div>
							<!-- end .input_container -->
						</div>
					</div>
				</div>
				<!-- end .spr_column -->
			</div>
			<!-- end .spr_row -->';

		$h.= '</div>
		<!-- end .option_box -->';

		return $h;
	}






	public static function adsense_tpl($args = array())
	{
		$defaults = array(
			'pub_id' => '',
			'type' => 'normal',
			'slot_id' => '',
			'width' => 300,
			'height' => 250,
			'layout_key' => '',
			'layout' => '',
			'google_src' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'
		);
		$args = wp_parse_args($args, $defaults);

		$code = '';

		switch ( $args['type'] ) 
		{
			case 'in-feed':
				$code = '<script async src="'.$args['google_src'].'"></script>'.
						'<ins class="adsbygoogle" ' .
							 'style="display:block;" ' .
							 'data-ad-client="ca-' . $args['pub_id'] . '" ' .
							 'data-ad-slot="' . $args['slot_id'] . '" ' .
							 'data-ad-layout-key="' . $args['layout_key'] . '" ';
				if ( args['layout'] !== '' ) {
					$code .= 'data-ad-layout="' . $args['layout'] . '" ';
				}
				$code .= 'data-ad-format="fluid"></ins>' .
						'<script>' .
						'(adsbygoogle = window.adsbygoogle || []).push({});' .
						'</script>';
				break;
			case 'in-article':
				$code = '<script async src="'.$args['google_src'].'"></script>' .
						'<ins class="adsbygoogle" ' .
							 'style="display:block;text-align:center;" ' .
							 'data-ad-client="ca-' . $args['pub_id'] . '" ' .
							 'data-ad-slot="' . $args['slot_id'] . '" ' .
							 'data-ad-layout="in-article" ' .
							 'data-ad-format="fluid"></ins>' .
						'<script>' .
						'(adsbygoogle = window.adsbygoogle || []).push({});' .
						'</script>';
				break;
			case 'matched-content':
				$code = '<script async src="'.$args['google_src'].'"></script>' .
						'<ins class="adsbygoogle" ' .
							 'style="display:block;" ' .
							 'data-ad-client="ca-' . $args['pub_id'] . '" ' .
							 'data-ad-slot="' . $args['slot_id'] . '" ' .
							 'data-ad-format="autorelaxed"></ins>' .
						'<script>' .
						'(adsbygoogle = window.adsbygoogle || []).push({});' .
						'</script>';
				break;
			case 'link-responsive':
				$code = '<script async src="'.$args['google_src'].'"></script>' .
						'<ins class="adsbygoogle" ' .
							 'style="display:block;" ' .
							 'data-ad-client="ca-' . $args['pub_id'] . '" ' .
							 'data-ad-slot="' . $args['slot_id'] . '" ' .
							 'data-ad-format="link"></ins>' .
						'<script>' .
						'(adsbygoogle = window.adsbygoogle || []).push({});' .
						'</script>';
				break;
			case 'link':
				$code = '<script async src="'.$args['google_src'].'"></script>' .
						'<ins class="adsbygoogle" ' .
							 'style="display:block;width:' . $args['width'] . 'px;height:' . $args['height'] . 'px" ' .
							 'data-ad-client="ca-' . $args['pub_id'] . '" ' .
							 'data-ad-slot="' . $args['slot_id'] . '" ' .
							 'data-ad-format="link"></ins>' .
						'<script>' .
						'(adsbygoogle = window.adsbygoogle || []).push({});' .
						'</script>';
				break;
			case 'responsive':
				$code = '<script async src="'.$args['google_src'].'"></script>' .
						'<ins class="adsbygoogle" ' .
							 'style="display:block;" ' .
							 'data-ad-client="ca-' . $args['pub_id'] . '" ' .
							 'data-ad-slot="' . $args['slot_id'] . '" ' .
							 'data-ad-format="auto"></ins>' .
						'<script>' .
						'(adsbygoogle = window.adsbygoogle || []).push({});' .
						'</script>';
				break;
			case 'normal':
				$code = '<script async src="'.$args['google_src'].'"></script>' .
						'<ins class="adsbygoogle" ' .
							 'style="display:inline-block;width:' . $args['width'] . 'px;height:' . $args['height'] . 'px" ' .
							 'data-ad-client="ca-' . $args['pub_id'] . '" ' .
							 'data-ad-slot="' . $args['slot_id'] . '"></ins>' .
						'<script>' .
						'(adsbygoogle = window.adsbygoogle || []).push({});' .
						'</script>';
				break;
			default:
		}

		return $code;
	}




	public static function devices_options($adzone = array())
	{
		$html = '';
		/*$html.= 'oioi -- '.$adzone['args']['display_filter']['show_desktop'];
		$html.= '<pre>'.print_r($adzone['args']['display_filter'],true).'</pre>';*/

		// DESKTOP
		$show_desktop = array_key_exists('show_desktop',$adzone['args']['display_filter']) ? $adzone['args']['display_filter']['show_desktop'] : 1;
		$show_desktop = $show_desktop === '' ? 1 : $show_desktop;
		/*$html.= '<label class="switch switch-slide small input_h ttip" title="'.__('Show/Hide.','adn').'">
			<input class="switch-input" type="checkbox" name="df_show_desktop" value="1" '.checked($show_desktop,1,false).' />
			<span class="switch-label" data-on="'.__('Show','adn').'" data-off="'.__('Hide','adn').'"></span> 
			<span class="switch-handle"></span>
		</label>';*/
		$html.= self::switch_btn(array(
			'title' => __('Desktop','adn'),
			'id' => 'dopt_show_desktop',
			'name' => 'df_show_desktop',
			//'value' => $show_desktop,
			'checked' => $show_desktop,
			'value' => 1,
			'chk-on' => __('SHOW','adn'),
			'chk-off' => __('HIDE','adn'),
			'chk-high' => 1,
			'column' => array(
				'size' => 'col-3',
				'desc' => __('Show banner on desktop.','adn'),
			)
		));
		// TABLET
		$show_tablet = array_key_exists('show_tablet',$adzone['args']['display_filter']) ? $adzone['args']['display_filter']['show_tablet'] : 1;
		$show_tablet = $show_tablet === '' ? 1 : $show_tablet;
		$html.= self::switch_btn(array(
			'title' => __('Tablet','adn'),
			'id' => 'dopt_show_tablet',
			'name' => 'df_show_tablet',
			//'value' => $show_tablet,
			'checked' => $show_tablet,
			'value' => 1,
			'chk-on' => __('SHOW','adn'),
			'chk-off' => __('HIDE','adn'),
			'chk-high' => 1,
			'column' => array(
				'size' => 'col-3',
				'desc' => __('Show banner on tablet devices.','adn'),
			)
		));
		// MOBILE
		$show_mobile = array_key_exists('show_mobile',$adzone['args']['display_filter']) ? $adzone['args']['display_filter']['show_mobile'] : 1;
		$show_mobile = $show_mobile === '' ? 1 : $show_mobile;
		$html.= self::switch_btn(array(
			'title' => __('Mobile','adn'),
			'desc' => __('Show banner on mobile devices.','adn'),
			'id' => 'dopt_show_mobile',
			'name' => 'df_show_mobile',
			'checked' => $show_mobile,
			'value' => 1,
			//'value' => $show_mobile,
			'chk-on' => __('SHOW','adn'),
			'chk-off' => __('HIDE','adn'),
			'chk-high' => 1,
			'column' => array(
				'size' => 'col-3',
				'desc' => __('Show banner on mobile devices.','adn'),
			)
		));
		
		/*$html.= '<hr>';
		
		// IOS
		$html.= self::checkbox(array(
			'title' => __('IOS','adn'),
			'desc' => __('Show banner on IOS devices.','adn'),
			'id' => 'dopt_show_ios',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// Android
		$html.= self::checkbox(array(
			'title' => __('Android','adn'),
			'desc' => __('Show banner on Android devices.','adn'),
			'id' => 'dopt_show_android',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// Windows Mobile
		$html.= self::checkbox(array(
			'title' => __('Windows Mobile','adn'),
			'desc' => __('Show banner on Windows Mobile devices.','adn'),
			'id' => 'dopt_show_windows_mobile',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// IPhone
		$html.= self::checkbox(array(
			'title' => __('Iphone','adn'),
			'desc' => __('Show banner on Iphones.','adn'),
			'id' => 'dopt_show_iphone',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// IPad
		$html.= self::checkbox(array(
			'title' => __('Ipad','adn'),
			'desc' => __('Show banner on Ipads.','adn'),
			'id' => 'dopt_show_ipad',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// IPod
		$html.= self::checkbox(array(
			'title' => __('Ipod','adn'),
			'desc' => __('Show banner on Ipods.','adn'),
			'id' => 'dopt_show_ipod',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// Samsung
		$html.= self::checkbox(array(
			'title' => __('Samsung','adn'),
			'desc' => __('Show banner on Samsung devices.','adn'),
			'id' => 'dopt_show_samsung',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// blackberry
		$html.= self::checkbox(array(
			'title' => __('Blackberry','adn'),
			'desc' => __('Show banner on Blackberry devices.','adn'),
			'id' => 'dopt_show_blackberry',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// sony_ericsson
		$html.= self::checkbox(array(
			'title' => __('Sony Ericsson','adn'),
			'desc' => __('Show banner on Sony Ericsson devices.','adn'),
			'id' => 'dopt_show_sony_ericsson',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		// Motorola
		$html.= self::checkbox(array(
			'title' => __('Motorola','adn'),
			'desc' => __('Show banner on Motorola devices.','adn'),
			'id' => 'dopt_show_motorola',
			'value' => 1,
			'chk-on' => __('YES','adn'),
			'chk-off' => __('NO','adn'),
			'chk_width' => 50,
			'chk_height' => 20,
			'chk_btn_width' => 30,
			'size' => 'one_third'
		));
		*/
		
		return $html;
	}





	public static function gdpr_approve_modal_tpl($args = array())
	{
		$defaults = array(
			'text' => '',
			'btn_text' => __('I Accept Cookies','adn'),
			'page_url' => ''
		);
		$args = wp_parse_args($args, $defaults);

		$text = empty($args['text']) ? __('We use cookies to offer you a better browsing experience. If you continue to use this site, you consent to our use of cookies.','adn') : $args['text'];
		$btn_text = empty($args['btn_text']) ? __('I Accept Cookies','adn') : $args['btn_text'];

		$h = '';
		$h.= '<div id="_ning_gdpr_approve" class="gdpr_approve">
			<div class="mdl_content">
				<div class="mjs_row is-hidden-tablet-and-below">
					<div class="mjs_column mjs_col-7 mjs_v_align">
						<div class="col_in desc">
							'.$text.'
						</div>
					</div>';

					if( !empty($args['page_url']) )
					{
						$h.= '<div class="mjs_column mjs_col-2 mjs_v_align">
							<div class="col_in">
								<a href="'.$args['page_url'].'" class="cookie_settings">'.__('Privacy Policy','adn').'</a>
							</div>
						</div>';
					}

					$h.= '<div class="mjs_column mjs_col-2 mjs_v_align">
						<div class="col_in">
							<a class="mdl_close_btn my_close_button">
								<svg viewBox="0 0 512 512" style="width:10px;"><path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path></svg>
								<span style="margin-left: 5px;">'.$btn_text.'</span>
							</a>
						</div>
					</div>
				</div>
		
				<div class="mjs_row is-hidden-desktop">
					<div class="mjs_column mjs_col-8 mjs_v_align">
						<div class="col_in desc">
							'.$text.'
						</div>
					</div>
					<div class="mjs_column mjs_col-4 mjs_v_align">
						<div class="col_in">
							<a class="mdl_close_btn my_close_button">
								<svg viewBox="0 0 512 512" style="width:10px;"><path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path></svg>
								<span style="margin-left: 5px;">'.$btn_text.'</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>';


		/*
		var five_minutes = new Date(new Date().getTime() + 5 * 60 * 1000);
		args.mjs_cookies.set(cookie_name, 1, { expires: five_minutes });
		*/
		$h.= '<script>
		jQuery(document).ready(function($){
			$("#_ning_gdpr_approve").modalJS({
				width:"100%",
				position: ["","bottom"],
				close_btn:{},
				animatedIn:"fadeInUp",
				trigger: {"event": "delay", "target":1},
				disable_window_scroll: 0,
				afterClose:function(args){
					if( typeof args.modal.attr("id") !== "undefined"){
						var cookie_name = "_mjs_"+args.modal.attr("id");
						args.mjs_cookies.set(cookie_name, 1, { expires: 365 });
					}
					location.reload();
				}
			});
		});
		</script>';

		return $h;
	}
	

}

endif;
?>