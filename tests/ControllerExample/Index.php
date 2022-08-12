<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;

class Index extends Base
{
    #[Api(
        apiName: "home",
        requestPath: "/test/index.html",
        params: [
            new Param(name:"account",from: [Param::GET],validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ],description: new Description("用户登录的账户Id,这个参数一定要有啊"))
        ],
        requestExample: [
            new Example(
                params: [
                    new Param(name:"account",value: "kiss291323003"),
                ],description: new Description("tests/res/json.json",Description::JSON)
            ),
            new Example(
                params: [
                    new Param(name:"account",value: "xmlxmlxmlxml"),
                ],description:  new Description("tests/res/xml.xml",Description::XML)
            ),
            new Example(
                params: [
                    new Param(name: "account",value: "291323003"),
                ],
                description:  new Description(
                    desc: "tests/res/description.md",
                    type: Description::MARKDOWN
                )
            )
        ],
        description: new Description("这是一个接口说明啊啊啊啊")
    )]
    function index(string $account){
        $this->writeJson(200,null,"account is {$account}");
    }
}