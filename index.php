<?php

 require_once 'includes/config.php';
 define('JSONDB_DATA_PATH', realpath(dirname(__FILE__)).'/data/');

 $users = Jsondb::factory('users');
 
 $users->pozdro = 'asd';
 $users->pa = '345';
 
 $users->save();
 
 var_dump($users);

?>
