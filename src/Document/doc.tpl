<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="description" content="Description"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <style>

        .container .navBar {
            position: fixed;
            z-index: 20;
            top: 0; left: 0; right: 0;
            height: 3.6rem;
            box-sizing: border-box;
            padding: 0 1.5rem;
            background: rgba(255, 255, 255, .96);
            border-bottom: 1px solid #e4eaf0;
            box-shadow: 0 2px 12px rgba(31, 52, 73, .04);
            backdrop-filter: blur(12px);
        }
        .navInner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            height: 100%;
        }
        .doc-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }
        .doc-logo {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 2.15rem;
            height: 2.15rem;
            border-radius: .65rem;
            background: linear-gradient(135deg, #22b889, #148c80);
            color: white;
            box-shadow: 0 3px 8px rgba(20, 140, 128, .18);
        }
        .doc-brand-text { min-width: 0; }
        #projectName {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: .95rem;
            font-weight: 650;
            line-height: 1.35;
            color: #203247;
        }
        .doc-subtitle {
            display: block;
            margin-top: .1rem;
            font-size: .65rem;
            line-height: 1.3;
            letter-spacing: .08em;
            color: #7c8b9a;
        }
        .doc-header-meta {
            display: flex;
            align-items: center;
            gap: .8rem;
            flex-shrink: 0;
        }
        .doc-header-label { color: #81909e; font-size: .75rem; }
        .doc-header-badge {
            padding: .15rem .6rem;
            border: 1px solid #d9eee6;
            border-radius: 999px;
            background: #f0faf5;
            color: #238365;
            font-size: .72rem;
            line-height: 1.5;
            font-weight: 600;
        }
        @media (max-width: 600px) {
            .container .navBar { padding: 0 .8rem; }
            .navInner { gap: .6rem; }
            .doc-header-label { display: none; }
            .doc-brand { gap: .55rem; }
        }

        .container .mainContent {
            margin-left: 15rem;
            padding: 6rem 2rem 2rem;
            display: grid;
            grid-template-columns: minmax(0, 740px) 220px;
            justify-content: center;
            align-items: start;
            gap: 2rem;
        }
        .container .mainContent .content {
            min-width: 0;
            padding: 0;
            overflow-wrap: anywhere;
        }
        .content pre { overflow-x: auto; }

        .container .sideBar {
            font-size: 16px;
            background-color: #fff;
            width: 15rem;
            position: fixed;
            z-index: 10;
            margin: 0;
            top: 3.6rem;
            left: 0;
            bottom: 0;
            box-sizing: border-box;
            border-right: 1px solid #eaecef;
            overflow-y: auto;
            display: block;
        }

        .container .sideBar::-webkit-scrollbar {
            width: 2px;
            height: 9px;
        }

        .container .sideBar::-webkit-scrollbar-track {
            width: 2px;
            background-color: #d2d3d6;
            -webkit-border-radius: 2em;
            -moz-border-radius: 2em;
            border-radius: 2em;
        }

        .container .sideBar::-webkit-scrollbar-thumb {
            background-color: #606d71;
            background-clip: padding-box;
            min-height: 28px;
            -webkit-border-radius: 2em;
            -moz-border-radius: 2em;
            border-radius: 2em;
        }

        .container .sideBar::-webkit-scrollbar-thumb:hover {
            background-color: #fff;
        }

        .container .sideBar>ul {
            padding: 1.5rem 0;
            display: block;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            padding-inline-start: 1em;
            margin: 0;
            list-style-type: none;
            box-sizing: border-box;
            font-size: 1.1em;
            font-weight: bold;
            text-transform: capitalize;
            border-left: 0.5rem solid transparent;
        }

        .container .sideBar>ul li {
            display: list-item;
            text-align: -webkit-match-parent;
            list-style-type: none;
            padding: 0.1em 0.1rem 0.2em 0.1rem;
            cursor: pointer;
        }

        .container .sideBar>ul li.active ul {
            display: block;
        }

        .container .sideBar>ul li>ul {
            padding: 0.3em 0.8em;
            list-style-type: none;
            box-sizing: border-box;
            font-size: 0.8em;
            font-weight: bold;
            color: #3f5163;
            display: none;
        }

        .container .sideBar>ul li>ul li {
            padding-top: 0.3rem;
        }

        .container .sideBar>ul li>ul li>li {
            padding-top: 0;
            display: block;
        }

        .container .sideBar>ul li a {
            color: #2c3e50;
            width: 100%;
            font-size: 1.1em;
            font-weight: 400;
            border-left: 0.25rem solid transparent;
            padding: 0.35rem 1rem 0.35rem 0.25rem;
            line-height: 1.4;
            box-sizing: border-box;
            cursor: pointer;
            text-decoration: none;
            padding-left: 0.3rem;
            display: inline-block;
        }


        .fa-angle-right::before {
            padding-right: 0.3rem
        }

        .fa-angle-down::before {
            padding-right: 0.3rem
        }

        li {
            line-height: 1.7rem !important;
        }


        /* 设置滚动条的样式 */
        ::-webkit-scrollbar {
            width: 6px;
        }
        /* 外层轨道 */
        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset006pxrgba(255, 0, 0, 0.3);
            background: rgba(0, 0, 0, 0.1);
        }
        /* 滚动条滑块 */
        ::-webkit-scrollbar-thumb {
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.2);
            -webkit-box-shadow: inset006pxrgba(0, 0, 0, 0.5);
        }
        ::-webkit-scrollbar-thumb:window-inactive {
            background: rgba(0, 0, 0, 0.2);
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Helvetica, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", SimSun, sans-serif;
            font-size: 13px;
            line-height: 25px;
            color: #393838;
            position: relative;
        }
        table {
            width: 700px !important;
            margin: 10px 0 15px 0;
            border-collapse: collapse;
        }
        td,
        th {
            /*text-align: center;*/
            border: 1px solid #ddd;
            padding: 3px 10px;
        }
        th {
            padding: 5px 10px;
        }
        a, a:link, a:visited {
            color: #34495e;
            text-decoration: none;
        }
        a:hover, a:focus {
            color: #59d69d;
            text-decoration: none;
        }
        a img {
            border: none;
        }
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #404040;
            line-height: 36px;
        }
        h1 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 16px;
            font-size: 32px;
            padding-bottom: 16px;
            border-bottom: 1px solid #ddd;
            line-height: 50px;
        }
        h2 {
            font-size: 28px;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        h3 {
            clear: both;
            font-weight: 400;
            margin-top: 20px;
            margin-bottom: 20px;
            border-left: 3px solid #59d69d;
            padding-left: 8px;
            font-size: 18px;
        }
        h4 {
            font-size: 16px;
        }
        h5 {
            font-size: 14px;
        }
        h6 {
            font-size: 13px;
        }
        hr {
            margin: 0 0 19px;
            border: 0;
            border-bottom: 1px solid #ccc;
        }
        blockquote {
            padding: 13px 13px 21px 15px;
            margin-bottom: 18px;
            font-family: georgia, serif;
            font-style: italic;
        }
        blockquote:before {
            font-size: 40px;
            margin-left: -10px;
            font-family: georgia, serif;
            color: #eee;
        }
        blockquote p {
            font-size: 14px;
            font-weight: 300;
            line-height: 18px;
            margin-bottom: 0;
            font-style: italic;
        }
        code,
        pre {
            font-family: Monaco, Andale Mono, Courier New, monospace;
        }
        code {
            background-color: #fee9cc;
            color: rgba(0, 0, 0, 0.75);
            padding: 1px 3px;
            font-size: 12px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
        }
        pre {
            display: block;
            padding: 14px;
            margin: 0 0 18px;
            line-height: 16px;
            font-size: 11px;
            border: 1px solid #d9d9d9;
            white-space: pre-wrap;
            word-wrap: break-word;
            background: #f6f6f6;
        }
        pre code {
            background-color: #f6f6f6;
            color: #737373;
            font-size: 11px;
            padding: 0;
        }
        sup {
            font-size: 0.83em;
            vertical-align: super;
            line-height: 0;
        }
        * {
            -webkit-print-color-adjust: exact;
        }
        @media print {
            body,
            code,
            pre code,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                color: black;
            }
            table,
            pre {
                page-break-inside: avoid;
            }
        }
        html,
        body {
            height: 100%;
        }
        .table-of-contents {
            position: fixed;
            top: 61px;
            left: 0;
            bottom: 0;
            /*overflow-x: hidden;*/
            /*overflow-y: auto;*/
            width: 260px;
        }
        .table-of-contents > ul > li > a {
            font-size: 20px;
            margin-bottom: 16px;
            margin-top: 16px;
        }
        .table-of-contents ul {
            overflow: auto;
            margin: 0px;
            height: 100%;
            padding: 0px 0px;
            box-sizing: border-box;
            list-style-type: none;
        }
        .table-of-contents ul li {
            padding-left: 20px;
        }
        .table-of-contents a {
            padding: 2px 0px;
            display: block;
            text-decoration: none;
        }
        .content-right {
            max-width: 700px;
            flex-grow: 1;
        }
        .content-right h1:target {
            padding-top: 80px;
        }
        .content-right h2:target {
            padding-top: 80px;
        }
        body > p {
            margin-left: 30px;
        }
        body > table {
            margin-left: 30px;
        }
        body > pre {
            margin-left: 30px;
        }


        #extra > p {
            margin-bottom: 9px;
            margin-top: 9px;
            padding-left: 0;
            font-size: 16px;
        }
        blockquote {
            overflow: visible;
            margin: 20px 0 !important;
            padding: 16px !important;
            border-width: 0 0 0 4px;
            border-left: 3px solid #59d69d;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Helvetica, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", SimSun, sans-serif;
            font-size: 16px;
            line-height: 25px;
            color: #393838;
            background: #f6f6f6;
        }
        blockquote  p {
            font-style: normal !important;
            font-weight: 400 !important;
            font-size: 16px !important;
            padding-left: 0 !important;
        }
        #extra a {
            text-decoration: underline;
        }

        .right-menu {
            position: sticky;
            top: 5rem;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            border-left: 1px solid #e4eaf0;
            padding: .25rem 0 .5rem 1rem;
            max-height: calc(100vh - 6rem);
            overflow-y: auto;
            overflow-wrap: anywhere;
            font-size: .8rem;
        }
        .right-menu a { display: block; padding: .2rem 0; }
        .right-menu > .title {
            color: #aaaaaa;
            background-color: #fff;
            width: 100%;
            right: 15px;
            padding-left: 0.1em;
            line-height: 200%;
            border-bottom: 1px solid #EEEEEE;
            cursor: pointer;
        }
        @media (max-width: 600px) {
            .right-menu {
                display:none;
            }
            #live2d-widget {
                display: none;
            }
        }

        .right-menu > li{
            list-style-type: none;
            padding-left:5px;
            padding-top: 5px;
        }
        .right-menu > li > a.active{
            color:#ff0006;
        }

        .arrow-right{
            list-style: none;
            padding-right: 1.5rem;
            background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAB6ElEQVRYR82WPUvDQBjHmzbYDoIiiG8dpDi4KOKi38A3/BhuugSSJtghDk2TUlPQza8iWN911S/g4O6cRGj9P9BAKMXc5a7WLnell/v97n/PXaPkxvxRxszP/X8B27bVYrH4qCjKRj6f3zEM405maqkJOI6zBfArQXu9XoD+frVa7ciSSBWgBEql0gOA232JCO2eZVk3MiRSBQgCiUlIUPSbfWiINHYhcSsqwSRAENd1p9BQEmsyJZgFCOr7/kwURbRqaRJcArFEGIZPOBWrMpLgFiAoTsYsBO6TEugf4nRc89ZEJoGExDPAK33oN/oHvBKZBQjabDbnu93uC7rLWSWEBAjqeV4ZR5JORyYJYYGEBCVR5k1CigBBG41GBQ0V5hJ9RyoR/kMWNE37+q0wpQkQBJfVFZqjGFgoFCq6rn/8iQDg5wBpCZhjmuZp2rGUkgDiv0T0xwlYC3A9DU6/CwuIwIUFROFCAjLgmQUGqx0TMe/5YF1w18AgHMVn4/4/Yym4YWO4BIasXEe1t7LCubZgFHBmAcAvMPgksVILK3dFVh4/m7oF9Xp9HVfqW/wA7ngTL6OeDDhTAu12ezoIgncU2yIeMLByXxacSYAG4bV8QlXVuVqt9ikTziwgG5qcL7UGRgmnuX8A4ie8ITn6AnkAAAAASUVORK5CYII=") no-repeat;
            background-size: 1.5rem;
            display: inline;
            *display: inline;
            zoom: 1;
        }


        .arrow-down{
            list-style: none;
            padding-right: 1.5rem;
            background-size: 1.5rem;
            display: inline;
            *display: inline;
            background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABk0lEQVRYR+2Vy06DUBCGe1jUNix8HxfGGBeoiTHGhU/A5WXYctmwcUXSxC7UJnZhXPg4EBdG8QLONGAGSstwiqkLmpBCOP3/b/6ZcyoGW/6ILfsPeoA+gf+XgOd5u4ZhvPzF7qjTLiXg+/5ZlmXXQghN1/XnLiFAew+0Z3BdmaZ5V2j/Ariuq4HxFF4MYdGboihHXUGgeZqmc9Afg/4HfJ+C9hwhKMAEXlyQql8B5ARonzZJAgrbB12sWCU6E2jzZQkgDMNhFEVTjL9YuGkSlcoXsqD5gAkAwGcJAB+6hOCYLwGsg8B2WJb1yGmH4zgHGHve89rKl4aQCq9IIgEIrQkiN5+B+Q5pZSl26rXyIJKBaGte2wLZJGTMGwHWzESpHbLmLIAmiIWIEOyeV4eY/WeEMxHH8Q0IHBOR9/x+RAYOp/+82OdNu4YNQJK4B4PDOuHqIdNkzm4BFQqCYJQkyW0VQsZcCgB/VIWQNZcGoBBw/03Pdk7srIOII2Tb9lhV1S/uwNVpthpCDlTbNT1An0CfwA8NqzYw/4+BawAAAABJRU5ErkJggg==") no-repeat;
        }

        .sideBar .group-toggle {
            border: 0; background: none; color: #2c3e50; font: inherit;
            text-align: left; cursor: pointer; padding: .35rem .3rem; width: 100%;
        }
        .container .sideBar ul li > ul { display: block; }
        .container .sideBar ul li > ul[hidden] { display: none; }
        .container .sideBar a.active { color: #0080ff; text-decoration: underline; }
        .content h2, .content h3 { scroll-margin-top: 4rem; }
        table { width: 100% !important; }
        .parameter-table th, .parameter-table td { text-align: left; }
        .parameter-table th:nth-child(1), .parameter-table td:nth-child(1),
        .parameter-table th:nth-child(2), .parameter-table td:nth-child(2),
        .parameter-table th:nth-child(5), .parameter-table td:nth-child(5),
        .parameter-table td.cell-empty { text-align: center; }
        .parameter-table .param-deprecated { position: relative; padding-top: 1.2rem; }
        .param-deprecated-label, .api-deprecated-label {
            position: absolute;
            top: .1rem;
            right: .3rem;
            color: #b77937;
            font-size: .6rem;
            line-height: 1rem;
            white-space: nowrap;
            font-weight: normal;
        }
        .api-deprecated-label {
            position: static;
            display: inline-block;
            margin-left: .4rem;
            font-size: .7rem;
            vertical-align: super;
        }
        @media (max-width: 1199px) {
            .container .mainContent { grid-template-columns: minmax(0, 740px); padding: 5rem 1.5rem 2rem; }
            .right-menu { display: none !important; }
        }
    </style>
</head>
<body>
<div class="container">
    <header class="navBar">
        <div class="navInner">
            <div class="doc-brand">
                <span class="doc-logo" aria-hidden="true">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m8 7-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/>
                    </svg>
                </span>
                <div class="doc-brand-text">
                    <span id="projectName"></span>
                    <span class="doc-subtitle">API DOCUMENTATION</span>
                </div>
            </div>
            <div class="doc-header-meta">
                <span class="doc-header-label">接口参考文档</span>
                <span class="doc-header-badge">HTTP API</span>
            </div>
        </div>
    </header>

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
<script>
    const jsonData = {{$docData}};
    const config = {{$config}};
    const content = document.getElementById('content');
    const sideBar = document.getElementById('sideBar');
    document.title = config.projectName;
    document.getElementById('projectName').textContent = config.projectName;

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[character]);
    }

    function displayValue(value) {
        return value == null ? '-' : escapeHtml(typeof value === 'object' ? JSON.stringify(value) : value);
    }

    function description(value) {
        return value ? '<pre>' + escapeHtml(value) + '</pre>' : '<p>暂无说明</p>';
    }

    function findGroup(path) {
        let map = jsonData;
        let group;
        for (const name of path) {
            if (!Object.prototype.hasOwnProperty.call(map, name)) return null;
            group = map[name];
            map = group.children;
        }
        return group;
    }

    function parameterTable(params) {
        const entries = Object.entries(params || {});
        if (!entries.length) return '<p>暂无参数</p>';
        let html = '<table class="parameter-table"><thead><tr><th>名称</th><th>类型</th><th>校验规则</th><th>说明</th><th>默认值</th></tr></thead><tbody>';
        for (const [name, param] of entries) {
            const rules = Object.values(param.validateRules || {})
                .filter(rule => rule.msg != null && String(rule.msg).trim() !== '')
                .map(rule => escapeHtml(rule.msg)).join('<br>');
            const descriptionEmpty = param.description == null || String(param.description).trim() === '';
            html += '<tr><td' + (param.deprecated === true ? ' class="param-deprecated"' : '') + '>'
                + escapeHtml(name) + (param.deprecated === true ? '<span class="param-deprecated-label">已废弃</span>' : '')
                + '</td><td>' + displayValue(param.type) + '</td><td' + (rules ? '' : ' class="cell-empty"') + '>' + (rules || '-')
                + '</td><td' + (descriptionEmpty ? ' class="cell-empty"' : '') + '>'
                + (descriptionEmpty ? '-' : displayValue(param.description)) + '</td><td>'
                + displayValue(param.defaultValue) + '</td></tr>';
        }
        return html + '</tbody></table>';
    }

    function examples(items, title) {
        return items && items.length ? items.map((item, index) => '<h4>' + escapeHtml(title) + ' ' + (index + 1) + '</h4><pre><code>' + escapeHtml(item) + '</code></pre>').join('') : '<p>暂无示例</p>';
    }

    function renderRightMenu() {
        const menu = document.getElementById('right-menu');
        menu.replaceChildren();
        const headings = content.querySelectorAll('h2, h3');
        menu.style.display = headings.length ? 'block' : 'none';
        headings.forEach((heading, index) => {
            heading.id = 'section-' + index;
            const item = document.createElement('li');
            const link = document.createElement('a');
            link.href = '#' + heading.id;
            link.textContent = heading.textContent;
            item.append(link);
            menu.append(item);
        });
    }

    sideBar.addEventListener('click', event => {
        const target = event.target.closest('button[data-path], a[data-api]');
        if (!target || !sideBar.contains(target)) return;
        event.preventDefault();
        const path = JSON.parse(target.dataset.path);
        const group = findGroup(path);
        if (!group) return;
        if (target.matches('button')) {
            const list = target.nextElementSibling;
            list.hidden = !list.hidden;
            target.setAttribute('aria-expanded', String(!list.hidden));
            target.querySelector('.menu-arrow').textContent = list.hidden ? '▸' : '▾';
            content.innerHTML = '<h1>' + escapeHtml(path.join('.')) + '</h1>'
                + description(group.description)
                + (Object.keys(group.onRequestParams || {}).length
                    ? '<h3>公共请求参数</h3>' + parameterTable(group.onRequestParams)
                    : '');
        } else {
            const api = group.apiList[target.dataset.api];
            if (!api) return;
            sideBar.querySelectorAll('a.active').forEach(link => link.classList.remove('active'));
            target.classList.add('active');
            // 接口参数覆盖同名公共参数，并排除当前接口忽略的参数。
            const params = Object.assign({}, group.onRequestParams, api.requestParams);
            for (const name of Object.keys(params)) {
                if ((params[name].ignoreAction || []).includes(api.apiName)) delete params[name];
            }
            content.innerHTML = '<h1 class="api-title">' + escapeHtml(api.apiName)
                + (api.deprecated === true ? '<span class="api-deprecated-label">已废弃</span>' : '') + '</h1>'
                + '<h3>请求地址</h3><pre>' + escapeHtml(config.host + api.requestPath) + '</pre>'
                + '<h3>接口说明</h3>' + (api.descriptionHtml || description(api.description))
                + '<h3>请求参数</h3>' + parameterTable(params)
                + '<h3>请求示例</h3>' + examples(api.requestExamples, '请求示例')
                + '<h3>成功响应示例</h3>' + examples(api.responseExamples.success, '成功响应示例')
                + '<h3>失败响应示例</h3>' + examples(api.responseExamples.fail, '失败响应示例');
        }
        renderRightMenu();
        window.scrollTo(0, 0);
    });
    renderRightMenu();
</script>
</body>
</html>
