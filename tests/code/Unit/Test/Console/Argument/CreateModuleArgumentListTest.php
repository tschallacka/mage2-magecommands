<?php namespace Tschallacka\MageCommands\Tests\Code\Unit\Test\Console\Argument;

use PHPUnit\Framework\TestCase;
use Tschallacka\MageCommands\Console\Argument\CreateModuleArgumentList;
use Tschallacka\MageCommands\Console\Argument\CreateModuleCommandArgument;

/**
 *  test case.
 */
class CreateModuleArgumentListTest extends TestCase
{
    protected $argument_name = 'test';
    
    protected $argument_name_need_optional = 'test:optional';
    protected $argument_name_need_required = 'test:required';
    protected $argument_name_need_invalid = 'test:invalid';
    
    protected $argument_name_need_optional_type_string = 'test:optional:string';
    protected $argument_name_need_required_type_string = 'test:required:string';
    protected $argument_name_need_invalid_type_string = 'test:invalid:string';
    
    
    protected $argument_name_need_optional_type_array = 'test:optional:array';
    protected $argument_name_need_required_type_array = 'test:required:array';
    protected $argument_name_need_invalid_type_array = 'test:invalid:array';
    
    protected $argument_name_need_optional_type_invalid = 'test:optional:invalid';
    protected $argument_name_need_required_type_invalid = 'test:required:invalid';
    
    protected $argument_name_need_optional_type_string_description = 'test:optional:string:text';
    protected $argument_name_need_required_type_string_description = 'test:required:string:text';
    protected $argument_name_need_invalid_type_string_description = 'test:invalid:string:text';
    
    protected $argument_name_need_optional_type_array_description = 'test:optional:array:text';
    protected $argument_name_need_required_type_array_description = 'test:required:array:text';
    protected $argument_name_need_invalid_type_array_description = 'test:invalid:array:text';
    
    protected $argument_name_need_optional_type_invalid_description = 'test:optional:invalid:text';
    protected $argument_name_need_required_type_invalid_description = 'test:required:invalid:text';
    
