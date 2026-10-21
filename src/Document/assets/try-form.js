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
