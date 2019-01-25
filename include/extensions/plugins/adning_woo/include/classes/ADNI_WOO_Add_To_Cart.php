<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_WOO_Add_To_Cart' ) ) :

class ADNI_WOO_Add_To_Cart {

    public function __construct() 
    {
		// Actions --------------------------------------------------------
        add_action( 'woocommerce_cart_item_removed', array(__CLASS__, 'remove_item_from_cart'), 10, 2 );
        // Add meta to order - WC 3.x
        add_action( 'woocommerce_checkout_create_order_line_item', array(__CLASS__, 'order_item_meta'), 20, 4);
        //add_action('woocommerce_new_order_item', array(__CLASS__, 'order_item_meta'), 10, 3);
        //add_action('woocommerce_add_order_item_meta', array(__CLASS__, 'order_item_meta'), 10, 2);
        
        // Filters --------------------------------------------------------
        // Add to cart
		add_filter( 'woocommerce_add_cart_item', array(__CLASS__, 'add_cart_item'), 10, 1);
		// Add item data to the cart
		add_filter( 'woocommerce_add_cart_item_data', array(__CLASS__, 'add_cart_item_data'), 10, 2);
		// Load cart data per page load
		add_filter( 'woocommerce_get_cart_item_from_session', array(__CLASS__, 'get_cart_item_from_session'), 10, 2);
        add_filter( 'woocommerce_add_to_cart_redirect', array(__CLASS__, 'add_to_cart_redirect') );
        add_filter( 'woocommerce_cart_item_thumbnail', array(__CLASS__, 'cart_item_thumbnail'), 10, 3 );
        // Validate when adding to cart
		//add_filter('woocommerce_add_to_cart_validation', array($this, 'validate_add_cart_item'), 10, 3);
	}

	/**
	 * add_cart_item function.
	 *
	 * @access public
	 * @param mixed $cart_item
	 * @return void
	 */
	public static function add_cart_item($cart_item) 
	{
        if( ADNI_WOO_Main::is_adning_woo_product($cart_item) )
		{
			//echo '<pre>'.print_r($cart_item,true).'</pre>';
			$extra_cost = $cart_item['adning_woo_data']['_price'];
			$cart_item['data']->set_price( $extra_cost );
		}

		return $cart_item;
	}
    
    


    public static function add_to_cart_redirect()
    {
        return wc_get_cart_url();
        //return WC()->cart->get_checkout_url();
    }
	
	
	
	
	public static function add_cart_item_data($cart_item_meta, $product_id)
	{
		$cart_item_meta['adning_woo_data']['adzone_title'] = ( isset($_POST['adzone_id']) && !empty($_POST['adzone_id'])) ? get_the_title($_POST['adzone_id']) : '';
        $cart_item_meta['adning_woo_data']['_adzone_id'] = ( isset($_POST['adzone_id']) && !empty($_POST['adzone_id'])) ? $_POST['adzone_id'] : 0;
        $cart_item_meta['adning_woo_data']['_ning_order_id'] = ( isset($_POST['order_id']) && !empty($_POST['order_id'])) ? $_POST['order_id'] : 0;
		$cart_item_meta['adning_woo_data']['_price'] = ( isset($_POST['price']) && !empty($_POST['price'])) ? $_POST['price'] : 0;
		
		return $cart_item_meta;
    }
    




    public static function cart_item_thumbnail( $thumb, $cart_item, $cart_item_key ) 
	{
		if( ADNI_WOO_Main::is_adning_woo_product($cart_item) )
		{
			$thumb = '<img src="'.ADNI_WOO_ASSETS_URL.'/img/adzone_thumb.jpg" />';
		}
		
		return $thumb;	
	}



    public static function remove_item_from_cart($cart_item_key, $instance)
    {
        global $wpdb;
        //error_log('remove from cart');
        //error_log(print_r($instance,true));
        if(!empty($instance))
        {
            $item = $instance->removed_cart_contents[$cart_item_key];
            if( array_key_exists('adning_woo_data', $item))
            {
                $order_id = $item['adning_woo_data']['_ning_order_id'];
                error_log("DELETE FROM " . $wpdb->prefix . "adning_sell WHERE id = ".$order_id.";");
			    $wpdb->query( "DELETE FROM " . $wpdb->prefix . "adning_sell WHERE id = ".$order_id.";" );
            }
        }
    }
	
	
	
	

	/**
	 * get_cart_item_from_session function.
	 *
	 * @access public
	 * @param mixed $cart_item
	 * @param mixed $values
	 * @return void
	 */
	public static function get_cart_item_from_session($cart_item, $values) 
	{
		if( isset($values['adning_woo_data'] ))
		{
			$cart_item['adning_woo_data'] = $values['adning_woo_data'];
		}
		
		//$cart_item = $this->add_cart_item($cart_item);
		self::add_cart_item($cart_item);
		
		return $cart_item;
    }
    
    /**
	 * order_item_meta function.
     * https://stackoverflow.com/questions/29666820/woocommerce-which-hook-to-replace-deprecated-woocommerce-add-order-item-meta/49419394#49419394
	 *
	 * @access public
	 * @param mixed $item_id
	 * @param mixed $values
	 * @return void
	 */
    public static function order_item_meta($item, $cart_item_key, $values, $order) 
	{	
        //error_log('CART');
        /*error_log('item:');
        //$custom_field_value = get_post_meta( $item->get_product_id(), '_meta_key', true );
        error_log($item);
        error_log('cart_item_key:');
        error_log($cart_item_key);
        error_log('values:');
        error_log(print_r($values,true));
        error_log('order:');
        error_log(print_r($order,true));*/
        
        if( ADNI_WOO_Main::is_adning_woo_product('', $values['product_id']) )
        {
            $data = $values['adning_woo_data'];

            $item->update_meta_data( 'adzone_title', $data['adzone_title'] );
            $item->update_meta_data( '_adzone_id', $data['_adzone_id'] );
            $item->update_meta_data( '_ning_order_id', $data['_ning_order_id'] );
            $item->update_meta_data( '_price', $data['_price'] );
            
            //error_log('item:');
            //error_log(print_r($item, true));
        }
    }

}
endif;
?>