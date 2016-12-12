<?php

namespace ZfSimpleMigrations\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Application;
use Zend\Router\Http\RouteMatch;
use ZfSimpleMigrations\Library\Migration;
use ZfSimpleMigrations\Library\MigrationSkeletonGenerator;

class MigrateControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var Application $application */
        $application = $container->get('Application');

        /** @var RouteMatch $routeMatch */
        $routeMatch =  $application->getMvcEvent()->getRouteMatch();

        $name = $routeMatch->getParam('name', 'default');

        /** @var Migration $migration */
        $migration = $container->get('migrations.migration.' . $name);

        /** @var MigrationSkeletonGenerator $generator */
        $generator = $container->get('migrations.skeleton-generator.' . $name);

        $controller = new MigrateController();
        $controller->setMigration($migration);
        $controller->setSkeletonGenerator($generator);

        return $controller;
    }
}
