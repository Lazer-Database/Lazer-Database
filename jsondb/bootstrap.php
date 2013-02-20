<?php

 define('JSONDB_SECURE', true); //Security constant


 /**
  * Example autoloading function
  * @param string $class
  */
 function __autoload($class)
 {
     $path = strtolower(str_replace('\\', '/', $class).'.php');

     if (file_exists($path))
     {
         require_once $path;
     }
 }

?>
