<?php

namespace EasySwoole\HttpAnnotation\Tests\Support;

use Swoole\Coroutine\Scheduler;
use Throwable;

final class CoroutineRunner
{
    public static function run(callable $callback): mixed
    {
        $result = null;
        $error = null;
        $scheduler = new Scheduler();
        $scheduler->add(static function () use ($callback, &$result, &$error): void {
            try {
                $result = $callback();
            } catch (Throwable $caught) {
                $error = $caught;
            }
        });
        $scheduler->start();
        if ($error !== null) {
            throw $error;
        }
        return $result;
    }
}
