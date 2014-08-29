<?php

namespace Lazer;

/**
 * Description of Field
 *
 * @author Grego
 */
class Column {

    private $name;
    private $dataType;

    public function __construct($name, $dataType)
    {
        $this->name = strtolower($name);

        if (DataType::isValid($dataType))
        {
            $this->dataType = $dataType;
        }
        else
        {
            throw new Exception\RuntimeException('Type of column is not valid (use Lazer\DataType enum)');
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

}
