<?php

namespace Lazer\DataType;

/**
 * Description of String
 *
 * @author Grego
 */
class String implements DataTypeInterface {

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate()
    {
        return is_string($this->data);
    }

    public function prepareToRead()
    {
        return (string) $this->data;
    }

    public function prepareToSave()
    {
        return (string) $this->data;
    }

}