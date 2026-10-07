<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\HttpAnnotation\Bean\Description\DescriptionInterface;
use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected string $host = "";
    protected string $projectName = "EasySwoole";

    protected DescriptionInterface|null $description = null;

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

    public function getDescription(): DescriptionInterface|null
    {
        return $this->description;
    }

    function setDescription(DescriptionInterface $description): void
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

}