    protected $argument_name_need_invalid_type_invalid_description = 'test:invalid:invalid:text';
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        
    }
    

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    /**
     * get a list of all test data and expected configurations
     * @return \Tschallacka\MageCommands\Tests\Code\Unit\Test\Console\Argument\ArgumentTestConfig[]
     */
    protected function getTestList() 
    {
        $invalid = 'invalid';
        $description = 'text';
        $empty_description = '';
        $testlist = [];
        $testlist[] = new ArgumentTestConfig($this->argument_name, $this->argument_name, CreateModuleCommandArgument::OPTIONAL, CreateModuleCommandArgument::STRING, $empty_description);
        
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_optional, $this->argument_name, CreateModuleCommandArgument::OPTIONAL, CreateModuleCommandArgument::STRING, $empty_description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_required, $this->argument_name, CreateModuleCommandArgument::REQUIRED, CreateModuleCommandArgument::STRING, $empty_description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_invalid, $this->argument_name, $invalid, CreateModuleCommandArgument::STRING, $empty_description, \InvalidArgumentException::class);
        
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_optional_type_string, $this->argument_name, CreateModuleCommandArgument::OPTIONAL, CreateModuleCommandArgument::STRING, $empty_description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_required_type_string, $this->argument_name, CreateModuleCommandArgument::REQUIRED, CreateModuleCommandArgument::STRING, $empty_description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_invalid_type_string, $this->argument_name, $invalid, CreateModuleCommandArgument::STRING, $empty_description, \InvalidArgumentException::class);
        
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_optional_type_array, $this->argument_name, CreateModuleCommandArgument::OPTIONAL, CreateModuleCommandArgument::ARRAY, $empty_description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_required_type_array, $this->argument_name, CreateModuleCommandArgument::REQUIRED, CreateModuleCommandArgument::ARRAY, $empty_description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_invalid_type_array, $this->argument_name, $invalid, CreateModuleCommandArgument::ARRAY, $empty_description, \InvalidArgumentException::class);
        
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_optional_type_invalid, $this->argument_name, CreateModuleCommandArgument::OPTIONAL, $invalid, $empty_description, \InvalidArgumentException::class);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_required_type_invalid, $this->argument_name, CreateModuleCommandArgument::REQUIRED, $invalid, $empty_description, \InvalidArgumentException::class);
        
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_optional_type_string_description, $this->argument_name, CreateModuleCommandArgument::OPTIONAL, CreateModuleCommandArgument::STRING, $description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_required_type_string_description, $this->argument_name, CreateModuleCommandArgument::REQUIRED, CreateModuleCommandArgument::STRING, $description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_invalid_type_string_description, $this->argument_name, $invalid, CreateModuleCommandArgument::STRING, $description, \InvalidArgumentException::class);
        
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_optional_type_array_description, $this->argument_name, CreateModuleCommandArgument::OPTIONAL, CreateModuleCommandArgument::ARRAY, $description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_required_type_array_description, $this->argument_name, CreateModuleCommandArgument::REQUIRED, CreateModuleCommandArgument::ARRAY, $description);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_invalid_type_array_description, $this->argument_name, $invalid, CreateModuleCommandArgument::ARRAY, $description, \InvalidArgumentException::class);
        
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_optional_type_invalid_description, $this->argument_name, CreateModuleCommandArgument::OPTIONAL, $invalid, $description, \InvalidArgumentException::class);
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_required_type_invalid_description, $this->argument_name, CreateModuleCommandArgument::REQUIRED, $invalid, $description, \InvalidArgumentException::class);
        
        $testlist[] = new ArgumentTestConfig($this->argument_name_need_invalid_type_invalid_description, $this->argument_name, $invalid, $invalid, $description, \InvalidArgumentException::class);
        
        return $testlist;
    }
    
    public function testAddArgumentFromInputString()
    {
        $testlist = $this->getTestList();
        
        foreach($testlist as $item) {
            $list = new CreateModuleArgumentList();
            if($item->expect_exception) {
                try {
                    $list->addArgumentFromInputString($item->argument_string);
                }
                catch(\Exception $e) {
                    if($e instanceof $item->expect_exception) {
                        $this->assertEquals(1, 1);
                        continue;
                    }
                }   
                $this->fail('Exception was not thrown for invalid argument input '.$item->argument_string);
            }
            try {
                $result = $list->addArgumentFromInputString($item->argument_string);
            }
            catch(\Exception $ex) {
                $this->fail('Unexpected exception in '.$item->argument_string . ' > '. $ex->getMessage());
            }
            $this->assertEquals($item->expected_name, $result->getName(), 'Failed assertion name for '.$item->argument_string);
            $this->assertEquals($item->expected_need, $result->getNeed(), 'Failed assertion need for '.$item->argument_string);
            $this->assertEquals($item->expected_type, $result->getType(), 'Failed assertion type for '.$item->argument_string);
            $this->assertEquals($item->expected_description, $result->getDescription(), 'Failed assertion description for '.$item->argument_string);
        }
    }
}
class ArgumentTestConfig 
{
    public $argument_string;
    public $expected_name;
    public $expected_need;
    public $expected_type;
    public $expected_description;
    public $expect_exception;
    /**
     * set test parameters to loop through for testing verification
     * @param string $argument input argument string
     * @param string $name
     * @param string $need <optional|required|invalid>
     * @param string $type <string|array|invalid>
     * @param string $description
     * @param string $exception exception class to expect, leave null for not to test
     */
    public function __construct($argument, $name, $need, $type, $description, $exception = null)
    {
        $this->argument_string = $argument;
        $this->expected_name = $name;
        $this->expected_need = $need;
        $this->expected_type = $type;
        $this->expected_description = $description;
        $this->expect_exception = $exception;
    }
}