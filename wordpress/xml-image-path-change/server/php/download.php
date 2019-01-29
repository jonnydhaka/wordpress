<?php
$path='files/'.$_POST['file'];
$xml = file_get_contents($path);
unlink($path);
$myXmlString = str_replace($_POST['old'], $_POST['new'], $xml);
file_put_contents($path, $myXmlString);
echo $_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "/")).'/'.$path;