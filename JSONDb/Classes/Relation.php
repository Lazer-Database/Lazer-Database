<?php

 namespace JSONDb\Classes;
 
use JSONDb\Classes\Helpers\Validate;
use JSONDb\Classes\Database;
 
 class Relation extends Core_Relation {
     
     private $tables = array();
     private $keys = array();
     private static $relations = array(
         'belongs_to' => 1,
         'has_many' => 2,
         'has_and_belongs_to_many' => 3
     );
     
     public function __construct($table_st, $table_nd)
     {
         $this->tables = array($table_st, $table_nd);
     }
     
     public function set($relation_st, $relation_nd)
     {
         if(Validate::relation_type(array_keys($relation_st)[0]) && Validate::relation_type(array_keys($relation_nd)[0]))
         {
             $this->keys = array($relation_st, $relation_nd);
         }
     }
     
     public static function relations()
     {
         return array_keys(self::$relations);
     }
     
     public function confirm() {}
     
     
     
 }
