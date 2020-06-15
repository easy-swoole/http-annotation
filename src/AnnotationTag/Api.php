<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\Annotation\ValueParser;

/**
 * Class Api
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Api extends AbstractAnnotationTag
{
    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $description;
    /**
     * @var bool
     */
    public $deprecated;
    /**
     * @var string
     */
    public $version = '1.0.0';


    /**
     * @var string
     */
    public $group = 'Api';

    public function tagName(): string
    {
        return  'Api';
    }

    public function assetValue(?string $raw)
    {
        $array = ValueParser::parser($raw);
        foreach ($array as $key => $value){
            $this->$key = $value;
        }
    }
}