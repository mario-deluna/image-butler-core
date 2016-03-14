<?php 

namespace ImageButler\RequestHandler;

use ImageButler\HTTP\Request;
use ImageButler\HTTP\Response;

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
	 * Construct a new image request handler
	 * 
	 * @param array 			$availableImageFormats
	 */
	public function __construct(array $availableImageFormats = array())
	{
		// always allow jpg images by default
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

		if ($this->method === 'GET') {
			return $this->handleGetRequest($request);
		}
	}

	/**
	 * Handle a get request
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

			// if the file extension is a vaild image
			// format run the image render action
			if (in_array($this->fileExtension, $this->availableImageFormats)) {
				return $this->actionContainer->run('action.render');
			}
		}
		return new Response(time());
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