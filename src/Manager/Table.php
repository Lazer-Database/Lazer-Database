<?php

namespace Lazer\Manager;

use Lazer\Exception;

/**
 * Description of Table
 *
 * @author Grego
 */
class Table {

    private $table;

    public function __construct(\Lazer\Table $table)
    {
        $this->table = $table;
    }

    public function create()
    {
        $columns = $this->table->getColumns();
        if (!$this->table->exists() && !empty($columns))
        {
            return $this->table->getTableFile()->create(array()) && $this->table->getConfigFile()->create(new \Lazer\Config($this->table));
        }
        throw new Exception\RuntimeException('Table already exists or columns are not set');
    }

    public function drop()
    {
        $this->table->getTableFile()->remove();
        $this->table->getConfigFile()->remove();
    }

    public function setColumns(array $columns)
    {
        $pendingColumns       = array();
        $pendingColumns['id'] = new \Lazer\Column('id', \Lazer\DataType::INTEGER);
        foreach ($columns as $key => $value)
        {
            if ($value instanceof \Lazer\Column)
            {
                $pendingColumns[$value->getName()] = $value;
            }
            else
            {
                $pendingColumns[strtolower($key)] = new \Lazer\Column($key, $value);
            }
        }
        $this->table->setColumns($pendingColumns);
    }

    public function dropColumn($columnName)
    {
        $tableColumns = $this->table->getColumns();
        if (isset($tableColumns[$columnName]))
        {
            unset($tableColumns[$columnName]);
            $this->setColumns($tableColumns);

            $tableFile  = $this->table->getTableFile();
            $configFile = $this->table->getConfigFile();

            $tableFile->read(TRUE);
            $records    = $tableFile->getContent();
            $newRecords = array();
            foreach ($records as $id => $row)
            {
                unset($row[$columnName]);
                $newRecords[$id] = $row;
            }

            return $tableFile->putContent($newRecords) && $configFile->putContent(new \Lazer\Config($this->table));
        }
        throw new Exception\RuntimeException('Column ' . $columnName . ' does not exist');
    }

    public function addColumn(\Lazer\Column $column)
    {
        $tableColumns = $this->table->getColumns();

        if (!isset($tableColumns[$column->getName()]))
        {
            $tableColumns[$column->getName()] = $column;
            $this->setColumns($tableColumns);

            $tableFile  = $this->table->getTableFile();
            $configFile = $this->table->getConfigFile();

            $tableFile->read(TRUE);
            $records    = $tableFile->getContent();
            $newRecords = array();
            foreach ($records as $id => $row)
            {
                $row[$column->getName()] = null;
                $newRecords[$id]         = $row;
            }

            return $tableFile->putContent($newRecords) && $configFile->putContent(new \Lazer\Config($this->table));
        }
        throw new Exception\RuntimeException('Column ' . $column->getName() . ' already exist');
    }

    public function saveChanges()
    {
        
    }

}
