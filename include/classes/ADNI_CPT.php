<?php
// Exit if accessed directly
if ( ! defined( "ABSPATH" ) ) exit;
if ( ! class_exists( 'ADNI_CPT' ) ) :

class ADNI_CPT {
	
	public static $banner_cpt = 'ADNI_banners';
	public static $adzone_cpt = 'ADNI_adzones';
	public static $campaign_cpt = 'ADNI_campaigns';
	
	public function __construct() 
	{
		// Actions ------------------------------------------------------
		add_action('init', array(__CLASS__, 'register_posttypes'));
		add_action( 'pre_get_posts', array(__CLASS__,'filter_cpt_listing_by_author') );
		
		// Filters ------------------------------------------------------
		add_filter( 'admin_url', array(__CLASS__,'custom_add_new_link'), 10, 2 );
		add_filter( 'single_template', array(__CLASS__,'file_template'));

		add_filter( 'admin_url', array(__CLASS__,'change_add_new_link_for_banners'), 10, 2 );
		add_filter( 'admin_url', array(__CLASS__,'change_add_new_link_for_adzones'), 10, 2 );
		add_filter( 'admin_url', array(__CLASS__,'change_add_new_link_for_campaigns'), 10, 2 );
		add_action( 'wp_trash_post', array(__CLASS__,'trash_post'));
		add_action( 'before_delete_post', array(__CLASS__,'delete_post'));
	}
	



	/**
	 * Trash banner / post
	 */
	public static function trash_post( $post_id )
	{
		$post_type = get_post_type( $post_id );
		if($post_type === strtolower(self::$banner_cpt) || $post_type === strtolower(self::$adzone_cpt))
		{
			$auto_pos = ADNI_Main::auto_positioning();
			if( array_key_exists($post_id, $auto_pos)) 
			{
				$post = self::load_post($post_id, array('filter' => 0));
				$new_args = $post['args'];
				$new_args['positioning'] = '';
				$id = self::add_update_post($new_args);

				unset($auto_pos[$post_id]);
				ADNI_Multi::update_option('_adning_auto_positioning', $auto_pos);
            }
		}
	}


	/**
	 * Remove - Delete banner / adzone
	 */
	public static function delete_post( $post_id )
	{
		$post_type = get_post_type( $post_id );
		if($post_type === strtolower(self::$banner_cpt) || $post_type === strtolower(self::$adzone_cpt))
		{
			// Remove folder
			ADNI_Main::delete_dir(ADNI_UPLOAD_DIR.'banners/'.$post_id);

			// Remove stats
			if( !class_exists('sTrack_DB') )
			{
				$group = $post_type === strtolower(self::$adzone_cpt) ? 'id_2' : 'id_1';
				sTrack_DB::delete_stats(array('id' => $post_id, 'group' => $group));
			}
		}
	}



	
	public static function change_add_new_link_for_banners( $url, $path )
	{
		if( $path === 'post-new.php?post_type='.strtolower(self::$banner_cpt) ) 
		{
			$url = get_admin_url().'admin.php?page=adning&view=banner';
		}
		return $url;
	}
	public static function change_add_new_link_for_adzones( $url, $path )
	{
		if( $path === 'post-new.php?post_type='.strtolower(self::$adzone_cpt) ) 
		{
			$url = get_admin_url().'admin.php?page=adning&view=adzone';
		}
		return $url;
	}
	public static function change_add_new_link_for_campaigns( $url, $path )
	{
		if( $path === 'post-new.php?post_type='.strtolower(self::$campaign_cpt) ) 
		{
			$url = get_admin_url().'admin.php?page=adning&view=campaign';
		}
		return $url;
	}
	
	
	/*
	 * Add custom capabilities
	 *
	 * @access public
	 * @return null
	*/
	public static function add_custom_caps($args = array())
	{
		$defaults = array(
			'role' => '',
			'cpt' => self::$banner_cpt
		);
		$args = wp_parse_args($args, $defaults);
		
		$roles = wp_roles();
		$cpt = $args['cpt'];
		$cap = ADNI_Main::ADNI_capability($args['role']);
		$roles_with_cap = ADNI_Main::get_roles_with_cap($cap);
		
		
		// First clean/remove all custom capabilities.
		self::remove_custom_caps(array('roles' => $roles, 'cpt' => $cpt));

		
		foreach($roles->roles as $key => $the_role) 
		{
			$role = get_role($key);
			if( array_key_exists($cap, $role->capabilities))
			{
				$role->add_cap( 'publish_'.$cpt.'s');
				$role->add_cap( 'edit_'.$cpt.'s');
				$role->add_cap( 'edit_published_'.$cpt.'s');
				$role->add_cap( 'delete_'.$cpt.'s');
				$role->add_cap( 'delete_published_'.$cpt.'s');
				
				
				//echo '<pre>'.print_r($roles_with_cap,true).'</pre>';
				$grant = in_array($key, $roles_with_cap) ? true : false;
				//echo $key;
				//$grant = $key == 'administrator' || $key == 'editor' ? true : false;
				$role->add_cap( 'edit_others_'.$cpt.'s', $grant);
				$role->add_cap( 'delete_others_'.$cpt.'s', $grant);
				$role->add_cap( 'read_private_'.$cpt.'s', $grant);
				//echo '<pre>'.print_r($role,true).'</pre>';
			}
		}
	}
	
	
	/*
	 * Remove all custom capabilities
	 *
	 * @access public
	 * @return null
	*/
	public static function remove_custom_caps($args = array())
	{
		$defaults = array(
			'roles' => wp_roles(),
			'cpt' => self::$banner_cpt
		);
		$args = wp_parse_args($args, $defaults);
		
		foreach($args['roles']->roles as $key => $the_role) 
		{
			$role = get_role($key);
			$role->remove_cap( 'publish_'.$args['cpt'].'s');
			$role->remove_cap( 'edit_'.$args['cpt'].'s');
			$role->remove_cap( 'edit_others_'.$args['cpt'].'s');
			$role->remove_cap( 'edit_published_'.$args['cpt'].'s');
			$role->remove_cap( 'delete_'.$args['cpt'].'s');
			$role->remove_cap( 'delete_others_'.$args['cpt'].'s');
			$role->remove_cap( 'delete_published_'.$args['cpt'].'s');
			$role->remove_cap( 'read_private_'.$args['cpt'].'s');
		}	
	}
	
	
	
