<?php

namespace EasySwoole\HttpAnnotation\Validator\MsgMap;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateMsgMapInterface;

class ChineseMap implements ValidateMsgMapInterface
{
    const MAP = [
        'RequiredIf' => '{#validateParam}在参数 {#paramName} 已设置且等于 {#value} 时必须传入且非空（strict={#strict}；1：比较值与类型；空值：宽松比较）',
        'RequiredWith' => '{#validateParam}在 {#paramNames} 中任意参数已设置且非空时必须传入且非空',
        'RequiredWithout' => '{#validateParam}在 {#paramNames} 中任意参数未设置或为空时必须传入且非空',
        'MultipleOf' => '{#validateParam}必须为 {#multiple} 的整数倍',
        'DistinctInString' => '{#validateParam}按分隔符 {#separator} 拆分后不能包含重复项（按字符串精确比较）',
        'DistinctInArray' => '{#validateParam}必须为无重复值的数组（strict={#strict}；1：比较值与类型；空值：宽松比较）',
        'AllDigital' => '{#validateParam}必须全部为数字字符',
        'Alpha' => '{#validateParam}必须全部为英文字母',
        'AlphaDash' => '{#validateParam}只能包含英文字母、连字符和下划线',
        'AlphaNum' => '{#validateParam}只能包含英文字母和数字',
        'Between' => '{#validateParam}必须在 {#min} 到 {#max} 之间（包含两端）',
        'BetweenLen' => '{#validateParam}字节长度必须在 {#minLen} 到 {#maxLen} 之间（包含两端）',
        'BetweenMbLen' => '{#validateParam}字符长度必须在 {#minLen} 到 {#maxLen} 之间（包含两端）',
        'BigThanColumn' => '{#validateParam}必须大于参数 {#paramName} 的值',
        'Date' => '{#validateParam}日期部分必须与 {#date} 相同（忽略时间）',
        'DateAfter' => '{#validateParam}日期必须晚于 {#date}',
        'DateAfterColumn' => '{#validateParam}日期必须晚于参数 {#compare} 的日期',
        'DateBefore' => '{#validateParam}日期必须早于 {#date}',
        'DateBeforeColumn' => '{#validateParam}日期必须早于参数 {#compare} 的日期',
        'DateFormat' => '{#validateParam}必须为符合格式 {#format} 的有效日期',
        'Decimal' => '{#validateParam}必须符合小数精度 {#accuracy}（null：浮点类型；0：整数值的浮点数；正数：1 到指定精度位小数）',
        'Different' => '{#validateParam}必须{#compare} 不同（strict={#strict}；1：值和类型均参与比较；空：宽松比较）',
        'DifferentWithColumn' => '{#validateParam}必须与参数 {#compare} 不同（strict={#strict}；1：值和类型均参与比较；空：宽松比较）',
        'Equal' => '{#validateParam}必须{#compare} 相同（strict={#strict}；1：值和类型均参与比较；空：宽松比较）',
        'EqualWithColumn' => '{#validateParam}必须与参数 {#compare} 相同（strict={#strict}；1：值和类型均参与比较；空：宽松比较）',
        'Func' => '{#validateParam}未通过自定义函数校验',
        'IgnoreValidatorWhenEmpty' => '{#validateParam}在未设置或 PHP empty(value) 为真时跳过校验（包括 0 和字符串 0）',
        'InArray' => '{#validateParam}必须属于 {#array}（strict={#strict}；1：值和类型均参与比较；空：宽松比较）',
        'Integer' => '{#validateParam}必须为整数',
        'IsBool' => '{#validateParam}必须为 true、false、整数 0/1 或字符串 0/1',
        'IsDomain' => '{#validateParam}必须为合法的域名',
        'IsEmail' => '{#validateParam}必须为合法的邮箱地址',
        'IsFile' => '{#validateParam}必须为符合大小上限 {#maxSize} 字节及扩展名列表 {#allowExt} 的上传文件（大小 null/0 或扩展名列表为空时不限制）',
        'IsFloat' => '{#validateParam}必须为可解析为浮点数的数值',
        'IsIp' => '{#validateParam}必须为符合 {#mode} 模式的合法 IP 地址',
        'IsNumeric' => '{#validateParam}必须为数字或数字字符串',
        'IsPhoneNumber' => '{#validateParam}必须为中国大陆 11 位手机号，以 1 开头且第二位为 3–9',
        'IsUrl' => '{#validateParam}必须为合法的 URL，允许协议 {#allowProtocols}（空值：不限制；[]：全部禁止）',
        'Length' => '{#validateParam}字节长度（数组为元素数量）必须等于 {#length}',
        'Max' => '{#validateParam}不能大于 {#max}',
        'MaxLength' => '{#validateParam}字节长度（数组为元素数量）必须小于等于 {#maxLen}',
        'MaxMbLength' => '{#validateParam}字符长度（数组为元素数量）必须小于等于 {#maxLen}',
        'MbLength' => '{#validateParam}字符长度（数组为元素数量）必须等于 {#length}',
        'Min' => '{#validateParam}不能小于 {#min}',
        'MinLength' => '{#validateParam}字节长度（数组为元素数量）必须大于等于 {#minLen}',
        'MinMbLength' => '{#validateParam}字符长度（数组为元素数量）必须大于等于 {#minLen}',
        'Money' => '{#validateParam}必须符合金额精度 {#precision}（null 或 0：整数金额；正数：1 到指定精度位小数）',
        'NotEmpty' => '{#validateParam}不能为空（允许整数 0 和字符串 0）',
        'NotInArray' => '{#validateParam}不能属于 {#array}（strict={#strict}；1：值和类型均参与比较；空：宽松比较）',
        'Optional' => '{#validateParam}仅在未设置且解析值为 null 时跳过校验',
        'OptionalIfParamMiss' => '{#validateParam}在当前参数未设置且参数 {#paramName} 未设置或未定义时跳过校验',
        'OptionalIfParamSet' => '{#validateParam}在当前参数未设置且参数 {#paramName} 已设置时跳过校验',
        'OptionalIfParamValInArray' => '{#validateParam}在当前参数未设置且参数 {#paramName} 已设置、值宽松匹配 {#inVal} 时跳过校验',
        'OptionalIfParamValNoInArray' => '{#validateParam}在当前参数未设置且参数 {#paramName} 未设置、未定义或值不宽松匹配 {#inVal} 时跳过校验',
        'Regex' => '{#validateParam}必须匹配正则表达式 {#rule}',
        'Required' => '{#validateParam}必须传入',
        'SmallThanColumn' => '{#validateParam}必须小于参数 {#paramName} 的值',
        'Timestamp' => '{#validateParam}必须为合法的时间戳',
        'TimestampAfter' => '{#validateParam}时间戳必须晚于 {#compare}',
        'TimestampBefore' => '{#validateParam}时间戳必须早于 {#compare}',
    ];

    public static function getMsgTpl(string $validateName): ?string
    {
        return self::MAP[$validateName] ?? null;
    }

    public static function getDefaultMsgTpl(string $validateName): string
    {
        return "{#validateParam}未通过 {$validateName} 规则校验";
    }
}
