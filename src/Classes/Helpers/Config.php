<?php

declare(strict_types=1);

namespace Lazer\Classes\Helpers;

use Lazer\Classes\LazerException;

/**
 * Config managing class
 *
 * @category Helpers
 * @author Grzegorz Kuźnik
 * @copyright (c) 2013, Grzegorz Kuźnik
 * @license http://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/Greg0/Lazer-Database GitHub Repository
 */
class Config extends File {

    /**
     * Get key from returned config
     * @param string $field 
     * @param bool $assoc 
     * @return mixed 
     * @throws LazerException 
     */
    public function getKey(string $field, bool $assoc = false)
    {
        return $assoc ? $this->get($assoc)[$field] : $this->get($assoc)->{$field};
    }

    /**
     * Get file of the table
     * @param string $name
     * @return File
     */
    public static function table(string $name): File
    {
        $file       = new Config;
        $file->name = $name;
        $file->setType('config');

        return $file;
    }

    /**
     * Return array with names of fields
     * @return array
     * @throws LazerException
     */
    public function fields(): array
    {
        return array_keys($this->getKey('schema', true));
    }

    /**
     * Return relations configure
     * @param string|array|null $tableName null-all tables;array-few tables;string-one table relation informations
     * @param bool $assoc Object or associative array
     * @return mixed
     * @throws LazerException
     */
    public function relations($tableName = null, bool $assoc = false)
    {
        if (is_array($tableName))
        {
            $relations = $this->getKey('relations', $assoc);
            if ($assoc)
            {
                return array_intersect_key($relations, array_flip($tableName));
            }
            else
            {
                return (object) array_intersect_key((array) $relations, array_flip($tableName));
            }
        }
        elseif ($tableName !== null)
        {
            return $assoc ? $this->getKey('relations', $assoc)[$tableName] : $this->getKey('relations', $assoc)->{$tableName};
        }

        return $this->getKey('relations', $assoc);
    }

    /**
     * Returning assoc array with types of fields
     * @return array
     * @throws LazerException
     */
    public function schema(): array
    {
        return $this->getKey('schema', true);
    }

    /**
     * Returning last ID from table
     * @return int
     * @throws LazerException 
     */
    public function lastId(): int
    {
        return $this->getKey('last_id');
    }

}
