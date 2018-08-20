<?php
ob_start();
	
date_default_timezone_set('Europe/London');


define('DOCROOT', dirname(__FILE__));

require_once('dbinfo.php');

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
