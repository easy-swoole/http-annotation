    searchInput.addEventListener('input', renderSearchResults);
    searchInput.addEventListener('focus', renderSearchResults);
    searchInput.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeSearchResults();
        if (event.key === 'Enter') {
            event.preventDefault();
            const first = searchDocuments(searchInput.value)[0];
            if (first) openSearchResult(first);
        }
        if (event.key === 'ArrowDown' && !searchResults.hidden) {
            const first = searchResults.querySelector('button');
            if (first) { event.preventDefault(); first.focus(); }
        }
    });
    document.addEventListener('click', event => {
        if (!event.target.closest('#doc-search')) closeSearchResults();
    }, true);
    projectTitle.addEventListener('click', () => showIntroduction());
    window.addEventListener('hashchange', restoreDocumentHash, true);
    window.addEventListener('popstate', restoreDocumentHash, true);
    for (const link of sideBar.querySelectorAll('a[data-api]')) {
        const hash = new URLSearchParams({group: JSON.parse(link.dataset.path).join('.'), api: link.dataset.api});
        link.href = '#' + hash.toString();
    }
    content.addEventListener('click', openTryDialog);
    tryForm.addEventListener('submit', runTryRequest);
    tryForm.addEventListener('input', saveTryDraft);
    tryForm.addEventListener('change', saveTryDraft);
    sideBar.addEventListener('click', handleMenuClick);
    document.getElementById('try-close').addEventListener('click', closeTryDialog);
    // 从 window 捕获；keyup 兼容输入控件先消耗 keydown 的情况。
    window.addEventListener('keydown', handleTryEscape, true);
    window.addEventListener('keyup', handleTryEscape, true);
    tryDialog.addEventListener('cancel', event => {
        event.preventDefault();
        closeTryDialog();
    });
    tryDialog.addEventListener('close', () => {
        saveTryDraft();
        if (tryController) tryController.abort();
        if (tryDialogTrigger && tryDialogTrigger.isConnected) {
            tryDialogTrigger.focus({preventScroll: true});
        }
        tryDialogTrigger = null;
    });
    renderRightMenu();
    restoreDocumentHash();
