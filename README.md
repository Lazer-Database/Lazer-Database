It's build version, many things may not work 
==============

In this set i'm working about manage relations between tables.

I won't update readme in that branch. 
There are builded files, mainly for me to easily write between 2 computers.

File Database based on JSON files
=============

PHP Library to use JSON files like a FlatFileDatabase.   
Functionality inspired by ORM's

Requirements
-------
- PHP 5.4+

Structure of table files
-------

`name.data.json` - table file with data   
`name.config.json` - file with configuration of table

    
Usage
------

First of all you should include `jsondb/bootstrap.php` file (Example autoloader) and define constant `JSONDB_DATA_PATH` containing absolute path to folder with JSON files:
```php
require_once 'jsondb/bootstrap.php';
define('JSONDB_DATA_PATH', realpath(dirname(__FILE__)).'/data/'); //Path to folder with tables
```
#### Assumptions

In that project I have used namespace but i will skip it in examples.

### Methods

##### Filter chain methods in multiple select

- `order_by()` - sort rows by key in order, can order by more than one field (just chain it). 
- `group_by()` - group rows by field.
- `where()` - filter records. Alias: `and_where()`.
- `or_where()` - other type of filtering results. 
- `limit()` - returns results between a certain number range. Should be used right before ending method `find_all()`.
- `as_array()` - returns data as indexed or assoc array: `['field_name' => 'field_name']`. Should be used right before ending method `find_all()` or `limit()` if setted.

##### Ending methods

- `add_fields()` - append new fields into existing table
- `delete_fields()` - removing fields from existing table
- `count()` - returns the number of rows.
- `find()` - returns one row with specified ID.
- `find_all()` - returns rows.
- `save()` - insert or Update data.
- `delete()` - deleting data.
- `config()` - returns object with configuration.
- `fields()` - returns array with fields name.
- `schema()` - returns assoc array with fields name and fields type `field => type`.
- `last_id()` - returns last ID from table.

### Create database
```php
JSONDB::create('table_name', array(
    'id' => 'integer',
    'nickname' => 'string',
    {field_name} => {field_type}
));
```
More informations about field types and usage in PHPDoc
	
### Remove database
```php
JSONDB::remove('table_name');
```
### Select

#### Multiple select
```php
$table = JSONDB::factory('table_name')->find_all();
    
foreach($table as $row)
{
    echo $row->id;
}
```
#### Single record select
```php
$row = JSONDB::factory('table_name')->find(1);

echo $row->id;
```
Type ID of row in `find()` method.

### Insert
```php
$row = JSONDB::factory('table_name');

$row->nickname = 'new_user';
$row->save();
```
Don't set the ID.

### Update

It's very smilar to `Inserting`.
```php
$row = JSONDB::factory('table_name')->find(1); //Will edit row with ID 1

$row->nickname = 'edited_user';

$row->save();
```
### Remove

#### Single record deleting
```php
JSONDB::factory('table_name')->find(1)->delete(); //Will remove row with ID 1
```
#### Multiple records deleting
```php
JSONDB::factory('table_name')->where('nickname', '=', 'edited_user')->delete();
```
#### Clear table
```php
JSONDB::factory('table_name')->delete();
```
Description
------

More informations you can find in PHPDoc. I think it's documented very well.

Homepage: <http://greg0.ovh.org>   
E-mail: <gerg0sz92@gmail.com>

If you like and using/want to use my repo or you have any suggestions I will be greatful for sending me few words on e-mail.