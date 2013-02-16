<?php

 defined('JSONDB') or die('Permission denied!');

 /**
  * File managing class of JSONDB project
  *
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  */
 class File {
     
     public static function get($name, $type='data')
     {
         return file_get_contents(JSONDB_DATA_PATH.$name.'.'.$type.'.json');
     }
     
     public static function put($name, $data, $type='data')
     {
         return file_put_contents(JSONDB_DATA_PATH.$name.'.'.$type.'.json', $data);
     }
 
 }

?>
