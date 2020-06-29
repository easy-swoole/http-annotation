<?php


namespace EasySwoole\HttpAnnotation\Annotation\AbstractInterface;



use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;

interface ParserInterface
{
    function getObjectAnnotation(string $class, ?int $filterType = null): ObjectAnnotation;
}