<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="description" content="Description"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>

        .container .navBar {
            position: fixed;
            z-index: 20;
            top: 0;
            left: 0;
            right: 0;
            height: 3.6rem;
            background-color: #fff;
            box-sizing: border-box;
            border-bottom: 1px solid #eaecef;
            padding: 0.7rem 1.5rem;
            line-height: 2.2rem;
            display: block;
        }
        .container .navBar .navInner img {
            height: 2.2rem;
            min-width: 2.2rem;
            margin-right: 0.8rem;
            vertical-align: top;
        }


        .container .mainContent {
            padding-left: 15rem;
            padding-bottom: 2rem;
            display: block;
            padding-top: 4rem;
        }
        .container .mainContent .content {
            max-width: 740px;
            margin: 0 auto;
            padding: 2rem 2.5rem;
        }

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

        .right-menu{
            width: 230px;
            position: fixed;
            right: 15px;
            top: 120px;
            min-height: 1px;
            z-index: 99;
            border: 1px solid #EEEEEE;
            border-radius: 0 3px 3px 3px;
            background-color: #fff;
            padding: 10px;
            max-height: 70%;
            overflow-y: auto;
        }
        .right-menu::-webkit-scrollbar{
            display:none;
        }
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

    </style>
</head>
<body>
<div class="container">
    <header class="navBar">
        <div class="navInner">
            <a href="">
                <img src="http://www.easyswoole.com/Images/docNavLogo.png">
            </a>
        </div>
    </header>

    <aside class="sideBar" id="sideBar">
    </aside>
    <section class="mainContent">
        <div class="content" id="content">
        </div>
        <div class="right-menu" id="right-menu" style="display: none"></div>
    </section>
