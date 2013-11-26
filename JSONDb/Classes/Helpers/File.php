<?php

 namespace JSONDb\Classes\Helpers;

use JSONDb\Classes\Exception;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * File managing class
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class File implements FileInterface {

     /**
      * File name
      * @var string
      */
     protected $_name;

     /**
      * File type (data|config)
      * @var string
      */
     protected $_type;

     public static function name($name)
     {
         $file = new File;
         $file->_name = $name;

         return $file;
     }

     public final function setType($type)
     {
         $this->_type = $type;
     }

     public final function getPath()
     {
         if (!empty($this->_type))
         {
             return JSONDB_DATA_PATH.$this->_name.'.'.$this->_type.'.json';
         }
         else
         {
             throw new Exception('You must specify the type of file in class: '.__);
         }
     }

     public final function get($assoc = false)
     {
         return json_decode(file_get_contents($this->getPath()), $assoc);
     }

     public final function put($data)
     {
         return file_put_contents($this->getPath(), json_encode($data));
     }

     public final function exists()
     {
         return file_exists($this->getPath());
     }

     public final function remove()
     {
         $type = ucfirst($this->_type);
         if ($this->exists())
         {
             if (unlink($this->getPath()))
                 return TRUE;

             throw new Exception($type.': Deleting failed');
         }

         throw new Exception($type.': File does not exists');
     }

     /**
      * Finds file from data folder
      * @param array $names
      * @param type $type
      * @return boolean
      */
     public static function find_file(array $names, $type = 'config')
     {
         $names = implode(',', $names);
         $pattern = './data/{'.$names.'}.'.$type.'.json';
         $found = glob($pattern, GLOB_BRACE);
         if (!empty($found))
         {
             preg_match('#./data/(.*).'.$type.'.json#', $found[0], $found_filename);
             return $found_filename[1];
         }

         return false;
     }

 }

