<?php

use EasySwoole\Command\AbstractInterface\CommandHelpInterface;
use EasySwoole\Command\AbstractInterface\CommandInterface;
use EasySwoole\Command\Caller;
use EasySwoole\Command\CommandManager;
use EasySwoole\HttpAnnotation\Scanner;
use EasySwoole\ParserDown\ParserDown;

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
        if(empty($dir)){
            return "php annotation-doc.php --dir=DIR  --format=html|markdown --project=PROJECT_NAME";
        }
        $format = CommandManager::getInstance()->getOpt("format","html");
        $fix = "doc_".date("Ymd");
        $maxCount = 1;
        if ($dh = opendir(getcwd())) {
            while (($file = readdir($dh)) !== false) {
                if(is_file($file)){
                    if(str_starts_with($file, $fix)){
                        $name = explode(".",$file)[0];
                        $count = (int)substr($name,strlen($fix)+1);
                        if($count >= $maxCount){
                            $maxCount = $count + 1;
                        }
                    }
                }
            }
            closedir($dh);
        }

        $finalFile = getcwd();
        if($format == "html"){
            $project = CommandManager::getInstance()->getOpt("project","Project");
            $temp = Scanner::scanToHtml($dir,$project);
            $finalFile = $finalFile."/{$fix}_{$maxCount}.html";
            file_put_contents($finalFile,$temp);
        }else{
            $data = Scanner::scanToMarkdown($dir);
            $finalFile = $finalFile."/{$fix}_{$maxCount}.md";
            file_put_contents($finalFile,$data);
        }
        return "create doc file :{$finalFile}";
    }

    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface
    {
        $commandHelp->addActionOpt('--dir', 'scanned directory or file');
        $commandHelp->addActionOpt('--format', 'format as markdown or html');
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