<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_WOO_Main' ) ) :

class ADNI_WOO_Main {
	
	public function __construct() 
	{
        // Actions --------------------------------------------------------
        add_action( 'woocommerce_order_status_completed', array(__CLASS__, 'order_complete'),10,1 );
        add_action( 'before_delete_post', array(__CLASS__,'delete_post'));
        
        // Filters --------------------------------------------------------
        add_filter( 'ADNI_sell_receive_payment', array(__CLASS__, 'receive_payment'),10, 3 );
        add_filter( 'woocommerce_thankyou_order_received_text', array(__CLASS__,'order_received_text'), 10, 2 );
    }

    public function create_product( $args = array() )
	{
		global $wp_error, $current_user;
		
		$defaults = array(
			'post_author' => $current_user->ID,
			'post_content' => '',
			'post_status' => "publish",
			'post_title' => __('Advertising AD Spot','adn'),
			'post_parent' => '',
			'post_type' => "product",
		);
		
		$post = wp_parse_args( $args, $defaults );
		
		//Create post
		$post_id = wp_insert_post( $post, $wp_error );
		if($post_id)
		{
			$attach_id = get_post_meta($product->parent_id, "_thumbnail_id", true);
			add_post_meta($post_id, '_thumbnail_id', $attach_id);
			
			wp_set_object_terms($post_id, 'simple', 'product_type');
			
			$product_attributes = array(
				'adning_woo' => array(
					'name' => 'adning_woo',
					'value' => '1',
					'is_visible' => '0', 
					'is_variation' => '0',
					'is_taxonomy' => '0'
				)
			);
			
			update_post_meta( $post_id, '_visibility', 'hidden' ); // visible
			update_post_meta( $post_id, '_stock_status', 'instock');
			update_post_meta( $post_id, 'total_sales', '0');
			update_post_meta( $post_id, '_downloadable', 'no');
			update_post_meta( $post_id, '_virtual', 'yes');
			update_post_meta( $post_id, '_regular_price', "1" );
			update_post_meta( $post_id, '_sale_price', "1" );
			update_post_meta( $post_id, '_purchase_note', "" );
			update_post_meta( $post_id, '_featured', "no" );
			update_post_meta( $post_id, '_weight', "" );
			update_post_meta( $post_id, '_length', "" );
			update_post_meta( $post_id, '_width', "" );
			update_post_meta( $post_id, '_height', "" );
			update_post_meta( $post_id, '_sku', "");
			update_post_meta( $post_id, '_product_attributes', $product_attributes); // array()
			update_post_meta( $post_id, '_sale_price_dates_from', "" );
			update_post_meta( $post_id, '_sale_price_dates_to', "" );
			update_post_meta( $post_id, '_price', "1" );
			update_post_meta( $post_id, '_sold_individually', "yes" );
			update_post_meta( $post_id, '_manage_stock', "no" );
			update_post_meta( $post_id, '_backorders', "no" );
			update_post_meta( $post_id, '_stock', "" );
			
		}
		
		return $post_id;
    }
    



    /**
	 * Check if product is a Adning ads woo item.
	 *
	 * @access public
	 * @param array $cart_item, int $product_id
	 * @return bool
	 */
	public static function is_adning_woo_product( $cart_item = '', $product_id = '' )
	{
        $is_proadswoo_product = 0;
		
		if( !empty($cart_item) )
		{
			if( !empty($cart_item['adning_woo_data']))
			{
				$ats = get_post_meta( $cart_item['product_id'] , '_product_attributes', true );
				
				if( !empty( $ats['adning_woo'] ))
				{
					$is_proadswoo_product = 1;
				}
			}
		}
		elseif( !empty($product_id))
		{
            $ats = get_post_meta( $product_id, '_product_attributes', true );
				
			if( !empty( $ats['adning_woo'] ))
			{
				$is_proadswoo_product = 1;
			}
		}
		
		return $is_proadswoo_product;
    }
    



    // ADNI_sell_receive_payment hook
    public static function receive_payment($data, $pay_arr, $provider)
    {
        if( $provider === 'woocommerce' )
        {
            //error_log(print_r($data, true));
            $data['all_ok'] = 1;
            $pay_arr = $data;
        }
        
        return $pay_arr;
    }




    /**
	 * Order Complete - Create banner
     * https://docs.woocommerce.com/wc-apidocs/class-WC_Order.html
	 *
	 * @access public
	 * @param int $order_id
	 * @return null
	 */
	public static function order_complete( $order_id )
	{
        //error_log('ORDER ID: '.$order_id);
		
        $order = new WC_Order( $order_id );
       
        //error_log(print_r($order, true));
		
        //$user_id = $order->customer_id;
        //$email = $order->billing['email']; //$order->billing_first_name, $order->billing_last_name
		//$full_name = $order->billing['first_name'].' '.$order->billing['last_name'];
        $user_id = $order->get_customer_id();
        $email = $order->get_billing_email();
        $order_key = $order->get_order_key();
		
		$items = $order->get_items(); 

		// PRODUCT - $product
		foreach ($items as $key => $product ) 
		{
            //error_log('PRODUCT');
            //error_log(print_r($product, true));

			if( self::is_adning_woo_product('', $product['product_id']) )
			{
				/**
				 * Order Complete
				*/
				// Check WP User
				//$user = get_user_by( 'email', $email );
                //$wpuser_id = $user_id ? $user_id : '';
                
                $data = array(
                    'type' => 'new',
                    'adzone_id' => $product['_adzone_id'],
                    'banner_id' => 0,
                    'order_id' => $product['_ning_order_id'],
                    'price' => $product['_price'],
                    'email' => $email,
                    'transaction' => $order_key,
                    'am_paid' => $product['_price'],
                    'trans_date' => current_time('timestamp')
                );

                //error_log('DATA');
                //error_log(print_r($data, true));

                ADNI_Sell::receive_payment($data, 'woocommerce');
			}
		}
    }
    


    
    public static function order_received_text( $str, $order ) 
    {
        $sell_settings = ADNI_Sell::sell_main_settings();
        $sell_settings = $sell_settings['sell'];

        $new_str = $str . '<p>'.sprintf(__(' <strong>Next step</strong>: Find your AD Spot in the %s and add your banner.','adn'),'<a href="'.$sell_settings['urls']['user_dashboard'].'">'.__('User Dashboard','adn').'</a>').'</p>';
        return $new_str;
    }




    public static function delete_post($post_id)
    {
        $adni_woo_product = ADNI_Multi::get_option('_adning_woo_product', 0);
        if( $post_id == $adni_woo_product)
        {
            ADNI_Multi::update_option('_adning_woo_product', 0);
        }
    }
}
endif;
?>