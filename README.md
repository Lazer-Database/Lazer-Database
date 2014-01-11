Lazer - database based on JSON files
=============

PHP Library to use JSON files like a FlatFileDatabase.   
Functionality inspired by ORM's

Requirements
-------
- PHP 5.4+

Structure of table files
-------

`table_name.data.json` - table file with data   
`table_name.config.json` - table file with configuration 

    
Basic Usage
------

First of all you should open `Lazer/bootstrap.php` file and define constant `LAZER_DATA_PATH` containing absolute path to folder with JSON files:
```php
define('LAZER_DATA_PATH', realpath(dirname(__FILE__)).'/data/'); //Path to folder with tables
```

Then include it in your file and set up namespace:
```php
require_once 'Lazer/bootstrap.php';
use Lazer\Classes\Database as Lazer; // example
```

### Methods

##### Chain methods

- `limit()` - returns results between a certain number range. Should be used right before ending method `find_all()`.
- `orderBy()` - sort rows by key in order, can order by more than one field (just chain it). 
- `groupBy()` - group rows by field.
- `where()` - filter records. Alias: `and_where()`.
- `orWhere()` - other type of filtering results. 
- `with()` - join other tables by defined relations

##### Ending methods

- `addFields()` - append new fields into existing table
- `deleteFields()` - removing fields from existing table
- `save()` - insert or Update data.
- `delete()` - deleting data.
- `relations()` - returns array with table relations
- `config()` - returns object with configuration.
- `fields()` - returns array with fields name.
- `schema()` - returns assoc array with fields name and fields type `field => type`.
- `lastId()` - returns last ID from table.
- `count()` - returns the number of rows.
- `find()` - returns one row with specified ID.
- `findAll()` - returns rows.
- `asArray()` - returns data as indexed or assoc array: `['field_name' => 'field_name']`. Should be used after ending method `find_all()`.

### Create database
```php
Lazer::create('table_name', array(
    'id' => 'integer',
    'nickname' => 'string',
    {field_name} => {field_type}
));
```
More informations about field types and usage in PHPDoc
	
### Remove database
```php
Lazer::remove('table_name');
```
### Select

#### Multiple select
```php
$table = Lazer::table('table_name')->findAll();
    
foreach($table as $row)
{
    print_r($row);
}
```
#### Single record select
```php
$row = Lazer::table('table_name')->find(1);

echo $row->id;
```
Type ID of row in `find()` method.

### Insert
```php
$row = Lazer::table('table_name');

$row->nickname = 'new_user';
$row->save();
```
Do not set the ID.

### Update

It's very smilar to `Inserting`.
```php
$row = Lazer::table('table_name')->find(1); //Edit row with ID 1

$row->nickname = 'edited_user';

$row->save();
```
### Remove

#### Single record deleting
```php
Lazer::table('table_name')->find(1)->delete(); //Will remove row with ID 1
```
#### Multiple records deleting
```php
Lazer::table('table_name')->where('nickname', '=', 'edited_user')->delete();
```
#### Clear table
```php
Lazer::table('table_name')->delete();
```
### Relations

To work with relations use class Relation
```php
use Lazer\Classes\Relation; // example
```

#### Relation types

- `belongsTo` - relation many to one
- `hasMany` - relation one to many
- `hasAndBelongsToMany` - relation many to many

#### Methods

##### Chain methods

- `belongsTo()` - set relation belongsTo
- `hasMany()` - set relation hasMany
- `hasAndBelongsToMany()` - set relation hasAndBelongsToMany
- `localKey()` - set relation local key
- `foreignKey()` - set relation foreign key
- `with()` - allow to work on existing relation

##### Ending methods

- `setRelation()` - creating specified relation
- `removeRelation()` - creating specified relation
- `getRelation()` - return informations about relation

#### Create relation
```php
Relation::table('table1')->belongsTo('table2')->localKey('table2_id')->foreignKey('id')->setRelation();
Relation::table('table2')->hasMany('table1')->localKey('id')->foreignKey('table2_id')->setRelation();
Relation::table('table2')->hasAndBelongsToMany('table1')->localKey('id')->foreignKey('id')->setRelation(); // Junction table will be crete automaticly
```

#### Remove relation
```php
Relation::table('table1')->with('table2')->removeRelation();
```
#### Get relation information
You can do it by two ways. Use Standard Database class or Relation but results will be different.
```php
Relation::table('table1')->with('table2')->getRelation(); // relation with specified table
Lazer::table('table1')->relations(); // all relations
Lazer::table('table1')->relations('table2'); // relation with specified table
```

Description
------
For some examples please check index.html file.
More informations you can find in PHPDoc, I think it's documented very well.

Homepage: <http://greg0.ovh.org>   
E-mail: <gerg0sz92@gmail.com>

If you like and using/want to use my repo or you have any suggestions I will be greatful for sending me few words on e-mail.