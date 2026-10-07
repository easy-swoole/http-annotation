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
                    <button type="button" id="projectName" aria-label="展示全局文档说明"></button>
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
<dialog id="try-dialog" aria-labelledby="try-title">
    <div class="try-header"><div><span class="try-eyebrow">API EXPLORER</span><h2 id="try-title">立即尝试</h2></div><button type="button" class="try-close" id="try-close" aria-label="关闭">×</button></div>
    <form id="try-form">
        <div class="try-request-line">
            <label class="try-method-label">请求方法 <input id="try-method" readonly tabindex="-1"></label>
            <label class="try-address-label">请求地址 <input id="try-url" type="url" required></label>
        </div>
        <div class="try-section-heading"><h3>请求参数</h3><span>勾选要发送的参数</span></div>
        <div id="try-fields"></div>
        <p class="try-hint">Cookie 由浏览器管理；跨域请求需要接口允许当前文档来源。</p>
        <div class="try-actions">
            <label>超时（秒） <input id="try-timeout" type="number" min="1" max="300" value="30" required></label>
            <button class="try-button" id="try-run" type="submit">立即运行 →</button>
        </div>
    </form>
    <section id="try-result" aria-live="polite" hidden>
        <h3>运行结果</h3><p id="try-status"></p>
        <details class="try-response-headers"><summary>响应头</summary><pre id="try-headers"></pre></details>
        <h4>响应内容</h4><pre id="try-body"></pre>
    </section>
