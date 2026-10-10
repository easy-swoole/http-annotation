<?php

namespace EasySwoole\HttpAnnotation\Validator\MsgMap;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateMsgMapInterface;

class DefaultMap implements ValidateMsgMapInterface
{
    const MAP = [
        'AllDigital' => '{#validateParam} must be all digital',
        'Alpha' => '{#validateParam} must be all alpha',
        'AlphaDash' => '{#validateParam} must be all AlphaDash',
        'AlphaNum' => '{#validateParam} must be all AlphaNum',
        'Between' => '{#validateParam} must between {#min} to {#max}',
        'BetweenLen' => '{#validateParam} length must between {#minLen} to {#maxLen}',
        'BetweenMbLen' => '{#validateParam} length must between {#minLen} to {#maxLen}',
        'BigThanColumn' => '{#validateParam} value must big than {#paramName} value',
        'Date' => '{#validateParam} must be date {#date}',
        'DateAfter' => '{#validateParam} must be date after {#date}',
        'DateAfterColumn' => '{#validateParam} must be date after {#compare} column',
        'DateBefore' => '{#validateParam} must be date before {#date}',
        'DateBeforeColumn' => '{#validateParam} must be date before {#compare} column',
        'DateFormat' => '{#validateParam} must be date format {#format}',
        'Decimal' => '{#validateParam} must be decimal',
        'Different' => '{#validateParam} must different with {#compare}',
        'DifferentWithColumn' => '{#validateParam} must different with {#compare} column',
        'Equal' => '{#validateParam} must equal with {#compare}',
        'EqualWithColumn' => '{#validateParam} must equal with {#compare} column',
        'Func' => '{#validateParam} validate fail in custom function',
        'IgnoreValidatorWhenEmpty' => '{#validateParam} is ignore validator when value empty',
        'InArray' => '{#validateParam} must in array of {#array}',
        'Integer' => '{#validateParam} must be integer',
        'IsBool' => '{#validateParam} must be bool',
        'IsDomain' => '{#validateParam} must be a valid domain name format',
        'IsEmail' => '{#validateParam} must be a email address',
        'IsFile' => '{#validateParam} must be an file',
        'IsFloat' => '{#validateParam} must be float',
        'IsIp' => '{#validateParam} must be a ip format',
        'IsNumeric' => '{#validateParam} must be numeric',
        'IsPhoneNumber' => '{#validateParam} must be phone number',
        'IsUrl' => '{#validateParam} must be a url',
        'Length' => '{#validateParam} length must be {#length}',
        'Max' => '{#validateParam} max value is {#max}',
        'MaxLength' => '{#validateParam} max length is {#maxLen}',
        'MaxMbLength' => '{#validateParam} max mb Length is {#maxLen}',
        'MbLength' => '{#validateParam} mb length must be {#length}',
        'Min' => '{#validateParam} min value is {#min}',
        'MinLength' => '{#validateParam} min length is {#minLen}',
        'MinMbLength' => '{#validateParam} min mb length is {#minLen}',
        'Money' => '{#validateParam} must be legal amount',
        'NotEmpty' => '{#validateParam} is no empty',
        'NotInArray' => '{#validateParam} must not in array of {#array}',
        'Optional' => '{#validateParam} is optional',
        'OptionalIfParamMiss' => '{#validateParam} is optional when param {#paramName} miss',
        'OptionalIfParamSet' => '{#validateParam} is optional when param {#paramName} set',
        'OptionalIfParamValInArray' => '{#validateParam} is optional when param {#paramName} value is in  {#inVal}',
        'OptionalIfParamValNoInArray' => '{#validateParam} is optional when param {#paramName} value is not in  {#inVal}',
        'Regex' => '{#validateParam} must meet specified rule: {#rule}',
        'Required' => '{#validateParam} is required',
        'SmallThanColumn' => '{#validateParam} value must small than {#paramName} value',
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