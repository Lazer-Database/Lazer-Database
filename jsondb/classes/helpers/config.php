<?php

 namespace jsondb\classes\helpers;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Config managing class adapter of File class
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class Config {

     /**
      * File class
      * @var object
      */
     private static $file;

     /**
      * Setting name of table
      * @param string $name
      * @return \jsondb\classes\helpers\File
      */
     public static function name($name)
     {

         self::$file = new File($name, 'config');

         return new Config();
     }

     /**
      * Calls File methods
      * @param string $name
      * @param mixed $arguments
      */
     public function __call($name, $arguments)
     {
         $param = (isset($arguments[0])) ? $arguments[0] : null;

         return call_user_func(array(self::$file, $name), $param);
     }

     /**
      * Getting decoded JSON
      * @param string $name
      * @param string $type
      * @return object Json_decode()
      */
     public function get($field = null, $assoc = false)
     {
         $return = self::$file->get($assoc);

         if ($field !== null)
             return $assoc ? $return[$field] : $return->{$field};

         return $return;
     }

     /**
      * Return array with names of fields
      * @return array
      */
     public function fields()
     {
         return array_keys($this->get('schema', true));
     }

     /**
      * Return relations configure
      * @return array|object
      */
     public function relations($table = null, $assoc = false)
     {
         if ($table !== null)
         {
             return $this->get('relations', $assoc)->{$table};
         }

         return $this->get('relations', $assoc);
     }

     /**
      * Returning assoc array with types of fields
      * @return array
      */
     public function schema()
     {
         return $this->get('schema', true);
     }

     /**
      * Returning last ID from table
      * @param string $name
      * @return integer
      */
     public function last_id()
     {
         return $this->get('last_id');
     }

 }

?>
