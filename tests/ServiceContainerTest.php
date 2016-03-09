<?php 

namespace ImageButler\Test;

use ImageButler\ServiceContainer;

class ServiceContainerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Create service container instance
	 * 
	 * @param array 				$configuration
	 * @return ServiceContainer
	 */
	public function createServiceContainer(array $configuration = array())
    {
    	$serviceContainer = new ServiceContainer;
    	$serviceContainer->setConfiguration($configuration);

    	return $serviceContainer;
    }

    /**
     * Test binding a service
     */
    public function testBind()
    {
    	$serviceContainer = $this->createServiceContainer();

    	// bind without dependencies
    	$serviceContainer->bind('testservice', 'SomeClass\\Name');

    	$this->assertEquals(array('testservice' => array(
    		'SomeClass\\Name',
    		array(),
    	)), $serviceContainer->getServices());

    	// bind another service with dependencies
    	$serviceContainer->bind('anotherservice', 'Some\\Other\\Class\\Name', array('testservice'));

    	$this->assertEquals(array(
    		'testservice' => array(
	    		'SomeClass\\Name',
	    		array(),
	    	),
	    	'anotherservice' => array(
	    		'Some\\Other\\Class\\Name',
	    		array('testservice'),
	    	)
	    ), $serviceContainer->getServices());

	    // now overwrite the first service
		$serviceContainer->bind('testservice', 'SomeDiffrentClass\\Name');

	    $this->assertEquals(array(
    		'testservice' => array(
	    		'SomeDiffrentClass\\Name',
	    		array(),
	    	),
	    	'anotherservice' => array(
	    		'Some\\Other\\Class\\Name',
	    		array('testservice'),
	    	)
	    ), $serviceContainer->getServices());
    }

    /**
     * Test creating a service instance
     */
    public function testCreate()
    {
    	$serviceContainer = $this->createServiceContainer();

    	$helloServiceClassName = __NAMESPACE__ . '\\Services\\Hello';

    	// bind the hello service
    	$serviceContainer->bind('hello', $helloServiceClassName);

    	// create an hello service
    	$service1 = $serviceContainer->create('hello');
    	$this->assertInstanceOf($helloServiceClassName, $service1);

    	// create another hello service
    	$service2 = $serviceContainer->create('hello');
    	$service2->setName('Ray');

		$this->assertInstanceOf($helloServiceClassName, $service1);
		$this->assertNotEquals($service1, $service2);
    }

    /**
     * Test creating a service instance
     */
    public function testCreateWithDirectDependency()
    {
    	$serviceContainer = $this->createServiceContainer();

    	$helloServiceClassName = __NAMESPACE__ . '\\Services\\Hello';

    	// bind the hello service
    	$serviceContainer->bind('hello.james', $helloServiceClassName, array('James'));
    	$serviceContainer->bind('hello.johanna', $helloServiceClassName, array('Johnna'));
    	$serviceContainer->bind('scream.ladina', $helloServiceClassName, array('Ladina', true));

	    // check if the name gets set correctly
	    $this->assertEquals('Hello James', $serviceContainer->create('hello.james')->say());
	    $this->assertEquals('Hello Johnna', $serviceContainer->create('hello.johanna')->say());
	    $this->assertEquals('HELLO LADINA', $serviceContainer->create('scream.ladina')->say());
    }

    /**
     * Test creating a service instance
     */
    public function testCreateWithContainerDependency()
    {
    	$serviceContainer = $this->createServiceContainer();

    	// bind the hello service
    	$serviceContainer->bind('hello.ray', __NAMESPACE__ . '\\Services\\Hello', array('Ray'));
    	$serviceContainer->bind('whisper.ray', __NAMESPACE__ . '\\Services\\Whisper', array('@hello.ray'));

	    // check if the name gets set correctly
	    $this->assertEquals('Hello Ray', $serviceContainer->create('hello.ray')->say());
	    $this->assertEquals('hello ray', $serviceContainer->create('whisper.ray')->say());
    }

    /**
     * Test creating a service instance
     */
    public function testCreateWithConfigDependency()
    {
    	$serviceContainer = $this->createServiceContainer(array(
    		'hello' => array(
    			'name' => 'Mario',
    			'scream' => true
    		)
    	));

    	// bind the hello service
    	$serviceContainer->bind('hello', __NAMESPACE__ . '\\Services\\Hello', array('#hello.name', '#hello.scream'));

	    // check if the name gets set correctly
	    $this->assertEquals('HELLO MARIO', $serviceContainer->create('hello')->say());
    }
}
