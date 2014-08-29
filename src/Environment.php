<?php

namespace Lazer;

/**
 * Description of Environment
 *
 * @author Grego
 */
class Environment {

    private $name;
    private $options = array(
        'tablePath' => './LazerDb/table/',
        'configPath' => './LazerDb/config/',
        'debug' => true,
    );

    public function __construct($name = 'default', array $options = array())
    {
        $this->name = $name;

        if ($this->environmentExists())
        {
            $options = array_intersect_key($options + $this->getEnvironment(), $this->getEnvironment());
        }

        $this->options = array_intersect_key($options + $this->options, $this->options);
        $_ENV['lazer'] = $this->getEnvironment();
    }

    private function getEnvironments($assoc = false)
    {
        $json = new File\JSON('environment', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);
        $json->read($assoc);
        return $json;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getOptions($option=null)
    {
        if(is_null($option))
        {
            return $this->options;
        }
        
        return $this->options[$option];
    }

    public function getEnvironment()
    {
        return $this->getEnvironments(true)->getKey($this->name);
    }

    public function environmentExists()
    {
        if ($this->getEnvironments()->keyExists($this->name))
        {
            return TRUE;
        }
        return FALSE;
    }

    public function save()
    {
        $getEnvironments = $this->getEnvironments();
        $content = $getEnvironments->getContent();
        $content->{$this->name} = $this->options;
        return $getEnvironments->putContent($content, JSON_UNESCAPED_SLASHES);
    }

    public function remove()
    {
        $getEnvironments = $this->getEnvironments();
        $content = $getEnvironments->getContent();
        unset($content->{$this->name});
        return $getEnvironments->putContent($content, JSON_UNESCAPED_SLASHES);
    }

    public function loadTable($name)
    {
        return new Table($name, $this);
    }

}
