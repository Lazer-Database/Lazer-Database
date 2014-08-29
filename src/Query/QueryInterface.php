<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lazer\Query;

/**
 *
 * @author Grego
 */
interface QueryInterface {

    public function __construct(\Lazer\Table $table);

    public function limit($limit, $offset);

    public function orderBy($column, $direction);
    
    public function groupBy($column);

    public function where($column, $operator, $value);

    public function orWhere($column, $operator, $value);
    
    public function join($tables);

    public function count();
    
    public function findAll();

    public function asArray($key = null, $value = null);
    
    public function delete();
}
