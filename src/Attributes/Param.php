<?php

namespace EasySwoole\HttpAnnotation\Attributes;

#[\Attribute(\Attribute::TARGET_ALL|\Attribute::IS_REPEATABLE)]
class Param
{
    const GET = "GET";
    const POST = "POST";
    const COOKIE = "COOKIE";
    const HEADER = "HEADER";
    const FILE = "FILE";
    const RAW = "RAW";
    const JSON = "JSON";
    const CONTEXT = "CONTEXT";
    const DI = "DI";

    public function __construct(string $name,?string $alias = null,array $from = ["GET","POST"],?array $validate = []){}
}