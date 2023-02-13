<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;

class Document
{
    public function __construct(
        private string $controllerPath,
        private string $controllerNameSpace = 'App\HttpController'
    )
    {
        if(!(is_file($controllerPath) || is_dir($controllerPath))){
            throw new Annotation("{$controllerPath} not exist");
        }
    }

    function scan()
    {
        $list = [];
        $len = strlen($this->controllerNameSpace);
        $allControllerClass = Utility::scanAllController($this->controllerPath);
        foreach ($allControllerClass as $controllerClass){
            if(substr($controllerClass,0,$len) !== $this->controllerNameSpace){
                throw new Annotation("class {$controllerClass} namespace not complete with {$this->controllerNameSpace}");
            }
            $ref = ReflectionCache::getInstance()->getClassReflection($controllerClass);
            //判断分组
            $g = $ref->getAttributes(ApiGroup::class);
            if(!empty($g)){
                $g = new ApiGroup(...$g[0]->getArguments());
            }else{
                $g = new ApiGroup("Default");
            }
            if(!isset($list[$g->groupName])){
                $list[$g->groupName] = new Group($g->groupName,$g->description);
            }else{
                if($g->description){
                    /** @var Group $group */
                    $group = $list[$g->groupName];
                    if($group->getDescription() == null){
                        $group->setDescription($g->description);
                    }else{
                        throw new Annotation("ApiGroup {$group->getName()} cannot rewrite description twice");
                    }
                }
            }
            /** @var Group $group */
            $group = $list[$g->groupName];

            $methods = ReflectionCache::getInstance()->allowMethodReflections($ref);

            /**
             * @var  $name
             * @var \ReflectionMethod $method
             */
            foreach ($methods as $name => $method){
                $api = $method->getAttributes(Api::class);
                if(!empty($api)){
                    $api = new Api(...$api[0]->getArguments());
                    $api->requestParam = Utility::parseActionParams($ref,$name);
                    if(!$group->addApi($api)){
                        throw new Annotation("cannot redefine apiName {$api->apiName} in apiGroup {$group->getName()}");
                    }
                }
            }

        }
    }
}