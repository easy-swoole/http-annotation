<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiGroupDescription;

class ClassAnnotation
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
     * @var ApiGroupAuth|null
     */
    protected $apiGroupAuth;

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

    /**
     * @return ApiGroupAuth|null
     */
    public function getApiGroupAuth(): ?ApiGroupAuth
    {
        return $this->apiGroupAuth;
    }

    /**
     * @param ApiGroupAuth|null $apiGroupAuth
     */
    public function setApiGroupAuth(?ApiGroupAuth $apiGroupAuth): void
    {
        $this->apiGroupAuth = $apiGroupAuth;
    }
}