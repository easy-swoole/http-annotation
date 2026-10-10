<?php

namespace EasySwoole\HttpAnnotation\Validator\MsgMap;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateMsgMapInterface;

class ChineseMap implements ValidateMsgMapInterface
{
    const MAP = [
        'AllDigital' => '{#validateParam}必须全部为数字字符',
        'Alpha' => '{#validateParam}必须全部为英文字母',
        'AlphaDash' => '{#validateParam}只能包含英文字母、连字符和下划线',
        'AlphaNum' => '{#validateParam}只能包含英文字母和数字',
        'Between' => '{#validateParam}必须在 {#min} 到 {#max} 之间',
        'BetweenLen' => '{#validateParam}长度必须在 {#minLen} 到 {#maxLen} 之间',
        'BetweenMbLen' => '{#validateParam}字符长度必须在 {#minLen} 到 {#maxLen} 之间',
        'BigThanColumn' => '{#validateParam}必须大于参数 {#paramName} 的值',
        'Date' => '{#validateParam}日期必须为 {#date}',
        'DateAfter' => '{#validateParam}日期必须晚于 {#date}',
        'DateAfterColumn' => '{#validateParam}日期必须晚于参数 {#compare} 的日期',
        'DateBefore' => '{#validateParam}日期必须早于 {#date}',
        'DateBeforeColumn' => '{#validateParam}日期必须早于参数 {#compare} 的日期',
        'DateFormat' => '{#validateParam}日期格式必须为 {#format}',
        'Decimal' => '{#validateParam}必须为合法的小数',
        'Different' => '{#validateParam}必须不等于 {#compare}',
        'DifferentWithColumn' => '{#validateParam}必须与参数 {#compare} 的值不同',
        'Equal' => '{#validateParam}必须等于 {#compare}',
        'EqualWithColumn' => '{#validateParam}必须与参数 {#compare} 的值相同',
        'Func' => '{#validateParam}未通过自定义函数校验',
        'IgnoreValidatorWhenEmpty' => '{#validateParam}为空时忽略校验',
        'InArray' => '{#validateParam}必须在 {#array} 中',
        'Integer' => '{#validateParam}必须为整数',
        'IsBool' => '{#validateParam}必须为布尔值或 0、1',
        'IsDomain' => '{#validateParam}必须为合法的域名',
        'IsEmail' => '{#validateParam}必须为合法的邮箱地址',
        'IsFile' => '{#validateParam}必须为上传文件',
        'IsFloat' => '{#validateParam}必须为合法的浮点数',
        'IsIp' => '{#validateParam}必须为合法的 IP 地址',
        'IsNumeric' => '{#validateParam}必须为数字或数字字符串',
        'IsPhoneNumber' => '{#validateParam}必须为合法的手机号码',
        'IsUrl' => '{#validateParam}必须为合法的 URL',
        'Length' => '{#validateParam}长度必须为 {#length}',
        'Max' => '{#validateParam}不能大于 {#max}',
        'MaxLength' => '{#validateParam}长度不能超过 {#maxLen}',
        'MaxMbLength' => '{#validateParam}字符长度不能超过 {#maxLen}',
        'MbLength' => '{#validateParam}字符长度必须为 {#length}',
        'Min' => '{#validateParam}不能小于 {#min}',
        'MinLength' => '{#validateParam}长度不能小于 {#minLen}',
        'MinMbLength' => '{#validateParam}字符长度不能小于 {#minLen}',
        'Money' => '{#validateParam}必须为合法的金额',
        'NotEmpty' => '{#validateParam}不能为空',
        'NotInArray' => '{#validateParam}不能在 {#array} 中',
        'Optional' => '{#validateParam}为可选参数',
        'OptionalIfParamMiss' => '{#validateParam}在参数 {#paramName} 未设置时为可选参数',
        'OptionalIfParamSet' => '{#validateParam}在参数 {#paramName} 已设置时为可选参数',
        'OptionalIfParamValInArray' => '{#validateParam}在参数 {#paramName} 的值属于 {#inVal} 时为可选参数',
        'OptionalIfParamValNoInArray' => '{#validateParam}在参数 {#paramName} 的值不属于 {#inVal} 时为可选参数',
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
