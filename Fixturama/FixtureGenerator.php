<?php

namespace Naldz\Bundle\FixturamaBundle\Fixturama;

use Naldz\Bundle\FixturamaBundle\Fixturama\ModelFixtureGenerator;
use Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelException;

class FixtureGenerator
{
    private $definition = null;
    private $modelFixtureGenerator = null;

    public function __construct($definition, $modelFixtureGenerator)
    {
        $this->definition = $definition;
        $this->modelFixtureGenerator = $modelFixtureGenerator;
    }

    public function generate(Array $dataPresets)
    {
        //if a model in the data presets is unknown, throw an exception
        $unknownModels = array_diff(array_keys($dataPresets), array_keys($this->definition['models']));
        if (count($unknownModels)) {
            throw new UnknownModelException(sprintf('Unknown FixtureModel names: %s', implode(', ', $unknownModels)));
        }

        $fixtureData = array();
        foreach ($dataPresets as $modelName => $modelPresetDataCollection) {
            $modelDefinition = $this->definition['models'][$modelName];
            $fixtureData[$modelName] = array();
            foreach ($modelPresetDataCollection as $modelPresetData) {
                $fixtureData[$modelName][] = $this->modelFixtureGenerator->generate($modelDefinition, $modelPresetData);
            }
        }

        return $fixtureData;
    }
}