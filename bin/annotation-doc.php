<?php

use EasySwoole\Command\AbstractInterface\CommandHelpInterface;
use EasySwoole\Command\AbstractInterface\CommandInterface;
use EasySwoole\Command\Caller;
use EasySwoole\Command\CommandManager;

$file = null;
foreach ([ __DIR__ . '/../../../autoload.php', __DIR__ . '/../vendor/autoload.php' ] as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}
if(!file_exists($file)){
    die("include composer autoload.php fail\n");
}

class DocCommand implements CommandInterface{

    public function commandName(): string
    {
        return 'doc';
    }

    public function exec(): ?string
    {
        $dir = CommandManager::getInstance()->getOpt("dir");
        $format = CommandManager::getInstance()->getOpt("format","html");
        return "";
    }

    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface
    {
        $commandHelp->addActionOpt('--dir', 'scanned directory or file');
        $commandHelp->addActionOpt('--format', 'import ext MD file');
        return $commandHelp;
    }

    public function desc(): string
    {
        return 'build api doc by annotations';
    }
}
$command = new DocCommand();
CommandManager::getInstance()->addCommand($command);
array_splice($argv, 1, 0, $command->commandName());
$caller = new Caller();
$caller->setScript(current($argv));
$caller->setCommand($command->commandName());
$caller->setParams($argv);
echo CommandManager::getInstance()->run($caller);