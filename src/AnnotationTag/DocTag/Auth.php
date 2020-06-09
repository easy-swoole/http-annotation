<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag\DocTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\Annotation\ValueParser;

/**
 * Class Auth
 * @package EasySwoole\HttpAnnotation\AnnotationTag\DocTag
 * @Annotation
 */
class Auth extends AbstractAnnotationTag
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $from = [];

    /**
     * @var string
     */
    public $description;

    public function tagName(): string
    {
        return 'Auth';
    }

    public function assetValue(?string $raw)
    {
        $allParams = ValueParser::parser($raw);
        foreach ($allParams as $key => $val){
            $this->$key = $val;
        }
    }
}