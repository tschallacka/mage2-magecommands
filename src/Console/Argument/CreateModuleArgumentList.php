<?php namespace Tschallacka\MageCommands\Console\Argument;
use Tschallacka\MageCommands\Console\Argument\CreateModuleCommandArgument as Argument;
class CreateModuleArgumentList 
{
    private $arguments;
    
    public function __construct()
    {
        $this->arguments = [];    
    }
    
    /**
     * Turns an input string in the format "argument_name:<optional|required>:<string|array>:help description"
     * into a CreateModuleCommandArgument and returns it, and adds it to the internal list.
     * @param string $input
     * @return \Tschallacka\MageCommands\Console\Argument\CreateModuleCommandArgument
     */
    public function addArgumentFromInputString($input)
    {
        $pos = strpos($input, ':');
        if($pos === false) {
            $argument = new Argument($input);
            $this->addArgument($argument);
            return $argument;
        }
        $name = substr($input, 0, $pos);
        $oldpos = $pos + 1;
        $pos = strpos($input, ':', $oldpos);
        
        if($pos === false) {
            $need = substr($input, $oldpos, strlen($input) - $oldpos);
            $argument = new Argument($name, $need);
            $this->addArgument($argument);
            return $argument;
        }
        $need = substr($input, $oldpos, $pos - $oldpos);
        $oldpos = $pos + 1;
        $pos = strpos($input, ':', $oldpos);
        if($pos === false) {
            $type = substr($input, $oldpos, strlen($input) );
            $argument = new Argument($name, $need, $type);
            $this->addArgument($argument);
            return $argument;
        }
        $type = substr($input, $oldpos, $pos - $oldpos);
        $oldpos = $pos + 1;
        
        $description = substr($input, $oldpos, strlen($input));
        $argument = new Argument($name, $need, $type, $description);
        $this->addArgument($argument);
        return $argument;
        
    }
    
    
    
    public function addArgument(Argument $item) 
    {
        $this->arguments[] = $item;        
    }
    
    public function validate() 
    {
        $last = count($this->arguments) - 1;
        foreach($this->arguments as $key => $value) {
            if($value->getType() == CreateModuleCommandArgument::ARRAY && $key < $last) {
                $value->invalidArgument('Invalid argument type array for what is not the last argument but argument #'.($key+1).' of '.$last.'. Only last argument of command can be type array');
            }                
        }
    }
}