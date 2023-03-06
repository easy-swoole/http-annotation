<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\ParserDown\ParserDown;

class Document
{
    private Config $config;
    public function __construct(
        private string $controllerPath,
        private string $controllerNameSpace = 'App\HttpController'
    )
    {
        if(!(is_file($controllerPath) || is_dir($controllerPath))){
            throw new Annotation("{$controllerPath} not exist");
        }
        $this->config = new Config();
    }

    public function getConfig():Config
    {
        return $this->config;
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

            //用于构建控制器路径
            $trimClass = ltrim(str_replace($this->controllerNameSpace,"",$controllerClass),"\\");
            $controllerRequestPrefix = str_replace("\\","/",$trimClass);
            //替换首字母为小写。
            $arr = explode("/",$controllerRequestPrefix);
            $controllerRequestPrefix = "";
            while ($a = array_shift($arr)){
                if(strtolower($a) != "index"){
                    $controllerRequestPrefix .= lcfirst($a);
                    if(!empty($arr)){
                        $controllerRequestPrefix .= "/";
                    }
                }else{
                    //当是index的时候，去除上一步构建的  xxx/ 的斜杆
                    $controllerRequestPrefix = substr($controllerRequestPrefix,0,-1);
                }
            }


            /**
             * @var  $name
             * @var \ReflectionMethod $method
             */
            foreach ($methods as $name => $method){
                $api = $method->getAttributes(Api::class);
                if(!empty($api)){
                    try{
                        $api = new Api(...$api[0]->getArguments());
                        $api->requestParam = Utility::parseActionParams($ref,$name);

                        //处理参数验证
                        /**
                         * @var  $key
                         * @var Param $item
                         */
                        foreach ($api->requestParam as $key => $item){
                            $rules = $item->validate;
                            /** @var AbstractValidator $rule */
                            foreach ($rules as $rule){
                                $rule->allCheckParams($api->requestParam);
                            }
                        }

                        if(empty($api->requestPath)){
                            if(strtolower($method->name) == "index"){
                                $api->requestPath = "/{$controllerRequestPrefix}";
                            }else{
                                $api->requestPath = "/{$controllerRequestPrefix}/{$method->name}";
                            }
                        }
                    }catch (\Throwable $throwable){
                        throw new Annotation("{$throwable->getMessage()} in class {$method->class} method {$method->name}");
                    }


                    if(!$group->addApi($api)){
                        throw new Annotation("cannot redefine apiName {$api->apiName} in apiGroup {$group->getName()}");
                    }
                }
            }
        }
        return $list;
    }

    function scanToHtml()
    {
        $json = json_encode($this->scan());
        $temp = file_get_contents(__DIR__ . '/doc.tpl');
        $temp = str_replace('{{$docData}}',$json,$temp);
        return str_replace('{{$config}}',json_encode($this->config),$temp);
    }
}