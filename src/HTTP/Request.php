<?php 

namespace ImageButler\HTTP;

class Request 
{   
    /**
     * Create a request object from super globals
     * 
     * @return Request
     */
    public static function fromGlobals()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

        return new static($method, $uri, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    }

    /**
     * Array of available http methods
     * 
     * @var array[string] 
     */
    private $availableMethods = array('GET', 'POST', 'PUT', 'DELETE');

    /**
     * The HTTP method
     * 
     * @var string 
     */
    protected $method = null;

    /** 
     * The requets uri
     * 
     * @var string
     */
    protected $uri = null;

    /** 
     * Get query parameters
     * 
     * @var string
     */
    protected $queryParameters = array();

    /** 
     * Post parameters
     * 
     * @var string
     */
    protected $postParameters = array();

    /** 
     * cookie parameters
     * 
     * @var string
     */
    protected $cookieParameters = array();

    /** 
     * File parameters
     * 
     * @var string
     */
    protected $fileParameters = array();

    /** 
     * Server parameters
     * 
     * @var string
     */
    protected $serverParameters = array();

    /**
     * Construct a new application object
     * 
     * @param array             $configuration
     * @return void
     */ 
    public function __construct($method, $uri = '/', array $get = array(), array $post = array(), array $cookies = array(), array $files = array(), array $server = array())
    {
        $this->setMethod($method);
        $this->setUri($uri);
        $this->setQueryParameters($get);
        $this->setPostParameters($post);
        $this->setCookieParameters($cookies);
        $this->setFileParameters($files);
        $this->setServerParameters($server);
    }

    /**
     * Return the http method
     * 
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Set the http method of the Request
     * 
     * @param string            $method
     * @return void
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);

        // when the given method is invalid
        // just use GET, I don't know if this 
        // is the bets behavior but throwing an exception
        // would allow basically anybody to crash this script.
        if (!in_array($method, $this->availableMethods)) {
            $method = reset($this->availableMethods);
        }

        $this->method = $method;
    }

    /**
     * Return the request uri
     * 
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Set the request uri
     * 
     * @param string            $uri
     * @return void
     */
    public function setUri($uri)
    {
        // remove the get parameters in the uri string
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // when the uri string is empty just use root
        if (substr($uri, 0, 1) !== '/') {
            $uri = '/' . $uri;
        }

        $this->uri = (string) $uri;
    }

    /**
     * Return a query parameter with the key
     * 
     * @param string                $key
     * @param mixed                 $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!array_key_exists($key, $this->queryParameters)) {
            return $default;
        }

        return $this->queryParameters[$key];
    }

    /**
     * Set the query parameters
     * 
     * @param array             $queryParameters
     * @return void
     */
    public function setQueryParameters(array $queryParameters = array())
    {
        $this->queryParameters = $queryParameters;
    }

    /**
     * Return a post parameter with the key
     * 
     * @param string                $key
     * @param mixed                 $default
     * @return mixed
     */
    public function post($key, $default = null)
    {
        if (!array_key_exists($key, $this->postParameters)) {
            return $default;
        }

        return $this->postParameters[$key];
    }

    /**
     * Set the post parameters
     * 
     * @param array             $postParameters
     * @return void
     */
    public function setPostParameters(array $postParameters = array())
    {
        $this->postParameters = $postParameters;
    }

    /**
     * Return a cookie parameter with the key
     * 
     * @param string                $key
     * @param mixed                 $default
     * @return mixed
     */
    public function cookie($key, $default = null)
    {
        if (!array_key_exists($key, $this->cookieParameters)) {
            return $default;
        }

        return $this->cookieParameters[$key];
    }

    /**
     * Set the cookie parameters
     * 
     * @param array             $cookieParameters
     * @return void
     */
    public function setCookieParameters(array $cookieParameters = array())
    {
        $this->cookieParameters = $cookieParameters;
    }

    /**
     * Return a file parameter with the key
     * 
     * @param string                $key
     * @param mixed                 $default
     * @return mixed
     */
    public function file($key, $default = null)
    {
        if (!array_key_exists($key, $this->fileParameters)) {
            return $default;
        }

        return $this->fileParameters[$key];
    }

    /**
     * Set the file parameters
     * 
     * @param array             $fileParameters
     * @return void
     */
    public function setFileParameters(array $fileParameters = array())
    {
        $this->fileParameters = $fileParameters;
    }

    /**
     * Return a server parameter with the key
     * 
     * @param string                $key
     * @param mixed                 $default
     * @return mixed
     */
    public function server($key, $default = null)
    {
        if (!array_key_exists($key, $this->serverParameters)) {
            return $default;
        }

        return $this->serverParameters[$key];
    }

    /**
     * Set the server parameters
     * 
     * @param array             $serverParameters
     * @return void
     */
    public function setServerParameters(array $serverParameters = array())
    {
        $this->serverParameters = $serverParameters;
    }    
}