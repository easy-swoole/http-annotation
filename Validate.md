# 验证器使用指南

[返回 README](README.md)

所有内置规则位于 `EasySwoole\HttpAnnotation\Validator` 命名空间。本文按当前源码说明规则行为；每个规则都可在 `Param::validate` 数组中使用。

## 基本用法

```php
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Required;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;
use EasySwoole\HttpAnnotation\Validator\MaxLength;

#[Api(requestParam: [
    new Param(name: 'account', from: [ParamFrom::GET], validate: [
        new Required(errorMsg: '必须传入账号'),
        new NotEmpty(errorMsg: '账号不能为空'),
        new MaxLength(maxLen: 20, errorMsg: '{#validateParam} 长度不能超过 {#maxLen}'),
    ]),
])]
public function login(array $data): void {}
```

类型转换先于规则执行。`Param` 默认 STRING，布尔值、数组和浮点值可能在校验前被转换；验证原始输入时使用 `type: null`。不要用 INT/BOOLEAN 转换代替合法性校验，尤其 BOOLEAN 会把字符串 `"false"` 转成 true。

规则依次执行，首次失败抛出 `ParamValidateFail`，消息包含类和方法，可通过 `getFailRule()`、`getParamName()` 获取失败规则与参数。规则通过静态方法 `ValidatorClass::ruleName()` 获取名称并保存，同一参数重复定义同名规则时后者覆盖前者。自定义规则必须实现 `public static function ruleName(): string`，可直接通过类名调用，不需要创建实例。

所有规则的最后一个构造参数均为 `?string $errorMsg = null`，以下列出完整签名和示例。错误模板支持 `{#validateParam}` 以及规则参数占位符，例如 `{#min}`、`{#maxLen}`。占位符取决于规则属性名；DateFormat 使用 `{#format}`。未设置自定义消息时，基类统一通过 `MsgMap\DefaultMap` 获取模板；未知规则使用默认兜底消息。各验证器构造函数只接收自定义消息，不再内置默认文案。Decimal、Money 默认消息包含精度及模式说明；IsFile 包含大小和扩展名限制；严格比较规则包含 strict 配置。Func 默认消息不追加回调名称，需要时可传入自定义模板。

## 倍数校验

### MultipleOf

```php
MultipleOf(int|float $multiple, string|null $errorMsg = null)
new MultipleOf(5);
new MultipleOf(0.25, '金额必须为 0.25 的整数倍');
```

要求数值或数值字符串为 `multiple` 的整数倍，支持负数、小数和科学计数法字符串。0 是任何合法基数的整数倍；基数必须为有限非零数，否则构造时抛出异常。非数值、布尔值、数组、null、INF、NAN 均不通过。

整数输入和整数基数使用精确取模；小数计算使用浮点数，并容许有限舍入误差（商的误差不超过 1e-9），因此 `0.3` 可通过 `MultipleOf(0.1)`。小数及超出 PHP 整数范围的数值受浮点精度限制，不适用于要求任意精度的计算。使用 `type: null` 保留原值，避免 INT 类型转换截断小数。

## 去重校验

这两个规则只检查重复项，不修改或去重输入。

### DistinctInString

```php
DistinctInString(string $separator = ',', string|null $errorMsg = null)
new DistinctInString();
new DistinctInString('|', 'ID 不能重复');
```

只接受字符串，按完整分隔符拆分并精确比较，区分大小写，不去除空白、不忽略空项。`1,1` 失败，`1,01` 和 `1, 1` 通过；`1,,` 因为空项重复而失败。空字符串作为单个空项通过，如需禁止空值请搭配 NotEmpty。分隔符不能为空，支持多字符分隔符。

### DistinctInArray

```php
DistinctInArray(bool $strict = false, string|null $errorMsg = null)
new DistinctInArray();
new DistinctInArray(true, 'ID 不能重复');
```

只接受数组，比较数组值而不是键，不递归检查内部元素。默认宽松比较，`[1, '1']` 失败；`strict: true` 时按值和类型比较，该输入通过。空数组通过，重复的 null 或空字符串失败。

JSON 或 POST 数组参数必须配置 `type: null`，保留数组原值：

