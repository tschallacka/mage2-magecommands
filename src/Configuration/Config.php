<?php 
namespace Tschallacka\MageCommands\Configuration;

use Tschallacka\MageRain\File\Directory;

class Config 
{
    const MODULE_NAME = 'Tschallacka_MageCommands';    
    const DEVELOPMENT_DIRECTORY = 'local';
    
    /**
     * Returns the local development directory as an absolute path
     */
    public static function getLocalDir() 
    {
        static $dir = null;
        // Needs to be like this because of no operation allowed in constants error
        if(!$dir) {
            $dir = realpath(Directory::getMagentoVendorDir() . '/../' . self::DEVELOPMENT_DIRECTORY);
        }
        return $dir;
    }
}