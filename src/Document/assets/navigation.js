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
            + '<h3>响应参数</h3>' + responseParameterTable(api.responseParams)
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
