<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;

class ApiGroup
{
    /**
     * @var \EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup|null
     */
    protected $apiGroup;
    /**
     * @var ApiGroupDescription|null
     */
    protected $apiGroupDescription;
    /**
     * @var array
     */
    protected $apiGroupAuth = [];


    /**
     * @return \EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup|null
     */
    public function getApiGroup(): ?ApiGroup
    {
        return $this->apiGroup;
    }

    /**
     * @param ApiGroup|null $apiGroup
     */
    public function setApiGroup(?ApiGroup $apiGroup): void
    {
        $this->apiGroup = $apiGroup;
    }

    /**
     * @return ApiGroupDescription|null
     */
    public function getApiGroupDescription(): ?ApiGroupDescription
    {
        return $this->apiGroupDescription;
    }

    /**
     * @param ApiGroupDescription|null $apiGroupDescription
     */
    public function setApiGroupDescription(?ApiGroupDescription $apiGroupDescription): void
    {
        $this->apiGroupDescription = $apiGroupDescription;
    }

    public function getApiGroupAuth():array
    {
        return $this->apiGroupAuth;
    }

    public function setApiGroupAuth(array $array): void
    {
        $this->apiGroupAuth = $array;
    }

    public function addApiGroupAuth(ApiGroupAuth $apiGroupAuth)
    {
        $this->apiGroupAuth[$apiGroupAuth->name] = $apiGroupAuth;
    }

}