	/*
	 * Create CPTs
	 *
	 * @access public
	 * @return null
	 *
	*/
	public static function register_posttypes() 
	{
		
		$cpts = array();
		$cpts[0] = array(
			'name'               => __('Banners', 'adn'),
			'name_clean'         => self::$banner_cpt,
			'singular_name'		 => __('Banner', 'adn'),
			'show_in_menu'       => 'adning',
			// http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
			// https://wordpress.stackexchange.com/questions/120442/using-custom-meta-capabilities-on-custom-post-type
			'capability_type'    => self::$banner_cpt, //'post',
			'capabilities'       => array(
				'publish_posts' => 'publish_'.self::$banner_cpt.'s',
				'edit_posts' => 'edit_'.self::$banner_cpt.'s',
				'edit_others_posts' => 'edit_others_'.self::$banner_cpt.'s',
				'edit_published_posts' => 'edit_published_'.self::$banner_cpt.'s',
				'delete_posts' => 'delete_'.self::$banner_cpt.'s',
				'delete_others_posts' => 'delete_others_'.self::$banner_cpt.'s',
				'delete_published_posts' => 'delete_published_'.self::$banner_cpt.'s',
				'read_private_posts' => 'read_private_'.self::$banner_cpt.'s',
				'edit_post' => 'edit_'.self::$banner_cpt,
				'delete_post' => 'delete_'.self::$banner_cpt,
				'read_post' => 'read_'.self::$banner_cpt,
			),
			'supports'           => apply_filters( 'ADNI_banners_cpt_supports', array('title') ), //$supports = array('title','editor','author','thumbnail','excerpt','comments','revisions', 'custom-fields');
			'taxonomies'         => array() // 'post_tag', 'category'
		);
		$cpts[1] = array(
			'name'               => __('AD Zones', 'adn'),
			'name_clean'         => self::$adzone_cpt,
			'singular_name'		 => __('AD Zone', 'adn'),
			'show_in_menu'       => 'adning',
			'capability_type'    => self::$adzone_cpt,
			'capabilities'       => array(
				'publish_posts' => 'publish_'.self::$adzone_cpt.'s',
				'edit_posts' => 'edit_'.self::$adzone_cpt.'s',
				'edit_others_posts' => 'edit_others_'.self::$adzone_cpt.'s',
				'edit_published_posts' => 'edit_published_'.self::$adzone_cpt.'s',
				'delete_posts' => 'delete_'.self::$adzone_cpt.'s',
				'delete_others_posts' => 'delete_others_'.self::$adzone_cpt.'s',
				'delete_published_posts' => 'delete_published_'.self::$adzone_cpt.'s',
				'read_private_posts' => 'read_private_'.self::$adzone_cpt.'s',
				'edit_post' => 'edit_'.self::$adzone_cpt,
				'delete_post' => 'delete_'.self::$adzone_cpt,
				'read_post' => 'read_'.self::$adzone_cpt,
			),
			'supports'           => apply_filters( 'ADNI_adzones_cpt_supports', array('title') ), //$supports = array('title','editor','author','thumbnail','excerpt','comments','revisions', 'custom-fields');
			'taxonomies'         => array() // 'post_tag', 'category'
		);
		$cpts[2] = array(
			'name'               => __('Campaigns', 'adn'),
			'name_clean'         => self::$campaign_cpt,
			'singular_name'		 => __('Campaign', 'adn'),
			'show_in_menu'       => 'adning',
			'capability_type'    => self::$campaign_cpt,
			'capabilities'       => array(
				'publish_posts' => 'publish_'.self::$campaign_cpt.'s',
				'edit_posts' => 'edit_'.self::$campaign_cpt.'s',
				'edit_others_posts' => 'edit_others_'.self::$campaign_cpt.'s',
				'edit_published_posts' => 'edit_published_'.self::$campaign_cpt.'s',
				'delete_posts' => 'delete_'.self::$campaign_cpt.'s',
				'delete_others_posts' => 'delete_others_'.self::$campaign_cpt.'s',
				'delete_published_posts' => 'delete_published_'.self::$campaign_cpt.'s',
				'read_private_posts' => 'read_private_'.self::$campaign_cpt.'s',
				'edit_post' => 'edit_'.self::$campaign_cpt,
				'delete_post' => 'delete_'.self::$campaign_cpt,
				'read_post' => 'read_'.self::$campaign_cpt,
			),
			'supports'           => apply_filters( 'ADNI_campaigns_cpt_supports', array('title') ), //$supports = array('title','editor','author','thumbnail','excerpt','comments','revisions', 'custom-fields');
			'taxonomies'         => array() // 'post_tag', 'category'
		);
		
		foreach( $cpts as $cpt )
		{	
			$labels = array(
				'name' 				=> $cpt['name'],
				'singular_name'		=> $cpt['singular_name'],
				'add_new' 			=> sprintf( __( 'Add New %s', 'wpproads' ), $cpt['singular_name']),
				'add_new_item' 		=> sprintf( __( 'Add New %s', 'wpproads' ), $cpt['singular_name']),
				'edit_item' 		=> sprintf( __( 'Edit %s', 'wpproads' ), $cpt['singular_name']),
				'new_item' 			=> sprintf( __( 'New %s', 'wpproads' ), $cpt['singular_name']),
				'view_item' 		=> sprintf( __( 'View %s', 'wpproads' ), $cpt['singular_name']),
				'search_items' 		=> sprintf( __( 'Search %s', 'wpproads' ), $cpt['name']),
				'not_found' 		=> sprintf( __( 'No %s Found', 'wpproads' ), $cpt['name']),
				'not_found_in_trash'=> sprintf( __( 'No %s Found in Trash', 'wpproads' ), $cpt['name']),
				'parent_item_colon' => '',
				'menu_name'			=> $cpt['name']
			);
			
			$taxonomies = $cpt['taxonomies']; 
			$supports = $cpt['supports'];
			
			$post_type_args = array(
				'labels' 			  => $labels,
				'singular_label' 	  => $cpt['name'],
				'public' 			  => true, // false
				'show_ui' 			  => true,
				'publicly_queryable'  => true, // false
				'query_var'			  => true,
				'capability_type' 	  => $cpt['capability_type'],
				'capabilities'      => $cpt['capabilities'],
				'map_meta_cap'      => true,
				'exclude_from_search' => true,
				'has_archive' 		    => false,
				'hierarchical' 		  => false,
				'rewrite' 			  => array('slug' => $cpt['name_clean'], 'with_front' => false ),
				'supports' 			  => $supports,
				'show_in_menu'        => $cpt['show_in_menu'],
				'taxonomies'		  => $taxonomies
			 );
			 register_post_type($cpt['name_clean'], $post_type_args);
			 
			 // Extra Filters
			 add_filter('manage_edit-'.strtolower($cpt['name_clean']).'_columns', array(__CLASS__, $cpt['name_clean'].'_columns'));
			 add_action('manage_posts_custom_column',  array(__CLASS__, $cpt['name_clean'].'_show_columns'));
		}
	}
	
	
	
	/*
	 * Custom "add new" link for post type
	 *
	 * @access public
	 * @return url
	*/
	public static function custom_add_new_link( $url, $path )
	{
		if( $path === 'post-new.php?post_type='.self::$banner_cpt ) {
			//$url = get_admin_url().'admin.php?page='.self::$banner_cpt;
			$url = get_admin_url().'admin.php?page=adning&view=banner';
		}
		elseif( $path === 'post-new.php?post_type='.self::$adzone_cpt ) {
			//$url = get_admin_url().'admin.php?page='.self::$banner_cpt;
			$url = get_admin_url().'admin.php?page=adning&view=adzone';
		}
		return $url;
	}
	
	
	
	
	
