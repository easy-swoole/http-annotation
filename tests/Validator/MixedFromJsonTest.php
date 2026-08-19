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

class MixedFromJsonTest extends TestCase
{
    function testFromJson()
    {
        $request = new Request();
        $body = new Stream(json_encode([
            'account'=>'accountVal',
            'userInfo'=>[
                'name'=>"easyswoole"
            ]
        ]));
        $request->withBody($body);

        $param = new Param(
            name:'account',
            from: ParamFrom::JSON
        );

        $ret = $param->parsedValue($request);
        $this->assertEquals('accountVal',$ret);


        $param = new Param(
            name:'userInfo',
            from: ParamFrom::JSON,
            subObject: [
                new Param(
                    name: 'name',
                    validate: [
                        new NotEmpty()
                    ]
                ),
                new Param(
                    name: 'age',
                    validate: [
                        new NotEmpty()
                    ]
                )
            ]
        );
        $param->parsedValue($request);

        $vReq = new ValidateRequest($param);
        $vReq->request = $request;
        $vReq->callClass = static::class;
        $vReq->callMethod = __FUNCTION__;

        Utility::validateParam($vReq);

    }
}