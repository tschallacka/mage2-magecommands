<?php namespace Tschallacka\MageCommands\Module;

use Tschallacka\MageCommands\Configuration\Config;
use Tschallacka\MageRain\Helper\Text\Str;
use Tschallacka\MageRain\Helper\File\Directory;

class ModuleInfo 
{
    protected $module_name;
    protected $author_name;
    protected $hyphen_module_name;
    protected $hyphen_author_name;
    protected $raw_name;
    
    protected $project_local_path;
    
    public function __construct($raw_name) 
    {
        $this->raw_name = $raw_name;    
    
        $parts = explode('_', $raw_name);
        $this->author_name = $parts[0];
        $this->module_name = $parts[1];
        $this->hyphen_author_name = $this->hyphenate($this->author_name);
        $this->hyphen_module_name = $this->hyphenate($this->module_name);
    }   
    
    protected function hyphenate($str) 
    {
        return str_replace("_", "-", Str::snake($str));
    }
    
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
     * Returns a directory instance
     * @return \Tschallacka\MageRain\Helper\File\Directory
     */
    protected function getVendorPath() 
    {
        $author = $this->hyphen_author_name;
        $module = $this->hyphen_module_name;
        
        $vendor_path_author = Config::getVendorDir() . '/' . $author;
        
        $project_path_vendor = $vendor_path_author . '/' . $module;
        
        return new Directory($project_path_vendor);
    }
    
    /**
     * Returns the module name as Magento likes it, Author_Module
     * @return string
     */
    public function getMagentoModuleName() 
    {
        return $this->raw_name;
    }
    
    /**
     * Returns the path of the module in the local development directory.
     * @return \Tschallacka\MageRain\Helper\File\Directory
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
    
    public function getNameSpace() 
    {
        return $this->author_name . '\\' . $this->module_name;   
    }
    
    
    /**
     * Returns the location of the src directory
     * @return \Tschallacka\MageRain\Helper\File\Directory
     */
    public function getSourcePath()
    {
        return $this->getLocalPath()->getChild('src');
    }
    
    public function getPackageName() 
    {
        return $this->hyphen_author_name . '/' . $this->hyphen_module_name;    
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
    
    /**
     * Get the author name
     * @return String
     */
    public function getAuthorName()
    {
        return $this->author_name;
    }
    
    /**
     * Return the package name
     * @return string
     */
    public function getModuleName()
    {
        return $this->module_name;
    }
    
    /**
     * Get the author name, hyphenated
     * @return String
     */
    public function getHyphenAuthorName()
    {
        return $this->hyphen_author_name;
    }
    
    /**
     * Return the package name, hyphenated
     * @return string
     */
    public function getHyphenModuleName()
    {
        return $this->hyphen_module_name;
    }
    
}