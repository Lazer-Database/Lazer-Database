<?php
namespace Lazer;

/**
 * Table
 *
 * @category Core
 * @author Grzegorz Kuźnik
 * @copyright (c) 2013, Grzegorz Kuźnik
 * @license http://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/Greg0/Lazer-Database GitHub Repository
 */
class Table {

    private $name;
    
    private $fields;
    
    private $relations;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }
    
    public function getFields()
    {
        return $this->fields;
    }
    
    
    
}