```php
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\DistinctInArray;
use EasySwoole\HttpAnnotation\Validator\DistinctInString;

new Param('ids', from: ParamFrom::JSON, type: null, validate: [new DistinctInArray()]);
new Param('ids', from: ParamFrom::POST, type: null, validate: [new DistinctInArray()]);
new Param('ids', from: ParamFrom::GET, validate: [new DistinctInString(',')]);
```

## 必填与可选

### Required

```php
Required(string|null $errorMsg = null)
new Required();
```

必须已设置参数；不检查内容是否为空。

### NotEmpty

```php
NotEmpty(string|null $errorMsg = null)
new NotEmpty();
```

拒绝 PHP empty 值，但允许整数 0 和字符串 0；false、空数组、空字符串及浮点 0.0 不通过。

### Optional

```php
Optional(string|null $errorMsg = null)
new Optional();
```

未设置且转换后的值严格等于 null 时，跳过该参数的所有规则。建议 type: null。

### IgnoreValidatorWhenEmpty

```php
IgnoreValidatorWhenEmpty(string|null $errorMsg = null)
new IgnoreValidatorWhenEmpty();
```

未设置或 PHP empty(value) 为真时跳过所有规则；0、字符串 0、false 也会跳过。

### OptionalIfParamMiss

```php
OptionalIfParamMiss(string $paramName,string|null $errorMsg = null)
new OptionalIfParamMiss(paramName: "account");
```

当前参数未设置且目标参数未设置时跳过；目标没有定义也视为未设置。

### OptionalIfParamSet

```php
OptionalIfParamSet(string $paramName,string|null $errorMsg = null)
new OptionalIfParamSet(paramName: "account");
```

当前参数未设置且目标参数已设置时跳过。

### OptionalIfParamValInArray

```php
OptionalIfParamValInArray(string $paramName,array $inVal,string|null $errorMsg = null)
new OptionalIfParamValInArray(paramName: "mode", inVal: ["guest"]);
```

当前参数未设置，目标已设置且值在 inVal 中时跳过；使用宽松比较。

### OptionalIfParamValNoInArray

```php
OptionalIfParamValNoInArray(string $paramName,array $inVal,string|null $errorMsg = null)
new OptionalIfParamValNoInArray(paramName: "mode", inVal: ["member"]);
```

当前参数未设置且目标已设置、值不在 inVal 中时跳过；目标不存在或未设置也跳过。

## 数字与金额

### Integer

```php
Integer(string|null $errorMsg = null)
new Integer();
```

使用 FILTER_VALIDATE_INT 校验整数及合法整数字符串。

### IsFloat

```php
IsFloat(string|null $errorMsg = null)
new IsFloat();
```

使用 FILTER_VALIDATE_FLOAT 校验浮点表示，整数表示也可能通过。

### IsNumeric

```php
IsNumeric(string|null $errorMsg = null)
new IsNumeric();
```

使用 is_numeric 校验数字和数字字符串。

### IsBool

```php
IsBool(string|null $errorMsg = null)
new IsBool();
```

只允许 true、false、1、0、字符串 1 和 0；字符串 true/false 不通过。

### AllDigital

```php
AllDigital(string|null $errorMsg = null)
new AllDigital();
```

转换为字符串后必须全是数字字符，允许前导零，不允许正负号或小数点。

### Min

```php
Min(int|float $min,string|null $errorMsg = null)
new Min(min: 1);
```

要求 is_numeric，数值大于等于 min。

### Max

```php
Max(int|float $max,string|null $errorMsg = null)
new Max(max: 100);
```

要求 is_numeric，数值小于等于 max。

### Between

```php
Between(float|int $min,float|int $max,string|null $errorMsg = null)
new Between(min: 1, max: 100);
```

数值或字符串与 min/max 比较，两端包含；当前实现没有独立的 is_numeric 限制，建议组合 IsNumeric。

### Decimal

```php
Decimal(int|null $accuracy = null,string|null $errorMsg = null)
new Decimal(accuracy: 2);
```

accuracy=null 要求原值为 float；accuracy=0 要求 float 且能通过整数校验；正数精度允许 1 到 accuracy 位小数。

### Money

```php
Money(int|null $precision = null, string|null $errorMsg = null)
new Money(precision: 2);
```

precision=null 或 0 只允许整数文本；正数精度要求小数点后 1 到 precision 位。允许负数，不允许正号或多位整数前导零。

## 字符串、长度与格式

### Alpha