	/*
	 * Filter custom post types by author - (Only show posts from author) * todo: post count is wrong.
	 *
	 * @access public
	 * @return url
	*/
	public static function filter_cpt_listing_by_author( $wp_query_obj ) 
	{
		// Front end, do nothing
		if( !is_admin() )
			return;
	
		global $current_user, $pagenow;
		wp_get_current_user();
	
		// http://php.net/manual/en/function.is-a.php
		if( !is_a( $current_user, 'WP_User') )
			return;
	
		// Not the correct screen, bail out
		if( 'edit.php' != $pagenow )
			return;
	
		// Not the correct post type, bail out
		$post_types = array(
			strtolower(self::$banner_cpt),
			strtolower(self::$adzone_cpt),
			strtolower(self::$campaign_cpt)
		);
		if( !in_array( $wp_query_obj->query['post_type'], $post_types) )
			return;
		
		$cap = '';
		if( strtolower($wp_query_obj->query['post_type']) === strtolower(self::$campaign_cpt) ){
			$cap = ADNI_CAMPAIGNS_ROLE;
		}
		if( strtolower($wp_query_obj->query['post_type']) === strtolower(self::$banner_cpt) ){
			$cap = ADNI_BANNERS_ROLE;
		}
		if( strtolower($wp_query_obj->query['post_type']) === strtolower(self::$adzone_cpt) ){
			$cap = ADNI_ADZONES_ROLE;
		}
		// If the user has no admin rights, filter the post listing
		if( !empty($cap) && !current_user_can( $cap ) )
			$wp_query_obj->set('author', $current_user->ID );
	}
	
	
	
	
	// Banners ----------------------------------------------------------
	public static function ADNI_banners_columns( $existing_columns ) 
	{
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns          = array();
		$columns['cb']    = '<input type="checkbox" />';
		//$columns['b_banner'] = __('<img src="'.WP_ADS_URL.'images/banner_icon_20.png" />', 'wpproads');
		$columns['_adn_b_name'] = __('Title', 'adn');
		$columns['_adn_b_advertiser'] = __('Advertiser', 'adn');
		$columns['_adn_b_status'] = __('Status', 'adn');
		/*$columns['b_campaign'] = __('Campaign', 'wpproads');
		$columns['b_status'] = __('Status', 'wpproads');
		$columns['b_stats'] = __('Stats', 'wpproads');*/
		//$columns['b_filetype'] = __('Type', 'wpproads');
		//$columns['b_adzone'] = __('adzone', 'wpproads');
		
		//return $columns;
		return array_merge( $columns, $existing_columns );
	}
	
	public static function ADNI_banners_show_columns($name) 
	{
		global $post;
		
		switch ($name) 
		{
			case '_adn_b_name' :
				
				$can_edit = get_current_user_id() == $post->post_author || current_user_can(ADNI_BANNERS_ROLE) ? 1 : 0;
				$edit_url = $can_edit ? get_admin_url().'admin.php?page=adning&view=banner&id='.$post->ID : '';
				$title            = _draft_or_post_title();
				$post_type_object = get_post_type_object( $post->post_type );
				$can_edit_post    = $can_edit ? current_user_can( $post_type_object->cap->edit_posts, $post->ID ) : 0;
				$can_delete_post  = $can_edit ? current_user_can( $post_type_object->cap->delete_posts, $post->ID ) : 0;
				
				echo '<strong>';
					echo $can_edit ? '<a class="row-title" href="'.esc_url( $edit_url ).'">'.$title.'</a>' : '<span class="row-title">'.$title.'</span>';
					_post_states( $post );
				echo '</strong>';
				
				// Get actions
				$actions = array();
				$actions['id'] = '#' . $post->ID;
				
				if( $can_edit )
				{
					if ( $can_edit_post && $post->post_status != 'trash' ) 
					{
						$actions['edit'] = '<a href="'. esc_url( $edit_url ).'" title="'.esc_attr( __( 'Edit Banner', 'adn' ) ).'">'.__( 'Edit', 'adn' ).'</a>';
					}
					if( $can_delete_post ) 
					{
						if( $post->post_status == 'trash' ) 
						{
							$actions['untrash'] = '<a title="'.esc_attr(__( 'Restore this banner from the Trash', 'adn')).'" href="'. wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID )), 'untrash-post_' . $post->ID ).'">'. __( 'Restore', 'adn' ).'</a>';
						}
						elseif( EMPTY_TRASH_DAYS ) 
						{
							$actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this banner to the Trash', 'adn' ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', 'adn' ) . '</a>';
						}
	
