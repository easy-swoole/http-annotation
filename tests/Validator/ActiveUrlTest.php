<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Dispatcher;
use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\ActiveUrl;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use http\Encoding\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class ActiveUrlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ContextManager::getInstance()->set('url', 'https://www.baidu.com');
        Di::getInstance()->set('url', 'https://www.baidu.com');
    }
    /*
     * 合法
     */
    public function testValidCase()
    {
        // 可以连通的网址
        $request = new Request();
        $request->withQueryParams([
            "url" => "https://www.baidu.com"
        ]);

        $param = new Param("url", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new ActiveUrl();
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 无效的网址
        $request = new Request();
        $request->withQueryParams([
            "url" => "this is a str"
        ]);

        $param = new Param("url", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new ActiveUrl();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("url must a active url", $rule->errorMsg());

        // 有效网址但不能连通
        $request = new Request();
        $request->withQueryParams([
            "url" => "https://www.noneDnsAnswerDomain.com"
        ]);

        $param = new Param("url", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new ActiveUrl();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("url must a active url", $rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        // 有效网址但不能连通
        $request = new Request();
        $request->withQueryParams([
            "url" => "https://www.noneDnsAnswerDomain.com"
        ]);

        $param = new Param("url", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new ActiveUrl(errorMsg: '您输入的网址无效');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("您输入的网址无效", $rule->errorMsg());
    }

}