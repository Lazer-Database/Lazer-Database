<?php

namespace Lazer\DataType;

/**
 * Description of Integer
 *
 * @author Grego
 */
class Integer implements DataTypeInterface {

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate()
    {
        return is_int($this->data);
    }

    public function prepareToRead()
    {
        return (int) $this->data;
    }

    public function prepareToSave()
    {
        return (int) $this->data;
    }

}