```php
Alpha(string|null $errorMsg = null)
new Alpha();
```

仅英文字母，不能为空。

### AlphaNum

```php
AlphaNum(string|null $errorMsg = null)
new AlphaNum();
```

仅英文字母和数字，不能为空。

### AlphaDash

```php
AlphaDash(string|null $errorMsg = null)
new AlphaDash();
```

仅英文字母、连字符和下划线；当前实现不允许数字。

### Regex

```php
Regex(string $rule,string|null $errorMsg = null)
new Regex(rule: "/^[A-Z]{2}[0-9]{4}$/");
```

对数字或字符串执行 preg_match，rule 必须包含正则分隔符。

### IsEmail

```php
IsEmail(string|null $errorMsg = null)
new IsEmail();
```

要求字符串并使用 FILTER_VALIDATE_EMAIL。

### IsUrl

```php
IsUrl(array|null $allowProtocols = null, string|null $errorMsg = null)
new IsUrl();
new IsUrl(['http', 'https']);
new IsUrl(['https'], '仅允许 HTTPS 地址');
```

要求字符串并使用 FILTER_VALIDATE_URL。`allowProtocols` 为允许的协议名称数组，不包含 `://`，匹配时忽略大小写。默认 `null` 不额外限制协议；空数组 `[]` 禁止所有协议。非法协议配置会抛出异常。

### IsIp

```php
IsIp(string $mode = 'ANY', string|null $errorMsg = null)
new IsIp();
new IsIp('IPV4');
new IsIp('IPV6', '请输入 IPv6 地址');
```

使用 FILTER_VALIDATE_IP。`mode` 支持 `ANY`（默认，允许 IPv4/IPv6）、`IPV4`、`IPV6`，忽略大小写；后两种分别使用 FILTER_FLAG_IPV4、FILTER_FLAG_IPV6。非法模式会抛出异常。

### IsDomain

```php
IsDomain(string|null $errorMsg = null)
new IsDomain();
```

校验含点号的域名，禁止空白并检查各段与顶级域，不接收完整 URL；不做 DNS 查询。

### IsPhoneNumber

```php
IsPhoneNumber(string|null $errorMsg = null)
new IsPhoneNumber();
```

匹配中国大陆手机号：1 开头，第二位 3–9，总计 11 位。

### Length

```php
Length(int $length,string|null $errorMsg = null)
new Length(length: 10);
```

字符串或数字的字节长度等于 length；数组比较元素数量。

### MinLength

```php
MinLength(int $minLen,string|null $errorMsg = null)
new MinLength(minLen: 10);
```

字符串或数字的字节长度大于等于 minLen；数组比较元素数量。

### MaxLength

```php
MaxLength(int $maxLen,string|null $errorMsg = null)
new MaxLength(maxLen: 10);
```

字符串或数字的字节长度小于等于 maxLen；数组比较元素数量。

### MbLength

```php
MbLength(int $length,string|null $errorMsg = null)
new MbLength(length: 10);
```

字符串或数字的字符长度等于 length；数组比较元素数量。

### MinMbLength

```php
MinMbLength(int $minLen,string|null $errorMsg = null)
new MinMbLength(minLen: 10);
```

字符串或数字的字符长度大于等于 minLen；数组比较元素数量。

### MaxMbLength

```php
MaxMbLength(int $maxLen,string|null $errorMsg = null)
new MaxMbLength(maxLen: 10);
```

字符串或数字的字符长度小于等于 maxLen；数组比较元素数量。

### BetweenLen

```php
BetweenLen(int $minLen,int $maxLen,string|null $errorMsg = null)
new BetweenLen(minLen: 2, maxLen: 20);
```

字符串或数字长度介于 minLen 和 maxLen，含两端；不接受数组。使用 strlen 字节长度。

### BetweenMbLen

```php
BetweenMbLen(int $minLen,int $maxLen,string|null $errorMsg = null)
new BetweenMbLen(minLen: 2, maxLen: 20);
```

字符串或数字长度介于 minLen 和 maxLen，含两端；不接受数组。使用 mb_strlen 字符长度。

## 值与字段比较

### Equal

```php
Equal(string|int|null|float $compare,bool $strict = false,string|null $errorMsg = null)
new Equal(compare: "active", strict: true);
```

与 compare 相等；strict=false 使用 ==，true 使用 ===。

