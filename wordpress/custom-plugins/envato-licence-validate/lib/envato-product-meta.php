<?php

class EnvatoProductLicenceMeta{

	public function __construct() {
	 
		
	 
		/* Fire our meta box setup function on the post editor screen. */
		add_action( 'load-post.php', array( $this,'omb_add_metabox') );
		add_action( 'load-post-new.php', array( $this,'omb_add_metabox' ));

		add_action( 'save_post', array( $this, 'omb_save_metabox' ) );
		add_action( 'save_post', array( $this, 'omb_save_changelog_metabox' ) );
		add_action( 'save_post', array( $this, 'omb_save_metabox_products' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'omb_admin_assets' ) );

		//change upload folder location

		//add_filter('wp_handle_upload_prefilter', array( $this, 'handle_upload_prefilter') );
		//add_filter('wp_handle_upload', array( $this, 'handle_upload') );

		add_filter('wp_ajax_cvl_envato_add_uploaddir', array( $this, 'cvl_envato_add_uploaddir_func') );
		add_filter('wp_ajax_nopriv_cvl_envato_add_uploaddir', array( $this, 'cvl_envato_add_uploaddir_func') );
		add_filter('wp_ajax_cvl_envato_remove_uploaddir', array( $this, 'cvl_envato_remove_uploaddir_func') );
		add_filter('wp_ajax_nopriv_cvl_envato_remove_uploaddir', array( $this, 'cvl_envato_remove_uploaddir_func') );
	}
	function cvl_envato_add_uploaddir_func() {
		update_option('upload_path',WP_CONTENT_DIR.'/uploads/envato-products');
		update_option('upload_url_path','../wp-content/uploads/envato-products');
		update_option('uploads_use_yearmonth_folders', false);
		die();
	}
	function cvl_envato_remove_uploaddir_func( $fileinfo )   {
		update_option('upload_path',null);
		update_option('upload_url_path',null);
		update_option('uploads_use_yearmonth_folders', true);
		die();
	}
	function omb_admin_assets() {
		wp_enqueue_style( 'omb-admin-style', plugin_dir_url( __FILE__ ) . "../assets/admin/css/style.css", null, time() );
		wp_enqueue_script( 'omb-admin-js', plugin_dir_url( __FILE__ ) . "../assets/admin/js/jquery.repeater.js", array('jquery',), '1.0', true );
		wp_enqueue_script( 'omb-admin-repeater-custom-js', plugin_dir_url( __FILE__ ) . "../assets/admin/js/custom.js", array('omb-admin-js',), time(), true );
		wp_localize_script( 'omb-admin-repeater-custom-js', 'omb_admin_repeater_ajax_object', array('ajax_url' => admin_url( 'admin-ajax.php' )) );
	}

	function omb_save_changelog_metabox( $post_id){


			if ( ! $this->is_secured( 'evl_product_info_field', 'evl_product_info', $post_id ) ) {
				return $post_id;
			}

			$item_zips = $_POST['enavato_download'];

			update_post_meta( $post_id, 'cvl_envato_product_zip', $item_zips );
	}
	function omb_save_metabox_products( $post_id){
		
		if ( ! $this->is_secured( 'evl_product_info_field', 'evl_product_info', $post_id ) ) {
			return $post_id;
		}

		$content_logs = $_POST['cvl_product_changelog'];

		update_post_meta( $post_id, 'cvl_product_changelog', $content_logs );


	}
	function omb_save_metabox( $post_id ) {

		if ( ! $this->is_secured( 'evl_product_info_field', 'evl_product_info', $post_id ) ) {
			return $post_id;
		}

		$envato_product    = isset( $_POST['cvl_envato_product'] ) ? $_POST['cvl_envato_product'] : '';

		update_post_meta( $post_id, 'cvl_envato_product', $envato_product );
	}

