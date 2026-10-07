<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\HttpAnnotation\Bean\Description\AbstractDescription;
use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected string $host = "";
    protected string $projectName = "EasySwoole";

    protected AbstractDescription|null $description = null;

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

}