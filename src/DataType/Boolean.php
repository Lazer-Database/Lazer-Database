<?php

namespace Lazer\DataType;

/**
 * Description of Boolean
 *
 * @author Grego
 */
class Boolean implements DataTypeInterface {

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate()
    {
        return is_bool($this->data);
    }

    public function prepareToRead()
    {
        return (bool) $this->data;
    }

    public function prepareToSave()
    {
        return (bool) $this->data;
    }

}
