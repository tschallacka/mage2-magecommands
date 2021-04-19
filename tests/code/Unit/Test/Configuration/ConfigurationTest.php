<?php namespace Tschallacka\MageCommands\Tests\Code\Unit\Test\Module;

use Magento\Framework\Module\FullModuleList;
use PHPUnit\Framework\TestCase;
use Tschallacka\MageCommands\Configuration\Config;
/**
 *  test case.
 */
class ConfigurationTest extends TestCase
{
    protected $module_list;
    
    protected $root_path;
    
    protected $relative_path;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        $this->module_list = $this->createMock(FullModuleList::class);
        $this->root_path = '/tmp';
        $this->relative_path = 'tmp';
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    protected function getConfig($base_path, $local_path, $vendor_path) 
    {
        $config = new Config($this->module_list, $base_path, $local_path, $vendor_path);
        return $config;
    }
    
    public function testGetFullModuleList() 
    {
        $config = $this->getConfig($this->root_path, $this->root_path, $this->root_path);
        $this->assertEquals($this->module_list, $config->getFullModuleList());
    }
    
    public function testGetVendorPath()
    {
        $config = $this->getConfig($this->root_path, $this->root_path, null);
        $this->assertNull($config->getVendorPath());
        
        $config = $this->getConfig($this->root_path, $this->root_path, $this->root_path);
        $this->assertEquals($this->root_path, $config->getVendorPath());
        
        $config = $this->getConfig($this->root_path, $this->root_path, $this->relative_path);
        $this->assertEquals($this->root_path . DIRECTORY_SEPARATOR . $this->relative_path, $config->getVendorPath());
    }
    
    public function testGetLocalPath()
    {
        $config = $this->getConfig($this->root_path, $this->root_path, $this->root_path);
        $this->assertEquals($this->root_path, $config->getLocalPath());
        
        $config = $this->getConfig($this->root_path, $this->relative_path, $this->relative_path);
        $this->assertEquals($this->root_path . DIRECTORY_SEPARATOR . $this->relative_path, $config->getLocalPath());
    }
    
    public function testGetBasepath() 
    {
        $config = $this->getConfig($this->root_path, $this->root_path, $this->root_path);
        $this->assertEquals($this->root_path, $config->getBasePath());
        
        $config = $this->getConfig($this->relative_path, null, null);
        $this->assertEquals($this->relative_path, $config->getBasePath());
    }
    
    
}