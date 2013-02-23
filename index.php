<?php

 require_once 'jsondb/bootstrap.php';
 define('JSONDB_DATA_PATH', realpath(dirname(__FILE__)).'/data/'); //Path to folder with tables

use \jsondb\classes\JSONDB as DB;

$users = DB::factory('users')->find_all();

 var_dump($users);
?>
