<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;

class GroupInfo
{
    /**
     * @var ApiGroup|null
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
     * @return ApiGroup|null
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