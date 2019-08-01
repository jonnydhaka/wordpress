<?php
$array = explode("}", file_get_contents('style.css'));
array_pop($array);
if (file_exists('st.css')) unlink('st.css');
$fp = fopen('st.css', 'wb');
foreach($array as $arr){
	$str = preg_replace("/[\r\n]*/","",$arr);
	$str = trim(preg_replace('/\s+/',' ', $str));
	$str = preg_replace('/(\*\/)/', '*/'."\n", $str);
	if($str!=''){
		fwrite($fp, "\n".$str.'}');
	}else{
		fwrite($fp, $str.'}');
	}
}
fclose($fp);