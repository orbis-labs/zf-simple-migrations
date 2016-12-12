<?php

namespace ZfSimpleMigrations\UnitTest\Controller;

use Zend\Mvc\Application;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use ZfSimpleMigrations\Controller\MigrateController;
use ZfSimpleMigrations\Controller\MigrateControllerFactory;
use ZfSimpleMigrations\Library\Migration;
use ZfSimpleMigrations\Library\MigrationSkeletonGenerator;

class MigrateControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ServiceManager */
    protected $service_manager;

    protected function setUp()
    {
        parent::setUp();

        $this->service_manager = new ServiceManager();
        $this->service_manager->setService('migrations.migration.foo',
            $this->getMock(Migration::class, [], [], '', false));
        $this->service_manager->setService('migrations.skeletongenerator.foo',
            $this->getMock(MigrationSkeletonGenerator::class, [], [], '', false));
        $this->service_manager->setService('Application',
            $application = $this->getMock(Application::class, [], [], '', false));
        $application->expects($this->any())
            ->method('getMvcEvent')
            ->willReturn($mvcEvent = new MvcEvent());
        $mvcEvent->setRouteMatch($route_match = new RouteMatch(['name' => 'foo']));

    }


    public function test_it_returns_a_controller()
    {
        $factory = new MigrateControllerFactory();
        $instance = $factory($this->service_manager);

        $this->assertInstanceOf(MigrateController::class, $instance,
            "factory should return an instance of " . MigrateController::class);
    }
}
