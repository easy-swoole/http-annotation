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

## 在控制器命名空间下注册路由

路由使用 `easyswoole/http` 的 `AbstractRouter`。将 `Router.php` 放在控制器根目录，例如 `App/HttpController/Router.php`，命名空间必须与 Dispatcher 的控制器命名空间一致：

```php
<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
        // 按原始 URL 路径匹配，使路由定义中的前导斜杠和路径保持一致。
        $this->setPathInfoMode(false);

        $routeCollector->addRoute('GET', '/api/messages', '/Common/Message/list');
        $routeCollector->addRoute('POST', '/api/messages/detail', '/Common/Message/detail');
        $routeCollector->addRoute('POST', '/api/profile/upload', '/Common/Message/update');
    }
}
```

服务启动时配置控制器命名空间：

```php
use EasySwoole\Http\Dispatcher;

$dispatcher = new Dispatcher();
$dispatcher->setNamespacePrefix('App\\HttpController');
```

Dispatcher 在首次分发请求时自动查找 `App\HttpController\Router` 并调用 `initialize()`，不需要手动实例化 Router。业务项目须配置 `App\` 的 Composer 自动加载。

`addRoute()` 的第三个参数是相对于控制器根命名空间的“控制器路径/action”，例如 `/Common/Message/list` 会调用 `App\HttpController\Common\Message::list()`。它不是完整 PHP 类名。请求方法须与对应 `Api::allowMethod` 一致，路由映射不会绕过参数校验或改变参数来源。

默认未命中自定义路由时仍会尝试按控制器路径分发。若只允许已注册的路由，可在 `initialize()` 中调用 `$this->setGlobalMode(true)`；未命中的请求将不再回退到控制器路径分发。

当前 `Api::registerRouter` 尚未实现自动注册；需要如上显式注册。文档生成器也不会读取自定义 Router 的映射，生产文档中的请求地址需与实际路由保持一致。

### pathInfo 模式与原始路径

`setPathInfoMode(true)` 为默认值，匹配前会通过 `UrlParser::pathInfo()` 处理路径：去掉文件扩展名，并将末尾名称为 `index` 的文件折叠到父路径。`setPathInfoMode(false)` 则使用 URI 的原始路径匹配，保留扩展名。两种模式都不包含查询字符串。

| 请求 URL | 开启 pathInfo 时用于匹配的路径 | 关闭 pathInfo 时用于匹配的路径 |
| --- | --- | --- |
| `/res/example.txt` | `/res/example` | `/res/example.txt` |
| `/res/example.html?version=1` | `/res/example` | `/res/example.html` |
| `/res/index.html` | `/res` | `/res/index.html` |
| `/Common/Message/list` | `/Common/Message/list` | `/Common/Message/list` |

因此，要区分 `.txt` 和 `.html`，需要关闭 pathInfo。此开关只决定路由匹配使用的路径；后续控制器路径分发仍会进行 pathInfo 处理。

### 正则匹配文件路径并在回调中响应

FastRoute 使用 `{参数名:正则}` 定义路径参数，不需要加 `^`、`$` 或正则分隔符。正则内部需要分组时使用非捕获组 `(?:...)`。下面的路由匹配 `/res/example.txt`、`/res/example.html`，文件名允许字母、数字、下划线和连字符：

```php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
        $this->setPathInfoMode(false);
        // 将匹配的 name、ext 注入 GET 参数；同名路由参数优先于原查询参数。
        $this->parseParams(self::PARSE_PARAMS_IN_GET);

        $routeCollector->addRoute(
            'GET',
            '/res/{name:[a-zA-Z0-9_-]+}.{ext:txt|html}',
            function (Request $request, Response $response) {
                $params = $request->getQueryParams();
                $name = $params['name'];
                $ext = $params['ext'];

                $response->withStatus(200);
                if ($ext === 'html') {
                    $response->withHeader('Content-Type', 'text/html; charset=utf-8');
                    $response->write('<p>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</p>');
                } else {
                    $response->withHeader('Content-Type', 'text/plain; charset=utf-8');
                    $response->write('资源名称：' . $name);
                }

                // 已处理请求，终止 Dispatcher 的后续控制器分发。
                return false;
            }
        );
    }
}
```

回调仅接收 `Request`、`Response`，没有第三个路径参数数组。默认路径参数保存在协程上下文；上述示例通过 `parseParams()` 改为注入 GET，控制器也可使用 `ParamFrom::GET` 读取。`write()`、`withStatus()`、`withHeader()` 设置响应，服务入口在 `dispatch()` 返回后仍按启动示例调用 `$responsePsr->__response()` 输出响应。

### 默认从 Context 读取路由参数

未调用 `parseParams()` 修改注入方式时，默认使用 `AbstractRouter::PARSE_PARAMS_IN_CONTEXT`。匹配成功后，所有路径参数以一个数组存入当前协程 Context，键为 `AbstractRouter::PARSE_PARAMS_CONTEXT_KEY`，不会自动加入 GET 或 POST 参数。

例如沿用 `/res/{name:[a-zA-Z0-9_-]+}.{ext:txt|html}`，去掉上例的 `$this->parseParams(self::PARSE_PARAMS_IN_GET)`，在路由回调或后续控制器 action 中读取：

```php
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\AbstractRouter;

