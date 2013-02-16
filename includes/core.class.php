<?php

 defined('JSONDB') or die('Permission denied!');

 /**
  * Core class of JSONDB project
  *
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  */
 abstract class Core {

     public $data;
     public $name;
     protected $set;

     public function __set($name, $value)
     {
         $this->set->{$name} = $value;
         
     }

     public function save()
     {
         array_push($this->data, $this->set);
         File::put($this->name, json_encode($this->data));
     }

     public function find_all()
     {
         return $this->data;
     }

 }

?>
