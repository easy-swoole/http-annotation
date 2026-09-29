<?php

namespace EasySwoole\HttpAnnotation;

use EasySwoole\Http\ReflectionCache;
use EasySwoole\Http\UrlParser;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\Utility\File;
use FastRoute\RouteCollector;
use PhpToken;
use Psr\Http\Message\ServerRequestInterface;

class Utility
{

    public static function getFileDeclaredClass(string $file): array
    {

        $tokens = PhpToken::tokenize(file_get_contents($file));
        $classes = [];
        $count = count($tokens);

        $currentNamespace = ''; // 存储当前文件的命名空间

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            // 1. 捕获命名空间 (支持 PHP 8.0+ 的 T_NAME_QUALIFIED 复合 Token)
            if ($token->id === T_NAMESPACE) {
                $namespaceParts = [];
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j]->isIgnorable()) {
                        continue;
                    }
                    // PHP 8.0 之后，命名空间可能是 T_STRING、T_NAME_QUALIFIED (如 App\Services) 或 T_NAME_FULLY_QUALIFIED
                    if (in_array($tokens[$j]->id, [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                        $namespaceParts[] = $tokens[$j]->text;
                    }
                    if ($tokens[$j]->text === ';' || $tokens[$j]->text === '{') {
                        $i = $j; // 移动外层指针，加速遍历
                        break;
                    }
                }
                $currentNamespace = implode('', $namespaceParts);
                continue;
            }

            // 2. 捕获真实的类定义 (排除匿名类)
            if ($token->id === T_CLASS) {
                // 往前检查，排除 new class 匿名类
                $isAnonymous = false;
                for ($j = $i - 1; $j >= 0; $j--) {
                    if ($tokens[$j]->isIgnorable()) {
                        continue;
                    }
                    if ($tokens[$j]->id === T_NEW) {
                        $isAnonymous = true;
                    }
                    break;
                }

                if ($isAnonymous) {
                    continue;
                }

                // 向后寻找类名
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j]->isIgnorable()) {
                        continue;
                    }

                    if ($tokens[$j]->id === T_STRING) {
                        $className = $tokens[$j]->text;
                        // 拼接完整的命名空间前缀
                        $fullClassName = $currentNamespace ? $currentNamespace . '\\' . $className : $className;
                        $classes[] = $fullClassName;

                        $i = $j; // 移动指针
                        break;
                    }
                    break;
                }
            }
        }

        return $classes;
    }


    public static function validateParam(ValidateRequest $validateRequest): void
    {
        $rules = $validateRequest->validateParam->validate;
        /** @var AbstractValidator $rule */
        foreach ($rules as $rule){
            if(!$rule->execute($validateRequest)){
                $msg = $rule->errorMsg($validateRequest->validateParam->name);
                $ex = new ParamValidateFail("{$msg} in {$validateRequest->callClass} method {$validateRequest->callMethod}");
                $ex->setFailRule($rule);
                $ex->setParamName($validateRequest->validateParam->name);
                throw $ex;
            }
        }
    }
}