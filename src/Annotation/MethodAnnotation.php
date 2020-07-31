<?php


namespace EasySwoole\HttpAnnotation\Annotation;

class MethodAnnotation
{
    protected $name;

    protected $api;
    protected $apiAuth = [];
    protected $apiDescription;
    protected $apiFail = [];
    protected $apiFailParam = [];
    protected $apiRequestExample = [];
    protected $apiSuccess = [];
    protected $apiSuccessParam = [];
    protected $circuitBreaker;
    protected $injectParamsContext;
    protected $method;
    protected $param = [];

    function __construct(string $name)
    {
        $this->name = $name;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}