	private function is_secured( $nonce_field, $action, $post_id ) {
		$nonce = isset( $_POST[ $nonce_field ] ) ? $_POST[ $nonce_field ] : '';

		if ( $nonce == '' ) {
			return false;
		}
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		if ( wp_is_post_autosave( $post_id ) ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		return true;

	}
	function omb_add_metabox() {
		add_meta_box(
			'omb_post_location',
			__( 'Product Info', 'our-metabox' ),
			array( $this, 'omb_display_metabox' ),
			array( 'cpt_env_products' ),
			'side'
		);

		add_meta_box(
			'omb_post_download',
			__( 'Download Zip Manage', 'our-metabox' ),
			array( $this, 'omb_display_download_metabox' ),
			array( 'cpt_env_products' )
		
		);
		add_meta_box(
			'omb_post_changelog',
			__( 'Change Log', 'our-metabox' ),
			array( $this, 'omb_display_changelog_metabox' ),
			array( 'cpt_env_products' )
		);
	}
	

	function omb_display_metabox( $post ) {

		$products = get_option('envato_licence_product_list' );
		
		if(!empty($products)){
			
		

	//print_r( $products->matches);
		$envato_products = array();
		$i =0;
		foreach($products->matches as $key => $product){
			$envato_products[$i]['id'] =   $product->id;
			$envato_products[$i]['name'] =  $product->name;
			$i++;
		}
		
		$saved_color = get_post_meta( $post->ID, 'omb_color', true );
		$label4 = __( 'Envato Product', 'our-metabox' );
		$metabox_html ='';
		// die();
		wp_nonce_field( 'evl_product_info', 'evl_product_info_field' );
		$envato_product = get_post_meta($post->ID,'cvl_envato_product',true);
		echo "Product Id : " . 	$envato_product ;
		$dropdown_html = "<option value='0'>".__('Select a Product','our-metabox')."</option>";
		foreach($envato_products as $product => $val){
			$selected ='';
			if($val['id'] == $envato_product){
				$selected = 'selected';
			}
			$dropdown_html .= sprintf("<option %s value='%s'>%s</option>",$selected, $val['id'], ucwords($val['name']));
		}
		$metabox_html .= <<<EOD
<p>
<label for="cvl_envato_product">{$label4}: </label>
<select name="cvl_envato_product" id="cvl_envato_product">
{$dropdown_html}
</select>
</p>
EOD;
		echo $metabox_html;
		}else{
			echo __("You Have No Product",'our-metabox');
		}
	}

	function omb_display_changelog_metabox($post){
		$post_id = $post->ID;
		$content_logs = get_post_meta($post->ID,'cvl_product_changelog',true);
		$metabox_html = <<<EOD
		<div class="panel envato_product_options_panel">
			<div id="change_log" class="form-field downloadable_files">
			<textarea name="cvl_product_changelog" class="widefat" style="width:100% !important; height:300px !important;" > {$content_logs}</textarea>
			</div>
		</div>
EOD;
		echo  $metabox_html;
	}

	function omb_display_download_metabox($post){

		$post_id = $post->ID;
		$item_zips = get_post_meta($post_id,'cvl_envato_product_zip',true);
		$metabox_html = <<<EOD
		<div class="panel envato_product_options_panel">
		<div id="downloadable_files" class="form-field downloadable_files">
			<div class="evl_repetable_header">
				<label>	Download file ID: </label>
				<span class="evl_file_id">1</span>
			</div>
		<div class="repeater-default">
			<div data-repeater-list="enavato_download">

EOD;

if(is_array($item_zips)){
foreach($item_zips as $key=>$f){
	
	$ev_file_dir=$ev_file_names=$ev_file_pname=$ev_file_version=$ev_changelog=$ev_details=$ev_file_hashes=$ev_file_urls='';		
	if(isset($f['_ev_file_dir']))
		$ev_file_dir=$f['_ev_file_dir'];
	if(isset($f['_ev_file_names']))
		$ev_file_names=$f['_ev_file_names'];
	if(isset($f['_ev_file_pname']))
		$ev_file_pname=$f['_ev_file_pname'];
	if(isset($f['_ev_file_version']))
		$ev_file_version=$f['_ev_file_version'];
	if(isset($f['_ev_changelog']))
		$ev_changelog=$f['_ev_changelog'];
	if(isset($f['_ev_details']))
		$ev_details=$f['_ev_details'];
	if(isset($f['_ev_file_hashes']))
		$ev_file_hashes=$f['_ev_file_hashes'];
	if(isset($f['_ev_file_urls']))
		$ev_file_urls=$f['_ev_file_urls'];
$metabox_html .= <<<EOD
			<div data-repeater-item="">
					<div class="wrap_downloadable_files_wrap">
						<div class="item_downloadable_files_wrap">
							<div class="evl_file_name">
							<input type="text" class="input_text" placeholder="Plugin Directory" name="_ev_file_dir" value="{$ev_file_dir}">
							<input type="text" class="input_text" placeholder="Plugin File Name" name="_ev_file_names" value="{$ev_file_names}">
							<input type="text" class="input_text" placeholder="Plugin Name" name="_ev_file_pname" value="{$ev_file_pname}">
							<input type="text" class="input_text" placeholder="Plugin Version" name="_ev_file_version" value="{$ev_file_version}">
							<textarea class="input_text" name="_ev_changelog" placeholder="Change Log" >{$ev_changelog}</textarea>
							<textarea class="input_text" name="_ev_details" placeholder="Details" >{$ev_details}</textarea>
							<input class="ev_file_hashes_input" type="hidden" name="_ev_file_hashes" value="{$ev_file_hashes}">
							</div>
							<div class="evl_url_name">
							<input type="text" class="envato-product-url-id" placeholder="http://" name="_ev_file_urls" value="{$ev_file_urls}">
							</div>
							<div class="evl_file_url_choose">
							<a href="#" class="button upload_file_button" data-choose="Choose file" data-update="Insert file URL">Choose file</a>
							</div>
							<div class="evl_action">
							<span data-repeater-delete="" class="submitdelete deletion">
								<span class="glyphicon glyphicon-remove"></span>
								Delete
								</span>
							</div>
						</div>
					</div>
			</div>
EOD;
}
}else{
	$metabox_html .= <<<EOD
	<div data-repeater-item="">
					<div class="wrap_downloadable_files_wrap">
						<div class="item_downloadable_files_wrap">
							<div class="evl_file_name">
							<input type="text" class="input_text" placeholder="Plugin Directory" name="_ev_file_dir" value="">
							<input type="text" class="input_text" placeholder="Plugin File Name" name="_ev_file_names" value="">
							<input type="text" class="input_text" placeholder="Plugin Name" name="_ev_file_pname" value="">
							<input type="text" class="input_text" placeholder="Plugin Version" name="_ev_file_version" value="">
							<textarea class="input_text" name="_ev_changelog" placeholder="Change Log"></textarea>
							<textarea class="input_text" name="_ev_details" placeholder="Details"></textarea>
							<input class="ev_file_hashes_input" type="hidden" name="_ev_file_hashes" value="">
							</div>
							<div class="evl_url_name">
							<input type="text" class="envato-product-url-id" placeholder="http://" name="_ev_file_urls" value="">
							</div>
							<div class="evl_file_url_choose">
							<a href="#" class="button upload_file_button" data-choose="Choose file" data-update="Insert file URL">Choose file</a>
							</div>
							<div class="evl_action">
					
							<span data-repeater-delete="" class="submitdelete deletion">
								<span class="glyphicon glyphicon-remove"></span>
								Delete
								</span>
							</div>
						</div>
					</div>
	</div>
EOD;
}
$metabox_html .=<<<EOD
			</div>
			<div class="add-repeatable-row">
			<div class="submit" style="float: none; clear:both;">
				<span data-repeater-create="" class="btn btn-info btn-md button-secondary evl_add_repeatable">
					<span class="glyphicon glyphicon-plus"></span>
					Add New File
				</span>
			</div>
		</div>
EOD;

$metabox_html .=<<<EOD
		</div>


		</div>
</div>
EOD;
echo  $metabox_html;
	}

	

}

new EnvatoProductLicenceMeta();
?>