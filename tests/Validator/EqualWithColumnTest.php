<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\EqualWithColumn;
use PHPUnit\Framework\TestCase;

class EqualWithColumnTest extends TestCase
{
    // 参数必须与某一列相同
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyAccount",
            'account' => "easyAccount"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account");

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "str" => "0",
            'account' => 0
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account");

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => "0",
            'account' => 0
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);
        // 严格模式 "0" != 0
        $rule = new EqualWithColumn(compare: "account", strict: true);

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));


        // errorMsg
        $request = new Request();
        $request->withQueryParams([
            "str" => "0",
            'account' => 0
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account", strict: true, errorMsg: '测试提示');
        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);
        $this->assertEquals(false,$rule->execute($param,$request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());

    }
}
