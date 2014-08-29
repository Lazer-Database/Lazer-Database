<?php

namespace Lazer\Query;

/**
 * Description of Query
 *
 * @author Grego
 */
class Query implements QueryInterface \IteratorAggregate, \Countable {

    private $table;
    private $pending = array(
        'where' => array(),
        'orderBy' => array(),
        'limit' => array(),
        'with' => array(),
        'groupBy' => array(),
    );

    public function __construct(\Lazer\Table $table)
    {
        $this->table = $table;
    }

    public function limit($limit, $offset)
    {
        if (is_int($limit) && is_int($offset))
        {
            $this->pending['limit'] = array(
                'offset' => $offset,
                'number' => $limit
            );
        }

        return $this;
    }

    //todo czy kolumna istnieje
    public function orderBy($column, $direction = 'ASC')
    {
        $directions = array(
            'ASC' => SORT_ASC,
            'DESC' => SORT_DESC
        );
        $this->pending[__FUNCTION__][$column] = isset($directions[$direction]) ? $directions[$direction] : 'ASC';

        return $this;
    }

    public function groupBy($column);

    public function where($column, $operator, $value);

    public function orWhere($column, $operator, $value);

    public function join($tables);

    public function count();

    public function findAll();

    public function asArray($key = null, $value = null);

    public function delete();

    public function getIterator()
    {
        return new \ArrayIterator(array());
    }

}
