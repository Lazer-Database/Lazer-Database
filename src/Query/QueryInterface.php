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

    public function count();
    
    public function find($id);
    
    public function findAll();

    public function asArray($key = null, $value = null);
    
    public function delete();
}
