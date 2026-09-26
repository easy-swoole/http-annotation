<?php

namespace EasySwoole\HttpAnnotation\Bean;

use EasySwoole\HttpAnnotation\Attributes\Property\Context;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;

class PropertyAttribute
{
    public Context|null $context = null;

    public Di|null $di = null;
}