<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>EasySwoole文档</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="description" content="Description" />
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/docsify-themeable@0/dist/css/theme-simple.css" />
</head>

<body>
<div id="app"></div>
<div id="toc" style="position: fixed; top: 0; right: 0;"></div>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
    window.$docsify = {
        auto2top: true,
        maxLevel: 2,
        subMaxLevel: 2,
        search: {
            paths: ["README"],
            depth: 6,
            noData: "没有搜索到结果！",
            placeholder: "搜索...",
        },
    };

</script>
<script>
    window.onload = function () {
        setTimeout(function () {
            // var rendererMD = new marked.Renderer();
            // marked.setOptions({
            //   renderer: rendererMD,
            //   gfm: true,
            //   tables: true,
            //   breaks: false,
            //   pedantic: false,
            //   sanitize: false,
            //   smartLists: true,
            //   smartypants: false
            // });
            var html = document.getElementById('rawMd').innerHTML
            document.getElementById('main').innerHTML = marked(html)
        }, 500)
        setTimeout(function () {
            new Toc('main', {
                'level': 2,
                'top': 200,
                'class': 'toc',
                'targetId': 'toc'
            });
            $(function () {
                $(window).scroll(function () {
                    var lenToc = document.getElementById('tocnav').className
                    var wst = $(window).scrollTop()
                    for (i = 1; i < lenToc; i++) {
                        var lenTop = Math.floor($("#tip" + i).offset().top)
                        if (lenTop <= wst) {
                            $('#tocnav li').removeClass("active");
                            $("#tip" + i + i).addClass("active");
                        }
                    }
                })
            });
        }, 1000)
    }
</script>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" type="text/javascript" charset="utf-8"></script>
<script src="//cdn.jsdelivr.net/npm/docsify/lib/docsify.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/docsify-themeable@0"></script>
<script src="//cdn.jsdelivr.net/npm/docsify/lib/plugins/search.min.js"></script>
<script src="//unpkg.com/prismjs/components/prism-json.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/prismjs/components/prism-bash.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/prismjs/components/prism-php.min.js"></script>
<script type="text/plain" id="rawMd">
{$rawMd}
  </script>
<script>
    (function (window) {
        function Toc(id, options) {
            this.el = document.getElementById(id);
            if (!this.el) return;
            this.options = options || {};
            this.tocLevel = parseInt(options.level) || 0;
            this.tocClass = options['class'] || 'toc';
            this.tocTop = parseInt(options.top) || 0;
            this.elChilds = this.el.children;
            if (!this.elChilds.length) return;
            this._init();
        }

        Toc.prototype._init = function () {
            this._collectTitleElements();
            this._createTocContent();
            this._showToc();
        };


        Toc.prototype._collectTitleElements = function () {
            this._elTitlesNames = [],
                this.elTitleElements = [];
            for (var i = 1; i < 7; i++) {
                if (this.el.getElementsByTagName('h' + i).length) {
                    this._elTitlesNames.push('h' + i);
                }
            }

            this._elTitlesNames.length = this._elTitlesNames.length > this.tocLevel ? this.tocLevel : this._elTitlesNames.length;

            for (var j = 0; j < this.elChilds.length; j++) {
                this._elChildName = this.elChilds[j].tagName.toLowerCase();
                if (this._elTitlesNames.toString().match(this._elChildName) && this.elChilds[j].className) {
                    this.elTitleElements.push(this.elChilds[j]);
                }
            }
        };

        Toc.prototype._createTocContent = function () {
            this._elTitleElementsLen = this.elTitleElements.length;
            if (!this._elTitleElementsLen) return;
            this.tocContent = '';
            this._tempLists = [];
            this._textList = [];
            var url = location.origin + location.pathname;
            for (var i = 0; i < this._elTitleElementsLen; i++) {
                var j = i + 1;
                this._elTitleElement = this.elTitleElements[i];
                this._elTitleElementName = this._elTitleElement.tagName;
                this._elTitleElementText = this._elTitleElement.innerHTML;
                this._elTitleElement.setAttribute('id', 'tip' + i);
                this.tocContent += '<li id="' + 'tip' + i + i + '"><a class="section-link" href="' + '#/?id=' + 'tip' + i + '">' + this._elTitleElementText + '</a>';


                if (j != this._elTitleElementsLen) {
                    this._elNextTitleElementName = this.elTitleElements[j].tagName;
                    if (this._elTitleElementName != this._elNextTitleElementName) {
                        var checkColse = false, y = 1;
                        for (var t = this._tempLists.length - 1; t >= 0; t--) {
                            if (this._tempLists[t].tagName == this._elNextTitleElementName) {
                                checkColse = true;
                                break;
                            }
                            y++;
                        }

                        if (checkColse) {
                            this.tocContent += new Array(y + 1).join('</li></ul>');
                            this._tempLists.length = this._tempLists.length - y;//更新栈的长度。
                        } else {
                            this._tempLists.push(this._elTitleElement);
                            this.tocContent += '<ul>';
                        }
                    }
                    else {
                        this.tocContent += '</li>';
                    }
                } else {
                    if (this._tempLists.length) {
                        this.tocContent += new Array(this._tempLists.length + 1).join('</li></ul>');
                    } else {
                        this.tocContent += '</li>';
                    }
                }
            }
            this.tocContent = '<ul id="tocnav" class="' + this._elTitleElementsLen + '">' + this.tocContent + '</ul>';
        };

        Toc.prototype._showToc = function () {

            document.getElementsByClassName('sidebar-nav')[0].innerHTML = this.tocContent


        };

        window.Toc = Toc;

    })(window);

</script>

</body>

</html>
