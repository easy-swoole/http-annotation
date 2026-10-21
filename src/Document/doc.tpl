<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="description" content="Description"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <style>{{$asset:document.css}}</style>
</head>
<body>
<div class="container">
    <!-- 全局标题 -->
    <header class="navBar">
        <div class="navInner">
            <div class="doc-brand">
                <span class="doc-logo" aria-hidden="true">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m8 7-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/>
                    </svg>
                </span>
                <div class="doc-brand-text">
                    <button type="button" id="projectName" aria-label="展示全局文档说明"></button>
                    <span class="doc-subtitle">API DOCUMENTATION</span>
                </div>
            </div>
            <div class="doc-header-meta">
                <div class="doc-search" id="doc-search">
                    <input id="doc-search-input" type="search" placeholder="搜索分组、接口、路径" aria-label="搜索文档" aria-controls="doc-search-results" aria-expanded="false" autocomplete="off">
                    <div id="doc-search-results" class="search-results" aria-label="搜索结果" hidden></div>
                </div>
            </div>
        </div>
    </header>

    <!-- 服务端生成的分组菜单 -->
    <aside class="sideBar" id="sideBar">
        {{$sideBar}}
    </aside>
    <section class="mainContent">
        <div class="content" id="content">
            {{$introduction}}
        </div>
        <div class="right-menu" id="right-menu" style="display: none"></div>
    </section>
</div>
<!-- 接口试运行：表单和结果面板 -->
<dialog id="try-dialog" aria-labelledby="try-title">
    <div class="try-header">
        <div>
            <span class="try-eyebrow">API EXPLORER</span>
            <h2 id="try-title">立即尝试</h2>
        </div>
        <button type="button" class="try-close" id="try-close" aria-label="关闭">×</button>
    </div>
    <form id="try-form">
        <div class="try-request-line">
            <label class="try-method-label">请求方法 <input id="try-method" readonly tabindex="-1"></label>
            <label class="try-address-label">请求地址 <input id="try-url" type="url" required></label>
        </div>
        <div class="try-section-heading">
            <h3>请求参数</h3>
            <span>勾选要发送的参数</span>
        </div>
        <div id="try-fields"></div>
        <p class="try-hint">COOKIE 参数可在同源文档中填写并设置；已有 Cookie 随请求发送，跨域需允许凭证和当前文档来源。</p>
        <div class="try-actions">
            <label>超时（秒） <input id="try-timeout" type="number" min="1" max="300" value="30" required></label>
            <button class="try-button" id="try-run" type="submit">立即运行 →</button>
        </div>
    </form>
    <section id="try-result" aria-live="polite" hidden>
        <h3>运行结果</h3>
        <p id="try-status"></p>
        <details class="try-response-headers">
            <summary>响应头</summary>
            <pre id="try-headers"></pre>
        </details>
        <h4>响应内容</h4>
        <pre id="try-body"></pre>
    </section>
</dialog>
<script>{{$asset:state.js}}{{$asset:render.js}}{{$asset:try-form.js}}{{$asset:try-request.js}}{{$asset:navigation.js}}{{$asset:search.js}}{{$asset:hash.js}}{{$asset:bootstrap.js}}</script>
</body>
</html>
