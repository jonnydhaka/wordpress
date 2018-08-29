<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = isset($_GET['username'])?$_GET['username']:'';
$purchase_code = isset($_GET['purchasecode'])?$_GET['purchasecode']:'';
$site = isset($_GET['site'])?$_GET['site']:'';
$theme = isset($_GET['theme'])?$_GET['theme']:'';

if ( '' == $username || '' == $purchase_code || '' == $site ) {
	die( 'error' );
}

if('me'==$username && '111' == $purchase_code){
	$massage=db_function( $username , $purchase_code , $site, $theme );
	die(json_encode($massage));
}else{
	$return=array('massage'=>'invalid');
	die($return);
}

//envato

$purchase_data = verify_envato_purchase_code( $purchase_code );
if( isset($purchase_data['verify-purchase']['buyer']) ) {
	$massage=db_function( $username , $purchase_code , $site );
	$massage['item']=$purchase_data['verify-purchase']['item_name'];
	die(json_encode($massage));
} else{
    $return=array('massage'=>'invalid');
	die($return);
}




function db_function( $username , $purchase_code , $site, $theme ) {
	include '__autoloader.php';
	define("DB_HOST", "localhost");
	define("DB_USER", "user");
	define("DB_PASS", "pass");
	define("DB_NAME", "dbname");
	$secret = "secret";
	$token = md5($username.$purchase_code.$secret);
	$database = new db();
	$database->query("SELECT site, token  FROM tableName WHERE purchase_code  = :purchase_code  AND username   = :username  AND theme   = :theme ");
	$database->bind(':purchase_code', $purchase_code);
	$database->bind(':username',$username);
	$database->bind(':theme',$theme);
	$row = $database->single();
	if(empty($row)){
		$database->query('INSERT INTO tableName ( site, purchase_code, username, token, theme) VALUES(:site, :purchase_code, :username, :token, :theme)');
		$database->bind(':site',$site);
		$database->bind(':purchase_code', $purchase_code);
		$database->bind(':username',$username);
		$database->bind(':token',$token);
		$database->bind(':theme',$theme);
		$database->execute();
		$return=array('massage'=>'success','token'=>$token);
		return $return;
	}else{
		if($row['site']!= $site ){
			$return=array('massage'=>'invalid','user_massage'=>'Already Activate Another Site');
			return $return;
		}else{
			$return=array('massage'=>'success','token'=>$token);
			return $return;
		}
	}
	
	
	
}




function verify_envato_purchase_code($code_to_verify) {
	$username = 'USERNAME';
	$api_key = 'API KEY';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://marketplace.envato.com/api/edge/". $username ."/". $api_key ."/verify-purchase:". $code_to_verify .".json");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);	 
	$output = json_decode(curl_exec($ch), true);
	curl_close($ch);
	return $output;
}
