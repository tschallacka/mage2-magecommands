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
use Tschallacka\MageRain\Helper\File\Directory;
/**
 * Class CreateModule
 */
class CreateModule extends Command
{
    const CREATE_MODULE_COMMAND = 'tsch:module:create';
    
    const SETUP_AFTER_CREATE_COMMAND = '<fg=white;bg=cyan>composer require %s && bin/magento module:enable %s && bin/magento setup:upgrade && bin/magento setup:di:compile && bin/magento cache:clean</>';
    
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
        
        $output->writeln("Written composer.json to ".$module->getLocalPath().'/composer.json');
        $output->writeln("Creating default plugin files");
        
        $output->writeln("Creating Configuration/Config.php");
        $sourcepath = $module->getSourcePath();
        $sourcepath->create();
        $config_dir = $sourcepath->createChildDirectory('Configuration');
        
        $template = new TemplateFile(__DIR__.'/template/configuration.txt', $config_dir->getPath('Config.php'));
        $template->load();
        $template->save([$module->getNameSpace(), $module->getMagentoModuleName()]);
        
        $output->writeln("Creating registration.php");
        $template = new TemplateFile(__DIR__.'/template/registration.txt', $dir->getPath('registration.php'));
        $template->load();
        $template->save([$module->getNameSpace()]);
        
        $output->writeln("Creating etc/module.xml");
        $etcpath = $module->getEtcPath();
        $etcpath->create();
        
        $template = new TemplateFile(__DIR__.'/template/module.txt', $etcpath->getPath('module.xml'));
        $template->load();
        $template->save([$module->getMagentoModuleName()]);
        
        $output->writeln("Creating etc/di.xml");
        $template = new TemplateFile(__DIR__.'/template/di.txt', $etcpath->getPath('di.xml'));
        $template->load();
        $template->save([]);
        
        $output->writeln("Modifying ".BP.'/composer.json');
        
        $magento_composer = new Composer(BP . '/composer.json');
        $magento_composer->load();
        $require = $magento_composer->get('require');
        
        if(!(array_key_exists($module->getPackageName(), $require))) {
            $require[$module->getPackageName()] = '^1.0';
            $magento_composer->require = $require;
        }
        
        $repositories = $magento_composer->get('repositories', []);
        $result = array_filter($repositories, function($item) use ($module) { return $item['type'] == 'path' && $item['url'] == $module->getLocalPath(); });
        if(!count($result)) {
            $repositories[] = [
                'type' => 'path',
                'url' => $module->getLocalPath()->getPath()
            ];
            $magento_composer->repositories = $repositories;
        }
        $magento_composer->save();
        
        
        $output->writeln('<fg=white;bg=green>          Module '.$module->getMagentoModuleName() . ' has been successfully generated.                 </>');
        $output->writeln('<fg=white;bg=red>===============================================================================</>');
        $output->writeln('<fg=white;bg=red>          Read the follwing information carefully!!!!                          </>');
        $output->writeln('<fg=white;bg=red>===============================================================================</>');
        $output->writeln("You can find the package source files for editing in the editor of your choice");
        $output->writeln("in <fg=yellow>". $module->getLocalPath()->getPath() . '</>');
        $output->writeln("A symlink to this directory will be created in <fg=yellow>" . Config::getVendorDir() . "</> after");
        $output->writeln("running the commands at the end of this output.");
        $output->writeln("A new repository was added to <fg=yellow>" . BP . '/composer.json</> enabling the symlink.');
        $output->writeln('If you do not wish to distribute this plugin via a symlinked repository you');
        $output->writeln('will need to make the package available via alternative means(Github, Magento)');
        $output->writeln("Place any PHP classes for your namespace <fg=yellow>". $module->getNameSpace() . '</> in the <fg=yellow>src/</> directory. ');
        $output->writeln('Run <fg=white;bg=cyan>composer update '.$module->getPackageName().'</> if they do not get autoloaded.');
        
        $output->writeln("Any changes to <fg=yellow>". $module->getLocalPath()->getPath() . '/composer.json</> must be realised');
        $output->writeln('by running <fg=white;bg=cyan>composer update '.$module->getPackageName().'</> in the Magento root directory');
        $output->writeln('<fg=yellow>'.BP.'</>. This ensures you have a clean working directory for your code.');
        
        $output->writeln("<fg=white;bg=red>Keep in mind that the module created is for developing purposes! When you are ready to move it to production don't forget to package like you normally would!</>");
        
        $output->writeln('<fg=white;bg=green>Run the following command to install your new plugin into magento:             </>');
        $output->writeln(sprintf(self::SETUP_AFTER_CREATE_COMMAND, $module->getPackageName(), $module->getMagentoModuleName()));
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