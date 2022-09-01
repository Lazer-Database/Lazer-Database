<?php

declare(strict_types=1);

namespace Lazer\Classes\Helpers;

use Lazer\Classes\{LazerException,
    Relation};

/**
 * Validation for tables
 *
 * @category Helpers
 * @author Grzegorz Kuźnik
 * @copyright (c) 2013, Grzegorz Kuźnik
 * @license http://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/Greg0/Lazer-Database GitHub Repository
 */
class Validate {

    /**
     * Name of table
     * @var string
     */
    private $name;

    /**
     * Table name
     * @param string $name
     * @return Validate
     */
    public static function table(string $name): Validate
    {
        $validate       = new Validate();
        $validate->name = $name;
        return $validate;
    }

    /**
     * Checking that field type is numeric
     * @param string $type
     * @return bool
     */
    public static function isNumeric(string $type): bool
    {
        $defined = ['integer', 'double'];

        if (in_array($type, $defined))
        {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Checking that types from array matching with [bool, integer, string, double]
     * @param array $types Indexed array
     * @return bool
     * @throws LazerException
     */
    public static function types(array $types): bool
    {
        $defined = ['boolean', 'integer', 'string', 'double'];
        $diff    = array_diff($types, $defined);

        if (empty($diff))
        {
            return TRUE;
        }
        throw new LazerException('Wrong types: "' . implode(', ', $diff) . '". Available "boolean, integer, string, double"');
    }

    /**
     * Delete ID field from arrays
     * @param array $fields
     * @return array Fields without ID
     */
    public static function filter(array $fields): array
    {
        if (array_values($fields) === $fields)
        {
            if (($key = array_search('id', $fields)) !== false)
            {
                unset($fields[$key]);
            }
        }
        else
        {
            unset($fields['id']);
        }
        return $fields;
    }

    /**
     * Change keys and values case to lower
     * @param array $array
     * @return array
     */
    public static function arrToLower(array $array): array
    {
        $array = array_change_key_case($array);
        $array = array_map('strtolower', $array);

        return $array;
    }

    /**
     * Checking that typed fields really exist in table
     * @param array $fields Indexed array
     * @return bool
     * @throws LazerException If field(s) does not exist
     */
    public function fields(array $fields): bool
    {
        $fields = self::filter($fields);
        $diff   = array_diff($fields, Config::table($this->name)->fields());

        if (empty($diff))
        {
            return TRUE;
        }
        throw new LazerException('Field(s) "' . implode(', ', $diff) . '" does not exists in table "' . $this->name . '"');
    }

    /**
     * Checking that typed field really exist in table
     * @param string $name
     * @return bool
     * @throws LazerException If field does not exist
     */
    public function field(string $name): bool
    {
        if (in_array($name, Config::table($this->name)->fields()))
        {
            return TRUE;
        }
        throw new LazerException('Field ' . $name . ' does not exists in table "' . $this->name . '"');
    }

    /**
     * Checking that Table and Config exists and throw exceptions if not
     * @return bool
     * @throws LazerException
     */
    public function exists(): bool
    {
        if (!Data::table($this->name)->exists())
            throw new LazerException('Table "' . $this->name . '" does not exists');

        if (!Config::table($this->name)->exists())
            throw new LazerException('Config "' . $this->name . '" does not exists');

        return TRUE;
    }

    /**
     * Checking that typed field have correct type of value
     * @param string $name
     * @param mixed $value
     * @return bool
     * @throws LazerException If type is wrong
     */
    public function type(string $name, $value): bool
    {
        $schema = Config::table($this->name)->schema();
        if ((array_key_exists($name, $schema) && (null === $value || $schema[$name] == gettype($value))) || (self::isNumeric($schema[$name]) === self::isNumeric(gettype($value))))
        {
            return TRUE;
        }

        throw new LazerException('Wrong data type');
    }

    /**
     * Checking that relation between tables exists
     * @param string $local local table
     * @param string $foreign related table
     * @return bool relation exists
     * @throws LazerException
     */
    public static function relation(string $local, string $foreign): bool
    {
        $relations = Config::table($local)->relations();
        if (isset($relations->{$foreign}))
        {
            return TRUE;
        }

        throw new LazerException('Relation "' . $local . '" to "' . $foreign . '" doesn\'t exist');
    }

    /**
     * Checking that relation type is correct
     * @param string $type 
     * @return bool relation type
     * @throws LazerException Wrong relation type
     */
    public static function relationType(string $type): bool
    {
        if (in_array($type, Relation::relations()))
        {
            return true;
        }

        throw new LazerException('Wrong relation type');
    }

}
