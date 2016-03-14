<?php

namespace ImageButler\HTTP;

class Response 
{
	/**
	 * Json response
	 * you can pass an array of data wich will be converted to json
	 *
	 * @param array 			$data
	 * @param int				$status
	 * @return Response
	 */
	public static function json(array $data = array(), $status = 200) 
	{
		$response = new static(json_encode($data), $status);
		$response->setHeader('Content-Type', 'text/json');

		return $response;
	}

	/**
	 * Create a new redirect response
	 * 
	 * @param string 			$url
	 * @param int 				$status
	 * @return Response
	 */
	public static function redirect($url, $status = 302)
	{
		$response = new static(null, $status);
		$response->setHeader('Location', $url);

		return $response;
	}

	/**
	 * HTTP status codes and messages by Kohana Framework: http://kohanaframework.org/
	 * 
	 * @var array
	 */
	private $messages = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',

		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',

		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		// 306 is deprecated but reserved
		307 => 'Temporary Redirect',

		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',

		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded'
	);

	/**
	 * status
	 * 
	 * @var int
	 */
	protected $status = 200;

	/**
	 * body
	 * 
	 * @var string
	 */
	protected $body = null;

	/**
	 * header
	 *
	 * @var array
	 */
	protected $header = array();

	/**
	 * Response constructor
	 *
	 * @param string 			$body
	 * @param int				$status
	 */
	public function __construct($body, $status = 200) 
	{
		$this->setBody($body);
		$this->setStatus($status);
	}

	/**
	 * Get the response body
	 *
	 * @return string
	 */
	public function body() 
	{
		return $this->body;
	}

	/**
	 * Set the response body
	 *
	 * @param string				$body
	 * @return void
	 */
	public function setBody($body) 
	{
		$this->body = $body;
	}

	/**
	 * Status setter and getter
	 *
	 * @return int
	 */
	public function status() 
	{
		return $this->status;
	}

	/**
	 * Status setter and getter
	 *
	 * @param int				$code
	 * 
	 * @throws Exception
	 * 
	 * @return void
	 */
	public function setStatus($code) 
	{
		if (!array_key_exists($code, $this->messages)) {
			throw new Exception('Invalid status code "'. $code .'" given.');
		}

		$this->status = $code;
	}

	/**
	 * Get a header value by key
	 *
	 * @param string				$key
	 * @return string
	 */
	public function header($key) 
	{
		return isset($this->header[$key]) ? $this->header[$key] : null;
	}

	/**
	 * Header setter and getter
	 *
	 * @param string				$key
	 * @param string				$string
	 * @return string
	 */
	public function setHeader($key, $string) 
	{
		$this->header[$key] = $string;
	}

	/** 
	 * Send the response headers
	 * 
	 * @throws Exception
	 * @return void 
	 */
	protected function sendHeaders()
	{
		if (headers_sent()) {
			throw new Exception( "Cannot send header, header has already been send." );
		}

		// status header
		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->status . ' ' . $this->messages[$this->status]);

		// check if content type is already set
		if (!isset( $this->header['Content-Type'])) {
			$this->setHeader('Content-Type', 'text/html; charset=utf-8');
		}

		$this->setHeader('X-Powered-By', 'Image-Butler');

		// set headers
		foreach($this->header as $key => $content) {
			header($key.': '.$content);
		}
	}

	/** 
	 * Send the response body
	 * In a basic response this means just outputting $this->body
	 * 
	 * @return void 
	 */
	protected function sendBody()
	{
		echo $this->body();
	}
	
	/**
	 * Send the the headers and body
	 *
	 * @param bool					$headers	
	 * @return void
	 */
	public function send($headers = true) 
	{
		if ($headers) {
			$this->sendHeaders();
		}

		$this->sendBody();
	}
}