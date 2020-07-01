<?php

declare(strict_types=1);

namespace Lazer\Classes\Helpers;

use Lazer\Classes\LazerException;

interface FileInterface {

    /**
     * Setting name of table
     * @param string $name
     * @return File
     */
    public static function table(string $name): File;

    /**
     * Set the file type
     * @param string $type File type (data|config)
     */
    public function setType(string $type);

    /**
     * Returning path to file
     * @return string Path to file
     * @throws LazerException You must specify the type of file
     */
    public function getPath(): string;

    /**
     * Return decoded JSON
     * @param bool $assoc Returns object if false; array if true
     * @return mixed (object|array)
     */
    public function get(bool $assoc = false);

    /**
     * Saving encoded JSON to file
     * @param object|array $data
     * @return int|bool
     */
    public function put($data);

    /**
     * Checking that file exists
     * @return bool
     */
    public function exists(): bool;

    /**
     * Removing file
     * @return bool
     * @throws LazerException If file doesn't exists or there's problems with deleting files
     */
    public function remove(): bool;
}
