<?php


namespace EasySwoole\HttpAnnotation;


use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di as IOC;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\HttpAnnotation\Annotation\Parser;
use EasySwoole\HttpAnnotation\Annotation\ParserInterface;
use EasySwoole\HttpAnnotation\Annotation\PropertyAnnotation;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation\ActionTimeout;
use EasySwoole\HttpAnnotation\Exception\Annotation\MethodNotAllow;
use EasySwoole\HttpAnnotation\Exception\Annotation\ParamError;
use EasySwoole\HttpAnnotation\Exception\Annotation\ParamValidateError;
use EasySwoole\HttpAnnotation\Exception\Exception;
use EasySwoole\Validate\Validate;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class AnnotationController extends Controller
{
    private $classAnnotation;

    public function __construct(?ParserInterface $parser = null)
    {
        parent::__construct();
        if($parser == null){
            $parser = new Parser();
        }

        $this->classAnnotation = $parser->parseObject(new \ReflectionClass(static::class));
    }

    protected function __exec()
    {
        /*
           执行成员属性解析
        */
        foreach ($this->classAnnotation->getProperty() as $name => $propertyAnnotation){
            /** @var $propertyAnnotation PropertyAnnotation */
            if($propertyAnnotation->getContextTag()){
                $contextKey = $propertyAnnotation->getContextTag()->key;
                $this->{$name} = ContextManager::getInstance()->get($contextKey);
            }
            if($propertyAnnotation->getDiTag()){
                $key = $propertyAnnotation->getDiTag()->key;
                $this->{$name} = IOC::getInstance()->get($key);
            }
        }
        //执行
        $actionName = $this->getActionName();
        $allowMethodReflections = $this->getAllowMethodReflections();
        $forwardPath = null;
        try {
            $this->__handleAnnotationParams('onRequest');
            $ret = call_user_func([$this,'onRequest'],$actionName);
            if ($ret !== false) {
                if (isset($allowMethodReflections[$actionName])) {
                    $actionArgs = $this->__handleAnnotationParams($actionName);
                    /** @var \ReflectionMethod $methodRef */
                    $methodRef = $allowMethodReflections[$actionName];
                    $runArg = [];
                    foreach ($methodRef->getParameters() as $parameter){
                        $name = $parameter->getName();
                        if(isset($actionArgs[$name])){
                            $runArg[] = $actionArgs[$name];
                        }else{
                            $runArg[] = $this->request()->getRequestParam($name);
                        }
                    }
                    if(isset($annotations['CircuitBreaker'])){
                        $breakerInfo = $annotations['CircuitBreaker'][0];
                        $timeout = $breakerInfo->timeout;
                        $failAction = $breakerInfo->failAction;
                        $channel = new Channel(1);
                        Coroutine::create(function ()use($channel,$actionName,$runArg){
                            /*
                             * 因为协程内的异常需要被外层捕获
                             */
                            try{
                                $ret = $this->$actionName(...array_values($runArg));
                            }catch (\Throwable $exception){
                                $ret = $exception;
                            }
                            $channel->push($ret);
                        });
                        $ret = $channel->pop($timeout);
                        if($ret instanceof \Throwable){
                            throw $ret;
                        }
                        if($ret === false){
                            if($failAction){
                                $forwardPath =  $this->$failAction();
                            }else{
                                throw new ActionTimeout("action:{$actionName} timeout");
                            }
                        }else{
                            $forwardPath = $ret;
                        }
                    }else{
                        $forwardPath = $this->$actionName(...array_values($runArg));
                    }
                } else {
                    $forwardPath = $this->actionNotFound($actionName);
                }
            }
        } catch (\Throwable $throwable) {
            //若没有重构onException，直接抛出给上层
            $this->onException($throwable);
        } finally {
            try {
                $this->afterAction($actionName);
            } catch (\Throwable $throwable) {
                $this->onException($throwable);
            } finally {
                try {
                    $this->gc();
                } catch (\Throwable $throwable) {
                    $this->onException($throwable);
                }
            }
        }
        return $forwardPath;
    }

    protected function __handleAnnotationParams(?string $actionName):array
    {
        if(isset($this->methodAnnotations[$actionName])){
            $annotations = $this->methodAnnotations[$actionName];
            //request method check
            if(!empty($annotations['Method'])){
                $method = $annotations['Method'][0]->allow;
                if(!in_array($this->request()->getMethod(),$method)){
                    throw new MethodNotAllow("request method {$this->request()->getMethod()} is not allow for action {$actionName} in class ".(static::class) );
                }
            }
            //params handler
            $injectKey = null;
            $filterNull = false;
            $filterEmpty = false;
            if(!empty($annotations['InjectParamsContext'])){
                $injectKey = $annotations['InjectParamsContext'][0]->key;
                $filterNull = $annotations['InjectParamsContext'][0]->filterNull;
                $filterEmpty = $annotations['InjectParamsContext'][0]->filterEmpty;
            }
            $actionArgs = [];
            $params = [];
            $validate = new Validate();
            //校验合并
            if(!empty($this->classAnnotation->getMethod($actionName)->getGroupInfo()->getApiGroupAuthTags())){
                foreach ($this->classAnnotation->getMethod($actionName)->getGroupInfo()->getApiGroupAuthTags() as $param){
                    $params[$param->name] = $param;
                }
            }
            if(!empty($annotations['ApiAuth'])){
                foreach ($annotations['ApiAuth'] as $param){
                    $params[$param->name] = $param;
                }
            }
            $realParams = [];
            if(!empty($annotations['Param'])){
                foreach ($annotations['Param'] as $param){
                    $params[$param->name] = $param;
                    $realParams[$param->name] = null;
                }
            }

            /** @var Param $param */
            foreach ($params as $param){
                $paramName = $param->name;
                if(empty($paramName)){
                    throw new ParamError("param annotation error for action {$actionName} in class ".(static::class));
                }
                if(!empty($param->from)){
                    $value = null;
                    /*
                     * 按照允许的列表顺序进行取值
                     */
                    foreach ($param->from as $from){
                        switch ($from){
                            case "POST":{
                                $value = $this->request()->getParsedBody($paramName);
                                break;
                            }
                            case "GET":{
                                $value = $this->request()->getQueryParam($paramName);
                                break;
                            }
                            case "COOKIE":{
                                $value = $this->request()->getCookieParams($paramName);
                                break;
                            }
                            case 'HEADER':{
                                $value = $this->request()->getHeader($paramName);
                                if(!empty($value)){
                                    $value = $value[0];
                                }else{
                                    $value = null;
                                }
                                break;
                            }
                            case 'FILE':{
                                $value = $this->request()->getUploadedFile($paramName);
                                break;
                            }
                            case 'DI':{
                                $value = IOC::getInstance()->get($paramName);
                                break;
                            }
                            case 'CONTEXT':{
                                $value = ContextManager::getInstance()->get($paramName);
                                break;
                            }
                            case 'RAW':{
                                $value = $this->request()->getBody()->__toString();
                                break;
                            }
                        }
                        if($value !== null){
                            break;
                        }
                    }
                }else{
                    $value = $this->request()->getRequestParam($paramName);
                }

                if($value === null){
                    $value = $param->defaultValue;
                }

                if($value !== null){
                    $value = $param->typeCast($value);
                }

                //如果参数不为null,执行预处理，并设置进去参数
                if(!empty($param->preHandler) && $value !== null){
                    if(is_callable($param->preHandler)){
                        $value = call_user_func($param->preHandler,$value);
                    }else{
                        throw new Exception("annotation param: {$paramName} preHandler is not callable");
                    }
                }
                $actionArgs[$paramName] = $value;
                if(!empty($param->validateRuleList)){
                    foreach ($param->validateRuleList as $rule => $none){
                        $validateArgs = $param->{$rule};
                        if(!is_array($validateArgs)){
                            $validateArgs = [$validateArgs];
                        }
                        $validate->addColumn($param->name,$param->alias)->{$rule}(...$validateArgs);
                    }
                }
            }

            //合并参数
            $data = $actionArgs + $this->request()->getRequestParam();
            if(!$validate->validate($data)){
                $ex = new ParamValidateError("validate fail for column {$validate->getError()->getField()}");
                $ex->setValidate($validate);
                throw $ex;
            }
            //仅仅返回@Param所指定的参数
            foreach ($realParams as $key => $value){
                $realParams[$key] = $actionArgs[$key];
            }
            if($injectKey){
                if($filterNull){
                    foreach ($realParams as $key => $arg){
                        if($arg === null){
                            unset($realParams[$key]);
                        }else if($filterEmpty){
                            if(empty($arg)){
                                unset($realParams[$key]);
                            }
                        }

                    }
                }else if($filterEmpty){
                    foreach ($actionArgs as $key => $arg){
                        if(empty($arg)){
                            unset($realParams[$key]);
                        }
                    }
                }
                ContextManager::getInstance()->set($injectKey,$realParams);
            }
            return $realParams;
        }
        return [];
    }
}