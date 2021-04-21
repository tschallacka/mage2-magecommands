<?php
namespace Tschallacka\MageCommands\Console\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ObjectManager;


use Tschallacka\MageCommands\Configuration\Config;
use Tschallacka\MageCommands\Module\ModuleInfo;
use Tschallacka\MageRain\File\Format\Composer;
use Tschallacka\MageRain\File\TemplateFile;
use Tschallacka\MageRain\File\Directory;
use Tschallacka\MageRain\File\Transformer\StringReplaceTransformer;
/**
 * Class CreateModule
 */
class CreateModuleDatabaseModel extends Command
{
    const CREATE_MODULE_COMMAND = 'tsch:module:create:model';
    
    
    /**
     * Name argument
     */
    const TABLE_NAME = 'table-name';
    
    /**
     * @var Config
     */
    protected $config;
    
    public function __construct(string $name=null, Config $config)
    {
       $this->config = $config;
       parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::CREATE_MODULE_COMMAND)
            ->setDescription('Create a module in the local folder')
            ->setDefinition([
                new InputArgument(
                    self::TABLE_NAME,
                    InputArgument::REQUIRED,
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
        
        
    }
    
      
}