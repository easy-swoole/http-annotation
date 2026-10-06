<?php

namespace EasySwoole\HttpAnnotation\Bean\Example;

interface ExampleInterface
{
    function toString():string;

    function isSuccessResponse(): bool;
}