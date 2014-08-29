<?php

namespace Lazer\File;

/**
 * Description of Object
 *
 * @author Grego
 */
class Serialize implements FileInterface {

    private $name;
    private $dir;
    private $path;
    private $content;

    public function __construct($name, $dir)
    {
        $this->name = $name;
        $this->dir = $dir;
        $this->path = $dir . $name . '.pser';
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
            $this->content = unserialize(file_get_contents($this->path));
        }
        else
        {
            throw new Exception\FileException('File "' . $this->path . '" does not exists');
        }
    }

    public function create($initialContent = array())
    {
        return $this->putContent($initialContent);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function putContent($content)
    {
        return file_put_contents($this->path, serialize($content));
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
