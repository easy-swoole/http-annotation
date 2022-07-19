<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AbstractValidator;
use EasySwoole\HttpAnnotation\Attributes\Validator\Func;
use PHPUnit\Framework\TestCase;

class FuncTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "fun" => "123456789",
        ]);

        $param = new Param("fun");
        $param->parsedValue($request);

        $rule = new Func(function (AbstractValidator $validator) {
            return $validator->getRequest()->getQueryParams()['fun'] == "123456789";
        });

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "fun" => "111",
        ]);

        $param = new Param("fun");
        $param->parsedValue($request);

        $rule = new Func(function (AbstractValidator $validator) {
            return $validator->getRequest()->getQueryParams()['fun'] == "222";
        });

        $this->assertEquals(false, $rule->execute($param, $request));

        // errorMsg
        $request = new Request();
        $request->withQueryParams([
            "fun" => "111",
        ]);

        $param = new Param("fun");
        $param->parsedValue($request);

        $rule = new Func(function (AbstractValidator $validator) {
            return $validator->getRequest()->getQueryParams()['fun'] == "222";
        }, errorMsg: '测试提示');

        $this->assertEquals(false, $rule->execute($param, $request));
        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}

