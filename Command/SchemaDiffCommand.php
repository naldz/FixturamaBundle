<?php

namespace Naldz\Bundle\FixturamaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;


class SchemaDiffCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fixturama:schema-diff')
            ->setDescription('Compares the fixture schema with the give ORM schema. Supports Propel ORM.')
            ->addArgument('orm-schema-paths', InputArgument::REQUIRED, 'The path of the ORM schema to compare.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $ormSchemaPathList = $input->getArgument('orm-schema-paths');
        $ormSchemaPaths = explode(' ', trim($ormSchemaPathList));

        $ormSchemaConverter = $this->getContainer()->get('fixturama.orm.converter');
        $schemaComparator = $this->getContainer()->get('fixturama.schema_comparator');


        $fs = new FileSystem();

        $rawOrmSchema = array();
        foreach ($ormSchemaPaths as $ormSchemaPath) {
            //check if the orm schema path exists
            if (!$fs->exists($ormSchemaPath)) {
                throw new IOException('ORM Schema file "%s" does not exist.');
            }

            $domDocument = new \DOMDocument();
            $domDocument->load($ormSchemaPath);
            $tempRawSchema = $ormSchemaConverter->convert($domDocument);

            $rawOrmSchema = array_merge($rawOrmSchema, $tempRawSchema);
        }

        $schemaDiff = $schemaComparator->compare($rawOrmSchema);
        $output->writeln(json_encode($schemaDiff, JSON_PRETTY_PRINT));

        // $output->writeln('');
        // if (count($schemaDiff['added_databases'])) {
        //     $output->writeln('New Databases: [');
        //     foreach ($schemaDiff['added_databases'] as $entity) {
        //         $output->writeln(' - '.$entity);
        //     }
        //     $output->writeln(']');
        //     $output->writeln('');
        // }

        // if (count($schemaDiff['removed_databases'])) {
        //     $output->writeln('Removed Databases: [');
        //     foreach ($schemaDiff['removed_databases'] as $entity) {
        //         $output->writeln(' - '.$entity);
        //     }
        //     $output->writeln(']');
        //     $output->writeln('');
        // }

        // if (count($schemaDiff['added_tables'])) {
        //     $output->writeln('New Tables: [');
        //     foreach ($schemaDiff['added_tables'] as $entity) {
        //         $output->writeln(' - '.$entity);
        //     }
        //     $output->writeln(']');
        //     $output->writeln('');
        // }

        // if (count($schemaDiff['removed_tables'])) {
        //     $output->writeln('Removed Tables: [');
        //     foreach ($schemaDiff['removed_tables'] as $entity) {
        //         $output->writeln(' - '.$entity);
        //     }
        //     $output->writeln(']');
        //     $output->writeln('');
        // }

        // if (count($schemaDiff['added_fields'])) {
        //     $output->writeln('New Table Fields: [');
        //     foreach ($schemaDiff['added_fields'] as $entity) {
        //         $output->writeln(' - '.$entity);
        //     }
        //     $output->writeln(']');
        //     $output->writeln('');
        // }

        // if (count($schemaDiff['removed_fields'])) {
        //     $output->writeln('Removed Table Fields: [');
        //     foreach ($schemaDiff['removed_fields'] as $entity) {
        //         $output->writeln(' - '.$entity);
        //     }
        //     $output->writeln(']');
        //     $output->writeln('');
        // }
    }
}