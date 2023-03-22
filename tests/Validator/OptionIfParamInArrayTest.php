<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\ValidateFail;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\DifferentWithColumn;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamMiss;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValInArray;
use EasySwoole\HttpAnnotation\Validator\Required;
use PHPUnit\Framework\TestCase;

class OptionIfParamInArrayTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "account"=>"admin",
        ]);

        $num = new Param(
            name:"num",
            validate: [
                new OptionalIfParamValInArray("account",[
                    'admin'
                ]),
                new Integer()
            ]
        );
        $num->parsedValue($request);

        $account = new Param(
            name: "account"
        );
        $account->parsedValue($request);

        $allDefineParams = [
            'account'=>$account,
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

    function testEmpty()
    {
        $request = new Request();
        $request->withQueryParams([
            "account"=>"",
        ]);

        $num = new Param(
            name:"num",
            validate: [
                new OptionalIfParamValInArray("account",[
                    'admin'
                ]),
                new Integer()
            ]
        );
        $num->parsedValue($request);

        $account = new Param(
            name: "account"
        );
        $account->parsedValue($request);

        $allDefineParams = [
            'account'=>$account,
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

    function testFail()
    {
        $request = new Request();
        $request->withQueryParams([
            "account"=>"normalUser",
        ]);

        $num = new Param(
            name:"num",
            validate: [
                new OptionalIfParamValInArray("account",[
                    'admin'
                ]),
                new Integer()
            ]
        );
        $num->parsedValue($request);

        $account = new Param(
            name: "account"
        );
        $account->parsedValue($request);

        $allDefineParams = [
            'account'=>$account,
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
            "account"=>"normal",
            "num"=>111
        ]);

        $num = new Param(
            name:"num",
            validate: [
                new OptionalIfParamValInArray("account",[
                    'admin'
                ]),
                new Integer()
            ]
        );
        $num->parsedValue($request);

        $account = new Param(
            name: "account"
        );
        $account->parsedValue($request);

        $allDefineParams = [
            'account'=>$account,
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