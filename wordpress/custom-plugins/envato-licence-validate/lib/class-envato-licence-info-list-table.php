<?php
// ref:: 
//ref:: http://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
//http://codingbin.com/display-custom-table-data-wordpress-admin/
//http://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
//https://webkul.com/blog/create-admin-tables-using-wp_list_table-class/

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * Create a new table class that will extend the WP_List_Table
 */
class Envato_Licence_Info_List_Table extends WP_List_Table
{


    
 
   /**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */
 
    function __construct() {
 
        parent::__construct( array(
  
             'singular'  => 'purchase_code',     //singular name of the listed records
  
             'plural'    => 'purchase_codes',    //plural name of the listed records
  
             'ajax'      => false 
  
       ) );
  
     }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {

     $this->process_bulk_action();
        $this->_column_headers = $this->get_column_info();
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        
        $per_page              = 10;
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page -1 ) * $per_page;
        $this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '2';

        // only ncessary because we have sample data
        $args = array(
            'offset' => $offset,
            'number' => $per_page,
        );

        if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
            $args['orderby'] = $_REQUEST['orderby'];
            $args['order']   = $_REQUEST['order'] ;
        }

        $this->items  = $this->table_data( $args );
		
		 $argsall = array(
            'all' => 'all'
        );
        $count_data =  count($this->table_data( $argsall));
		
    
        $this->set_pagination_args( array(
            'total_items' => $count_data,
            'per_page'    => $per_page
        ) );
       
        $this->_column_headers = array($columns, $hidden, $sortable);
             	/** Process bulk action */
	
		
    }


    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            //'id_envato_licence_info'          => 'ID',
            'cb'       => '<input type="checkbox" />',
            'purchase_key'       => 'Purchase Code',
            'domain_name' => 'Domain',
            'status'    =>'status',
            'buyer'=>'buyer',
            'licence_type'=>'licence_type',
            'item_id'    => 'Item Id',
            'item_name'    => 'Item Name',
            'created'        => 'Created',
           //'rating'      => 'Rating'
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'purchase_key' => array('purchase_key',true) ,
            'item_id' => array('item_id',false) ,
            );
            return $sortable_columns;
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data( $args = array() )
    {
        $data = array();

        global $wpdb;

        $table_name = $wpdb->prefix . "envato_licence_info";

        
        $defaults = array(
            'number'     => 20,
            'offset'     => 0,
            'orderby'    => 'id_envato_licence_info',
            'order'      => 'DESC',
        );
    
        $args      = wp_parse_args( $args, $defaults );

            // check if a search was performed.
	    $search = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
	
        /* If the value is not NULL, do a search for it. */
        if( $search != NULL ){
            
            $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;

            $do_search = ( $search ) ? $wpdb->prepare(" AND `purchase_key` LIKE '%%%s%%'  OR `domain_name` LIKE '%%%s%%'",  $search, $search ) : ''; 
            /* Notice how you can search multiple columns for your search term easily, and return one data set */
             $data = $wpdb->get_results( 'SELECT * FROM ' . $table_name .'  WHERE 1=1 ' . $do_search . ' ORDER BY ' . $args['orderby'] .' ' . $args['order'] .' LIMIT ' . $args['offset'] . ', ' . $args['number'] );

            }elseif(isset($args['all']) && $args['all']=='all'){
            	$data = $wpdb->get_results( 'SELECT * FROM ' . $table_name .' ORDER BY ' . $args['orderby'] .' ' . $args['order']  );
            }else{
				 $data = $wpdb->get_results( 'SELECT * FROM ' . $table_name .' ORDER BY ' . $args['orderby'] .' ' . $args['order'] .' LIMIT ' . $args['offset'] . ', ' . $args['number'] );
				 
			}
         return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {

        switch ( $column_name ) {
            
            case 'item_id':
                return $item->item_id;
            case 'status':
                return $item->status;
            case 'buyer':
                return $item->buyer;   
            case 'licence_type':
                return $item->licence_type;       
            case 'domain_name':
                return $item->domain_name;
            case 'created':
                return $item->created;

            default:
            // return isset( $item->$column_name ) ? $item->$column_name : '';
            return print_r( $item, true ) ;
        }
    }

      
    function column_licence_type($item) {

        $licence_type_name = Envato_Licence_Valicator::licenceTypeName($item->license_type);

        $value =  $licence_type_name ;

        return $value;
        }

        function column_item_name($item) {
            $productArray=get_option('envato_licence_product_list');
            foreach($productArray->matches as $val){
                if($val->id==$item->item_id)
                return $val->name;
            }
            return '';
        }
            
    function column_domain_name($item) {

        

        $value = '<a target="_blank" href="'. $item->domain_name .'">'. $item->domain_name .'</a>' ;

        return $value;
        }
                     
   function column_status($item) {

    $color = "#28a745";
    $label = "Active";
   
    if($item->status==0){
        $color = $active_color = "#ffc107";
        $label = "In-active";
    }
       
    
    $value = '<span style="display: inline-block;padding: 6px 12px; margin-bottom: 0;
    font-size: 14px;color: #fff;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;background-color: ' . $color.'">' .$label . "</span>";
    return $value;
 }


     /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
 
   function column_purchase_key($item) {

    
    $licence_type_name = Envato_Licence_Valicator::licenceTypeName($item->license_type);
    $value =  $item->purchase_key;
    $actions ='';
    return $value;
 }

 function search_box( $text, $input_id ) { ?>
    <p class="search-box">
      <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
      <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
      <?php submit_button( $text, 'button', false, false, array('id' => 'search-submit') ); ?>
  </p>
<?php }

/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />', $item->id_envato_licence_info
		);
	}

    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {

    // security check!
    if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

        $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
        $action = 'bulk-' . $this->_args['plural'];
        if ( ! wp_verify_nonce( $nonce, $action ) )
            wp_die( 'Nope! Security check failed!' );

    
    $action = $this->current_action();

    switch ( $action ) {

        case 'delete':
        
            global $wpdb;
            $table_name = $wpdb->prefix . 'envato_licence_info';
            if ('delete' === $this->current_action()) {
                $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
                if (is_array($ids)) $ids = implode(',', $ids);
                if (!empty($ids)) {
                    $wpdb->query("DELETE FROM $table_name WHERE id_envato_licence_info IN($ids)");
                }
            }

        //wp_die( 'You have deleted this succesfully' ); 
        wp_redirect( esc_url_raw( add_query_arg() ) );
		exit;
		
        break;

        case 'edit':
            wp_die( 'This is the edit page.' );
            break;

        default:
            // do nothing or something else
            return;
            break;
    }
	}

    return;
    }
 
}
 
