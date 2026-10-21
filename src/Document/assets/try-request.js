    function buildTryRequest(address, method, fields, bodyInput = null, acceptContentType = null) {
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
        if (bodyKinds.has('form') || (!['GET', 'HEAD'].includes(method)
            && ['FORM_DATA', 'FORM_URLENCODED'].includes(acceptContentType))) {
            if (files.length && acceptContentType === 'FORM_URLENCODED') {
                throw new Error('application/x-www-form-urlencoded 不支持文件上传');
            }
            if (acceptContentType === 'FORM_DATA' || files.length) {
                const form = new FormData();
                for (const [name, value] of post) form.append(name, value);
                for (const [name, value] of files) form.append(name, value);
                options.body = form;
                headers.delete('Content-Type');
            } else {
                options.body = post;
                headers.set('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
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
            const request = buildTryRequest(document.getElementById('try-url').value, activeTryApi.allowMethod, fields, bodyInput, activeTryApi.acceptContentType);
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
