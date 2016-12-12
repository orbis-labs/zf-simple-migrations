<?php

namespace ZfSimpleMigrations\Model;

use Interop\Container\ContainerInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use ZfSimpleMigrations\Library\MigrationException;

class MigrationVersionTableGatewayAbstractFactory implements AbstractFactoryInterface
{
    const FACTORY_PATTERN = '/migrations\.versiontablegateway\.(.*)/';

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
        $adapter_name = $matches[1];

        /** @var $dbAdapter \Zend\Db\Adapter\Adapter */
        $dbAdapter = $container->get($adapter_name);
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new MigrationVersion());

        return new TableGateway(MigrationVersion::TABLE_NAME, $dbAdapter, null, $resultSetPrototype);
    }
}
