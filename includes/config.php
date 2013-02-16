<?php

 define('JSONDB', 1);

 function __autoload($class)
 {
     if (file_exists('includes/'.$class.'.class.php'))
     {
         require_once 'includes/'.$class.'.class.php';
     }
 }

?>
