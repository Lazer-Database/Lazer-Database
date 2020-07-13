<?php

declare(strict_types=1);

namespace Lazer\Classes\Helpers;

use Lazer\Classes\LazerException;

/**
 * File managing class
 *
 * @category Helpers
 * @author Grzegorz Kuźnik
 * @copyright (c) 2013, Grzegorz Kuźnik
 * @license http://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/Greg0/Lazer-Database GitHub Repository
 * 
 * @method array fields
 * @method array schema
 * @method object relations
 * @method int lastId
 */
class File implements FileInterface {

    /**
     * File name
     * @var string
     */
    protected $name;

    /**
     * File type (data|config)
     * @var string
     */
    protected $type;

    /**
     * 
     * @param string $name
     * @return File
     */
    public static function table(string $name): File
    {
        $file       = new File;
        $file->name = $name;

        return $file;
    }

    /**
     * @param string $type
     * @return void
     */
    public final function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * 
     * @return string
     * @throws LazerException
     */
    public final function getPath(): string
    {
        if (!defined('LAZER_DATA_PATH'))
        {
            throw new LazerException('Please define constant LAZER_DATA_PATH (check README.md)');
        }
        else if (!empty($this->type))
        {
            return LAZER_DATA_PATH . $this->name . '.' . $this->type . '.json';
        }
        else
        {
            throw new LazerException('Please specify the type of file in class: ' . __CLASS__);
        }
    }

    /**
     * @param bool $assoc
     * @return array|object
     * @throws LazerException
     */
    public final function get(bool $assoc = false)
    {
        return json_decode(file_get_contents($this->getPath()), $assoc);
    }

    /**
     * @param object|array $data
     * @return int|bool
     * @throws LazerException
     */
    public final function put($data)
    {
        return file_put_contents($this->getPath(), json_encode($data));
    }

    /**
     * @return bool
     * @throws LazerException
     */
    public final function exists(): bool
    {
        return file_exists($this->getPath());
    }

    /**
     * 
     * @return bool
     * @throws LazerException 
     */
    public final function remove(): bool
    {
        $type = ucfirst($this->type);
        if ($this->exists())
        {
            if (unlink($this->getPath()))
                return TRUE;

            throw new LazerException($type . ': Deleting failed');
        }

        throw new LazerException($type . ': File does not exists');
    }

}
