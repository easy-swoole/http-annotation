<?php

namespace EasySwoole\HttpAnnotation\Validator\AbstractInterface;

interface ValidateMsgMapInterface
{
    static function getMsgTpl(string $validateName):?string;

    static function getDefaultMsgTpl(string $validateName):string;
}