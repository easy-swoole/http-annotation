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
        let html = '<div class="parameter-table-scroll" role="region" aria-label="参数表格，可左右滚动" tabindex="0"><table class="parameter-table"><thead><tr><th>名称</th><th>类型</th><th>校验规则</th><th>说明</th><th>默认值</th><th>来源</th></tr></thead><tbody>';
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
        return html + '</tbody></table></div>';
    }

    function responseParameterTable(params) {
        if (!Array.isArray(params) || !params.length) return '<p>暂无示例</p>';
        let html = '<div class="parameter-table-scroll" role="region" aria-label="响应参数表格，可左右滚动" tabindex="0"><table class="response-table"><thead><tr><th>参数名</th><th>参数值</th><th>参数类型</th><th>描述说明</th></tr></thead><tbody>';
        for (const param of params) {
            html += '<tr><td>' + displayValue(param.name) + '</td><td>' + displayValue(param.exampleValue)
                + '</td><td>' + displayValue(param.type) + '</td><td>' + displayValue(param.description) + '</td></tr>';
        }
        return html + '</tbody></table></div>';
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
