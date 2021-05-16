<?php namespace Tschallacka\MageCommands\Tests\Code\Unit\Test\Module;

use PHPUnit\Framework\TestCase;
use Tschallacka\MageCommands\Module\ModuleInfo;
use InvalidArgumentException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Tschallacka\MageCommands\Configuration\Config;
use Tschallacka\MageRain\File\Directory;
use Magento\Framework\Module\FullModuleList;
/**
 *  test case.
 */
class ModuleInfoTest extends TestCase
{
    protected $module_info;

    protected $config;

    protected $module_info_mocked;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);

        $modulelist = $this->getMockForAbstractClass(\Magento\Framework\Module\ModuleListInterface::class);

        $this->config->method('getFullModuleList')->willReturn($modulelist);
        $this->config->method('getLocalPath')->willReturn('/tmp');
        $this->config->method('getBasePath')->willReturn('/tmp');
        $this->config->method('getVendorPath')->willReturn('/tmp');

        $this->module_info = new ModuleInfo(
                'AuthorName_ModuleName',
                $this->config
        );
        $this->module_info_mocked = new ModuleInfoTestInstance('AuthorName_ModuleName',
            $this->config);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void
    {
        $this->config = null;
        $this->module_info = null;
        parent::tearDown();
    }

    public function testFailTestPath()
    {
        $module_directory = $this->createMock(Directory::class);
        $module_directory->method('exists')->willReturn(false);
        $this->assertTrue($this->module_info->failTestPath($module_directory));

        $module_directory = $this->createMock(Directory::class);
        $module_directory->method('exists')->willReturn(true);
        $this->expectException(InvalidArgumentException::class);
        $this->module_info->failTestPath($module_directory);
    }

    public function testGetLocalPath()
    {
        $this->assertEquals($this->module_info->getLocalPath(), '/tmp/author-name/module-name');
    }

    public function testGetVendorPath()
    {
        $this->assertEquals($this->module_info->getVendorPath('/test'), '/test/author-name/module-name');
        $this->assertEquals($this->module_info->getVendorPath(), '/tmp/author-name/module-name');
    }

    public function testGetSourcePath()
    {
        $this->assertEquals($this->module_info->getSourcePath()->getPath(), '/tmp/author-name/module-name');
    }

    public function testGetEtcPath()
    {
        $this->assertEquals($this->module_info->getEtcPath()->getPath(), '/tmp/author-name/module-name/etc');
    }

    public function testCheckIfDirectoryExists()
    {
        $directory = $this->createMock(Directory::class);
        $directory->method('exists')->willReturn(false);

        $this->module_info_mocked->setMock($directory);
        $this->assertTrue($this->module_info_mocked->checkIfDirectoryExists());

        $directory = $this->createMock(Directory::class);
        $directory->method('exists')->willReturn(true);

        $this->module_info_mocked->setMock($directory);
        $this->expectException(\InvalidArgumentException::class);
        $this->module_info_mocked->checkIfDirectoryExists();
    }


}

class ModuleInfoTestInstance extends ModuleInfo
{
    private $mock;

    public function setMock($path)
    {
        $this->mock = $path;
    }

    public function getLocalPath()
    {
        return $this->mock;
    }
    public function getVendorPath($vendor_path = null)
    {
        return $this->mock;
    }
}

