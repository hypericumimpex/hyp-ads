<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_WOO_Templates' ) ) :

class ADNI_WOO_Templates {
	
	public function __construct() 
	{
        // Actions --------------------------------------------------------
        add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'order_form'), 15 );
        //add_action( 'woocommerce_before_add_to_cart_button', array(__CLASS__, 'extend_product_options'));
        add_action( 'ADNI_sell_send_payment', array(__CLASS__, 'send_payment_form'),10, 3);
        add_action( 'ADNI_sell_settings_get', array(__CLASS__, 'handle_get'));
        

        // Filters --------------------------------------------------------
        add_filter( 'ADNI_sell_payment_options_settings', array(__CLASS__, 'payment_settings'), 10, 2);
        add_filter( 'ADNI_sell_payment_option_form_settings', array(__CLASS__, 'payment_settings_form'), 10, 3);
        add_filter( 'ADNI_save_sell_settings', array(__CLASS__, 'save_sell_settings'));
        add_filter( 'ADNI_sell_payment_options', array(__CLASS__, 'payment_options'),10 ,2);
        add_filter( 'ADNI_sell_active_payment', array(__CLASS__, 'active_payment'),10, 2);
    }



    
    /**
     * HOOKs into ADNI_Sell::sell_payment_options() 
     * using FILTER: ADNI_sell_payment_options_settings
     * 
     * $options: array - payment options
     * $settings: array - ONLY sell settings!
     */ 
    public static function payment_settings($options, $settings)
    {
        $product_id = ADNI_Multi::get_option('_adning_woo_product', 0);

        $options['woocommerce'] = array(
            'active' => 0,
            'product_id' => $product_id,
        );
        //echo '<pre>'.print_r($options['woocommerce'],true).'</pre>';
        return $options;
    }




    public static function payment_settings_form($options, $settings, $key)
    {
        $product_id = ADNI_Multi::get_option('_adning_woo_product', 0);
        $h = '';
        $title = $product_id ? '(#'.$product_id.') ' : '';
        $desc = '';

        if( array_key_exists('woocommerce', $settings['payment']))
        {
            //echo '<pre>'.print_r($settings['payment']['woocommerce'],true).'</pre>';
            $woo_settings = $settings['payment']['woocommerce'];
            $product_id = !empty($woo_settings['form']['product_id']['args']['value']) ? $woo_settings['form']['product_id']['args']['value'] : ADNI_Multi::get_option('_adning_woo_product', 0);
        }

        if( $product_id )
        {
            $h.= '<a href="post.php?post='.$product_id.'&action=edit" class="button-secondary" target="_blank">'.get_the_title($product_id).'</a>';
            $h.= '<span class="description">'.__('','adn').'</span>';
            $desc = __('Adning Ads Woocommerce Product','adn');
        }
        else
        {
            $h.= '<a href="admin.php?page=adning-sell&create_woo_product=1" class="button-secondary">'.__('Create Adning Ads Woocommerce Product','adn').'</a>';
            $desc = __('It seems like you have not yet created the Adning Ads Woocommerce Product.','adn');
        }

        $options['woocommerce'] = array(
            'title' => __('WooCommerce','adn'),
            'logo' => '<svg viewBox="0 0 256 153"><g><path d="M23.7586644,0 L232.137438,0 C245.324643,0 256,10.6753566 256,23.8625617 L256,103.404434 C256,116.591639 245.324643,127.266996 232.137438,127.266996 L157.409942,127.266996 L167.666657,152.385482 L122.558043,127.266996 L23.8633248,127.266996 C10.6761196,127.266996 0.000763038458,116.591639 0.000763038458,103.404434 L0.000763038458,23.8625617 C-0.10389732,10.7800169 10.5714592,0 23.7586644,0 L23.7586644,0 Z" fill="#9B5C8F"/><path d="M14.5781994,21.7495935 C16.0351099,19.7723577 18.2204758,18.7317073 21.1342969,18.5235772 C26.441614,18.1073171 29.4595002,20.604878 30.1879555,26.0162602 C33.4139717,47.7658537 36.9521831,66.1853659 40.6985246,81.2747967 L63.4887685,37.8796748 C65.5700693,33.9252033 68.1716953,31.8439024 71.2936465,31.6357724 C75.8725083,31.3235772 78.6822644,34.2373984 79.8269798,40.3772358 C82.4286059,54.2178862 85.7586872,65.9772358 89.7131587,75.9674797 C92.4188498,49.5349593 96.9977116,30.4910569 103.449744,18.7317073 C105.01072,15.8178862 107.300151,14.3609756 110.318037,14.1528455 C112.711533,13.9447154 114.896899,14.6731707 116.874134,16.2341463 C118.85137,17.795122 119.89202,19.7723577 120.100151,22.1658537 C120.204216,24.0390244 119.89202,25.6 119.0595,27.1609756 C115.000964,34.6536585 111.670882,47.2455285 108.965191,64.7284553 C106.363565,81.6910569 105.42698,94.9073171 106.05137,104.377236 C106.2595,106.978862 105.84324,109.268293 104.80259,111.245528 C103.553809,113.534959 101.680638,114.78374 99.2871424,114.99187 C96.5814514,115.2 93.7716953,113.95122 91.0660042,111.141463 C81.3879555,101.255285 73.6871424,86.4780488 68.0676303,66.8097561 C61.3034026,80.1300813 56.3082807,90.1203252 53.0822644,96.7804878 C46.942427,108.539837 41.739175,114.57561 37.3684433,114.887805 C34.5586872,115.095935 32.1651912,112.702439 30.0838904,107.707317 C24.7765733,94.0747967 19.0529961,67.7463415 12.9131587,28.7219512 C12.4968985,26.0162602 13.1212888,23.6227642 14.5781994,21.7495935 Z M238.213972,38.0878049 C234.46763,31.5317073 228.952183,27.5772358 221.563565,26.0162602 C219.586329,25.6 217.713159,25.3918699 215.944053,25.3918699 C205.953809,25.3918699 197.836736,30.595122 191.488768,41.001626 C186.077386,49.8471545 183.371695,59.6292683 183.371695,70.3479675 C183.371695,78.3609756 185.036736,85.2292683 188.366817,90.9528455 C192.113159,97.5089431 197.628606,101.463415 205.017224,103.02439 C206.99446,103.44065 208.86763,103.64878 210.636736,103.64878 C220.731045,103.64878 228.848118,98.4455285 235.09202,88.0390244 C240.503403,79.0894309 243.209094,69.3073171 243.209094,58.5886179 C243.313159,50.4715447 241.544053,43.7073171 238.213972,38.0878049 Z M225.101777,66.9138211 C223.644866,73.7821138 221.04324,78.8813008 217.192834,82.3154472 C214.174947,85.0211382 211.365191,86.1658537 208.763565,85.6455285 C206.266004,85.1252033 204.184703,82.9398374 202.623728,78.8813008 C201.374947,75.6552846 200.750557,72.4292683 200.750557,69.4113821 C200.750557,66.8097561 200.958687,64.2081301 201.479012,61.8146341 C202.415598,57.5479675 204.184703,53.3853659 206.99446,49.4308943 C210.428606,44.3317073 214.070882,42.2504065 217.817224,42.9788618 C220.314785,43.499187 222.396086,45.6845528 223.957061,49.7430894 C225.205842,52.9691057 225.830232,56.195122 225.830232,59.2130081 C225.830232,61.9186992 225.622102,64.5203252 225.101777,66.9138211 Z M173.069256,38.0878049 C169.322915,31.5317073 163.703403,27.5772358 156.41885,26.0162602 C154.441614,25.6 152.568443,25.3918699 150.799338,25.3918699 C140.809094,25.3918699 132.69202,30.595122 126.344053,41.001626 C120.932671,49.8471545 118.22698,59.6292683 118.22698,70.3479675 C118.22698,78.3609756 119.89202,85.2292683 123.222102,90.9528455 C126.968443,97.5089431 132.48389,101.463415 139.872508,103.02439 C141.849744,103.44065 143.722915,103.64878 145.49202,103.64878 C155.586329,103.64878 163.703403,98.4455285 169.947305,88.0390244 C175.358687,79.0894309 178.064378,69.3073171 178.064378,58.5886179 C178.064378,50.4715447 176.399338,43.7073171 173.069256,38.0878049 Z M159.852996,66.9138211 C158.396086,73.7821138 155.79446,78.8813008 151.944053,82.3154472 C148.926167,85.0211382 146.116411,86.1658537 143.514785,85.6455285 C141.017224,85.1252033 138.935923,82.9398374 137.374947,78.8813008 C136.126167,75.6552846 135.501777,72.4292683 135.501777,69.4113821 C135.501777,66.8097561 135.709907,64.2081301 136.230232,61.8146341 C137.166817,57.5479675 138.935923,53.3853659 141.745679,49.4308943 C145.179825,44.3317073 148.822102,42.2504065 152.568443,42.9788618 C155.066004,43.499187 157.147305,45.6845528 158.708281,49.7430894 C159.957061,52.9691057 160.581451,56.195122 160.581451,59.2130081 C160.685516,61.9186992 160.373321,64.5203252 159.852996,66.9138211 L159.852996,66.9138211 L159.852996,66.9138211 Z" fill="#FFFFFF"/></g></svg>',
            'info' => __('If WooCommerce payments are activated they will overrule all other payment options. In that case, all payments will be handled by the default WooCommerce checkout.','adn'),
            'form' => array(
                'product_id' => array(
                    'html' => ADNI_Templates::spr_column(array(
                        'col' => 'spr_col-8',
                        'title' => $title.__('Product','adn'),
                        'desc' => $desc,
                        'content' => $h
                    ))
                    /*'type' => 'column',
                    'col' => 'spr_col-8',
                    'title' => $title.__('Product','adn'),
                    'desc' => $desc,
                    'content' => $h,*/
                    /*'args' => array(
                        'name' => 'payment[woocommerce][product_id]',
                        'value' => $product_id
                    )*/
                )
            )
        );

        return $options;
    }




    public static function handle_get($get)
    {
        if(isset($get['create_woo_product']) && !empty($get['create_woo_product']))
        {
            $product_id = ADNI_WOO_Main::create_product();
            ADNI_Multi::update_option( '_adning_woo_product', $product_id );
        }
    }



    public static function save_sell_settings($settings)
    {
        if( !empty($settings['payment']['woocommerce']['active']) )
        {
            // If WooCommerce payment setting is active we turn off all other options.
            foreach( $settings['payment'] as $key => $payment )
            {
                if( $key !== 'woocommerce' )
                {
                    $settings['payment'][$key]['active'] = 0;
                }
            }
        }
        return $settings;
    }


    /**
     * Turn ADNI_Sell active_payment on
     */
    public static function active_payment($activa_payment, $provider)
    {
        if( $provider === 'woocommerce')
        {
            return 1;
        }

        return $activa_payment;
    }


    /**
     * Take over ADNI_Sell payment options
     */
    public static function payment_options($h, $settings)
    {
        if( $settings['payment']['woocommerce']['active'] )
        {
            $logo = '<svg viewBox="0 0 256 153" style="width:40px;"><g><path d="M23.7586644,0 L232.137438,0 C245.324643,0 256,10.6753566 256,23.8625617 L256,103.404434 C256,116.591639 245.324643,127.266996 232.137438,127.266996 L157.409942,127.266996 L167.666657,152.385482 L122.558043,127.266996 L23.8633248,127.266996 C10.6761196,127.266996 0.000763038458,116.591639 0.000763038458,103.404434 L0.000763038458,23.8625617 C-0.10389732,10.7800169 10.5714592,0 23.7586644,0 L23.7586644,0 Z" fill="#9B5C8F"/><path d="M14.5781994,21.7495935 C16.0351099,19.7723577 18.2204758,18.7317073 21.1342969,18.5235772 C26.441614,18.1073171 29.4595002,20.604878 30.1879555,26.0162602 C33.4139717,47.7658537 36.9521831,66.1853659 40.6985246,81.2747967 L63.4887685,37.8796748 C65.5700693,33.9252033 68.1716953,31.8439024 71.2936465,31.6357724 C75.8725083,31.3235772 78.6822644,34.2373984 79.8269798,40.3772358 C82.4286059,54.2178862 85.7586872,65.9772358 89.7131587,75.9674797 C92.4188498,49.5349593 96.9977116,30.4910569 103.449744,18.7317073 C105.01072,15.8178862 107.300151,14.3609756 110.318037,14.1528455 C112.711533,13.9447154 114.896899,14.6731707 116.874134,16.2341463 C118.85137,17.795122 119.89202,19.7723577 120.100151,22.1658537 C120.204216,24.0390244 119.89202,25.6 119.0595,27.1609756 C115.000964,34.6536585 111.670882,47.2455285 108.965191,64.7284553 C106.363565,81.6910569 105.42698,94.9073171 106.05137,104.377236 C106.2595,106.978862 105.84324,109.268293 104.80259,111.245528 C103.553809,113.534959 101.680638,114.78374 99.2871424,114.99187 C96.5814514,115.2 93.7716953,113.95122 91.0660042,111.141463 C81.3879555,101.255285 73.6871424,86.4780488 68.0676303,66.8097561 C61.3034026,80.1300813 56.3082807,90.1203252 53.0822644,96.7804878 C46.942427,108.539837 41.739175,114.57561 37.3684433,114.887805 C34.5586872,115.095935 32.1651912,112.702439 30.0838904,107.707317 C24.7765733,94.0747967 19.0529961,67.7463415 12.9131587,28.7219512 C12.4968985,26.0162602 13.1212888,23.6227642 14.5781994,21.7495935 Z M238.213972,38.0878049 C234.46763,31.5317073 228.952183,27.5772358 221.563565,26.0162602 C219.586329,25.6 217.713159,25.3918699 215.944053,25.3918699 C205.953809,25.3918699 197.836736,30.595122 191.488768,41.001626 C186.077386,49.8471545 183.371695,59.6292683 183.371695,70.3479675 C183.371695,78.3609756 185.036736,85.2292683 188.366817,90.9528455 C192.113159,97.5089431 197.628606,101.463415 205.017224,103.02439 C206.99446,103.44065 208.86763,103.64878 210.636736,103.64878 C220.731045,103.64878 228.848118,98.4455285 235.09202,88.0390244 C240.503403,79.0894309 243.209094,69.3073171 243.209094,58.5886179 C243.313159,50.4715447 241.544053,43.7073171 238.213972,38.0878049 Z M225.101777,66.9138211 C223.644866,73.7821138 221.04324,78.8813008 217.192834,82.3154472 C214.174947,85.0211382 211.365191,86.1658537 208.763565,85.6455285 C206.266004,85.1252033 204.184703,82.9398374 202.623728,78.8813008 C201.374947,75.6552846 200.750557,72.4292683 200.750557,69.4113821 C200.750557,66.8097561 200.958687,64.2081301 201.479012,61.8146341 C202.415598,57.5479675 204.184703,53.3853659 206.99446,49.4308943 C210.428606,44.3317073 214.070882,42.2504065 217.817224,42.9788618 C220.314785,43.499187 222.396086,45.6845528 223.957061,49.7430894 C225.205842,52.9691057 225.830232,56.195122 225.830232,59.2130081 C225.830232,61.9186992 225.622102,64.5203252 225.101777,66.9138211 Z M173.069256,38.0878049 C169.322915,31.5317073 163.703403,27.5772358 156.41885,26.0162602 C154.441614,25.6 152.568443,25.3918699 150.799338,25.3918699 C140.809094,25.3918699 132.69202,30.595122 126.344053,41.001626 C120.932671,49.8471545 118.22698,59.6292683 118.22698,70.3479675 C118.22698,78.3609756 119.89202,85.2292683 123.222102,90.9528455 C126.968443,97.5089431 132.48389,101.463415 139.872508,103.02439 C141.849744,103.44065 143.722915,103.64878 145.49202,103.64878 C155.586329,103.64878 163.703403,98.4455285 169.947305,88.0390244 C175.358687,79.0894309 178.064378,69.3073171 178.064378,58.5886179 C178.064378,50.4715447 176.399338,43.7073171 173.069256,38.0878049 Z M159.852996,66.9138211 C158.396086,73.7821138 155.79446,78.8813008 151.944053,82.3154472 C148.926167,85.0211382 146.116411,86.1658537 143.514785,85.6455285 C141.017224,85.1252033 138.935923,82.9398374 137.374947,78.8813008 C136.126167,75.6552846 135.501777,72.4292683 135.501777,69.4113821 C135.501777,66.8097561 135.709907,64.2081301 136.230232,61.8146341 C137.166817,57.5479675 138.935923,53.3853659 141.745679,49.4308943 C145.179825,44.3317073 148.822102,42.2504065 152.568443,42.9788618 C155.066004,43.499187 157.147305,45.6845528 158.708281,49.7430894 C159.957061,52.9691057 160.581451,56.195122 160.581451,59.2130081 C160.685516,61.9186992 160.373321,64.5203252 159.852996,66.9138211 L159.852996,66.9138211 L159.852996,66.9138211 Z" fill="#FFFFFF"/></g></svg>';
            $h = ADNI_Templates::spr_column(array(
                'col' => 'spr_col-1',
                'style' => 'display:none;',
                'title' => '',
                'desc' => '',
                'content' => '<a class="payment_btn ttip selected" title="WooCommerce" data-opt="woocommerce">'.$logo.'</a>'
            ));
        }

        return $h;
    }



    /**
     * Handle the payment form - action.
     */
    public static function send_payment_form($h, $ipn_data, $payment)
    {
        if( $payment === 'woocommerce' )
        { 
            $sell_settings = ADNI_Sell::sell_main_settings();
            $sell_settings = $sell_settings['sell'];

            $urlf = strpos($sell_settings['urls']['available_adzones'], '?') !== false ? '&' : '?';
            $url = $sell_settings['urls']['available_adzones'].$urlf.'adzone='.$ipn_data['aid'];

            $adni_woo_product = ADNI_Multi::get_option('_adning_woo_product', 0);

            $h = '';
            $h.= '<form id="_ning_pmt_send" class="cart" action="'.$url.'" method="post" enctype="multipart/form-data">';
                $h.= '<input type="hidden" name="adzone_id" value="'.$ipn_data['aid'].'">';
                $h.= '<input type="hidden" name="banner_id" value="0">';
                $h.= '<input type="hidden" name="order_id" value="'.$ipn_data['order_id'].'">';
                $h.= '<input type="hidden" name="price" value="'.$ipn_data['price'].'">';
                $h.= '<input type="hidden" class="qty" name="quantity" value="1">'; // id="quantity_5c4066227a6ba"
                $h.= '<input type="hidden" name="add-to-cart" value="'.$adni_woo_product.'">';
                //$h.= '<button type="submit" name="add-to-cart" value="201" class="single_add_to_cart_button button alt">Add to cart</button>';
            $h.= '</form>';
        }

        echo $h;
    }





    public static function order_form()
    {
        global $post, $wpdb, $product, $woocommerce;
        
        $h = '';

		if( ADNI_WOO_Main::is_adning_woo_product('', $post->ID) )
		{
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
            
            $h.= '<div class="adning_cont adning_woo_ajax_content content_holder" style="display:inline;">';
				
                $h.= '<style>.product_title, .summary { display:none; }</style>';
                $h.= '<h4>'.__('Advertise on this Website','adn').'</h4>';
                $h.= ADNI_Sell::all_available_adzones();
                $h.= ADNI_Sell::faq_info_footer();
                
            $h.= '</div>';
        }
        
        echo $h;
    }





    /*public static function extend_product_options()
	{
		global $post, $wpdb, $product, $woocommerce;
		
		if( ADNI_WOO_Main::is_adning_woo_product('', $post->ID) )
		{
			$adzone_id = 202; //isset($_GET['adzone_id']) && !empty($_GET['adzone_id']) ? $_GET['adzone_id'] : 0;
			echo self::extend_product_options_form($adzone_id);
		}
	}
	

	
	public static function extend_product_options_form($adzone_id)
	{
        global $post, $wpdb, $product, $woocommerce;
        
        $html = '';
        $adzone = ADNI_CPT::load_post($adzone_id, array('post_type' => ADNI_CPT::$adzone_cpt, 'filter' => 0));
        
        if( !empty($adzone) ){
            $adzone_info = ADNI_Sell::adzone_details($adzone);
            //$arr = $pro_ads_adzones->get_adzone_data( $adzone_id );
            //$size = !empty($arr['size']) ? $arr['size'][0].'x'.$arr['size'][1] : __('responsive', 'wpproads');
            $adzone_id = $adzone['post']->ID;
            $size = $adzone['args']['size'];
            $price = $adzone['args']['sell']['price'];
            
            
            $html.= '<div id="proadswoo_custom_data">';
                $html.= '<h4>'.__('Banner Options','adn').'</h4>';
                $html.= '<input type="hidden" name="banner_adzone" value="'.$adzone_id.'" />';
                $html.= '<input type="hidden" name="banner_size" value="'.$size.'" />';
                $html.= '<input type="hidden" name="banner_price" value="'.$price.'" />';
                $html.= '<div>';
                    $html.= '<div>'.__('Banner Title','adn').'</div>';
                    $html.= '<div>';
                        $html.= '<input class="buyandsell_input banner_title_'.$adzone_id.'" name="banner_title" type="text" value="" />';
                    $html.= '</div>';
                $html.= '</div>';
                $html.= '<div>';
                    $html.= '<div>'.__('Banner Link','adn').'</div>';
                    $html.= '<div>';
                        $html.= '<input class="buyandsell_input banner_link_'.$adzone_id.'" name="banner_link" type="text" value="" placeholder="http://www.yourlink.com" />';
                    $html.= '</div>';
                $html.= '</div>';
            $html.= '</div>';
            $html.= '<br />';
       }
		
		return $html;
    }
    */
}
endif;
?>