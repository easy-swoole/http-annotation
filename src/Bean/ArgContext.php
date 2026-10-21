<?php

namespace EasySwoole\HttpAnnotation\Bean;

use EasySwoole\Component\Singleton;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Coroutine;

class ArgContext
{
    use Singleton;

    // 单例跨协程共享，缓存必须按协程 ID 隔离，不能直接保存当前请求的数据。
    private array $cacheJson = [];
    private array $cacheXml = [];
    private array $deferRegistered = [];

    public function getJson(ServerRequestInterface $request): mixed
    {
        $cid = Coroutine::getCid();
        if (isset($this->cacheJson[$cid])) {
            return $this->cacheJson[$cid];
        }

        $body = $request->getBody()->__toString();
        $data = empty($body) ? [] : (json_decode($body, true) ?: []);
        $this->registerCleanup($cid);
        $this->cacheJson[$cid] = $data;
        return $data;
    }

    public function getXml(ServerRequestInterface $request): array
    {
        $cid = Coroutine::getCid();
        if (isset($this->cacheXml[$cid])) {
            return $this->cacheXml[$cid];
        }

        $body = $request->getBody()->__toString();
        $data = json_decode(json_encode(simplexml_load_string($body)), true);
        $data = is_array($data) ? $data : [];
        $this->registerCleanup($cid);
        $this->cacheXml[$cid] = $data;
        return $data;
    }

    private function registerCleanup(int $cid): void
    {
        if (isset($this->deferRegistered[$cid])) {
            return;
        }
        $this->deferRegistered[$cid] = true;
        // 每个协程只注册一次，不捕获 Request、Param 或校验上下文。
        Coroutine::defer(function () use ($cid): void {
            unset($this->cacheJson[$cid], $this->cacheXml[$cid], $this->deferRegistered[$cid]);
        });
    }
}
