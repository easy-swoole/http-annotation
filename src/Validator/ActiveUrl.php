<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use Psr\Http\Message\ServerRequestInterface;

class ActiveUrl extends AbstractValidator
{
    function __construct(?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $errorMsg = "{#name} must be a active url";
        }
        $this->errorMsg($errorMsg);
    }


    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $itemData = $param->parsedValue();
        if (!is_string($itemData)) {
            return false;
        }

        if (!filter_var($itemData, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = parse_url($itemData, PHP_URL_HOST);
        return (checkdnsrr($host,'CNAME') or checkdnsrr($host,'A') or checkdnsrr($host,'AAAA'));
    }

    function ruleName(): string
    {
        return "ActiveUrl";
    }
}