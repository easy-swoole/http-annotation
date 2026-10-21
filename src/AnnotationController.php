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
use ReflectionNamedType;
use ReflectionParameter;
use Throwable;


abstract class AnnotationController extends Controller
{
    public function __hook(array|null $actionArg = [], array|null $onRequestArg = null)
    {
        try {
            $attributeInfo = AttributeCache::getInstance()->parseClass(static::class);
            if (!$this->runPreCalls($attributeInfo->globalPreCall, [
                $this->getActionName(), $this->request(), $this->response(),
            ])) {
                return;
            }

            $apiTag = $attributeInfo->apiTag($this->getActionName());
            if ($apiTag) {
                $this->checkRequestMethod($apiTag);
            }

            $actionParams = $apiTag ? $apiTag->requestParam : [];
            $publicParams = $attributeInfo->onRequest->onRequestParams;
            // action 同名定义优先；公共定义只用于确定校验顺序和传递位置。
            $finalParams = $this->parseParameters($actionParams + $publicParams);
            $this->validateParameters($publicParams, $actionParams, $finalParams);
            $onRequestArg = $this->buildPublicArguments($publicParams, $finalParams);
            $actionArg = $apiTag ? $this->buildActionArguments($actionParams, $finalParams) : [];

            $preCalls = $attributeInfo->methodPreCall[$this->getActionName()] ?? [];
            if (!$this->runPreCalls($preCalls, [$this->request(), $this->response()])) {
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
    private function runPreCalls(array $preCalls, array $args): bool
    {
        /** @var PreCall $preCall */
        foreach ($preCalls as $preCall) {
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
        $methodRef = ReflectionCache::getInstance()->allowMethodReflection(static::class, $this->getActionName());
        $parameters = $methodRef->getParameters();
        if (empty($parameters)) {
            return [];
        }

        $type = $parameters[0]->getType();
        $paramKey = $parameters[0]->name;
        // 同名字段优先绑定；联合或交叉类型按名称绑定，不作为收集模式。
        if (count($parameters) == 1 && $type instanceof ReflectionNamedType
            && $type->getName() === 'array' && !array_key_exists($paramKey, $finalAllParams)) {
            return [$paramKey => $this->collectActionArguments($actionArgsInTag, $finalAllParams)];
        }
        return $this->bindNamedArguments($parameters, $finalAllParams);
    }

    // 收集模式仅包含 action 参数，并按配置省略没有实际传入的字段。
    private function collectActionArguments(array $definitions, array $finalParams): array
    {
        $args = [];
        foreach ($definitions as $definition) {
            $param = $finalParams[$definition->name];
            if ($param->ignorePassArgWhenNotSet && !$param->hasSet()) {
                continue;
            }
            $args[$param->name] = $param->parsedValue();
        }
        return $args;
    }

    /** @param list<ReflectionParameter> $parameters */
    private function bindNamedArguments(array $parameters, array $finalParams): array
    {
        $args = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->name;
            if (!array_key_exists($name, $finalParams)) {
                throw new ParamError("method {$this->getActionName()}() require arg: {$name} , but not define in any controller annotation");
            }
            $args[$name] = $finalParams[$name]->parsedValue();
        }
        return $args;
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

    protected function __getValidateRuleMapClass(): string
    {
        return DefaultMap::class;
    }
}
