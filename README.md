# HttpAnnotation

EasySwoole HTTP 控制器的 PHP 原生属性组件。通过 `#[Api]`、`Param` 等定义请求方法、参数来源、类型转换、校验规则及接口说明，并生成支持在线试运行的独立 HTML 文档。

## 安装与环境

```bash
composer require easyswoole/http-annotation
```

需要 PHP **8.1 或以上**，以及 JSON、mbstring、DOM、SimpleXML、libxml 扩展。HTTP 处理依赖 `easyswoole/http` 3.x；运行下面的 Swoole 服务示例还需要实际安装 Swoole 扩展，IDE helper 不提供运行能力。

## 定义控制器

控制器继承 `AnnotationController`。`ApiGroup` 定义文档分组，公开的非静态方法通过 `Api` 定义接口。

例如将下面的控制器保存为 `App/HttpController/Common/Message.php`，并在业务项目的 Composer 中配置 `App\` 自动加载。

```php
<?php

namespace App\HttpController\Common;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ContentType;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\IsFile;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;
use EasySwoole\HttpAnnotation\Validator\Required;

#[ApiGroup(groupName: 'Common.Message', description: '消息接口')]
class Message extends AnnotationController
{
    #[Api(
        allowMethod: HttpMethod::GET,
        requestParam: [
            new Param(name: 'page', from: ParamFrom::GET, type: ParamType::INT, value: 1),
        ],
        description: '分页读取消息'
    )]
    public function list(int $page)
    {
        $this->writeJson(200, ['page' => $page]);
    }

    #[Api(
        allowMethod: HttpMethod::POST,
        acceptContentType: ContentType::JSON,
        requestParam: [
            new Param(
                name: 'msgId',
                from: ParamFrom::JSON,
                type: ParamType::STRING,
                validate: [new Required(), new NotEmpty()]
            ),
            new Param(name: 'testHeader', from: ParamFrom::HEADER),
        ],
        description: '根据消息 ID 读取详情'
    )]
    public function detail(string $msgId, string $testHeader)
    {
        $this->writeJson(200, ['msgId' => $msgId, 'testHeader' => $testHeader]);
    }

    #[Api(
        allowMethod: HttpMethod::POST,
        acceptContentType: ContentType::FORM_DATA,
        requestParam: [
            new Param(
                name: 'userThumb',
                from: ParamFrom::FILE,
                type: ParamType::FILE,
                validate: [new Required(), new IsFile()]
            ),
        ]
    )]
    public function update(array $data)
    {
        // $data['userThumb'] 是上传文件对象，由业务代码处理保存。
        $this->writeJson(200, ['received' => isset($data['userThumb'])]);
    }
}
```

普通方法的形参名称需要与属性中定义的参数名称一致。方法只有一个 `array` 形参时，组件将接口的 `requestParam` 打包成数组传入。此数组模式只收集接口参数；公共参数可在 `onRequest` 中接收。

## 请求方法、Content-Type 与参数来源

`Api::allowMethod` 为单个 `HttpMethod` 枚举，默认 `GET`，不接受方法数组。

构造函数检查接口定义的一致性，不合规时抛出 `Exception\Annotation`。文档扫描或首次解析控制器属性时也会触发检查。

| 请求方法 / Content-Type | 允许的请求体参数来源 |
| --- | --- |
| GET、HEAD：`acceptContentType` 必须为 `null` | 不允许 POST、JSON、XML、RAW_POST、FILE |
| FORM_DATA | POST、FILE |
| FORM_URLENCODED | POST |
| JSON | JSON |
| XML | XML |
| RAW | RAW_POST |

GET、HEAD 的 Content-Type 默认是 `null`；其他方法未指定时默认为 `FORM_DATA`。GET、HEADER、COOKIE、DI、CONTEXT 不属于请求体来源，可与上表中的内容类型一起定义。

特别注意：

- `Param::from` 默认是 `[ParamFrom::GET]`。**GET、HEAD 接口必须显式指定合规来源**，例如 `from: ParamFrom::GET`；JSON、XML、RAW 接口也应显式指定匹配的来源。
- `from` 可以是单个枚举或数组。数组不能为空，每一项都必须合规；不能把不合规来源当作“备用来源”。读取时按数组顺序选择第一个命中的来源。
- `requestParam` 必须由 `Param` 对象组成，同一接口内不允许重复参数名称。
- 这里校验的是 **Api 定义**。当前控制器运行时会检查 HTTP 方法，但没有单独核验实际请求的 `Content-Type` 是否与 `acceptContentType` 一致。需要严格限制实际请求头时，应在业务层增加检查。
- `onRequest` 中的公共 `Param` 不经过 `Api` 构造函数这项来源校验，公共参数也应根据使用它的接口合理定义。

## Param 常用配置

| 属性 | 用途与默认值 |
| --- | --- |
| `name` | 参数名，用于取值和方法形参匹配 |
| `from` | 参数来源，默认 GET、POST |
| `type` | 默认 `ParamType::STRING`；设置为 `null` 可保留原值 |
| `value` | 未取到参数时的默认值，默认 `null` |
| `validate` | 校验器对象数组，默认空数组 |
| `description` | 普通字符串或 `Text`；参数说明不接受 `Markdown` |
| `deprecated` | 标记废弃，文档显示“已废弃”，不会禁止调用 |
| `ignoreAction` | 忽略该参数的 action 名称列表 |
| `ignorePassArgWhenNotSet` | 单数组形参模式中，未传入时不加入参数数组 |

支持 STRING、INT、DOUBLE、REAL、FLOAT、BOOLEAN、FILE、NULL_WHILE_EMPTY 类型。转换发生在校验前，转换本身不是合法性校验，必要时仍需配置校验器。

- 上传文件同时配置 `from: ParamFrom::FILE` 和 `type: ParamType::FILE`，避免默认字符串转换影响文件对象。
- `value` 在实际请求中也会参与类型转换；文档默认值列展示声明时的原值，保留 `0`、`false`、空字符串，`null` 显示为 `-`。
- `Required` 检查是否设置参数，`NotEmpty` 检查值是否为空，两者含义不同。Header 参数还应根据需要使用 `NotEmpty`，当前 Header 解析分支即使请求头缺失也会标记为已设置。
- 想用 `Optional` 保留“未传入且为 null”的语义时，应显式设置 `type: null`，避免默认 STRING 将 `null` 转成空字符串。
- BOOLEAN 使用 PHP 布尔转换；字符串 `"false"` 会被转成 `true`。表单布尔值建议使用 `1`、`0`。
- JSON 参数按字段名从请求体解码结果取值；XML 参数从根节点的直接子节点取值；RAW_POST 返回整个请求体。

内置校验器位于 `src/Validator`，包括必填、长度、数值、日期、邮箱、文件和字段比较等。当前控制器未向 `ValidateRequest::allDefineParams` 传入全部参数，因此跨字段校验器在控制器集成流程中仍有使用限制。

## 公共参数与继承

在 `onRequest` 上使用可重复的 `#[Param]` 定义公共参数。接口定义的同名参数优先覆盖公共定义。

