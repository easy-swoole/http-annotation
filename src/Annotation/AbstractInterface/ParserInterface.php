<?php


namespace EasySwoole\HttpAnnotation\Annotation\AbstractInterface;



use EasySwoole\Annotation\Annotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;

interface ParserInterface
{
    function __construct(?Annotation $annotation = null);

    function parseObject(\ReflectionClass  $reflectionClass):ObjectAnnotation;
}