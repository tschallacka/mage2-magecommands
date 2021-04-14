<?php namespace Tschallacka\MageCommands\Tests\Code;

use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use Tschallacka\MageCommands\Module\ModuleInfo;
use InvalidArgumentException;
/**
 *  test case.
 */
class ModuleInfoTest extends TestCase
{
    /**
     * The object manager
     * @var $module_info \Tschallacka\MageCommands\Module\ModuleInfo
     */
    protected $module_info;
    
    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        $this->module_info = $subject;
    }
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testFailTestPath()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->module_info->fail
    }
    
}

