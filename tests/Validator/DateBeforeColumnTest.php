<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\DateBefore;
use EasySwoole\HttpAnnotation\Validator\DateBeforeColumn;
use PHPUnit\Framework\TestCase;

class DateBeforeColumnTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 日期格式
        $request = new Request();
        $request->withQueryParams([
            "date"  => "20220101",
            "date1" => "20220430"
        ]);

        $param = new Param(name: "date");
        $param->parsedValue($request);

        $date1 = new Param(name: "date1");
        $date1->parsedValue($request);

        $rule = new DateBeforeColumn(compare: "date1");

        $rule->allCheckParams([
            "date"  => $param,
            "date1" => $date1
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "date"  => "2022-01-06",
            "date1" => "20220630",
        ]);

        $param = new Param(name: "date");
        $param->parsedValue($request);

        $date1 = new Param(name: "date1");
        $date1->parsedValue($request);

        $rule = new DateBeforeColumn(compare: "date1");

        $rule->allCheckParams([
            "date"  => $param,
            "date1" => $date1
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "date"  => "2022-01-06 00:00:00",
            "date1" => "20220630",
        ]);

        $param = new Param(name: "date");
        $param->parsedValue($request);

        $date1 = new Param(name: "date1");
        $date1->parsedValue($request);

        $rule = new DateBeforeColumn(compare: "date1");

        $rule->allCheckParams([
            "date"  => $param,
            "date1" => $date1
        ]);

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
            "date"  => "2022-06-08",
            "date1" => "20220530",
        ]);

        $param = new Param(name: "date");
        $param->parsedValue($request);

        $date1 = new Param(name: "date1");
        $date1->parsedValue($request);

        $rule = new DateBeforeColumn(compare: "date1");

        $rule->allCheckParams([
            "date"  => $param,
            "date1" => $date1
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be date before date1 column", $rule->errorMsg());

        // 非法参数
        $request = new Request();
        $request->withQueryParams([
            "date"  => "xuesi",
            "date1" => "20220530",
        ]);

        $param = new Param(name: "date");
        $param->parsedValue($request);

        $date1 = new Param(name: "date1");
        $date1->parsedValue($request);

        $rule = new DateBeforeColumn(compare: "date1");

        $rule->allCheckParams([
            "date"  => $param,
            "date1" => $date1
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be date before date1 column", $rule->errorMsg());

        //字段必须是日期格式。因此传时间戳，失败
        $request = new Request();
        $request->withQueryParams([
            "date"  => "1654765394",
            "date1" => "20220530",
        ]);

        $param = new Param(name: "date");
        $param->parsedValue($request);

        $date1 = new Param(name: "date1");
        $date1->parsedValue($request);

        $rule = new DateBeforeColumn(compare: "date1");

        $rule->allCheckParams([
            "date"  => $param,
            "date1" => $date1
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be date before date1 column", $rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date"  => "xuesi",
            "date1" => "20220530",
        ]);

        $param = new Param(name: "date");
        $param->parsedValue($request);

        $date1 = new Param(name: "date1");
        $date1->parsedValue($request);

        $rule = new DateBeforeColumn(compare: "date1", errorMsg: "日期不合法");

        $rule->allCheckParams([
            "date"  => $param,
            "date1" => $date1
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("日期不合法", $rule->errorMsg());
    }
}
