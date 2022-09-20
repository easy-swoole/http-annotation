<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\EqualWithColumn;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
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

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
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

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
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

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
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

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
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

    /*
    * 合法
    */
    public function testValidCase()
    {
        // 值相等，类型一样
        $request = new Request();
        $request->withQueryParams([
            "str" => "easySwoole",
            'account' => "easySwoole"
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account");

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));

        // 值相等，但类型不一样
        $request = new Request();
        $request->withQueryParams([
            "str" => "89",
            'account' => 89
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account");

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));


    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 值相等，但类型不一样
        $request = new Request();
        $request->withQueryParams([
            "str" => "89",
            'account' => 89
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account",strict: true);

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must equal with account column",$rule->errorMsg());

        // 值不相等
        $request = new Request();
        $request->withQueryParams([
            "str" => "89",
            'account' => 98
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account");

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must equal with account column",$rule->errorMsg());

        $request = new Request();
        $request->withQueryParams([
            "str" => 89,
            'account' => 98
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account");

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must equal with account column",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "89",
            'account' => 98
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $account = new Param("account", [ParamFrom::GET]);
        $account->parsedValue($request);

        $rule = new EqualWithColumn(compare: "account",errorMsg: '两个参数必须一样');

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("两个参数必须一样",$rule->errorMsg());
    }
}
