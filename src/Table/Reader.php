<?php

namespace Lazer\Table;

/**
 * Description of Reader
 *
 * @author Grego
 */
class Reader {

    private $table;

    public function __construct(\Lazer\Table $table)
    {
        $this->table = $table;
    }

}
