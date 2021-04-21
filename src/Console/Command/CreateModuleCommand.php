<?php namespace Tschallacka\MageCommands\Console\Command;

use InvalidArgumentException;
use Magento\Setup\Console\Command\DiCompileCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tschallacka\MageCommands\Configuration\Config;
use Tschallacka\MageCommands\Console\Argument\CreateModuleArgumentList;
use Tschallacka\MageCommands\Module\ModuleInfo;
use Tschallacka\MageRain\Helper\Text\Str;
use Tschallacka\MageRain\File\TemplateFile;
use Tschallacka\MageCommands\Console\Argument\CreateModuleCommandArgument;


class CreateModuleCommand extends Command 
{

    const CREATE_MODULE_COMMAND_COMMAND = 'tsch:module:create:command';
    
    
    /**
     * Name argument
     */
    const MODULE_NAME_ARGUMENT = 'module_name';
    
    const COMMAND_NAME_ARGUMENT = 'command_name';
    
    const COMMAND_ARGUMENTS = 'command arguments';
    

    
    public function __construct($name = null, Config $config)
    {
        $this->config = $config;
        
        parent::__construct($name);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::CREATE_MODULE_COMMAND_COMMAND)
        ->setDescription('Create a command in a module in the local folder. This command will fail if the module isn\'t present in the local development folder in '.BP.'/'.$this->config->getLocalPath())
        ->setDefinition([
            new InputArgument(
                self::MODULE_NAME_ARGUMENT,
                InputArgument::REQUIRED,
                'Module name in the format AuthorName_ModuleName. If this doesn\'t exist create it with '.CreateModule::CREATE_MODULE_COMMAND
                ),
            new InputArgument(
                self::COMMAND_NAME_ARGUMENT,
                InputArgument::REQUIRED,
                'the name of the command you wish to generate. if you provide "command" the command will become "command", if you provide "command:someting" the command will become "command:something", etc...'
                ),
            new InputArgument(
                self::COMMAND_ARGUMENTS,
                InputArgument::OPTIONAL | INPUTARGUMENT::IS_ARRAY,
                'Space seperated list of arguments for the command in the format of "argument_name:<optional|required>:<string|array>:help description". For example bin/magento '.self::CREATE_MODULE_COMMAND_COMMAND.' Tschallacka_TestModule hello "type:required:string:Say hello to whomever is provided as argument here" "greetings:optional:array:Add these words to the greeting message"'
            ),
        ]);
        
        parent::configure();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module = $this->getModuleInfoFromValidArgument($input);
        
        $command = $this->getCommand($input, $module);

        $this->validateCommandName($module, $command);
        
        $classname = $this->getQualifiedClassFromCommandName($command, $module);
        
        $path = $this->getPathFromCommandName($command, $module);
        
