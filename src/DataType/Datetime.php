<?php

namespace Lazer\DataType;

/**
 * Description of DateTime
 *
 * @author Grego
 */
class Datetime implements DataTypeInterface {

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate()
    {
        return $this->data instanceof \DateTime;
    }

    public function prepareToRead()
    {
        return new Datetime($this->data);
    }

    public function prepareToSave()
    {
        return $this->data->format('Y-m-d H:i:s');
    }

}
