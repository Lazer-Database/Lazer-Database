<?php

namespace Lazer\File;

use Lazer\Exception;
/**
 * Description of JSON
 * 
 * @author Grego
 */
class JSON implements FileInterface {

    private $name;
    private $dir;
    private $path;
    private $assoc = false;
    private $content;

    public function __construct($name, $dir)
    {
        $this->name = $name;
        $this->dir = $dir;
        $this->path = $dir . $name . '.json';
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

    public function read($assoc = false)
    {
        if ($this->exists())
        {
            $this->assoc = $assoc;
            $this->content = json_decode(file_get_contents($this->path), $this->assoc);
        }
        else
        {
            throw new Exception\FileException('File "' . $this->path . '" does not exists');
        }
    }

    public function create($initialContent = array(), $options=0)
    {
        return $this->putContent($initialContent, $options);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function putContent($content, $options = 0)
    {
        return file_put_contents($this->path, json_encode($content, $options));
    }

    public function getKey($key)
    {
        if ($this->keyExists($key))
        {
            return $this->assoc ? $this->content[$key] : $this->content->$key;
        }
        else
        {
            throw new Exception\JSONException('Key "' . $key . '" does not exists in file (' . $this->path . ')');
        }
    }

    public function keyExists($key)
    {
        return $this->assoc ? isset($this->content[$key]) : isset($this->content->$key);
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
        if($this->exists())
        {
            return unlink($this->path);
        }
        
    }

}
