<?php

namespace Lazer\File;

use Lazer\Exception;

/**
 * Description of File
 *
 * @author Grego
 */
class FileManager implements FileInterface{
    
    private $name;
    private $dir;
    private $path;
    private $content;

    public function __construct($name, $dir)
    {
        $this->name = $name;
        $this->dir = $dir;
        $this->path = $dir . $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDir()
    {
        return $this->dir;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function read()
    {
        if ($this->exists())
        {
            $this->content = file_get_contents($this->path);
        }
        else
        {
            throw new Exception\FileException('File "' . $this->path . '" does not exists');
        }
    }

    public function create($initialContent=null)
    {
        return $this->putContent($initialContent);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function putContent($content)
    {
        return file_put_contents($this->path, $content);
    }

    public function exists()
    {
        if (file_exists($this->path))
        {
            return TRUE;
        }
        return FALSE;
    }

    public function remove()
    {
        return unlink($this->path);
    }
    
}
