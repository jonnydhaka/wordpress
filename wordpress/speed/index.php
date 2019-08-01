1. Leverage Browser Caching on Server

## EXPIRES HEADER CACHING ##
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType image/jpg "access 1 month"
ExpiresByType image/jpeg "access 1 month"
ExpiresByType image/gif "access 1 month"
ExpiresByType image/png "access 1 month"
ExpiresByType image/svg "access 1 month"
ExpiresByType text/css "access 1 month"
ExpiresByType application/pdf "access 1 month"
ExpiresByType application/javascript "access 1 month"
ExpiresByType application/x-javascript "access 1 month"
ExpiresByType application/x-shockwave-flash "access 1 month"
ExpiresByType image/x-icon "access 1 month"
ExpiresDefault "access 2 days"
</IfModule>
## EXPIRES HEADER CACHING ##

# 1 Month for most static assets
<filesMatch ".(css|jpg|jpeg|png|gif|js|ico)$">
Header set Cache-Control "max-age=2592000, public"
</filesMatch>


2. Remove Query Strings From Static Resources With Code

You can also remove query strings from your assets with code. Simply add the following to your WordPress theme’s functions.php file.
Important! Editing the source code of a WordPress theme could break your site if not done correctly. If you are not comfortable doing this, please check with a developer first.

function _remove_script_version( $src ){ 
$parts = explode( '?', $src ); 	
return $parts[0]; 
} 
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 ); 
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );