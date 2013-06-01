<?php

 namespace jsondb\classes\helpers;

use jsondb\classes\JDBException as JDBException;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * File managing class
  *
  * @category Helpers
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class File {

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

     /**
      * Setting the values
      * @param string $name File name
      * @param string $type File type (data|config)
      */
     public function __construct($name, $type)
     {
         $this->_name = $name;
         $this->_type = $type;
     }

     /**
      * Returning path to file
      * @return string Path to file
      */
     protected function getPath()
     {
         return JSONDB_DATA_PATH.$this->_name.'.'.$this->_type.'.json';
     }

     /**
      * Return decoded JSON
      * @param boolean $assoc Returns object if false; array if true
      * @return mixed (object|array)
      */
     public function get($assoc = false)
     {
         return json_decode(file_get_contents(self::getPath()), $assoc);
     }

     /**
      * Saving encoded JSON to file
      * @param object $data
      * @return boolean
      */
     public function put($data)
     {
         return file_put_contents(self::getPath(), json_encode($data));
     }

     /**
      * Checking that file exists
      * @return boolean
      */
     public function exists()
     {
         return file_exists(self::getPath());
     }

     /**
      * Removing file
      * @return boolean
      * @throws JDBException If file doesn't exists or there's problems with deleting files
      */
     public function remove()
     {
         $type = ucfirst($this->_type);
         if (self::exists())
         {
             if (unlink(self::getPath()))
                 return TRUE;

             throw new JDBException($type.': Deleting failed');
         }

         throw new JDBException($type.': File does not exists');
     }

     /**
      * Finds file from data folder
      * @param array $names
      * @param type $type
      * @return boolean
      */
     public static function find_file(array $names, $type='config')
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

?>
