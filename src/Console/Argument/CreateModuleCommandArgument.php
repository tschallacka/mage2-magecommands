<?php namespace Tschallacka\MageCommands\Console\Argument;

use Tschallacka\MageRain\Helper\Text\Str;

class CreateModuleCommandArgument 
{
    const REQUIRED = "required";
    const OPTIONAL = "optional";
    const STRING = "string";
    const ARRAY = "array";
    
    private $name;
    private $need;
    private $type;
    private $description;
    
    public function __construct($name, $need = self::OPTIONAL, $type = self::STRING, $description = '') 
    {
        $this->name = $this->validateName($name);
        
        $this->need = $this->validateNeed($need);
        
        $this->type = $this->validateType($type);
        $this->description = $description;
    }
    
    public function validateName($name) 
    {
        if(is_null($name) || empty(trim($name))) {
            $this->invalidArgument('No name for the argument was provided. You need to provide a name');
        }
        return Str::snake(trim($name));
    }
    
    public function validateNeed($need)
    {
        if(is_null($need) || empty(trim($need))) {
            return self::OPTIONAL;
        }
        $need = strtolower(trim($need));
        if($need != self::REQUIRED && $need != self::OPTIONAL) {
            $this->invalidArgument('Invalid value for the need of the parameter. Valid values are "'.self::REQUIRED.'" and "'. self::OPTIONAL.'". Provided value is "'. $need.'" ');
        }
        return $need;
    }
    
    public function validateType($type) 
    {
        if(is_null($type) || empty(trim($type))) {
            return self::STRING;
        }
        $type = strtolower(trim($type));
        if($type != self::STRING && $type != self::ARRAY) {
            $this->invalidArgument('Invalid value for the type of the parameter. Valid values are "'.self::STRING.'" and "'. self::ARRAY.'". Provided value is '. $type);
        }
        return $type;
    }
    
    public function createInputArgumentText()
    {
        $str = "
         new InputArgument(
            '%s',
            %s,
            \"%s\")";
        
        $need = $this->getNeed() == self::STRING ? 'InputArgument::STRING' : 'InputArgument::ARRAY';
        $array = $this->getType() == self::ARRAY ? ' | InputArgument::ARRAY' : '';
        
        return sprintf($str, [
            $this->getName(),
            $need . $array,
            $this->getDescription()            
        ]);
    }
    
    public function getConstantName()
    {
        return strtoupper($this->getName());
    }
    
    public function invalidArgument($custom_message)
    {
        throw new \InvalidArgumentException($custom_message . ' in '.$this->name. ':'.$this->need.':'.$this->type.':'.$this->description);
    }
    
    public function getName() 
    {
        return $this->name;
    }
    
    public function getNeed()
    {
        return $this->need;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
}