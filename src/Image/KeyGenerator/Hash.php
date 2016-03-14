<?php 

namespace ImageButler\Image\KeyGenerator;

use ImageButler\Image;

class Hash implements KeyGeneratorInterface
{
    /**
     * The hashing algorithm
     */
    private $algo = null;

    /**
     * Construct a new image hasher
     * 
     * @param string            $algo
     * @return void
     */
    public function __construct($algo = 'md5')
    {
        $this->algo = $algo;
    }

    /** 
     * Generate an image key based on the image itself
     * 
     * @param Image             $image
     * @return string
     */
    public function generate(Image $image)
    {
        return hash_file($this->algo, $image->path());
    }
}