<?php namespace Tschallacka\MageCommands\Console\Argument;

use Tschallacka\MageRain\Helper\Text\Str;

class CreateModuleCommandArgument 
{
    const REQUIRED = "required";
    const OPTIONAL = "optional";
    const STRING = "string";
    const ARRAY = "array";
    const NONE = 'none';
    const INPUT_ARGUMENT = "argument";
    const INPUT_OPTION = "option";
    
    private $name;
    private $need;
    private $type;
    private $description;
    private $argument_type;
    
    /**
     * 
     * @param string $name
     * @param string $need
     * @param string $type
     * @param string $description
     */
    public function __construct($name, $need = self::OPTIONAL, $type = self::STRING, $description = '') 
    {
        
        $this->name = $this->validateName($this->setArgumentType($name));
        
        $this->need = $this->validateNeed($need);
        
        $this->type = $this->validateType($type);
        
        $this->description = $description;
    }
    
    public function setArgumentType($name) 
    {
        
        if(strpos($name, '==') === 0) {
            $this->argument_type = self::INPUT_OPTION;
            return substr($name, 2);
        }
        
        $this->argument_type = self::INPUT_ARGUMENT;
        return $name;
    }
    
    public function isOption()
    {
        return $this->argument_type === self::INPUT_OPTION;
    }
    
    public function isArgument()
    {
        return $this->argument_type === self::INPUT_ARGUMENT;
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
            $this->invalidArgument('Invalid value for the need of the parameter. Valid values are "'.self::REQUIRED.'" and "'. self::OPTIONAL.'"(default). Provided value is "'. $need.'" ');
        }
        return $need;
    }
    
    public function validateType($type) 
    {
        if(is_null($type) || empty(trim($type))) {
            return self::STRING;
        }
        $type = strtolower(trim($type));
        if($type != self::STRING && $type != self::ARRAY && $type != self::NONE) {
            $this->invalidArgument('Invalid value for the type of the parameter. Valid values are "'.self::STRING.'", '.self::NONE.' and "'. self::ARRAY.'". Provided value is '. $type);
        }
        return $type;
    }
    
    public function getInputArgumentTypedFormat()
    {
        if($this->isArgument()) {
            return "new InputArgument(
                    self::%s,
                    %s,
                    \"%s\")";
        }
        else {
            return "new InputOption(
                    self::%s,
                    null, /** shortcut **/
                    %s,
                    \"%s\",
                    null) /** default value%s **/";
        }
    }
    
    public function getNeedArgument()
    {
        if($this->isArgument()) {
            return $this->getNeed() == self::OPTIONAL ? 'InputArgument::OPTIONAL' : 'InputArgument::REQUIRED';
        }
        
        if($this->getNeed() == self::REQUIRED) {
            return 'InputOption::VALUE_REQUIRED';
        }
        return $this->getType() == self::NONE ? '': 'InputOption::VALUE_OPTIONAL';
        
    }
    
    public function getTypeArgument()
    {
        if($this->isArgument()) {
            return $this->getType() == self::ARRAY ? ' | InputArgument::IS_ARRAY' : '';
        }
        if($this->getType() == self::ARRAY)
        {
            return ' | InputOption::VALUE_ARRAY';
        }
        if($this->getType() == self::NONE)
        {
            return 'InputOption::VALUE_NONE';
        }
    }
    
    public function createInputArgumentText()
    {
        $str = $this->getInputArgumentTypedFormat();
        $need = $this->getNeedArgument();
        $array = $this->getTypeArgument();
        $args = [
            $this->getConstantName(),
            $need . $array,
            $this->getDescription(),
            $this->getType() == self::NONE ? ', must be null for InputOption::VALUE_NONE':''
        ];
        
        return vsprintf($str, $args);
        
    }
    
    public function createInputArgumentConstant() 
    {
        $str = "/** input type: %s(%s), value type: %s **/
    const %s = '%s';";
        $args = [
            $this->argument_type,
            $this->getNeed() . ($this->isOption() && $this->getNeed() == self::REQUIRED ? ' to have value when used':''),
            $this->getType(),
            $this->getConstantName(),
            $this->getName()
        ];
        return vsprintf($str, $args);
    }
    
    public function createInputArgumentValueHolder() 
    {
        $str = "

    /**
     * @var %s \$%s provided value for %s input %s %s 
     */
    protected \$%s;";
        $args = [
            ($this->isOption() && $this->getType() == self::NONE ? 'boolean':'string' . ($this->isArray() ? '[]' : '')),
            $this->getName(),
            ($this->isOption() && $this->getNeed() == self::REQUIRED ? 'that is required to be provided(--option=value)' : $this->getNeed()),
            $this->argument_type,
            ($this->isOption() ? '--' : '').$this->getName(),
            $this->getName()
        ];
        return vsprintf($str, $args);
    }
    
    public function createInputArgumentValueAssigner()
    {
        $str = "
        \$this->%s = \$input->get%s(self::%s);";
        $args = [
            $this->getName(),
            ucfirst($this->argument_type),
            $this->getConstantName()
        ];
        return vsprintf($str, $args);
    }
    
    public function getConstantName()
    {
        return strtoupper($this->argument_type.'_'.$this->getName());
    }
    
    public function invalidArgument($custom_message)
    {
        throw new \InvalidArgumentException($custom_message . ' in '.$this->name. ':'.$this->need.':'.$this->type.':'.$this->description);
    }
    
    public function isArray()
    {
        return $this->getType() === self::ARRAY;
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