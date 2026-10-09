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
        button#projectName {
            border: 0; padding: 0; background: none;
            font-family: inherit; text-align: left; cursor: pointer;
        }
        button#projectName:disabled { cursor: default; }
        button#projectName:focus-visible { outline: 2px solid #148c80; outline-offset: 3px; }
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
        @media (max-width: 600px) {
            .container .navBar { padding: 0 .8rem; }
            .navInner { gap: .6rem; }
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
            font-size: 1rem;
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
            font-size: 1em;
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
            font-size: .9rem;
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
        .parameter-table th:nth-child(6), .parameter-table td:nth-child(6),
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
        .api-heading { display: flex; align-items: center; gap: 1rem; justify-content: space-between; }
        .api-heading h1 { min-width: 0; flex: 1; }
        .try-button { border: 0; border-radius: .4rem; background: #148c80; color: white; padding: .55rem 1rem; cursor: pointer; white-space: nowrap; }
        .try-button:disabled { opacity: .6; cursor: wait; }
        #try-dialog { width: min(760px, calc(100vw - 2rem)); max-height: 85vh; box-sizing: border-box; border: 1px solid #e4eaf0; border-radius: .8rem; padding: 1.5rem; overflow: auto; }
        #try-dialog::backdrop { background: rgba(20, 35, 50, .45); }
        .try-header { display: flex; justify-content: space-between; align-items: center; }
        .try-header h2 { margin: 0; font-size: 1.3rem; }
        .try-close { background: none; border: 0; font-size: 1.5rem; cursor: pointer; }
        #try-form label { display: block; margin: .6rem 0; }
        #try-form input:not([type=checkbox]), #try-form textarea, #try-form select { box-sizing: border-box; max-width: 100%; padding: .4rem; border: 1px solid #ced7e0; border-radius: .3rem; }
        #try-url { width: 100%; }
        .try-field { padding: .6rem 0; border-bottom: 1px solid #eaecef; }
        .try-field .try-value { width: 100%; margin: .3rem 0; }
        .try-hint { color: #687889; font-size: .75rem; }
        #try-result { margin-top: 1rem; }
        #try-status { white-space: pre-wrap; }
        #try-result pre { max-height: 24rem; overflow: auto; white-space: pre-wrap; overflow-wrap: anywhere; }
        #try-dialog { width: min(880px, calc(100vw - 2rem)); padding: 0; border: 0; border-radius: 1rem; color: #26384b; box-shadow: 0 24px 80px rgba(15, 35, 55, .25); }
        .try-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid #e5ebef; background: #f8fbfa; position: sticky; top: 0; z-index: 1; }
        .try-eyebrow { color: #148c80; font-size: .65rem; font-weight: 700; letter-spacing: .12em; }
        .try-header h2 { line-height: 1.6; font-size: 1.15rem; overflow-wrap: anywhere; }
        .try-close { color: #647889; border-radius: .4rem; width: 2rem; height: 2rem; }
        .try-close:hover { background: #e7efed; }
        #try-form { padding: 1rem 1.5rem 1.5rem; }
        .try-request-line { display: flex; gap: .7rem; }
        .try-method-label { width: 6rem; flex-shrink: 0; }
        .try-address-label { flex: 1; min-width: 0; }
        #try-form .try-request-line input { width: 100%; margin-top: .3rem; height: 2.6rem; }
        #try-form #try-method { background: #eaf7f2; color: #168368; border-color: #cce8dd; font-weight: 700; text-align: center; }
        #try-form input:focus, #try-form select:focus { outline: 2px solid #9dd8c9; outline-offset: 1px; }
        .try-section-heading { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .try-section-heading h3 { margin: .6rem 0; font-size: .9rem; }
        .try-section-heading span { color: #8392a0; font-size: .72rem; }
        #try-fields { border: 1px solid #e5ebef; border-radius: .5rem; padding: 0 .9rem; }
        .try-field { display: grid; grid-template-columns: minmax(0, 1fr) 7rem; gap: .2rem .8rem; padding: .65rem 0; }
        .try-field:last-child { border-bottom: 0; }
        #try-form .try-field label { margin: 0; overflow-wrap: anywhere; }
        .try-field .try-value, .try-field .try-hint { grid-column: 1 / -1; }
        .try-field .try-source { width: 100%; }
        #try-form .try-source:enabled { background: #fff; color: #2c3e50; cursor: pointer; }
        #try-form .try-source:disabled { background: #f0f2f5; color: #8392a0; border-color: #e1e7ed; opacity: 1; -webkit-text-fill-color: #8392a0; cursor: default; }
        .try-include { accent-color: #148c80; }
        #try-raw-body { width: 100%; min-height: 13rem; resize: vertical; font-family: monospace; line-height: 1.6; margin: .5rem 0; }
        .try-body-editor { padding: .7rem 0; }
        .try-body-editor label { font-weight: 600; }
        .try-empty { color: #8392a0; text-align: center; padding: 1rem; }
        .try-actions { display: flex; justify-content: space-between; align-items: center; gap: 1rem; border-top: 1px solid #edf1f4; padding-top: .7rem; }
        #try-timeout { width: 5rem; margin-left: .4rem; }
        #try-result { margin: 0; padding: .5rem 1.5rem 1.5rem; border-top: 1px solid #e5ebef; background: #f7f9fb; }
        #try-result h3 { font-size: 1rem; margin: .7rem 0; }
        #try-status { padding: .6rem .8rem; background: #e9f1f7; border-radius: .4rem; font-family: monospace; }
        #try-result pre { background: white; border-color: #e1e7ed; border-radius: .4rem; font-size: .78rem; line-height: 1.6; }
        .try-response-headers summary { cursor: pointer; color: #687889; padding: .4rem 0; }
        @media (max-width: 600px) {
            #try-form, #try-result, .try-header { padding-left: 1rem; padding-right: 1rem; }
            .try-section-heading span { display: none; }
            .try-field { grid-template-columns: minmax(0, 1fr) 5.5rem; }
        }
        /* 文档全局搜索 */
        .doc-search { position: relative; width: 240px; }
        #doc-search-input { width: 100%; box-sizing: border-box; padding: .5rem .75rem; border: 1px solid #dce5eb; border-radius: .5rem; background: #f7faf9; font: inherit; }
        #doc-search-input:focus { outline: 2px solid #9dd8c9; outline-offset: 1px; background: white; }
        .search-results { position: absolute; right: 0; top: calc(100% + .6rem); width: min(420px, calc(100vw - 2rem)); max-height: 60vh; overflow-y: auto; background: white; border: 1px solid #e4eaf0; border-radius: .6rem; box-shadow: 0 12px 35px rgba(20, 40, 60, .15); padding: .35rem; }
        .search-result { display: block; width: 100%; text-align: left; border: 0; background: none; cursor: pointer; padding: .6rem .7rem; border-radius: .35rem; color: #26384b; font: inherit; overflow-wrap: anywhere; }
        .search-result:hover, .search-result:focus-visible { background: #edf8f3; }
        .search-result span, .search-result small { display: block; }
        .search-result small { color: #7c8b9a; margin-top: .15rem; }
        .search-result .search-context { color: #526779; line-height: 1.6; margin-top: .4rem; font-size: .75rem; }
        .search-result mark { background: #fff0ad; color: #614d00; border-radius: .15rem; padding: 0 .1rem; }
        .search-empty { padding: .6rem; color: #7c8b9a; }
        @media (max-width: 600px) { .doc-search { width: min(180px, 45vw); } }
    </style>
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
<script>
    // 模板数据与页面状态
    const jsonData = {{$docData}};
    const config = {{$config}};
    const searchInput = document.getElementById('doc-search-input');
    const searchResults = document.getElementById('doc-search-results');
    const searchIndex = buildSearchIndex(jsonData);
    const content = document.getElementById('content');
    const sideBar = document.getElementById('sideBar');
    let activeDocument = {path: [], apiName: null};
    let activeTryApi = null;
    let activeTryParams = {};
    let tryController = null;
    let tryDialogTrigger = null;
    let tryDraftKey = null;
    const tryDrafts = new Map();
    const tryDialog = document.getElementById('try-dialog');
    const tryForm = document.getElementById('try-form');
    const tryMethod = document.getElementById('try-method');
    const tryResult = document.getElementById('try-result');
    const tryStatus = document.getElementById('try-status');
    const tryHeaders = document.getElementById('try-headers');
    const tryBody = document.getElementById('try-body');
    const tryRun = document.getElementById('try-run');

    document.title = config.projectName;
    const projectTitle = document.getElementById('projectName');
    const introductionHtml = content.innerHTML;
    projectTitle.textContent = config.projectName;
    projectTitle.disabled = !config.hasDescription;


    // 文档首页
    function showIntroduction(updateHash = true) {
        if (!config.hasDescription) return;
        activeDocument = {path: [], apiName: null};
        activeTryApi = null;
        content.innerHTML = introductionHtml;
        if (updateHash) setDocumentHash();
        sideBar.querySelectorAll('a.active').forEach(link => link.classList.remove('active'));
        renderRightMenu();
        window.scrollTo(0, 0);
    }

    // 展示辅助函数
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

    // 接口地址与参数表格
    function requestAddress(api) {
        const address = config.host
            ? config.host.replace(/\/+$/, '') + '/' + api.requestPath.replace(/^\/+/, '')
            : api.requestPath;
        const methods = Array.isArray(api.allowMethod) ? api.allowMethod : [api.allowMethod || 'GET'];
        let link = escapeHtml(address);
        try {
            const url = new URL(address, location.href);
            if (['http:', 'https:'].includes(url.protocol)) {
                link = '<a href="' + escapeHtml(url.href) + '" target="_blank" rel="noopener noreferrer">' + escapeHtml(address) + '</a>';
            }
        } catch (_) {}
        const contentTypes = {
            FORM_DATA: 'multipart/form-data',
            FORM_URLENCODED: 'application/x-www-form-urlencoded',
            JSON: 'application/json',
            XML: 'application/xml',
            RAW: 'RAW'
        };
        const accepted = api.acceptContentType;
        const contentType = accepted && !methods.every(method => method === 'GET') ? '<br>Content-Type: ' + escapeHtml(contentTypes[accepted] || accepted) : '';
        return '<pre>' + escapeHtml(methods.join(', ')) + ' ' + link + contentType + '</pre>';
    }

    function parameterTable(params) {
        const entries = Object.entries(params || {});
        if (!entries.length) return '<p>暂无参数</p>';
        let html = '<table class="parameter-table"><thead><tr><th>名称</th><th>类型</th><th>校验规则</th><th>说明</th><th>默认值</th><th>来源</th></tr></thead><tbody>';
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
                + displayValue(param.defaultValue) + '</td><td>'
                + displayValue(Array.isArray(param.from) ? param.from.join(', ') : param.from) + '</td></tr>';
        }
        return html + '</tbody></table>';
    }

    function examples(items, title) {
        return items && items.length ? items.map((item, index) => '<h4>' + escapeHtml(title) + ' ' + (index + 1) + '</h4><pre><code>' + escapeHtml(item) + '</code></pre>').join('') : '<p>暂无示例</p>';
    }

    // 章节导航
    function renderRightMenu() {
        const menu = document.getElementById('right-menu');
        menu.replaceChildren();
        const headings = content.querySelectorAll('h2, h3');
        menu.style.display = headings.length ? 'block' : 'none';
        headings.forEach((heading, index) => {
            heading.id = 'section-' + index;
            const item = document.createElement('li');
            const link = document.createElement('a');
            link.href = documentHash(heading.textContent, index);
            link.addEventListener('click', event => {
                event.preventDefault();
                setDocumentHash(heading.textContent, index);
                heading.scrollIntoView({block: 'start'});
            });
            link.textContent = heading.textContent;
            item.append(link);
            menu.append(item);
        });
    }


    // 试运行表单
    function buildBodyEditor(bodyType, method) {
        const editor = document.createElement('div');
        editor.className = 'try-body-editor';
        const label = document.createElement('label');
        label.htmlFor = 'try-raw-body';
        label.textContent = bodyType + ' 请求体';
        const input = document.createElement('textarea');
        input.id = 'try-raw-body';
        input.spellcheck = false;
        input.placeholder = bodyType === 'JSON' ? '{"msgId": "123"}' : bodyType === 'XML' ? '<request><msgId>123</msgId></request>' : '填写原始请求体';
        const hint = document.createElement('p');
        hint.className = 'try-hint';
        hint.textContent = ['GET', 'HEAD'].includes(method)
            ? method + ' 无法在浏览器中发送请求体，请将 Api::allowMethod 定义为 POST、PUT 或 PATCH。'
            : '将完整内容作为请求体原样发送。';
        editor.append(label, input, hint);
        return editor;
    }

    function buildParameterField(name, param, method, bodyType) {
        const row = document.createElement('div');
        row.className = 'try-field';
        row.dataset.parameter = 'true';
        row.dataset.name = name;
        row.dataset.type = param.type || '';
        const definedSources = Array.isArray(param.from) ? param.from : (param.from ? [param.from] : ['GET']);
        let sources = [...new Set(definedSources)];
        if (bodyType) {
            sources = sources.filter(source => ['GET', 'HEADER', 'COOKIE'].includes(source));
            if (!sources.length) return null;
        }
        const allowed = sources.filter(source =>
            !['DI', 'CONTEXT'].includes(source) && (!['GET', 'HEAD'].includes(method) || !['POST', 'JSON', 'XML', 'RAW_POST', 'FILE'].includes(source)));
        const required = Object.prototype.hasOwnProperty.call(param.validateRules || {}, 'Required');
        const include = document.createElement('input');
        include.type = 'checkbox';
        include.className = 'try-include';
        include.checked = allowed.length > 0 && (required || param.defaultValue != null);
        include.disabled = !allowed.length;
        const label = document.createElement('label');
        label.append(include, document.createTextNode(' ' + name + (required ? '（必填）' : '') + (param.deprecated ? ' · 已废弃' : '')));
        const source = document.createElement('select');
        source.className = 'try-source';
        for (const from of allowed) source.add(new Option(from, from));
        if (method !== 'GET' && method !== 'HEAD' && allowed.includes('POST')) source.value = 'POST';
        source.disabled = allowed.length <= 1;
        source.setAttribute('aria-label', name + ' 参数来源');
        source.title = allowed.length > 1 ? '选择本次请求的参数来源' : '参数来源';
        const value = document.createElement('input');
        value.className = 'try-value';
        value.setAttribute('aria-label', name + ' 参数值');
        const updateType = () => {
            value.type = param.type === 'FILE' || source.value === 'FILE' ? 'file' : 'text';
            value.placeholder = source.value === 'COOKIE' ? '填写 Cookie ' + name + ' 的值' : source.value === 'HEADER' ? '填写请求头 ' + name + ' 的值' : '填写参数值';
        };
        updateType();
        if (value.type !== 'file' && param.defaultValue != null) {
            value.value = typeof param.defaultValue === 'object' ? JSON.stringify(param.defaultValue)
                : typeof param.defaultValue === 'boolean' ? (param.defaultValue ? '1' : '0') : String(param.defaultValue);
        }
        value.disabled = !allowed.length;
        const includeValue = () => { if (!include.disabled) include.checked = true; };
        value.addEventListener('input', includeValue);
        value.addEventListener('change', includeValue);
        source.addEventListener('change', () => {
            updateType();
            updateHint();
        });
        const hint = document.createElement('div');
        hint.className = 'try-hint';
        const updateHint = () => {
            const sourceHint = {
                GET: 'GET：作为 URL 查询参数发送',
                POST: 'POST：作为表单参数发送',
                HEADER: 'HEADER：作为 HTTP 请求头发送',
                COOKIE: 'COOKIE：运行时设置当前站点 Cookie；需通过 HTTP(S) 打开同源文档，跨域 Cookie 请先在接口站点登录。',
                FILE: 'FILE：选择文件后上传'
            }[source.value] || '';
            hint.textContent = allowed.length
                ? [sourceHint, param.type, param.description,
                    ...Object.values(param.validateRules || {}).map(rule => rule.msg)].filter(Boolean).join(' · ')
                : '该参数由服务器或浏览器管理，或不适用于当前请求方法。';
        };
        updateHint();
        row.append(label, source, value, hint);
        return row;
    }

    function buildTryFields() {
        const method = activeTryApi.allowMethod;
        const fields = document.getElementById('try-fields');
        fields.replaceChildren();
        const bodyType = ['JSON', 'XML', 'RAW'].includes(activeTryApi.acceptContentType) ? activeTryApi.acceptContentType : null;
        if (bodyType) fields.append(buildBodyEditor(bodyType, method));
        if (!bodyType && !Object.keys(activeTryParams).length) {
            const empty = document.createElement('p');
            empty.className = 'try-empty';
            empty.textContent = '此接口无需填写请求参数，可直接运行。';
            fields.append(empty);
        }
        for (const [name, param] of Object.entries(activeTryParams)) {
            const row = buildParameterField(name, param, method, bodyType);
            if (row) fields.append(row);
        }
    }


    function saveTryDraft() {
        if (!tryDraftKey) return;
        const parameters = Object.create(null);
        for (const row of document.getElementById('try-fields').children) {
            if (!row.dataset.parameter) continue;
            const input = row.querySelector('.try-value');
            parameters[row.dataset.name] = {
                source: row.querySelector('.try-source').value,
                included: input.type !== 'file' && row.querySelector('.try-include').checked,
                value: input.type === 'file' ? '' : input.value
            };
        }
        const editor = document.getElementById('try-raw-body');
        const draft = {parameters, body: editor ? editor.value : '',
            url: document.getElementById('try-url').value,
            timeout: document.getElementById('try-timeout').value};
        tryDrafts.set(tryDraftKey, draft);
        try { sessionStorage.setItem(tryDraftKey, JSON.stringify(draft)); } catch (_) {}
    }

    function restoreTryDraft() {
        let draft = tryDrafts.get(tryDraftKey);
        if (!draft) {
            try { draft = JSON.parse(sessionStorage.getItem(tryDraftKey)); } catch (_) {}
        }
        if (!draft || !draft.parameters) return;
        for (const row of document.getElementById('try-fields').children) {
            if (!row.dataset.parameter) continue;
            const saved = draft.parameters[row.dataset.name];
            if (!saved) continue;
            const source = row.querySelector('.try-source');
            if (Array.from(source.children).some(option => option.value === saved.source)) {
                source.value = saved.source;
                source.dispatchEvent(new Event('change'));
            }
            const input = row.querySelector('.try-value');
            if (input.type !== 'file') input.value = saved.value;
            const include = row.querySelector('.try-include');
            include.checked = !include.disabled && input.type !== 'file' && saved.included;
        }
        const editor = document.getElementById('try-raw-body');
        if (editor) editor.value = draft.body || '';
        if (draft.url) document.getElementById('try-url').value = draft.url;
        if (draft.timeout) document.getElementById('try-timeout').value = draft.timeout;
    }

    function openTryDialog(event) {
        const trigger = event.target.closest('#try-open');
        if (!trigger || !activeTryApi) return;
        tryDialogTrigger = trigger;
        tryDraftKey = 'http-annotation:try:' + JSON.stringify([location.href.split('#')[0], config.host, activeDocument.path, activeTryApi.apiName, activeTryApi.allowMethod, activeTryApi.requestPath]);
        const requestPath = config.host
            ? config.host.replace(/\/+$/, '') + '/' + activeTryApi.requestPath.replace(/^\/+/, '')
            : activeTryApi.requestPath;
        try {
            document.getElementById('try-url').value = new URL(requestPath, location.href).href.replace(/%7B/gi, '{').replace(/%7D/gi, '}');
        } catch (_) { document.getElementById('try-url').value = requestPath; }
        tryMethod.value = activeTryApi.allowMethod;
        document.getElementById('try-title').textContent = activeTryApi.apiName + ' · 立即尝试';
        buildTryFields();
        restoreTryDraft();
        tryResult.hidden = true;
        tryDialog.showModal();
        document.getElementById('try-close').focus({preventScroll: true});
    }

    // 请求构建（不访问界面）
    function buildTryRequest(address, method, fields, bodyInput = null) {
        let path = address;
        const headers = new Headers();
        const post = new URLSearchParams();
        const json = Object.create(null);
        const xml = [];
        const files = [];
        const query = [];
        let raw = null;
        const bodyKinds = new Set();
        const toXml = text => String(text).replace(/[&<>"']/g, char => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&apos;'})[char]);
        for (const field of fields) {
            const {name, source, value, type} = field;
            const scalar = value instanceof File ? value : String(value);
            if (source === 'GET') query.push([name, scalar]);
            else if (source === 'HEADER') headers.set(name, scalar);
            else if (source === 'POST') {
                bodyKinds.add('form');
                post.append(name, scalar);
            }
            else if (source === 'FILE') {
                bodyKinds.add('form');
                files.push([name, value]);
            }
            else if (source === 'JSON') {
                bodyKinds.add('json');
                let parsed = value;
                if (['INT', 'DOUBLE', 'REAL', 'FLOAT'].includes(type)) {
                    if (String(value).trim() === '' || !Number.isFinite(Number(value))) throw new Error(name + ' 必须为数字');
                    parsed = Number(value);
                } else if (type === 'BOOLEAN') {
                    if (!['true', 'false', '1', '0'].includes(String(value))) throw new Error(name + ' 请填写 true、false、1 或 0');
                    parsed = value === 'true' || value === '1';
                }
                json[name] = parsed;
            } else if (source === 'XML') {
                if (!/^[A-Za-z_][A-Za-z0-9_.-]*$/.test(name)) throw new Error('无效的 XML 字段名：' + name);
                bodyKinds.add('xml'); xml.push('<' + name + '>' + toXml(value) + '</' + name + '>');
            } else if (source === 'RAW_POST') {
                if (raw !== null) throw new Error('只能发送一个原始请求体');
                bodyKinds.add('raw'); raw = value;
            }
        }
        if (bodyInput) {
            if (bodyKinds.size) throw new Error('完整请求体不能与表单字段混合发送');
            if (['GET', 'HEAD'].includes(method)) throw new Error(method + ' 无法发送请求体，请调整 Api::allowMethod');
            if (bodyInput.type === 'JSON') {
                try { JSON.parse(bodyInput.value); } catch (_) { throw new Error('JSON 格式错误，请填写完整有效的 JSON 数据'); }
            }
            if (bodyInput.type === 'XML') {
                const parsed = new DOMParser().parseFromString(bodyInput.value, 'application/xml');
                if (parsed.querySelector('parsererror')) throw new Error('XML 格式错误，请填写完整有效的 XML 数据');
            }
            const mime = {JSON: 'application/json', XML: 'application/xml', RAW: 'text/plain'}[bodyInput.type];
            if (!mime) throw new Error('不支持的请求体类型');
            headers.set('Content-Type', mime);
        }
        if (bodyKinds.size > 1) throw new Error('JSON、XML、原始请求体和表单不能混合发送，请调整参数来源或取消勾选。');
        const url = new URL(path, location.href);
        if (!['http:', 'https:'].includes(url.protocol)) throw new Error('请填写 HTTP 或 HTTPS 请求地址');
        for (const [name, value] of query) url.searchParams.set(name, value);
        const options = {method, headers, credentials: 'include'};
        if (bodyKinds.size && ['GET', 'HEAD'].includes(method)) throw new Error(method + ' 请求不能发送请求体');
        if (bodyKinds.has('json')) {
            options.body = JSON.stringify(json);
            if (!headers.has('Content-Type')) headers.set('Content-Type', 'application/json');
        }
        if (bodyKinds.has('xml')) {
            options.body = '<request>' + xml.join('') + '</request>';
            if (!headers.has('Content-Type')) headers.set('Content-Type', 'application/xml');
        }
        if (bodyKinds.has('raw')) options.body = raw;
        if (bodyKinds.has('form')) {
            if (files.length) {
                const form = new FormData();
                for (const [name, value] of post) form.append(name, value);
                for (const [name, value] of files) form.append(name, value);
                options.body = form;
                headers.delete('Content-Type');
            } else {
                options.body = post;
                if (!headers.has('Content-Type')) headers.set('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
            }
        }
        if (bodyInput) options.body = bodyInput.value;
        return {url: url.href, options};
    }

    function applyTryCookies(request, fields) {
        const cookies = fields.filter(field => field.source === 'COOKIE');
        if (!cookies.length) return;
        const page = new URL(location.href);
        const target = new URL(request.url);
        if (!['http:', 'https:'].includes(page.protocol) || page.origin !== target.origin) {
            throw new Error('无法设置目标站点 Cookie：请通过与接口同源的 HTTP(S) 地址打开文档，或取消勾选 COOKIE 参数并先在接口站点登录。');
        }
        for (const {name, value} of cookies) {
            if (!/^[!#$%&'*+.^_`|~0-9A-Za-z-]+$/.test(name)) throw new Error('无效的 Cookie 名称：' + name);
            const encoded = encodeURIComponent(String(value));
            document.cookie = name + '=' + encoded + '; Path=/; SameSite=Lax' + (page.protocol === 'https:' ? '; Secure' : '');
            if (!String(document.cookie || '').split(';').some(item => item.trim() === name + '=' + encoded)) {
                throw new Error('Cookie ' + name + ' 设置失败，请检查浏览器 Cookie 策略或已有 HttpOnly Cookie。');
            }
        }
    }

    // 请求执行与异常处理
    async function executeTryRequest(request, timeoutMs, onResponse) {
        const controller = new AbortController();
        tryController = controller;
        let timedOut = false;
        const timer = setTimeout(() => { timedOut = true; controller.abort(); }, timeoutMs);
        try {
            const response = await fetch(request.url, {...request.options, signal: controller.signal});
            onResponse(response);
            const text = await response.text();
            let body = text;
            try { body = JSON.stringify(JSON.parse(text), null, 2); } catch (_) {}
            return {body: body || '（空响应）', error: null};
        } catch (error) {
            return {body: '', error: timedOut ? '请求超时（' + timeoutMs / 1000 + ' 秒）'
                : controller.signal.aborted ? '请求已取消'
                : '网络异常：' + error.message + '。请检查网络、接口地址或跨域配置。'};
        } finally {
            clearTimeout(timer);
            if (tryController === controller) tryController = null;
        }
    }

    function collectTryFields() {
        const fields = [];
        for (const row of document.getElementById('try-fields').children) {
            if (!row.dataset.parameter || !row.querySelector('.try-include').checked) continue;
            const input = row.querySelector('.try-value');
            const source = row.querySelector('.try-source').value;
            if (source === 'FILE' && !input.files.length) throw new Error('请选择文件：' + row.dataset.name);
            fields.push({name: row.dataset.name, type: row.dataset.type, source,
                value: source === 'FILE' ? input.files[0] : input.value});
        }
        return fields;
    }

    // 试运行结果展示
    async function runTryRequest(event) {
        event.preventDefault();
        if (tryController) return;
        tryResult.hidden = false;
        tryStatus.textContent = '正在请求…';
        tryHeaders.textContent = '';
        tryBody.textContent = '';
        tryRun.disabled = true;
        tryRun.textContent = '请求中…';
        let status = '';
        try {
            const fields = collectTryFields();
            const bodyEditor = document.getElementById('try-raw-body');
            const bodyInput = bodyEditor ? {type: activeTryApi.acceptContentType, value: bodyEditor.value} : null;
            const request = buildTryRequest(document.getElementById('try-url').value, activeTryApi.allowMethod, fields, bodyInput);
            if (Object.values(activeTryParams).some(param => (Array.isArray(param.from) ? param.from : [param.from]).includes('COOKIE'))) {
                request.options.credentials = 'include';
            }
            applyTryCookies(request, fields);
            const started = performance.now();
            const result = await executeTryRequest(request, Number(document.getElementById('try-timeout').value) * 1000, response => {
                status = 'HTTP ' + response.status + ' ' + response.statusText;
                tryStatus.textContent = status;
                tryHeaders.textContent = Array.from(response.headers).map(([key, value]) => key + ': ' + value).join('\n') || '（无可读取的响应头）';
            });
            tryStatus.textContent = (status || '未收到 HTTP 响应') + ' · ' + Math.round(performance.now() - started) + ' ms';
            if (result.error) tryStatus.textContent += '\n' + result.error;
            tryBody.textContent = result.body || (result.error ? '未能读取响应内容' : '（空响应）');
        } catch (error) {
            tryStatus.textContent = '请求未发送：' + error.message;
        } finally {
            tryRun.disabled = false;
            tryRun.textContent = '立即运行 →';
        }
    }

    // 分组与接口页面渲染
    function mergeRequestParams(group, api) {
        // 接口参数覆盖同名公共参数，并排除当前接口忽略的参数。
        const params = Object.assign({}, group.onRequestParams, api.requestParams);
        for (const name of Object.keys(params)) {
            if ((params[name].ignoreAction || []).includes(api.apiName)) delete params[name];
        }
        return params;
    }

    function renderGroup(group, path) {
        content.innerHTML = '<h1>' + escapeHtml(path.join('.')) + '</h1>'
            + (group.descriptionHtml || description(group.description))
            + (Object.keys(group.onRequestParams || {}).length
                ? '<h3>公共请求参数</h3>' + parameterTable(group.onRequestParams)
                : '');
    }

    function renderApi(api, params) {
        content.innerHTML = '<div class="api-heading"><h1 class="api-title">' + escapeHtml(api.apiName)
            + (api.deprecated === true ? '<span class="api-deprecated-label">已废弃</span>' : '') + '</h1><button type="button" class="try-button" id="try-open">立即尝试</button></div>'
            + '<h3>请求地址</h3>' + requestAddress(api)
            + '<h3>接口说明</h3>' + (api.descriptionHtml || description(api.description))
            + '<h3>请求参数</h3>' + parameterTable(params)
            + '<h3>请求示例</h3>' + examples(api.requestExamples, '请求示例')
            + '<h3>成功响应示例</h3>' + examples(api.responseExamples.success, '成功响应示例')
            + '<h3>失败响应示例</h3>' + examples(api.responseExamples.fail, '失败响应示例');
    }

    function handleMenuClick(event) {
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
            renderGroup(group, path);
            activeTryApi = null;
        } else {
            const api = group.apiList[target.dataset.api];
            if (!api) return;
            sideBar.querySelectorAll('a.active').forEach(link => link.classList.remove('active'));
            target.classList.add('active');
            const params = mergeRequestParams(group, api);
            activeTryApi = api;
            activeTryParams = params;
            renderApi(api, params);
        }
        activeDocument = {path, apiName: target.matches('button') ? null : target.dataset.api};
        setDocumentHash();
        renderRightMenu();
        window.scrollTo(0, 0);
    }
    function closeTryDialog() {
        if (!tryDialog.open) return;
        if (tryController) tryController.abort();
        tryDialog.close();
    }

    function handleTryEscape(event) {
        const isEscape = event.key === 'Escape' || event.key === 'Esc'
            || event.code === 'Escape' || event.keyCode === 27;
        if (!tryDialog.open || !isEscape) return;
        event.preventDefault();
        event.stopPropagation();
        closeTryDialog();
    }

    // 全局搜索：索引递归分组，保留完整路径以区分同名接口。
    function buildSearchIndex(map, path = []) {
        const entries = [];
        for (const [name, group] of Object.entries(map)) {
            const groupPath = [...path, name];
            const children = buildSearchIndex(group.children || {}, groupPath);
            if (Object.keys(group.apiList || {}).length || children.length) {
                entries.push({path: groupPath, apiName: null, title: groupPath.join('.'), requestPath: ''});
            }
            for (const [apiName, api] of Object.entries(group.apiList || {})) {
                entries.push({path: groupPath, apiName, title: groupPath.join('.') + ' / ' + apiName,
                    requestPath: api.requestPath || '', description: api.description || ''});
            }
            entries.push(...children);
        }
        return entries;
    }

    function searchDocuments(query, entries = searchIndex) {
        const words = query.trim().toLowerCase().split(/\s+/).filter(Boolean);
        if (!words.length) return [];
        return entries.filter(entry => words.every(word =>
            (entry.title + ' ' + entry.requestPath + ' ' + (entry.description || '')).toLowerCase().includes(word))).slice(0, 20);
    }

    function highlightSearchText(text, query) {
        const words = query.trim().split(/\s+/).filter(Boolean);
        if (!words.length) return escapeHtml(text);
        const pattern = words.map(word => word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|');
        const expression = new RegExp(pattern, 'gi');
        let html = '';
        let offset = 0;
        for (const match of text.matchAll(expression)) {
            html += escapeHtml(text.slice(offset, match.index)) + '<mark>' + escapeHtml(match[0]) + '</mark>';
            offset = match.index + match[0].length;
        }
        return html + escapeHtml(text.slice(offset));
    }

    function buildSearchContext(entry, query) {
        const text = String(entry.description || '').replace(/\s+/g, ' ').trim();
        if (!text) return '';
        const lowerText = text.toLowerCase();
        const positions = query.trim().toLowerCase().split(/\s+/).filter(Boolean)
            .map(word => ({index: lowerText.indexOf(word), length: word.length}))
            .filter(match => match.index >= 0).sort((left, right) => left.index - right.index);
        // 展示命中处前后各 35 个字符，合并临近命中，最多三段。
        const ranges = [];
        for (const match of positions) {
            const start = Math.max(0, match.index - 35);
            const end = Math.min(text.length, match.index + match.length + 35);
            const previous = ranges[ranges.length - 1];
            if (previous && start <= previous.end) previous.end = Math.max(previous.end, end);
            else ranges.push({start, end});
        }
        if (!ranges.length) ranges.push({start: 0, end: Math.min(text.length, 90)});
        return ranges.slice(0, 3).map(range =>
            (range.start ? '…' : '') + highlightSearchText(text.slice(range.start, range.end), query)
            + (range.end < text.length ? '…' : '')).join(' · ');
    }

    function renderSearchResults() {
        const matches = searchDocuments(searchInput.value);
        searchResults.replaceChildren();
        searchResults.hidden = !searchInput.value.trim();
        searchInput.setAttribute('aria-expanded', String(!searchResults.hidden));
        if (searchResults.hidden) return;
        if (!matches.length) {
            const empty = document.createElement('p');
            empty.className = 'search-empty';
            empty.textContent = '未找到匹配的分组或接口';
            searchResults.append(empty);
        }
        for (const entry of matches) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'search-result';
            const title = document.createElement('span');
            title.innerHTML = highlightSearchText(entry.title, searchInput.value);
            const subtitle = document.createElement('small');
            subtitle.innerHTML = highlightSearchText(entry.apiName === null ? '接口分组' : entry.requestPath, searchInput.value);
            button.append(title, subtitle);
            const snippet = buildSearchContext(entry, searchInput.value);
            if (snippet) {
                const context = document.createElement('small');
                context.className = 'search-context';
                context.innerHTML = snippet;
                button.append(context);
            }
            button.addEventListener('click', () => openSearchResult(entry));
            searchResults.append(button);
        }
    }

    function closeSearchResults() {
        searchResults.hidden = true;
        searchInput.setAttribute('aria-expanded', 'false');
    }

    function openSearchResult(entry, updateHash = true) {
        const group = findGroup(entry.path);
        if (!group) return;
        sideBar.querySelectorAll('a.active').forEach(link => link.classList.remove('active'));
        // 展开搜索结果所在的完整菜单层级。
        for (const button of sideBar.querySelectorAll('button[data-path]')) {
            const path = JSON.parse(button.dataset.path);
            if (path.length <= entry.path.length && path.every((name, index) => name === entry.path[index])) {
                button.nextElementSibling.hidden = false;
                button.setAttribute('aria-expanded', 'true');
                button.querySelector('.menu-arrow').textContent = '▾';
            }
        }
        if (entry.apiName === null) {
            renderGroup(group, entry.path);
        } else {
            const api = group.apiList[entry.apiName];
            if (!api) return;
            activeTryApi = api;
            activeTryParams = mergeRequestParams(group, api);
            renderApi(api, activeTryParams);
            for (const link of sideBar.querySelectorAll('a[data-api]')) {
                if (link.dataset.api === entry.apiName && link.dataset.path === JSON.stringify(entry.path)) {
                    link.classList.add('active');
                }
            }
        }
        activeDocument = {path: entry.path, apiName: entry.apiName};
        if (entry.apiName === null) activeTryApi = null;
        if (updateHash) setDocumentHash();
        closeSearchResults();
        renderRightMenu();
        window.scrollTo(0, 0);
    }

    // 哈希链接同时保存菜单与章节，刷新和历史导航可恢复页面。
    function documentHash(section = '', index = null) {
        const hash = new URLSearchParams();
        if (activeDocument.path.length) hash.set('group', activeDocument.path.join('.'));
        if (activeDocument.apiName !== null) hash.set('api', activeDocument.apiName);
        if (section) hash.set('section', section);
        if (index !== null) hash.set('heading', String(index));
        return '#' + hash.toString();
    }

    function setDocumentHash(section = '', index = null) {
        const hash = documentHash(section, index);
        if (location.hash !== hash) history.pushState(null, '', hash);
    }

    function restoreDocumentHash() {
        const raw = (location.hash || '').slice(1);
        const hash = new URLSearchParams(raw);
        const groupName = hash.get('group');
        if (groupName) {
            const path = groupName.split('.');
            const group = findGroup(path);
            const apiName = hash.has('api') ? hash.get('api') : null;
            if (!group || (apiName !== null && !Object.prototype.hasOwnProperty.call(group.apiList, apiName))) return;
            openSearchResult({path, apiName}, false);
        } else if (!raw || hash.has('section')) {
            showIntroduction(false);
        } else {
            // 兼容现有的 #section-0 章节链接。
            if (!/^section-\d+$/.test(raw)) return;
        }
        const headings = Array.from(content.querySelectorAll('h2, h3'));
        const section = hash.get('section');
        const indexed = hash.has('heading') ? headings[Number(hash.get('heading'))] : null;
        const heading = section
            ? (indexed && indexed.textContent === section ? indexed : headings.find(item => item.textContent === section))
            : headings.find(item => item.id === raw);
        if (heading) heading.scrollIntoView({block: 'start'});
    }

    // 初始化与事件绑定
    searchInput.addEventListener('input', renderSearchResults);
    searchInput.addEventListener('focus', renderSearchResults);
    searchInput.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeSearchResults();
        if (event.key === 'Enter') {
            event.preventDefault();
            const first = searchDocuments(searchInput.value)[0];
            if (first) openSearchResult(first);
        }
        if (event.key === 'ArrowDown' && !searchResults.hidden) {
            const first = searchResults.querySelector('button');
            if (first) { event.preventDefault(); first.focus(); }
        }
    });
    document.addEventListener('click', event => {
        if (!event.target.closest('#doc-search')) closeSearchResults();
    }, true);
    projectTitle.addEventListener('click', () => showIntroduction());
    window.addEventListener('hashchange', restoreDocumentHash, true);
    window.addEventListener('popstate', restoreDocumentHash, true);
    for (const link of sideBar.querySelectorAll('a[data-api]')) {
        const hash = new URLSearchParams({group: JSON.parse(link.dataset.path).join('.'), api: link.dataset.api});
        link.href = '#' + hash.toString();
    }
    content.addEventListener('click', openTryDialog);
    tryForm.addEventListener('submit', runTryRequest);
    tryForm.addEventListener('input', saveTryDraft);
    tryForm.addEventListener('change', saveTryDraft);
    sideBar.addEventListener('click', handleMenuClick);
    document.getElementById('try-close').addEventListener('click', closeTryDialog);
    // 从 window 捕获；keyup 兼容输入控件先消耗 keydown 的情况。
    window.addEventListener('keydown', handleTryEscape, true);
    window.addEventListener('keyup', handleTryEscape, true);
    tryDialog.addEventListener('cancel', event => {
        event.preventDefault();
        closeTryDialog();
    });
    tryDialog.addEventListener('close', () => {
        saveTryDraft();
        if (tryController) tryController.abort();
        if (tryDialogTrigger && tryDialogTrigger.isConnected) {
            tryDialogTrigger.focus({preventScroll: true});
        }
        tryDialogTrigger = null;
    });
    renderRightMenu();
    restoreDocumentHash();
</script>
</body>
</html>
