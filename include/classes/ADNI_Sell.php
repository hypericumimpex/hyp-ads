<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_Sell' ) ) :

class ADNI_Sell {


    public function __construct() 
	{
        // Actions --------------------------------------------------------
        add_action( 'adning_single_adzone_settings', array( __CLASS__, 'adzone_sell_box' ), 10, 1 );

        // Filters --------------------------------------------------------
        add_filter( 'ADNI_default_adzone_args', array(__CLASS__,'adzone_set'), 10, 1 );
        add_filter( 'ADNI_save_post', array(__CLASS__,'save_adzone'), 10, 1 );
    }



    /**
     * default settings
     */
    public static function defaults()
    {
        return array(
            //'sell' => array(
            'enable' => 0,
            'approve_manually' => 0,
            'contract' => 'ppc',
            'contract_duration' => 10,
            'price' => 10,
            'max_banners' => ''
            //)
        );
    }



    /**
     * Adjust adzone default settings
     * wp_parse_args( args, defaults );
     */
    public static function adzone_set($args = array())
    {
        return wp_parse_args(array('sell' => self::defaults()), $args);
    }





    /*
	 * Adzones for sale array
	 * Array containing Adzone ids that are available for sale.
	 *
	 * @access public
	 * @return array
	*/
	public static function adzones_for_sale($args = array())
	{
		return ADNI_Multi::get_option('_adning_adzones_for_sale', $args);
	}



    /**
     * Adjust Adzone post settings when saving adzone settings
     */
    public static function save_adzone($post = array())
    {
        // Only run this on adzones
        if( strtolower($post['post_type']) === strtolower(ADNI_CPT::$adzone_cpt))
        {
            //echo 'SELL';
            //echo '<pre>'.print_r($post, true).'</pre>';
            $set = wp_parse_args( $post['sell'], self::defaults() );
            $a_id = $post['post_id'];
            $adzones_for_sale = self::adzones_for_sale();

            if( $set['enable'] )
            {
                //echo '<pre>'.print_r($set, true).'</pre>';
                $adzones_for_sale[$a_id] = array(
                    'contract' => $set['contract'],
                    'duration' => $set['contract_duration'],
                    'price' => $set['price']
                );
                ADNI_Multi::update_option('_adning_adzones_for_sale', $adzones_for_sale);
            }
            else
            {
                if( array_key_exists($a_id, $adzones_for_sale)) 
                {
                    unset($adzones_for_sale[$a_id]);
                    ADNI_Multi::update_option('_adning_adzones_for_sale', $adzones_for_sale);
                }
            }
        }
       
        return $post;
    }






