<?php

 namespace jsondb\classes\core;

use jsondb\classes\JDBException as JDBException;
use jsondb\classes\helpers as helper;
use jsondb\classes\JSONDB as JSONDB;

 class Relation {

     private $_local;
     private $_foreign;
     private $_junction;
     private $_relations = array(
         'belongs_to' => 1,
         'has_many' => 2,
         'has_and_belongs_to_many' => 3
     );
     private $_relation;
     private $_keys = array();

     public function __construct($local, $foreign)
     {
         helper\Validate::name($local)->exists();
         helper\Validate::name($foreign)->exists();
         helper\Validate::relation($local, $foreign);
             
         $this->_local = $local;
         $this->_foreign = $foreign;
         $this->set_relation();
         $this->set_keys();
     }

     private function set_relation()
     {
         $relation = helper\Config::name($this->_local)->relations($this->_foreign)->type;
         $this->_relation = $this->_relations[$relation];

         if ($this->_relation === 3)
         {
             $this->_junction = $this->get_junction();
         }
     }

     private function set_keys()
     {
         $this->_keys['local'] = helper\Config::name($this->_local)->relations($this->_foreign)->keys;
         $this->_keys['foreign'] = helper\Config::name($this->_foreign)->relations($this->_local)->keys;
     }

     private function get_junction()
     {
         return helper\File::find_file(array($this->_local.'_'.$this->_foreign, $this->_foreign.'_'.$this->_local));
     }

     private function join($row)
     {
         $keys['local'] = $this->_keys['local'];
         $keys['foreign'] = $this->_keys['foreign'];

         if ($this->_relation == 3)
         {
             $join = JSONDB::factory($this->_junction)
                     ->group_by($keys['local']->foreign)
                     ->as_array($keys['local']->foreign, $keys['foreign']->foreign)
                     ->where($keys['local']->foreign, '=', $row->{$keys['local']->local})
                     ->find_all();

             if (empty($join))
                 return array();

             return JSONDB::factory($this->_foreign)
                             ->where($keys['foreign']->local, 'IN', $join[$row->{$keys['local']->local}])
                             ->find_all();
         }

         return JSONDB::factory($this->_foreign)
                         ->where($keys['foreign']->local, '=', $row->{$keys['local']->local})
                         ->find_all();
     }

     public function build($array, $part)
     {
         $return = array();
         foreach ($array as $key => $row)
         {
             if (is_object($row))
             {
                 if (!isset($row->{$part}))
                 {
                     $query = $this->join($row);

                     $row->{$part} = ($this->_relation == 1) ? reset($query) : $query;
                 }

                 $array[$key] = $row->{$part};
                 $return[] = $row->{$part};
             }
             else
             {
                 $return = array_merge($return, $this->build($row, $part));
             }
         }
         return $return;
     }
     
     public static function get_relations()
     {
         return array_keys($this->_relations);
     }

 }

?>
