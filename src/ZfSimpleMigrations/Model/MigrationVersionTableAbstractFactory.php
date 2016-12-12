<?php

namespace ZfSimpleMigrations\Model;

use Interop\Container\ContainerInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class MigrationVersionTableAbstractFactory implements AbstractFactoryInterface
{
    const FACTORY_PATTERN = '/migrations\.versiontable\.(.*)/';

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
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        // $matches will be set by first preg_match if it matches, or second preg_match if it doesnt
        preg_match(self::FACTORY_PATTERN, $name, $matches);

        $adapter_name = $matches[1];

        /** @var $tableGateway TableGateway */
        $tableGateway = $container->get('migrations.versiontablegateway.' . $adapter_name);
        $table = new MigrationVersionTable($tableGateway);
        return $table;
    }
}
