<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\ReflectionCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Bean\PropertyAttribute;
use EasySwoole\HttpAnnotation\Exception\ParamError;
use EasySwoole\HttpAnnotation\Exception\RequestMethodNotAllow;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\MsgMap\DefaultMap;
use Throwable;


abstract class AnnotationController extends Controller
{
    public function __hook(array|null $actionArg = [], array|null $onRequestArg = null)
    {
        try {
            $attributeInfo = AttributeCache::getInstance()->parseClass(static::class);
            if (!$this->runPreCalls($attributeInfo->globalPreCall, true)) {
                return;
            }

            $apiTag = $attributeInfo->apiTag($this->getActionName());
            if ($apiTag) {
                $this->checkRequestMethod($apiTag);
            }

            $actionParams = $apiTag ? $apiTag->requestParam : [];
            [$publicParams, $definitions] = $this->mergeParamDefinitions(
                $attributeInfo->onRequest->onRequestParams,
                $actionParams
            );
            $finalParams = $this->parseParameters($definitions);
            $this->validateParameters($publicParams, $actionParams, $finalParams);
            $onRequestArg = $this->buildPublicArguments($publicParams, $finalParams);
            $actionArg = $apiTag ? $this->buildActionArguments($actionParams, $finalParams) : [];

            $preCalls = $attributeInfo->methodPreCall[$this->getActionName()] ?? [];
            if (!$this->runPreCalls($preCalls)) {
                return;
            }
            $this->injectProperties($attributeInfo->propertyAttribute);
        } catch (Throwable $throwable) {
            $this->onException($throwable);
            return;
        }

        parent::__hook($actionArg, $onRequestArg);
    }

    // 类级回调在参数处理前执行，方法级回调在校验和传参组装后执行。
    private function runPreCalls(array $preCalls, bool $global = false): bool
    {
        /** @var PreCall $preCall */
        foreach ($preCalls as $preCall) {
            $args = [$this->request(), $this->response()];
            if ($global) {
                array_unshift($args, $this->getActionName());
            }
            if (call_user_func_array($preCall->call, $args) === false) {
                return false;
            }
        }
        return true;
    }

    private function checkRequestMethod(Api $api): void
    {
        $method = $this->request()->getMethod();
        if (empty($method)) {
            throw new RequestMethodNotAllow('request method is empty');
        }
        if ($method != $api->allowMethod->name) {
            throw new RequestMethodNotAllow("{$method} method is not allow for this request");
        }
    }

    // 同名项完整使用 action 定义，同时保留公共参数的传递位置与顺序。
    private function mergeParamDefinitions(array $publicParams, array $actionParams): array
    {
        foreach ($actionParams as $param) {
            if (isset($publicParams[$param->name])) {
                $publicParams[$param->name] = $param;
            }
        }
        return [$publicParams, $actionParams + $publicParams];
    }

    private function parseParameters(array $finalAllParams): array
    {
        /** @var Param $param */
        foreach ($finalAllParams as $paramName => $param) {
            if (in_array($this->getActionName(), $param->ignoreAction)) {
                unset($finalAllParams[$paramName]);
                continue;
            }
            $param = clone $param;
            $finalAllParams[$paramName] = $param;
            $param->parsedValue($this->request());
        }
        return $finalAllParams;
    }

    // 所有值先解析完成，再按公共参数、action 独有参数的顺序各校验一次。
    private function validateParameters(array $publicParams, array $actionParams, array $finalParams): void
    {
        foreach ($publicParams + $actionParams as $name => $definition) {
            if (!isset($finalParams[$name])) {
                continue;
            }
            $validateRequest = new ValidateRequest($finalParams[$name]);
            $validateRequest->allDefineParams = $finalParams;
            $validateRequest->callClass = static::class;
            $validateRequest->callMethod = $this->getActionName();
            $validateRequest->request = $this->request();
            Utility::validateParam($validateRequest, $this->__getValidateRuleMapClass());
        }
    }

    private function buildPublicArguments(array $publicParams, array $finalParams): array
    {
        $args = [];
        foreach ($publicParams as $name => $definition) {
            if (isset($finalParams[$name])) {
                $args[$name] = $finalParams[$name]->parsedValue();
            }
        }
        return $args;
    }

    private function buildActionArguments(array $actionArgsInTag, array $finalAllParams): array
    {
        $actionArg = [];
        $methodRef = ReflectionCache::getInstance()->allowMethodReflection(static::class, $this->getActionName());
        $parameters = $methodRef->getParameters();
        if (empty($parameters)) {
            return $actionArg;
        }

        $type = $parameters[0]->getType();
        if ($type) {
            $type = $type->getName();
        }
        // 单数组形参仅接收 action 定义的参数，按配置省略未设置字段。
        if (count($parameters) == 1 && $type == 'array') {
            $paramKey = $parameters[0]->name;
            $temp = [];
            foreach ($actionArgsInTag as $param) {
                /** @var Param $param */
                $param = $finalAllParams[$param->name];
                if ($param->ignorePassArgWhenNotSet && !$param->hasSet()) {
                    continue;
                }
                $temp[$param->name] = $param->parsedValue();
            }
            $actionArg[$paramKey] = $temp;
        } else {
            foreach ($parameters as $parameter) {
                $key = $parameter->name;
                if (key_exists($key, $finalAllParams)) {
                    $actionArg[$key] = $finalAllParams[$key]->parsedValue();
                } else {
                    throw new ParamError("method {$this->getActionName()}() require arg: {$key} , but not define in any controller annotation");
                }
            }
        }
        return $actionArg;
    }

    private function injectProperties(array $properties): void
    {
        /** @var PropertyAttribute $propertyAttribute */
        foreach ($properties as $property => $propertyAttribute) {
            if ($propertyAttribute->di) {
                $this->{$property} = Di::getInstance()->get($propertyAttribute->di->key);
            }
            if ($propertyAttribute->context) {
                $this->{$property} = ContextManager::getInstance()->get($propertyAttribute->context->key);
            }
        }
    }

    protected function __getValidateRuleMapClass():string
    {
        return DefaultMap::class;
    }
}
