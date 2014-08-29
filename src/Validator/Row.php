<?php

namespace Lazer\Validator;

use Lazer\Exception;

/**
 * Description of Row
 *
 * @author Grego
 */
class Row implements ValidatorInterface {

    private $row;
    private $rowValues;
    private $tableColumns;
    private $valid = FALSE;

    public function __construct(\Lazer\Row $row, \Lazer\Table $table)
    {
        $this->row = $row;
        $this->rowValues = $row->getValues();
        $this->tableColumns = $table->getColumns();
    }

    public function validate()
    {
        $this->valid = $this->validateColumns() && $this->validateDataType();
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function getPreparedToSave()
    {
        if ($this->isValid())
        {
            foreach ($this->rowValues as $column => $value)
            {
                $dataTypeClass = '\Lazer\DataType\\' . $this->tableColumns[$column]->getDataType();
                $data = new $dataTypeClass($value);
                $this->rowValues[$column] = $data->prepareToSave();
            }
            return $this->rowValues;
        }
    }

    public function getPreparedToRead()
    {
        foreach ($this->rowValues as $column => $value)
        {
            if ($column != 'id')
            {
                $dataTypeClass = '\Lazer\DataType\\' . $this->tableColumns[$column]->getDataType();
                $data = new $dataTypeClass($value);
                $this->row->{$column} = $data->prepareToRead();
            }
        }
        return $this->row;
    }

    private function validateColumns()
    {
        $wrong = array_diff(array_keys($this->rowValues), array_keys($this->tableColumns));
        $miss = array_diff(array_keys($this->tableColumns), array_keys($this->rowValues));

        if (!empty($wrong))
        {
            throw new Exception\RuntimeException('Column(s) [' . implode(', ', $wrong) . '] do not exists');
        }
        else if (!empty($miss))
        {
            throw new Exception\RuntimeException('Column(s) [' . implode(', ', $miss) . '] are missing');
        }

        return TRUE;
    }

    private function validateDataType()
    {
        foreach ($this->rowValues as $column => $value)
        {
            $dataType = (gettype($value) == 'object') ? strtolower(get_class($value)) : gettype($value);
            if ($this->tableColumns[$column]->getDataType() != $dataType)
            {
                throw new Exception\RuntimeException('Wrong data type in column "' . $column . '", ' . $this->tableColumns[$column]->getDataType() . ' excepted');
            }
        }

        return TRUE;
    }

}
