<?php

namespace ZfSimpleMigrations\Library;

use Interop\Container\ContainerInterface;
use RuntimeException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class MigrationSkeletonGeneratorAbstractFactory implements AbstractFactoryInterface
{
    const FACTORY_PATTERN = '/migrations\.skeletongenerator\.(.*)/';

    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $name)
    {
        return (bool) preg_match(self::FACTORY_PATTERN, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $name, array $options = [])
    {
        preg_match(self::FACTORY_PATTERN, $name, $matches);

        $migration_name = $matches[1];

        $config = $container->get('Config');

        if (! isset($config['migrations'][$migration_name])) {
            throw new RuntimeException(sprintf("`%s` is not in migrations configuration", $migration_name));
        }

        $migration_config = $config['migrations'][$migration_name];

        if (! isset($migration_config['dir'])) {
            throw new RuntimeException(sprintf("`dir` has not be specified in `%s` migrations configuration", $migration_name));
        }

        if (! isset($migration_config['namespace'])) {
            throw new RuntimeException(sprintf("`namespace` has not be specified in `%s` migrations configuration", $migration_name));
        }

        $generator = new MigrationSkeletonGenerator($migration_config['dir'], $migration_config['namespace']);

        return $generator;
    }
}
