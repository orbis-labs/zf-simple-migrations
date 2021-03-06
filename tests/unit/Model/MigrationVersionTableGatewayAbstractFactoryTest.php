<?php


namespace ZfSimpleMigrations\UnitTest\Model;


use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;
use ZfSimpleMigrations\Model\MigrationVersionTableGatewayAbstractFactory;

class MigrationVersionTableGatewayAbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ServiceManager */
    protected $service_manager;

    protected function setUp()
    {
        parent::setUp();

        $this->service_manager = new ServiceManager();
        $this->service_manager->setService('foo',
            $this->getMock(Adapter::class, [], [], '', false));
    }


    public function test_it_indicates_what_services_it_creates()
    {
        $factory = new MigrationVersionTableGatewayAbstractFactory();
        $this->assertTrue($factory->canCreate($this->service_manager,
            'migrations.versiontablegateway.foo'), "should indicate it provides service for \$name");


        $this->assertFalse($factory->canCreate($this->service_manager,
            'asdf'), "should indicate it does not provide service for \$name or \$requestedName");
    }

    public function test_it_returns_a_table_gateway()
    {
        $factory = new MigrationVersionTableGatewayAbstractFactory();
        $instance = $factory($this->service_manager, 'migrations.versiontablegateway.foo', ['migrations.versiontablegateway.foo']);
        $this->assertInstanceOf(TableGateway::class, $instance,
            "factory should return an instance of " . TableGateway::class . " when asked by \$name");

        $instance2 = $factory($this->service_manager, 'migrations.versiontablegateway.foo');
        $this->assertInstanceOf(TableGateway::class, $instance2,
            "factory should return an instance of " . TableGateway::class . " when asked by \$requestedName");
    }
}