```php
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;

abstract class BaseController extends AnnotationController
{
    #[Param(name: 'token', from: ParamFrom::HEADER, validate: [new NotEmpty()])]
    public function onRequest(?string $action, ?array $data = null): ?bool
    {
        // 此处可读取 $data['token']，执行实际鉴权逻辑。
        return true;
    }
}
```

覆盖父类 `onRequest` 后，使用 `#[ExtendParam]` 合并父类的公共参数；使用 `#[ExtendParam(parentParamsName: ['token'])]` 只继承指定参数，子类同名定义优先。`ExtendParam` 仅对 `onRequest` 有效。

`ignoreAction` 可用于公共参数豁免，例如开放接口不要求 token。不要在单数组传参模式中对接口自身的参数使用 `ignoreAction`：当前数组收集分支仍会访问这些被移除的参数。

## 前置回调与属性注入

`#[PreCall([Hooks::class, 'before'])]` 可用于控制器类或接口方法，回调返回 `false` 时中止后续执行。

- 类级回调接收 `($actionName, $request, $response)`，在参数解析前执行。
- 方法级回调接收 `($request, $response)`，在参数解析、校验后执行。
- `#[Di(key: 'service')]` 和 `#[Context(key: 'user')]` 可给控制器公开或受保护属性注入值，同一属性不能同时定义这两种属性。注入发生在方法级前置回调之后。

