<?php

namespace EasySwoole\HttpAnnotation\Bean;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di as IOC;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 负责单次参数的来源读取、类型转换和结果保存，不持有请求或参数定义的引用。
 * @internal
 */
final class ParamParseState
{
    public bool $hasSet = false;

    public mixed $parsedValue = null;

    // Param 仅在本方法完整执行成功后缓存当前状态，校验器仍由控制器调用。
    public function parse(Param $param, ServerRequestInterface $request): void
    {
        $this->hasSet = false;
        $this->parsedValue = $param->value;

        $this->readSources($param, $request);
        $this->convertType($param);
    }

    private function readSources(Param $param, ServerRequestInterface $request): void
    {
        foreach ($param->from as $source) {
            switch ($source) {
                // 普通字段使用 isset：null 不算设置，空字符串、false、0 和空数组算设置。
                case ParamFrom::GET:
                case ParamFrom::POST:
                case ParamFrom::JSON:
                case ParamFrom::XML:
                case ParamFrom::COOKIE:
                    $data = match ($source) {
                        ParamFrom::GET => $request->getQueryParams(),
                        ParamFrom::POST => $request->getParsedBody(),
                        ParamFrom::JSON => ArgContext::getInstance()->getJson($request),
                        ParamFrom::XML => ArgContext::getInstance()->getXml($request),
                        ParamFrom::COOKIE => $request->getCookieParams(),
                    };
                    $this->readField($data, $param->name);
                    break;

                case ParamFrom::RAW_POST:
                    // 原始请求体始终算已设置，包括空字符串。
                    $this->hasSet = true;
                    $this->parsedValue = $request->getBody()->__toString();
                    break;

                case ParamFrom::FILE:
                case ParamFrom::DI:
                case ParamFrom::CONTEXT:
                    $value = match ($source) {
                        ParamFrom::FILE => $request->getUploadedFile($param->name),
                        ParamFrom::DI => IOC::getInstance()->get($param->name),
                        ParamFrom::CONTEXT => ContextManager::getInstance()->get($param->name),
                    };
                    // 保留文件、DI 和 Context 原有的非 empty 命中规则。
                    if (!empty($value)) {
                        $this->hasSet = true;
                        $this->parsedValue = $value;
                    }
                    break;

                case ParamFrom::HEADER:
                    // 多值请求头只取第一项，第一项为空字符串时仍算已设置。
                    $headers = $request->getHeader(strtolower($param->name));
                    if (!empty($headers)) {
                        $this->hasSet = true;
                        $this->parsedValue = $headers[0];
                    }
                    break;
            }

            // 只读取第一个命中的来源，不提前读取后续请求体或依赖对象。
            if ($this->hasSet) {
                break;
            }
        }
    }

    private function readField(mixed $data, string $name): void
    {
        if (isset($data[$name])) {
            $this->hasSet = true;
            $this->parsedValue = $data[$name];
        }
    }

    private function convertType(Param $param): void
    {
        // 实际取到的值在验证器执行前转换；type 为 null 时保留原值（例如数组）。
        // 仅转换可接受的输入；失败时保留原值，由类型验证器拒绝，不改变 hasSet。
        // 只有实际取到参数才做类型转换，未传参数保留默认值，避免 null 被转为 0 等值。
        if ($this->hasSet && $param->type !== null) {
            switch ($param->type) {
                case ParamType::STRING: {
                    if (is_scalar($this->parsedValue)
                        && (!is_float($this->parsedValue) || is_finite($this->parsedValue))) {
                        $this->parsedValue = (string)$this->parsedValue;
                    }
                    break;
                }
                case ParamType::INT: {
                    if (is_int($this->parsedValue) || is_string($this->parsedValue)) {
                        $integer = filter_var($this->parsedValue, FILTER_VALIDATE_INT);
                        if ($integer !== false) {
                            $this->parsedValue = $integer;
                        }
                    }
                    break;
                }
                case ParamType::FLOAT:
                case ParamType::REAL:
                case ParamType::DOUBLE: {
                    if (is_numeric($this->parsedValue)) {
                        $float = filter_var($this->parsedValue, FILTER_VALIDATE_FLOAT);
                        if ($float !== false && is_finite($float)) {
                            $this->parsedValue = $float;
                        }
                    }
                    break;
                }
                case ParamType::BOOLEAN: {
                    // 仅接受明确的布尔表示，避免 'false' 或任意文本被强制转换为 true。
                    $boolean = is_string($this->parsedValue) ? strtolower($this->parsedValue) : $this->parsedValue;
                    if (in_array($boolean, [true, 1, '1', 'true'], true)) {
                        $this->parsedValue = true;
                    } elseif (in_array($boolean, [false, 0, '0', 'false'], true)) {
                        $this->parsedValue = false;
                    }
                    break;
                }
                case ParamType::NULL_WHILE_EMPTY: {
                    // PHP empty 值转为 null，但特意保留整数 0 和字符串 '0'。
                    if (empty($this->parsedValue) && $this->parsedValue !== 0 && $this->parsedValue !== '0') {
                        $this->parsedValue = null;
                    }
                    break;
                }
                case ParamType::FILE: {
                    // 文件对象保持原样。
                    break;
                }
            }
        }
    }
}
