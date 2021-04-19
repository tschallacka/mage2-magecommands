<?php
use Magento\Framework\Component\ComponentRegistrar;
use Tschallacka\MageCommands\Configuration\Config;
ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    Config::MODULE_NAME,
    __DIR__
);
$dumper = '\\Symfony\\Component\\VarDumper\\VarDumper';
if(class_exists($dumper)) {
    $dumper::setHandler(function ($var) {
        $cloner = '\\Symfony\\Component\\VarDumper\\Cloner\\VarCloner';
        $cli = '\\Symfony\\Component\\VarDumper\\Dumper\\CliDumper';
        $html = '\\Symfony\\Component\\VarDumper\\Dumper\\HtmlDumper';
        $dd = ('cli' === PHP_SAPI ? $cli : $html);
        $cloner = new $cloner();
        $dumper = new $dd();
        $dumper->dump($cloner->cloneVar($var));
    });
}
else {
    function dump() {
        throw new \Exception("You forgot a trace of dump here");
    }
}