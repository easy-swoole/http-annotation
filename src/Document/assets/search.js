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
