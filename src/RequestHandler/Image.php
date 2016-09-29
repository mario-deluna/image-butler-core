<?php 

namespace ImageButler\RequestHandler;

use ImageButler\HTTP\Request;
use ImageButler\HTTP\Response;
use ImageButler\ServiceContainer;

class Image implements RequestHandlerInterface
{
	/**
	 * The request method
	 * 
	 * @var string
	 */
	protected $method = null;

	/**
	 * The request file name
	 * 
	 * @var string
	 */
	protected $fileName = null;

	/**
	 * The request file domaint
	 * 
	 * @var string
	 */
	protected $fileDomain = null;

	/**
	 * The request file extension
	 * 
	 * @var string
	 */
	protected $fileExtension = null;

	/**
	 * Available image formats
	 * 
	 * @var array
	 */
	protected $availableImageFormats = array();

	/**
	 * Application object
	 * 
	 * @var 
	 */
	protected $application = null;

	/** 
	 * Construct a new image request handler
	 * 
	 * @param ServiceContainer  		$application
	 * @param array 					$availableImageFormats
	 * @return void
	 */
	public function __construct(
		ServiceContainer $application, 
		$availableImageFormats = array()
	){
		// assign the applications service container
		$this->application = $application;

		// always allow jpg images by default
		if (!is_array($availableImageFormats)) {
			$availableImageFormats = array();
		}

		$this->availableImageFormats = array_merge($availableImageFormats, array('jpg'));
	}

	/**
	 * Handle image requests
	 * 
	 * @param Request 			$request
	 * @return Response|false
	 */
	public function handle(Request $request)
	{
		$this->method = $request->method();
		$this->parseRequestUri($request->uri());

		// GET = image render / info
		if ($this->method === 'GET') {
			return $this->handleGetRequest($request);
		}

		// POST = create image 
		elseif ($this->method === 'POST') {
			return $this->handlePostRequest($request);
		}

		// DELETE = delete image 
		elseif ($this->method === 'DELETE') {
			return $this->handleDeleteRequest($request);
		}
	}

	/**
	 * Handle a GET request
	 * 
	 * @param Request 			$request
	 * @return Response|false
	 */
	protected function handleGetRequest(Request $request)
	{
		// when a file name and file extension 
		// is set we have a detail request. This can 
		// mean we have to render an image or return image details.
		if ((!is_null($this->fileName)) && (!is_null($this->fileExtension))) {

			// resolve the image resource from the name
			// and image domain 
			$image = $this->fileName;//$this->finder->find($this->fileDomain, $this->fileName);

			// if the file extension is a vaild image
			// format run the image render action
			if (in_array($this->fileExtension, $this->availableImageFormats)) {
				return $this->executeAction($request, 'Render', array(&$image));
			}

			return $this->executeAction($request, 'Info', array(&$image));
		}
	}

	

	/**
	 * Execute an application action
	 */
	protected function executeAction(Request $request, $actionName, array $arguments = array())
	{
		$actionName = "ImageButler\\Action\\" . $actionName;

		$action = new $actionName($request);
		$action->setContainer($this->application);

		return call_user_func_array(array($action, 'execute'), $arguments);
	}

	/**
	 * Parse the request uri to fill the file properties
	 * 
	 * @param string 			$uriString
	 * @return void
	 */
	protected function parseRequestUri($uriString)
	{
		// if there is a dott in the uri string 
		// we assume to have a file specified
		if (strpos($uriString, '.') !== false) {

			$fileName = substr($uriString, strrpos($uriString, '/') + 1);
			list($this->fileName, $this->fileExtension) = explode('.', $fileName);

			// remove the file part from the uri
			$uriString = substr($uriString, 0, strlen($fileName) * -1);
		}

		$this->fileDomain = $uriString;
	}
}