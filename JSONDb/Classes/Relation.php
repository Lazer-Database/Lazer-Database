<?php

 namespace JSONDb\Classes;

use JSONDb\Classes\Helpers\Validate;
use JSONDb\Classes\Helpers\Config;
use JSONDb\Classes\Database;
use JSONDb\Classes\Exception;

 class Relation extends Core_Relation {

     private $_tables = array(
         'local' => null,
         'foreign' => null
     );
     private $_keys = array(
         'local' => null,
         'foreign' => null
     );
     private $_relationType;
     private static $relations = array(
         'belongsTo' => 1,
         'hasMany' => 2,
         'hasAndBelongsToMany' => 3
     );

     public static function table($name)
     {
         Validate::name($name)->exists();

         $self = new Relation;
         $self->_tables['local'] = $name;

         return $self;
     }

     private function setKey($type, $key)
     {
         if (!in_array(null, $this->_tables))
         {
             Validate::name($this->_tables[$type])->field($key);

             $this->_keys[$type] = $key;
             return $this;
         }

         throw new Exception('First you must define tables name');
     }

     public function localKey($key)
     {
         return $this->setKey('local', $key);
     }

     public function foreignKey($key)
     {
         return $this->setKey('foreign', $key);
     }

     private function setTable($type, $name)
     {
         Validate::name($name)->exists();
         $this->_tables[$type] = $name;
     }

     public function belongsTo($table)
     {
         $this->setTable('foreign', $table);
         $this->_relationType = __FUNCTION__;

         return $this;
     }

     public function hasMany($table)
     {
         $this->setTable('foreign', $table);
         $this->_relationType = __FUNCTION__;

         return $this;
     }
     
     public function with($table)
     {
         $this->setTable('foreign', $table);
         return $this;
     }

     public function set()
     {
         if (!in_array(null, $this->_tables) && !in_array(null, $this->_keys))
         {
             $this->createRelation();
         }
         else
         {
             throw new Exception('Tables name or keys missing');
         }
     }
     
     public function get()
     {
         $config = Config::name($this->_tables['local'])->get(true);
         if($this->exist())
         {
             return $config['relations'][$this->_tables['foreign']];
         }
     }
     
     public function exist()
     {
         $config = Config::name($this->_tables['local'])->get();
         if(property_exists($config->relations, $this->_tables['foreign']))
         {
             return true;
         }
             throw new Exception('Relation "'.$this->_tables['local'].'" to "'.$this->_tables['foreign'].'" doesn\'t exist');
     }

     private function createRelation()
     {
         $config = Config::name($this->_tables['local']);
         $content = $config->get();
         $content->relations->{$this->_tables['foreign']} = array(
             'type' => $this->_relationType,
             'keys' => $this->_keys,
         );
         $config->put($content);
     }

     public static function relations()
     {
         return array_keys(self::$relations);
     }

 }
 