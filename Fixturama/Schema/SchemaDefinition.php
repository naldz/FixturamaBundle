<?php

namespace Naldz\Bundle\FixturamaBundle\Fixturama\Schema;

use Naldz\Bundle\FixturamaBundle\Fixturama\ModelFixtureGenerator;
use Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelException;
use Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelFieldException;

class SchemaDefinition
{
    private $rawDefinition = null;

    public function __construct($rawDefinition)
    {
        $this->rawDefinition = $rawDefinition;
    }

    public function getModelDefinition($modelName)
    {
        //var_dump($this->rawDefinition);
        if (!isset($this->rawDefinition['models'][$modelName])) {
            throw new UnknownModelException(sprintf('Unknown model: %s', $modelName));
        }

        return $this->rawDefinition['models'][$modelName];
    }

    public function getModelFieldDefinition($modelName, $fieldName)
    {
        $modelDefinition = $this->getModelDefinition($modelName);

        if (!isset($modelDefinition['fields'][$fieldName])) {
            throw new UnknownModelFieldException(sprintf('Unknown model field: %s', $fieldName));
        }

        return $modelDefinition['fields'][$fieldName];
    }

    public function getModelNames()
    {
        return array_keys($this->rawDefinition['models']);
    }
}