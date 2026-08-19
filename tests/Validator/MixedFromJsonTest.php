<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Message\Stream;
use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
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
            subObject: [
                new Param(
                    name: 'name'
                )
            ]
        );
        $ret = $param->parsedValue($request);
        var_dump($ret);

    }
}