<?php
$to ='test@gmail.com';
$subject = 'test';
$message = 'Thia is test mail';
$headers[] = "Content-Type: text/html; charset=UTF-8";
$headers[] = 'From '.get_bloginfo('name').' \r\n' . 'Reply-To: ' . $email;
$send = wp_mail($to, $subject, $message, $headers);
if (!$send) {
	global $ts_mail_errors;
	global $phpmailer;
 
	if (!isset($ts_mail_errors)) 
	$ts_mail_errors = array();
 
	if (isset($phpmailer)) {
		$ts_mail_errors[] = $phpmailer->ErrorInfo;
	}
	//print_r($ts_mail_errors);
	echo json_encode(array("result" => $send));
	exit;
}else{
	echo json_encode(array("result" => "success"));
	exit;
}


add_action( 'phpmailer_init', 'WCMphpmailerException' );
function WCMphpmailerException( $phpmailer )
{
	if ( ! defined( 'WP_DEBUG' ) OR ! WP_DEBUG )
	{
		$phpmailer->SMTPDebug = 0;
		$phpmailer->debug = 0;
		return;
	}
	if ( ! current_user_can( 'manage_options' ) )
		return;
	// Enable SMTP
	# $phpmailer->IsSMTP();
	$phpmailer->SMTPDebug = 2;
	$phpmailer->debug     = 1;
	$data = apply_filters(
		'wp_mail',
		compact( 'to', 'subject', 'message', 'headers', 'attachments' )
	);
	// Show what we got
	current_user_can( 'manage_options' )
		AND print htmlspecialchars( var_export( $phpmailer, true ) );
	$error = null;
	try
	{
		$sent = $phpmailer->Send();
		! $sent AND $error = new WP_Error( 'phpmailer-error', $sent->ErrorInfo );
	}
	catch ( phpmailerException $e )
	{
		$error = new WP_Error( 'phpmailer-exception', $e->errorMessage() );
	}
	catch ( Exception $e )
	{
		$error = new WP_Error( 'phpmailer-exception-unknown', $e->getMessage() );
	}
	if ( is_wp_error( $error ) )
		return printf(
			"%s: %s",
			$error->get_error_code(),
			$error->get_error_message()
		);
}


define( 'SMTP_USER',   'admin@smartdata.tonytemplates.com' );    // Username to use for SMTP authentication
define( 'SMTP_PASS',   'eM6NZC2tn816' );       // Password to use for SMTP authentication
define( 'SMTP_HOST',   'mail.smartdata.tonytemplates.com' );    // The hostname of the mail server
define( 'SMTP_FROM',   'admin@smartdata.tonytemplates.com' ); // SMTP From email address
define( 'SMTP_NAME',   'Smartdatasoft' );    // SMTP From name
define( 'SMTP_PORT',   '587' );                  // SMTP port number - likely to be 25, 465 or 587
define( 'SMTP_SECURE', 'ssl' );                 // Encryption system to use - ssl or tls
define( 'SMTP_AUTH',    true );                 // Use SMTP authentication (true|false)
define( 'SMTP_DEBUG',   2 );   


add_action( 'phpmailer_init', 'send_smtp_email' );
function send_smtp_email( $phpmailer ) {
	
	 $phpmailer->isSMTP();     
    $phpmailer->Host = SMTP_HOST;
    $phpmailer->SMTPAuth = SMTP_AUTH; // Force it to use Username and Password to authenticate
    $phpmailer->Port = SMTP_PORT;
    $phpmailer->Username = SMTP_USER;
    $phpmailer->Password = SMTP_PASS;
	$phpmailer->SMTPSecure = SMTP_SECURE;
	$phpmailer->From       = SMTP_FROM;
	$phpmailer->FromName   = SMTP_NAME;
	$phpmailer->SMTPDEbug = SMTP_DEBUG;
}