        $arguments = $this->getArgumentList($input, $module);
        $const_argument_name_collection = [];
        $argument_collection = [];
        $const_argument_value_holders = [];
        $const_argument_value_assigners = [];
        foreach($arguments->getArguments() as $argument) {
            $argument_collection[] = $argument->createInputArgumentText();
            $const_argument_name_collection[] = $argument->createInputArgumentConstant();
            $const_argument_value_holders[] = $argument->createInputArgumentValueHolder();
            $const_argument_value_assigners[] = $argument->createInputArgumentValueAssigner();
        }
        $command_executor = str_replace('_',':',strtolower($module->getMagentoModuleName())).':'.$command;
        $this->writeTemplatedFile($output, 'Command.txt', $path, [
            $module->getCommandNameSpace(), 
            $this->getClassNameFromCommandName($command),
            strtoupper($command),
            $command_executor,
            implode("\n    ", $const_argument_name_collection),
            implode('', $const_argument_value_holders),
            strtoupper($command),
            implode(",\n            ", $argument_collection),
            implode('', $const_argument_value_assigners)
        ]);
        $output->writeln("Modifying <fg=yellow>".$module->getDiPath() . "</> to register the new command");
        $this->addCommand($module, $command, $classname);
        $output->writeln('<fg=white;bg=green>Command '.$command.' successfully registered. '.
                        'Please run</> <fg=yellow;bg=black>bin/magento setup:di:compile </> <fg=white;bg=green>'.
                        'to activate the command.</>'
            );
        $output->writeln("To execute the command run <fg=yellow;bg=black>bin/magento $command_executor</>");
        
    }
    
    /**
     * Checks if the given command name is already registered.
     * if it's registered it will throw an exception
     * @param \Tschallacka\MageCommands\Module\ModuleInfo $module
     * @param string $command
     * @throws \InvalidArgumentException
     */
    public function validateCommandName(ModuleInfo $module, $command) 
    {
        if($this->isCommandRegisteredInDi($module, $command)) {
            throw new \InvalidArgumentException("Command $command is already registered in ".$module->getDiPath());
        }
    }
    
    public function isCommandRegisteredInDi(ModuleInfo $module, $command) 
    {
        $commandlist = 'Magento\\Framework\\Console\\CommandList';
        $di = $module->getDiDocument();
        $path = "//config/type[@name='$commandlist']/arguments/argument[@name='commands']/item[@name='$command']";
        $xpath = new \DOMXPath($di);
        return $xpath->query($path)->length > 0;
    }
    
    public function addCommand(ModuleInfo $module, $command, $qualified_classpath)
    {
        $commandlist = 'Magento\\Framework\\Console\\CommandList';
        $di = $module->getDiDocument();
        $modified = false; 
        $xpath = new \DOMXPath($di);
        $path = '//config';
        $config = $xpath->query($path)->item(0);
        
        $path .= "/type[@name='$commandlist']";
        $type = $xpath->query($path);
        if($type->length == 0) {
            $type = $di->createElement('type');
            $type->setAttribute('name', $commandlist);
            $config->appendChild($type); 
            $modified = true;
        }
        else {
            $type = $type->item(0);
        }
        $path .= "/arguments";
        $arguments = $xpath->query($path);
        if($arguments->length == 0) {
            $arguments = $di->createElement('arguments');
            $type->appendChild($arguments);
            $modified = true;
        }
        else {
            $arguments = $arguments->item(0);
        }
        $path .= "/argument[@name='commands']";
        $argument = $xpath->query($path);

        if($argument->length == 0) {
            $argument = $di->createElement('argument');
            $argument->setAttribute('name', 'commands');
            $argument->setAttribute('xsi:type', 'array');
            $arguments->appendChild($argument);
            $modified = true;
        }
        else {
            $argument = $argument->item(0);
        }
        $path .= "/item[@name='$command']";
        $item = $xpath->query($path);
        if($item->length == 0) {
            $item = $di->createElement('item');
            $item->setAttribute('name', $command);
            $item->setAttribute('xsi:type', 'object');
            $text = $di->createTextNode($qualified_classpath);
            $item->appendChild($text);
            $argument->appendChild($item);
            $modified = true;
        }
        else {
            $item = $item->item(0);
        }
        if($modified) {
            /**
             * Load in fresh dom document so we get pretty printing
             */
            $fresh_dom = new \DOMDocument();
            $fresh_dom->preserveWhiteSpace = false;
            $fresh_dom->formatOutput = true;
            $fresh_dom->loadXML($di->saveXML());
            $fresh_dom->save($module->getDiPath());    
        }
        
    }
    
    
    
    protected function writeTemplatedFile(OutputInterface $output, $filename, $destination_path, $arguments=[])
    {
        $output->writeln("Creating <fg=yellow>$destination_path</>");
        $template = new TemplateFile(__DIR__.'/template/'.$filename, $destination_path);
        $template->load();
        $template->save($arguments);
    }
    
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return \Tschallacka\MageCommands\Console\Argument\CreateModuleArgumentList
     */
    public function getArgumentList(InputInterface $input)
    {
        $arguments = $input->getArgument(self::COMMAND_ARGUMENTS);
        $list = new CreateModuleArgumentList();
        foreach($arguments as $argument) {
            $list->addArgumentFromInputString($argument);
        }
        $list->validate();
        return $list;
    }
    
    public function getPathFromCommandName($command, ModuleInfo $module) 
    {
         $commandDir = $module->getCommandDir();
         return $commandDir->getPath($this->getClassNameFromCommandName($command).'.php');
    }
    
    /**
     * Get a qualified class name from a input command
     * @param string $command
     * @param ModuleInfo $module
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getQualifiedClassFromCommandName($command, ModuleInfo $module)
    {
        $class = $module->getCommandNameSpace().'\\'.$this->getClassNameFromCommandName($command);
        
        if(class_exists($class)) {
            throw new \InvalidArgumentException('Class ' . $class . ' already exists, command "'.$command.'" could not be generated.');
        }
        return $class;
    }
    
    public function getClassNameFromCommandName($command) 
    {
        return Str::studly($command);    
    }
    
    public function getCommand(InputInterface $input) 
    {
        $raw = $input->getArgument(self::COMMAND_NAME_ARGUMENT);
        $command = Str::snake($raw);
        return $command;
    }
    
    
    /**
     * Creates a ModuleInfo from a given argument in the form of AuthorName_PluginName
     * after validating that the given argument is in the correct format and the plugin
     * does not exist at this moment.
     * @param InputInterface $input
     * @throws InvalidArgumentException
     * @return \Tschallacka\MageCommands\Module\ModuleInfo
     */
    public function getModuleInfoFromValidArgument(InputInterface $input)
    {
        $name = $input->getArgument(self::MODULE_NAME_ARGUMENT);
        
        $module = new ModuleInfo($name, $this->config);
        $module->failIfNotInDevDir();
        
        return $module;
    }
    
}