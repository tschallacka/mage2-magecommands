<?php
namespace Tschallacka\MageCommands\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\FullModuleList;
/**
 * Class CreateModule
 */
class CreateModule extends Command
{
    /**
     * Name argument
     */
    const MODULE_NAME_ARGUMENT = 'name';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('tsch:module:create')
            ->setDescription('Create a module in the app/code folder')
            ->setDefinition([
                new InputArgument(
                    self::MODULE_NAME_ARGUMENT,
                    InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                    'Module name in the format AuthorName_ModuleName'
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
        if(!is_array($args) || count($args) < 1) {
            throw new InvalidArgumentException('No module name in the format of AuthorName_ModuleName provided');
        }
        $name = array_shift($args);
        if(!$this->checkModuleNameValidity($name)) return;
        
        $output->writeln('<info>Hello ' . $name . '!</info>');
    }
    
    protected function checkModuleNameValidity($name) 
    {
        if (is_null($name)) {
            throw new \InvalidArgumentException('Argument ' . self::MODULE_NAME_ARGUMENT . ' is missing.');
        }
        $pos = strpos($name, '_');
        if($pos === false || $pos === 0 || $pos == strlen($name) - 1) {
            throw new \InvalidArgumentException('Argument ' . self::MODULE_NAME_ARGUMENT . ' needs to be in format AuthorName_ModuleName instead "'.$name.'" was provided.');
        }
        $list = ObjectManager::getInstance()->create(FullModuleList::class);
        if($list->has($name)) {
            throw new \InvalidArgumentException('The module '. $name .' already exists. Please use another name.');
        }
        return true;
    }
        
}