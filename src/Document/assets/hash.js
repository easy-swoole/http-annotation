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