</div>
<script>
    var jsonData = {{$docData}}

    var config = {{$config}}

    document.title = config.projectName

    function parseDesc(desc,parseToHtml = true){
        if(desc){
            if(parseToHtml){
                if(desc.type === "MARKDOWN" || desc.type === 'MARKDOWN_FILE'){
                    return marked.parse(desc.desc);
                }
                return "<pre><xmp>"+desc.desc+"</xmp></pre>"
            }
            return desc.desc
        }else{
            return 'Not Any Description'
        }
    }

    function buildApiContent(api){
        var html = "";
        html += '<h1>'+api.apiName+'</h1>'
        html += '<h3><strong>Request Path</strong> </h3>'
        html += '<pre>'+api.allowMethod+": "+ config.host + api.requestPath+'</pre>'
        html += '<h3><strong>Api Description</strong></h3>'
        html += parseDesc(api.description)

        html += '<h3><strong>Request Params</strong></h3>'

        if(api.requestParam instanceof Array){
            html += '<p>Empty Request Params</p>'
        }else{
          html += buildRequestParamTable(api.requestParam)
        }

        html += '<h3><strong>Request Example</strong></h3>'

        if(api.requestExamples.length > 0){
            for(var i in api.requestExamples){
                var example = api.requestExamples[i]
                html += "<h5>Request Example "+(parseInt(i)  + 1)+"</h5>"

                if(example.exampleType === 'PARAM_ARRAY'){
                    html += buildResponseParamTable(example.example)
                }else{
                    html += parseDesc(example.example)
                }
            }
        }else{
            html += '<p>Empty Request Example</p>'
        }



        html += '<h3><strong>Response Params</strong></h3>'

        if(api.responseParam instanceof Array){
            html += '<p>Empty Response Params</p>'
        }else{
            html += buildResponseParamTable(api.responseParam)
        }



        html += '<h3><strong>Response Example</strong></h3>'
        if(api.responseExamples.length > 0){
            for(var i in api.responseExamples){
                var example = api.responseExamples[i]
                html += "<h5>Response Example "+(parseInt(i)  + 1)+"</h5>"

                if(example.exampleType === 'PARAM_ARRAY'){
                    html += buildResponseParamTable(example.example)
                }else{
                    html += parseDesc(example.example)
                }
            }
        }else{
            html += '<p>Empty Response Example</p>'
        }

        $("#content").html(html)
    }

    function buildGroupDesc(apiGroup){
        var html = "";
        html += '<h2 >'+apiGroup.groupName+'</h2>'
        html += parseDesc(apiGroup.description)
        $("#content").html(html)
    }

    function buildRequestParamTable(params){

        var hanlder = function (param,subCount,paramFrom){
            var name = "&nbsp;&nbsp;&nbsp;&nbsp;".repeat(subCount)+param.name

            var fromStr = param.from.join(',')
            var rules = ''
            var ruleCount = 1
            for(var ruleName in param.validate){
                rules += "<p>"+ruleCount+"."+param.validate[ruleName]+"</p>"
                ruleCount++
            }

            var desc = parseDesc(param.description,false)
            var defaultVal = '-';
            if(param.value != null){
                defaultVal = param.value
            }

            var next = '';
            subCount++;

            for (var sub in param.subObject){
                next += hanlder(param.subObject[sub],subCount,param.from)
            }

            return "<tr><td>"+name+"</td> <td>"+fromStr+"</td> <td>"+rules+"</td> <td>"+desc+"</td> <td>"+defaultVal+"</td></tr>"+next;
        }
        var final = '';
        for (var i in params){
            final += hanlder(params[i],0)
        }

        return "<table> <tr> <td>Name</td> <td>From</td> <td>Validate</td> <td>Description</td> <td>Default Value</td> </tr>"+final+"</table>";
    }

    function buildResponseParamTable(params){

        var hanlder = function (param,subCount,paramFrom = null){
            var name = "&nbsp;&nbsp;&nbsp;&nbsp;".repeat(subCount)+param.name
            if(param.type === "LIST"){
                name += "[0-N]"
            }
            var desc = parseDesc(param.description,false)
            if(param.type){
                var type = param.type
            }else{
                var type = '-'
            }

            var next = '';
            subCount++;

            var defaultVal = '-';
            if(param.value != null){
                defaultVal = param.value
            }

            for (var sub in param.subObject){
                next += hanlder(param.subObject[sub],subCount,param.from)
            }

            return "<tr><td>"+name+"</td> <td>"+desc+"</td> <td>"+type+"</td><td>"+defaultVal+"</td></tr>"+next;
        }
        var final = '';
        for (var i in params){
            final += hanlder(params[i],0)
        }

        return "<table> <tr> <td>Name</td><td>Description</td> <td>Type</td><td>Default Value</td> </tr>"+final+"</table>";
    }

    function renderRightMenu()
    {
        var rightMenu = [];
        $(".content").children().each(function(index, element) {
            var tagName=$(this).get(0).tagName;
            if(tagName.substr(0,1).toUpperCase()=="H"){
                var contentH=$(this).text();//获取内容
                var markid="mark-"+tagName+"-"+index.toString();
                $(this).attr("id",contentH);//为当前h标签设置id
                var level = tagName.substr(1,2);
                rightMenu.push({
                    level: level,
                    content: contentH,
                    markid: markid,
                });
            }
        });
        $('.right-menu').empty();

        if(rightMenu.length === 0){
            $('#right-menu').hide()
            return
        }else{
            $('#right-menu').show()
        }

        $('.right-menu').append("<div class='title'><i class='fa fa-list'></i> 本章导航</div>");
        $.each(rightMenu, function (index, item) {
            var padding_left = (item.level - 1) * 12 +"px";
            $('.right-menu').append("<li style='padding-left:"+padding_left+"'><a href='#"+item.content+"' class='right-menu-item'>"+item.content+"</a></li>");
        });
        // 防止点击的导航是最底部，拉取滑动的只会到倒数其他菜单
        $('.right-menu').on('click','a',function(){
            // 延迟执行 等滚动完
            var that = $(this);
            setTimeout(function (that) {
                $(".right-menu-item.active").removeClass("active");
                that.addClass("active");
            }, 50, that);
        });
        // 切换导航显示
        $('.right-menu .title').on('click', function(){
            $(this).siblings().toggle();
        });
    }

    $(function (){
        if(config.description){
            $("#content").html(parseDesc(config.description))
        }else{
            $("#content").html("<h1>"+config.projectName+"</h1>")
        }
        var sideBarHtml = "";
        for(var groupName in jsonData){
            var groupApi = jsonData[groupName]
            //说明不是空
            if(!(groupApi.apiList instanceof Array)){
                sideBarHtml = sideBarHtml + "<li groupName='"+groupName+"'><i></i>"+groupName+"<ul>";
                for(var apiName in groupApi.apiList){
                    sideBarHtml = sideBarHtml + "<li><a groupName='"+groupName+"' apiName='"+apiName+"'>"+apiName+"</a></li>"
                }
                sideBarHtml = sideBarHtml + "</ul></li>";
            }
        }
        $("#sideBar").html("<ul>"+sideBarHtml+"</ul>")
        $.each($('.sideBar li:has(li)'), function () {
            $(this).attr('isOpen', 0)
            $(this).find('i').addClass('arrow-right');
        });

        $('.sideBar li:has(ul)').click(function (event) {
            if (this == event.target) {
                $(this).children().toggle('fast');
                if ($(this).attr('isOpen') == 1) {
                    $(this).attr('isOpen', 0);
                    $(this).find('i').removeClass('arrow-down');
                    $(this).find('i').addClass('arrow-right');
                } else {
                    $(this).attr('isOpen', 1);
                    $(this).find('i').removeClass('arrow-right');
                    $(this).find('i').addClass('arrow-down');
                }
                $(this).find('i').css({
                    "display":"inline"
                })

                var groupName = $(this).attr('groupName');
                var groupApi = jsonData[groupName]
                buildGroupDesc(groupApi)
                //隐藏章节导航
                $('.right-menu').hide()
            }
        });
        $('.sideBar ul li a').on('click', function () {
            $.each($('.sideBar ul li a'), function () {
                $(this).filter("a").css("text-decoration", "none").css('color','#2c3e50');
            });
            $(this).filter("a").css("text-decoration", "underline").css('color','#0080ff');
            var groupName = $(this).attr('groupName');
            var apiName = $(this).attr('apiName');
            var groupApi = jsonData[groupName]
            buildApiContent(groupApi.apiList[apiName])
            renderRightMenu()
            $('.right-menu').show()
        })
    });
</script>
</body>
</html>