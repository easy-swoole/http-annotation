<?php

namespace EasySwoole\HttpAnnotation\Document;


use EasySwoole\HttpAnnotation\Bean\Description\AbstractDescription;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateMsgMapInterface;
use EasySwoole\HttpAnnotation\Validator\MsgMap\DefaultMap;
use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected string $host = "";
    protected string $projectName = "EasySwoole";

    protected AbstractDescription|null $description = null;

    protected string $validateMsgMap = DefaultMap::class;

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @param string $projectName
     */
    public function setProjectName(string $projectName): void
    {
        $this->projectName = $projectName;
    }

    public function getDescription(): AbstractDescription|null
    {
        return $this->description;
    }

    function setDescription(AbstractDescription $description): void
    {
        $this->description = $description;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /** @return class-string<ValidateMsgMapInterface> */
    public function getValidateMsgMap(): string
    {
        return $this->validateMsgMap;
    }

    function setValidateMsgMap(string $validateMsgMapClass): void
    {
        $ref = new \ReflectionClass($validateMsgMapClass);
        if(!$ref->implementsInterface(ValidateMsgMapInterface::class)){
            throw new Annotation("class {$validateMsgMapClass} did not implements ValidateMsgMapInterface");
        }
        $this->validateMsgMap = $validateMsgMapClass;
    }

}