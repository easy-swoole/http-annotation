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

        // GET、POST、JSON、XML 使用 isset 判断：null 视为未设置，空字符串和 0 仍算已设置。
        foreach ($param->from as $source) {
            switch ($source){
                case ParamFrom::GET:{
                    $data = $request->getQueryParams();
                    if(isset($data[$param->name])){
                        $this->hasSet = true;
                        $this->parsedValue = $data[$param->name];
                    }
                    break;
                }
                case ParamFrom::POST:{
                    $data = $request->getParsedBody();
                    if(isset($data[$param->name])){
                        $this->hasSet = true;
                        $this->parsedValue = $data[$param->name];
                    }
                    break;
                }
                case ParamFrom::JSON:{
                    // 将 JSON 对象解码为关联数组，再按参数名取值。
                    $data = $request->getBody()->__toString();
                    if(empty($data)){
                        $data = [];
                    }else{
                        $data = json_decode($data,true) ?: [];
                    }

                    if(isset($data[$param->name])){
                        $this->hasSet = true;
                        if(!empty($param->subObject)){
                            $temps = [];
                            /** @var Param $item */
                            foreach ($param->subObject as $item){
                                $temps[$item->name] = $item->parsedValue($request,$data[$param->name]);
                            }
                            $this->parsedValue = $temps;
                        }else{
                            $this->parsedValue = $data[$param->name];
                        }
                    }

                    break;
                }
                case ParamFrom::XML:{
                    $xml = $request->getBody()->__toString();
                    // xml 转数组
                    $data = json_decode(json_encode(simplexml_load_string($xml)), true);
                    if(!is_array($data)){
                        $data = [];
                    }
                    if(isset($data[$param->name])){
                        $this->hasSet = true;
                        if(!empty($param->subObject)){
                            $temps = [];
                            /** @var Param $item */
                            foreach ($param->subObject as $item){
                                $temps[$item->name] = $item->parsedValue($request,$data[$param->name]);
                            }
                            $this->parsedValue = $temps;
                        }else{
                            $this->parsedValue = $data[$param->name];
                        }
                    }

                    break;
                }
                case ParamFrom::RAW_POST:{
                    // RAW_POST 使用整个请求体；即使内容为空，也标记为已设置。
                    $this->hasSet = true;
                    $this->parsedValue = $request->getBody()->__toString();
                    break;
                }
                case ParamFrom::FILE:{
                    // 保留上传文件对象，交由文件规则校验，不读取文件内容。
                    $data = $request->getUploadedFile($param->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->parsedValue = $data;
                    }
                    break;
                }
                case ParamFrom::DI:{
                    // 从依赖注入容器读取；当前以非 empty 值作为命中条件。
                    $data = IOC::getInstance()->get($param->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->parsedValue = $data;
                    }
                    break;
                }
                case ParamFrom::CONTEXT:{
                    // 从当前协程上下文读取；当前以非 empty 值作为命中条件。
                    $data = ContextManager::getInstance()->get($param->name);
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->parsedValue = $data;
                    }
                    break;
                }
                case ParamFrom::COOKIE:{
                    // 仅 null 视为未命中，允许 Cookie 值为空字符串或 0。
                    $cookies = $request->getCookieParams();
                    $data = $cookies[$param->name] ?? null;
                    if($data !== null){
                        $this->hasSet = true;
                        $this->parsedValue = $data;
                    }
                    break;
                }
                case ParamFrom::HEADER:{
                    // Swoole 请求头名称统一为小写；多值请求头只取第一项。
                    $data = $request->getHeader(strtolower($param->name));
                    if(!empty($data)){
                        $this->hasSet = true;
                        $this->parsedValue = $data[0];
                    }
                    break;
                }
            }

            // 按声明顺序读取，命中后不再检查后续来源。
            if ($this->hasSet) {
                break;
            }
        }

        // 实际取到的值在验证器执行前转换；type 为 null 时保留原值（例如数组）。
        // 转换不代表数据合法，且不会改变 hasSet，合法性仍需由验证器检查。
        // 只有实际取到参数才做类型转换，未传参数保留默认值，避免 null 被转为 0 等值。
        if($this->hasSet && $param->type != null){
            switch ($param->type){
                case ParamType::STRING:{
                    $this->parsedValue = (string)$this->parsedValue;
                    break;
                }
                case ParamType::INT:{
                    $this->parsedValue = (int)$this->parsedValue;
                    break;
                }
                case ParamType::FLOAT:
                case ParamType::REAL:
                case ParamType::DOUBLE:{
                    $this->parsedValue = (float)$this->parsedValue;
                    break;
                }
                case ParamType::BOOLEAN:{
                    // 使用 PHP 布尔转换规则，字符串 "false" 会转换为 true。
                    $this->parsedValue = (bool)$this->parsedValue;
                    break;
                }
                case ParamType::NULL_WHILE_EMPTY:{
                    // PHP empty 值转为 null，但特意保留整数 0 和字符串 '0'。
                    if(empty($this->parsedValue) && (($this->parsedValue !== 0) && ($this->parsedValue !== '0'))){
                        $this->parsedValue = null;
                    }
                    break;
                }
                case ParamType::FILE:{
                    //无需转化
                    break;
                }
            }
        }

    }
}
