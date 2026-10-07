<?php

namespace EasySwoole\HttpAnnotation\Bean\Description;

abstract class AbstractDescription
{
    public string|null $relateClass = null;

    public string|null $relateMethod = null;

    abstract function toString():string;
}