						if( $post->post_status == 'trash' || ! EMPTY_TRASH_DAYS ) 
						{
							$actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this banner permanently', 'adn' ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', 'adn' ) . '</a>';
						}
					}
					if ( $post_type_object->public ) 
					{
						if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) 
						{
							if ( $can_edit_post )
								$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'adn' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'adn' ) . '</a>';
						} 
						elseif ( $post->post_status != 'trash' ) 
						{
							$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'adn' ), $title ) ) . '" rel="permalink">' . __( 'View', 'adn' ) . '</a>';
						}
					}
				}
				
				
				$actions = apply_filters( 'post_row_actions', $actions, $post );
				
				echo '<div class="row-actions">';
					$i = 0;
					$action_count = sizeof($actions);
	
					foreach ( $actions as $action => $link ) 
					{
						$i++;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo '<span class="' . $action . '">' . $link . $sep . '</span>';
					}
				echo '</div>';
				
				get_inline_data( $post );
			
			break;
			case '_adn_b_advertiser':
				echo get_the_author_meta('display_name', $post->post_author);
			break;
			case '_adn_b_status':
				$post_args = ADNI_Multi::get_post_meta($post->ID, '_adning_args', array());
				$status = self::status(array('key' => $post_args['status']));
				echo $status['name'];
			break;
		}
	}
	
	// adzones ----------------------------------------------------------
	public static function ADNI_adzones_columns( $existing_columns ) 
	{
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns          = array();
		$columns['cb']    = '<input type="checkbox" />';
		//$columns['b_banner'] = __('<img src="'.WP_ADS_URL.'images/banner_icon_20.png" />', 'wpproads');
		$columns['_adn_a_name'] = __('Title', 'wpproads');
		/*$columns['b_advertiser'] = __('Advertiser', 'wpproads');
		$columns['b_campaign'] = __('Campaign', 'wpproads');
		$columns['b_status'] = __('Status', 'wpproads');
		$columns['b_stats'] = __('Stats', 'wpproads');*/
		//$columns['b_filetype'] = __('Type', 'wpproads');
		//$columns['b_adzone'] = __('adzone', 'wpproads');
		
		//return $columns;
		return array_merge( $columns, $existing_columns );
	}
	
	public static function ADNI_adzones_show_columns($name) 
	{
		global $post;
		
		switch ($name) 
		{
			case '_adn_a_name' :
				
				$can_edit = get_current_user_id() == $post->post_author || current_user_can(ADNI_ADZONES_ROLE) ? 1 : 0;
				$edit_url = $can_edit ? get_admin_url().'admin.php?page=adning&view=adzone&id='.$post->ID : '';
				$title            = _draft_or_post_title();
				$post_type_object = get_post_type_object( $post->post_type );
				$can_edit_post    = $can_edit ? current_user_can( $post_type_object->cap->edit_post, $post->ID ) : 0;
				$can_delete_post  = $can_edit ? current_user_can( $post_type_object->cap->delete_posts, $post->ID ) : 0;

				echo '<strong>';
					echo $can_edit ? '<a class="row-title" href="'.esc_url( $edit_url ).'">'.$title.'</a>' : '<span class="row-title">'.$title.'</span>';
					_post_states( $post );
				echo '</strong>';
				
				// Get actions
				$actions = array();
				$actions['id'] = '#' . $post->ID;
				
				if( $can_edit )
				{
					if ( $can_edit_post && $post->post_status != 'trash' ) 
					{
						$actions['edit'] = '<a href="'. esc_url( $edit_url ).'" title="'.esc_attr( __( 'Edit adzone', 'adn' ) ).'">'.__( 'Edit', 'adn' ).'</a>';
					}
					if( $can_delete_post ) 
					{
						if( $post->post_status == 'trash' ) 
						{
							$actions['untrash'] = '<a title="'.esc_attr(__( 'Restore this adzone from the Trash', 'adn')).'" href="'. wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID )), 'untrash-post_' . $post->ID ).'">'. __( 'Restore', 'adn' ).'</a>';
						}
						elseif( EMPTY_TRASH_DAYS ) 
						{
							$actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this adzone to the Trash', 'adn' ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', 'adn' ) . '</a>';
						}
	
						if( $post->post_status == 'trash' || ! EMPTY_TRASH_DAYS ) 
						{
							$actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this adzone permanently', 'adn' ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', 'adn' ) . '</a>';
						}
					}
					/*if ( $post_type_object->public ) 
					{
						if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) 
						{
							if ( $can_edit_post )
								$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'adn' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'adn' ) . '</a>';
						} 
						elseif ( $post->post_status != 'trash' ) 
						{
							$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'adn' ), $title ) ) . '" rel="permalink">' . __( 'View', 'adn' ) . '</a>';
						}
					}*/
				}
				
				
				$actions = apply_filters( 'post_row_actions', $actions, $post );
				
				echo '<div class="row-actions">';
					$i = 0;
					$action_count = sizeof($actions);
	
					foreach ( $actions as $action => $link ) 
					{
						$i++;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo '<span class="' . $action . '">' . $link . $sep . '</span>';
					}
				echo '</div>';
				
				get_inline_data( $post );
			
			break;
		}
	}

	// campaigns ----------------------------------------------------------
	public static function ADNI_campaigns_columns( $existing_columns ) 
	{
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns          = array();
		$columns['cb']    = '<input type="checkbox" />';
		//$columns['b_banner'] = __('<img src="'.WP_ADS_URL.'images/banner_icon_20.png" />', 'wpproads');
		$columns['_adn_c_name'] = __('Title', 'adn');
		/*$columns['b_advertiser'] = __('Advertiser', 'wpproads');
		$columns['b_campaign'] = __('Campaign', 'wpproads');
		$columns['b_status'] = __('Status', 'wpproads');
		$columns['b_stats'] = __('Stats', 'wpproads');*/
		//$columns['b_filetype'] = __('Type', 'wpproads');
		//$columns['b_adzone'] = __('adzone', 'wpproads');
		
		//return $columns;
		return array_merge( $columns, $existing_columns );
	}
	
	public static function ADNI_campaigns_show_columns($name) 
	{
		global $post;
		
		switch ($name) 
		{
			case '_adn_c_name' :
				
				$can_edit = get_current_user_id() == $post->post_author || current_user_can(ADNI_CAMPAIGNS_ROLE) ? 1 : 0;
				$edit_url = $can_edit ? get_admin_url().'admin.php?page=adning&view=campaign&id='.$post->ID : '';
				$title            = _draft_or_post_title();
				$post_type_object = get_post_type_object( $post->post_type );
				$can_edit_post    = $can_edit ? current_user_can( $post_type_object->cap->edit_post, $post->ID ) : 0;
				$can_delete_post  = $can_edit ? current_user_can( $post_type_object->cap->delete_posts, $post->ID ) : 0;

				echo '<strong>';
					echo $can_edit ? '<a class="row-title" href="'.esc_url( $edit_url ).'">'.$title.'</a>' : '<span class="row-title">'.$title.'</span>';
					_post_states( $post );
				echo '</strong>';
				
				// Get actions
				$actions = array();
				$actions['id'] = '#' . $post->ID;
				
				if( $can_edit )
				{
					if ( $can_edit_post && $post->post_status != 'trash' ) 
					{
						$actions['edit'] = '<a href="'. esc_url( $edit_url ).'" title="'.esc_attr( __( 'Edit adzone', 'adn' ) ).'">'.__( 'Edit', 'adn' ).'</a>';
					}
					if( $can_delete_post ) 
					{
						if( $post->post_status == 'trash' ) 
						{
							$actions['untrash'] = '<a title="'.esc_attr(__( 'Restore this adzone from the Trash', 'adn')).'" href="'. wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID )), 'untrash-post_' . $post->ID ).'">'. __( 'Restore', 'adn' ).'</a>';
						}
						elseif( EMPTY_TRASH_DAYS ) 
						{
							$actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this adzone to the Trash', 'adn' ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', 'adn' ) . '</a>';
						}
	
						if( $post->post_status == 'trash' || ! EMPTY_TRASH_DAYS ) 
						{
							$actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this adzone permanently', 'adn' ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', 'adn' ) . '</a>';
						}
					}
					/*if ( $post_type_object->public ) 
					{
						if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) 
						{
							if ( $can_edit_post )
								$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'adn' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'adn' ) . '</a>';
						} 
						elseif ( $post->post_status != 'trash' ) 
						{
							$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'adn' ), $title ) ) . '" rel="permalink">' . __( 'View', 'adn' ) . '</a>';
						}
					}*/
				}
				
				
				$actions = apply_filters( 'post_row_actions', $actions, $post );
				
				echo '<div class="row-actions">';
					$i = 0;
					$action_count = sizeof($actions);
	
					foreach ( $actions as $action => $link ) 
					{
						$i++;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo '<span class="' . $action . '">' . $link . $sep . '</span>';
					}
				echo '</div>';
				
				get_inline_data( $post );
			
			break;
		}
	}
	
	
	
	
	
	/* ----------------------------------------------------------------
	 * Custom Single template
	 * ---------------------------------------------------------------- */
	public static function file_template( $single )
	{
		global $wp_query, $post;
		
		// Docs
		if ($post->post_type === strtolower(self::$banner_cpt)){
			if ( is_single() ){
				
				/**
				 * Rewrite check, Snipr_Init.php - function custom_rewrite_tags()
				 * Check which file we need to use based on the query vars.
				 */
				//$file_name = array_key_exists( $snipr_init->edit_rewrite_endpoint, $wp_query->query_vars ) ? 'Snipr_Editor' : 'Snipr_Project';
				
				// checks if the file exists in the theme first,
				// otherwise serve the file from the plugin
				if( $theme_file = locate_template( array( 'adning/single_banner.php' ) ) )
				{
					return $theme_file;
				} 
				else 
				{
					return ADNI_TPL_DIR. '/adn/single_banner.php';
				}
			}
		}
		
		return $single;
	}
	
	
	
	
	public static function default_banner_args($args = array())
	{
		$defaults = array(
			'empty_checkbox_values' => 0  // fix for $_POST not sending empty checkbox values.
		);
		$args = wp_parse_args($args, $defaults);
		
		return apply_filters( 'ADNI_default_banner_args', array(
			'type' => 'banner',
			'status' => 'active',
			'size' => '300x250',
			'size_w' => 300,
			'size_h' => 250,
			'campaigns' => array(),
			'adzones' => array(),
			'responsive' => $args['empty_checkbox_values'] ? 0 : 1,
			'enable_stats' => 0,
			'banner_size' => '300x250', // @since v1.0.7 deprecated - start using size
			'banner_size_w' => 300, // @since v1.0.7 deprecated - start using size_w
			'banner_size_h' => 250, // @since v1.0.7 deprecated - start using size_h
			'banner_url' => '',
			'banner_target' => '',
			'banner_no_follow' => 0,
			'banner_link_masking' => $args['empty_checkbox_values'] ? 0 : 1,
			'banner_content' => '',
			'banner_responsive' => $args['empty_checkbox_values'] ? 0 : 1, // @since v1.0.7 deprecated - start using responsive
			'banner_scale' => $args['empty_checkbox_values'] ? 0 : 1,
			'align' => 'center',
			'wrap_text' => $args['empty_checkbox_values'] ? 0 : 1,
			'positioning' => '',
			'cont_border' => 0,
			'cont_border_color' => '',
			'cont_label' => '',
			'cont_label_pos' => 'left',
			'cont_label_color' => '',
			'bg_takeover_src' => '',
			'bg_takeover_bg_container' => '',
			'bg_takeover_content_container' => '',
			'bg_takeover_top_skin' => '',
			'bg_takeover_bg_color' => '',
			'bg_takeover_content_bg_color' => '',
			'bg_takeover_position' => 'absolute',
			'bg_takeover_top_skin_url' => '',
			'bg_takeover_left_skin_url' => '',
			'bg_takeover_right_skin_url' => '',
			'display_filter' => array(
				'show_hide' => 0,
				'show_desktop' => 1,
            	'show_tablet' => 1,
				'show_mobile' => 1,
				'homepage' => 0,
				'countries' => array(),
				'categories' => array(),
				'tags' => array(),
				'post_types' => array()
			),
			'adsense_settings' => array(),
			'sell' => array()
		));	
	}
	
	public static function default_adzone_args($args = array())
	{
		$defaults = array(
			'empty_checkbox_values' => 0  // fix for $_POST not sending empty checkbox values.
		);
		$args = wp_parse_args($args, $defaults);
		
		return apply_filters( 'ADNI_default_adzone_args', array(
			'type' => 'adzone',
			'description' => '',
			'status' => 'active',
			'size' => '300x250',
			'size_w' => 300,
			'size_h' => 250,
			'campaigns' => array(),
			'responsive' => $args['empty_checkbox_values'] ? 0 : 1,
			'no_banner_filter' => 0,
			'enable_stats' => 0,
			'random_order' => 0,
			'load_single' => 0,
			'load_grid' => 0,
			'grid_columns' => 2,
			'grid_rows' => 2,
			'adzone_size' => '300x250', // @since v1.0.7 deprecated - start using size
			'adzone_size_w' => 300, // @since v1.0.7 deprecated - start using size_w
			'adzone_size_h' => 250, // @since v1.0.7 deprecated - start using size_h
			'adzone_content' => '',
			'adzone_responsive' => $args['empty_checkbox_values'] ? 0 : 1, // @since v1.0.7 deprecated - start using responsive
			'adzone_transition' => '{$Duration:400,x:-1,$Easing:$Jease$.$InQuad}',
			'linked_banners' => array(),
			'align' => 'center',
			'wrap_text' => $args['empty_checkbox_values'] ? 0 : 1,
			'positioning' => '',
			'cont_border' => 0,
			'cont_border_color' => '',
			'cont_label' => '',
			'cont_label_pos' => 'left',
			'cont_label_color' => '',
			'display_filter' => array(
				'show_hide' => 0,
				'show_desktop' => 1,
            	'show_tablet' => 1,
				'show_mobile' => 1,
				'homepage' => 0,
				'countries' => array(),
				'categories' => array(),
				'tags' => array(),
				'post_types' => array()
			)
		));	
	}

	public static function default_campaign_args($args = array())
	{
		$defaults = array(
			'empty_checkbox_values' => 0  // fix for $_POST not sending empty checkbox values.
		);
		$args = wp_parse_args($args, $defaults);
		
		return apply_filters( 'ADNI_default_campaign_args', array(
			'type' => 'campaign',
			'description' => '',
			'status' => 'active',
			'display_filter' => array(
				'years' => array(),
				'months' => array(),
				'days' => array(),
				'time' => array(),
				'weekdays' => array(),
				'countries' => array()
			)
		));
	}
	



	/*
	 * Get type from post_type
	 *
	 * @access public
	 * @return string
	*/
	public static function get_type( $post_type = '' )
	{
		$type = '';

		if( !empty($post_type))
		{
			if( strtolower($post_type) === strtolower(self::$banner_cpt) )
			{
				return 'banner';
			}
			if( strtolower($post_type) === strtolower(self::$adzone_cpt) )
			{
				return 'adzone';
			}
			if( strtolower($post_type) === strtolower(self::$campaign_cpt) )
			{
				return 'campaign';
			}
		}

		return $type;
	}
	
	
	
	/*
	 * Add New or Update existing Post
	 *
	 * @access public
	 * @return id
	*/
	public static function add_update_post( $post, $post_status = 'publish' )
	{
		global $current_user;

		//echo 'ADD_UPDATE - '.$post['post_id'];

		$b_id = $post['post_id'];
		$post_type = $post['post_type'];
		$advertiser = isset($post['advertiser']) ? $post['advertiser'] : $current_user->ID;
		$type = self::get_type($post_type);
		$b_title = !empty($post['title']) ? $post['title'] : sprintf(__('%s created on: %s','adn'), ucfirst($type), date('D j F Y, H:s', current_time( 'timestamp' )) );
		$b_args = array();
		

		$display_filter = array();
		$post['responsive'] = isset($post['responsive']) ? $post['responsive'] : 0;

		// Allow 3rd party plugins to adjust the post data.
		$post = apply_filters( 'ADNI_save_post', $post );

		


		if( $type !== 'campaign')
		{
			$display_filter['homepage'] = isset($post['display_filter']['homepage']) ? $post['display_filter']['homepage'] : 0;
			
			$display_filter['show_desktop'] = isset($post['df_show_desktop']) ? $post['df_show_desktop'] : 0;
			$display_filter['show_tablet'] = isset($post['df_show_tablet']) ? $post['df_show_tablet'] : 0;
			$display_filter['show_mobile'] = isset($post['df_show_mobile']) ? $post['df_show_mobile'] : 0;
			// remove post values we dont need anymore.
			unset($_POST['df_show_desktop']);
			unset($_POST['df_show_tablet']);
			unset($_POST['df_show_mobile']);

			
			if( current_user_can(ADNI_BANNERS_ROLE) && isset($post['display_filter']['post_types']))
			{
				foreach( $post['display_filter']['post_types'] as $key => $post_arr)
				{
					$display_filter['post_types'][$key]['show_hide'] = isset($post_arr['show_hide']) ? $post_arr['show_hide'] : 0;
					$display_filter['post_types'][$key]['ids'] = isset($post_arr['ids']) ? $post_arr['ids'] : array();

					// Taxonomies
					if( isset($post['display_filter']['post_types'][$key]['taxonomies']))
					{
						foreach( $post['display_filter']['post_types'][$key]['taxonomies'] as $tax => $tax_arr)
						{
							//echo $tax;
							//echo '<pre>'.print_r($tax_arr).'</pre>';
							$display_filter['post_types'][$key]['taxonomies'][$tax]['show_hide'] = isset($tax_arr['show_hide']) ? $tax_arr['show_hide'] : 0;
							$display_filter['post_types'][$key]['taxonomies'][$tax]['ids'] = isset($tax_arr['ids']) ? $tax_arr['ids'] : array();
						}
					}
					unset($post['display_filter']['post_types'][$key]);
				}
			}
			
			
			//echo '<pre>'.print_r($display_filter,true).'</pre>';
			if( current_user_can(ADNI_BANNERS_ROLE) )
			{
				// POSITIONING SETTINGS
				$custom_positioning_arr = array();
				$auto_pos = ADNI_Main::auto_positioning();
				if( !empty($post['positioning']))
				{
					$popup_w = isset($post['popup_width']) ? $post['popup_width'] : '';
					$popup_h = isset($post['popup_height']) ? $post['popup_height'] : '';
					$popup_w = empty($popup_w) && $post['positioning'] === 'popup' ? $post['size_w'] : $popup_w;
					$popup_h = empty($popup_h) && $post['positioning'] === 'popup' ? $post['size_h'] : $popup_h;
					
					$custom_pos_arr = array(
						'position_after_x_p' => isset($post['position_after_x_p']) ? $post['position_after_x_p'] : '',
						'popup_width' => $popup_w,
						'popup_height' => $popup_h,
						'popup_bg_color' => isset($post['popup_bg_color']) ? $post['popup_bg_color'] : '',
						'popup_overlay_color' => isset($post['popup_overlay_color']) ? $post['popup_overlay_color'] : '',
						'popup_shadow_color' => isset($post['popup_shadow_color']) ? $post['popup_shadow_color'] : '',
						'popup_cookie_value' => isset($post['popup_cookie_value']) ? $post['popup_cookie_value'] : '',
						'popup_cookie_type' => isset($post['popup_cookie_type']) ? $post['popup_cookie_type'] : '',
						'popup_custom_json' => isset($post['popup_custom_json']) ? $post['popup_custom_json'] : '',
						'inject_where' => isset($post['inject_where']) ? $post['inject_where'] : '',
						'inject_element' => isset($post['inject_element']) ? $post['inject_element'] : '',
					);
					
					foreach($custom_pos_arr as $key => $pos_val)
					{
						if( !empty( $pos_val ))
						{
							$custom_positioning_arr[$key] = $pos_val;
						}
					}
					
					// Add to auto positioning array
					$auto_pos[$b_id] = array(
						'pos' => $post['positioning'],
						'custom' => $custom_positioning_arr
					);
					ADNI_Multi::update_option('_adning_auto_positioning', $auto_pos);
					//update_option('_adning_auto_positioning', $auto_pos);
				}
				else
				{
					if( array_key_exists($b_id, $auto_pos)) {
						unset($auto_pos[$b_id]);
						ADNI_Multi::update_option('_adning_auto_positioning', $auto_pos);
						//update_option('_adning_auto_positioning', $auto_pos);
					}
				}
				// remove post values we dont need anymore.
				unset($post['position_after_x_p']); 
				unset($post['popup_width']);
				unset($post['popup_height']);
				unset($post['popup_bg_color']);
				unset($post['popup_overlay_color']);
				unset($post['popup_shadow_color']);
				unset($post['popup_cookie_value']);
				unset($post['popup_cookie_type']);
				unset($post['popup_custom_json']);
				unset($post['inject_where']);
				unset($post['inject_element']);
			}
			


			
			if($type === 'banner')
			{
				// Adsense data
				$adsense_settings = array(
					'pub_id' => isset($post['adsense_pubid']) ? $post['adsense_pubid'] : '',
					'slot_id' => isset($post['adsense_slotid']) ? $post['adsense_slotid'] : '',
					'type' => isset($post['adsense_type']) ? $post['adsense_type'] : '',
				);
				unset($post['adsense_pubid']);
				unset($post['adsense_slotid']);
				unset($post['adsense_type']);

				if( current_user_can(ADNI_BANNERS_ROLE) )
				{
					// Add banner to adzones
					// 1. remove from previous adzones
					$admin_args = ADNI_Multi::get_post_meta($b_id, '_adning_args', array());
					$admin_args['display_filter']['post_types'] = array();
					$admin_args['campaigns'] = array();
					$post = ADNI_Main::parse_args($post, $admin_args);
					
					if( !empty($admin_args['adzones']))
					{
						foreach($admin_args['adzones'] as $adzone_id)
						{
							self::remove_banner_from_adzone($adzone_id, $b_id);
						}
					}
					if( isset($post['adzones']) && !empty($post['adzones']) )
					{
						// 2. Then add to current selected adzones
						foreach($post['adzones'] as $adzone_id)
						{
							self::add_banner_to_adzone($adzone_id, $b_id);
						}
					}
				}
			}

			if($type === 'adzone')
			{
				if( current_user_can(ADNI_ADZONES_ROLE) )
				{
					// Add banner to adzones
					// 1. remove previous linked banners
					$admin_args = ADNI_Multi::get_post_meta($b_id, '_adning_args', array());
					if( !empty($admin_args['linked_banners']))
					{
						foreach($admin_args['linked_banners'] as $banner_id)
						{
							self::remove_adzone_from_banner($b_id, $banner_id);
						}
					}
					if( isset($post['linked_banners']) && !empty($post['linked_banners']) )
					{
						// 2. Then add to current selected adzones
						foreach($post['linked_banners'] as $banner_id)
						{
							self::add_adzone_to_banner($b_id, $banner_id);
						}
					}
				}
			}
			
			//echo '<pre>'.print_r($post,true).'</pre>';
			if( strtolower($post_type) == strtolower(self::$banner_cpt))
			{
				// If banner gets saved by user without banner role previleges from the frontend
				// Get default values saved by admins
				if( !current_user_can(ADNI_BANNERS_ROLE) && $type === 'banner' )
				{
					$admin_args = ADNI_Multi::get_post_meta($b_id, '_adning_args', array());
					$banner_content = $post['banner_content'];
					//echo '<pre>'.print_r($admin_args,true).'</pre>';
					
					$default_display_filter = self::default_banner_args(array('empty_checkbox_values' => 1));
					$display_filter = ADNI_Main::parse_args($admin_args['display_filter'], $default_display_filter['display_filter']);
					$adsense_settings = $admin_args['adsense_settings'];
					$post = ADNI_Main::parse_args($post, $admin_args);
					$post['banner_content'] = $banner_content;
					/*$post['responsive'] = $admin_args['responsive'];
					$post['size'] = $admin_args['size'];
					$post['size_w'] = $admin_args['size_w'];
					$post['size_h'] = $admin_args['size_h'];
					$post['sell'] = $admin_args['sell'];*/
				}
				
				$b_args = ADNI_Main::parse_args($post, self::default_banner_args(array('empty_checkbox_values' => 1)));
				$b_args = ADNI_Main::parse_args(array('display_filter' => $display_filter, 'adsense_settings' => $adsense_settings), $b_args);
				$b_args['type'] = 'banner';
				$type = 'banner';
			}
			elseif( strtolower($post_type) == strtolower(self::$adzone_cpt))
			{
				$b_args = ADNI_Main::parse_args($post, self::default_adzone_args(array('empty_checkbox_values' => 1)));
				$b_args = ADNI_Main::parse_args(array('display_filter' => $display_filter), $b_args);
				$b_args['type'] = 'adzone';
				$type = 'adzone';
			}
		}
		// End $type !== 'campaign'
		else
		{
			// Type is Campaign
			$display_filter['months']['show_hide'] = isset($post['display_filter']['months']['show_hide']) ? $post['display_filter']['months']['show_hide'] : 0;
			$display_filter['months']['ids'] = isset($post['display_filter']['months']['ids']) ? $post['display_filter']['months']['ids'] : array();
			$display_filter['days']['show_hide'] = isset($post['display_filter']['days']['show_hide']) ? $post['display_filter']['days']['show_hide'] : 0;
			$display_filter['days']['ids'] = isset($post['display_filter']['days']['ids']) ? $post['display_filter']['days']['ids'] : array();
			$display_filter['weekdays']['show_hide'] = isset($post['display_filter']['weekdays']['show_hide']) ? $post['display_filter']['weekdays']['show_hide'] : 0;
			$display_filter['weekdays']['ids'] = isset($post['display_filter']['weekdays']['ids']) ? $post['display_filter']['weekdays']['ids'] : array();
			$display_filter['time']['show_hide'] = isset($post['display_filter']['time']['show_hide']) ? $post['display_filter']['time']['show_hide'] : 0;
			$display_filter['time']['ids'] = isset($post['display_filter']['time']['ids']) ? $post['display_filter']['time']['ids'] : array();
			$display_filter['years']['show_hide'] = isset($post['display_filter']['years']['show_hide']) ? $post['display_filter']['years']['show_hide'] : 0;
			$display_filter['years']['ids'] = isset($post['display_filter']['years']['ids']) ? $post['display_filter']['years']['ids'] : array();

			$b_args = ADNI_Main::parse_args($post, self::default_campaign_args(array('empty_checkbox_values' => 1)));
			unset($post['display_filter']);
			$b_args = ADNI_Main::parse_args(array('display_filter' => $display_filter), $b_args);
			$b_args['type'] = $type;
		}
		//echo '<pre>'.print_r($b_args,true).'</pre>';
		if( !$b_id )
		{
			// Insert new post
			$b_data = array(
				'post_title'       		  => $b_title,
				'post_content'     		  => '',
				'post_category'   		  => array(),
				'post_status'      		  => $post_status,
				'post_type'        		  => $post_type,
				'post_date'               => date('Y-m-d H:i:s', current_time('timestamp')),
				'post_date_gmt'           => date('Y-m-d H:i:s', current_time('timestamp', 1)),
				'post_author'             => $advertiser,
				//'ping_status'             => get_option('default_ping_status'), 
				'ping_status'             => ADNI_Multi::get_option('default_ping_status'), 
				'post_parent'             => 0,
				'menu_order'              => 0,
				'to_ping'                 => '',
				'pinged'                  => '',
				'post_password'           => '',
				'guid'                    => '',
				'post_content_filtered'   => '',
				'post_excerpt'            => '',
				'import_id'               => 0,
				'tags_input'              => '',
				'filter'                  => true	
			);
			
			// Filter to do something with the post data
			$b_data = apply_filters( 'adning_insert_args_data', $b_data );		
			$b_id = wp_insert_post( $b_data );
		}
		else
		{
			$banner_post = self::load_post($b_id, array('filter' => 0));
			if(!empty($banner_post))
			{
				//print_r($post);
				// Merge banner data
				$b_args = wp_parse_args( $b_args, $banner_post['args'] );
				
				$b_data = array(
					'ID'          => $b_id,
					'post_status' => $post_status,
					'post_author' => $advertiser,
					'post_title'  => !empty($post['title']) ? $post['title'] : $banner_post['post']->post_title,
				);
				wp_update_post( $b_data );
			}
		}
		
		update_post_meta($b_id, '_adning_args', $b_args);
		if( $type !== 'campaign')
		{
			update_post_meta($b_id, '_adning_size', $b_args['size']);
		}
		
		return $b_id;
	}
	
	
	
	
	
	/*
	 * Get posts query
	 *
	 * @access public
	 * @return array
	*/
	public static function get_posts( $args = array() ) 
	{	
		$defaults = array(
			'posts_per_page'   => -1,
			'post_type'        => self::$banner_cpt,
			'post_status'      => 'publish'
		);
		$args = wp_parse_args( $args, $defaults );
		
		$query = new WP_Query( $args );
		return $query->get_posts();
	}
	
	
	
	
	/*
	 * Load Post
	 *
	 * @access public
	 * @return array
	*/
	public static function load_post( $id, $args = array() )
	{
		$defaults = array(
			'post_type' => self::$banner_cpt,
			'filter' => 1 // set to false to block the filter option. || ADNI_Filters::show_hide()
		);
		$__args = wp_parse_args($args, $defaults);
		
		if( $id )
		{
			/***
			 * Multisite ___________________________________________________________________ */
			ADNI_Multi::wpmu_load_from_main_start();

			$post = get_post($id);

			if(!empty($post))
			{
				$post_type = !empty($post) ? $post->post_type : $__args['post_type'];
				//echo '<pre>'.print_r($post, true).'</pre>';
				$args = get_post_meta($id, '_adning_args', array());
				$args = !empty($post) ? $args[0] : $args;
				//echo '<pre>'.print_r($args, true).'</pre>';
				
				if( strtolower($post_type) == strtolower(self::$banner_cpt))
				{
					$args = ADNI_Main::parse_args($args, self::default_banner_args());
					//echo '<pre>'.print_r($args, true).'</pre>';
				}
				elseif( strtolower($post_type) == strtolower(self::$adzone_cpt))
				{
					$args = ADNI_Main::parse_args($args, self::default_adzone_args());
					//echo '<pre>'.print_r($args, true).'</pre>';
				}
				elseif( strtolower($post_type) == strtolower(self::$campaign_cpt))
				{
					$args = ADNI_Main::parse_args($args, self::default_campaign_args());
				}
			}

			/***
			 * Multisite ___________________________________________________________________ */
			ADNI_Multi::wpmu_load_from_main_stop();
			
			if(!empty($post))
			{
				// Allow 3rd partys to do stuff when banner gets loaded.
				$post_arr = apply_filters('ADNI_load_post', array('post' => $post, 'args' => $args), $__args['filter']);
				//$post_arr = array('post' => $post, 'args' => $args);
				if(empty($post_arr))
					return array();

				return $__args['filter'] ? ADNI_Filters::show_hide($post_arr) : $post_arr;
			}
			else
			{
				return array();
			}
		}
		else
		{
			if( strtolower($__args['post_type']) == strtolower(self::$banner_cpt))
			{
				$args = ADNI_Main::parse_args($args, self::default_banner_args());
				//echo '<pre>'.print_r($args, true).'</pre>';
			}
			elseif( strtolower($__args['post_type']) == strtolower(self::$adzone_cpt))
			{
				$args = ADNI_Main::parse_args($args, self::default_adzone_args());
			}
			elseif( strtolower($__args['post_type']) == strtolower(self::$campaign_cpt))
			{
				$args = ADNI_Main::parse_args($args, self::default_campaign_args());
			}

			return array('post' => array(), 'args' => $args);
		}
	}
	
	
	
	
	/*
	 * Check if user has access to post
	 *
	 * @access public
	 * @return int || exit/error
	*/
	public static function user_has_access($args = array())
	{
		$defaults = array(
			'id' => 0,
			'author' => 0,
			'post_type' => self::$banner_cpt
		);
		$args = wp_parse_args($args, $defaults);
		
		// Dont check to create new posts - Only when we edit existing posts.
		if( !empty($id))
		{
			if($args['author'] != get_current_user_id())
			{
				if(!current_user_can('edit_others_'.$args['post_type'].'s'))
				{
					$error = new WP_Error('broke', __( "You have no access to this area.", "adn" ));
					//wp_die( $error ); //,$title, $args
					if( is_wp_error( $error )){
					   $error_string = $error->get_error_message();
					   echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
					}
					exit;
				}
			}
		}
		
		return 1;
	}





	/*
	 * Remove banner from adzone linked_banners
	 *
	 * @access public
	*/
	public static function remove_banner_from_adzone($aid, $bid)
	{
		$adzone_args = ADNI_multi::get_post_meta($aid, '_adning_args', array());
		if( !empty($adzone_args) && array_key_exists('linked_banners', $adzone_args))
		{
			if (($key = array_search($bid, $adzone_args['linked_banners'])) !== false) 
			{
				unset($adzone_args['linked_banners'][$key]);
				ADNI_multi::update_post_meta($aid, '_adning_args', $adzone_args);
			}	
		}
	}

	/*
	 * Remove adzone from banner
	 *
	 * @access public
	*/
	public static function remove_adzone_from_banner($aid, $bid)
	{
		$banner_args = ADNI_multi::get_post_meta($bid, '_adning_args', array());
		if( !empty($banner_args) && array_key_exists('adzones', $banner_args))
		{
			if (($key = array_search($aid, $banner_args['adzones'])) !== false) 
			{
				unset($banner_args['adzones'][$key]);
				ADNI_multi::update_post_meta($bid, '_adning_args', $banner_args);
			}	
		}
	}



	/**
     * Add banner to adzone linked_banners
     */
    public static function add_banner_to_adzone($aid, $bid)
    {
        if( !empty($aid))
        {
			$adzone_args = ADNI_multi::get_post_meta($aid, '_adning_args', array());
			if( !empty($adzone_args) && array_key_exists('linked_banners', $adzone_args))
			{
				if( !in_array($bid, $adzone_args['linked_banners']) )
				{
					$linked_banners = wp_parse_args(array($bid), $adzone_args['linked_banners']);
					$adzone_args = wp_parse_args(array('linked_banners' => $linked_banners), $adzone_args);
					ADNI_multi::update_post_meta($aid, '_adning_args', $adzone_args);
					// Also add the adzone to banner.
					self::add_adzone_to_banner($aid, $bid);
				}
			}   
        }
	}
	
	/**
     * Add adzone to banner
     */
    public static function add_adzone_to_banner($aid, $bid)
    {
        if( !empty($aid))
        {
			$banner_args = ADNI_multi::get_post_meta($bid, '_adning_args', array());
			if( !empty($banner_args) && array_key_exists('adzones', $banner_args))
			{
				if( !in_array($aid, $banner_args['adzones']) )
				{
					$adzones = wp_parse_args(array($aid), $banner_args['adzones']);
					$banner_args = wp_parse_args(array('adzones' => $adzones), $banner_args);
					ADNI_multi::update_post_meta($bid, '_adning_args', $banner_args);
				}
			}   
        }
	}
	



	/**
	 * Post Status Array
	 * 
	 */
	public static function status($args = array())
	{
		$defaults = array(
			'key' => ''
		);
		$args = wp_parse_args( $args, $defaults );
		
		$array = array(
			'review' => array('value' => 'draft', 'name' => __('Pending Review','adn')),
			'draft' => array('value' => 'draft', 'name' => __('Draft','adn')),
			'active' => array('value' => 'active', 'name' => __('Active','adn')),
			'expired' => array('value' => 'expired', 'name' => __('Expired','adn'))
		);
		
		return !empty($args['key']) ? $array[$args['key']] : $array;
    }
	
}

endif;
?>