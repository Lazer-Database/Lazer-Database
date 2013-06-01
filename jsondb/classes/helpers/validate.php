<?php

 namespace jsondb\classes\helpers;

use jsondb\classes\JDBException as JDBException;
use \jsondb\classes\core\Relation as Relation;

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
     public static function name($name)
     {
         $validate = new Validate();
         $validate->_name = $name;
         return $validate;
     }

     /**
      * Checking that field type is numeric
      * @param string $type
      * @return boolean
      */
     public static function is_numeric($type)
     {
         $defined = array('integer', 'double');

         if (in_array($type, $defined))
         {
             return TRUE;
         }

         return FALSE;
     }

     /**
      * Checking that types from array matching with [boolean, integer, string, double]
      * @param array $fields Indexed array
      * @return array Fields without ID
      */
     public static function types(array $types)
     {
         $defined = array('boolean', 'integer', 'string', 'double');
         $diff = array_diff($types, $defined);

         if (empty($diff))
         {
             return TRUE;
         }
         throw new JDBException('Wrong types: "'.implode(', ', $diff).'". Available "boolean, integer, string, double"');
     }

     /**
      * Delete ID field from arrays
      * @param array $fields
      * @return array Fields without ID
      */
     public static function filter(array $fields)
     {
         if (array_values($fields) === $fields)
         {
             if (($key = array_search('id', $fields)) !== false)
             {
                 unset($fields[$key]);
             }
         }
         else
         {
             unset($fields['id']);
         }
         return $fields;
     }

     /**
      * Change keys and values case to lower
      * @param array $array
      * @return array
      */
     public static function arr_to_lower(array $array)
     {
         $array = array_change_key_case($array);
         $array = array_map('strtolower', $array);

         return $array;
     }

     /**
      * Checking that typed fields really exist in table
      * @param array $fields Indexed array
      * @return boolean
      * @throws JDBException If field(s) does not exist
      */
     public function fields(array $fields)
     {
         $fields = self::filter($fields);
         $diff = array_diff($fields, Config::name($this->_name)->fields());

         if (empty($diff))
         {
             return TRUE;
         }
         throw new JDBException('Field(s) "'.implode(', ', $diff).'" does not exists in table "'.$this->_name.'"');
     }

     /**
      * Checking that typed field really exist in table
      * @param string $name
      * @return boolean
      * @throws JDBException If field does not exist
      */
     public function field($name)
     {
         if (in_array($name, Config::name($this->_name)->fields()))
         {
             return TRUE;
         }
         throw new JDBException('Field '.$name.' does not exists in table "'.$this->_name.'"');
     }
     
     public function exists()
     {
         if (!Data::name($this->_name)->exists())
             throw new JDBException('Table "'.$this->_name.'" does not exists');
         
         if (!Config::name($this->_name)->exists())
             throw new JDBException('Config "'.$this->_name.'" does not exists');

         return TRUE;
     }

     /**
      * Checking that typed field have correct type of value
      * @param string $name
      * @param mixed $value
      * @return boolean
      * @throws JDBException If type is wrong
      */
     public function type($name, $value)
     {
         $schema = Config::name($this->_name)->schema();
         if (array_key_exists($name, $schema) && $schema[$name] == gettype($value))
         {
             return TRUE;
         }

         throw new JDBException('Wrong data type');
     }

     /**
      * Checking that relation between tables exists
      * @param string $local local table
      * @param string $foreign related table
      * @throws JDBException
      */
     public static function relation($local, $foreign)
     {
         $relations = Config::name($local)->relations();
         if (isset($relations->{$foreign}))
         {
             return TRUE;
         }
         
         throw new JDBException('Relation '.$local.'-'.$foreign.' does not exists');
     }

     /**
      * Checking that relation type is correct
      * @param string $type 
      * @return type
      */
     public static function relation_type($type)
     {
         if (in_array($type, Relation::get_relations()))
         {
             return TRUE;
         }
         
         throw new JDBException('Wrong relation type');
     }

 }

?>
