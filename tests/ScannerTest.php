<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\HttpAnnotation\Tests\TestController\Normal;
use EasySwoole\HttpAnnotation\Utility\Scanner;
use PHPUnit\Framework\TestCase;

class ScannerTest extends TestCase
{
    function testGetFileDeclaredClass()
    {
        $class = Scanner::getFileDeclaredClass(__DIR__.'/TestController/Normal.php');
        $this->assertEquals(Normal::class,$class);
    }
}