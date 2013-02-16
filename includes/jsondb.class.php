<?php

 defined('JSONDB') or die('Permission denied!');

 /**
  * Core class of JSONDB project
  *
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  */
 class Jsondb extends Core {

     public static function factory($name)
     {
         $self = new Jsondb;
         $self->name = $name;
         
         try {
             if (!file_exists(JSONDB_DATA_PATH.$self->name.'.data.json'))
                 throw new JDBException('Failed to load data file');

             $self->data = json_decode(File::get($self->name));
         }
         catch (JDBException $exc) {
             echo $exc;
         }
         
         return $self;
     }

 }

?>
