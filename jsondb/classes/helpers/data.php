<?php

 namespace jsondb\classes\helpers;

use jsondb\classes\JDBException as JDBException;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Data managing class adapter of File class
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class Data {
     
     /**
      * Setting name of table
      * @param string $name
      * @return \jsondb\classes\helpers\File
      */
     public static function name($name)
     {
         $table = new File($name, 'data');
         
         return $table;
     }

 }

?>
