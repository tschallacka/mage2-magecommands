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
        $this->name = $name;
        $this->validateNeed($need);
        $this->need = strtolower($need);
        $this->validateType($type);
        $this->type = strtolower($type);
        $this->description = $description;
    }
    
    public function validateNeed($need)
    {
        if($need != self::REQUIRED && $need != self::OPTIONAL) {
            $this->invalidArgument('Invalid value for the need of the parameter. Valid values are "'.self::REQUIRED.'" and "'. self::OPTIONAL.'". Provided value is '. sneed);
        }
    }
    
    public function validateType($type) 
    {
        if($type != self::STRING && $type != self::ARRAY) {
            $this->invalidArgument('Invalid value for the type of the parameter. Valid values are "'.self::STRING.'" and "'. self::ARRAY.'". Provided value is '. $type);
        }
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