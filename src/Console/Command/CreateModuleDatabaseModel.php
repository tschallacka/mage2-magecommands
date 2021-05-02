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
    const CREATE_TABLE_COMMAND = 'tsch:module:create:model';
    
    
    /**
     * Name argument
     */
    const TABLE_NAME = 'table-name';
    const FIELD_ARGUMENTS = 'fields';
    const NO_DEFAULTS = 'no-defaults';
    
    
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
        $this->setName(self::CREATE_TABLE_COMMAND)
            ->setDescription("Create a new table. \n".
                "Example: <fg=yellow>bin/magento ".self::CREATE_TABLE_COMMAND . 
                " example_table \"user_name:varchar:null:required:IX_example_table_users\" ".
                "\"some_time:datetime:nullable\"</>\n".
                "See https://devdocs.magento.com/guides/v2.4/extension-dev-guide/declarative-schema/db-schema.html#perform-common-database-operations".
                " how to edit the generated db_schema.xml file in your module's etc/ directory.\n".
                "By default tables will have their primary column named id, and gain columns ".
                "created_at(datetime) and updated_at(datetime), use --no-defaults to disable their generation\n".
                "if you want laravels/wintercms eloquent supported modules generated next to the magento style modules".
                "add --eloquent. The magerain library will be added as a dependency to your composer.json which enables the database features of the wintercms storm library",
                "You can read documentation on how to use wintercms models methods at https://github.com/wintercms/docs/blob/main/database-model.md")
            ->setDefinition([
                new InputArgument(
                    self::TABLE_NAME,
                    InputArgument::REQUIRED,
                    'The name of the table'
                ),
                new InputOption(
                    new InputOption(
                        self::NO_DEFAULTS,
                        null, /** shortcut **/
                        InputOption::VALUE_NONE,
                        "n",
                        null) /** default value **/,
                ),
                new InputArgument(
                    self::FIELD_ARGUMENTS,
                    InputArgument::OPTIONAL | INPUTARGUMENT::IS_ARRAY,
                    "Space seperated list of fields to create in the format of \n<fg=yellow>".
                    '"field_name:' .
                    'type<blob|boolean|date|datetime|decimal|float|int|json|real|smallint|text|timestamp|varbinary|varchar>:' .
                    'default_value:nullable<nullable|required>:' . 
                    "index_name\"</>\n".
                    "When there are multiple fields with the same index name they will be added to the same index. Please note that mysql index size limitations apply."
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