<?php
use Magento\Framework\Component\ComponentRegistrar;
use Tschallacka\MageCommands\Configuration\Config;
ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    Config::MODULE_NAME,
    __DIR__
);
if(file_exists(__DIR__.'/debug_tools.php')) {
    require_once __DIR__.'/debug_tools.php';
}