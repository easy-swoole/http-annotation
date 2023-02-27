<?php

namespace EasySwoole\HttpAnnotation\Document;

use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected string $host = "";
    protected string $projectName = "EasySwoole";

    protected ?Description $description;

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

    /**
     * @return Description|null
     */
    public function getDescription(): ?Description
    {
        return $this->description;
    }

    /**
     * @param Description|null $description
     */
    public function setDescription(?Description $description): void
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