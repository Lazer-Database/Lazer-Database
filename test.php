<?php

 require_once 'jsondb/bootstrap.php';
 define('JSONDB_DATA_PATH', realpath(dirname(__FILE__)).'/data/'); //Path to folder with tables

use \jsondb\classes\JSONDB as JSONDB;


//$models[1] = array('159', '2000', '2600', 'AR6', 'AR8', 'Giulia', 'Giulietta', 'Sprint');
// $models[2] = array('A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'S8', 'TT');
// $models[3] = array('303', '501', 'M1');
// $models[4] = array('Astra', 'Corvette', 'Lumina', 'Spark', 'Vectra');
// $models[5] = array('Berlingo', 'C2', 'C3', 'C4', 'Xsara Picasso', 'Jumper');
// $models[6] = array('Logan', 'Sandero');
// $models[7] = array('125p', '126p', 'Panda', '500', 'Punto', 'Ducato', 'Uno');
// $models[8] = array('Escort', 'Fiesta', 'Focus', 'Mustang', 'Sierra');
// $models[9] = array('Civic', 'CRX');
// $models[10] = array('Citan', 'R171');
// $models[11] = array('Almera', 'Micra', 'Juke');
// $models[12] = array('Astra', 'Corsa', 'Vectra', 'Zafira');
// $models[13] = array('110', '430', 'Fabia', 'Felicia', 'Octavia', 'RSO');
// $models[14] = array('Golf', 'Passat', 'Polo');
// $models[15] = array('440', 'FH');
//
$car = JSONDB::factory('cars')->with('marques')->find_all();


echo'<pre>';
print_r($car);

?>
