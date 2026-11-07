<?php

declare(strict_types=1);

namespace Lazer\Classes;

use Lazer\Classes\Helpers\{Validate,
    Config};

/**
 * Relation class of LAZER project.
 *
 * @category Core
 * @author Grzegorz Kuźnik
 * @copyright (c) 2013, Grzegorz Kuźnik
 * @license http://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/Greg0/Lazer-Database GitHub Repository
 */
class Relation {

    /**
     * Tables names
     * @var array tables
     */
    protected $tables = [
        'local'   => null,
        'foreign' => null
    ];

    /**
     * Relation keys names
     * @var array keys
     */
    protected $keys = [
        'local'   => null,
        'foreign' => null
    ];

    /**
     * Current relation type
     * @var string
     */
    protected $relationType;

    /**
     * All relations types
     * @var array
     */
    protected static $relations = ['belongsTo', 'hasMany', 'hasAndBelongsToMany'];

    /**
     * Factory method
     * @param string $name Name of table
     * @return self
     */
    public static function table($name)
    {
        Validate::table($name)->exists();

        $self                  = new self();
        $self->tables['local'] = $name;

        return $self;
    }

    /**
     * Getter of junction table name in many2many relation
     * @return boolean|string Name of junction table or false
     */
    public function getJunction()
    {
        if ($this->relationType == 'hasAndBelongsToMany')
        {
            $tables = $this->tables;
            sort($tables);
            return implode('_', $tables);
        }
        return false;
    }

    /**
     * Set relation type to field
     * @param string $relation Name of relation
     */
    protected function setRelationType($relation)
    {
        Validate::relationType($relation);
        $this->relationType = $relation;
    }

    /**
     * Set table name
     * @param string $type local or foreign
     * @param string $name table name
     */
    protected function setTable($type, $name)
    {
        Validate::table($name)->exists();
        $this->tables[$type] = $name;
    }

    /**
     * Set key name
     * @param string $type local or foreign
     * @param string $key key name
     * @return self
     * @throws LazerException First you must define tables name
     */
    protected function setKey($type, $key)
    {
        if (!in_array(null, $this->tables))
        {
            Validate::table($this->tables[$type])->field($key);

            $this->keys[$type] = $key;
            return $this;
        }

        throw new LazerException('First you must define tables name');
    }

    /**
     * Set local key name
     * @param string $key key name
     * @return self
     * @throws LazerException First you must define tables name
     */
    public function localKey($key)
    {
        return $this->setKey('local', $key);
    }

    /**
     * Set foreign key name
     * @param string $key key name
     * @return self
     * @throws LazerException First you must define tables name
     */
    public function foreignKey($key)
    {
        return $this->setKey('foreign', $key);
    }

    /**
     * Set relation one2many to table
     * @param string $table Table name
     * @return self
     */
    public function belongsTo($table)
    {
        $this->setTable('foreign', $table);
        $this->setRelationType(__FUNCTION__);

        return $this;
    }

    /**
     * Set relation many2one to table
     * @param string $table Table name
     * @return self
     */
    public function hasMany($table)
    {
        $this->setTable('foreign', $table);
        $this->setRelationType(__FUNCTION__);

        return $this;
    }

    /**
     * Set relation many2many to table
     * @param string $table Table name
     * @return self
     */
    public function hasAndBelongsToMany($table)
    {
        $this->setTable('foreign', $table);
        $this->setRelationType(__FUNCTION__);

        return $this;
    }

    /**
     * Use relation to table
     * @param string $table Table name
     * @return self
     */
    public function with($table)
    {
        Validate::relation($this->tables['local'], $table);
        $this->setTable('foreign', $table);
        $this->setRelationType(Config::table($this->tables['local'])->relations($this->tables['foreign'])->type);
        $this->setKey('local', Config::table($this->tables['local'])->relations($this->tables['foreign'])->keys->local);
        $this->setKey('foreign', Config::table($this->tables['local'])->relations($this->tables['foreign'])->keys->foreign);

        return $this;
    }

    /**
     * Set specified relation
     * @throws LazerException Tables names or keys missing
     */
    public function setRelation()
    {
        if (!in_array(null, $this->tables) && !in_array(null, $this->keys))
        {
            $this->addRelation();
            return true;
        }
        else
        {
            throw new LazerException('Tables names or keys missing');
        }
    }

    /**
     * Get relation information
     * @return array relation information
     */
    public function getRelation()
    {
        return [
            'tables' => $this->tables,
            'keys'   => $this->keys,
            'type'   => $this->relationType
        ];
    }

    /**
     * Remove relation
     */
    public function removeRelation()
    {
        if ($this->relationType == 'hasAndBelongsToMany')
        {
            $junction = $this->getJunction();

            $this->deleteRelationData($junction, $this->tables['local']);
            $this->deleteRelationData($junction, $this->tables['foreign']);
        }
        $this->deleteRelationData($this->tables['local'], $this->tables['foreign']);
    }

    /**
     * Add data to configs and create all necessary files
     */
    protected function addRelation()
    {
        if ($this->relationType == 'hasAndBelongsToMany')
        {
            $junction = $this->getJunction();

            try
            {
                Validate::table($junction)->exists();
            }
            catch (LazerException $e)
            {
                Database::create($junction, [
                    $this->tables['local'] . '_id'   => 'integer',
                    $this->tables['foreign'] . '_id' => 'integer',
                ]);

                $this->insertRelationData($junction, $this->tables['local'], 'hasMany', [
                    'local'   => $this->tables['local'] . '_id',
                    'foreign' => $this->keys['local']
                ]);

                $this->insertRelationData($junction, $this->tables['foreign'], 'hasMany', [
                    'local'   => $this->tables['foreign'] . '_id',
                    'foreign' => $this->keys['foreign']
                ]);
            }
        }
        $this->insertRelationData($this->tables['local'], $this->tables['foreign'], $this->relationType, $this->keys);
    }

