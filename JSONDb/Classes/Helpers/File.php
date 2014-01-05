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
     protected $name;

     /**
      * File type (data|config)
      * @var string
      */
     protected $type;

     public static function table($name)
     {
         $file = new File;
         $file->name = $name;

         return $file;
     }

     public final function setType($type)
     {
         $this->type = $type;
     }

     public final function getPath()
     {
         if (!empty($this->type))
         {
             return JSONDB_DATA_PATH.$this->name.'.'.$this->type.'.json';
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
         $type = ucfirst($this->type);
         if ($this->exists())
         {
             if (unlink($this->getPath()))
                 return TRUE;

             throw new Exception($type.': Deleting failed');
         }

         throw new Exception($type.': File does not exists');
     }
 }

