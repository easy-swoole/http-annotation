<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\Annotation\AbstractAnnotationTag;

class AnnotationBean
{
    protected $__otherTags = [];

    public function addAnnotationTag(AbstractAnnotationTag $annotationTag)
    {
        $propertyName = lcfirst($annotationTag->tagName());
        if(property_exists($this,$propertyName)){
            if(!empty($this->{$propertyName})){
                if(is_array($this->{$propertyName})){
                    $this->{$propertyName}[] = $annotationTag;
                }else{
                    $this->{$propertyName} = $annotationTag;
                }
            }else if(is_array($this->{$propertyName})){
                $this->{$propertyName}[] = $annotationTag;
            }
        }else{
            $this->__otherTags[$annotationTag->tagName()][] = $annotationTag;
        }
    }

    function getOtherTags()
    {
        return $this->__otherTags;
    }
}