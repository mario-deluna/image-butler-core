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
	 * Return a list of all binded services
	 * 
	 * @return array
	 */
	public function getServices()
	{
		return $this->services;
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
	 * Check if a service with the given key is already loaded
	 * 
	 * @return string 			$key
	 * @return bool
	 */
	public function isLoaded($key)
	{
		return array_key_exists($key, $this->container);
	}

	/**
	 * Check if a service exists
	 * 
	 * @param string 			$key
	 * @return bool
	 */
	public function hasService($key)
	{
		return array_key_exists($key, $this->services);
	}

	/**
	 * Check if the key exists in the container or the binded services
	 * 
	 * @param string 			$key
	 * @return bool
	 */
	public function has($key)
	{
		return $this->isLoaded($key) || $this->hasService($key);
	}

	/**
	 * Bind a service to the container
	 * 
	 *     $app->bind('config', Service\DBConfig::class, ['@hydrahon'])
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
	 * Get a service from the servie container
	 * 
	 * @param string 			$key
	 * @return mixed
	 */
	public function get($key)
	{
		// If a service is currently not loaded create
		// him and store him into the container
		if (!$this->isLoaded($key)) {
			$this->set($key, $this->create($key));
		}

		// return the loaded service
		return $this->container[$key];
	}

	/**
	 * Create a new instance of a binded service 
	 * 
	 *     $app->create('config')
	 * 
	 * @param string 			$key
	 * 
	 * @throws Exception
	 * 
	 * @return mixed
	 */
	public function create($key)
	{
		if (!$this->hasService($key)) {
			throw new Exception('Cannot create instance of unbinded service "' . $key . '".');
		}

		list($className, $dependencies) = $this->services[$key];

		// before creating the instance of the 
		// requested service we have to resolve it's 
		// dependencies
		$resolvedDependencies = array();

		foreach($dependencies as $dependency) {

			// when the dependency starts with an @ character
			// we have to resolve it from the current container 
			if (is_string($dependency) && substr($dependency, 0, 1) === '@') {

				$dependencyName = substr($dependency, 1);

				// if the dependency name matches the current 
				// we have an infinite loop 
				if ($dependencyName === $key) {
					throw new Exception('A service cannot depend on itself.');
				}

				// add the dependency from the current container
				$resolvedDependencies[] = $this->get($dependencyName);
			}

			// when the dependency starts with a # character we
			// have to resolve and inject a configuration value
			elseif(is_string($dependency) && substr($dependency, 0, 1) === '#') {
       			$resolvedDependencies[] = $this->getConfigurationValue(substr($dependency, 1));
			}

			// otherwise we just use the value itself
			else {
				$resolvedDependencies[] = $dependency;
			}
		}

		// well my love to php is to big to not 
		// start screaming that I have to do the following:
		if (version_compare(PHP_VERSION, '5.6', '<')) {

			// in older php versions use reflection class
			// to set the constructor arguments
			$reflector = new ReflectionClass($className);
			$service = $reflector->newInstanceArgs($resolvedDependencies);

		} else {

			// in the newer php versions we can user ...
			$service = new $className(...$resolvedDependencies);
		}

		return $service;
	}

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
	 * Return the full configuration of the current application
	 * 
	 * @return array
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

		/**
	 * Get configuration value with the given key
	 * 
	 * @param string 			$configurationKey
	 * @return mixed
	 */
	public function getConfigurationValue($configurationKey)
	{
		// direct match
		if (isset($this->configuration[$configurationKey])) {
            return $this->configuration[$configurationKey];
        }

        // try to resolve the dotts
		elseif (strpos($configurationKey, '.') !== false) {

	   	 	$kp = explode('.', $configurationKey);
	    	$curr = $this->configuration;
	        
	        foreach ($kp as $k) {
	            if (isset($curr[$k])) {
	                $curr = $curr[$k];
	            } 
	            else {
	            	return null;
	            }
	        }

	        return $curr;
		}

		return null;
	}
}