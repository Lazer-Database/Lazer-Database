<?php

namespace Lazer;

/**
 * Description of Row
 *
 * @author Grego
 */
class Row implements \JsonSerializable {

    private $values = array();

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function __set($name, $value)
    {
        if (isset($this->values[$name]) || array_key_exists($name, $this->values))
        {
            if ($name != 'id')
            {
                $this->values[$name] = $value;
            }
            else
            {
                throw new Exception\RuntimeException('Can not change value of ID');
            }
        }
        else
        {
            throw new Exception\RuntimeException('Column ' . $name . ' does not exist');
        }
    }

    public function __get($name)
    {
        if (isset($this->values[$name]) || array_key_exists($name, $this->values))
        {
            return $this->values[$name];
        }
        else
        {
            throw new Exception\RuntimeException('Column ' . $name . ' does not exist');
        }
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getId()
    {
        return $this->values['id'];
    }

    public function setId($id)
    {
        $this->values = array_merge(array('id' => $id), $this->values);
    }

    public function jsonSerialize()
    {
        return $this->values;
    }

}
