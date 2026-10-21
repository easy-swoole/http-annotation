<?php

namespace EasySwoole\HttpAnnotation\Tests\Document\Fixtures\DuplicateGroup;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;

#[ApiGroup('Duplicate.Example')]
class FirstController extends AnnotationController
{
    public function onRequest(?string $action): ?bool
    {
        return true;
    }
}
