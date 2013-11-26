<?php

 namespace JSONDb\Classes\Helpers;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Config managing class
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class Config extends File {

     public static function name($name)
     {
         $file = new Config;
         $file->_name = $name;
         $file->setType('config');

         return $file;
     }

     /**
      * Get key from returned config
      * @param type $field key
      * @param type $assoc
      * @return mixed
      */
     public function getKey($field, $assoc = false)
     {
         return $assoc ? $this->get($assoc)[$field] : $this->get($assoc)->{$field};
     }

     /**
      * Return array with names of fields
      * @return array
      */
     public function fields()
     {
         return array_keys($this->getKey('schema', true));
     }

     /**
      * Return relations configure
      * @return array|object
      */
     public function relations($table = null, $assoc = false)
     {
         if ($table !== null)
         {
             return $this->getKey('relations', $assoc)->{$table};
         }

         return $this->getKey('relations', $assoc);
     }

     /**
      * Returning assoc array with types of fields
      * @return array
      */
     public function schema()
     {
         return $this->getKey('schema', true);
     }

     /**
      * Returning last ID from table
      * @param string $name
      * @return integer
      */
     public function last_id()
     {
         return $this->getKey('last_id');
     }

 }
 