</dialog>
<script>
    const jsonData = {{$docData}};
    const config = {{$config}};
    const content = document.getElementById('content');
    const sideBar = document.getElementById('sideBar');
    document.title = config.projectName;
    const projectTitle = document.getElementById('projectName');
    const introductionHtml = content.innerHTML;
    projectTitle.textContent = config.projectName;
    projectTitle.disabled = !config.hasDescription;
    projectTitle.addEventListener('click', () => {
        if (!config.hasDescription) return;
        content.innerHTML = introductionHtml;
        sideBar.querySelectorAll('a.active').forEach(link => link.classList.remove('active'));
        renderRightMenu();
        window.scrollTo(0, 0);
    });

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

    let activeTryApi = null;
    let activeTryParams = {};
    let tryController = null;
    const tryDialog = document.getElementById('try-dialog');
    const tryForm = document.getElementById('try-form');
    const tryMethod = document.getElementById('try-method');
    const tryResult = document.getElementById('try-result');
    const tryStatus = document.getElementById('try-status');
    const tryHeaders = document.getElementById('try-headers');
    const tryBody = document.getElementById('try-body');
    const tryRun = document.getElementById('try-run');

    function buildTryFields() {
        const method = activeTryApi.allowMethod;
        const fields = document.getElementById('try-fields');
        fields.replaceChildren();
        const bodyType = ['JSON', 'XML', 'RAW'].includes(activeTryApi.acceptContentType) ? activeTryApi.acceptContentType : null;
        if (bodyType) {
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
            fields.append(editor);
        }
        if (!bodyType && !Object.keys(activeTryParams).length) {
            const empty = document.createElement('p');
            empty.className = 'try-empty';
            empty.textContent = '此接口无需填写请求参数，可直接运行。';
            fields.append(empty);
        }
        for (const [name, param] of Object.entries(activeTryParams)) {
            const row = document.createElement('div');
            row.className = 'try-field';
            row.dataset.parameter = 'true';
            row.dataset.name = name;
            row.dataset.type = param.type || '';
            let sources = param.type === 'FILE' ? ['FILE'] : (param.from || ['GET', 'POST']);
            if (bodyType) {
                sources = sources.filter(source => ['GET', 'HEADER', 'ROUTER_PARAMS'].includes(source));
                if (!sources.length) continue;
            }
            const allowed = sources.filter(source =>
                !['DI', 'CONTEXT', 'COOKIE'].includes(source) && (!['GET', 'HEAD'].includes(method) || !['POST', 'JSON', 'XML', 'RAW_POST', 'FILE'].includes(source)));
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
            source.disabled = !allowed.length;
            source.setAttribute('aria-label', name + ' 参数来源');
            const value = document.createElement('input');
            value.className = 'try-value';
            value.setAttribute('aria-label', name + ' 参数值');
            const updateType = () => {
                value.type = param.type === 'FILE' || source.value === 'FILE' ? 'file' : 'text';
                value.placeholder = source.value === 'HEADER' ? '填写请求头 ' + name + ' 的值' : '填写参数值';
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
            source.addEventListener('change', updateType);
            const hint = document.createElement('div');
            hint.className = 'try-hint';
            hint.textContent = allowed.length ? [allowed.includes('HEADER') ? 'HEADER：填写后作为 HTTP 请求头发送' : '', param.type, param.description,
                ...Object.values(param.validateRules || {}).map(rule => rule.msg)].filter(Boolean).join(' · ')
                : '该参数由服务器或浏览器管理，或不适用于当前请求方法。';
            row.append(label, source, value, hint);
            fields.append(row);
        }
    }

    content.addEventListener('click', event => {
        if (!event.target.closest('#try-open') || !activeTryApi) return;
        const requestPath = config.host
            ? config.host.replace(/\/+$/, '') + '/' + activeTryApi.requestPath.replace(/^\/+/, '')
            : activeTryApi.requestPath;
        try {
            document.getElementById('try-url').value = new URL(requestPath, location.href).href.replace(/%7B/gi, '{').replace(/%7D/gi, '}');
        } catch (_) { document.getElementById('try-url').value = requestPath; }
        tryMethod.value = activeTryApi.allowMethod;
        document.getElementById('try-title').textContent = activeTryApi.apiName + ' · 立即尝试';
        buildTryFields();
        tryResult.hidden = true;
        tryDialog.showModal();
    });
    document.getElementById('try-close').addEventListener('click', () => tryDialog.close());
    tryDialog.addEventListener('close', () => { if (tryController) tryController.abort(); });

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
            if (source === 'ROUTER_PARAMS') {
                const pattern = new RegExp('\\{' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '(?::[^}]+)?\\}', 'g');
                if (!pattern.test(path)) throw new Error('地址中未找到路由参数：' + name);
                pattern.lastIndex = 0;
                path = path.replace(pattern, encodeURIComponent(scalar));
            } else if (source === 'GET') query.push([name, scalar]);
            else if (source === 'HEADER') headers.set(name, scalar);
            else if (source === 'POST') { bodyKinds.add('form'); post.append(name, scalar); }
            else if (source === 'FILE') { bodyKinds.add('form'); files.push([name, value]); }
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
        if (bodyKinds.has('json')) { options.body = JSON.stringify(json); if (!headers.has('Content-Type')) headers.set('Content-Type', 'application/json'); }
        if (bodyKinds.has('xml')) { options.body = '<request>' + xml.join('') + '</request>'; if (!headers.has('Content-Type')) headers.set('Content-Type', 'application/xml'); }
        if (bodyKinds.has('raw')) options.body = raw;
        if (bodyKinds.has('form')) {
            if (files.length) {
                const form = new FormData();
                for (const [name, value] of post) form.append(name, value);
                for (const [name, value] of files) form.append(name, value);
                options.body = form;
                headers.delete('Content-Type');
            } else { options.body = post; if (!headers.has('Content-Type')) headers.set('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8'); }
        }
        if (bodyInput) options.body = bodyInput.value;
        return {url: url.href, options};
    }

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

    tryForm.addEventListener('submit', async event => {
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
            const fields = [];
            for (const row of document.getElementById('try-fields').children) {
                if (!row.dataset.parameter || !row.querySelector('.try-include').checked) continue;
                const input = row.querySelector('.try-value');
                const source = row.querySelector('.try-source').value;
                if (source === 'FILE' && !input.files.length) throw new Error('请选择文件：' + row.dataset.name);
                fields.push({name: row.dataset.name, type: row.dataset.type, source,
                    value: source === 'FILE' ? input.files[0] : input.value});
            }
            const bodyEditor = document.getElementById('try-raw-body');
            const bodyInput = bodyEditor ? {type: activeTryApi.acceptContentType, value: bodyEditor.value} : null;
            const request = buildTryRequest(document.getElementById('try-url').value, activeTryApi.allowMethod, fields, bodyInput);
            const started = performance.now();
            const result = await executeTryRequest(request, Number(document.getElementById('try-timeout').value) * 1000, response => {
                status = 'HTTP ' + response.status + ' ' + response.statusText;
                tryStatus.textContent = status;
                tryHeaders.textContent = Array.from(response.headers).map(([key, value]) => key + ': ' + value).join('\n') || '（无可读取的响应头）';
            });
            tryStatus.textContent = (status || '未收到 HTTP 响应') + ' · ' + Math.round(performance.now() - started) + ' ms';
            if (result.error) tryStatus.textContent += '\n' + result.error;
            tryBody.textContent = result.body || (result.error ? '未能读取响应内容' : '（空响应）');
        } catch (error) { tryStatus.textContent = '请求未发送：' + error.message; }
        finally { tryRun.disabled = false; tryRun.textContent = '立即运行 →'; }
    });

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
                + (group.descriptionHtml || description(group.description))
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
            activeTryApi = api;
            activeTryParams = params;
            content.innerHTML = '<div class="api-heading"><h1 class="api-title">' + escapeHtml(api.apiName)
                + (api.deprecated === true ? '<span class="api-deprecated-label">已废弃</span>' : '') + '</h1><button type="button" class="try-button" id="try-open">立即尝试</button></div>'
                + '<h3>请求地址</h3>' + requestAddress(api)
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
