<?php

namespace Lazer\Table;

/**
 * Description of Writer
 *
 * @author Grego
 */
class Writer {

    private $table;

    public function __construct(\Lazer\Table $table)
    {
        $this->table = $table;
    }

    public function insert(\Lazer\Row $row)
    {
        $id = $this->table->getLastId() - 1;
        $row->setId($id);

        $validator = new \Lazer\Validator\Row($row, $this->table);
        $validator->validate();
        if ($validator->isValid())
        {
            $this->table->getTableFile()->read(true);
            $rows = $this->table->getTableFile()->getContent();
            $rows[$id] = $validator->getPreparedToSave();

            return $this->table->getTableFile()->putContent($rows) && $this->table->getConfigFile()->putContent(new \Lazer\Config($this->table));
        }

        return FALSE;
    }

    public function update(\Lazer\Row $row)
    {
        $id = $row->getId();
        $validator = new \Lazer\Validator\Row($row, $this->table);
        $validator->validate();
        if ($validator->isValid())
        {
            $this->table->getTableFile()->read(true);
            $rows = $this->table->getTableFile()->getContent();
            $rows[$id] = $validator->getPreparedToSave();

            return $this->table->getTableFile()->putContent($rows) && $this->table->getConfigFile()->putContent(new \Lazer\Config($this->table));
        }

        return FALSE;
    }

}