$routeParams = ContextManager::getInstance()->get(AbstractRouter::PARSE_PARAMS_CONTEXT_KEY) ?? [];
$name = $routeParams['name'] ?? null;
$ext = $routeParams['ext'] ?? null;

// 请求 /res/example.html 时：
// $routeParams 为 ['name' => 'example', 'ext' => 'html']。
```

读取应在当前请求处理的协程内完成。未匹配路由或没有该 Context 数据时，上述写法返回空数组。`ParamFrom::CONTEXT` 按参数名称读取 Context 的顶层键，不会自动从 `PARSE_PARAMS_CONTEXT_KEY` 数组中取同名字段；需要通过上述方式读取，或改用 `parseParams(AbstractRouter::PARSE_PARAMS_IN_GET)` 将路径参数注入 GET。

### 回调如何拦截或继续分发

`addRoute()` 的第三个参数可以是闭包、可调用对象或其他合法 callable。Dispatcher 根据回调返回值决定下一步：

| 回调返回值 | 后续行为 |
| --- | --- |
| `false` | 立即结束分发，适合已响应或鉴权失败的请求 |
| 控制器路径字符串，例如 `'/Common/Message/list'` | 转交指定控制器 action，即使开启全局模式也会继续 |
| `true`、`null` 或其他非字符串、非 false 值 | 非全局模式下继续按原请求路径分发；全局模式下结束分发 |

例如，在 `initialize()` 中注册一个鉴权回调，失败时响应并拦截，通过时转交控制器：

```php
$routeCollector->addRoute('GET', '/api/messages', function (Request $request, Response $response) {
    // 示例条件；实际项目应替换为自己的鉴权逻辑。
    if ($request->getHeaderLine('Authorization') === '') {
        $response->withStatus(401);
        $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->write(json_encode(['message' => '请先登录'], JSON_UNESCAPED_UNICODE));
        return false;
    }

    return '/Common/Message/list';
});
```

如果回调只做日志记录，并希望继续按原控制器路径分发，可注册原路径 `/Common/Message/list`，处理完返回 `true`，同时保持 `setGlobalMode(false)`。继续分发表示进入控制器处理，不会继续执行其他路由；FastRoute 每次请求只选择一个匹配结果。回调抛出异常时，Dispatcher 调用异常处理并终止后续分发。

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
| `from` | 参数来源数组，默认 `[ParamFrom::GET]`，兼容单个枚举 |
| `type` | 默认 `ParamType::STRING`；设置为 `null` 可保留原值 |
| `value` | 未取到参数时的默认值，默认 `null` |
| `validate` | 校验器对象数组，默认空数组 |
| `description` | 普通字符串或 `Text`；参数说明不接受 `Markdown` |
| `deprecated` | 标记废弃，文档显示“已废弃”，不会禁止调用 |
| `ignoreAction` | 忽略该参数的 action 名称列表 |
| `ignorePassArgWhenNotSet` | 单数组形参模式中，未传入时不加入参数数组 |

完整的验证器参数、规则行为和使用示例见 [验证器使用指南](Validate.md)。

`IsUrl(allowProtocols: ['http', 'https'])` 可限制 URL 协议；`IsIp(mode: 'IPV4')` 或 `IsIp(mode: 'IPV6')` 可限制 IP 版本。这两个规则的校验配置参数位于首位，`errorMsg` 位于最后；默认分别不额外限制协议、允许 IPv4/IPv6。

支持 STRING、INT、DOUBLE、REAL、FLOAT、BOOLEAN、FILE、NULL_WHILE_EMPTY 类型。转换发生在校验前，转换本身不是合法性校验，必要时仍需配置校验器。

### 未传参数与空值转换

`ignorePassArgWhenNotSet: true` 只在 action 使用**单个 `array` 形参接收参数**时生效：没有取到参数（`hasSet() === false`）时，不将该参数加入传给 action 的数组。独立形参模式不应用此选项。它不跳过参数校验，因此配置 `Required` 等规则时，未传参数仍可能校验失败。

GET、POST、JSON 字段通过 `isset` 判断是否存在，未传字段和显式传入 `null` 均视为未设置；空字符串 `''`、整数 `0`、字符串 `'0'`、`false` 则视为已设置，仍会传入。声明了默认 `value` 也不会改变 `hasSet()`，未设置时仍会省略该参数。

`type: ParamType::NULL_WHILE_EMPTY` 使用 PHP 的 `empty()` 规则转换值，但特别保留整数 `0` 和字符串 `'0'`：

| 原始值 | 转换后的值 |
| --- | --- |
| `null`、`''`、`false`、`[]`、浮点 `0.0` | `null` |
| 整数 `0`、字符串 `'0'` | 保留原值和类型 |
| 空格字符串 `' '`、其他非空值 | 保留原值和类型 |

转换不会改变是否已设置参数。组合使用这两个选项时，**已传入的空字符串会转为 `null`，但不会从参数数组中省略**。

例如，定义一个可选的请求限制周期参数，action 使用数组接收参数：

```php
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\Optional;

