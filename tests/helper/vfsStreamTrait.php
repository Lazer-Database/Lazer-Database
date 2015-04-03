<?php
namespace helper;
use org\bovigo\vfs\vfsStream;

trait vfsStreamTrait {

    protected $root;

    protected function setUpFilesystem()
    {
        $structure  = array(
            'users.data.json'   => '[{"id":2,"name":"Kriss","email":"kriss@example.com"},{"id":3,"name":"Larry","email":"larry@example.com"},{"id":4,"name":"Paul","email":"paul@example.com"}]',
            'users.config.json' => '{"last_id":4,"schema":{"id":"integer","name":"string","email":"string"},"relations":{"news":{"type":"hasMany","keys":{"local":"id","foreign":"author_id"}},"comments":{"type":"hasMany","keys":{"local":"id","foreign":"author_id"}}}}',
        );
        $this->root = vfsStream::setup('data', 777, $structure);
    }

}
