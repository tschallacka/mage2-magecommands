<?php namespace Tschallacka\MageCommands\Module;

use Tschallacka\MageCommands\Configuration\Config;

use Tschallacka\MageRain\File\Directory;
use Tschallacka\MageRain\Module\ModuleInfo as BaseInfo;

class ModuleInfo extends BaseInfo
{
    protected $project_local_path;
    
    /**
     * If the directory exists this method will throw an exception
     * @param Directory $path
     * @throws \InvalidArgumentException
     */
    protected function failTestPath(Directory $path) 
    {
        if($path->exists()) {
            throw new \InvalidArgumentException($this->project_local_path->getPath() . " already exists. Creating " . $this->raw_name." failed.");
        }
    }
    
    /**
     * Returns the path of the module in the local development directory.
     * @return \Tschallacka\MageRain\File\Directory
     */
    public function getLocalPath() 
    {
        if(is_null($this->project_local_path)) {
            $author = $this->hyphen_author_name;
            $module = $this->hyphen_module_name;
            $local_path_author = Config::getLocalDir() . '/' . $author;
            
            $this->project_local_path = new Directory($local_path_author . '/' . $module);
            
        }
        
        return $this->project_local_path;
    }
    
    /**
     * Returns the location of the src directory
     * @return \Tschallacka\MageRain\Helper\File\Directory
     */
    public function getSourcePath()
    {
        return $this->getLocalPath()->getChild('src');
    }
    
    /**
     * Returns the location of the etc directory
     * @return \Tschallacka\MageRain\Helper\File\Directory
     */
    public function getEtcPath()
    {
        return $this->getLocalPath()->getChild('etc');
    }
    
    /**
     * Tests wether the module is already present on disk in the local development
     * directory or in the magento root composer verdor directory.
     * @throws \InvalidArgumentException when a matching directory is found
     * @return boolean
     */
    public function checkIfDirectoryExists() 
    {
        $this->failTestPath($this->getLocalPath());
        $this->failTestPath($this->getVendorPath());
        return true;
    }
}