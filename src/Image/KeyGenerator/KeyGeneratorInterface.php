<?php 

namespace ImageButler\Image\KeyGenerator;

use ImageButler\Image;

interface KeyGeneratorInterface
{
    /** 
     * Generate a key for the given image
     * 
     * @param Image             $image
     * @return string
     */ 
    public function generate(Image $image);
}