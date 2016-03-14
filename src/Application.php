<?php 

namespace ImageButler;

use ImageButler\HTTP\Request;
use ImageButler\HTTP\Response;
use ImageButler\RequestHandler\RequestHandlerInterface;

class Application extends ServiceContainer
{
    /**
     * What request handlers should be used
     */
    protected $requestHandlers = array(
        'handler.image'
    );

    /**
     * Construct a new application object
     * 
     * @param array             $configuration
     * @return void
     */ 
    public function __construct(array $configuration = array())
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
        $this->bind('handler.image', __NAMESPACE__ . "\\RequestHandler\\Image");
    }   

    /**
     * Dispatch a request
     * 
     * @param Request           $request
     * @return Response
     */
    public function dispatch(Request $request)
    {
        foreach($this->requestHandlers as $handlerService) {

            $handler = $this->get($handlerService);

            if (($response = $this->dispatchWithHandler($handler, $request)) instanceof Response) {
                return $response;
            }
        }

        return $this->createNothingToHandleResponse($request);
    }

    /**
     * Create a response displayed when all handlers did not return a response
     * 
     * @return Response
     */
    protected function createNothingToHandleResponse(Request $request)
    {
        return new Response('Image-Butler has nothing to do here...', 404);
    }
    
    /** 
     * Dispatch a request object with the given handler object
     * 
     * @param RequestHandlerInterface               $handler
     * @param Request                               $request
     * @return Response|false
     */
    private function dispatchWithHandler(RequestHandlerInterface $handler, Request $request)
    {
        return $handler->handle($request);
    }

    /**
     * Dispatch the application using the super globals
     * 
     * @return Resposne
     */
    public function dispatchFromSuperGlobals()
    {
        return $this->dispatch(Request::fromGlobals());
    }
}