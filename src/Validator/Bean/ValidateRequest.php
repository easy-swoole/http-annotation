<?php

namespace EasySwoole\HttpAnnotation\Validator\Bean;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class ValidateRequest
{
    function __construct(
        public Param $validateParam,
        public ServerRequestInterface|null $request = null,
        public array $allDefineParams = []
    )
    {}
}