<?php namespace Tschallacka\MageCommands\Console\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tschallacka\MageCommands\Configuration\Config;

class CreateModuleCommand extends Command 
{

    const CREATE_MODULE_COMMAND_COMMAND = 'tsch:module:create-command';
    
    
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
        $args = $input->getArgument(self::MODULE_NAME_ARGUMENT);
        echo $output->writeln($args);
        $cmd = $input->getArgument(self::COMMAND_NAME_ARGUMENT);
        echo $output->writeln($cmd);
        $arguments = $input->getArgument(self::COMMAND_ARGUMENTS);
        /*if(!is_array($args) || count($args) < 1) {
        throw new InvalidArgumentException('No module name in the format of AuthorName_ModuleName provided. Please use bin/magento '.self::CREATE_MODULE_COMMAND. ' AuthorName_ModuleName');
        }*/
        
    }
    
}