<?php

 namespace jsondb\classes;

defined('JSONDB_SECURE') or die('Permission denied!');

 /**
  * Exception extend
  *
  * @category Exceptions
  * @author Grzegorz Kuźnik
  * @copyright (c) 2013, Grzegorz Kuźnik
  * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  */
 class JDBException extends \Exception {

     public function __construct($message, $code = 0)
     {
         parent::__construct($message, $code);
     }

     public function __toString()
     {
         return $this->message;
     }

 }

?>
