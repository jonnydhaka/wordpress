<?php

class CPT_Envato_Products {

	/**
	 * Construct
	 */
	function __construct() {
		
		add_action( 'init', array( __CLASS__, 'add_post_types' ) );
		add_filter( 'manage_edit-cpt_env_products_columns', array( &$this, 'manage_columns' ) );
		add_filter( 'manage_cpt_env_products_posts_custom_column', array( &$this, 'manage_custom_columns' ), 10, 3 );
	}

	/**
	 * Add post types.
	 */
	public static function add_post_types() {
		self::add( array(
			'plural'       => __( 'Envato Products', 'computer-repair-core' ),
			'singular'     => __( 'Envato Product', 'computer-repair-core' ),
			'menu_name'    => __( 'Envato Product', 'computer-repair-core' ),
			'key'          => 'cpt_env_products',
			//'rewrite_slug' => apply_filters( 'cpt_env_products', 'computer-repair-core' ),
			'supports'     => array( 'title', 'editor' ),

		) );
	}

	/**
	 * Method: Add
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 */
	public static function add( $options ) {
		$defaults = array(
			"plural"              => "",                   // !required
			"singular"            => "",                   // !required
			"key"                 => false,                // !required
			"rewrite_slug"        => false,                // !recommended if has frontend visibility
			"rewrite_with_front"  => false,
			"rewrite_feeds"       => true,
			"rewrite_pages"       => true,
			"menu_icon"           => "dashicons-admin-post",
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'capability_type'     => 'post',
			'menu_name'           => false,
		);

		$options = wp_parse_args( $options, $defaults );

		if ( $options['key'] ) {
			$labels = array(
				'name'               => $options['plural'],
				'singular_name'      => $options['singular'],
				'add_new'            => _x( 'Add New', 'iconic-advanced-layered-nav' ),
				'add_new_item'       => _x( sprintf( 'Add New %s', $options['singular'] ), 'iconic-advanced-layered-nav' ),
				'edit_item'          => _x( sprintf( 'Edit %s', $options['singular'] ), 'iconic-advanced-layered-nav' ),
				'new_item'           => _x( sprintf( 'New %s', $options['singular'] ), 'iconic-advanced-layered-nav' ),
				'view_item'          => _x( sprintf( 'View %s', $options['singular'] ), 'iconic-advanced-layered-nav' ),
				'search_items'       => _x( sprintf( 'Search %s', $options['plural'] ), 'iconic-advanced-layered-nav' ),
				'not_found'          => _x( sprintf( 'No %s found', strtolower( $options['plural'] ) ), 'iconic-advanced-layered-nav' ),
				'not_found_in_trash' => _x( sprintf( 'No %s found in Trash', strtolower( $options['plural'] ) ), 'iconic-advanced-layered-nav' ),
				'parent_item_colon'  => _x( sprintf( 'Parent %s:', $options['singular'] ), 'iconic-advanced-layered-nav' ),
				'menu_name'          => $options['menu_name'] ? $options['menu_name'] : $options['plural'],
			);

			$args = array(
				'labels'              => $labels,
				'hierarchical'        => $options['hierarchical'],
				'supports'            => $options['supports'],
				'public'              => $options['public'],
				'show_ui'             => $options['show_ui'],
				'show_in_menu'        => $options['show_in_menu'],
				'menu_icon'           => $options['menu_icon'],
				'show_in_nav_menus'   => $options['show_in_nav_menus'],
				'publicly_queryable'  => $options['publicly_queryable'],
				'exclude_from_search' => $options['exclude_from_search'],
				'has_archive'         => $options['has_archive'],
				'query_var'           => $options['query_var'],
				'can_export'          => $options['can_export'],
				'capability_type'     => $options['capability_type'],
				'rewrite'             => false,
			);

			if ( $options['rewrite_slug'] ) {
				$args['rewrite'] = array(
					"slug"       => $options['rewrite_slug'],
					"with_front" => $options['rewrite_with_front'],
					"feeds"      => $options['rewrite_feeds'],
					"pages"      => $options['rewrite_pages'],
				);
			}

			register_post_type( $options['key'], $args );
		}
	}

	public function manage_columns( $columns ) {
		$old_columns = $columns;

		$columns = array(
			'cb' => $old_columns['cb'],
			'title' => $old_columns['title'],
			'item_id' => __( 'Item Id', 'service-price-tabs' ),
			'sale' => __( 'Sale', 'service-price-tabs' ),
			'activated' => __( 'Active', 'computer-repair-core' ),
			'inactive' => __( 'InActive', 'computer-repair-core' ),
		
		);

		return $columns;
	}
	public function manage_custom_columns(  $column_name, $id ) {

		$envato_product_id = get_post_meta($id,'cvl_envato_product',true);
		$licence_information = $this->get_item_licence_info($envato_product_id);
		$products = get_option('envato_licence_product_list' );
		$envato_products = array();
		$number_of_sales = 0;
		$i =0;
		if(isset($products->matches)){
			foreach($products->matches as $key => $product){

				if($envato_product_id==$product->id){
						$number_of_sales =  $product->number_of_sales;
						//echo  $product->name;
				}
				
				$i++;
			}
		}
		switch ( $column_name ) {
			case 'item_id' :
				$value = $envato_product_id;
				break;
			case 'activated' :
				$value = isset($licence_information->Active)?$licence_information->Active:'0'; //Active
				break;
			case 'inactive' :
				$value = isset($licence_information->InActive)?$licence_information->InActive:'0'; //InActive
				break;
			case 'sale' :
				$value = $number_of_sales;
				break;
		}
		echo  $value;
	}
	function get_item_licence_info($id){
		global $wpdb;
		$table_name = $wpdb->prefix . "envato_licence_info";

		$sql ='
		SELECT s.`item_id`, COUNT(`id_envato_licence_info`) AS Total ,sActive.Active,iActive.InActive FROM ' . $table_name .' as s
LEFT JOIN (
    SELECT `item_id`, sum(`status`) as Active
    FROM ' . $table_name .' as r
    WHERE r.`status` = 1
    GROUP BY r.`item_id`
) as sActive ON sActive.`item_id` = s.`item_id`
LEFT JOIN (
    SELECT `item_id`, sum(`status`) as InActive
    FROM ' . $table_name .' as i
    WHERE i.`status` = 0
    GROUP BY i.`item_id`
) as iActive ON iActive.`item_id` = s.`item_id`
where s.item_id='.$id.'
GROUP BY s.item_id';

		$data = $wpdb->get_results( $sql );
		if(!empty($data))
			return $data[0];
			return false;

	}

}


$cpt_envato_products = new CPT_Envato_Products();