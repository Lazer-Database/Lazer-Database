<?php

 namespace jsondb\helpers;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Validation for tables
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class Validate {

     /**
      * Name of table
      * @var string
      */
     private $_name;

     /**
      * @param string $name
      * @return \jsondb\helpers\Validate
      */
     public static function factory($name)
     {
         $validate = new Validate();
         $validate->_name = $name;
         return $validate;
     }

     /**
      * Checking that typed field really exist in table
      * @param string $name
      * @return boolean
      * @throws \jsondb\classes\JDBException If field does not exist
      */
     public function field($name)
     {
         if (in_array($name, Config::fields($this->_name)))
         {
             return TRUE;
         }
         throw new \jsondb\classes\JDBException('Field does not exists');
     }

     /**
      * Checking that typed field have correct type of value
      * @param string $name
      * @param mixed $value
      * @return boolean
      * @throws \jsondb\classes\JDBException If type is wrong
      */
     public function type($name, $value)
     {
         $types = Config::fields_type($this->_name);
         if (array_key_exists($name, $types) && $types[$name] == gettype($value))
         {
             return TRUE;
         }

         throw new \jsondb\classes\JDBException('Wrong data type');
     }

 }

?>
