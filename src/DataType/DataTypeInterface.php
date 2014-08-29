<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lazer\DataType;

/**
 *
 * @author Grego
 */
interface DataTypeInterface {

    public function __construct($data);
    
    public function validate();
    
    public function prepareToRead();
    
    public function prepareToSave();
    
}
