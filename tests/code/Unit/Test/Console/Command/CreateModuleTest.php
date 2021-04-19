<?php namespace Tschallacka\MageCommands\Tests\Code\Unit\Test\Module;

use PHPUnit\Framework\TestCase;
use Tschallacka\MageCommands\Configuration\Config;
use Tschallacka\MageCommands\Console\Command\CreateModule;
use Tschallacka\MageCommands\Module\ModuleInfo;
use Tschallacka\MageRain\File\Format\Composer;
use Tschallacka\MageRain\File\Directory;
/**
 *  test case.
 */
class CreateModuleTest extends TestCase
{
    protected $module_info;
    protected $command;
    protected $config;
    protected $test_name;
    protected $directory;
    protected $namespace;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->test_name = 'tester-test/test-module';
        $this->namespace = 'TesterTest\\TestModule';
        $modulelist = $this->getMockForAbstractClass(\Magento\Framework\Module\ModuleListInterface::class);
        
        $this->config->method('getFullModuleList')->willReturn($modulelist);
        $this->config->method('getLocalPath')->willReturn('/tmp');
        $this->config->method('getBasePath')->willReturn('/tmp');
        $this->config->method('getVendorPath')->willReturn('/tmp');
        
        $this->directory = $this->createMock(Directory::class);
        $this->directory->method('getPath')->willReturn('/tmp');
        
        $this->module_info = $this->createMock(ModuleInfo::class);
        $this->module_info->method('getPackageName')->willReturn($this->test_name);
        $this->module_info->method('getLocalPath')->willReturn($this->directory);
        $this->module_info->method('getNameSpace')->willReturn($this->namespace);
        
        $this->command = new CreateModule(null, $this->config);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void
    {
        $this->module_info = null;
        parent::tearDown();
    }
    
    protected function getComposer()
    {
        $composer = new Composer('/tmp/composer.json');
        $composer->initializeEmptyFile();
        return $composer;
    }
    
    public function testAddPackageToRequire() 
    {
        $composer = $this->getComposer();
        
        $this->command->addPackageToRequire($composer, $this->module_info);
        $this->assertArrayHasKey($this->test_name, $composer->require);
        
        $composer = $this->getComposer();
        
        $test_version = '^1.2';
        $composer->require = [$this->test_name => $test_version];
        $this->command->addPackageToRequire($composer, $this->module_info);
        $this->assertArrayHasKey($this->test_name, $composer->require);
        $this->assertEquals($test_version, $composer->require[$this->test_name]);
    }

    public function testAddPackageAsLocalRepository()
    {
        $composer = $this->getComposer();
        $this->assertNull($composer->repositories);
        $this->command->addPackageAsLocalRepository($composer, $this->module_info);
        $this->assertEquals(1, count($composer->repositories));
        $item = $composer->repositories[0];
        $this->assertEquals('path', $item['type']);
        $this->assertEquals($this->directory->getPath(), $item['url']);
        
        $this->command->addPackageAsLocalRepository($composer, $this->module_info);
        $this->assertEquals(1, count($composer->repositories));
    }
    
    public function testWriteModuleToComposer() 
    {
        $composer = $this->getComposer();
        $this->command->writeModuleToComposer($composer, $this->module_info);
        $autoload = $composer->get('autoload.files');
        $this->assertEquals('registration.php', array_shift($autoload));
        
        $namespace = $this->namespace.'\\';
        $this->assertArrayHasKey($namespace, $composer->get('autoload.psr-4'));
        $this->assertEquals('src', $composer->get('autoload.psr-4')[$namespace]);
        
        $this->assertEquals("magento2-module", $composer->type);
        $this->assertEquals($this->test_name, $composer->name);
    }
    
    
}


