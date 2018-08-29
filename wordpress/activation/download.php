<?php
$username      = isset( $_GET['username'] ) ? $_GET['username'] : '';
$purchase_code = isset( $_GET['purchasecode'] ) ? $_GET['purchasecode'] : '';
$token         = isset( $_GET['token'] ) ? $_GET['token'] : '';
$file          = isset( $_GET['filename'] ) ? $_GET['filename'] : '';
$site 			= isset($_GET['site'])?$_GET['site']:'';
$theme 			= isset($_GET['theme'])?$_GET['theme']:'';
if ( '' == $username || '' == $purchase_code || '' == $token || '' == $file || '' == $site || '' == $theme) {
	die( 'error' );
}
include '__autoloader.php';
define("DB_HOST", "localhost");
define("DB_USER", "user");
define("DB_PASS", "pass");
define("DB_NAME", "dbname");
$secret = "secret";
$_token = md5( $username.$purchase_code.$secret );
$database = new db();
$database->query("SELECT site, token  FROM tableName WHERE purchase_code  = :purchase_code  AND username   = :username  AND theme   = :theme ");
$database->bind(':purchase_code', $purchase_code);
$database->bind(':username',$username);
$database->bind(':theme',$theme);
$row = $database->single();
if(empty($row)){
	die( 'error' );
}
if($row['site']!= $site ){
	die( 'error' );
}
$file = $file . ".zip";

if ( $_token == $token && $_token == $row['token'] ) {
	header( 'Content-Description: File Transfer' );
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Disposition: attachment; filename="' . basename( $file ) . '"' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate' );
	header( 'Pragma: public' );
	header( 'Content-Length: ' . filesize( $file ) );
	readfile( $file );
} else {
	die( 'error' );
}