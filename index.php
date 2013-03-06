<?php

 require_once 'jsondb/bootstrap.php';
 define('JSONDB_DATA_PATH', realpath(dirname(__FILE__)).'/data/'); //Path to folder with tables

use \jsondb\classes\JSONDB as JSONDB;

$users = JSONDB::factory('users')->where('id', '>', 20)->order_by('name')->order_by('id')->find_all();

 var_dump($users);

?>
