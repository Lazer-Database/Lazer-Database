<?php

 namespace JSONDb\Classes;

use JSONDb\Classes\Helpers\Validate;
use JSONDb\Classes\Helpers\Config;
use JSONDb\Classes\Database;
use JSONDb\Classes\Exception;

 /**
  * Relation class of JSONDB project.
  * 
  * @category Core
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://opensource.org/licenses/MIT The MIT License
  */
 abstract class Core_Relation {

     /**
      * Tables names
      * @var array tables
      */
     protected $tables = array(
         'local' => null,
         'foreign' => null
     );

     /**
      * Relation keys names
      * @var array keys
      */
     protected $keys = array(
         'local' => null,
         'foreign' => null
     );

     /**
      * Current relation type
      * @var string
      */
     protected $relationType;

     /**
      * All relations types
      * @var array
      */
     protected static $relations = array('belongsTo', 'hasMany', 'hasAndBelongsToMany');

     /**
      * Factory method
      * @param string $name Name of table
      * @return \JSONDb\Classes\Relation
      */
     public static function table($name)
     {
         Validate::name($name)->exists();

         $self = new Relation;
         $self->_tables['local'] = $name;

         return $self;
     }

     /**
      * Getter of junction table name in many2many relation
      * @return boolean|string Name of junction table or false
      */
     public function getJunction()
     {
         if ($this->_relationType == 'hasAndBelongsToMany')
         {
             $tables = $this->_tables;
             sort($tables);
             return implode('_', $tables);
         }
         return false;
     }

     /**
      * Set relation type to field
      * @param string $relation Name of relation
      */
     protected function setRelation($relation)
     {
         Validate::relation_type($relation);
         $this->_relationType = $relation;
     }

     /**
      * Set table name
      * @param string $type local or foreign
      * @param string $name table name
      */
     protected function setTable($type, $name)
     {
         Validate::name($name)->exists();
         $this->_tables[$type] = $name;
     }

     /**
      * Set key name
      * @param string $type local or foreign
      * @param string $key key name
      * @return \JSONDb\Classes\Core_Relation
      * @throws Exception First you must define tables name
      */
     protected function setKey($type, $key)
     {
         if (!in_array(null, $this->_tables))
         {
             Validate::name($this->_tables[$type])->field($key);

             $this->_keys[$type] = $key;
             return $this;
         }

         throw new Exception('First you must define tables name');
     }

     /**
      * Set local key name
      * @param string $key key name
      * @return \JSONDb\Classes\Core_Relation
      * @throws Exception First you must define tables name
      */
     public function localKey($key)
     {
         return $this->setKey('local', $key);
     }

     /**
      * Set foreign key name
      * @param string $key key name
      * @return \JSONDb\Classes\Core_Relation
      * @throws Exception First you must define tables name
      */
     public function foreignKey($key)
     {
         return $this->setKey('foreign', $key);
     }

     /**
      * Set relation one2many to table 
      * @param string $table Table name
      * @return \JSONDb\Classes\Core_Relation
      */
     public function belongsTo($table)
     {
         $this->setTable('foreign', $table);
         $this->setRelation(__FUNCTION__);

         return $this;
     }

     /**
      * Set relation many2one to table 
      * @param string $table Table name
      * @return \JSONDb\Classes\Core_Relation
      */
     public function hasMany($table)
     {
         $this->setTable('foreign', $table);
         $this->setRelation(__FUNCTION__);

         return $this;
     }
     

     /**
      * Set relation many2many to table 
      * @param string $table Table name
      * @return \JSONDb\Classes\Core_Relation
      */
     public function hasAndBelongsToMany($table)
     {
         $this->setTable('foreign', $table);
         $this->setRelation(__FUNCTION__);

         return $this;
     }


     /**
      * Use relation to table
      * @param string $table Table name
      * @return \JSONDb\Classes\Core_Relation
      */
     public function with($table)
     {
         $this->setTable('foreign', $table);
         $this->setRelation(Config::name($this->_tables['local'])->relations($this->_tables['foreign'])->type);
         $this->setKey('local', Config::name($this->_tables['local'])->relations($this->_tables['foreign'])->keys->local);
         $this->setKey('foreign', Config::name($this->_tables['local'])->relations($this->_tables['foreign'])->keys->foreign);

         return $this;
     }

     /**
      * Set specified relation
      * @throws Exception Tables names or keys missing
      */
     public function set()
     {
         if (!in_array(null, $this->_tables) && !in_array(null, $this->_keys))
         {
             $this->createRelation();
         }
         else
         {
             throw new Exception('Tables names or keys missing');
         }
     }

     /**
      * Get relation information
      * @return array relation information
      */
     public function get()
     {
         return array(
             'tables' => $this->_tables,
             'keys' => $this->_keys,
             'type' => $this->_relationType
         );
     }

     /**
      * Add data to configs and create all necessary files
      */
     protected function createRelation()
     {
         if ($this->_relationType == 'hasAndBelongsToMany')
         {
             $junction = $this->getJunction();

             try {
                 Validate::name($junction)->exists();
             }
             catch (Exception $e) {
                 Database::create($junction, array(
                     $this->_tables['local'].'_id' => 'integer',
                     $this->_tables['foreign'].'_id' => 'integer',
                 ));

                 $this->addRelation($junction, $this->_tables['local'], 'hasMany', array(
                     'local' => $this->_tables['local'].'_id',
                     'foreign' => $this->_keys['local']
                 ));

                 $this->addRelation($junction, $this->_tables['foreign'], 'hasMany', array(
                     'local' => $this->_tables['foreign'].'_id',
                     'foreign' => $this->_keys['foreign']
                 ));
             }
         }
         $this->addRelation($this->_tables['local'], $this->_tables['foreign'], $this->_relationType, $this->_keys);
     }

     protected function addRelation($from, $to, $type, array $keys)
     {
         $config = Config::name($from);
         $content = $config->get();
         $content->relations->{$to} = array(
             'type' => $type,
             'keys' => $keys,
         );
         $config->put($content);
     }

     protected function join($row)
     {
         $keys['local'] = $this->_keys['local'];
         $keys['foreign'] = $this->_keys['foreign'];

         if ($this->_relationType == 'hasAndBelongsToMany')
         {
             $join = Database::table($this->getJunction())
                     ->group_by($this->_tables['local'].'_id')
                     ->where($this->_tables['local'].'_id', '=', $row->{$keys['local']})
                     ->find_all()
                     ->as_array($this->_tables['local'].'_id', $this->_tables['foreign'].'_id');


             if (empty($join))
                 return array();

             return Database::table($this->_tables['foreign'])
                             ->where($keys['foreign'], 'IN', $join[$row->{$keys['local']}]);
         }

         return Database::table($this->_tables['foreign'])
                         ->where($keys['foreign'], '=', $row->{$keys['local']});
     }

     public function build($array, $part)
     {
         $return = array();
         foreach ($array as $key => $row)
         {
             if (is_object($row))
             {
                 if ($row instanceof \stdClass)
                 {
                     $part = ucfirst($part);

                     if (!isset($row->{$part}))
                     {
                         $query = $this->join($row);

                         if ($this->_relationType == 'belongsTo')
                         {
                             $query = $query->find_all();
                             $query = reset($query)[0];
                         }

                         $row->{$part} = $query;
                     }

                     $array[$key] = $row->{$part};
                     $return[] = $row->{$part};
                 }
                 else
                 {
                     $row->with($part);
                 }
             }
             else
             {
                 $return = array_merge($return, $this->build($row, $part));
             }
         }
         return $return;
     }

     public static function relations()
     {
         return self::$relations;
     }

 }
 