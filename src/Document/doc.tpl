<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>{{$projectName}}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="description" content="Description"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.3.0/css/all.min.css" rel="stylesheet">
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
            line-height: 1.7;
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
            padding: 0.3em 0.3em;
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

        body{
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
            font: normal normal normal 14px/1 FontAwesome;
            line-height: 1;
            font-size: inherit;
            text-rendering: auto;
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
        <div class="content">
            <p>测试内容</p>
        </div>
    </section>
</div>
<script>
    var jsonData = {{$docData}}
    // var decodedData = window.atob(jsonBase64Data);
    // jsonBase64Data = JSON.parse(jsonBase64Data)
    $(function (){
        var sideBarHtml = "";
        for(var groupName in jsonData){
            var groupApis = jsonData[groupName]
            //说明不是空
            if(!(groupApis.apiList instanceof Array)){
                sideBarHtml = sideBarHtml + "<li>"+groupName+"<ul>";
                for(var apiName in groupApis.apiList){
                    sideBarHtml = sideBarHtml + "<li><a groupName='"+groupName+"' apiName='"+apiName+"'>"+apiName+"</a></li>"
                }
                sideBarHtml = sideBarHtml + "</ul></li>";

                console.log(sideBarHtml)
            }
        }
        $("#sideBar").html("<ul>"+sideBarHtml+"</ul>")
        $.each($('.sideBar li:has(li)'), function () {
            $(this).attr('isOpen', 0).addClass('fa fa-angle-right');
        });

        $('.sideBar li:has(ul)').click(function (event) {
            if (this == event.target) {
                $(this).children().toggle('fast');
                if ($(this).attr('isOpen') == 1) {
                    $(this).attr('isOpen', 0);
                    $(this).removeClass('fa fa-angle-down');
                    $(this).addClass('fa fa-angle-right');
                } else {
                    $(this).attr('isOpen', 1);
                    $(this).removeClass('fa fa-angle-right');
                    $(this).addClass('fa fa-angle-down');
                }
            }
        });
        $('.sideBar ul li a').on('click', function () {
            $.each($('.sideBar ul li a'), function () {
                $(this).filter("a").css("text-decoration", "none").css('color','#2c3e50');
            });
            $(this).filter("a").css("text-decoration", "underline").css('color','#0080ff');
            var groupName = $(this).attr('groupName');
            var apiName = $(this).attr('apiName');
        })

    });
</script>
</body>
</html>