    /**
     * Adds a Sell settings box to the adzone settings page.
     */
    public static function adzone_sell_box($adzone = array())
    {
        //print_r($adzone);
        $id = $adzone['post']->ID;
        $args = $adzone['args'];
        
        $h = '';
        //$h.= '<pre class="clearFix">'.print_r($args, true).'</pre>';
        $h.= '<div id="sell_adzone_settings" class="spr_column-inner left_column clearFix">
			<div class="spr_wrapper">
				<div class="option_box">
					<div class="info_header">
						<span class="nr"><svg viewBox="0 0 288 512" style="height:20px;"><path fill="currentColor" d="M209.2 233.4l-108-31.6C88.7 198.2 80 186.5 80 173.5c0-16.3 13.2-29.5 29.5-29.5h66.3c12.2 0 24.2 3.7 34.2 10.5 6.1 4.1 14.3 3.1 19.5-2l34.8-34c7.1-6.9 6.1-18.4-1.8-24.5C238 74.8 207.4 64.1 176 64V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48h-2.5C45.8 64-5.4 118.7.5 183.6c4.2 46.1 39.4 83.6 83.8 96.6l102.5 30c12.5 3.7 21.2 15.3 21.2 28.3 0 16.3-13.2 29.5-29.5 29.5h-66.3C100 368 88 364.3 78 357.5c-6.1-4.1-14.3-3.1-19.5 2l-34.8 34c-7.1 6.9-6.1 18.4 1.8 24.5 24.5 19.2 55.1 29.9 86.5 30v48c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-48.2c46.6-.9 90.3-28.6 105.7-72.7 21.5-61.6-14.6-124.8-72.5-141.7z"></path></svg></span>
						<span class="text">'.__('Sell Ad spots','adn').'</span>
						<input type="submit" value="'.__('Save Adzone','adn').'" class="button-primary" name="save_adzone" style="width:auto;float:right;margin:8px;">
					</div>
					<!-- end .info_header -->
					
					<div class="spr_column">
						<div class="spr_column-inner">
							<div class="spr_wrapper">
								<div class="input_container">
									<p>
										'.__('','adn').'
									</p>
								</div>
							</div>
						</div>
                    </div>';

                    $sell_ad_spots = array_key_exists('enable',$args['sell']) ? $args['sell']['enable'] : 0;
		            $sell_ad_spots = $sell_ad_spots === '' ? 0 : $sell_ad_spots;
                    $h.= ADNI_Templates::switch_btn(array(
                        'title' => __('Sell AD Spots','adn'),
                        'id' => 'sellAdSpots',
                        'name' => 'sell[enable]',
                        'checked' => $sell_ad_spots,
                        'value' => 1,
                        'chk-on' => __('YES','adn'),
                        'chk-off' => __('NO','adn'),
                        'chk-high' => 1,
                        'column' => array(
                            'size' => 'col-3',
                            'desc' => __('Sell available ad spots in this adzone.','adn'),
                        )
                    ));


                    //$h.= '<div class="clearFix"></div>';
                    $contract = $args['sell']['contract'];
                    $h.= ADNI_Templates::spr_column(array(
                        'col' => 'spr_col-3',
                        'title' => __('Contract Type','adn'),
                        'desc' => __('Select a contract type.','adn'),
                        'content' => '<select name="sell[contract]" class=""><option value="ppc" '.selected( $contract, 'ppc', false ).'>'.__('Pay per click','adn').'</option><option value="ppv" '.selected( $contract, 'ppv', false ).'>'.__('Pay per view','adn').'</option><option value="ppd" '.selected( $contract, 'ppd', false ).'>'.__('Pay per day','adn').'</option></select>'
                    ));

                    $h.= ADNI_Templates::spr_column(array(
                        'col' => 'spr_col-3',
                        'title' => __('Contract Duration','adn'),
                        'desc' => __('Select the duration amount (int value) for the contract.','adn'),
                        'content' => ADNI_Templates::inpt_cont(array(
                                'type' => 'text',
                                'show_icon' => 1,
                                'icon' => 'clock-o',
                                'width' => '100%',
                                'name' => 'sell[contract_duration]',
                                'value' => $args['sell']['contract_duration'],
                                'placeholder' => ''
                            ))
                    ));

                    $sell_approve_manually = array_key_exists('approve_manually',$args['sell']) ? $args['sell']['approve_manually'] : 0;
		            $sell_approve_manually = $sell_approve_manually === '' ? 0 : $sell_approve_manually;
                    $h.= ADNI_Templates::switch_btn(array(
                        'title' => __('Approve Manually','adn'),
                        'id' => 'sellApproveManually',
                        'name' => 'sell[approve_manually]',
                        'checked' => $sell_approve_manually,
                        'value' => 1,
                        'chk-on' => __('YES','adn'),
                        'chk-off' => __('NO','adn'),
                        'chk-high' => 1,
                        'column' => array(
                            'size' => 'col-3',
                            'desc' => __('Do you want each banner to be approved manually before going live?','adn'),
                        )
                    ));

                    $h.= '<div class="clearFix"></div>';

                    $h.= ADNI_Templates::spr_column(array(
                        'col' => 'spr_col-3',
                        'title' => __('Price','adn'),
                        'desc' => __('Price for the selected contract duration.','adn'),
                        'content' => ADNI_Templates::inpt_cont(array(
                                'type' => 'text',
                                'show_icon' => 1,
                                'icon' => 'money',
                                'width' => '100%',
                                'name' => 'sell[price]',
                                'value' => $args['sell']['price'],
                                'placeholder' => ''
                            ))
                    ));

                    $h.= ADNI_Templates::spr_column(array(
                        'col' => 'spr_col-3',
                        'title' => __('Max. amount of banners','adn'),
                        'desc' => __('The maximum amount of banners allowed in this adzone (int value). Leave empty for unlimited.','adn'),
                        'content' => ADNI_Templates::inpt_cont(array(
                                'type' => 'text',
                                'show_icon' => 1,
                                'icon' => 'pencil',
                                'width' => '100%',
                                'name' => 'sell[max_banners]',
                                'value' => $args['sell']['max_banners'],
                                'placeholder' => ''
                            ))
                    ));

                    
                $h.= '</div>
            </div>
        </div>';
        // end .spr_column-inner


        echo $h;
    }







    /*
	 * Create the database tables the plugin needs to function.
	 *
	 * @access public
	 * @return void
	*/
	public static function create_tables() 
	{
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$wpdb->hide_errors();
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) 
		{
			if ( ! empty($wpdb->charset ) ) 
			{
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty($wpdb->collate ) )
			{
				$collate .= " COLLATE $wpdb->collate";
			}
		}
		
		
		// Sell database
		$sql_adning_sell = "CREATE TABLE " . $wpdb->prefix . "adning_sell (
			id int(11) NOT NULL AUTO_INCREMENT,
			time int(11) NOT NULL,
			user_id int(11) NOT NULL,
			email VARCHAR( 80 ) NOT NULL,
			ip_address VARCHAR( 50 ) NOT NULL,
			adzone_id mediumint(9) NOT NULL,
			status VARCHAR( 50 ) NOT NULL,
			price VARCHAR(50) NOT NULL,
			am_paid VARCHAR(50) NOT NULL,
			pos VARCHAR(50) NOT NULL,
			trans_date VARCHAR(100) NOT NULL,
			transaction VARCHAR(200) NOT NULL,
			provider VARCHAR( 50 ) NOT NULL,
			UNIQUE KEY id (id),
			KEY time (time)
		) ".$collate.";";
		dbDelta( $sql_adning_sell );
	}

}
endif;
?>