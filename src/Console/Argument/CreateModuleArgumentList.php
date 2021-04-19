<?php namespace Tschallacka\MageCommands\Console\Argument;

class CreateModuleArgumentList 
{
    private $arguments;
    
    public function __construct()
    {
        $this->arguments = [];    
    }
    
    public function addArgumentFromInputString($input)
    {
        $input = explode(':', $input);
        
    }
    
    
    
    public function addArgument(CreateModuleCommandArgument $item) 
    {
        $this->arguments[] = $item;        
    }
    
    public function validate() 
    {
        $last = count($this->arguments) - 1;
        foreach($arguments as $key => $value) {
            if($value->getType() == CreateModuleCommandArgument::ARRAY && $key != $last) {
                $value->invalidArgument('Invalid argument type array for what is not the last argument. Only last argument of command can be type array');
            }                
        }
    }
}