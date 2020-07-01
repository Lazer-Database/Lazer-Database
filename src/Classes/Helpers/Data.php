<?php

declare(strict_types=1);

namespace Lazer\Classes\Helpers;

/**
 * Data managing class
 *
 * @category Helpers
 * @author Grzegorz Kuźnik
 * @copyright (c) 2013, Grzegorz Kuźnik
 * @license http://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/Greg0/Lazer-Database GitHub Repository
 */
class Data extends File {

    /**
     * Get file of the table
     * @param string $name
     * @return File
     */
    public static function table(string $name): File
    {
        $file       = new Data;
        $file->name = $name;
        $file->setType('data');

        return $file;
    }

}
