<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\DateAfter;
use EasySwoole\HttpAnnotation\Enum\ValueFrom;
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

        $param = new Param("date", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220430");
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-05-06"
        ]);

        $param = new Param("date", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220430");
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-05-06 00:00:00"
        ]);

        $param = new Param("date", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20200630");
        $this->assertEquals(true, $rule->execute($param, $request));

        // func
        $request = new Request();
        $request->withQueryParams([
            "date" => "20220630"
        ]);

        $param = new Param("date", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new DateAfter(date: function () {
            return "20200605";
        });
        $this->assertEquals(true, $rule->execute($param, $request));
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

        $param = new Param("date", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220530");
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be date after 20220530", $rule->errorMsg());

        // 非法参数
        $request = new Request();
        $request->withQueryParams([
            "date" => "bajiu"
        ]);

        $param = new Param("date", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220530");
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be date after 20220530", $rule->errorMsg());

        //字段必须是日期格式。因此传时间戳，失败
        $request = new Request();
        $request->withQueryParams([
            "date" => "1654765394"
        ]);

        $param = new Param("date", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220530");
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be date after 20220530", $rule->errorMsg());
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

        $param = new Param("date", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new DateAfter(date: "20220530", errorMsg: '日期不合法');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("日期不合法", $rule->errorMsg());
    }
}