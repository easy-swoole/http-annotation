<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
use PHPUnit\Framework\TestCase;

class OptionalTest extends TestCase
{

    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            'account' => "easyAccount"
        ]);

        $num = new Param(
            name:"num",
            validate: [
                new Optional(),
                new Integer()
            ]
        );
        $num->parsedValue($request);


        $allDefineParams = [
            'num'=>$num,
        ];

        $ret = true;
        $rules = $num->validate;
        /** @var AbstractValidator $rule */
        foreach ($rules as $rule){
            $rule->allCheckParams($allDefineParams);
            $ret = $rule->execute($num,$request);
            if(!$ret){
                break;
            }
        }

        $this->assertEquals(true,$ret);
    }

    function testFail()
    {
        $request = new Request();
        $request->withQueryParams([
            'account' => "easyAccount",
            "num"=>"asd"
        ]);

        $num = new Param(
            name:"num",
            validate: [
                new Optional(),
                new Integer()
            ]
        );
        $num->parsedValue($request);


        $allDefineParams = [
            'num'=>$num,
        ];

        $ret = true;
        $rules = $num->validate;
        /** @var AbstractValidator $rule */
        foreach ($rules as $rule){
            $rule->allCheckParams($allDefineParams);
            $ret = $rule->execute($num,$request);
            if(!$ret){
                break;
            }
        }

        $this->assertEquals(false,$ret);
    }


    function testSuccess()
    {
        $request = new Request();
        $request->withQueryParams([
            'account' => "easyAccount",
            "num"=>1
        ]);

        $num = new Param(
            name:"num",
            validate: [
                new Optional(),
                new Integer()
            ]
        );
        $num->parsedValue($request);


        $allDefineParams = [
            'num'=>$num,
        ];

        $ret = true;
        $rules = $num->validate;
        /** @var AbstractValidator $rule */
        foreach ($rules as $rule){
            $rule->allCheckParams($allDefineParams);
            $ret = $rule->execute($num,$request);
            if(!$ret){
                break;
            }
        }

        $this->assertEquals(true,$ret);
    }
}
