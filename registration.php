<?php
use Magento\Framework\Component\ComponentRegistrar;
use Tschallacka\MageCommands\Configuration\Config;
if(!class_exists('Tschallacka\\MageCommands\\Console\\Command\\CreateModule')) {
    rtschallacka('vendor/autoload.php');
}
ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    Config::MODULE_NAME,
    __DIR__
);
