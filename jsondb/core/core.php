<?php

 namespace jsondb\core;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Core class of JSONDB project.
  * 
  * There are classes to use JSON files like file databes.
  * 
  * Using style was inspired by ORM classes.
  *
  * @category Core
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 abstract class Core {

     /**
      * Contain returned data from file as object or array of objects
      * @var mixed Data from table
      */
     protected $_data;

     /**
      * Name of file (table)
      * @var string Name of table
      */
     protected $_name;

     /**
      * Object with setted data
      * @var object Setted data
      */
     protected $_set;

     /**
      * ID of current row if setted
      * @var integer Current ID
      */
     protected $_current_id;

     /**
      * Key if current row if setted
      * @var integer Current key
      */
     protected $_current_key;

     /**
      * Keeping sort order 
      * @var string ASC|DESC
      */
     protected $_sort_order;

     /**
      * Keeping sort key (field in table)
      * @var string Name of field
      */
     protected $_sort_key;

     /**
      * Conditions of where statement
      * @var array 
      */
     protected $_where_conditions = array();

     /**
      * Where type
      * 0 - AND
      * 1 - OR
      * @var integer 0|1
      */
     protected $_where_type = 0;

     /**
      * Factory pattern
      * @param string $name Name of table
      * @return \Jsondb
      * @throws \jsondb\classes\JDBException If there's problems with load file
      */
     public static function factory($name)
     {
         $self = new \jsondb\classes\JSONDB();
         $self->_name = $name;

         if (!\jsondb\helpers\Table::exists($self->_name))
             throw new \jsondb\classes\JDBException('Table does not exists');

         $self->_data = \jsondb\helpers\Table::get($self->_name);

         return $self;
     }

     /**
      * Constructor, setting new data object
      */
     public function __construct()
     {
         $this->_set = new \stdClass();
         $this->_set->id = null;
     }

     /**
      * Returns array key of row with specified ID
      * @param integer $id Row ID
      * @return integer Row key
      * @throws \jsondb\classes\JDBException If there's no data with that ID
      */
     protected function _get_row_key($id)
     {
         foreach ($this->_data as $key => $data)
         {
             if ($data->id == $id)
             {
                 $this->_current_key = $key;
                 return $key;
                 break;
             }
         }
         throw new \jsondb\classes\JDBException('No data found');
     }

     /**
      * Validating fields and setting variables to current operations
      * @param string $name Field name
      * @param mixed $value Field value
      */
     public function __set($name, $value)
     {
         if (\jsondb\helpers\Validate::factory($this->_name)->field($name)
                 && \jsondb\helpers\Validate::factory($this->_name)->type($name, $value))
         {
             $this->_set->{$name} = $value;
         }
     }

     /**
      * Returning variable from Object
      * @param string $name Field name
      * @return mixed Field value
      */
     public function __get($name)
     {
         if (isset($this->_set->{$name}))
             return $this->_set->{$name};

         throw new \jsondb\classes\JDBException('There is no data');
     }

     /**
      * Creating new table
      * 
      * For example few fields:
      * 
      * JSONDB::create('news', array(
      *  'title' => 'string',
      *  'content' => 'string',
      *  'rating' => 'double',
      *  'author' => 'integer'
      * ));
      * 
      * Types of field:
      * - boolean
      * - integer
      * - string
      * - double (also for float type)
      * 
      * ID field isn't required (it will be created automatically) but you can specify it at first place.
      * 
      * @param string $name Table name
      * @param array $fields Field configuration
      * @throws \jsondb\classes\JDBException If table exist
      */
     public static function create($name, array $fields)
     {
         if (\jsondb\helpers\Table::exists($name) && \jsondb\helpers\Config::exists($name))
         {
             throw new \jsondb\classes\JDBException('\jsondb\helpers\Table already exists');
         }

         $names = array_keys($fields);
         $types = array_values($fields);

         if (!array_key_exists('id', $fields))
         {
             array_unshift($names, 'id');
             array_unshift($types, 'integer');
         }

         $data = new \stdClass();
         $data->last_id = 0;
         $data->fields = $names;
         $data->types = $types;

         \jsondb\helpers\Table::put($name, array());
         \jsondb\helpers\Config::put($name, $data);
     }

     /**
      * Removing table with config
      * @param string $name Table name
      * @return boolean|\jsondb\classes\JDBException
      */
     public static function remove($name)
     {
         return \jsondb\helpers\Table::remove($name);
     }

     /**
      * Returning object with config for table
      * @return object Config
      */
     public function config()
     {
         return \jsondb\helpers\Config::get($this->_name);
     }

     /**
      * Return array with names of fields
      * @return array Fields
      */
     public function fields()
     {
         return \jsondb\helpers\Config::fields($this->_name);
     }

     /**
      * Returning assoc array with types of fields
      * @return array Fields type
      */
     public function fields_type()
     {
         return \jsondb\helpers\Config::fields_type($this->_name);
     }

     /**
      * Returning last ID from table
      * @return integer Last ID
      */
     public function last_id()
     {
         return \jsondb\helpers\Config::last_id($this->_name);
     }

     /**
      * Comparison function for usort() in order_by()
      * @param object $objA 
      * @param object $objB
      * @return integer
      */
     protected function _sort_cmp($objA, $objB)
     {
         $key = $this->_sort_key;
         $order = $this->_sort_order;

         $a = $objA->{$key};
         $b = $objB->{$key};
         if ($order == 'ASC')
         {
             return (is_int($a) && is_int($b)) ? $a - $b : strcmp($a, $b);
         }
         elseif ($order == 'DESC')
         {
             return (is_int($a) && is_int($b)) ? $b - $a : strcmp($b, $a);
         }
     }

     /**
      * Sorting data by field
      * @param string $key Field name
      * @param string $order ASC|DESC
      * @return \Core
      */
     public function order_by($key, $order = 'ASC')
     {
         if (\jsondb\helpers\Validate::factory($this->_name)->field($key))
         {
             $this->_sort_key = $key;
             $this->_sort_order = $order;

             if (is_array($this->_data))
             {
                 usort($this->_data, array($this, '_sort_cmp'));
             }

             return $this;
         }
     }

     /**
      * Where function, like SQL
      * 
      * Operators:
      * - Standard operators (=, !=, >, <, >=, <=)
      * - IN (only for array value)
      * - NOT IN (only for array value)
      * 
      * @param string $field Field name
      * @param string $op Operator
      * @param mixed $value Field value
      * @return \jsondb\core\Core
      */
     public function where($field, $op, $value)
     {
         $this->_where_conditions[] = array(
             'field' => $field,
             'op' => $op,
             'value' => $value
         );

         return $this;
     }

     /**
      * Alias for where()
      * @param string $field Field name
      * @param string $op Operator
      * @param mixed $value Field value
      * @return \jsondb\core\Core
      */
     public function and_where($field, $op, $value)
     {
         $this->where($field, $op, $value);

         return $this;
     }

     /**
      * Alias for where(), setting OR for searching
      * @param string $field Field name
      * @param string $op Operator
      * @param mixed $value Field value
      * @return \jsondb\core\Core
      */
     public function or_where($field, $op, $value)
     {
         $this->_where_type = 1;
         $this->where($field, $op, $value);

         return $this;
     }

     /**
      * Filter function for array_filter() in where()
      * @param object $row
      * @return boolean
      */
     protected function _where($row)
     {
         $operator = array(
             '=' => '==',
             '!=' => '!=',
             '>' => '>',
             '<' => '<',
             '>=' => '>=',
             '<=' => '<=',
         );

         $result = true;

         foreach ($this->_where_conditions as $condition)
         {
             extract($condition);

             if (is_array($value))
             {
                 if ($op == 'IN')
                     $exec = in_array($row->{$field}, $value);
                 elseif ($op == 'NOT IN')
                     $exec = !in_array($row->{$field}, $value);
             }
             else
             {
                 eval('$exec = strtolower($row->{$field}) '.$operator[$op].' strtolower($value);');
             }

             if ($exec)
             {
                 $result = true;
                 if ($this->_where_type)
                     break;
                 else
                     continue;
             }
             else
             {
                 $result = false;
                 if ($this->_where_type)
                     continue;
                 else
                     break;
             }
         }

         return $result;
     }

     /**
      * Returning data as indexed or assoc array.
      * @param string $key Field that will be the key, NULL for Indexed
      * @param string $value Field that will be the value
      * @return \jsondb\core\Core
      */
     public function as_array($key, $value)
     {
         $datas = array();
         foreach ($this->_data as $data)
         {
             if (is_null($key))
                 $datas[] = $data->{$value};
             else
                 $datas[$data->{$key}] = $data->{$value};
         }

         $this->_data = $datas;

         return $this;
     }

     /**
      * Limit returned data
      * 
      * Should be used at the end of chain, before end method
      * @param integer $number Limit number
      * @param integer $offset Offset number
      * @return \Core
      */
     public function limit($number, $offset = 0)
     {
         $this->_data = array_slice($this->_data, $offset, $number);
         return $this;
     }

     /**
      * Saving inserted or updated data
      */
     public function save()
     {
         if (!$this->_current_id)
         {
             $config = $this->config();
             $config->last_id += 1;

             $this->_set->id = $config->last_id;
             array_push($this->_data, $this->_set);

             \jsondb\helpers\Config::put($this->_name, $config);
         }
         else
         {
             $this->_set->id = $this->_current_id;
             $this->_data[$this->_current_key] = $this->_set;
         }

         \jsondb\helpers\Table::put($this->_name, $this->_data);

         $this->_set = new \stdClass();
         $this->_set->id = null;
     }

     /**
      * Deleting loaded data
      * @return boolean
      */
     public function delete()
     {
         if (isset($this->_current_id))
         {
             unset($this->_data[$this->_current_key]);
         }
         else
         {
             $this->_data = array();
         }
         $this->_data = array_values($this->_data);

         return \jsondb\helpers\Table::put($this->_name, $this->_data) ? true : false;
     }

     /**
      * @return integer Data count
      */
     public function count()
     {
         return count($this->_data);
     }

     /**
      * Returns one row with specified ID
      * @param integer $id Row ID
      * @return \Core
      */
     public function find($id)
     {
         $this->_current_id = $id;
         foreach ($this->_data[$this->_get_row_key($id)] as $field => $value)
         {
             $this->{$field} = $value;
         }

         return $this;
     }

     /**
      * Returning all of data
      * @return array Data
      */
     public function find_all()
     {
         if (!empty($this->_where_conditions))
         {
             $this->_data = array_filter($this->_data, array($this, '_where'));
         }

         return $this->_data;
     }

 }

?>