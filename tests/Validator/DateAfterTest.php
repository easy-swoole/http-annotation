<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\DateAfter;
use PHPUnit\Framework\TestCase;

class DateAfterTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 日期格式
        $request = new Request();
        $request->withQueryParams([
            "date" => "20220501"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220430");
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));

        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-05-06"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220430");
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));

        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-05-06 00:00:00"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20200630");
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));

        // func
        $request = new Request();
        $request->withQueryParams([
            "date" => "20220630"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 日期不符
        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-05-08"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220530");
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("date must be date after 20220530", $rule->errorMsg($request));

        // 非法参数
        $request = new Request();
        $request->withQueryParams([
            "date" => "bajiu"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new DateAfter(date: "20220530");
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("date must be date after 20220530", $rule->errorMsg($request));

        //字段必须是日期格式。因此传时间戳，失败
        $request = new Request();
        $request->withQueryParams([
            "date" => "1654765394"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new DateAfter(date: "20220530");
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("date must be date after 20220530", $rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => "bajiu"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new DateAfter(date: "20220530", errorMsg: '日期不合法');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("日期不合法", $rule->errorMsg($request));
    }
}