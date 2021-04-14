<?php 
namespace Tschallacka\MageCommands\Configuration;

use Tschallacka\MageRain\File\Directory;

class Config 
{
    const MODULE_NAME = 'Tschallacka_MageCommands';    
    
    protected $base_path;
    
    protected $local_dir;
    
    protected $vendor_path;
    
    /**
     * Creates the configuration for the commands.
     * @param string $base_path Base path to the magento installation
     * @param string $development_directory where the modules will be created, relative to the base path. 
     */
    public function __construct($base_path = BP, $development_directory = 'local', $vendor_path = null) 
    {
        $this->local_dir = $this->base_path . DIRECTORY_SEPARATOR . $development_directory;
        $this->base_path = $base_path;
        $this->vendor_path = $vendor_path;
    }
    
    /**
     * Returns the local development directory as an absolute path
     */
    public function getLocalPath() 
    {
        return $this->local_dir;
    }
    
    public function getBasePath() 
    {
        return $this->base_path;
    }
    
    public function getVendorPath()
    {
        return $this->vendor_path;
    }
}