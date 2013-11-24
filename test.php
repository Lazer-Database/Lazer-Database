<?php

 require_once 'JSONDb/bootstrap.php';

use JSONDb\Classes\Database as JSONDB;

$db = JSONDB::factory('cars');

 var_dump($db);