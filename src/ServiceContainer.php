<?php 

namespace ImageButler;

class ServiceContainer 
{
	/**
	 * An array of available services
	 * 
	 * @var array 
	 */
	private $services = array();

	/**
	 * The current container values
	 * 
	 * @var array
	 */
	private $container = array();

	/**
	 * The service configuration
	 * 
	 * @var array
	 */
	private $configuration = array();

	/**
	 * Set the application parameters
	 * 
	 * @param array 			$configuration
	 * @return void
	 */
	public function setConfiguration(array $configuration)
	{
		$this->configuration = $configuration;
	}

	/**
	 * Store something inside the the container 
	 * 
	 * @param string 			$key
	 * @param mixed 			$value
	 * 
	 * @throws Exception
	 * 
	 * @return void
	 */
	public function set($key, $value)
	{
		if (array_key_exists($key, $this->container)) {
			throw new Exception('Cannot overwrite service container properties.');
		}

		$this->container[$key] = $value;
	}

	/**
	 * Bind a service to the container
	 * 
	 *     $app->bind('config', Service\DBConfig::class, ['hydrahon'])
	 * 
	 * @param string 			$key
	 * @param string 			$className
	 * @param array[string]		$dependencies
	 * 
	 * @return void
	 */
	public function bind($key, $className, array $dependencies = array())
	{
		$this->services[$key] = array($className, $dependencies);
	}

	/**
	 * Get a value from the service container or 
	 * 
	 *     $app->bind('config', Service\DBConfig::class, ['hydrahon'])
	 * 
	 * @param string 			$key
	 * @param string 			$className
	 * @param array[string]		$dependencies
	 * 
	 * @return void
	 */
	public function bind($key, $className, array $dependencies = array())
	{
		$this->services[$key] = array($className, $dependencies);
	}
}