<?php

namespace EasySwoole\HttpAnnotation\Validator\MsgMap;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateMsgMapInterface;

class DefaultMap implements ValidateMsgMapInterface
{
    const MAP = [
        'RequiredIf' => '{#validateParam} must be set and non-empty when parameter {#paramName} is set and equals {#value} (strict={#strict}; 1: compare value and type; empty: loose comparison)',
        'RequiredWith' => '{#validateParam} must be set and non-empty when any parameter in {#paramNames} is set and non-empty',
        'RequiredWithout' => '{#validateParam} must be set and non-empty when any parameter in {#paramNames} is unset or empty',
        'MultipleOf' => '{#validateParam} must be an integer multiple of {#multiple}',
        'DistinctInString' => '{#validateParam} must contain distinct items separated by {#separator} (exact string comparison)',
        'DistinctInArray' => '{#validateParam} must be an array of distinct values (strict={#strict}; 1: compare value and type; empty: loose comparison)',
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
        'Decimal' => '{#validateParam} must satisfy decimal accuracy {#accuracy} (null: float input; 0: integer-valued float; positive: 1 to accuracy decimal places)',
        'Different' => '{#validateParam} must be different from {#compare} (strict={#strict}; 1: compare value and type; empty: loose comparison)',
        'DifferentWithColumn' => '{#validateParam} must be different from parameter {#compare} (strict={#strict}; 1: compare value and type; empty: loose comparison)',
        'Equal' => '{#validateParam} must be equal to {#compare} (strict={#strict}; 1: compare value and type; empty: loose comparison)',
        'EqualWithColumn' => '{#validateParam} must be equal to parameter {#compare} (strict={#strict}; 1: compare value and type; empty: loose comparison)',
        'Func' => '{#validateParam} failed custom function validation',
        'IgnoreValidatorWhenEmpty' => '{#validateParam} skips validation when unset or PHP empty(value) is true, including 0 and "0"',
        'InArray' => '{#validateParam} must be in {#array} (strict={#strict}; 1: compare value and type; empty: loose comparison)',
        'Integer' => '{#validateParam} must be integer',
        'IsBool' => '{#validateParam} must be true, false, 0, 1, "0" or "1"',
        'IsDomain' => '{#validateParam} must be a syntactically valid domain name',
        'IsEmail' => '{#validateParam} must be a valid email address',
        'IsFile' => '{#validateParam} must be an uploaded file satisfying maxSize {#maxSize} bytes and allowExt {#allowExt} (null or 0 size / empty extensions: unrestricted)',
        'IsFloat' => '{#validateParam} must be a value parseable as a floating-point number',
        'IsIp' => '{#validateParam} must be a valid IP address in mode {#mode}',
        'IsNumeric' => '{#validateParam} must be numeric',
        'IsPhoneNumber' => '{#validateParam} must be an 11-digit mainland China mobile number starting with 1 followed by 3–9',
        'IsUrl' => '{#validateParam} must be a valid URL with allowed protocols {#allowProtocols} (empty value: unrestricted; []: none)',
        'Length' => '{#validateParam} must have byte length (or array element count) equal to {#length}',
        'Max' => '{#validateParam} max value is {#max}',
        'MaxLength' => '{#validateParam} must have byte length (or array element count) at most {#maxLen}',
        'MaxMbLength' => '{#validateParam} must have character length (or array element count) at most {#maxLen}',
        'MbLength' => '{#validateParam} must have character length (or array element count) equal to {#length}',
        'Min' => '{#validateParam} min value is {#min}',
        'MinLength' => '{#validateParam} must have byte length (or array element count) at least {#minLen}',
        'MinMbLength' => '{#validateParam} must have character length (or array element count) at least {#minLen}',
        'Money' => '{#validateParam} must satisfy monetary precision {#precision} (null or 0: integer amount; positive: 1 to precision decimal places)',
        'NotEmpty' => '{#validateParam} must not be empty (integer 0 and string "0" are allowed)',
        'NotInArray' => '{#validateParam} must not be in {#array} (strict={#strict}; 1: compare value and type; empty: loose comparison)',
        'Optional' => '{#validateParam} skips validation only when unset and the parsed value is null',
        'OptionalIfParamMiss' => '{#validateParam} skips validation when unset and parameter {#paramName} is unset or undefined',
        'OptionalIfParamSet' => '{#validateParam} skips validation when unset and parameter {#paramName} is set',
        'OptionalIfParamValInArray' => '{#validateParam} skips validation when unset and parameter {#paramName} is set and loosely matches {#inVal}',
        'OptionalIfParamValNoInArray' => '{#validateParam} skips validation when unset and parameter {#paramName} is unset, undefined or does not loosely match {#inVal}',
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
