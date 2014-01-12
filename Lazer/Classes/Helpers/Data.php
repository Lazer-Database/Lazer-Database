<?php

 namespace Lazer\Classes\Helpers;

defined('LAZER_SECURE') or die('Permission denied!');

 /**
  * Data managing class
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://opensource.org/licenses/MIT The MIT License
  */
 class Data extends File {

     public static function table($name)
     {
         $file = new Data;
         $file->name = $name;
         $file->setType('data');

         return $file;
     }

 }
 