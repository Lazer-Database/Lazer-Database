<?php

 namespace jsondb\classes\helpers;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Proxy pattern for Table helper {@uses Table}
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class Config {

     /**
      * Getting decoded JSON
      * @param string $name
      * @param string $type
      * @return object Json_decode()
      */
     public static function get($name)
     {
         return Table::get($name, 'config');
     }

     /**
      * Saving encoded JSON to file
      * @param string $name
      * @param object $data
      * @param string $type
      * @return boolean
      */
     public static function put($name, $data)
     {
         return Table::put($name, $data, 'config');
     }

     /**
      * Checking that config file exists
      * @param string $name
      * @param string $type
      * @return boolean
      */
     public static function exists($name)
     {
         return Table::exists($name, 'config');
     }

     /**
      * Return array with names of fields
      * @return array
      */
     public static function fields($name)
     {
         return self::get($name)->fields;
     }
     
     
     /**
      * Returning assoc array with types of fields
      * @return array
      */
     public static function fields_type($name)
     {
         $config = self::get($name);
         return array_combine($config->fields, $config->types);
     }
     
     /**
      * Returning last ID from table
      * @param string $name
      * @return integer
      */
     public static function last_id($name)
     {
         return self::get($name)->last_id;
     }
  }

?>
