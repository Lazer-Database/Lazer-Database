<?php

namespace Lazer\DataType;

/**
 * Description of Double
 *
 * @author Grego
 */
class Double implements DataTypeInterface {

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate()
    {
        return is_double($this->data);
    }

    public function prepareToRead()
    {
        return (double) $this->data;
    }

    public function prepareToSave()
    {
        return (double) $this->data;
    }

}