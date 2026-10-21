<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Message\Stream;
use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;
use PHPUnit\Framework\TestCase;
use EasySwoole\HttpAnnotation\Tests\Support\CoroutineRunner;

class MixedFromJsonTest extends TestCase
{
    function testFromJson()
    {
        CoroutineRunner::run(function () {
            $request = new Request();
            $body = new Stream(json_encode([
                'account'=>'accountVal',
                'userInfo'=>[
                    'name'=>"easyswoole"
                ]
            ]));
            $request->withBody($body);

            $param = new Param(
                type: null, name:'account',
                from: ParamFrom::JSON
            );

            $ret = $param->parsedValue($request);
            $this->assertEquals('accountVal',$ret);


            $param = new Param(
                type: null, name:'userInfo',
                from: ParamFrom::JSON,
            );
            $param->parsedValue($request);

            $vReq = new ValidateRequest($param);
            $vReq->request = $request;
            $vReq->callClass = static::class;
            $vReq->callMethod = __FUNCTION__;

            Utility::validateParam($vReq);
        });
    }
}