参数解析和校验在调用父控制器 `__hook()` 之前执行，异常不进入父方法内部的 `onException()` 捕获块。接入项目时应核对上层分发器的异常处理，按业务要求返回错误响应。

## 接口说明与示例

`ApiGroup::description` 和 `Api::description` 支持普通字符串、`Text`、`Markdown`。文档全局说明使用 `Config::setDescription()`，传入 `Text` 或 `Markdown` 对象。

```php
use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Bean\Description\Text;

$markdown = new Markdown(__DIR__ . '/docs/message.md');
$text = new Text('消息接口说明');
$textFile = new Text(__DIR__ . '/docs/message.txt', isFile: true);
```

`Markdown` 的构造参数是 **Markdown 文件路径**，不是 Markdown 内容字符串。生成文档时使用 `easyswoole/parsedown` 转成 HTML；Text 按纯文本转义展示。说明文件必须存在，建议用 `__DIR__` 拼接绝对路径。Markdown 渲染可能保留内嵌 HTML，说明文件应来自可信内容。

`Api::requestExamples`、`responseExamples` 接收示例对象数组：

```php
use EasySwoole\HttpAnnotation\Bean\Example\Array2Json;
use EasySwoole\HttpAnnotation\Bean\Example\ArrayForm;
use EasySwoole\HttpAnnotation\Bean\Example\Raw;

$requestExamples = [
    new Array2Json(['msgId' => '123']),
    new ArrayForm(['page' => 1]),
    new Raw('<request><msgId>123</msgId></request>'),
];
$responseExamples = [
    new Array2Json(['code' => 200, 'data' => []]),
    new Array2Json(['code' => 400, 'message' => '参数错误'], isSuccessResponse: false),
];
```

Raw 也支持 `isFile: true`。响应示例通过 `isSuccessResponse` 分到成功、失败区域，各类示例分别从 1 编号。`Api::deprecated` 只控制废弃标识，不禁止接口执行。

## 生成接口文档

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Document\Document;

$document = new Document(
    __DIR__ . '/App/HttpController',
    'App\\HttpController'
);
$document->getConfig()->setProjectName('消息服务');
$document->getConfig()->setHost('http://127.0.0.1:9501');
$document->getConfig()->setDescription(new Markdown(__DIR__ . '/docs/introduction.md'));

