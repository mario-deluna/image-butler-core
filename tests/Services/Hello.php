<?php 

namespace ImageButler\Test\Services;

class Hello
{
    private $name = null;

    private $scream = false;

    public function __construct($name = 'World', $scream = false)
    {
        $this->name = $name;
        $this->scream = $scream;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function say()
    {
        $phrase = 'Hello ' . $this->name;

        if ($this->scream) {
            $phrase = strtoupper($phrase);
        }

        return $phrase;
    }
}
