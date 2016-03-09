<?php 

namespace ImageButler\Test\Services;

class Whisper
{
    protected $helloInstance = null;

    public function __construct(Hello $helloInstance)
    {
        $this->helloInstance = $helloInstance;
    }

    public function say()
    {
        return strtolower($this->helloInstance->say());
    }
}
