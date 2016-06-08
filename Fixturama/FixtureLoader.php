<?php

namespace Naldz\Bundle\FixturamaBundle\Fixturama;

use Naldz\Bundle\FixturamaBundle\Fixturama\ModelFixtureGenerator;
use Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelException;
use Naldz\Bundle\FixturamaBundle\Fixturama\SqlConverter;
use Naldz\Bundle\FixturamaBundle\Fixturama\Schema\SchemaDefinition;

class FixtureLoader
{
    private $definition = null;
    private $sqlConverter = null;
    private $pdo = null;

    public function __construct(SchemaDefinition $definition, SqlConverter $sqlConverter, \PDO $pdo)
    {
        $this->definition = $definition;
        $this->sqlConverter = $sqlConverter;
        $this->pdo = $pdo;
    }

    public function load($fixtureData)
    {
        //if a model in the data presets is unknown, throw an exception
        $unknownModels = array_diff(array_keys($fixtureData), $this->definition->getModelNames());
        if (count($unknownModels)) {
            throw new UnknownModelException(sprintf('Unknown FixtureModel names: %s', implode(', ', $unknownModels)));
        }

        //construct the options
        $sqls = array(
            'SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;',
            'SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;',
            'SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'TRADITIONAL,ALLOW_INVALID_DATES\';'
        );

        foreach ($fixtureData as $modelName => $modelFixtureDataset) {
            $sqls[] = $this->sqlConverter->convert($modelName, $modelFixtureDataset);
        }

        $sqls[] = 'SET SQL_MODE=@OLD_SQL_MODE;';
        $sqls[] = 'SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;';
        $sqls[] = 'SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;';

        $fullSql = implode("\n", $sqls);
        $this->pdo->exec($fullSql);
    }
}