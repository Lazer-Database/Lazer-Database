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
    private $lastId = 1;
    private $columns = array();
    private $rows = array();
    private $relations = array();
    private $environment;
    private $tableFile;
    private $configFile;

    public function __construct($name, Environment $environment)
    {
        $this->name = strtolower($name);
        $this->environment = $environment;
        $this->tableFile = new File\JSON($this->name, $environment->getOptions('tablePath'));
        $this->configFile = new File\Serialize($this->name, $environment->getOptions('configPath'));
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getLastId()
    {
        return $this->lastId;
    }

    public function getRelations()
    {
        return $this->relations;
    }
    
    public function getTableFile()
    {
        return $this->tableFile;
    }
    
    public function getConfigFile()
    {
        return $this->configFile;
    }

    public function exists()
    {
        if ($this->tableFile->exists() && $this->configFile->exists())
        {
            return TRUE;
        }
        return FALSE;
    }

    public function read()
    {
        if ($this->exists())
        {
            $this->configFile->read();
            $config = $this->configFile->getContent();

            foreach ($config->fetch() as $property => $value)
            {
                $this->{$property} = $value;
            }
            return TRUE;
        }
        throw new Exception\RuntimeException('Table "' . $this->name . '" does not exists');
    }

    public function insert(Row $row)
    {
        $this->read();
        $this->lastId++;
        $writer = new Table\Writer($this);
        return $writer->insert($row);
    }

    public function update(Row $row)
    {
        $this->read();
        $writer = new Table\Writer($this);
        return $writer->update($row);
    }

    public function find($id)
    {
        $this->tableFile->read(true);
        $data = $this->tableFile->getContent();
        $row = new Row((array) $data[$id]);
        $validator = new Validator\Row($row, $this);
        return $validator->getPreparedToRead();
    }

    

    //TODO usunąć później
    public function findAll()
    {
        $this->tableFile->read();
        $records = $this->tableFile->getContent();
        $result = array();
        foreach ($records as $id => $value)
        {
            $row = new Row((array) $value);
            $validator = new Validator\Row($row, $this);
            $result[$id] = $validator->getPreparedToRead();
        }
        return $result;
    }

}
