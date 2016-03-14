<?php 

namespace ImageButler\Action;

use ImageButler\BaseAction;
use ImageButler\HTTP\Response;

class Render extends BaseAction
{
    /**
     * Render an image and return an image response
     */
    public function execute($image)
    {
        return new Response($image);
    }
}