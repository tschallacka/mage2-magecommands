<?php 
namespace Tschallacka\MageCommands\Configuration;


use \Magento\Framework\Module\ModuleListInterface;

class Config 
{
    const MODULE_NAME = 'Tschallacka_MageCommands';    
    
    protected $base_path;
    
    protected $local_dir;
    
    protected $vendor_path;
    
    /**
     * @var ModuleListInterface
     */
    protected $module_list;
    
    /**
     * Creates the configuration for the commands.
     * @param ModuleListInterface $module_list The full module list.
     * @param string $base_path Base path to the magento installation
     * @param string $development_directory where the modules will be created, relative to the base path.
     * @param string $vendor_path a custom vendor path to the composer vendor directory 
     */
    public function __construct(ModuleListInterface $module_list, $base_path = BP, $development_directory = 'local', $vendor_path = null) 
    {
        $this->local_dir = $development_directory;
        $this->base_path = $base_path;
        $this->vendor_path = $vendor_path;
        $this->module_list = $module_list;
    }
    
    /**
     * Get the module list
     * @return \Magento\Framework\Module\ModuleListInterface
     */
    public function getFullModuleList() 
    {
        return $this->module_list;    
    }
    
    /**
     * Returns the local development directory as an absolute path
     * @return string
     */
    public function getLocalPath() 
    {
        if(substr($this->local_dir, 0, 1) == DIRECTORY_SEPARATOR) {
            return $this->local_dir;
        }
        return $this->base_path . DIRECTORY_SEPARATOR . $this->local_dir;
    }
    
    /**
     * Get the base path
     * @return string
     */
    public function getBasePath() 
    {
        return $this->base_path;
    }
    
    public function getVendorPath()
    {
        if(is_null($this->vendor_path) || substr($this->vendor_path, 0, 1) == DIRECTORY_SEPARATOR) {
            return $this->vendor_path;
        }
        return $this->base_path . DIRECTORY_SEPARATOR . $this->vendor_path;
    }
}