<?php 

namespace ImageButler\RequestHandler;

use ImageButler\HTTP\Request;
use ImageButler\HTTP\Response;

interface RequestHandlerInterface
{
    /**
	 * Handle the given request and produce an response 
	 * object or return false
	 * 
	 * @param Request 			$request
	 * @return Response|false
	 */
    public function handle(Request $request);
}