<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\Doc;


use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\ParserDown\ParserDown;

class Render
{
    static function parseToMarkdown(array $methodAnnotation):?string
    {
        //一定要有Api标签
        if(!isset($methodAnnotation['Api'][0])){
            return null;
        }
        /** @var Api $api */
        $api = $methodAnnotation['Api'][0];
        $tpl = '';
        $deprecated = '';
        if($api->deprecated){
            $deprecated = "<sup class='deprecated'>已废弃</sup>";
        }
        $tpl .= "<h2 id='{$api->group}-{$api->name}'>{$api->group}-{$api->name}{$deprecated}</h2>\n";
        $tpl .= "接口说明: <span>{$api->description}</span> \n\n";
        if(isset($methodAnnotation['Method'][0])){
            $method = implode("|",$methodAnnotation['Method'][0]->allow);
        }else{
            $method = '不限制';
        }
        $tpl .= "> <span class='requestMethod'>{$method}</span> <span>```{$api->path}```</span>\n\n";

        $tpl .= "### 请求 \n\n";
        $tpl .= "#### 请求字段 \n\n";
        if(isset($methodAnnotation['Param'])){
            $tpl .= "|字段|类型|描述|验证规则|\n";
            $tpl .= "|----|----|----|----|\n";
            /** @var Param $param */
            foreach ($methodAnnotation['Param'] as $param){
                $rule = json_encode($param->validateRuleList,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                $tpl .= "| {$param->name} | {$param->type} | {$param->description} | {$rule} |\n";
            }
            $tpl .= "\n\n";
        }else{
            $tpl .= "无 \n\n";
        }
        $tpl .= "#### 请求示例 \n\n";
        if(isset($methodAnnotation['ApiRequestExample'])){
            $index = 1;
            foreach ($methodAnnotation['ApiRequestExample'] as $example){
                $tpl .= "##### 请求示例{$index} \n\n";
                $tpl .= "```\n{$example->getContent()}\n```\n";
            }
        }else{
            $tpl .= "无 \n\n";
        }
        $tpl .= "### 响应 \n\n";
        $tpl .= "#### 响应字段 \n\n";
        if(isset($methodAnnotation['ResponseParam'])){
            $tpl .= "|字段|类型|描述|\n";
            $tpl .= "|----|----|----|\n";
            /** @var Param $param */
            foreach ($methodAnnotation['ResponseParam'] as $param){
                $tpl .= "| {$param->name} | {$param->type} | {$param->description} | \n";
            }
            $tpl .= "\n\n";
        }else{
            $tpl .= "无 \n\n";
        }
        $tpl .= "#### 成功响应示例 \n\n";
        if(isset($methodAnnotation['ApiSuccess'])){
            $index = 1;
            foreach ($methodAnnotation['ApiSuccess'] as $example){
                $tpl .= "##### 成功响应示例{$index} \n\n";
                $tpl .= "```\n{$example->getContent()}\n```\n";
            }
        }else{
            $tpl .= "无 \n\n";
        }

        $tpl .= "#### 失败响应示例 \n\n";
        if(isset($methodAnnotation['ApiFail'])){
            $index = 1;
            foreach ($methodAnnotation['ApiFail'] as $example){
                $tpl .= "##### 失败响应示例{$index} \n\n";
                $tpl .= "```\n{$example->getContent()} \n```\n";
            }
        }else{
            $tpl .= "无 \n\n";
        }
        return $tpl;
    }

    public static function renderToHtml(array $methodAnnotations)
    {
        $category = [];
        $temp = '';
        foreach ($methodAnnotations as $methodAnnotation){
            $ret = static::parseToMarkdown($methodAnnotation);
            if($ret){
                $category[$methodAnnotation['Api'][0]->group][] = "{$methodAnnotation['Api'][0]->group}-{$methodAnnotation['Api'][0]->name}";
                $temp .= $ret;
            }
        }

        /*
         * 构造sidebar
         */
        $sideBar = '';
        foreach ($category as $group => $value){
            $sideBar .= "- {$group}\n";
            foreach ($value as $h){
                $sideBar .= "\t- [{$h}](#{$h})\n";
            }
        }

        $path = pathinfo(__FILE__)['dirname'];
        $tpl = file_get_contents($path.'/page.tpl');
        $parser = new ParserDown();
        $ret = $parser->text($sideBar);
        $tpl = str_replace('{$SIDEBAR}',$ret,$tpl);
        $ret = $parser->text($temp);
        return str_replace('{$BODY}',$ret,$tpl);
    }
}