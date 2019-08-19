<?php

/*
Plugin Name: xmlread
Plugin URI: http://localhost/wordpress
Description: this is the plugin for upload image from xml.
Version: 1.0
Author: example
*/

add_action( 'wp_ajax_uploadXML', 'uploadXML' );
add_action( 'wp_ajax_nopriv_uploadXML', 'uploadXML' );

add_action( 'wp_ajax_uploadImg', 'uploadImg' );
add_action( 'wp_ajax_nopriv_uploadImg', 'uploadImg' );

add_action('admin_enqueue_scripts', 'xml_read_enqueue_script');

add_action( 'admin_menu', 'readxml_menu' );

//upload remote image to directory which is read from xml.
function uploadImg() {
	ini_set('max_execution_time', 300);
	
	$i=0;
	foreach($_POST['data'] as $dataset){
		$title=$dataset['title'];
		unset($dataset['title']);
		foreach($dataset as $key=> $data){
			if($data!=''){

				$content = @file_get_contents($data);
				if($content !== FALSE) {
			        $upload = wp_upload_bits(basename($data) , null, $content);
			        if($upload['error']==''){
			            list($width, $height) = getimagesize($upload['file'] );
				        $image_url_array[$i][$key]['width']=$width;
				        $image_url_array[$i][$key]['height']=$height;
				        $image_url_array[$i][$key]['url']=$upload['url'];
				        $image_url_array[$i]['title']=$title;
			        }
			       }
				
			}
		}
		$i++;
	}
	$responce['print']=$image_url_array;
	echo json_encode($responce);

	exit();
}

//read temporary xml and build dataset for upload.
function uploadXML() {
	$rss = new DOMDocument();
    $rss->load($_FILES['file_xml']['tmp_name'] );
    $feed = array();
    $count=$rss->getElementsByTagName('item')->length ; 
    $responce=array();
    $responce['count']=$count;
    $i=0;
    $array=array();
    $image_url_array=array();
    $print_array=array();
    foreach ($rss->getElementsByTagName('item') as $node) {
    	//print_r($node);
		$ns = 'http://wordpress.org/export/1.2/';
		$title = $node->getElementsByTagName('title')->item(0)->nodeValue;
		$json = $node->getElementsByTagNameNS($ns, 'meta_value')->item(1)->nodeValue;
		$json = $node->getElementsByTagNameNS($ns, 'meta_value')->item(1)->nodeValue;
		if(isset($node->getElementsByTagNameNS($ns, 'attachment_url')->item(0)->nodeValue)){
			$imageurl=$node->getElementsByTagNameNS($ns, 'attachment_url')->item(0)->nodeValue;
		}else{
			$imageurl='';
		}
		$imageurlPart=substr($imageurl,0,strrpos($imageurl,'/'));
		$another_image=array();
		$another_image = @unserialize($json);
		$array[]=array('text'=>$title,'id'=>'m'.$i,"parent"=>"#" );
		
		
		$image_url_array[$i]['main']=$imageurl;
			$image_url_array[$i]['title']=$title;
		
		if($imageurl!=''){
			
			$print_array[$i]['title']=$title;
			$print_array[$i]['main']['url']=$imageurl;
			$print_array[$i]['main']['width']=$another_image['width'];
			$print_array[$i]['main']['height']=$another_image['height'];
	    }

		if($imageurl!=''){
			$array[]=array('text'=>'main','id'=>'main'.$i,"parent"=>'m'.$i );
			$array[]=array('text'=>'url','id'=>'mainurl'.$i,"parent"=>'main'.$i );
			$array[]=array('text'=>$imageurl,'id'=>'mainurllink'.$i,"parent"=>'mainurl'.$i );
		}
		if(!empty($another_image)){
			$array[]=array('text'=>'width','id'=>'mainw'.$i,"parent"=>'main'.$i);
			$array[]=array('text'=>$another_image['width'],'id'=>'mainwval'.$i,"parent"=>'mainw'.$i );
			$array[]=array('text'=>'height','id'=>'mainh'.$i,"parent"=>'main'.$i);
			$array[]=array('text'=>$another_image['height'],'id'=>'mainhval'.$i,"parent"=>'mainh'.$i );
		}
		if(!empty($another_image)){
			foreach ($another_image['sizes'] as $key => $value) {
				if(is_array($value)){
					$image_url_array[$i][$key]=$imageurlPart.'/'.$value['file'];

					$print_array[$i][$key]['url']=$imageurlPart.'/'.$value['file'];
					$print_array[$i][$key]['width']=$value['width'];
					$print_array[$i][$key]['height']=$value['height'];



					$array[]=array('text'=>$key,'id'=>$key.$i,"parent"=>'m'.$i );
					$array[]=array('text'=>'url','id'=>'url'.$key.$i,"parent"=>$key.$i );
					$array[]=array('text'=>$imageurlPart.'/'.$value['file'],'id'=>'mainurl'.$key.$i,"parent"=>'url'.$key.$i );

					$array[]=array('text'=>'width','id'=>$key.'w'.$i,"parent"=>$key.$i);
					$array[]=array('text'=>$value['width'],'id'=>$key.'wval'.$i,"parent"=>$key.'w'.$i);

					$array[]=array('text'=>'height','id'=>$key.'h'.$i,"parent"=>$key.$i);
					$array[]=array('text'=>$value['height'],'id'=>$key.'hval'.$i,"parent"=>$key.'h'.$i);
				}
			}
		}
		$i++;
	}
	$responce['data']=$array;
	$responce['uploads']=$image_url_array;
	$responce['print']=$print_array;
	echo json_encode($responce);
	exit();
}

// enqueue file 
function xml_read_enqueue_script(){
	wp_register_script( 'customjs', plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery','jstree'), false, true );
	wp_enqueue_script( 'customjs' );
	wp_localize_script( 'customjs', 'ajax_object', array( 'ajaxurl' =>   admin_url( 'admin-ajax.php' ) ) );

	wp_register_script( 'jstree', 'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js', array('jquery'), false, true );
	wp_enqueue_script( 'jstree' );

	wp_register_style( 'style.min', 'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css' );
	wp_enqueue_style( 'style.min' );
	wp_register_style( 'mystyle', plugin_dir_url( __FILE__ ) . 'css/mystyle.css' );
	wp_enqueue_style( 'mystyle' );
}

//add menu in admin
function readxml_menu() {
	add_menu_page( 'Read XML', 'Readxml', 'manage_options', 'readxml', 'readxml_menu_option' );
}

//admin menu page layout function
function readxml_menu_option() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have permissions to access this page.' ) );
	}
	echo 'Upload Your XML File.' ;
	echo '<div class="wrap">';
	echo '<form method="post" enctype="multipart/form-data" id="xml_upload">';
	echo '<label id="xml_upload_file_label" for="xml_upload_file">Upload XML</label>';
	echo '<input type="file" value="Import XML" name="file_xml" id="xml_upload_file">';
	echo '<input type="hidden" value="upload" name="submit" class="submit_btn">';
	echo wp_nonce_field( plugin_basename( __FILE__ ), 'xml_uploader' );
	echo '</form>';
	echo '</div>';
	echo '<div class="meter-text"></div>';
	echo '<div class="meter">
            <span style="width: 0%;"></span>
            <div class="showper"></div>
          </div>
          <div id="output"></div>';
}

?>