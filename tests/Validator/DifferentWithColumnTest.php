<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\DifferentWithColumn;
use PHPUnit\Framework\TestCase;

class DifferentWithColumnTest extends TestCase
{
    function testNormal(){
        $request = new Request();
        $request->withQueryParams([
            "str"=>"easyswoole",
            'account'=>"easyAccount"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account");


        $rule->allCheckParams([
           "str"=>$param,
            "account"=>$account
        ]);

        $this->assertEquals(true,$rule->execute($param,$request));




        $request = new Request();
        $request->withQueryParams([
            "str"=>"0",
            'account'=>0
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account");


        $rule->allCheckParams([
            "str"=>$param,
            "account"=>$account
        ]);

        $this->assertEquals(false,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "str"=>"0",
            'account'=>0
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account",strict: true);


        $rule->allCheckParams([
            "str"=>$param,
            "account"=>$account
        ]);

        $this->assertEquals(true,$rule->execute($param,$request));

    }
}