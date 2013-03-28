<?php

 namespace jsondb\classes\core;

use jsondb\classes\JDBException as JDBException;
use jsondb\classes\JSONDB as JSONDB;
use jsondb\classes\helpers as helper;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Core class of JSONDB project.
  * 
  * There are classes to use JSON files like file database.
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
      * All pending functions parameters in right order
      * @var array
      */
     protected $_pending = array(
         'where' => array(),
         'order_by' => array(),
         'group_by' => array(),
         'as_array' => array()
     );

     /**
      * Information about to reset keys in array or not to
      * @var integer
      */
     protected $_values_clear = 1;

     /**
      * Factory pattern
      * @param string $name Name of table
      * @return \Jsondb
      * @throws JDBException If there's problems with load file
      */
     public static function factory($name)
     {
         $self = new JSONDB();
         $self->_name = $name;

         if (!helper\Table::exists($self->_name))
             throw new JDBException('Table "'.$name.'" does not exists');

         $self->_set_fields();
         $self->_data = helper\Table::get($self->_name);

         return $self;
     }

     /**
      * Returns array key of row with specified ID
      * @param integer $id Row ID
      * @return integer Row key
      * @throws JDBException If there's no data with that ID
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
         throw new JDBException('No data found');
     }

     /**
      * Setting fields with default values
      */
     protected function _set_fields()
     {
         $this->_set = new \stdClass();
         $fields = $this->schema();

         foreach ($fields as $field => $type)
         {
             if (helper\Validate::is_numeric($type) AND $field != 'id')
                 $this->_set->{$field} = 0;
             else
                 $this->_set->{$field} = null;
         }
     }

     /**
      * Validating fields and setting variables to current operations
      * @param string $name Field name
      * @param mixed $value Field value
      */
     public function __set($name, $value)
     {
         if (helper\Validate::factory($this->_name)->field($name)
                 && helper\Validate::factory($this->_name)->type($name, $value))
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

         throw new JDBException('There is no data');
     }

     /**
      * Execute pending functions
      */
     protected function _pending()
     {
         foreach ($this->_pending as $func => $args)
         {
             if (!empty($args))
             {
                 call_user_func(array($this, '_'.$func));
             }
         }
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
      * @throws JDBException If table exist
      */
     public static function create($name, array $fields)
     {
         if (helper\Table::exists($name) && helper\Config::exists($name))
         {
             throw new JDBException('helper\Table "'.$name.'" already exists');
         }
         

         $names = array_keys($fields);
         $types = array_values($fields);
         
         helper\Validate::types($types);

         if (!array_key_exists('id', $fields))
         {
             array_unshift($names, 'id');
             array_unshift($types, 'integer');
         }

         $data = new \stdClass();
         $data->last_id = 0;
         $data->fields = $names;
         $data->types = $types;

         helper\Table::put($name, array());
         helper\Config::put($name, $data);
     }

     /**
      * Removing table with config
      * @param string $name Table name
      * @return boolean|JDBException
      */
     public static function remove($name)
     {
         return helper\Table::remove($name);
     }

     /**
      * Grouping results by one field
      * @param string $column
      * @return \jsondb\classes\core\Core
      */
     public function group_by($column)
     {
         if (helper\Validate::factory($this->_name)->field($column))
         {
             $this->_values_clear = 0;
             $this->_pending[__FUNCTION__] = $column;
         }

         return $this;
     }

     /**
      * Grouping array pending method
      */
     protected function _group_by()
     {
         $column = $this->_pending['group_by'];

         $grouped = array();
         foreach ($this->_data as $object)
         {
             $grouped[$object->{$column}][] = $object;
         }

         $this->_data = $grouped;
     }

     /**
      * Sorting data by field
      * @param string $key Field name
      * @param string $direction ASC|DESC
      * @return \Core
      */
     public function order_by($key, $direction = 'ASC')
     {
         if (helper\Validate::factory($this->_name)->field($key))
         {
             $directions = array(
                 'ASC' => SORT_ASC,
                 'DESC' => SORT_DESC
             );
             $this->_pending[__FUNCTION__][$key] = $directions[$direction];
         }

         return $this;
     }

     /**
      * Sort an array of objects by more than one field.
      * @link http://blog.amnuts.com/2011/04/08/sorting-an-array-of-objects-by-one-or-more-object-property/ It's not mine algorithm
      */
     protected function _order_by()
     {
         $properties = $this->_pending['order_by'];
         uasort($this->_data, function($a, $b) use ($properties)
                 {
                     foreach ($properties as $column => $direction)
                     {
                         if (is_int($column))
                         {
                             $column = $direction;
                             $direction = SORT_ASC;
                         }
                         $collapse = function($node, $props)
                                 {
                                     if (is_array($props))
                                     {
                                         foreach ($props as $prop)
                                         {
                                             $node = (!isset($node->$prop)) ? null : $node->$prop;
                                         }
                                         return $node;
                                     }
                                     else
                                     {
                                         return (!isset($node->$props)) ? null : $node->$props;
                                     }
                                 };
                         $aProp = $collapse($a, $column);
                         $bProp = $collapse($b, $column);

                         if ($aProp != $bProp)
                         {
                             return ($direction == SORT_ASC) ? strnatcasecmp($aProp, $bProp) : strnatcasecmp($bProp, $aProp);
                         }
                     }
                     return FALSE;
                 });
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
         $this->_pending['where'][] = array(
             'type' => 'and',
             'field' => $field,
             'op' => $op,
             'value' => $value,
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
         $this->_pending['where'][] = array(
             'type' => 'or',
             'field' => $field,
             'op' => $op,
             'value' => $value,
         );

         return $this;
     }

     /**
      * Filter function for array_filter() in where()
      * @param object $row
      * @return boolean
      */
     protected function _where()
     {
         $operator = array(
             '=' => '==',
             '!=' => '!=',
             '>' => '>',
             '<' => '<',
             '>=' => '>=',
             '<=' => '<=',
             'and' => '&&',
             'or' => '||'
         );

         $this->_data = array_filter($this->_data, function($row) use ($operator)
                 {
                     $clause = '';

                     foreach ($this->_pending['where'] as $key => $condition)
                     {
                         extract($condition);

                         if (is_array($value))
                         {
                             $value = (in_array($row->{$field}, $value)) ? 1 : 0;
                             $op = '==';
                             $field = 1;
                         }
                         else
                         {
                             $value = is_string($value) ?
                                     '\''.$value.'\'' :
                                     $value;

                             $op = $operator[$op];
                             $field = '$row->'.$field;
                         }

                         $type = (!$key) ?
                                 null :
                                 $operator[$type];

                         $query = array($type, $field, $op, $value);
                         $clause .= implode(' ', $query).' ';
                         eval('$result = '.$clause.';');
                     }

                     return $result;
                 });
     }

     /**
      * Returning data as indexed or assoc array.
      * @param string $key Field that will be the key, NULL for Indexed
      * @param string $value Field that will be the value
      * @return \jsondb\core\Core
      */
     public function as_array($key, $value)
     {
         if (helper\Validate::factory($this->_name)->field($value))
         {
             $this->_values_clear = 0;
             $this->_pending['as_array'] = array(
                 'key' => $key,
                 'value' => $value
             );
         }
         return $this;
     }

     /**
      * Pending function for as_array
      */
     protected function _as_array()
     {
         extract($this->_pending['as_array']);

         $datas = array();

         if (!empty($this->_pending['group_by']))
         {
             foreach ($this->_data as $array)
             {
                 foreach ($array as $data)
                 {
                     if (is_null($key))
                         $datas[] = $data->{$value};
                     else
                         $datas[$data->{$key}] = $data->{$value};
                 }
             }
         }
         else
         {
             foreach ($this->_data as $data)
             {
                 if (is_null($key))
                     $datas[] = $data->{$value};
                 else
                     $datas[$data->{$key}] = $data->{$value};
             }
         }

         $this->_data = $datas;
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
      * Add new fields to table, array schema like in create() function
      * @param array $fields New fields and their types
      */
     public function add_fields(array $fields)
     {
         helper\Validate::types(array_values($fields));

         $schema = $this->schema();
         $fields = array_diff_assoc($fields, $schema);

         if (!empty($fields))
         {
             $config = $this->config();
             $config->schema = array_merge($schema, $fields);

             foreach ($this->_data as $key => $object)
             {
                 foreach ($fields as $name => $type)
                 {
                     if (helper\Validate::is_numeric($type))
                         $this->_data[$key]->{$name} = 0;
                     else
                         $this->_data[$key]->{$name} = null;
                 }
             }

             helper\Table::put($this->_name, $this->_data);
             helper\Config::put($this->_name, $config);
         }
     }

     /**
      * Delete fields from array
      * @param array $fields
      */
     public function delete_fields(array $fields)
     {
         helper\Validate::factory($this->_name)->fields($fields);

         $config = $this->config();
         $config->schema = array_diff_key($this->schema(), array_flip($fields));

         foreach ($this->_data as $key => $object)
         {
             foreach ($fields as $name)
             {
                 unset($this->_data[$key]->{$name});
             }
         }

         helper\Table::put($this->_name, $this->_data);
         helper\Config::put($this->_name, $config);
     }

     /**
      * Returning object with config for table
      * @return object Config
      */
     public function config()
     {
         return helper\Config::get($this->_name);
     }

     /**
      * Return array with names of fields
      * @return array Fields
      */
     public function fields()
     {
         return helper\Config::fields($this->_name);
     }

     /**
      * Returning assoc array with types of fields
      * @return array Fields type
      */
     public function schema()
     {
         return helper\Config::schema($this->_name);
     }

     /**
      * Returning last ID from table
      * @return integer Last ID
      */
     public function last_id()
     {
         return helper\Config::last_id($this->_name);
     }

     /**
      * Saving inserted or updated data
      */
     public function save()
     {
         if (!$this->_current_id)
         {
             $config = $this->config();
             $config->last_id++;

             $this->_set->id = $config->last_id;
             array_push($this->_data, $this->_set);

             helper\Config::put($this->_name, $config);
         }
         else
         {
             $this->_set->id = $this->_current_id;
             $this->_data[$this->_current_key] = $this->_set;
         }

         helper\Table::put($this->_name, $this->_data);

         $this->_set_fields();
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
         elseif (isset($this->_pending['where']) && !empty($this->_pending['where']))
         {
             $old = $this->_data;
             call_user_func(array($this, '_where'));
             $this->_data = array_diff_key($old, $this->_data);
         }
         else
         {
             $this->_data = array();
         }
         $this->_data = array_values($this->_data);

         return helper\Table::put($this->_name, $this->_data) ? true : false;
     }

     /**
      * Return count in integer or array of integers (if grouped)
      * @return mixed 
      */
     public function count()
     {
         $this->_pending();
         if (!empty($this->_pending['group_by']))
         {
             $count = array();
             foreach ($this->_data as $group => $data)
             {
                 $count[$group] = count($data);
             }
         }
         else
         {
             $count = count($this->_data);
         }

         return $count;
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
         $this->_pending();

         return $this->_values_clear ? array_values($this->_data) : $this->_data;
     }

 }

?>