<?php 

namespace ImageButler;

use ImageButler\HTTP\Request;

abstract class BaseAction
{
    /**
     * The app instance to access serivces
     * 
     * @var ServiceContainer
     */
    protected $app = null;

    /**
     * The request object
     * 
     * @var Request
     */
    protected $request = null;

    /**
     * Construct a new action 
     * 
     * @param Request           $request
     */
    final public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Set the app object / service container 
     * 
     * @param ServiceContainer              $app
     * @return void
     */
    final public function setContainer(ServiceContainer $app)
    {
        $this->app = $app;
    }
}