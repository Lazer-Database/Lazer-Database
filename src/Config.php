<?php

namespace Lazer;

/**
 * Description of Config
 *
 * @author Grego
 */
class Config  {

    private $lastId;
    private $columns = array();
    private $relations = array();

    public function __construct(Table $table)
    {
        $this->columns = $table->getColumns();
        $this->lastId = $table->getLastId();
        $this->relations = $table->getRelations();
    }

    public function fetch()
    {
        return get_object_vars($this);
    }
}
