<?php

 namespace JSONDb\Classes\Helpers;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Data managing class
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
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
 