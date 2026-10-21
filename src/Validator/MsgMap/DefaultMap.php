<?php

namespace EasySwoole\HttpAnnotation\Validator\MsgMap;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateMsgMapInterface;

class DefaultMap implements ValidateMsgMapInterface
{
    const MAP = [
        'ObjectShape' => '{#validateParam} must be an object',
        'Each' => '{#validateParam} must be a list',
        'Enum' => '{#validateParam} must be a valid enum value',
        'RequiredIf' => '{#validateParam} must be provided and non-empty when parameter {#paramName} equals {#value}',
        'RequiredWith' => '{#validateParam} must be provided and non-empty when any parameter in {#paramNames} is provided and non-empty',
        'RequiredWithout' => '{#validateParam} must be provided and non-empty when any parameter in {#paramNames} is missing or empty',
        'MultipleOf' => '{#validateParam} must be an integer multiple of {#multiple}',
        'DistinctInString' => '{#validateParam} must contain distinct items separated by {#separator}',
        'DistinctInArray' => '{#validateParam} must be an array of distinct values',
        'AllDigital' => '{#validateParam} must contain only digits',
        'Alpha' => '{#validateParam} must contain only English letters',
        'AlphaDash' => '{#validateParam} must contain only English letters, hyphens and underscores',
        'AlphaNum' => '{#validateParam} must contain only English letters and digits',
        'Between' => '{#validateParam} must be between {#min} and {#max}, inclusive',
        'BetweenLen' => '{#validateParam} byte length must be between {#minLen} and {#maxLen}, inclusive',
        'BetweenMbLen' => '{#validateParam} character length must be between {#minLen} and {#maxLen}, inclusive',
        'BigThanColumn' => '{#validateParam} must be greater than parameter {#paramName}',
        'Date' => '{#validateParam} must have the same calendar date as {#date} (time is ignored)',
        'DateAfter' => '{#validateParam} must be date after {#date}',
        'DateAfterColumn' => '{#validateParam} must be date after {#compare} column',
        'DateBefore' => '{#validateParam} must be date before {#date}',
        'DateBeforeColumn' => '{#validateParam} must be date before {#compare} column',
        'DateFormat' => '{#validateParam} must be a valid date matching format {#format}',
        'Decimal' => '{#validateParam} must satisfy decimal precision {#accuracy}',
        'Different' => '{#validateParam} must be different from {#compare}',
        'DifferentWithColumn' => '{#validateParam} must be different from parameter {#compare}',
        'Equal' => '{#validateParam} must be equal to {#compare}',
        'EqualWithColumn' => '{#validateParam} must be equal to parameter {#compare}',
        'Func' => '{#validateParam} failed validation',
        'IgnoreValidatorWhenEmpty' => '{#validateParam} may be omitted when empty',
        'InArray' => '{#validateParam} must be in {#array}',
        'Integer' => '{#validateParam} must be integer',
        'IsBool' => '{#validateParam} must be true, false, 0 or 1',
        'IsDomain' => '{#validateParam} must be a syntactically valid domain name',
        'IsEmail' => '{#validateParam} must be a valid email address',
        'IsFile' => '{#validateParam} file size or extension does not meet the requirements (maximum size: {#maxSize} bytes; allowed extensions: {#allowExt})',
        'IsFloat' => '{#validateParam} must be a valid number',
        'IsIp' => '{#validateParam} must be a valid IP address in mode {#mode}',
        'IsNumeric' => '{#validateParam} must be numeric',
        'IsPhoneNumber' => '{#validateParam} must be an 11-digit mainland China mobile number starting with 1 followed by 3–9',
        'IsUrl' => '{#validateParam} must be a valid URL with allowed protocols {#allowProtocols}',
        'Length' => '{#validateParam} must have byte length (or array element count) equal to {#length}',
        'Max' => '{#validateParam} max value is {#max}',
        'MaxLength' => '{#validateParam} must have byte length (or array element count) at most {#maxLen}',
        'MaxMbLength' => '{#validateParam} must have character length (or array element count) at most {#maxLen}',
        'MbLength' => '{#validateParam} must have character length (or array element count) equal to {#length}',
        'Min' => '{#validateParam} min value is {#min}',
        'MinLength' => '{#validateParam} must have byte length (or array element count) at least {#minLen}',
        'MinMbLength' => '{#validateParam} must have character length (or array element count) at least {#minLen}',
        'Money' => '{#validateParam} must satisfy monetary precision {#precision}',
        'NotEmpty' => '{#validateParam} must not be empty',
        'NotInArray' => '{#validateParam} must not be in {#array}',
        'Optional' => '{#validateParam} is optional',
        'OptionalIfParamMiss' => '{#validateParam} may be omitted when parameter {#paramName} is missing',
        'OptionalIfParamSet' => '{#validateParam} may be omitted when parameter {#paramName} is provided',
        'OptionalIfParamValInArray' => '{#validateParam} may be omitted when parameter {#paramName} is in {#inVal}',
        'OptionalIfParamValNoInArray' => '{#validateParam} may be omitted when parameter {#paramName} is missing or not in {#inVal}',
        'Regex' => '{#validateParam} must meet specified rule: {#rule}',
        'Required' => '{#validateParam} is required',
        'SmallThanColumn' => '{#validateParam} must be less than parameter {#paramName}',
        'Timestamp' => '{#validateParam} must be timestamp',
        'TimestampAfter' => '{#validateParam} must be timestamp after {#compare}',
        'TimestampBefore' => '{#validateParam} must be timestamp before {#compare}',
    ];

    public static function getMsgTpl(string $validateName): ?string
    {
        if(isset(self::MAP[$validateName])){
            return self::MAP[$validateName];
        }
        return null;
    }

    public static function getDefaultMsgTpl(string $validateName): string
    {
        return "{#validateParam} fail in validate {$validateName} rule";
    }

}
