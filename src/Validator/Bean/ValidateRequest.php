<?php

namespace EasySwoole\HttpAnnotation\Validator\Bean;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class ValidateRequest
{
    // 路径只用于错误定位，不修改 Param::name，保证跨字段规则按局部名称查找。
    public ?string $parameterPath = null;

    public int $depth = 0;

    public ?string $validateMsgMapClass = null;

    function __construct(
        public Param $validateParam,
        public ServerRequestInterface|null $request = null,
        public array $allDefineParams = [],
        public string|null $callClass = null,
        public string|null $callMethod = null,
    )
    {}
}
