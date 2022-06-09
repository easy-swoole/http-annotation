<?php

namespace EasySwoole\HttpAnnotation\Attributes\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class DateBefore extends AbstractValidator
{

    public $date;

    function __construct(string|callable $date,?string $errorMsg = null)
    {
        $this->date = $date;
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be date before {#date}";
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        // TODO: Implement validate() method.
    }

    function ruleName(): string
    {
        return "DateBefore";
    }
}