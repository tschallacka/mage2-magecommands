<?php
namespace Tschallacka\MageCommands\Console\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\FullModuleList;

use Tschallacka\MageCommands\Configuration\Config;
use Tschallacka\MageCommands\Module\ModuleInfo;
use Tschallacka\MageRain\Helper\File\Format\Composer;
use Tschallacka\MageRain\Helper\File\TemplateFile;

/**
 * Class CreateModule
 */
class CreateModule extends Command
{
    const CREATE_MODULE_COMMAND = 'tsch:module:create';
    
    /**
     * Name argument
     */
    const MODULE_NAME_ARGUMENT = 'name';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::CREATE_MODULE_COMMAND)
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
            throw new InvalidArgumentException('No module name in the format of AuthorName_ModuleName provided. Please use bin/magento '.self::CREATE_MODULE_COMMAND. ' AuthorName_ModuleName');
        }
        $name = array_shift($args);
        if(!$this->checkModuleNameValidity($name)) return; 
        
        $module = new ModuleInfo($name);
        
        if(!$module->checkIfDirectoryExists()) return;
        $output->writeln("Creating directory for module ".$input);
        
        $dir = $module->getLocalPath();
        $dir->create();
        $composer = new Composer($dir->getPath('composer.json'));
        $composer->initializeEmptyFile();
        $composer->addAuthor($module->getAuthorName());
        $composer->description = "A magento2 module";
        $composer->put('autoload.psr-4', [
            $module->getNameSpace().'\\' => 'src'
        ]);
        $composer->put('autoload.files', [
            'registration.php'
        ]);
        $composer->type = "magento2-module";
        $composer->name = $module->getPackageName();
        $composer->save();
        $sourcepath = $module->getSourcePath();
        $sourcepath->create();
        $config_dir = $sourcepath->createChildDirectory('Configuration');
        
        $template = new TemplateFile(__DIR__.'/template/configuration.txt', $config_dir->getPath('Config.php'));
        $template->load();
        $template->save([$module->getNameSpace(), $module->getMagentoModuleName()]);
        
        $template = new TemplateFile(__DIR__.'/template/registration.txt', $dir->getPath('registration.php'));
        $template->load();
        $template->save([$module->getNameSpace()]);
        
        $etcpath = $module->getEtcPath();
        $etcpath->create();
        
        $template = new TemplateFile(__DIR__.'/template/module.txt', $etcpath->getPath('module.xml'));
        $template->load();
        $template->save([$module->getMagentoModuleName()]);
        
        $template = new TemplateFile(__DIR__.'/template/di.txt', $etcpath->getPath('di.xml'));
        $template->load();
        $template->save([]);
        
        
        
        $output->writeln('<info>Hello ' . $name . '!</info>');
    }
    
    
    protected function checkModuleNameValidity($name) 
    {
        if (is_null($name) || empty($name)) {
            throw new \InvalidArgumentException('Argument ' . self::MODULE_NAME_ARGUMENT . ' is missing.');
        }
        $pos = strpos($name, '_');
        if($pos === false || $pos === 0 || $pos == strlen($name) - 1) {
            throw new \InvalidArgumentException('Argument ' . self::MODULE_NAME_ARGUMENT . ' needs to be in format AuthorName_ModuleName instead "'.$name.'" was provided.');
        }
        $list = ObjectManager::getInstance()->create(FullModuleList::class);
        if($list->has($name)) {
            throw new \InvalidArgumentException('The module '. $name .' already exists. Please use another module name.');
        }
        
        
        return true;
    }
    
    
        
}