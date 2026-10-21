
    // 模板数据与页面状态
    const jsonData = {{$docData}};
    const config = {{$config}};
    const searchInput = document.getElementById('doc-search-input');
    const searchResults = document.getElementById('doc-search-results');
    const searchIndex = buildSearchIndex(jsonData);
    const content = document.getElementById('content');
    const sideBar = document.getElementById('sideBar');
    let activeDocument = {path: [], apiName: null};
    let activeTryApi = null;
    let activeTryParams = {};
    let tryController = null;
    let tryDialogTrigger = null;
    let tryDraftKey = null;
    const tryDrafts = new Map();
    const tryDialog = document.getElementById('try-dialog');
    const tryForm = document.getElementById('try-form');
    const tryMethod = document.getElementById('try-method');
    const tryResult = document.getElementById('try-result');
    const tryStatus = document.getElementById('try-status');
    const tryHeaders = document.getElementById('try-headers');
    const tryBody = document.getElementById('try-body');
    const tryRun = document.getElementById('try-run');

    document.title = config.projectName;
    const projectTitle = document.getElementById('projectName');
    const introductionHtml = content.innerHTML;
    projectTitle.textContent = config.projectName;
    projectTitle.disabled = !config.hasDescription;


    // 文档首页
