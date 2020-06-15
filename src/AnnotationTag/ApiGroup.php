<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\HttpAnnotation\Exception\Annotation\InvalidTag;

/**
 * Class ApiGroup
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiGroup extends AbstractAnnotationTag
{
    public $groupName;

    public function tagName(): string
    {
        return 'ApiGroup';
    }

    public function assetValue(?string $raw)
    {
        parse_str($raw,$str);
        if(!empty($str['groupName'])){
            $this->groupName = trim($str['groupName']," \t\n\r\0\x0B\"'");
        }
        if(empty($this->groupName)){
            throw new InvalidTag("groupName is required");
        }
    }

}