### Different

```php
Different(string|float|int $compare,bool $strict = false,string|null $errorMsg = null)
new Different(compare: "blocked", strict: true);
```

与 compare 不相等；strict=false 使用 !=，true 使用 !==。

### InArray

```php
InArray(array $array,bool $strict = false,string|null $errorMsg = null)
new InArray(array: ["draft", "published"], strict: true);
```

值在 array 中；strict 控制 in_array 的严格模式。

### NotInArray

```php
NotInArray(array $array,bool $strict = false,string|null $errorMsg = null)
new NotInArray(array: ["admin"], strict: true);
```

值不在 array 中；strict 控制比较模式。

### EqualWithColumn

```php
EqualWithColumn(string $compare,bool $strict = false,string|null $errorMsg = null)
new EqualWithColumn(compare: "password", strict: true);
```

与 compare 指定的参数值相等，strict 控制严格比较。

### DifferentWithColumn

```php
DifferentWithColumn(string $compare,bool $strict = false,string|null $errorMsg = null)
new DifferentWithColumn(compare: "oldPassword", strict: true);
```

与 compare 指定的参数值不同，strict 控制严格比较。

### BigThanColumn

```php
BigThanColumn(string $paramName,string|null $errorMsg = null)
new BigThanColumn(paramName: "min");
```

当前值必须严格大于 paramName 指定参数，使用 PHP > 比较。

### SmallThanColumn

```php
SmallThanColumn(string $paramName,string|null $errorMsg = null)
new SmallThanColumn(paramName: "max");
```

当前值必须严格小于 paramName 指定参数，使用 PHP < 比较。

## 日期与时间戳

### Date

```php
Date(string $date,string|null $errorMsg = null)
new Date(date: "2026-01-01");
```

要求可被 strtotime 解析的字符串；与 date 比较 Y-m-d 日期，忽略时间部分。

### DateFormat

```php
DateFormat(string $dateFormat,string|null $errorMsg = null)
new DateFormat(dateFormat: "Y-m-d");
```

使用 DateTime::createFromFormat；同时检查解析错误和警告，拒绝溢出日期，例如 2026-02-31。

### DateAfter

```php
DateAfter(string $date,string|null $errorMsg = null)
new DateAfter(date: "2026-01-01");
```

当前值必须为日期字符串，解析后严格晚于 date；date 可为日期字符串或 10 位数字时间戳。

### DateBefore

```php
DateBefore(string $date,string|null $errorMsg = null)
new DateBefore(date: "2027-01-01");
```

当前日期字符串必须严格早于 date，不包含相等。

### DateAfterColumn

```php
DateAfterColumn(string $compare, string|null $errorMsg = null)
new DateAfterColumn(compare: "startTime");
```

当前日期字符串严格晚于 compare 指定字段；目标支持日期文本或 10 位时间戳。

### DateBeforeColumn

```php
DateBeforeColumn(string $compare, string|null $errorMsg = null)
new DateBeforeColumn(compare: "endTime");
```

当前日期字符串严格早于 compare 指定字段。

### Timestamp

```php
Timestamp(string|null $errorMsg = null)
new Timestamp();
```

要求数字，使用 date 与 strtotime 往返转换并比较整数时间戳；应提交秒级整数时间戳，受默认时区影响。

### TimestampAfter

```php
TimestampAfter(string $compare, string|null $errorMsg = null)
new TimestampAfter(compare: "2026-01-01");
```

当前数字时间戳严格大于 compare；compare 可为数字字符串或 strtotime 可解析的日期。

### TimestampBefore

```php
TimestampBefore(string $compare, string|null $errorMsg = null)
new TimestampBefore(compare: "2027-01-01");
```

当前数字时间戳严格小于 compare，不包含相等。

## 文件与自定义规则

### IsFile

```php
IsFile(int|null $maxSize = null,array|null $allowExt = null,string|null $errorMsg = null)
new IsFile(maxSize: 2097152, allowExt: ["jpg", "png"]);
```

要求 UploadedFileInterface；maxSize 单位字节，null/0 不限；allowExt 比较客户端文件名扩展名，不校验 MIME 或内容，应传小写扩展名。

### Func

```php
Func(ValidateFuncInterface|callable $func,string|null $errorMsg = null)
new Func(func: [TicketValidator::class, "check"]);
```

