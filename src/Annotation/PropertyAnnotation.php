<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\HttpAnnotation\AnnotationTag\Context;
use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\AnnotationTag\InjectParamsContext;

class PropertyAnnotation
{
    protected $name;
    /** @var Di|null */
    protected $di;
    /** @var InjectParamsContext|null */
    protected $injectParamsContext;
    /** @var Context|null */
    protected $context;

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

    /**
     * @return Di|null
     */
    public function getDi(): ?Di
    {
        return $this->di;
    }

    /**
     * @param Di|null $di
     */
    public function setDi(?Di $di): void
    {
        $this->di = $di;
    }

    /**
     * @return InjectParamsContext|null
     */
    public function getInjectParamsContext(): ?InjectParamsContext
    {
        return $this->injectParamsContext;
    }

    /**
     * @param InjectParamsContext|null $injectParamsContext
     */
    public function setInjectParamsContext(?InjectParamsContext $injectParamsContext): void
    {
        $this->injectParamsContext = $injectParamsContext;
    }

    /**
     * @return Context|null
     */
    public function getContext(): ?Context
    {
        return $this->context;
    }

    /**
     * @param Context|null $context
     */
    public function setContext(?Context $context): void
    {
        $this->context = $context;
    }
}