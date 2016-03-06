<?php 

namespace ImageButler;

class Application extends ServiceContainer
{
	/**
	 * Construct a new application object
	 * 
	 * @param array 			$configuration
	 * @return void
	 */ 
	public function __consturct(array $configuration = array())
	{
		// assign the configuartion values to the service container
		$this->setConfiguration($configuration);

		// Now we can bind our services needed to run the image butler application
		$this->bindServices();
	}

	/**
	 * Bind the services needed to run the application
	 * 
	 * @return void
	 */ 
	public function bindServices()
	{
		// 
		$this->bind('image.key_gen', Image\KeyGenerator\Hash, '');
	}
}