接受 callable 或 ValidateFuncInterface，接收 ValidateRequest，返回真表示通过。

## 可选参数的组合

```php
new Param(name: 'age', from: [ParamFrom::GET], type: null, validate: [
    new \EasySwoole\HttpAnnotation\Validator\Optional(),
    new \EasySwoole\HttpAnnotation\Validator\Integer(),
    new \EasySwoole\HttpAnnotation\Validator\Min(min: 18),
]);
```

未传且值为 null 时跳过全部校验；传空字符串仍执行 Integer 并失败。Optional 与 Required 同时出现时，满足 Optional 跳过条件也会跳过 Required，不应用这种组合表达必填。

`IgnoreValidatorWhenEmpty` 的空值规则与 `NotEmpty` 不同：字符串 `'0'` 也会触发跳过。条件 Optional 仅在当前参数未设置时生效；多个条件标记同时配置时，按实现顺序处理：Miss、Set、ValInArray、ValNoInArray，而不是逻辑合取。默认 STRING 会把未传入的 null 转为 `''`，使 Optional 的严格 null 条件失效。

`ignorePassArgWhenNotSet` 控制 action 数组中是否传递字段，不跳过校验；`NULL_WHILE_EMPTY` 转换值也不改变 hasSet。详见 [README 参数说明](README.md#未传参数与空值转换)。

## 跨字段规则与独立校验

字段比较和条件 Optional 需要 `ValidateRequest::allDefineParams`。目标应是已定义、已解析的 Param。比较规则在目标不存在时抛出 Annotation，条件 Optional 按各规则的缺失分支处理。

AnnotationController 在 onRequest 参数校验和 action 参数校验阶段均填充 `allDefineParams = $finalAllParams`。该集合已完成参数解析、action 同名覆盖以及 ignoreAction 过滤，可直接使用跨字段规则。独立调用验证器时仍需显式传入上下文，例如：

```php
use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\EqualWithColumn;

$request = new Request();
$request->withQueryParams(['password' => 'secret', 'confirmation' => 'secret']);
$password = new Param(name: 'password', type: null);
$confirmation = new Param(name: 'confirmation', type: null, validate: [
    new EqualWithColumn(compare: 'password', strict: true),
]);
$password->parsedValue($request);
$confirmation->parsedValue($request);
Utility::validateParam(new ValidateRequest(
    validateParam: $confirmation,
    request: $request,
    allDefineParams: ['password' => $password, 'confirmation' => $confirmation],
    callClass: 'Example',
    callMethod: 'register',
));
```

严格比较基于转换后的类型。若两边都使用默认 STRING，原始整数 0 与字符串 '0' 都转成字符串，无法用 strict 区分；需要保留原值或指定适当类型。

## 文件校验组合

```php
new Param(name: 'avatar', from: [ParamFrom::FILE], type: \EasySwoole\HttpAnnotation\Enum\ParamType::FILE, validate: [
    new \EasySwoole\HttpAnnotation\Validator\Required(),
    new \EasySwoole\HttpAnnotation\Validator\IsFile(maxSize: 2 * 1024 * 1024, allowExt: ['jpg', 'png']),
]);
```

FILE 来源只能单独使用。长度规则用于数组时需要 `type: null`；BetweenLen/BetweenMbLen 只处理字符串或数字，不能用来限制数组数量。多字节长度取决于当前 mbstring 内部编码，日期规则使用服务器默认时区。

## 自定义验证函数

PHP Attribute 的参数需要常量表达式，使用 `[类名::class, '方法名']` 或实现 ValidateFuncInterface 的对象，不要在 Attribute 中传闭包。普通 PHP 代码创建 Func 时可以使用闭包。

```php
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class TicketValidator
{
    public static function check(ValidateRequest $context): bool
    {
        $value = $context->validateParam->parsedValue();
        return is_string($value) && str_starts_with($value, 'ticket-');
    }
}

new Param(name: 'ticket', validate: [
    new \EasySwoole\HttpAnnotation\Validator\Func(
        func: [TicketValidator::class, 'check'],
        errorMsg: '无效的 ticket'
    ),
]);
```

也可以实现 `ValidateFuncInterface::execute(ValidateRequest $validateRequest)` 与 `functionName(): string`，再传 `new Func(func: new YourValidator())`。
