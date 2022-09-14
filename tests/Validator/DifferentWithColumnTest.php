<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\DifferentWithColumn;
use PHPUnit\Framework\TestCase;

class DifferentWithColumnTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 值不相等
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
            'account' => "easyAccount"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account");


        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));

        // 值相等,但类型不一样
        $request = new Request();
        $request->withQueryParams([
            "str" => "12",
            'account' => 12
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account",strict: true);

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
        // 值相等
        $request = new Request();
        $request->withQueryParams([
            "str" => "bajiu",
            'account' => "bajiu"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account",strict: true);

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must different with account column", $rule->errorMsg());

        $request = new Request();
        $request->withQueryParams([
            "str" => 89,
            'account' => 89
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account");

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must different with account column", $rule->errorMsg());

        // 值相等,但类型不一样
        $request = new Request();
        $request->withQueryParams([
            "str" => "89",
            'account' => 89
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account");

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must different with account column", $rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "bajiu",
            'account' => "bajiu"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $account = new Param("account");
        $account->parsedValue($request);

        $rule = new DifferentWithColumn(compare: "account",strict: true,errorMsg: '两个参数不能一样');

        $rule->allCheckParams([
            "str" => $param,
            "account" => $account
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("两个参数不能一样", $rule->errorMsg());
    }
}