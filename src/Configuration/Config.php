<?php 
namespace Tschallacka\MageCommands\Configuration;

class Config 
{
    const MODULE_NAME = 'Tschallacka_MageCommands';    
    const DEVELOPMENT_DIRECTORY = 'local';
    
    /**
     * Returns the path as it's defined in VENDOR_PATH(app/etc/vendor_path.php) in a realpath format 
     * @return string full path to the vendor dir.
     */
    public static function getVendorDir()
    {
        static $dir = null;
        // Needs to be like this because of no operation allowed in constants error
        if(!$dir) {
            $dir = realpath(require(VENDOR_PATH));
        }
        return $dir;
    }
    
    /**
     * Returns the local development directory as an absolute path
     */
    public static function getLocalDir() 
    {
        static $dir = null;
        // Needs to be like this because of no operation allowed in constants error
        if(!$dir) {
            $dir = realpath(self::getVendorDir() . '/../' . self::DEVELOPMENT_DIRECTORY);
        }
        return $dir;
    }
}