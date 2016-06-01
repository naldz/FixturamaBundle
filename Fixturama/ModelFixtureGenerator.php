<?php

namespace Naldz\Bundle\FixturamaBundle\Fixturama;

use Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelFieldException;

class ModelFixtureGenerator
{
    private $faker = null;

    public function __construct($faker)
    {
        $this->faker = $faker;
    }

    public function generate($definition, $dataPresets = array())
    {
        //if a field in the dataPresets is unknown, throw an error
        $unknownFields = array_diff(array_keys($dataPresets), array_keys($definition['fields']));
        if (count($unknownFields)) {
            throw new UnknownModelFieldException(sprintf('Unknown FixtureModelField names: %s', implode(', ', $unknownFields)));
        }

        $fieldDefinitions = $definition['fields'];
        $data = array();

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            if (isset($dataPresets[$fieldName])) {
                $data[$fieldName] = $dataPresets[$fieldName];
            }
            else {
                $type = $fieldDefinition['type'];
                if (array_key_exists('params', $fieldDefinition)) {
                    $data[$fieldName] = call_user_func_array(array($this->faker, $type), $fieldDefinition['params']);
                }
                else {
                    $data[$fieldName] = $this->faker->$type;
                }
            }
        }

        return $data;
    }
}