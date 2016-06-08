<?php

namespace Naldz\Bundle\FixturamaBundle\Fixturama;

use Naldz\Bundle\FixturamaBundle\Fixturama\ModelFixtureGenerator;
use Naldz\Bundle\FixturamaBundle\Fixturama\Schema\SchemaDefinition;
use Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelException;

class FixtureGenerator
{
    private $schemaDefinition = null;
    private $modelFixtureGenerator = null;

    public function __construct(SchemaDefinition $schemaDefinition, $modelFixtureGenerator)
    {
        $this->schemaDefinition = $schemaDefinition;
        $this->modelFixtureGenerator = $modelFixtureGenerator;
    }

    public function generate(Array $dataPresets)
    {
        $fixtureData = array();
        foreach ($dataPresets as $modelName => $modelPresetDataCollection) {
            $rawModelDefinition = $this->schemaDefinition->getModelDefinition($modelName);
            $fixtureData[$modelName] = array();
            foreach ($modelPresetDataCollection as $modelPresetData) {
                $fixtureData[$modelName][] = $this->modelFixtureGenerator->generate($rawModelDefinition, $modelPresetData);
            }
        }

        return $fixtureData;
    }
}