#[Api(requestParam: [
    new Param(
        name: 'queryLimitPeriod',
        from: [ParamFrom::GET],
        validate: [new Optional()],
        description: '请求限制周期，单位秒',
        type: ParamType::NULL_WHILE_EMPTY,
        ignorePassArgWhenNotSet: true,
    ),
])]
public function updateLimit(array $params): void
{
    // 未传参数：$params 为 []。
    // 传入 queryLimitPeriod=''：$params 为 ['queryLimitPeriod' => null]。
}
```

下面对比上述配置中 `ignorePassArgWhenNotSet` 开启和关闭时的结果。默认值为 `null`；GET、POST、JSON 字段的是否设置判断规则相同，表格假设参数校验通过：

| 请求中的 `queryLimitPeriod` | `hasSet()` | 解析后的值 | `ignorePassArgWhenNotSet: true` 的 action 数组 | `ignorePassArgWhenNotSet: false` 的 action 数组 |
| --- | --- | --- | --- | --- |
| 未传入 | `false` | `null`（不做类型转换） | `[]`，不包含该字段 | `['queryLimitPeriod' => null]` |
| 显式传入 `null` | `false` | `null`（不做类型转换） | `[]`，不包含该字段 | `['queryLimitPeriod' => null]` |
| 空字符串 `''` | `true` | `null` | `['queryLimitPeriod' => null]` | `['queryLimitPeriod' => null]` |
| `false`、空数组 `[]`、浮点数 `0.0` | `true` | `null` | `['queryLimitPeriod' => null]` | `['queryLimitPeriod' => null]` |
| 整数 `0` | `true` | 整数 `0` | `['queryLimitPeriod' => 0]` | `['queryLimitPeriod' => 0]` |
| 字符串 `'0'` | `true` | 字符串 `'0'` | `['queryLimitPeriod' => '0']` | `['queryLimitPeriod' => '0']` |
| 空格字符串 `' '` | `true` | 字符串 `' '` | `['queryLimitPeriod' => ' ']` | `['queryLimitPeriod' => ' ']` |
| 非空字符串 `'30'` | `true` | 字符串 `'30'` | `['queryLimitPeriod' => '30']` | `['queryLimitPeriod' => '30']` |

`ignorePassArgWhenNotSet` 判断的是是否取到参数，不是解析结果是否为 `null`。`NULL_WHILE_EMPTY` 只转换空值，不将非空数字字符串转换为整数。

```php
#[Api(requestParam: [
    new Param(
        name: 'remark',
        from: [ParamFrom::GET],
        type: ParamType::NULL_WHILE_EMPTY,
        ignorePassArgWhenNotSet: true
    ),
])]
public function update(array $params): void
{
    // 未传 remark 或传入 null：$params 中没有 remark。
    // 传入 remark=''：$params 为 ['remark' => null]。
    // 传入 remark='0'：$params 为 ['remark' => '0']。
}
```


- 上传文件同时配置 `from: ParamFrom::FILE` 和 `type: ParamType::FILE`，避免默认字符串转换影响文件对象。
- 未从声明来源取到参数时，不做类型转换，`value` 默认值原样保留；只有实际取到的值才按 `type` 转换。文档默认值列展示声明时的原值，保留 `0`、`false`、空字符串，`null` 显示为 `-`。
- `Required` 检查是否设置参数，`NotEmpty` 检查值是否为空，两者含义不同。Header 参数还应根据需要使用 `NotEmpty`，缺失请求头不会标记为已设置。
- `RequiredIf`、`RequiredWith`、`RequiredWithout` 支持条件必填；同一参数的 `Optional*`（包括 `IgnoreValidatorWhenEmpty`）与 `Required*`、`NotEmpty` 互斥，同时定义会直接抛出配置异常。
- 想用 `Optional` 保留“未传入且为 null”的语义时，应显式设置 `type: null`，避免默认 STRING 将 `null` 转成空字符串。
- BOOLEAN 使用 PHP 布尔转换；字符串 `"false"` 会被转成 `true`。表单布尔值建议使用 `1`、`0`。
- JSON 参数按字段名从请求体解码结果取值；XML 参数从根节点的直接子节点取值；RAW_POST 返回整个请求体。

内置校验器位于 `src/Validator`，包括必填、长度、数值、日期、邮箱、文件和字段比较等。控制器在公共参数和 action 参数校验阶段均传入已解析的 `allDefineParams`，支持跨字段比较及条件可选规则；action 同名参数优先，ignoreAction 排除的参数不会进入该集合。

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

### 响应参数与完整响应示例

`responseParam` 描述各字段，`responseExamples` 展示完整响应，两者分别配置。例如登录方法：

```php
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Bean\Example\Array2Json;
use EasySwoole\HttpAnnotation\Bean\Example\ResParam;
use EasySwoole\HttpAnnotation\Bean\Example\ResParamType;

