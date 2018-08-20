<?php
ob_start();
	
date_default_timezone_set('Europe/London');

define('DIR','http://file-vault.com/');
define('DOCROOT', dirname(__FILE__));

define('DB_TYPE','mysql');
define('DB_HOST','localhost');
define('DB_NAME','users');
define('DB_USER','phpmyadmin');
define('DB_PASS','vanjavk321');
define('PREFIX','smvc_');

define('SESSION_PREFIX','smvc_');

define('SITETITLE','Simple MVC Framework');

function autoloadsystem($class) 
{

   $filename = DOCROOT . "/core/" . strtolower($class) . ".php";
   if(file_exists($filename)){
      require $filename;
   }

   $filename = DOCROOT . "/helpers/" . strtolower($class) . ".php";
   if(file_exists($filename)){
      require $filename;
   } 
 
}
spl_autoload_register("autoloadsystem");

$app = new Bootstrap();
$app->setController('members');
$app->setTemplate('default');
$app->init();

ob_flush();

?>