    /**
     * Inserts relation data to config file
     * @param string $from Local table
     * @param string $to Related table
     * @param string $type Relation type
     * @param array $keys Relationed keys
     */
    protected function insertRelationData($from, $to, $type, array $keys)
    {
        $config                    = Config::table($from);
        $content                   = $config->get();
        $content->relations->{$to} = [
            'type' => $type,
            'keys' => $keys,
        ];
        $config->put($content);
    }

    /**
     * Inserts relation data to config file
     * @param string $from Local table
     * @param string $to Related table
     */
    protected function deleteRelationData($from, $to)
    {
        $config  = Config::table($from);
        $content = $config->get();
        unset($content->relations->{$to});
        $config->put($content);
    }

    /**
     *
     * @param array  $array
     * @param string $part
     * @return array
     */
    public function build(array $array, $part)
    {
        $name = ucfirst($part);

        if ($this->relationType == 'hasAndBelongsToMany')
        {
            return $this->buildManyToMany($array, $name);
        }

        // Group the parent rows that still need the relation by their local key value
        $grouped      = [];
        $uniqueValues = [];

        foreach ($array as $row)
        {
            if (!($row instanceof \stdClass) || isset($row->{$name}))
            {
                continue;
            }

            $value                = $row->{$this->keys['local']} ?? null;
            $grouped[$value][]    = $row;
            $uniqueValues[$value] = true;
        }

        // Fetch every related row with a single query instead of one per parent (N+1)
        if (!empty($uniqueValues))
        {
            $foreign = Database::table($this->tables['foreign'])
                ->where($this->keys['foreign'], 'IN', array_keys($uniqueValues))
                ->findAll();

            $index = [];
            foreach ($foreign as $foreignRow)
            {
                $index[$foreignRow->{$this->keys['foreign']}][] = $foreignRow;
            }

            foreach ($grouped as $value => $rows)
            {
                $matches = $index[$value] ?? [];

                foreach ($rows as $row)
                {
                    $row->{$name} = ($this->relationType == 'belongsTo')
                        ? ($matches[0] ?? null)
                        : $foreign->withRows($matches);
                }
            }
        }

        return $this->collectRelated($array, $name);
    }

    /**
     * Eagerly attach a many-to-many relation to every parent row.
     *
     * The junction table is read once for all parents; the related rows stay as a
     * pending query (evaluated with findAll()) so where()/limit() can still be chained.
     *
     * @param array  $array
     * @param string $name Property name to attach
     * @return array Related rows for chaining
     */
    protected function buildManyToMany(array $array, string $name)
    {
        $localId   = $this->tables['local'] . '_id';
        $foreignId = $this->tables['foreign'] . '_id';

        $uniqueValues = [];
        foreach ($array as $row)
        {
            if ($row instanceof \stdClass && !isset($row->{$name}))
            {
                $uniqueValues[$row->{$this->keys['local']} ?? null] = true;
            }
        }

        // One query on the junction table for every parent row
        $junctionIds = [];
        if (!empty($uniqueValues))
        {
            $junction = Database::table($this->getJunction())
                ->where($localId, 'IN', array_keys($uniqueValues))
                ->findAll();

            foreach ($junction as $junctionRow)
            {
                $junctionIds[$junctionRow->{$localId}][] = $junctionRow->{$foreignId};
            }
        }

        $foreign = Database::table($this->tables['foreign']);

        foreach ($array as $row)
        {
            if ($row instanceof \stdClass && !isset($row->{$name}))
            {
                $ids = $junctionIds[$row->{$this->keys['local']} ?? null] ?? [];

                // Keep the query pending so the caller can still refine it before findAll()
                $row->{$name} = (clone $foreign)->where($this->keys['foreign'], 'IN', $ids);
            }
        }

        return $this->collectRelated($array, $name);
    }

    /**
     * Flatten the related rows attached to every parent row.
     *
     * The result feeds the next hop of a chained with() call.
     *
     * @param array  $array
     * @param string $name Property holding the related data
     * @return array
     */
    protected function collectRelated(array $array, string $name)
    {
        $return = [];

        foreach ($array as $row)
        {
            if ($row instanceof \stdClass)
            {
                $related = $row->{$name} ?? null;

                if ($related instanceof Database)
                {
                    if ($this->relationType != 'hasAndBelongsToMany')
                    {
                        foreach ($related as $relatedRow)
                        {
                            $return[] = $relatedRow;
                        }
                    }
                }
                elseif ($related !== null)
                {
                    $return[] = $related;
                }
            }
            elseif ($row instanceof Database)
            {
                if ($this->relationType != 'hasAndBelongsToMany')
                {
                    foreach ($row as $relatedRow)
                    {
                        $return[] = $relatedRow;
                    }
                }
            }
            else
            {
                $return = array_merge($return, $this->build($row, $name));
            }
        }

        return $return;
    }

    /**
     * Get relations types
     * @return array
     */
    public static function relations()
    {
        return self::$relations;
    }

}