file_put_contents(__DIR__ . '/api.html', $document->scan2html());
// 也可 echo $document->scan2html()，作为 HTTP 响应输出。
```

- `scanAllApiGroup()` 收集控制器属性；`scan2ArrayMap()` 返回文档树；`scan2html()` 返回完整 HTML 字符串，不自动保存文件。
- 扫描的控制器需能被 Composer 自动加载、继承 `AnnotationController` 且定义 `ApiGroup`。仅带 `Api` 的公开非静态方法进入文档；每个文件目前只使用扫描到的第一个类。
- 分组名称不可重复。`Common.Message`、`Common.Profile` 生成 Common → Message / Profile → action 的侧边栏。没有接口且没有有效后代的节点不展示。
- 控制器命名空间参数必须与实际目录对应。文档路径由控制器类名和方法名生成，各路径层级首字母转小写，尾部 Index 控制器与 index action 有简写处理。
- `Api` 构造函数当前没有 `requestPath` 参数，不要使用 `requestPath: ...`；`registerRouter` 也尚未实现自动注册路由。文档路径应与业务路由配置一致。
- `responseParam` 虽可定义，当前 HTML 模板尚未展示响应参数表，可通过响应示例说明返回结构。
- 修改定义后应重新生成 HTML、刷新浏览器；常驻服务中的属性缓存还需要通过重启相关工作进程刷新。

## “立即尝试”使用与限制

点击 action 标题旁的“立即尝试”，填写参数后运行。

- 请求方法固定使用 `Api::allowMethod`，不可在窗口内切换；地址可编辑，默认由配置 host 和接口路径组合。
- 表单类型显示参数输入框，预填 `Param::value`。勾选的参数才发送，编辑参数后会自动勾选；FILE 类型显示文件选择器，选中文件后自动勾选。
- JSON、XML、RAW 使用一个多行输入框提交完整请求体，JSON/XML 会检查格式。Header 和 URL 参数仍可单独填写。
- Header 来源的参数作为 HTTP 请求头发送；GET 来源作为 URL 查询参数发送。DI、CONTEXT 不由浏览器填写，Cookie 由浏览器管理。
- 所有 HTTP 状态都显示，包括 4xx、5xx；结果区展示状态码、耗时、响应内容及浏览器允许读取的响应头。JSON 响应会格式化，空响应也有提示。
- 超时可设置为 1–300 秒。网络异常、超时和取消会显示结果；点击关闭或在弹窗内按 Esc 会取消正在进行的请求。
- 打开弹窗时焦点移到关闭按钮，关闭后回到“立即尝试”。焦点在浏览器地址栏、开发者工具或其他窗口时，页面无法接收 Esc。

特别注意浏览器请求的实际限制：

1. 建议通过 HTTP 服务访问生成文档。例如在仓库目录执行 `php -S 127.0.0.1:8080`，再打开 `http://127.0.0.1:8080/api.html`。该服务只用于静态文档预览，业务接口仍需运行自己的服务。
2. 请求使用 `credentials: 'include'`。跨域接口应允许文档的具体 Origin、凭据、请求方法和自定义 Header；需要凭据时不能用 `Access-Control-Allow-Origin: *`。HTTPS 文档请求 HTTP 接口还可能被混合内容策略阻止。
3. 浏览器禁止设置的请求头不能由表单强行发送；跨域响应头的可见范围由服务端 CORS 配置决定。连接失败或 CORS 拦截时通常无法获得 HTTP 状态码。
4. RAW 试运行默认使用 `text/plain`。当前没有单独配置 RAW MIME 类型的界面。
5. 当前普通表单在有文件时使用 multipart/form-data，无文件时使用 URL 编码表单；即使 Api 声明 FORM_DATA，也不会强制无文件表单使用 multipart。服务端严格限制内容类型时需要注意这一差异。
6. HTML 是生成时的静态快照，示例数据、默认值和说明会写入文件，公开前检查是否包含不应公开的信息。

## 启动 Swoole HTTP 服务

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use EasySwoole\Http\Dispatcher;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Swoole\Http\Server;

$dispatcher = new Dispatcher();
$dispatcher->setNamespacePrefix('App\\HttpController');

$http = new Server('127.0.0.1', 9501);
$http->set(['worker_num' => 1]);
$http->on('request', function ($request, $response) use ($dispatcher) {
    $requestPsr = new Request($request);
    $responsePsr = new Response($response);
    $dispatcher->dispatch($requestPsr, $responsePsr);
    $responsePsr->__response();
});
$http->start();
```

控制器路径、命名空间及文档 host 需要与业务项目保持一致。本仓库的可运行文档生成示例见 `test.php`，控制器示例见 `tests/ControllerExample`。

## 验证

开发测试使用 PHPUnit 13.4，需要 PHP **8.4 或以上**；库本身的最低 PHP 版本仍为 8.1。安装开发依赖后，默认读取 `phpunit.xml.dist`，可运行完整测试或指定目录：

```bash
php vendor/bin/phpunit
php vendor/bin/phpunit tests/Attributes
php vendor/bin/phpunit tests/Document
node tests/Document/try-runner.test.cjs
node tests/Document/document-ui.test.cjs
```

Node 测试使用内置的 Fetch、File、FormData 等 API，需使用提供这些全局对象的现代 Node.js（建议 20+）。前端交互测试采用 DOM 替身；浏览器焦点、布局和跨域行为仍需在实际浏览器与服务环境中验证。

验证器测试通过 `Param(type: null, ...)` 保留原始输入类型，专门验证规则行为；默认 `ParamType::STRING` 会先将输入转换为字符串。PHPUnit 数据提供器使用 `#[DataProvider(...)]`，提供器方法必须为 `public static`。

参数来源包含 `ParamFrom::FILE` 时，只允许单一来源 `from: [ParamFrom::FILE]`（兼容 `from: ParamFrom::FILE`）；与其他来源混用或重复定义 FILE 会抛出异常。
