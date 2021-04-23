## Magento Helper commands ##

This Magento Module is purely for developer aid, and not to be used in production.

This Magento Module is only intended currently for Magento 2.4. It might work in 2.3 too, but testing only happens on 2.4 CE

To install this module run in your magento root folder

```bash
composer require tschallacka/mage2-magecommands && \
bin/magento module:enable Tschallacka_MageCommands && \
bin/magento setup:di:compile
```

With this a couple commands are added in the `tsch` namespace so it will be easier to start developing certain magento
modules.

This plugin will require that you do your module development in a folder named `local` in your magento root.
If you create your modules via the MageCommands module, it will automatically register a repository in your Magento
root composer.json so you can install your newly created module into the corrent vendor folder via symlink.

# creating a module

When paths are not started with a / you can assume your current working directory is the magento root folder, /var/www/html for the examples below.

**command**: `bin/magento tsch:module:create <module_name>`

**requires**: <module_name> A module name in the format of "AuthorName_ModuleName". Example: "Tschallacka_SalesMonitor"

**performs**: 

   - Creates a bare bones plugin development folder in /magento/root/folder/local/authorname/modulename
   - registers plugin in /magento/root/composer.json
   - creates tests/unit/phpunit.xml for running local unit tests, code for these tests should be placed in tests/code
   - creates tests/unit/framework/bootstrap.php for bootstrapping the magento bootstrap
   - creates etc/di.xml
   - creates etc/module.xml
   - creates composer.json
   - creates registration.php
   - outputs which commands you need to execute to install the plugin
    
# creating a command

**command**: `bin/magento tsch:module:create:command <module_name> <command_name> [<command arguments>...]`

**requires**:  

   - **<module_name>** A module name in the format of `AuthorName_ModuleName`. Example: `Tschallacka_SalesMonitor`
   - **<command_name>** the name of the command, unique for your  module. The command will become `authorname:modulename:commandname`
 
![Image of output](https://i.imgur.com/W3OegE6.png) 

**optional:**    
   **<command arguments>** 
   Command arguments can be provided in the format of `"[==]argument_name:<optional|required>:<string:array:none>:<help description>"`   
   If you start your command argument with `==` then it will become an input option. Othewise it becomes an argument.  
   You can only use type none with an optional command(`==argumentname`)
   
   **Examples**
   
   *bin/magento author:module:command --quiet*   
   `bin/magento tsch:module:create:command Author_Module  command "==quiet::none:stop from outputting to console"
   
   *bin/magento author:module:command --name=foobar*   
   `bin/magento tsch:module:create:command Author_Module  command "==name:required::A name is required for this command option"
   
   *bin/magento author:module:command --make="stew" carrots beef apples*   
   `bin/magento tsch:module:create:command Author_Module  command "==make:required:string:what to cook stew or soup" "ingredients:required:array:what to put in the stew"

![Image of output](https://i.imgur.com/xTAzOSb.png)
   
**performs:**

   - creates file Console/Commands/CommandName.php
   - registers command in etc/di.xml
   - outputs which commands you need to run to activate the command
   
   