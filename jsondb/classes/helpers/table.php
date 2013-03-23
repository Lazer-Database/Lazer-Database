<?php

 namespace jsondb\classes\helpers;

use jsondb\classes\JDBException as JDBException;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * File managing class of JSONDB project
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class Table {

     /**
      * Returning path to file
      * @param string $name
      * @param string $type
      * @return string Path to file
      */
     private static function getPath($name, $type = 'data')
     {
         return JSONDB_DATA_PATH.$name.'.'.$type.'.json';
     }

     /**
      * Getting decoded JSON
      * @param string $name
      * @param string $type
      * @return object|array Json_decode()
      */
     public static function get($name, $type = 'data', $assoc=false)
     {
         return json_decode(file_get_contents(self::getPath($name, $type)), $assoc);
     }

     /**
      * Saving encoded JSON to file
      * @param string $name
      * @param object $data
      * @param string $type
      * @return boolean
      */
     public static function put($name, $data, $type = 'data')
     {
         return file_put_contents(self::getPath($name, $type), json_encode($data));
     }

     /**
      * Checking that file exists
      * @param string $name
      * @param string $type
      * @return boolean
      */
     public static function exists($name, $type = 'data')
     {
         return file_exists(self::getPath($name, $type));
     }

     /**
      * Removing files with Table and Config
      * @param string $name
      * @return boolean
      * @throws JDBException If file doesn't exists or there's problems with deleting files
      */
     public static function remove($name)
     {
         if (self::exists($name))
         {
             $table = unlink(self::getPath($name));
             $config = unlink(self::getPath($name, 'config'));

             if ($table && $config)
                 return TRUE;

             throw new JDBException('Table deleting failed');
         }

         throw new JDBException('Table does not exists');
     }

 }

?>