#[Api(
    responseParam: [
        new ResParam('code', 200, ResParamType::int, '响应码，200 表示成功'),
        new ResParam('result', null, ResParamType::object, '返回数据'),
        new ResParam('result.access_token', 'token-example', ResParamType::string, '授权 Token'),
        new ResParam('result.user_info', null, ResParamType::object, '用户信息'),
        new ResParam('result.user_info.id', 19, ResParamType::int, '用户 ID'),
        new ResParam('msg', 'Success', ResParamType::string, '响应消息'),
    ],
    responseExamples: [
        new Array2Json([
            'code' => 200,
            'result' => [
                'access_token' => 'token-example',
                'user_info' => ['id' => 19],
            ],
            'msg' => 'Success',
        ]),
        new Array2Json([
            'code' => 401,
            'result' => null,
            'msg' => '账号或密码错误',
        ], isSuccessResponse: false),
    ]
)]
public function login(): void
{
    // 实际登录逻辑与响应由业务实现。
}
```

`ResParam` 依次接收 `name`、`exampleValue`、`paramType`、`description`，类型支持 `ResParamType::object`、`string`、`int`、`bool`、`float`；说明支持字符串或 `Text`。嵌套字段使用点分名称，按声明顺序逐行展示，不会据此自动构造响应 JSON。

生成的文档在“请求参数”后显示四列响应参数表，再展示请求示例、成功响应示例及失败响应示例。响应参数为空时显示“暂无示例”。这些定义仅用于文档展示，不会校验实际响应，也不会自动设置 HTTP 状态码；示例中的 `code` 是响应体字段。

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
- `responseParam` 使用 `ResParam` 定义响应字段，文档在请求参数后展示“响应参数”表（参数名、参数值、参数类型、描述说明）。嵌套字段可用 `result.user.id` 这样的点分名称逐行定义，按声明顺序展示。
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
