<?php

namespace Lazer\File;

/**
 *
 * @author Grego
 */
interface FileInterface {
    
    public function __construct($name, $dir);

    public function getName();

    public function getDir();

    public function getPath();

    public function read();

    public function create($initialContent);

    public function getContent();

    public function putContent($content);

    public function exists();

    public function remove();
}
