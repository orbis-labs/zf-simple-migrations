<?php

namespace ZfSimpleMigrations\Library;

use Interop\Container\ContainerInterface;
use RuntimeException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use ZfSimpleMigrations\Model\MigrationVersionTable;

class MigrationAbstractFactory implements AbstractFactoryInterface
{
    const FACTORY_PATTERN = '/migrations\.migration\.(.*)/';

    /**
     * @return Migration
     */
    public function __invoke(ContainerInterface $container, $name, array $options = [])
    {
        $config = $container->get('Config');

        preg_match(self::FACTORY_PATTERN, $name, $matches);

        $name = $matches[1];

        if (! isset($config['migrations'][$name])) {
            throw new RuntimeException(sprintf("`%s` does not exist in migrations configuration", $name));
        }

        $migration_config = $config['migrations'][$name];

        $adapter_name = isset($migration_config['adapter'])
            ? $migration_config['adapter'] : 'Zend\Db\Adapter\Adapter';
        /** @var $adapter \Zend\Db\Adapter\Adapter */
        $adapter = $container->get($adapter_name);


        $output = null;
        if (isset($migration_config['show_log']) && $migration_config['show_log']) {
            $console = $container->get('console');
            $output = new OutputWriter(function ($message) use ($console) {
                $console->write($message . "\n");
            });
        }

        /** @var MigrationVersionTable $version_table */
        $version_table = $container->get('migrations.versiontable.' . $adapter_name);
        $migration = new Migration($adapter, $migration_config, $version_table, $output);

        return $migration;
    }


    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return (bool) preg_match(self::FACTORY_PATTERN, $requestedName);
    }
}
